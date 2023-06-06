<?php
require_once '../utils/auth.php';

// Vérification si le bouton de déconnexion a été cliqué
if (isset($_POST['logout'])) {
    // Suppression de toutes les variables de session
    session_unset();
    // Destruction de la session
    session_destroy();
    // Redirection vers la page de connexion
    header("Location: ../login.php");
    exit();
}

// Vérification si l'utilisateur est connecté
checkLoggedIn();

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Accueil - MediaLibrary</title>
    <link rel="stylesheet" type="text/css" href="accueil.css">
</head>
<body>
    <header>
        <div class="container">
            <h1 class="title">MediaLibrary</h1>
            <form method="post" action="" class="logout-form">
                <input type="submit" name="logout" value="Déconnexion">
            </form>
        </div>
    </header>

    <div class="container">
        <h2 class="welcome-message">Bienvenue, <?php echo $username; ?> !</h2>
        <p class="description">Ceci est la page d'accueil.</p>
        <div class="section-links">
            <a href="film/film.php" class="section-link">
                <span class="section-link-text">Section Films</span>
            </a>
            <a href="serie/serie.php" class="section-link">
                <span class="section-link-text">Section Séries / Drama</span>
            </a>
            <a href="livre/livre.php" class="section-link">
                <span class="section-link-text">Section Livres</span>
            </a>
            <a href="concert/concert.php" class="section-link">
                <span class="section-link-text">Section Concerts</span>
            </a>
            <a href="documentaire/documentaire.php" class="section-link">
                <span class="section-link-text">Section Documentaires</span>
            </a>
            <!-- Ajoutez ici d'autres liens vers vos différentes sections -->
        </div>
    </div>
</body>
</html>
