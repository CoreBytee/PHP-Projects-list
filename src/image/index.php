
<?php
include __DIR__ . "/../Classes/Project.php";
$CurrentProject = Project::GetProjectById($_GET["id"] ?? "");

?>