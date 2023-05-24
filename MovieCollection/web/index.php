<!DOCTYPE html>
<html>
<head>
    <title>Accueil - Movie Collection</title>
    <link rel="stylesheet" type="text/css" href="accueil.css">
</head>
<body>
    <div class="admin-section">
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
        <form method="POST" class="admin-form">
            <label for="code">Code d'accès :</label>
            <input type="password" id="code" name="code" required>
            <input type="submit" value="Se connecter">
        </form>
    </div>

    <div class="container">
        <div class="section-links">
            <a href="film/film.php" class="section-link">
                <span class="section-link-text">Section Films</span>
            </a>
            <a href="serie/serie.php" class="section-link">
                <span class="section-link-text">Section Séries</span>
            </a>
            <!-- Ajoutez ici d'autres liens vers vos différentes sections -->
        </div>
    </div>
</body>
</html>
