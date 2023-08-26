<?php
require_once '../utils/auth.php';
require_once '../utils/config.php';

// Démarrage de la session
session_start();

$username = $_SESSION['username'] ?? '';
$email = $_SESSION['email'] ?? '';
$loggedInUser = getLoggedInUser();

// Vérification si l'utilisateur est autorisé à accéder à la page
if ($username !== "Nathan" || $email !== "nathan.gregoar@yahoo.fr") {
    // Redirection vers la page d'accueil
    header("Location: ../accueil/index.php");
    exit();
}

// Variables pour afficher les messages d'état
$successMessage = '';
$errorMessage = '';

// Vérification si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $budget_min = $_POST['budget_min'];
    $budget_max = $_POST['budget_max'];
    $dispo_dates = $_POST['dispo_date'];
    $not_dispo_dates = $_POST['not_dispo_date'];
    $transport = isset($_POST['transport']) ? implode(', ', $_POST['transport']) : '';
    $pref_countries = isset($_POST['pref_countries']) ? implode(', ', $_POST['pref_countries']) : '';
    $non_pref_countries = isset($_POST['non_pref_countries']) ? implode(', ', $_POST['non_pref_countries']) : '';

    // Connexion à la base de données (à adapter avec vos informations d'accès)
    $host = 'db';
    $dbuser = 'nathan';
    $dbpassword = '444719';
    $dbname = 'media_library';

    $connection = new mysqli($host, $dbuser, $dbpassword, $dbname);

    if ($connection->connect_error) {
        die('Erreur de connexion : ' . $connection->connect_error);
    }

    // Préparation de la requête d'insertion
    $insert_query = "INSERT INTO olympe (added_by, budget_min, budget_max, dispo, indispo, transport, pays_oui, pays_non)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $connection->prepare($insert_query);
    $stmt->bind_param("iiisssss", $loggedInUser['id'], $budget_min, $budget_max, $dispo_dates, $not_dispo_dates, $transport, $pref_countries, $non_pref_countries);

    if ($stmt->execute()) {
        echo '<script>document.getElementById("successMessage").style.display = "block";</script>';
    } else {
        $errorMessage = "Erreur lors de l'enregistrement : " . $stmt->error;
        echo '<script>document.getElementById("errorMessage").innerHTML = "Erreur lors de l\'enregistrement : ' . addslashes($stmt->error) . '";</script>';
        echo '<script>document.getElementById("errorMessage").style.display = "block";</script>';
    }    

    // Fermeture de la connexion
    $stmt->close();
    $connection->close();
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
    <h1>Bienvenue dans l'Olympe <?php echo $username;?> - Choix de la destination Summer 2024</h1>

    <!-- Formulaire -->
    <form method="post" class="form-container">
        <div class="form-row">
            <div class="form-column">
                <div class="subcolumn">
                    <div class="input-group">
                        <label for="budget_min">Budget min :</label><br>
                        <small>(Transport & Location compris)</small><br>
                        <input type="number" id="budget_min" name="budget_min" min="1" required>
                    </div>
                    <div class="input-group">
                        <label for="choosePrefCountriesBtn">Pays où j'ai envie de partir :</label><br>
                        <small>(5 Pays Max)</small><br>
                        <button type="button" id="choosePrefCountriesBtn">Choisir</button>
                        <div id="prefCountries"></div>
                    </div>
                </div>
                <div class="subcolumn">
                    <div class="input-group">
                        <label for="budget_max">Budget max :</label><br>
                        <small>(Transport & Location compris)</small><br>
                        <input type="number" id="budget_max" name="budget_max" min="1" required>
                    </div>
                    <div class="input-group">
                        <label for="chooseNonPrefCountriesBtn">Pays où je ne veux pas partir :</label><br>
                        <small>(3 Pays Max)</small><br>
                        <button type="button" id="chooseNonPrefCountriesBtn">Choisir</button>
                        <div id="nonPrefCountries"></div>
                    </div>
                </div>
            </div>
            <div class="form-column">
                <div class="subcolumn">
                    <div class="input-group">
                        <label for="dispo_date">Mes disponibilités :</label><br>
                        <input type="text" id="dispo_date" name="dispo_date" class="flatpickr inline" required>
                    </div>
                </div>
                <div class="subcolumn">
                    <div class="input-group">
                        <label for="not_dispo_date">Mes non-disponibilités :</label><br>
                        <input type="text" id="not_dispo_date" name="not_dispo_date" class="flatpickr inline" required>
                    </div>
                </div>
            </div>
            <div class="form-column">
                <div class="transport-group">
                    <label>Transport allé-retour :</label><br>
                    <small>(Coché : Accepté / Pas coché : Refusé)</small><br>
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
        </div>
        <div class="centered">
            <div class="button-group">
                <button type="submit" class="submit-button">Enregistrer</button>
            </div>
        </div>
    </form>

    <div id="successMessage" class="alert alert-success" style="display: none;">
        Enregistrement réussi !
    </div>
    <div id="errorMessage" class="alert alert-error" style="display: none;">
        Erreur lors de l'enregistrement : [message d'erreur]
    </div>

    <div id="prefCountriesModal" class="modal">
        <div class="modal-content">
            <h2>Pays ou j'ai envie de partir (5 choix max)</h2>
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

            <div class="top-countries">
                <div class="top-countries-list">
                    <h3>Top 5 Preferred Countries:</h3>
                    <ol id="topPreferredCountries"></ol>
                </div>
            </div>

        </div>
    </div>

    <div id="nonPrefCountriesModal" class="modal">
        <div class="modal-content">
            <h2>Pays ou je ne veux pas partir (3 choix max)</h2>
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

            <div class="top-countries">
                <div class="top-countries-list">
                    <h3>Top 3 Non-Preferred Countries:</h3>
                    <ol id="topNonPreferredCountries"></ol>
                </div>
            </div>

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

    <script>
        const casesPaysPreferes = document.querySelectorAll('[name="pref_countries[]"]');
        const casesPaysNonPreferes = document.querySelectorAll('[name="non_pref_countries[]"]');
        const topPaysPreferes = document.getElementById('topPreferredCountries');
        const topPaysNonPreferes = document.getElementById('topNonPreferredCountries');

        const LIMITE_PAYS_PREFERES = 5;
        const LIMITE_PAYS_NON_PREFERES = 3;

        const mettreAJourTopPays = () => {
            let paysPreferesSelectionnes = [];
            let paysNonPreferesSelectionnes = [];

            casesPaysPreferes.forEach(casePays => {
                if (casePays.checked) {
                    paysPreferesSelectionnes.push(casePays.value);
                }
            });

            casesPaysNonPreferes.forEach(casePays => {
                if (casePays.checked) {
                    paysNonPreferesSelectionnes.push(casePays.value);
                }
            });

            desactiverCasesNonSelectionnees(paysPreferesSelectionnes, casesPaysPreferes, LIMITE_PAYS_PREFERES);
            desactiverCasesNonSelectionnees(paysNonPreferesSelectionnes, casesPaysNonPreferes, LIMITE_PAYS_NON_PREFERES);

            mettreAJourListesTopPays(paysPreferesSelectionnes, paysNonPreferesSelectionnes);
        };

        const desactiverCasesNonSelectionnees = (paysSelectionnes, casesPays, limite) => {
            casesPays.forEach(casePays => {
                casePays.disabled = !casePays.checked && paysSelectionnes.length >= limite;
            });
        };

        const mettreAJourListesTopPays = (paysPreferesSelectionnes, paysNonPreferesSelectionnes) => {
            topPaysPreferes.innerHTML = paysPreferesSelectionnes
                .slice(0, LIMITE_PAYS_PREFERES)
                .map(pays => `<li>${pays}</li>`)
                .join('');

            topPaysNonPreferes.innerHTML = paysNonPreferesSelectionnes
                .slice(0, LIMITE_PAYS_NON_PREFERES)
                .map(pays => `<li>${pays}</li>`)
                .join('');
        };

        casesPaysPreferes.forEach(casePays => {
            casePays.addEventListener('change', mettreAJourTopPays);
        });

        casesPaysNonPreferes.forEach(casePays => {
            casePays.addEventListener('change', mettreAJourTopPays);
        });
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
