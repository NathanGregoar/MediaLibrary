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
        // Effectuer la logique pour récupérer l'ID de l'utilisateur à partir de la base de données
        $host = 'db';
        $user = 'nathan';
        $password = '444719';
        $database = 'media_library';

        $connection = new mysqli($host, $user, $password, $database);

        if ($connection->connect_error) {
            die('Erreur de connexion : ' . $connection->connect_error);
        }

        $loggedInUsername = $_SESSION['username'];

        $query = "SELECT id FROM users WHERE username = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("s", $loggedInUsername);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $loggedInUserId = $row['id'];
            $_SESSION['user_id'] = $loggedInUserId; // Stocker l'ID de l'utilisateur en session pour les prochaines requêtes
            $stmt->close();
            $connection->close();
            return $loggedInUserId;
        } else {
            $stmt->close();
            $connection->close();
            return null;
        }
    }
}
?>
