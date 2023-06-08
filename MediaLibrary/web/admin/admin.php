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
    <title>Admin</title>
    <link rel="stylesheet" type="text/css" href="./admin.css">
</head>
<body>
    <div class="container">
        <h1 class="welcome-message">Bienvenue sur la page Admin, <?php echo $username; ?> !</h1>
        <div class="section-links">
            <a href="../accueil/index.php" class="section-link">
                <span class="section-link-text">Accueil</span>
            </a>
            <a href="./gestion_db.php" class="section-link">
                <span class="section-link-text">Gestion base de donnée</span>
            </a>
            <a href="./gestion_donnees.php" class="section-link">
                <span class="section-link-text">Gestion des données</span>
            </a>
            <!-- Ajoutez ici d'autres liens vers vos différentes sections -->
        </div>
    </div>
</body>
</html>
