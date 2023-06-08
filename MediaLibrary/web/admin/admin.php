<?php
require_once '../utils/auth.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin</title>
    <link rel="stylesheet" type="text/css" href="./accueil.css">
</head>
<body>
    <div class="container">
        <h1 class="welcome-message">Bienvenue, <?php echo $username; ?> !</h1>
        <div class="section-links">
            <a href="../film/film.php" class="section-link">
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
            <a href="documentaire/documentaire.php" class="section-link">
                <span class="section-link-text">Section Manhwa / Manga</span>
            </a>
            <!-- Ajoutez ici d'autres liens vers vos différentes sections -->
        </div>
    </div>
</body>
</html>
