<?php
require_once '../../utils/auth.php';
require_once '../../utils/config.php';
session_start();

$username = $_SESSION['username'] ?? '';
$loggedInUser = getLoggedInUser();

$allowedRoles = ["admin", "olympe"]; // Rôles autorisés
if (!in_array($loggedInUser['role'], $allowedRoles)) {
    header("Location: ../../accueil/index.php");
    exit();
}

$connection = new mysqli('db', 'nathan', '444719', 'media_library');

if ($connection->connect_error) {
    die('Erreur de connexion : ' . $connection->connect_error);
}

$check_query = "SELECT COUNT(*) FROM olympe WHERE added_by = ?";
$stmt_check = $connection->prepare($check_query);
$stmt_check->bind_param("i", $loggedInUser['id']);
$stmt_check->execute();
$stmt_check->bind_result($existingRecords);
$stmt_check->fetch();
$stmt_check->close();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $budget_min = $_POST['budget_min'];
    $budget_max = $_POST['budget_max'];
    $dispo_dates = $_POST['dispo_date'];
    $not_dispo_dates = $_POST['not_dispo_date'];
    $transport = isset($_POST['transport']) ? implode(', ', $_POST['transport']) : '';
    $pref_countries = $_POST['pref_countries_selected'] ?? '';
    $non_pref_countries = $_POST['non_pref_countries_selected'] ?? '';

    // Effectuez une mise à jour des données existantes dans la base de données
    $update_query = "UPDATE olympe SET budget_min = ?, budget_max = ?, dispo = ?, indispo = ?, transport = ?, pays_oui = ?, pays_non = ? WHERE added_by = ?";
    $stmt = $connection->prepare($update_query);
    $stmt->bind_param("iisssssi", $budget_min, $budget_max, $dispo_dates, $not_dispo_dates, $transport, $pref_countries, $non_pref_countries, $loggedInUser['id']);

    if ($stmt->execute()) {
        // Mise à jour réussie, récupérez les nouvelles données depuis la base de données
        $get_updated_dates_query = "SELECT dispo, indispo FROM olympe WHERE added_by = ?";
        $stmt_get_updated_dates = $connection->prepare($get_updated_dates_query);
        $stmt_get_updated_dates->bind_param("i", $loggedInUser['id']);
        $stmt_get_updated_dates->execute();
        $stmt_get_updated_dates->bind_result($dispoDates, $notDispoDates);
        $stmt_get_updated_dates->fetch();
        $stmt_get_updated_dates->close();

        // Mettez à jour les valeurs par défaut pour les dates de disponibilité et d'indisponibilité
        $dispoDatesDefaultValue = $dispoDates;
        $notDispoDatesDefaultValue = $notDispoDates;

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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
                <div class="flex"> 
                    <label for="budget_min">Budget min :</label>
                    <small>(Transport & Location compris)</small>
                    <input type="number" id="budget_min" name="budget_min" min="1" value="<?= $budgetMinDefaultValue; ?>"><br>

                    <label for="budget_max">Budget max :</label>
                    <small>(Transport & Location compris)</small>
                    <input type="number" id="budget_max" name="budget_max" min="1" value="<?= $budgetMaxDefaultValue; ?>"><br>
                </div>
            </div>
            <div class="input-group">
                <label for="dispo_date">Mes disponibilités :</label>
                <input type="text" id="dispo_date" name="dispo_date" class="flatpickr inline" required data-dispo-dates="<?= $dispoDatesDefaultValue; ?>">
            </div>
            <div class="input-group">
                <label for="not_dispo_date">Mes indisponibilités :</label>
                <input type="text" id="not_dispo_date" name="not_dispo_date" class="flatpickr inline" required data-non-dispo-dates="<?= $notDispoDatesDefaultValue; ?>">
            </div>
        </div>

        <div class="centered">
            <div class="button-group">
                <button type="submit" class="submit-button">Enregistrer</button>
            </div>
        </div>
    </form>
</body>
</html>