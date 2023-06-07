<?php
require('utils/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $newPassword = generateRandomPassword();
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $updateQuery = "UPDATE users SET password_hash = '$hashedPassword' WHERE email = '$email'";
        if (mysqli_query($conn, $updateQuery)) {
            $subject = "Réinitialisation du mot de passe";
            $message = "Votre nouveau mot de passe est : " . $newPassword;
            $headers = "From: nathan.gregoar@yahoo.fr";

            mail($email, $subject, $message, $headers);

            header("Location: login.php?reset=success");
            exit();
        } else {
            $errorMessage = "Erreur lors de la réinitialisation du mot de passe : " . mysqli_error($conn);
        }
    } else {
        $errorMessage = "Aucun utilisateur avec cette adresse e-mail n'a été trouvé.";
    }
}

function generateRandomPassword($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $password;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Mot de passe oublié</title>
    <link rel="stylesheet" type="text/css" href="auth.css">
</head>
<body>
    <h1>Mot de passe oublié</h1>
    <?php if (isset($errorMessage)) { ?>
        <p class="error-message"><?php echo $errorMessage; ?></p>
    <?php } ?>
    <form method="POST" action="">
        <label>Adresse e-mail:</label>
        <input type="email" name="email" required><br>

        <input type="submit" value="Réinitialiser le mot de passe">
    </form>
    <p><a href="login.php">Retour à la page de connexion</a></p>
</body>
</html>
