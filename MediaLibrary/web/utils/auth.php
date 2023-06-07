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
    // Insérez ici votre logique pour obtenir l'ID de l'utilisateur connecté
    // Par exemple, vous pouvez utiliser une session ou un système d'authentification

    // Retourner l'ID de l'utilisateur connecté
    return $_SESSION['user_id'];
}
?>
