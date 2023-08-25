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
                <input type="text" id="dispo_date" name="dispo_date" class="flatpickr inline" required>
            </div>
            <div class="input-group">
                <button type="button" id="choosePrefCountriesBtn">Choix du pays préféré</button>
                <div id="prefCountries"></div>
            </div>
            <div class="transport-group">
                <label>Transport :</label>
                <input type="checkbox" id="train" name="transport[]" value="train" class="transport-checkbox">
                <label for="train" class="transport-label">Train</label>
                <input type="checkbox" id="avion" name="transport[]" value="avion" class="transport-checkbox">
                <label for="avion" class="transport-label">Avion</label>
            </div>
        </div>
        <div class="form-column">
            <div class="input-group">
                <label for="budget_max">Budget max :</label>
                <input type="number" id="budget_max" name="budget_max" min="1" required>
            </div>
            <div class="input-group">
                <label for="not_dispo_date">Pas de disponibilité :</label>
                <input type="text" id="not_dispo_date" name="not_dispo_date" class="flatpickr inline" required>
            </div>
            <div class="input-group">
                <button type="button" id="chooseNonPrefCountriesBtn">Choix du pays non préféré</button>
                <div id="nonPrefCountries"></div>
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
        flatpickr(".flatpickr", {
            mode: "multiple",
            dateFormat: "Y-m-d",
            inline: true,
            onChange: function(selectedDates, dateStr, instance) {
                const calendarContainer = instance._container;
                if (calendarContainer.classList.contains("flatpickr-calendar")) {
                    const dispoDates = document.querySelectorAll(".green-date");
                    const notDispoDates = document.querySelectorAll(".red-date");
                    dispoDates.forEach(date => {
                        date.classList.remove("green-date");
                    });
                    notDispoDates.forEach(date => {
                        date.classList.remove("red-date");
                    });
                    selectedDates.forEach(date => {
                        if (instance._input.id === "dispo_date") {
                            date.classList.add("green-date");
                        } else if (instance._input.id === "not_dispo_date") {
                            date.classList.add("red-date");
                        }
                    });
                }
            }
        });
    </script>
</body>
</html>
