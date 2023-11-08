<!DOCTYPE html>
<html lang="en">

<?php
    include __DIR__ . "/Classes/Project.php";
?>

<?php 
    $_SESSION["page"] = $_GET["page"] ?? 1;
    $_SESSION["showcount"] = $_GET["showcount"] ?? 5;

    $_SESSION["projects"] = Project::GetProjects($_SESSION["showcount"], ($_SESSION["page"] - 1) * $_SESSION["showcount"]);
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
            <div class="d-flex justify-content-center align-items-center m-4">
                <nav aria-label="search and filter">
                    <input type="search" class="form-control ds-input" id="search-input" placeholder="Search..." aria-label="Search for..." autocomplete="off" spellcheck="false" role="combobox" aria-autocomplete="list" aria-expanded="false" aria-owns="algolia-autocomplete-listbox-0" dir="auto" style="position: relative; vertical-align: top;">
                </nav>
            </div>
            <div class="row row-cols-1 row-cols-sm-1 row-cols-md-1 g-1 projects">
                <div id="project1" class="project card shadow-sm card-body m-2">
                    <?php foreach ($_SESSION["projects"] as $project) :?>
                        <div class="card-text">
                            <h2><?= $project->Title ?></h2>
                            <div><?= $project->Description ?></div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-outline-secondary">
                                    View
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary">
                                    Edit
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary">
                                    Delete
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="d-flex justify-content-center align-items-center m-4">
                <nav aria-label="Page navigation example">
                    <ul class="pagination">
                        <?php
                            $CurrentPage = $_SESSION["page"];
                            $PageCount = Project::GetPageCount();

                            if ($CurrentPage != 1) {
                                echo '<li class="page-item"><a class="page-link" href="?page=' . ($CurrentPage - 1) . '">Previous</a></li>';
                            }

                            for ($i = 1; $i < $PageCount + 1; $i++) {
                                echo '<li class="page-item"><a class="page-link" href="?page=' . $i . '">' . $i . '</a></li>';
                            }

                            if ($CurrentPage != $PageCount) {
                                echo '<li class="page-item"><a class="page-link" href="?page=' . ($CurrentPage + 1) . '">Next</a></li>';
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