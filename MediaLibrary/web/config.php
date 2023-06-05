<?php
$host = 'db';
$username = 'nathan';
$password = '444719';
$dbName = 'media_library';

// Connexion à la base de données
$conn = mysqli_connect($host, $username, $password, $dbName);

// Vérification de la connexion
if (!$conn) {
    die("Erreur de connexion à la base de données : " . mysqli_connect_error());
}
?>
