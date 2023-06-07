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
    if (isset($_SESSION['user_id'])) {
        return $_SESSION['user_id'];
    } else {
        return null;
    }
}
?>
