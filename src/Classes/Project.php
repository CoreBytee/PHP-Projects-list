<?php
include __DIR__ . "/../Helpers/GetConnection.php";

$_SESSION["ProjectStatements"] = [
    "GetProjectById" => $DatabaseConnection->prepare("SELECT * FROM projects WHERE Id = :Id"),
    "GetPossibleYears" => $DatabaseConnection->prepare("SELECT DISTINCT Year FROM projects ORDER BY Year DESC"),
    "GetPageCount" => $DatabaseConnection->prepare("SELECT COUNT(*) FROM projects"),
    "GetProjects" => $DatabaseConnection->prepare("SELECT * FROM projects LIMIT :limitcount OFFSET :fromcount"),
    "GetProjectsSearch" => $DatabaseConnection->prepare("SELECT * FROM projects WHERE Title LIKE :search LIMIT :limitcount OFFSET :fromcount"),
    "GetProjectsYear" => $DatabaseConnection->prepare("SELECT * FROM projects WHERE Year IN (:years) LIMIT :limitcount OFFSET :fromcount"),
    "GetProjectsSearchYear" => $DatabaseConnection->prepare("SELECT * FROM projects WHERE Title LIKE :search AND Year IN (:years) LIMIT :limitcount OFFSET :fromcount"),
];

class Project
{
    public $Id;
    public $Title;
    public $Type;
    public $Year;
    public $Description;
    public $Body;

    public function __construct($Data)
    {
        $this->Id = $Data["Id"] ?? null;
        $this->Title = $Data["Title"] ?? null;
        $this->Type = $Data["Type"] ?? null;
        $this->Year = $Data["Year"] ?? null;
        $this->Description = $Data["Description"] ?? null;
        $this->Body = $Data["Body"] ?? null;
    }

    public static function GetPossibleYears()
    {
        $Statement = $_SESSION["ProjectStatements"]["GetPossibleYears"];
        $Statement->execute();

        $Rows = array_map(
            function ($Row) {
                return $Row["Year"];
            },
            $Statement->fetchAll()
        );

        return $Rows;
    }

    public static function GetPageCount() {
        $Statement = $_SESSION["ProjectStatements"]["GetPageCount"];
        $Statement->execute();

        return ceil($Statement->fetchAll()[0][0] / 5);
    }

    public static function GetProjectById($Id)
    {
        $Statement = $_SESSION["ProjectStatements"]["GetProjectById"];
        $Statement->bindParam(":Id", $Id, PDO::PARAM_INT);
        $Statement->execute();

        $Row = $Statement->fetchAll()[0];

        $Project = new Project($Row);
        return $Project;
    }

    public static function GetProjects($Count, $From, $Search, $Years = [])
    {
        if ($Search != "" && count($Years) > 0) {
            $Statement = $_SESSION["ProjectStatements"]["GetProjectsSearchYear"];
        } else if ($Search != "") {
            $Statement = $_SESSION["ProjectStatements"]["GetProjectsSearch"];
        } else if (count($Years) > 0) {
            $Statement = $_SESSION["ProjectStatements"]["GetProjectsYear"];
        } else {
            $Statement = $_SESSION["ProjectStatements"]["GetProjects"];
        }

        if ($Search != "") {
            $Search = "%" . $Search . "%";
            $Statement->bindParam(":search", $Search, PDO::PARAM_STR);
        }

        if (count($Years) > 0) {
            $Statement->bindValue(":years", implode("', '", $Years), PDO::PARAM_STR);
        }

        $Statement->bindParam(":limitcount", $Count, PDO::PARAM_INT);
        $Statement->bindParam(":fromcount", $From, PDO::PARAM_INT);
        $Statement->execute();

        $Rows = array_map(
            function ($Row) {
                $Project = new Project($Row);
                return $Project;
            },
            $Statement->fetchAll()
        );

        return $Rows;
    }
}
