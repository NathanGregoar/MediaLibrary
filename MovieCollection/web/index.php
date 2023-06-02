<?php
    if (isset($_POST['code'])) {
        $code = $_POST['code'];
        if ($code == '444719') {
            header('Location: admin/admin.php');
            exit();
        } else {
            echo '<div class="alert alert-error">Le code est incorrect. Veuillez réessayer.</div>';
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Accueil - Movie Collection</title>
    <link rel="stylesheet" type="text/css" href="accueil.css">
</head>
<body>
    <div class="admin-section">
        <form method="POST" class="admin-form">
            <label for="code">Admin :</label>
            <input type="password" id="code" name="code" required>
            <input type="submit" value="Se connecter">
        </form>
    </div>

    <div class="container">
        <div class="section-links">
            <a href="film/film.php" class="section-link">
                <span class="section-link-text">Films</span>
            </a>
            <a href="serie/serie.php" class="section-link">
                <span class="section-link-text">Séries / Drama</span>
            </a>
            <a href="livre/livre.php" class="section-link">
                <span class="section-link-text">Livres</span>
            </a>
            <a href="concert/concert.php" class="section-link">
                <span class="section-link-text">Concerts</span>
            </a>
            <a href="documentaire/documentaire.php" class="section-link">
                <span class="section-link-text">Documentaires</span>
            </a>
            <!-- Ajoutez ici d'autres liens vers vos différentes sections -->
        </div>
    </div>
</body>
</html>
