<?php
require_once '../utils/auth.php';

$username = $_SESSION['username'];
$email = $_SESSION['email'];

// Vérification si l'utilisateur est autorisé à accéder à la page d'administration
if ($username !== "Nathan" || $email !== "nathan.gregoar@yahoo.fr") {
    // Redirection vers la page d'accueil
    header("Location: ../accueil/index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Accueil</title>
    <link rel="stylesheet" type="text/css" href="./accueil.css">
</head>
<body>
    <div class="container">
        <div class="section-links">
            <a href="gestion_db.php" class="section-link">
                <span class="section-link-text">Gestion base de donnée</span>
            </a>
            <a href="gestion_donnees.php" class="section-link">
                <span class="section-link-text">Gestion des données</span>
            </a>
            <a href="structure_db.php" class="section-link">
                <span class="section-link-text">Structure base de donnée</span>
            </a>
            <a href="contenu_db.php" class="section-link">
                <span class="section-link-text">Contenu base de donnée</span>
            </a>
            <!-- Ajoutez ici d'autres liens vers vos différentes sections -->
        </div>
    </div>
</body>
</html>
