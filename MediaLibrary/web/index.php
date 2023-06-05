<?php
session_start();

// Fonction de vérification si l'utilisateur est connecté
function checkLoggedIn() {
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit();
    }
}

// Vérification si l'utilisateur est connecté
checkLoggedIn();

$username = $_SESSION['username'];

// Traitement de la déconnexion
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
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
    <a href="film.php">Aller à la page des films</a>

    <!-- Bouton de déconnexion -->
    <form method="POST" action="">
        <input type="submit" name="logout" value="Se déconnecter">
    </form>
</body>
</html>
