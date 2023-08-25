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
    <h1>Bienvenue dans l'Olympe <?php echo $username; ?> - Choix de la destination Summer 2024</h1>

    <!-- Formulaire -->
    <form action="traitement_formulaire.php" method="post" class="form-container">
        <div class="form-column">
            <div class="input-group">
                <label for="budget_min">Budget min (Transport & Location comprise) :</label>
                <input type="number" id="budget_min" name="budget_min" min="1" required>
            </div>
            <div class="input-group">
                <label for="dispo_date">Mes disponibilitées :</label>
                <input type="text" id="dispo_date" name="dispo_date" class="flatpickr inline" required>
            </div>
            <div class="input-group">
                <button type="button" id="choosePrefCountriesBtn">Pays ou j'ai envie de partir :</button>
                <div id="prefCountries"></div>
            </div>
            <div class="transport-group">
                <label>Transport allé-retour (Cocher ceux pour lesquels vous êtes d'accord / Ne cochez pas ceux que vous ne voulez pas prendre :) :</label>
                <input type="checkbox" id="train" name="transport[]" value="train" class="transport-checkbox">
                <label for="train" class="transport-label">Train</label>
                <input type="checkbox" id="avion" name="transport[]" value="avion" class="transport-checkbox">
                <label for="avion" class="transport-label">Avion</label>
                <input type="checkbox" id="bus" name="transport[]" value="bus" class="transport-checkbox">
                <label for="bus" class="transport-label">Bus</label>
                <input type="checkbox" id="bateau" name="transport[]" value="bateau" class="transport-checkbox">
                <label for="bateau" class="transport-label">Bateau</label>
            </div>
        </div>
        <div class="form-column">
            <div class="input-group">
                <label for="budget_max">Budget max (Transport & Location comprise) :</label>
                <input type="number" id="budget_max" name="budget_max" min="1" required>
            </div>
            <div class="input-group">
                <label for="not_dispo_date">Mes non-disponibilitées :</label>
                <input type="text" id="not_dispo_date" name="not_dispo_date" class="flatpickr inline" required>
            </div>
            <div class="input-group">
                <button type="button" id="chooseNonPrefCountriesBtn">Pays ou je ne veux pas partir :</button>
                <div id="nonPrefCountries"></div>
            </div>
            <div class="button-group">
                <button type="submit" class="submit-button">Enregistrer</button>
            </div>
        </div>
    </form>

    <div id="prefCountriesModal" class="modal">
        <div class="modal-content">
            <h2>Choix du pays préféré</h2>
            <div class="country-list">
                <label><input type="checkbox" name="pref_countries[]" value="allemagne">Allemagne</label>
                <label><input type="checkbox" name="pref_countries[]" value="autriche">Autriche</label>
                <label><input type="checkbox" name="pref_countries[]" value="belgique">Belgique</label>
                <label><input type="checkbox" name="pref_countries[]" value="bulgarie">Bulgarie</label>
                <label><input type="checkbox" name="pref_countries[]" value="chypre">Chypre</label>
                <label><input type="checkbox" name="pref_countries[]" value="croatie">Croatie</label>
                <label><input type="checkbox" name="pref_countries[]" value="danemark">Danemark</label>
                <label><input type="checkbox" name="pref_countries[]" value="espagne">Espagne</label>
                <label><input type="checkbox" name="pref_countries[]" value="estonie">Estonie</label>
                <label><input type="checkbox" name="pref_countries[]" value="finlande">Finlande</label>
                <label><input type="checkbox" name="pref_countries[]" value="france">France</label>
                <label><input type="checkbox" name="pref_countries[]" value="grece">Grèce</label>
                <label><input type="checkbox" name="pref_countries[]" value="hongrie">Hongrie</label>
                <label><input type="checkbox" name="pref_countries[]" value="irlande">Irlande</label>
                <label><input type="checkbox" name="pref_countries[]" value="italie">Italie</label>
                <label><input type="checkbox" name="pref_countries[]" value="lettonie">Lettonie</label>
                <label><input type="checkbox" name="pref_countries[]" value="lituanie">Lituanie</label>
                <label><input type="checkbox" name="pref_countries[]" value="luxembourg">Luxembourg</label>
                <label><input type="checkbox" name="pref_countries[]" value="malte">Malte</label>
                <label><input type="checkbox" name="pref_countries[]" value="pays-bas">Pays-Bas</label>
                <label><input type="checkbox" name="pref_countries[]" value="pologne">Pologne</label>
                <label><input type="checkbox" name="pref_countries[]" value="portugal">Portugal</label>
                <label><input type="checkbox" name="pref_countries[]" value="republique-tcheque">République tchèque</label>
                <label><input type="checkbox" name="pref_countries[]" value="roumanie">Roumanie</label>
                <label><input type="checkbox" name="pref_countries[]" value="slovaquie">Slovaquie</label>
                <label><input type="checkbox" name="pref_countries[]" value="slovenie">Slovénie</label>
                <label><input type="checkbox" name="pref_countries[]" value="suede">Suède</label>
            </div>
            <button id="closePrefCountriesModal">Fermer</button>
        </div>
    </div>

    <div id="nonPrefCountriesModal" class="modal">
        <div class="modal-content">
            <h2>Choix du pays non préféré</h2>
            <div class="country-list">
                <label><input type="checkbox" name="non_pref_countries[]" value="allemagne">Allemagne</label>
                <label><input type="checkbox" name="non_pref_countries[]" value="autriche">Autriche</label>
                <label><input type="checkbox" name="non_pref_countries[]" value="belgique">Belgique</label>
                <label><input type="checkbox" name="non_pref_countries[]" value="bulgarie">Bulgarie</label>
                <label><input type="checkbox" name="non_pref_countries[]" value="chypre">Chypre</label>
                <label><input type="checkbox" name="non_pref_countries[]" value="croatie">Croatie</label>
                <label><input type="checkbox" name="non_pref_countries[]" value="danemark">Danemark</label>
                <label><input type="checkbox" name="non_pref_countries[]" value="espagne">Espagne</label>
                <label><input type="checkbox" name="non_pref_countries[]" value="estonie">Estonie</label>
                <label><input type="checkbox" name="non_pref_countries[]" value="finlande">Finlande</label>
                <label><input type="checkbox" name="non_pref_countries[]" value="france">France</label>
                <label><input type="checkbox" name="non_pref_countries[]" value="grece">Grèce</label>
                <label><input type="checkbox" name="non_pref_countries[]" value="hongrie">Hongrie</label>
                <label><input type="checkbox" name="non_pref_countries[]" value="irlande">Irlande</label>
                <label><input type="checkbox" name="non_pref_countries[]" value="italie">Italie</label>
                <label><input type="checkbox" name="non_pref_countries[]" value="lettonie">Lettonie</label>
                <label><input type="checkbox" name="non_pref_countries[]" value="lituanie">Lituanie</label>
                <label><input type="checkbox" name="non_pref_countries[]" value="luxembourg">Luxembourg</label>
                <label><input type="checkbox" name="non_pref_countries[]" value="malte">Malte</label>
                <label><input type="checkbox" name="non_pref_countries[]" value="pays-bas">Pays-Bas</label>
                <label><input type="checkbox" name="non_pref_countries[]" value="pologne">Pologne</label>
                <label><input type="checkbox" name="non_pref_countries[]" value="portugal">Portugal</label>
                <label><input type="checkbox" name="non_pref_countries[]" value="republique-tcheque">République tchèque</label>
                <label><input type="checkbox" name="non_pref_countries[]" value="roumanie">Roumanie</label>
                <label><input type="checkbox" name="non_pref_countries[]" value="slovaquie">Slovaquie</label>
                <label><input type="checkbox" name="non_pref_countries[]" value="slovenie">Slovénie</label>
                <label><input type="checkbox" name="non_pref_countries[]" value="suede">Suède</label>
            </div>
            <button id="closeNonPrefCountriesModal">Fermer</button>
        </div>
    </div>

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

    <script>
        // Récupérer les éléments nécessaires
        const choosePrefCountriesBtn = document.getElementById('choosePrefCountriesBtn');
        const prefCountriesModal = document.getElementById('prefCountriesModal');
        const closePrefCountriesModal = document.getElementById('closePrefCountriesModal');

        // Afficher la fenêtre modale lors du clic sur le bouton
        choosePrefCountriesBtn.addEventListener('click', function() {
            prefCountriesModal.style.display = 'block';
        });

        // Fermer la fenêtre modale lors du clic sur le bouton de fermeture
        closePrefCountriesModal.addEventListener('click', function() {
            prefCountriesModal.style.display = 'none';
        });
    </script>

    <script>
        // Modal handling for non preferred countries
        const chooseNonPrefCountriesBtn = document.getElementById('chooseNonPrefCountriesBtn');
        const nonPrefCountriesModal = document.getElementById('nonPrefCountriesModal');
        const closeNonPrefCountriesModal = document.getElementById('closeNonPrefCountriesModal');

        chooseNonPrefCountriesBtn.addEventListener('click', () => {
            nonPrefCountriesModal.style.display = 'flex';
        });

        closeNonPrefCountriesModal.addEventListener('click', () => {
            nonPrefCountriesModal.style.display = 'none';
        });

        // Close the modal if user clicks outside the modal content
        window.addEventListener('click', (event) => {
            if (event.target === nonPrefCountriesModal) {
                nonPrefCountriesModal.style.display = 'none';
            }
        });
    </script>
</body>
</html>
