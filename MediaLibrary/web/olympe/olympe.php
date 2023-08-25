<?php
require_once '../utils/auth.php';
require_once '../utils/config.php';

// Démarrage de la session
session_start();

$username = $_SESSION['username'] ?? ''; // Utilisation de l'opérateur de fusion null
$email = $_SESSION['email'] ?? '';

// Vérification si l'utilisateur est autorisé à accéder à la page
if ($username !== "Nathan" || $email !== "nathan.gregoar@yahoo.fr") {
    // Redirection vers la page d'accueil
    header("Location: ../accueil/index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>L'Olympe - Choix de destination</title>
    <link rel="stylesheet" type="text/css" href="./olympe.css">
    <!-- Inclure le CSS pour le calendrier -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>
<body>
    <div class="navbar">
        <a href="../accueil/index.php">Accueil</a>
        <a href="../olympe/olympe.php" style="color: #D7EBF3;">L'Olympe</a>
        <a href="../ecollyday/ecollyday.php">Ecollyday</a>        
    </div>
    <h1>Bienvenue dans l'Olympe <?php echo $username; ?></h1>
    <h2>Choix de destination 2024</h2>

    <!-- Formulaire -->
    <form action="traitement_formulaire.php" method="post">
        <label for="budget_min">Budget min :</label>
        <input type="number" id="budget_min" name="budget_min" required>
        <label for="budget_max">Budget max :</label>
        <input type="number" id="budget_max" name="budget_max" required>
        <br>
        <label for="dispo_date">Disponibilité :</label>
        <input type="text" id="dispo_date" name="dispo_date" class="flatpickr" required>
        <label for="not_dispo_date">Pas de disponibilité :</label>
        <input type="text" id="not_dispo_date" name="not_dispo_date" class="flatpickr" required>
        <br>
        <label for="pays_pref">Pays préférés :</label>
        <select id="pays_pref" name="pays_pref[]" multiple>
            <option value="pays1">Pays 1</option>
            <option value="pays2">Pays 2</option>
            <!-- Ajoutez d'autres options -->
        </select>
        <label for="pays_non_pref">Pays non préférés :</label>
        <select id="pays_non_pref" name="pays_non_pref[]" multiple>
            <option value="pays3">Pays 3</option>
            <option value="pays4">Pays 4</option>
            <!-- Ajoutez d'autres options -->
        </select>
        <br>
        <label>Transport :</label>
        <input type="checkbox" id="train" name="transport[]" value="train">
        <label for="train">Train</label>
        <input type="checkbox" id="avion" name="transport[]" value="avion">
        <label for="avion">Avion</label>
        <!-- Ajoutez d'autres options de transport -->
        <br>
        <button type="submit">Enregistrer</button>
    </form>

    <!-- Inclure le script pour le calendrier -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        flatpickr(".flatpickr", {
            dateFormat: "Y-m-d",
        });
    </script>
</body>
</html>
