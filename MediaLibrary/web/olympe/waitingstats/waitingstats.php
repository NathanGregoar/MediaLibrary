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
    <!-- Inclure l'icône de page -->
    <link rel="icon" type="image/png" href="https://static.vecteezy.com/system/resources/thumbnails/009/399/550/small/sun-icon-set-clipart-design-illustration-free-png.png">
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
            background-color: #E1E9EC;
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
        .envelope-emoji {
            font-size: 20px;
        }
        .airline-logo {
            width: 75px;
            height: 75px;
        }
        .navbar {
            display: flex;
            position: absolute;
            top: 0;
            left: 0;
            background-color: #333;
            color: #fff;
            padding: 10px 0;
            width: 100%;
        }

        .navbar a {
            color: #fff;
            text-decoration: none;
            font-weight: bold;
            font-size: 18px;
            transition: color 0.3s ease;
            margin: 10px;
        }

        .navbar a:hover {
            color: #86c1ff;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="../../accueil/index.php">Accueil</a>
        <a href="../../olympe/olympe.php">L'Olympe</a>
        <a href="207.154.232.200/olympe/modifdispo/modifdispo.php">Modifier mes dispo</a>  
        <a href="../../ecollyday/ecollyday.php">Ecollyday</a>   
    </div>

    <div class="container">
        <img src="https://cdn-icons-png.flaticon.com/512/4793/4793084.png" alt="Hermes AirLine" class="airline-logo">
        <h1>Cher Dieu de l'Olympe,</h1>
        <p>Merci de votre visite sur la page de statistiques, mais pour l'instant, il semble que vous soyez le seul divin à avoir partagé vos préférences.</p>
        <p>Nous attendons que d'autres Dieux de l'Olympe se manifestent pour que nous puissions créer des statistiques intéressantes et divertissantes.</p>
        <p>Revenez bientôt, les étoiles célestes brillent de l'espoir que vous ne serez pas seul très longtemps.</p>
        <p>Bien à vous,</p>
        <p>Votre Dieu préféré, Hermes <span class="envelope-emoji">✉️</span></p>
    </div>
</body>
</html>
