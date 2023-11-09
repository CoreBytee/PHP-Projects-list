<!DOCTYPE html>
<html lang="en">

<?php
    include __DIR__ . "/../Classes/User.php";
?>

<?php

    function LoginError($ErrorText) {
        header("Location: /login?error=" . $ErrorText . "&email=" . $_POST["email"], true, 303);
        exit();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $User = User::GetUserByEmail($_POST["email"]);

        if (!$User) {
            LoginError("Invalid email");
        }

        if ($User && $User->PasswordMatches($_POST["password"], $User->PasswordSalt)) {

            setcookie("sessiontoken", $User->GenerateSession(), time() + 60 * 60 * 24, "/");
            header("Location: /");
            exit();
        } else {
            LoginError("Invalid password");
        }
    }

?>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Portfolio Website - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
</head>

<body class="d-flex align-items-center py-4 bg-body-tertiary">
    <main class="form-signin w-25 m-auto">
        <form action="/login" method="post">
            <h1 class="h3 mb-3 fw-normal">Please sign in</h1>

            <div class="form-floating">
                <input type="email" class="form-control" name="email" id="floatingInput" placeholder="name@example.com" value="<?= $_GET["email"] ?? "" ?>" />
                <label for="floatingInput">Email address</label>
            </div>
            <div class="form-floating">
                <input type="password" class="form-control" name="password" id="floatingPassword" placeholder="Password" />
                <label for="floatingPassword">Password</label>
            </div>

            <a class="text-danger text-decoration-none"><?= $_GET["error"] ?? "" ?></a>

            <button class="btn btn-primary w-100 py-2" type="submit">
                Sign in
            </button>
        </form>
    </main>
    <script src="/docs/5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>