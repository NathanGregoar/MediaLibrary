<?php
require_once '../utils/auth.php';
include '../utils/bootstrap.php';

if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: ../login.php");
    exit();
}

$loggedInUser = getLoggedInUser();
$username = $_SESSION['username'];
$email = $_SESSION['email'];
?>

<!DOCTYPE html>
<html>

<head>
    <title>Accueil</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- <link rel="stylesheet" type="text/css" href="./accueil.css"> -->
</head>

<body class="align-items-center justify-content-center bg-secondary text-white overflow-hidden" style="min-height: 100vh;">
    <header class="bg-dark text-white text-center py-3 d-md-flex justify-content-between align-items-center">
        <h1 class="welcome-message mx-auto mb-3 mb-md-0">Bienvenue, <?php echo $username; ?> !</h1>
        <form method="post" action="" class="logout-form ml-auto">
            <button type="submit" name="logout" class="btn btn-danger">
                Déconnexion <i class="bi bi-door-closed"></i>
            </button>
        </form>
    </header>

    <div class="container-fluid vh-100 d-flex justify-content-center align-items-center">
    <div class="row justify-content-center text-center">
        <div class="col-md-4 mb-3">
            <button class="section-link btn btn-primary btn-block p-4 w-100 h-100" onclick="window.location.href='../film/ajouter_films.php'">
                <span class="section-link-text fs-3">Section Films</span>
            </button>
        </div>
        <div class="col-md-4 mb-3">
            <button class="section-link btn btn-primary btn-block p-4 w-100 h-100" onclick="window.location.href='../series/ajouter_series.php'">
                <span class="section-link-text fs-3">Section Séries / Drama</span>
            </button>
        </div>
        <div class="col-md-4 mb-3">
            <button class="section-link btn btn-primary btn-block p-4 w-100 h-100" onclick="window.location.href='../livres/ajouter_livres.php'">
                <span class="section-link-text fs-3">Section Livres</span>
            </button>
        </div>
        <div class="col-md-4 mb-3">
            <button class="section-link btn btn-primary btn-block p-4 w-100 h-100" onclick="window.location.href='concert/concert.php'">
                <span class="section-link-text fs-3">Section Concerts</span>
            </button>
        </div>
        <div class="col-md-4 mb-3">
            <button class="section-link btn btn-primary btn-block p-4 w-100 h-100" onclick="window.location.href='documentaire/documentaire.php'">
                <span class="section-link-text fs-3">Section Documentaires</span>
            </button>
        </div>
        <div class="col-md-4 mb-3">
            <button class="section-link btn btn-primary btn-block p-4 w-100 h-100" onclick="window.location.href='documentaire/documentaire.php'">
                <span class="section-link-text fs-3">Section Manhwa / Manga</span>
            </button>
        </div>
    </div>
</div>



    <?php
    if ($loggedInUser['role'] == "admin") {
        echo '<div class="position-fixed bottom-0 end-0 mb-4 me-4">';
        echo '<a href="../admin/admin.php" class="btn btn-warning admin-link" style="border-radius: 5px; padding: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">';
        echo '<span class="section-link-text">Section Admin</span>';
        echo '</a>';
        echo '</div>';
    }

    $allowedRoles = ["admin", "olympe"];
    if (in_array($loggedInUser['role'], $allowedRoles)) {
        echo '<div class="position-fixed bottom-0 start-0 mb-4 ms-4">';
        echo '<a href="../olympe/olympe.php" class="olympe">';
        echo '<img src="https://static.vecteezy.com/system/resources/thumbnails/009/399/550/small/sun-icon-set-clipart-design-illustration-free-png.png" alt="olympe" class="img-fluid" style="max-width: 10vh;">';
        echo '</a>';
        echo '</div>';
    }
    ?>
</body>

</html>