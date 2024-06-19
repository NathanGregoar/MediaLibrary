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

function getLoggedInUser() {
    if (isset($_SESSION['user'])) {
        return $_SESSION['user'];
    } else {
        // Effectuer la logique pour récupérer les informations de l'utilisateur à partir de la base de données
        $host = 'localhost';
        $user = 'root';
        $password = '';
        $database = 'media_library';

        $connect = new mysqli($host, $user, $password, $database);

        if ($connect->connect_error) {
            die('Erreur de connexion : ' . $connect->connect_error);
        }

        $loggedInUsername = $_SESSION['username'];

        // Modifier la requête pour récupérer l'utilisateur et son rôle
        $query = "
            SELECT u.*, r.role_name AS role_name 
            FROM users u
            JOIN lien_users_roles lur ON u.id = lur.user_id
            JOIN roles r ON lur.role_id = r.id
            WHERE u.username = ?
        ";
        $stmt = $connect->prepare($query);
        $stmt->bind_param("s", $loggedInUsername);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $loggedInUser = $row;
            $_SESSION['user'] = $loggedInUser;
            $_SESSION['email'] = $row['email'];
            $_SESSION['role'] = $row['role_name']; // Utiliser le nom du rôle récupéré
            $stmt->close();
            $connect->close();
            return $loggedInUser;
        } else {
            $stmt->close();
            $connect->close();
            return null;
        }
    }
}

// Récupérer l'utilisateur connecté lors de l'inclusion du fichier auth.php
getLoggedInUser();
?>
