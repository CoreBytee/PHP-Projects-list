<!DOCTYPE html>
<html lang="en">

<?php
include __DIR__ . "/../Classes/User.php";
include __DIR__ . "/../Classes/Project.php";
$CurrentUser = User::GetCurrentUser();

if (!($CurrentUser->IsAdmin ?? 0)) {
    header("Location: /");
}

$CurrentProject = Project::GetProjectById($_GET["id"] ?? "");
$CurrentProject->Delete();
header("Location: /");
?>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Portfolio Website - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
</head>

<body class="d-flex align-items-center py-4 bg-body-tertiary">
    <script src="/docs/5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>