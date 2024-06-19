<?php
$host = 'localhost';
$username = 'root';
$password = '';
$dbName = 'media_library';

// Connexion à la base de données
$connect = mysqli_connect($host, $username, $password, $dbName);

// Vérification de la connexion
if (!$connect) {
    die("Erreur de connexion à la base de données : " . mysqli_connect_error());
}
?>
