<?php
require('utils/config.php');
include 'utils/bootstrap.php';

// Fonction de vérification si l'utilisateur est déjà connecté
function checkLoggedIn() {
    session_start();
    if (isset($_SESSION['username'])) {
        header("Location: accueil/index.php");
        exit();
    }
}

// Vérification si l'utilisateur est déjà connecté
checkLoggedIn();

// Traitement de la connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = $_POST['identifier'];
    $password = $_POST['password'];

    // Vérifier si l'utilisateur existe dans la base de données par email ou pseudo
    $query = "SELECT * FROM users WHERE email = '$identifier' OR username = '$identifier'";
    $result = mysqli_query($connect, $query);
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password_hash'])) {
            session_start();
            $_SESSION['username'] = $user['username'];
            header("Location: accueil/index.php");
            exit();
        } else {
            $errorMessage = "Mot de passe incorrect.";
        }
    } else {
        $errorMessage = "Cet utilisateur n'existe pas.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Connexion</title>
    <link rel="icon" href="images/icon.png" type="image/x-icon">
</head>
<body class="d-flex align-items-center justify-content-center bg-dark text-white" style="min-height: 100vh;">

    <div class="container text-center">
        <h1 class="my-4">Connexion</h1>

        <?php if (isset($errorMessage)) { ?>
            <p class="text-danger"><?php echo $errorMessage; ?></p>
        <?php } ?>

        <form method="POST" action="" class="col-md-6 mx-auto pb-5">
            <div class="form-group pb-3">
                <label for="identifier">Adresse e-mail ou Pseudo:</label>
                <input type="text" name="identifier" id="identifier" class="form-control text-center" required>
            </div>

            <div class="form-group pb-3">
                <label for="password">Mot de passe:</label>
                <input type="password" name="password" id="password" class="form-control text-center" required>
            </div>

            <div class="form-group pb-3">
                <button type="submit" class="btn btn-primary">Se connecter <i class="bi bi-door-open"></i></button>
            </div>
        </form>

        <p class="text-light">Pas encore inscrit ? <a href="register.php" class="text-primary">S'inscrire</a></p>
    </div>
</body>
</html>
