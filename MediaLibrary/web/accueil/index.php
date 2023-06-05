<?php
require_once '../auth.php';

// Traitement de la déconnexion
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Accueil</title>
</head>
<body>
    <h1>Bienvenue, <?php echo $username; ?> !</h1>
    <p>Ceci est la page d'accueil.</p>
    <a href="../film/film.php">Aller à la page des films</a>

    <!-- Bouton de déconnexion -->
    <form method="POST" action="">
        <input type="submit" name="logout" value="Se déconnecter">
    </form>
</body>
</html>
