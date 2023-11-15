<!DOCTYPE html>
<html lang="en">

<?php
    include __DIR__ . "/Classes/Project.php";
    include __DIR__ . "/Classes/User.php";
    $CurrentUser = User::GetCurrentUser();
?>

<?php
    $_SESSION["page"] = $_GET["page"] ?? 1;
    $_SESSION["showcount"] = $_GET["showcount"] ?? 5;

    $_SESSION["projects"] = Project::GetProjects($_SESSION["showcount"], ($_SESSION["page"] - 1) * $_SESSION["showcount"], $_GET["search-input"] ?? "", $_GET["search-year"] ?? []);
?>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Portfolio Website - Overzichtspagina</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
</head>

<body>
    <main>
        <div class="container">
            <div class="d-flex flex-row card shadow-sm p-4 m-4 justify-content-sm-start">
                <form aria-label="search and filter" class="d-flex flex-row align-self-start justify-content-start" action="/" method="GET" id="search-form">
                    <input value="<?= htmlspecialchars($_GET['search-input'] ?? "") ?>" type="search" class="form-control ds-input" id="search-input" name="search-input" placeholder="Search..." aria-label="Search for..." autocomplete="off" spellcheck="false" role="combobox" aria-autocomplete="list" aria-expanded="false" aria-owns="algolia-autocomplete-listbox-0" dir="auto" style="position: relative; vertical-align: top;">
                    <select name="search-year[]" id="search-year" class="form-control ds-input" form="search-form">
                        <?php foreach (Project::GetPossibleYears() as $year) : ?>
                            <option value="<?= $year ?>"><?= $year ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-primary form-control">Search</button>
                </form>

                <?php if($CurrentUser->IsAdmin ?? 0): ?>
                    <button class="btn btn-primary" onclick="location.href='/create'">Create Project</button>
                <?php endif; ?>

                <?php
                    User::EchoLoginButton();
                ?>
            </div>
            <div class="row row-cols-1 row-cols-sm-1 row-cols-md-1 g-1 projects">
                <?php foreach ($_SESSION["projects"] as $project) : ?>
                    <div id="project1" class="project card shadow-sm card-body m-2" style="cursor: pointer;">
                        <div class="card-text">
                            <a style="font-size: 30px;" href="/detail?id=<?= $project->Id ?>"><?= $project->Title ?></a>
                            <div><?= $project->Description ?></div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <?php if ($CurrentUser->IsAdmin ?? 0): ?>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="location.href='/create?id=<?= $project->Id ?>'">
                                        Edit
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="location.href='/delete?id=<?= $project->Id ?>'">
                                        Delete
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="d-flex justify-content-center align-items-center m-4">
                <nav aria-label="Page navigation example">
                    <ul class="pagination">
                        <?php
                            $CurrentPage = $_SESSION["page"];
                            $PageCount = Project::GetPageCount($_GET["search-input"] ?? "", $_GET["search-year"] ?? []);

                            function LinkToPage($PageId) {
                                if (isset($_GET["page"])) {
                                    $_GET["page"] = $PageId;
                                    return http_build_query($_GET);
                                } else {
                                    return http_build_query($_GET) . "&page=" . $PageId;
                                }
                            }

                            if ($CurrentPage != 1) {
                                echo '<li class="page-item"><a class="page-link" href="?' . (LinkToPage($CurrentPage - 1)) . '">Previous</a></li>';
                            }

                            for ($i = 1; $i < $PageCount + 1; $i++) {
                                echo '<li class="page-item"><a class="page-link" href="?' . LinkToPage($i) . '">' . $i . '</a></li>';
                            }

                            if ($CurrentPage != $PageCount && $PageCount != 0) {
                                echo '<li class="page-item"><a class="page-link" href="?' . (LinkToPage($CurrentPage + 1)) . '">Next</a></li>';
                            }
                        ?>
                    </ul>
                </nav>
            </div>

        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>