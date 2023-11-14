<!DOCTYPE html>
<html lang="en">

<?php
include __DIR__ . "/../Classes/Project.php";
include __DIR__ . "/../Classes/User.php";
$CurrentUser = User::GetCurrentUser();

if (!($CurrentUser->IsAdmin ?? 0)) {
    header("Location: /");
}

if ($_POST) {
    $ImageHash = md5_file($_FILES["image"]["tmp_name"]);    
    move_uploaded_file($_FILES["image"]["tmp_name"], __DIR__ . "/../images/" . $ImageHash . ".png");
    echo json_encode($_POST);
    if ($_POST["id"] ?? "") {
        $CurrentProject = Project::GetProjectById($_POST["id"]);
        $CurrentProject->Title = $_POST["title"];
        $CurrentProject->Description = $_POST["description"];
        $CurrentProject->Body = $_POST["body"];
        $CurrentProject->Type = $_POST["type"];
        $CurrentProject->Year = $_POST["year"];
        $CurrentProject->Image = $ImageHash;
        $CurrentProject->Save();
    } else {
        Project::CreateProject($_POST["title"], $_POST["type"], $_POST["year"], $_POST["description"], $_POST["body"], $ImageHash);
    }
    // header("Location: /");
} else {
    $CurrentProject = Project::GetProjectById($_GET["id"] ?? "");
}
?>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Portfolio Website - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
</head>

<body>
    <main>
        <div class="container">
            <div class="d-flex flex-row card shadow-sm p-4 m-4 justify-content-sm-start">
                <button class="btn btn-primary" onclick="location.href='/'">Home</button>
                <?php
                User::EchoLoginButton();
                ?>
            </div>
            <div class="card shadow-sm card-body m-2">
                <form action="/create" method="post" enctype="multipart/form-data">
                    <input id="id" type="number" class="form-control ds-input" value="<?= $_GET["id"] ?? "" ?>" hidden>

                    <label for="title">Title</label>
                    <input id="title" name="title" type="text" class="form-control" value="<?= $CurrentProject->Title ?? "" ?>">

                    <label for="description">Description</label>
                    <input id="description" name="description" type="text" class="form-control" value="<?= $CurrentProject->Description ?? "" ?>">

                    <label for="body">Body</label>
                    <input id="body" name="body" type="text" class="form-control" value="<?= $CurrentProject->Body ?? "" ?>">

                    <label for="type">Type</label>
                    <input id="type" name="type" type="text" class="form-control" value="<?= $CurrentProject->Type ?? "" ?>">

                    <label for="year">Year</label>
                    <input id="year" name="year" type="number" class="form-control" min="1000" max="3000" value="<?= $CurrentProject->Year ?? "" ?>">

                    <label for="image"></label>
                    <input id="image" name="image" type="file" class="form-control">
                    <br>

                    <button type="submit" class="btn btn-primary w-100 py-2">
                        <?php if ($_GET["id"] ?? "") : ?>
                            Update
                        <?php else : ?>
                            Create
                        <?php endif; ?>
                    </button>
                </form>
            </div>
        </div>
    </main>



    <script src="/docs/5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>