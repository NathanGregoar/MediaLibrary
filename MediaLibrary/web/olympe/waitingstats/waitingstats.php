<?php
require_once '../../utils/auth.php';
require_once '../../utils/config.php';

// Démarrage de la session
session_start();

$username = $_SESSION['username'] ?? '';
$email = $_SESSION['email'] ?? '';
$loggedInUser = getLoggedInUser();

// Vérification si l'utilisateur est autorisé à accéder à la page
$allowedRoles = ["admin", "olympe"]; // Rôles autorisés
if (!in_array($loggedInUser['role'], $allowedRoles)) {
    header("Location: ../../accueil/index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>L'Olympe - Waiting Stats</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            text-align: center;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }
        h1 {
            color: #3498db;
        }
        p {
            font-size: 18px;
        }
        .god-image {
            max-width: 100px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="your-god-image.png" alt="Image de l'Olympe" class="god-image">
        <h1>Cher Dieu de l'Olympe,</h1>
        <p>Merci de votre visite sur la page de statistiques, mais pour l'instant, il semble que vous soyez le seul divin à avoir partagé vos préférences.</p>
        <p>Nous attendons que d'autres Dieux de l'Olympe se manifestent pour que nous puissions créer des statistiques intéressantes et divertissantes.</p>
        <p>Revenez bientôt, les étoiles célestes brillent de l'espoir que vous ne serez pas seul très longtemps.</p>
        <p>Bien à vous,</p>
        <p>L'équipe de L'Olympe</p>
    </div>
</body>
</html>
