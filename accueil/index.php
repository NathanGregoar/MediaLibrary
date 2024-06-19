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
$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <title>Accueil</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="manifest" href="/manifest.json">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="../images/icon.png" type="image/x-icon">
    <style>
        .admin-link:hover,
        .olympe:hover {
            transform: scale(1.1);
        }
    </style>
</head>

<body class="bg-secondary text-white d-flex flex-column min-vh-100">
    <header class="bg-dark text-center py-3 position-relative">
        <h1 class="display-5">Bienvenue, <?php echo htmlspecialchars($username); ?> !</h1>
        <form method="post" action="" class="d-inline">
            <button type="submit" name="logout" class="btn btn-danger">
                Déconnexion <i class="bi bi-door-closed"></i>
            </button>
        </form>
        <a href="../profil/profil.php" class="btn btn-info profile-btn">Profil <i class="bi bi-person"></i></a>
    </header>

    <main class="container flex-grow-1 d-flex justify-content-center align-items-center">
        <div class="row w-100 g-3">
            <div class="col-12 col-md-4">
                <button class="btn btn-primary w-100 py-3" onclick="window.location.href='../film/ajouter_films.php'">
                    <span class="h5">Section Films</span>
                </button>
            </div>
            <div class="col-12 col-md-4">
                <button class="btn btn-primary w-100 py-3" onclick="window.location.href='../series/ajouter_series.php'">
                    <span class="h5">Section Séries / Drama</span>
                </button>
            </div>
            <div class="col-12 col-md-4">
                <button class="btn btn-primary w-100 py-3" onclick="window.location.href='../livres/ajouter_livres.php'">
                    <span class="h5">Section Livres</span>
                </button>
            </div>
            <div class="col-12 col-md-4">
                <button class="btn btn-primary w-100 py-3" onclick="window.location.href='../error/error_404.php'">
                    <span class="h5">Section Concerts</span>
                </button>
            </div>
            <div class="col-12 col-md-4">
                <button class="btn btn-primary w-100 py-3" onclick="window.location.href='../error/error_404.php'">
                    <span class="h5">Section Documentaires</span>
                </button>
            </div>
            <div class="col-12 col-md-4">
                <button class="btn btn-primary w-100 py-3" onclick="window.location.href='../error/error_404.php'">
                    <span class="h5">Section Manhwa / Manga</span>
                </button>
            </div>
        </div>
    </main>

    <?php if ($role == "admin"): ?>
        <div class="position-fixed bottom-0 end-0 p-3">
            <a href="../admin/admin.php" class="btn btn-warning admin-link shadow">
                <span class="h5">Section Admin</span>
            </a>
        </div>
    <?php endif; ?>

    <?php if (in_array($role, ["admin", "olympe"])): ?>
        <div class="position-fixed bottom-0 start-0 p-3">
            <a href="../olympe/olympe.php" class="olympe">
                <img src="https://static.vecteezy.com/system/resources/thumbnails/009/399/550/small/sun-icon-set-clipart-design-illustration-free-png.png" alt="olympe" class="img-fluid" style="max-width: 10vh;">
            </a>
        </div>
    <?php endif; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>

<script>
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/service-worker.js')
            .then(registration => {
                console.log('Service Worker registered with scope:', registration.scope);
            }).catch(error => {
                console.log('Service Worker registration failed:', error);
            });
    }
</script>
