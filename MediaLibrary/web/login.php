<?php
require('config.php');

// Fonction de vérification si l'utilisateur est déjà connecté
function checkLoggedIn() {
    session_start();
    if (isset($_SESSION['username'])) {
        header("Location: index.php");
        exit();
    }
}

// Vérification si l'utilisateur est déjà connecté
checkLoggedIn();

// Traitement de la connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Vérifier si l'utilisateur existe dans la base de données
    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['username'] = $username;
            header("Location: index.php");
            exit();
        } else {
            echo "Mot de passe incorrect.";
        }
    } else {
        echo "Cet utilisateur n'existe pas.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Connexion</title>
</head>
<body>
    <h1>Connexion</h1>
    <form method="POST" action="">
        <label>Pseudo:</label>
        <input type="text" name="username" required><br>

        <label>Mot de passe:</label>
        <input type="password" name="password" required><br>

        <input type="submit" value="Se connecter">
    </form>
    <p>Pas encore inscrit ? <a href="register.php">S'inscrire</a></p>
</body>
</html>