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

        $query = "SELECT * FROM users WHERE username = ?";
        $stmt = $connect->prepare($query);
        $stmt->bind_param("s", $loggedInUsername);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $loggedInUser = $row;
            $_SESSION['user'] = $loggedInUser;
            $_SESSION['email'] = $row['email'];
            $_SESSION['role'] = $row['role'];
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
