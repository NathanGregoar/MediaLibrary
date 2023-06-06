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
?>
<!DOCTYPE html>
<html>
<head>
    <title>Films</title>
</head>
<body>
    <h1>Bienvenue sur la page des films, <?php echo $username; ?> !</h1>
    <p>Ici, vous pouvez consulter une liste de films.</p>
</body>
</html>
