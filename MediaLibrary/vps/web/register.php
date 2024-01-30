<?php
require('utils/config.php');

// Fonction de vérification si l'utilisateur est déjà connecté
function checkLoggedIn()
{
    session_start();
    if (isset($_SESSION['username'])) {
        header("Location: accueil/index.php");
        exit();
    }
}

// Vérification si l'utilisateur est déjà connecté
checkLoggedIn();

// Traitement de l'inscription
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    // Vérifier si l'utilisateur existe déjà dans la base de données
    $query = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        $errorMessage = "Ce pseudo ou cette adresse e-mail est déjà utilisé(e).";
    } else {
        // Insérer l'utilisateur dans la base de données avec le rôle "user" par défaut
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
        $insertQuery = "INSERT INTO users (username, password_hash, email, role) VALUES ('$username', '$hashedPassword', '$email', 'user')";
        if (mysqli_query($conn, $insertQuery)) {
            // Connexion automatique après l'inscription
            session_start();
            $_SESSION['username'] = $username;
            header("Location: accueil/index.php");
            exit();
        } else {
            $errorMessage = "Erreur lors de l'inscription : " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inscription</title>
    <link rel="stylesheet" type="text/css" href="./auth.css">
</head>
<body>
    <h1>Inscription</h1>
    <?php if (isset($errorMessage)) { ?>
        <p class="error-message"><?php echo $errorMessage; ?></p>
    <?php } ?>
    <form method="POST" action="">
        <label>Pseudo:</label>
        <input type="text" name="username" required><br>

        <label>Mot de passe:</label>
        <input type="password" name="password" required><br>

        <label>Adresse e-mail:</label>
        <input type="email" name="email" required><br>

        <input type="submit" value="S'inscrire">
    </form>
    <p>Déjà inscrit ? <a href="login.php">Se connecter</a></p>
</body>
</html>
