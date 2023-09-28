<?php
require_once '../../utils/auth.php';
require_once '../../utils/config.php';
session_start();

$username = $_SESSION['username'] ?? '';
$loggedInUser = getLoggedInUser();

$allowedRoles = ["admin"]; // Rôles autorisés
if (!in_array($loggedInUser['role'], $allowedRoles)) {
    header("Location: ../../olympe/olympe.php");
    exit();
}

$connection = new mysqli('db', 'nathan', '444719', 'media_library');

if ($connection->connect_error) {
    die('Erreur de connexion : ' . $connection->connect_error);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>L'Olympe - Anniversaires</title>
    <link id="icon" rel="icon" type="image/png" href="https://i.goopics.net/oqioit.png">
    <link rel="stylesheet" type="text/css" href="./anniversaire.css">
</head>
<body>
    <div class="navbar">
        <a href="../../accueil/index.php">Accueil</a>
        <a href="../../olympe/olympe.php">L'Olympe</a> 
        <a href="../../olympe/anniversaire/anniversaire.php" style="color: #D7EBF3;">Anniversaire</a> 
    </div>
</body>
</html>