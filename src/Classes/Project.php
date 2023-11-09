<?php
include __DIR__ . "/../Helpers/GetConnection.php";

$_SESSION["ProjectStatements"] = [
    "GetProjectById" => $DatabaseConnection->prepare("SELECT * FROM projects WHERE Id = :Id"),
    "GetPossibleYears" => $DatabaseConnection->prepare("SELECT DISTINCT Year FROM projects ORDER BY Year DESC"),
    "GetProjects" => $DatabaseConnection->prepare("SELECT * FROM projects LIMIT :limitcount OFFSET :fromcount"),
    "GetProjectsCount" => $DatabaseConnection->prepare("SELECT COUNT(*) FROM projects"),
    "GetProjectsSearch" => $DatabaseConnection->prepare("SELECT * FROM projects WHERE Title LIKE :search LIMIT :limitcount OFFSET :fromcount"),
    "GetProjectsSearchCount" => $DatabaseConnection->prepare("SELECT COUNT(*) FROM projects WHERE Title LIKE :search AND Year IN (:years)"),
    "GetProjectsYear" => $DatabaseConnection->prepare("SELECT * FROM projects WHERE Year IN (:years) LIMIT :limitcount OFFSET :fromcount"),
    "GetProjectsYearCount" => $DatabaseConnection->prepare("SELECT COUNT(*) FROM projects WHERE Year IN (:years)"),
    "GetProjectsSearchYear" => $DatabaseConnection->prepare("SELECT * FROM projects WHERE Title LIKE :search AND Year IN (:years) LIMIT :limitcount OFFSET :fromcount"),
    "GetProjectsSearchYearCount" => $DatabaseConnection->prepare("SELECT COUNT(*) FROM projects WHERE Title LIKE :search AND Year IN (:years)"),
    "CreateProject" => $DatabaseConnection->prepare("INSERT INTO projects (Title, Type, Year, Description, Body, Image) VALUES (:Title, :Type, :Year, :Description, :Body, :Image)"),
    "UpdateProject" => $DatabaseConnection->prepare("UPDATE projects SET Title = :Title, Type = :Type, Year = :Year, Description = :Description, Body = :Body, Image = :Image WHERE Id = :Id"),
    "DeleteProject" => $DatabaseConnection->prepare("DELETE FROM projects WHERE Id = :Id")
];

class Project
{
    public $Id;
    public $Title;
    public $Type;
    public $Year;
    public $Description;
    public $Body;
    public $Image;

    public function __construct($Data)
    {
        $this->Id = $Data["Id"] ?? null;
        $this->Title = $Data["Title"] ?? null;
        $this->Type = $Data["Type"] ?? null;
        $this->Year = $Data["Year"] ?? null;
        $this->Description = $Data["Description"] ?? null;
        $this->Body = $Data["Body"] ?? null;
        $this->Image = $Data["Image"] ?? null;
    }

    public function Save() {
        $Statement = $_SESSION["ProjectStatements"]["UpdateProject"];
        $Statement->bindParam(":Id", $this->Id, PDO::PARAM_INT);
        $Statement->bindParam(":Title", $this->Title, PDO::PARAM_STR);
        $Statement->bindParam(":Type", $this->Type, PDO::PARAM_STR);
        $Statement->bindParam(":Year", $this->Year, PDO::PARAM_INT);
        $Statement->bindParam(":Description", $this->Description, PDO::PARAM_STR);
        $Statement->bindParam(":Body", $this->Body, PDO::PARAM_STR);
        $Statement->bindParam(":Image", $this->Image, PDO::PARAM_STR);

        $Statement->execute();
    }

    public function Delete() {
        $Statement = $_SESSION["ProjectStatements"]["DeleteProject"];
        $Statement->bindParam(":Id", $this->Id, PDO::PARAM_INT);
        $Statement->execute();
    }

    public static function CreateProject($Title, $Type, $Year, $Description, $Body) {
        $Statement = $_SESSION["ProjectStatements"]["CreateProject"];
        $Statement->bindParam(":Title", $Title, PDO::PARAM_STR);
        $Statement->bindParam(":Type", $Type, PDO::PARAM_STR);
        $Statement->bindParam(":Year", $Year, PDO::PARAM_INT);
        $Statement->bindParam(":Description", $Description, PDO::PARAM_STR);
        $Statement->bindParam(":Body", $Body, PDO::PARAM_STR);
        $Statement->execute();

        // $Row = $Statement->fetchAll()[0];

        // return new Project($Row);
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

    public static function GetPageCount($Search, $Years = []) {
        if ($Search != "" && count($Years) > 0) {
            $Statement = $_SESSION["ProjectStatements"]["GetProjectsSearchYearCount"];
        } else if ($Search != "") {
            $Statement = $_SESSION["ProjectStatements"]["GetProjectsSearchCount"];
        } else if (count($Years) > 0) {
            $Statement = $_SESSION["ProjectStatements"]["GetProjectsYearCount"];
        } else {
            $Statement = $_SESSION["ProjectStatements"]["GetProjectsCount"];
        }

        if ($Search != "") {
            $Search = "%" . $Search . "%";
            $Statement->bindParam(":search", $Search, PDO::PARAM_STR);
        }

        if (count($Years) > 0) {
            $Statement->bindValue(":years", implode("', '", $Years), PDO::PARAM_STR);
        }

        $Statement->execute();

        return ceil($Statement->fetchAll()[0][0] / 5);
    }

    public static function GetProjectById($Id)
    {
        $Statement = $_SESSION["ProjectStatements"]["GetProjectById"];
        $Statement->bindParam(":Id", $Id, PDO::PARAM_INT);
        $Statement->execute();

        $Rows = $Statement->fetchAll();

        if (count($Rows) == 0) {
            return null;
        }

        $Project = new Project($Rows[0]);
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
