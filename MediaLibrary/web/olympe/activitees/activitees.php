<?php
require_once '../../utils/auth.php';
require_once '../../utils/config.php';
session_start();

$username = $_SESSION['username'] ?? '';
$loggedInUser = getLoggedInUser();

$allowedRoles = ["admin"]; // Rôles autorisés
if (!in_array($loggedInUser['role'], $allowedRoles)) {
    header("Location: ../../olympe/olympe.php");
    exit();
}

$connection = new mysqli('db', 'nathan', '444719', 'media_library');

if ($connection->connect_error) {
    die('Erreur de connexion : ' . $connection->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $selectedButtons = $_POST['selected_buttons'] ?? [];

    // Convertir les boutons sélectionnés en une chaîne CSV
    $selectedButtonsCSV = implode(', ', $selectedButtons);

    // Effectuer une mise à jour des données dans la base de données
    $update_query = "UPDATE olympe SET activitees = ? WHERE added_by = ?";
    $stmt = $connection->prepare($update_query);
    $stmt->bind_param("si", $selectedButtonsCSV, $loggedInUser['id']);

    if ($stmt->execute()) {
        $successMessage = "Mise à jour réussie !";
    } else {
        $errorMessage = "Erreur lors de la mise à jour : " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>L'Olympe - Activitées souhaitées</title>
    <link rel="icon" type="image/png" href="https://static.vecteezy.com/system/resources/thumbnails/009/399/550/small/sun-icon-set-clipart-design-illustration-free-png.png">
    <link rel="stylesheet" type="text/css" href="./activitees.css">
</head>
<body>
    <div class="navbar">
        <a href="../../accueil/index.php">Accueil</a>
        <a href="../../olympe/olympe.php">L'Olympe</a>
        <a href="../../olympe/modifdispo/modifdispo.php">Modifier mes dispo</a> 
        <a href="../../olympe/activitees/activitees.php" style="color: #D7EBF3;">Activitées souhaitées</a>  
        <a href="../../ecollyday/ecollyday.php">Ecollyday</a>        
    </div>
    <h1>Bienvenue dans l'Olympe <?= $username; ?> - Activitées souhaitées</h1>

    <div id="messageContainer">
        <?php if (!empty($successMessage)) : ?>
            <div class="alert alert-success"><?= $successMessage; ?></div>
        <?php endif; ?>
        <?php if (!empty($errorMessage)) : ?>
            <div class="alert alert-error"><?= $errorMessage; ?></div>
        <?php endif; ?>
    </div>

    <form method="post" class="form-container">
        <div class="form-grid">
            <div class="input-group">
                <label>Activités souhaitées :</label>
                <div class="toggle-button" data-toggle="activite_1">Activité 1</div>
                <div class="toggle-button" data-toggle="activite_2">Activité 2</div>
                <div class="toggle-button" data-toggle="activite_3">Activité 3</div>
                <!-- Ajoutez d'autres boutons d'activité ici -->
            </div>
        </div>

        <div class="centered">
            <div class="button-group">
                <button type="submit" class="submit-button">Enregistrer</button>
            </div>
        </div>
    </form>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const toggleButtons = document.querySelectorAll(".toggle-button");

            toggleButtons.forEach((button) => {
                button.addEventListener("click", () => {
                    button.classList.toggle("selected");
                });
            });
        });
    </script>
</body>
</html>
