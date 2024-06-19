<?php
require_once '../utils/auth.php';
require_once '../utils/config.php';

$username = $_SESSION['username'];
$email = $_SESSION['email'];
$role = $_SESSION['role'];

// Vérification si l'utilisateur est autorisé à accéder à la page d'administration
if ($role !== "admin") {
    // Redirection vers la page d'accueil
    header("Location: ../accueil/index.php");
    exit();
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['change_user_password'])) {
        $selectedUsername = $_POST['username'];
        $newPassword = $_POST['new_password'];

        // Hash the new password
        $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update the password in the database
        $updateSql = "UPDATE users SET password_hash = ? WHERE username = ?";
        $updateStmt = $connect->prepare($updateSql);
        $updateStmt->bind_param("ss", $newPasswordHash, $selectedUsername);

        if ($updateStmt->execute()) {
            $message = '<div class="alert alert-success">Le mot de passe de l\'utilisateur ' . htmlspecialchars($selectedUsername) . ' a été mis à jour avec succès.</div>';
        } else {
            $message = '<div class="alert alert-danger">Erreur lors de la mise à jour du mot de passe : ' . $connect->error . '</div>';
        }
    } elseif (isset($_POST['change_user_roles'])) {
        $selectedUsername = $_POST['username_roles'];
        $selectedRoles = isset($_POST['roles']) ? $_POST['roles'] : [];

        // Get user ID from username
        $selectSql = "SELECT id FROM users WHERE username = ?";
        $selectStmt = $connect->prepare($selectSql);
        $selectStmt->bind_param("s", $selectedUsername);
        $selectStmt->execute();
        $result = $selectStmt->get_result();
        $user = $result->fetch_assoc();
        $userId = $user['id'];

        // Delete existing roles for the user
        $deleteSql = "DELETE FROM lien_users_roles WHERE user_id = ?";
        $deleteStmt = $connect->prepare($deleteSql);
        $deleteStmt->bind_param("i", $userId);
        $deleteStmt->execute();

        // Insert new roles
        $insertSql = "INSERT INTO lien_users_roles (user_id, role_id) VALUES (?, ?)";
        $insertStmt = $connect->prepare($insertSql);
        foreach ($selectedRoles as $roleId) {
            $insertStmt->bind_param("ii", $userId, $roleId);
            $insertStmt->execute();
        }

        $message = '<div class="alert alert-success">Les rôles de l\'utilisateur ' . htmlspecialchars($selectedUsername) . ' ont été mis à jour avec succès.</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <title>Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" type="image/png" href="../images/icon.png">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
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
                        <a class="nav-link active text-warning" href="./admin.php">Admin</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container text-light p-4 mt-4 rounded">
        <?php echo $message; ?>
        <div class="card mb-3 bg-dark">
            <div class="card-header">
                <h2>Modifier le mot de passe d'un utilisateur</h2>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3 position-relative">
                        <label for="username" class="form-label">Nom d'utilisateur</label>
                        <input type="text" id="username" name="username" class="form-control" required autocomplete="off">
                        <div id="userSuggestions" class="dropdown-menu position-absolute w-100"></div>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Nouveau mot de passe</label>
                        <input type="password" id="new_password" name="new_password" class="form-control" required>
                    </div>
                    <input type="submit" name="change_user_password" value="Modifier le mot de passe" class="btn btn-success">
                </form>
            </div>
        </div>
        <div class="card mb-3 bg-dark">
            <div class="card-header">
                <h2>Modifier les rôles d'un utilisateur</h2>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3 position-relative">
                        <label for="username_roles" class="form-label">Nom d'utilisateur</label>
                        <input type="text" id="username_roles" name="username_roles" class="form-control" required autocomplete="off">
                        <div id="userRolesSuggestions" class="dropdown-menu position-absolute w-100"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rôles</label>
                        <div id="rolesCheckboxes">
                            <!-- Checkboxes will be populated by JavaScript -->
                        </div>
                    </div>
                    <input type="submit" name="change_user_roles" value="Modifier les rôles" class="btn btn-primary">
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#username').on('keyup', function() {
                let query = $(this).val();
                if (query.length > 1) {
                    $.ajax({
                        url: './tools/fetch_usernames.php',
                        method: 'POST',
                        data: { query: query },
                        success: function(data) {
                            $('#userSuggestions').fadeIn();
                            $('#userSuggestions').html(data);
                        }
                    });
                } else {
                    $('#userSuggestions').fadeOut();
                }
            });

            $(document).on('click', '.dropdown-item', function() {
                $('#username').val($(this).text());
                $('#userSuggestions').fadeOut();
            });

            $('#username_roles').on('keyup', function() {
                let query = $(this).val();
                if (query.length > 1) {
                    $.ajax({
                        url: './tools/fetch_usernames.php',
                        method: 'POST',
                        data: { query: query },
                        success: function(data) {
                            $('#userRolesSuggestions').fadeIn();
                            $('#userRolesSuggestions').html(data);
                        }
                    });
                } else {
                    $('#userRolesSuggestions').fadeOut();
                }
            });

            $(document).on('click', '.dropdown-item', function() {
                $('#username_roles').val($(this).text());
                $('#userRolesSuggestions').fadeOut();
                // Fetch current roles for the selected user
                $.ajax({
                    url: './tools/fetch_user_roles.php',
                    method: 'POST',
                    data: { username: $(this).text() },
                    success: function(data) {
                        $('#rolesCheckboxes').html(data);
                    }
                });
            });

            // Fetch roles and populate the roles checkboxes on page load
            $.ajax({
                url: './tools/fetch_roles_checkboxes.php',
                method: 'GET',
                success: function(data) {
                    $('#rolesCheckboxes').html(data);
                }
            });
        });
    </script>
</body>

</html>
