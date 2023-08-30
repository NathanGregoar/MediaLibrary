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

$loggedInUser = getLoggedInUser();
$username = $_SESSION['username'];
$email = $_SESSION['email'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Accueil</title>
    <link rel="stylesheet" type="text/css" href="./accueil.css">
</head>
<body>
    <header>
        <h1 class="welcome-message">Bienvenue, <?php echo $username;?><?php echo $loggedInUser['role'];?> !</h1>
        <form method="post" action="" class="logout-form">
            <input type="submit" name="logout" value="Déconnexion">
        </form>
    </header>

    <div class="container">
        <div class="section-links">
            <a href="../film/film.php" class="section-link">
                <span class="section-link-text">Section Films</span>
            </a>
            <a href="../series/series.php" class="section-link">
                <span class="section-link-text">Section Séries / Drama</span>
            </a>
            <a href="../livres/livres_souhaite.php" class="section-link">
                <span class="section-link-text">Section Livres</span>
            </a>
            <a href="concert/concert.php" class="section-link">
                <span class="section-link-text">Section Concerts</span>
            </a>
            <a href="documentaire/documentaire.php" class="section-link">
                <span class="section-link-text">Section Documentaires</span>
            </a>
            <a href="documentaire/documentaire.php" class="section-link">
                <span class="section-link-text">Section Manhwa / Manga</span>
            </a>
            <!-- Ajoutez ici d'autres liens vers vos différentes sections -->

            <?php
            // Vérification si l'utilisateur est "Nathan" et son adresse e-mail est "nathan.gregoar@yahoo.fr"
            if ($username === "Nathan" && $email === "nathan.gregoar@yahoo.fr") {
                echo '<a href="../admin/admin.php" class="section-link admin-link">';
                echo '<span class="section-link-text">Section Admin</span>';
                echo '</a>';
            }
            ?>

            <?php
            // Vérification si l'utilisateur est "Nathan" et son adresse e-mail est "nathan.gregoar@yahoo.fr"
            if ($username === "Nathan" && $email === "nathan.gregoar@yahoo.fr") {
                echo '<a href="../olympe/olympe.php" class="olympe">';
                echo '<img src="https://static.vecteezy.com/system/resources/thumbnails/009/399/550/small/sun-icon-set-clipart-design-illustration-free-png.png" alt="olympe">';
                echo '</a>';
            }
            ?>
        </div>
    </div>
</body>
</html>
