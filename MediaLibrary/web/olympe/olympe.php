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
    <form action="traitement_formulaire.php" method="post" class="form-container">
        <div class="form-column">
            <div class="input-group">
                <label for="budget_min">Budget min :</label>
                <input type="number" id="budget_min" name="budget_min" min="1" required>
            </div>
            <div class="input-group">
                <label for="dispo_date">Disponibilité :</label>
                <div id="dispo_date" class="flatpickr"></div>
            </div>
            <div class="input-group">
                <label for="pays_pref">Pays préférés :</label>
                <select id="pays_pref" name="pays_pref[]" multiple size="5" class="country-select">
                    <!-- Ajouter ici les pays européens -->
                </select>
                <div class="ranking-label">Classement pays préférés :</div>
                <input type="number" name="classement_pref[]" min="1" max="5" class="ranking-input">
            </div>
            <div class="transport-group">
                <label>Transport :</label>
                <input type="checkbox" id="train" name="transport[]" value="train" class="transport-checkbox">
                <label for="train" class="transport-label">Train</label>
                <input type="checkbox" id="avion" name="transport[]" value="avion" class="transport-checkbox">
                <!-- Ajouter d'autres options de transport -->
            </div>
        </div>
        <div class="form-column">
            <div class="input-group">
                <label for="budget_max">Budget max :</label>
                <input type="number" id="budget_max" name="budget_max" min="1" required>
            </div>
            <div class="input-group">
                <label for="not_dispo_date">Pas de disponibilité :</label>
                <div id="not_dispo_date" class="flatpickr"></div>
            </div>
            <div class="input-group">
                <label for="pays_non_pref">Pays non préférés :</label>
                <select id="pays_non_pref" name="pays_non_pref[]" multiple size="3" class="country-select">
                    <!-- Ajouter ici les pays européens -->
                </select>
                <div class="ranking-label">Top 3 pays non préférés :</div>
                <input type="text" name="top_non_pref[]" class="ranking-input">
            </div>
            <div class="button-group">
                <button type="submit" class="submit-button">Enregistrer</button>
            </div>
        </div>
    </form>

    <!-- Inclure le script pour la vérification en temps réel -->
    <script>
        const budgetMinInput = document.getElementById('budget_min');
        const budgetMaxInput = document.getElementById('budget_max');

        budgetMinInput.addEventListener('input', function() {
            checkBudgetValidity(this);
        });

        budgetMaxInput.addEventListener('input', function() {
            checkBudgetValidity(this);
        });

        function checkBudgetValidity(input) {
            const value = parseFloat(input.value);
            if (isNaN(value) || value <= 0) {
                input.classList.remove('valid');
                input.classList.add('invalid');
            } else {
                input.classList.remove('invalid');
                input.classList.add('valid');
            }
        }
    </script>

    <!-- Inclure le script pour le calendrier -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        flatpickr("#dispo_date", {
            mode: "multiple",
            dateFormat: "Y-m-d"
        });

        flatpickr("#not_dispo_date", {
            mode: "multiple",
            dateFormat: "Y-m-d"
        });
    </script>
</body>
</html>
