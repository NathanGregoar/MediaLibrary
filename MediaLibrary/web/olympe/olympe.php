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
                <label for="pays_pref">Pays préférés :</label>
                <select id="pays_pref" name="pays_pref[]" multiple size="5" class="country-select">
                    <option value="allemagne">Allemagne</option>
                    <option value="autriche">Autriche</option>
                    <option value="belgique">Belgique</option>
                    <option value="bulgarie">Bulgarie</option>
                    <option value="chypre">Chypre</option>
                    <option value="croatie">Croatie</option>
                    <option value="danemark">Danemark</option>
                    <option value="espagne">Espagne</option>
                    <option value="estonie">Estonie</option>
                    <option value="finlande">Finlande</option>
                    <option value="france">France</option>
                    <option value="grece">Grèce</option>
                    <option value="hongrie">Hongrie</option>
                    <option value="irlande">Irlande</option>
                    <option value="italie">Italie</option>
                    <option value="lettonie">Lettonie</option>
                    <option value="lituanie">Lituanie</option>
                    <option value="luxembourg">Luxembourg</option>
                    <option value="malte">Malte</option>
                    <option value="pays-bas">Pays-Bas</option>
                    <option value="pologne">Pologne</option>
                    <option value="portugal">Portugal</option>
                    <option value="republique-tcheque">République tchèque</option>
                    <option value="roumanie">Roumanie</option>
                    <option value="slovaquie">Slovaquie</option>
                    <option value="slovenie">Slovénie</option>
                    <option value="suede">Suède</option>
                </select>
                <div class="ranking-label">Classement pays préférés :</div>
                <input type="number" name="classement_pref[]" min="1" max="5" class="ranking-input">
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
                <label for="pays_non_pref">Pays non préférés :</label>
                <select id="pays_non_pref" name="pays_non_pref[]" multiple size="3" class="country-select">
                    <option value="allemagne">Allemagne</option>
                    <option value="autriche">Autriche</option>
                    <option value="belgique">Belgique</option>
                    <option value="bulgarie">Bulgarie</option>
                    <option value="chypre">Chypre</option>
                    <option value="croatie">Croatie</option>
                    <option value="danemark">Danemark</option>
                    <option value="espagne">Espagne</option>
                    <option value="estonie">Estonie</option>
                    <option value="finlande">Finlande</option>
                    <option value="france">France</option>
                    <option value="grece">Grèce</option>
                    <option value="hongrie">Hongrie</option>
                    <option value="irlande">Irlande</option>
                    <option value="italie">Italie</option>
                    <option value="lettonie">Lettonie</option>
                    <option value="lituanie">Lituanie</option>
                    <option value="luxembourg">Luxembourg</option>
                    <option value="malte">Malte</option>
                    <option value="pays-bas">Pays-Bas</option>
                    <option value="pologne">Pologne</option>
                    <option value="portugal">Portugal</option>
                    <option value="republique-tcheque">République tchèque</option>
                    <option value="roumanie">Roumanie</option>
                    <option value="slovaquie">Slovaquie</option>
                    <option value="slovenie">Slovénie</option>
                    <option value="suede">Suède</option>
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

    <script>
        // Activer la sélection multiple des options dans les listes déroulantes
        const selectElements = document.querySelectorAll('select[multiple]');
        selectElements.forEach(select => {
            let isMouseDown = false;

            select.addEventListener('mousedown', function(event) {
                isMouseDown = true;
                event.preventDefault();

                const option = event.target;
                option.selected = !option.selected;
                updateSelectedOptions(select);
            });

            select.addEventListener('mouseup', function() {
                isMouseDown = false;
            });

            select.addEventListener('mousemove', function(event) {
                if (isMouseDown) {
                    const option = event.target;
                    option.selected = !option.selected;
                    updateSelectedOptions(select);
                }
            });
        });

        function updateSelectedOptions(select) {
            const selectedOptions = Array.from(select.options)
                .filter(option => option.selected)
                .map(option => option.value);

            select.value = selectedOptions;
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
