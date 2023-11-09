<?php
include __DIR__ . "/../Helpers/GetConnection.php";

$_SESSION["UserStatements"] = [
    "GetUserByEmail" => $DatabaseConnection->prepare("SELECT * FROM `users` WHERE `Email` = :Email"),
    "GetSessionFromToken" => $DatabaseConnection->prepare("SELECT * FROM `sessions` WHERE `SessionToken` = :Token"),
    "CreateSession" => $DatabaseConnection->prepare("INSERT INTO `sessions` (`UserId`, `SessionToken`, `ExpiresAt`) VALUES (:UserId, :SessionToken, NOW() + INTERVAL 1 DAY)"),
    "DeleteSessionByToken" => $DatabaseConnection->prepare("DELETE FROM `sessions` WHERE `SessionToken` = :Token"),
    "GetUserById" => $DatabaseConnection->prepare("SELECT * FROM `users` WHERE `Id` = :Id")
];

class User
{
    public $Id;
    public $Email;
    public $Name;
    public $IsAdmin;
    public $PasswordHash;
    public $SessionToken;

    public function __construct($Data)
    {
        $this->Id = $Data["Id"] ?? null;
        $this->Email = $Data["Email"] ?? null;
        $this->Name = $Data["Name"] ?? null;
        $this->IsAdmin = $Data["IsAdmin"] ?? null;
        $this->PasswordHash = $Data["PasswordHash"] ?? null;
    }

    function PasswordMatches($Password) {
        return password_verify($Password, $this->PasswordHash);
    }

    function GenerateSession() {
        $Statement = $_SESSION["UserStatements"]["CreateSession"];

        $Token = bin2hex(random_bytes(32));

        $Statement->bindParam(":UserId", $this->Id, PDO::PARAM_INT);
        $Statement->bindParam(":SessionToken", $Token, PDO::PARAM_STR);
        $Statement->execute();

        return $Token;
    }

    function DeleteSession() {
        $Statement = $_SESSION["UserStatements"]["DeleteSessionByToken"];
        $Statement->bindParam(":Token", $this->SessionToken, PDO::PARAM_STR);
        $Statement->execute();
    }

    public static function GetCurrentUser() {
        $Token = $_COOKIE["sessiontoken"] ?? null;

        $GetSessionStatement = $_SESSION["UserStatements"]["GetSessionFromToken"];
        $GetSessionStatement->bindParam(":Token", $Token, PDO::PARAM_STR);
        $GetSessionStatement->execute();
        $SessionRows = $GetSessionStatement->fetchAll();

        if (count($SessionRows) == 0) {
            return null;
        }

        $Session = $SessionRows[0];

        $GetUserStatement = $_SESSION["UserStatements"]["GetUserById"];
        $GetUserStatement->bindParam(":Id", $Session["UserId"], PDO::PARAM_INT);
        $GetUserStatement->execute();
        $UserRows = $GetUserStatement->fetchAll();

        if (count($UserRows) == 0) {
            return null;
        }

        $User = new User($UserRows[0]);
        $User->SessionToken = $Token;
        return $User;
    }

    public static function HashPassword($Password)
    {
        return password_hash($Password, PASSWORD_BCRYPT);
    }

    public static function GetUserByEmail($Email) {
        $Statement = $_SESSION["UserStatements"]["GetUserByEmail"];
        $Statement->bindParam(":Email", $Email, PDO::PARAM_STR);
        $Statement->execute();

        $Rows = $Statement->fetchAll();

        if (count($Rows) == 0) {
            return null;
        }

        return new User($Rows[0]);
    }

    public static function EchoLoginButton() {
        $CurrentUser = User::GetCurrentUser();
        if ($CurrentUser) {
            echo '<button class="btn btn-primary align-self-end ml-4" onclick="location.href=\'/logout\'">Logout</button>';
        } else {
            echo '<button class="btn btn-primary align-self-end ml-4" onclick="location.href=\'/login\'">Login</button>';
        }
    }

}
