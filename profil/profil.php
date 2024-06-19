<?php
require_once '../utils/auth.php';
require_once '../utils/config.php';
include '../utils/bootstrap.php';

$message = '';

// Get the logged-in user
$loggedInUser = getLoggedInUser();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $connect = mysqli_connect($host, $username, $password, $dbName);
    if (!$connect) {
        die('Erreur de connexion à la base de données : ' . mysqli_connect_error());
    }

    $userId = $loggedInUser['id'];

    // Handle password change
    if (isset($_POST['change_password'])) {
        $currentPassword = $_POST['current_password'];
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];

        if ($newPassword !== $confirmPassword) {
            $message = '<div class="alert alert-danger">Les nouveaux mots de passe ne correspondent pas.</div>';
        } else {
            $selectSql = "SELECT password_hash FROM users WHERE id = ?";
            $selectStmt = $connect->prepare($selectSql);
            $selectStmt->bind_param("i", $userId);
            $selectStmt->execute();
            $result = $selectStmt->get_result();
            $user = $result->fetch_assoc();

            if (password_verify($currentPassword, $user['password_hash'])) {
                $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
                $updateSql = "UPDATE users SET password_hash = ? WHERE id = ?";
                $updateStmt = $connect->prepare($updateSql);
                $updateStmt->bind_param("si", $newPasswordHash, $userId);

                if ($updateStmt->execute()) {
                    $message = '<div class="alert alert-success">Votre mot de passe a été mis à jour avec succès.</div>';
                } else {
                    $message = '<div class="alert alert-danger">Erreur lors de la mise à jour du mot de passe : ' . $connect->error . '</div>';
                }
            } else {
                $message = '<div class="alert alert-danger">Le mot de passe actuel est incorrect.</div>';
            }
        }
    }

    // Handle username and email change
    if (isset($_POST['change_info'])) {
        $newUsername = $_POST['new_username'];
        $newEmail = $_POST['new_email'];

        $updateSql = "UPDATE users SET username = ?, email = ? WHERE id = ?";
        $updateStmt = $connect->prepare($updateSql);
        $updateStmt->bind_param("ssi", $newUsername, $newEmail, $userId);

        if ($updateStmt->execute()) {
            // Update session variables
            $_SESSION['username'] = $newUsername;
            $_SESSION['email'] = $newEmail;
            // Clear the cached user info
            unset($_SESSION['user']);

            $message = '<div class="alert alert-success">Vos informations ont été mises à jour avec succès.</div>';
        } else {
            $message = '<div class="alert alert-danger">Erreur lors de la mise à jour des informations : ' . $connect->error . '</div>';
        }
    }

    $connect->close();
}

// Re-fetch the updated user information
$loggedInUser = getLoggedInUser();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <title>Profil</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" type="image/png" href="../images/icon.png">
    <style>
        .progress-custom {
            height: 5px;
            border-top-left-radius: 0;
            border-top-right-radius: 0;
            border-bottom-left-radius: .25rem;
            border-bottom-right-radius: .25rem;
        }
    </style>
    <script>
        function updateProgress() {
            var totalFields = 2;
            var filledFields = 0;

            if (document.getElementById('new_username').value) filledFields++;
            if (document.getElementById('new_email').value) filledFields++;

            var progress = (filledFields / totalFields) * 100;
            document.getElementById('progressBar').style.width = progress + '%';
            document.getElementById('progressText').innerText = filledFields + '/' + totalFields;
        }

        document.addEventListener('DOMContentLoaded', function() {
            var inputs = document.querySelectorAll('#new_username, #new_email');
            inputs.forEach(function(input) {
                input.addEventListener('input', updateProgress);
            });
            updateProgress(); // Initial call to set the progress bar correctly at page load
        });
    </script>
</head>

<body class="bg-secondary">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-5">
        <div class="container-fluid">
            <a class="navbar-brand" href="../accueil/index.php">Accueil</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active text-warning" href="./profil.php">Profil</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container bg-dark text-light p-4 mt-4 rounded">
        <h1 class="display-5 text-center text-decoration-underline mb-4">Mon Profil</h1>
        <?php echo $message; ?>
        <div class="card mb-3">
            <div class="card-header">
                <h2>Modifier mes informations</h2>
            </div>
            <div class="progress progress-custom mt-0">
                <div id="progressBar" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="new_username" class="form-label">Nouveau nom d'utilisateur</label>
                        <input type="text" id="new_username" name="new_username" class="form-control" value="<?php echo htmlspecialchars($loggedInUser['username']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_email" class="form-label">Nouvelle adresse email</label>
                        <input type="email" id="new_email" name="new_email" class="form-control" value="<?php echo htmlspecialchars($loggedInUser['email']); ?>" required>
                    </div>
                    <input type="submit" name="change_info" value="Modifier les informations" class="btn btn-primary">
                </form>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-header">
                <h2>Modifier mon mot de passe</h2>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Mot de passe actuel</label>
                        <input type="password" id="current_password" name="current_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Nouveau mot de passe</label>
                        <input type="password" id="new_password" name="new_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirmer le nouveau mot de passe</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                    </div>
                    <input type="submit" name="change_password" value="Modifier le mot de passe" class="btn btn-success">
                </form>
            </div>
        </div>
    </div>
</body>

</html>
