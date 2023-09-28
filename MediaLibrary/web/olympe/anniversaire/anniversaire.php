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
    <link rel="icon" type="image/png" href="https://i.goopics.net/oqioit.png">
    <link rel="stylesheet" type="text/css" href="./anniversaire.css">
</head>
<body>
    <div class="navbar">
        <a href="../../accueil/index.php">Accueil</a>
        <a href="../../olympe/olympe.php">L'Olympe</a> 
        <a href="../../olympe/anniversaire/anniversaire.php" class="select">Anniversaire</a> 
    </div>

    <!-- Formulaire de Date d'Anniversaire -->
    <h2>Date d'Anniversaire</h2>
    <form action="traitement_date_anniversaire.php" method="post">
        <label for="date_anniversaire">Date d'anniversaire :</label>
        <input type="date" id="date_anniversaire" name="date_anniversaire">
        <input type="submit" value="Enregistrer">
    </form>

    <!-- Formulaire pour Enregistrer un Cadeau Souhaité -->
    <h2>Enregistrer un Cadeau Souhaité</h2>
    <form action="traitement_cadeau.php" method="post" enctype="multipart/form-data">
        <label for="description_cadeau">Description de l'objet :</label>
        <input type="text" id="description_cadeau" name="description_cadeau">
        
        <label for="photo_cadeau">Photo de l'objet :</label>
        <input type="file" id="photo_cadeau" name="photo_cadeau">
        
        <label for="categorie_cadeau">Catégorie/Thème :</label>
        <input type="text" id="categorie_cadeau" name="categorie_cadeau">
        
        <input type="submit" value="Enregistrer">
    </form>
</body>
</html>
