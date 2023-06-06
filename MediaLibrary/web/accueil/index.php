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

// Vérification si le bouton de déconnexion a été cliqué
if (isset($_POST['logout'])) {
    // Suppression de toutes les variables de session
    session_unset();
    // Destruction de la session
    session_destroy();
    // Redirection vers la page de connexion
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
    <form method="post" action="">
        <input type="submit" name="logout" value="Déconnexion">
    </form>
    <a href="../film/film.php">Aller à la page des films</a>
</body>
</html>
