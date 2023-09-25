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

// Tableau associatif pour faire correspondre les numéros aux noms des activités
$activites = [
    'visite' => 'Visite',
    'plage' => 'Plage',
    // Ajoutez d'autres activités ici
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $selectedActivities = [];

    // Parcourez les cases cochées pour collecter les noms des activités
    foreach ($_POST as $key => $value) {
        if (array_key_exists($key, $activites) && $value === 'on') {
            // Ajoutez le nom de l'activité à la liste
            $selectedActivities[] = $activites[$key];
        }
    }

    // Convertir les noms des activités en une chaîne séparée par des virgules
    $selectedActivitiesCSV = implode(', ', $selectedActivities);

    // Effectuer une mise à jour des données dans la base de données
    $update_query = "INSERT INTO olympe_activitees (added_by, activitees) VALUES (?, ?) ON DUPLICATE KEY UPDATE activitees = ?";
    $stmt = $connection->prepare($update_query);
    $stmt->bind_param("iss", $loggedInUser['id'], $selectedActivitiesCSV, $selectedActivitiesCSV);

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
    <title>L'Olympe - Activités souhaitées</title>
    <link rel="icon" type="image/png" href="https://static.vecteezy.com/system/resources/thumbnails/009/399/550/small/sun-icon-set-clipart-design-illustration-free-png.png">
    <link rel="stylesheet" type="text/css" href="./activitees.css">
</head>
<body>
    <div class="navbar">
        <a href="../../accueil/index.php">Accueil</a>
        <a href="../../olympe/olympe.php">L'Olympe</a>
        <a href="../../olympe/modifdispo/modifdispo.php">Modifier mes dispo</a> 
        <a href="../../olympe/activitees/activitees.php" style="color: #D7EBF3;">Activités souhaitées</a>  
        <a href="../../ecollyday/ecollyday.php">Ecollyday</a>        
    </div>
    <h1>Bienvenue dans l'Olympe <?= $username; ?> - Activités souhaitées</h1>

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
                <?php foreach ($activites as $key => $activityName) : ?>
                    <div>
                        <input type="checkbox" id="<?= $key; ?>" name="<?= $key; ?>" <?= isChecked($key); ?>>
                        <label for="<?= $key; ?>"><?= $activityName; ?></label>
                    </div>
                <?php endforeach; ?>
                <!-- Ajoutez d'autres activités ici -->
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

<?php
function isChecked($checkboxName) {
    if (isset($_POST[$checkboxName]) && $_POST[$checkboxName] === 'on') {
        return 'checked';
    }
    return '';
}
?>
