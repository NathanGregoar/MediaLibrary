<?php
session_start();

// Fonction de vérification si l'utilisateur est connecté
function checkLoggedIn() {
    if (!isset($_SESSION['username'])) {
        header("Location: ../login.php");
        exit();
    }
}

// Vérification si l'utilisateur est connecté
checkLoggedIn();

$username = $_SESSION['username'];

function getLoggedInUserId() {
    // Vérifiez si l'ID de l'utilisateur connecté est stocké dans la session
    if (isset($_SESSION['user_id'])) {
        // Retournez l'ID de l'utilisateur connecté
        return $_SESSION['user_id'];
    } else {
        // Si l'ID de l'utilisateur n'est pas disponible, retournez null
        return null;
    }
}
?>
