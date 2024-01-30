<?php
require('utils/config.php');
include 'utils/bootstrap.php';

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
    $result = mysqli_query($connect, $query);
    if (mysqli_num_rows($result) > 0) {
        $errorMessage = "Ce pseudo ou cette adresse e-mail est déjà utilisé(e).";
    } else {
        // Insérer l'utilisateur dans la base de données avec le rôle "user" par défaut
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
        $insertQuery = "INSERT INTO users (username, password_hash, email, role) VALUES ('$username', '$hashedPassword', '$email', 'user')";
        if (mysqli_query($connect, $insertQuery)) {
            // Connexion automatique après l'inscription
            session_start();
            $_SESSION['username'] = $username;
            header("Location: accueil/index.php");
            exit();
        } else {
            $errorMessage = "Erreur lors de l'inscription : " . mysqli_error($connect);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inscription</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
</head>
<body class="d-flex align-items-center justify-content-center bg-dark text-white" style="min-height: 100vh;">

    <div class="container text-center">
        <h1 class="my-4">Inscription</h1>

        <?php if (isset($errorMessage)) { ?>
            <p class="error-message"><?php echo $errorMessage; ?></p>
        <?php } ?>

        <form method="POST" action="" class="col-md-6 mx-auto pb-5">
            <div class="form-group pb-3">
                <label for="username">Pseudo:</label>
                <input type="text" name="username" id="username" class="form-control" required>
            </div>

            <div class="form-group pb-3">
                <label for="password">Mot de passe:</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>

            <div class="form-group pb-3">
                <label for="email">Adresse e-mail:</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>

            <div class="form-group pb-3">
                <button type="submit" class="btn btn-primary">S'inscrire</button>
            </div>
        </form>

        <p class="text-light">Déjà inscrit ? <a href="login.php" class="text-primary">Se connecter</a></p>
    </div>
</body>
</html>
