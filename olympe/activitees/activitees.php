<?php
require_once '../../utils/auth.php';
require_once '../../utils/config.php';

$username = $_SESSION['username'] ?? '';
$role = $_SESSION['role'] ?? '';
$loggedInUser = getLoggedInUser();

$allowedRoles = ["admin"]; // Rôles autorisés
if (!in_array($role, $allowedRoles)) {
    header("Location: ../../olympe/olympe.php");
    exit();
}

if ($connect->connect_error) {
    die('Erreur de connexion : ' . $connect->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $selectedActivities = [];

    // Parcourez les cases cochées pour collecter les noms des activités
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'activite_') === 0 && $value === 'on') {
            // Récupérez le nom de l'activité en supprimant le préfixe "activite_"
            $activityName = substr($key, strlen('activite_'));
            $selectedActivities[] = $connect->real_escape_string(strtolower($activityName));
        }
    }

    // Récupérez les activités saisies dans le textarea, les séparez par des virgules, nettoyez-les et convertissez-les en minuscules
    if (!empty($_POST['autres_activites'])) {
        $otherActivities = $_POST['autres_activites'];
        $otherActivities = preg_replace('/\s*,\s*/', ',', $otherActivities); // Supprime les espaces avant et après les virgules
        $otherActivities = explode(',', $otherActivities);
        $otherActivities = array_map('trim', $otherActivities); // Supprime les espaces autour des activités
        $otherActivities = array_map('strtolower', $otherActivities); // Convertit en minuscules
        $selectedActivities = array_merge($selectedActivities, $otherActivities);
    }

    // Convertir les noms des activités en une chaîne séparée par des virgules
    $selectedActivitiesCSV = implode(', ', $selectedActivities);

    // Effectuer une mise à jour des données dans la base de données
    $update_query = "INSERT INTO olympe_activitees (added_by, activitees) VALUES (?, ?) ON DUPLICATE KEY UPDATE activitees = ?";
    $stmt = $connect->prepare($update_query);
    $stmt->bind_param("iss", $loggedInUser['id'], $selectedActivitiesCSV, $selectedActivitiesCSV);

    if ($stmt->execute()) {
        $successMessage = "Ajouts des activités réussie !";
    } else {
        $errorMessage = "Erreur lors de l'ajouts des activités : " . $stmt->error;
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
                <?php foreach (['Visite (Musée, etc)', 'Visite (Ville)', 'Plage', 'Randonnée', 'Kayak', 'Vélo', 'Parc (Attraction/Aquatique)', 'Spa'] as $activityName) : ?>
                    <div>
                        <input type="checkbox" id="activite_<?= strtolower($activityName); ?>" name="activite_<?= strtolower($activityName); ?>" <?= isChecked('activite_' . strtolower($activityName)); ?>>
                        <label for="activite_<?= strtolower($activityName); ?>"><?= $activityName; ?></label>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="input-group">
                <label for="autres_activites">Autres activités :</label>
                <small>(Veuillez séparer les activités par des virgules)</small>
                <textarea id="autres_activites" name="autres_activites" rows="4" cols="50"><?= isset($_POST['autres_activites']) ? htmlspecialchars($_POST['autres_activites']) : ''; ?></textarea>
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
