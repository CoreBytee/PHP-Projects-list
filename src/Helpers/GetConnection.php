<?php
    include __DIR__ . "/../Config/Config.php";
    $DatabaseConnection = new PDO("mysql:host=" . $_SESSION["DB_HOST"] . ";dbname=" . $_SESSION["DB_NAME"], $_SESSION["DB_USER"], $_SESSION["DB_PASSWORD"]);
    $DatabaseConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>