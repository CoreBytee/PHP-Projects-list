<?php
    include __DIR__ . "/../Helpers/GetConnection.php";

    $_SESSION["ProjectStatements"] = [
        "GetProjectById" => $DatabaseConnection->prepare("SELECT * FROM projects WHERE Id = :Id"),
        "GetProjects" => $DatabaseConnection->prepare("SELECT * FROM projects LIMIT :limitcount OFFSET :fromcount")
    ];

    class Project {
        public $Id;
        public $Title;
        public $Type;
        public $Year;
        public $Description;
        public $Body;

        public function __construct($Data) {
            $this->Id = $Data["Id"] ?? null;
            $this->Title = $Data["Title"] ?? null;
            $this->Type = $Data["Type"] ?? null;
            $this->Year = $Data["Year"] ?? null;
            $this->Description = $Data["Description"] ?? null;
            $this->Body = $Data["Body"] ?? null;
        }

        public static function GetProjectById($Id) {
            $Statement = $_SESSION["ProjectStatements"]["GetProjectById"];
            $Statement->bindParam(":Id", $Id, PDO::PARAM_INT);
            $Statement->execute();

            $Row = $Statement->fetchAll()[0];

            $Project = new Project($Row);
            return $Project;
        }
    public static function GetPageCount() {
        $Statement = $_SESSION["ProjectStatements"]["GetPageCount"];
        $Statement->execute();

        return ceil($Statement->fetchAll()[0][0] / 5);
    }


        public static function GetProjects($Count, $From) {
            $Statement = $_SESSION["ProjectStatements"]["GetProjects"];
            $Statement->bindParam(":limitcount", $Count, PDO::PARAM_INT);
            $Statement->bindParam(":fromcount", $From, PDO::PARAM_INT);
            $Statement->execute();

            $Rows = array_map(
                function($Row) {
                    $Project = new Project($Row);
                    return $Project;
                },
                $_SESSION["ProjectStatements"]["GetProjects"]->fetchAll()
            );

            return $Rows;
        }
    }
?>