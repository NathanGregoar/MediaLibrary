<?php
require_once '../utils/auth.php';
require_once '../utils/config.php';

// Démarrage de la session
session_start();

$username = $_SESSION['username'] ?? '';
$email = $_SESSION['email'] ?? '';
$loggedInUser = getLoggedInUser();

// Vérification si l'utilisateur est autorisé à accéder à la page
$allowedRoles = ["admin", "olympe"]; // Rôles autorisés
if (!in_array($loggedInUser['role'], $allowedRoles)) {
    header("Location: ../../accueil/index.php");
    exit();
}

// Connexion à la base de données (à adapter avec vos informations d'accès)
$host = 'db';
$dbuser = 'nathan';
$dbpassword = '444719';
$dbname = 'media_library';

$connection = new mysqli($host, $dbuser, $dbpassword, $dbname);

if ($connection->connect_error) {
    die('Erreur de connexion : ' . $connection->connect_error);
}

// Vérification si l'ID de l'utilisateur existe en base de données
$check_query = "SELECT COUNT(*) FROM olympe WHERE added_by = ?";
$stmt_check = $connection->prepare($check_query);
$stmt_check->bind_param("i", $loggedInUser['id']);
$stmt_check->execute();
$stmt_check->bind_result($existingRecords);
$stmt_check->fetch();
$stmt_check->close();

// Redirection vers la page "olympestat.php" si des enregistrements existent
if ($existingRecords > 0) {
    header("Location: ../olympe/statchoixpays/stats.php");
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
    $pref_countries = isset($_POST['pref_countries_selected']) ? $_POST['pref_countries_selected'] : '';
    $non_pref_countries = isset($_POST['non_pref_countries_selected']) ? $_POST['non_pref_countries_selected'] : '';

    // Vérification si des dates identiques existent dans les champs "dispo_date" et "not_dispo_date"
    $dispo_dates_array = explode(",", $dispo_dates);
    $not_dispo_dates_array = explode(",", $not_dispo_dates);

    $commonDates = array_intersect($dispo_dates_array, $not_dispo_dates_array);

    if (!empty($commonDates)) {
        $errorMessage = "Des dates identiques ont été sélectionnées dans les calendriers dispo et indispo. Veuillez corriger votre sélection.";
    } else {
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
            $successMessage = "Enregistrement réussi !";

            // Rediriger après 3 secondes
            echo '<script>
                setTimeout(function() {
                    window.location.href = "../olympe/statchoixpays/stats.php";
                }, 3000); // 3000 millisecondes (3 secondes)
            </script>';
        } else {
            $errorMessage = "Erreur lors de l'enregistrement : " . $stmt->error;
            
            // Réafficher les données du formulaire en utilisant les valeurs précédemment soumises
            $dispo_dates = $_POST['dispo_date'];
            $not_dispo_dates = $_POST['not_dispo_date'];
            $transport = isset($_POST['transport']) ? $_POST['transport'] : array();
            $pref_countries = isset($_POST['pref_countries_selected']) ? $_POST['pref_countries_selected'] : array();
            $non_pref_countries = isset($_POST['non_pref_countries_selected']) ? $_POST['non_pref_countries_selected'] : array();
        }

        // Fermeture de la connexion
        $stmt->close();
        $connection->close();
    }
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
        <!-- <a href="../olympe/statchoixpays/stats.php">Stats</a> -->
        <a href="../ecollyday/ecollyday.php">Ecollyday</a>        
    </div>
    <h1>Bienvenue dans l'Olympe <?php echo $username;?> - Choix de la destination Summer 2024</h1>

    <div id="messageContainer">
        <?php if (!empty($successMessage)) : ?>
            <div class="alert alert-success"><?php echo $successMessage; ?></div>
        <?php endif; ?>
        <?php if (!empty($errorMessage)) : ?>
            <div class="alert alert-error"><?php echo $errorMessage; ?></div>
        <?php endif; ?>
    </div>

    <!-- Formulaire -->
    <form method="post" class="form-container">
        <div class="form-grid">
            <div class="input-group">
                <div class="flex"> 
                    <label for="budget_min">Budget min :</label>
                    <small>(Transport & Location compris)</small>
                    <input type="number" id="budget_min" name="budget_min" min="1"><br>

                    <label for="choosePrefCountriesBtn">Pays où j'ai envie de partir :</label>
                    <small>(5 Pays Max)</small>
                    <button type="button" id="choosePrefCountriesBtn">Choisir</button>
                    <input type="hidden" name="pref_countries_selected" id="pref_countries_selected"><br>
                </div>

                <label>Transport allé-retour :</label><br>
                <small>(Coché : Accepté / Pas coché : C'est mort)</small><br>
                <input type="checkbox" id="train" name="transport[]" value="train" class="transport-checkbox">
                <label for="train" class="transport-label">Train</label>
                <input type="checkbox" id="avion" name="transport[]" value="avion" class="transport-checkbox">
                <label for="avion" class="transport-label">Avion</label><br>
                <input type="checkbox" id="bus" name="transport[]" value="bus" class="transport-checkbox">
                <label for="bus" class="transport-label">Bus</label>
                <input type="checkbox" id="bateau" name="transport[]" value="bateau" class="transport-checkbox">
                <label for="bateau" class="transport-label">Bateau</label>
            </div>
            <div class="input-group flex">
                <label for="budget_max">Budget max :</label>
                <small>(Transport & Location compris)</small>
                <input type="number" id="budget_max" name="budget_max" min="1"><br>

                <label for="chooseNonPrefCountriesBtn">Pays où je ne veux pas partir :</label>
                <small>(3 Pays Max)</small>
                <button type="button" id="chooseNonPrefCountriesBtn">Choisir</button>
                <input type="hidden" name="non_pref_countries_selected" id="non_pref_countries_selected">
            </div>
            <div class="input-group">
                <label for="dispo_date">Mes disponibilités :</label>
                <input type="text" id="dispo_date" name="dispo_date" class="flatpickr inline" required>
            </div>
            <div class="input-group">
                <label for="not_dispo_date">Mes indisponibilités :</label>
                <input type="text" id="not_dispo_date" name="not_dispo_date" class="flatpickr inline" required>
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

    <div id="prefCountriesModal" class="modal modal-content">
        <div class="modal-content">
            <h2>Pays ou j'ai envie de partir</h2>
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
            <button id="closePrefCountriesModal">Fermer & sauvegarder</button>

            <div class="top-countries">
                <div class="top-countries-list">
                    <h3>Mes 5 pays sélectionnés :</h3>
                    <ol id="topPreferredCountries"></ol>
                </div>
            </div>

        </div>
    </div>

    <div id="nonPrefCountriesModal" class="modal modal-content">
        <div class="modal-content">
            <h2>Pays ou je ne veux pas partir</h2>
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
            <button id="closeNonPrefCountriesModal">Fermer & sauvegarder</button>

            <div class="top-countries">
                <div class="top-countries-list">
                    <h3>Mes 3 pays sélectionnés :</h3>
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

    <!-- Vérification du form -->
    <script>
        const form = document.querySelector('.form-container');
        const messageContainer = document.getElementById('messageContainer');

        form.addEventListener('submit', function (event) {
            const isValid = validateForm();
            
            if (!isValid) {
                event.preventDefault(); // Empêche l'envoi du formulaire s'il n'est pas valide
                messageContainer.innerHTML = '<div class="alert alert-error">Complétez le formulaire correctement !</div>';
                messageContainer.style.display = 'block';
            }
        });

        function validateForm() {
            const budgetMinInput = document.getElementById('budget_min');
            const budgetMaxInput = document.getElementById('budget_max');
            const selectedPrefCountries = document.querySelectorAll('[name="pref_countries[]"]:checked');
            const selectedNonPrefCountries = document.querySelectorAll('[name="non_pref_countries[]"]:checked');
            const selectedTransport = document.querySelectorAll('[name="transport[]"]:checked');
            const dispoDateInput = document.getElementById('dispo_date');
            const notDispoDateInput = document.getElementById('not_dispo_date');

            const isValidBudget = validateBudget(budgetMinInput.value, budgetMaxInput.value);
            const isValidPrefCountries = selectedPrefCountries.length > 0;
            const isValidNonPrefCountries = selectedNonPrefCountries.length > 0;
            const isValidTransport = selectedTransport.length > 0;
            const isValidDispoDate = dispoDateInput.value.trim() !== '';
            const isValidNotDispoDate = notDispoDateInput.value.trim() !== '';

            return isValidBudget && isValidPrefCountries && isValidNonPrefCountries && isValidTransport && isValidDispoDate && isValidNotDispoDate;
        }

        function validateBudget(budgetMin, budgetMax) {
            const parsedBudgetMin = parseFloat(budgetMin);
            const parsedBudgetMax = parseFloat(budgetMax);

            if (isNaN(parsedBudgetMin) || isNaN(parsedBudgetMax)) {
                return false;
            }

            if (parsedBudgetMax <= parsedBudgetMin) {
                const budgetMaxInput = document.getElementById('budget_max');
                const budgetMinInput = document.getElementById('budget_min');
                budgetMaxInput.classList.remove('valid');
                budgetMaxInput.classList.add('invalid');
                budgetMinInput.classList.remove('valid');
                budgetMinInput.classList.add('invalid');

                messageContainer.innerHTML = '<div class="alert alert-error">La valeur budget max doit être plus grande que budget min.</div>';
                messageContainer.style.display = 'block';
                return false;
            }

            return true;
        }
    </script>

    <script>
        const casesPaysPreferes = document.querySelectorAll('[name="pref_countries[]"]');
        const casesPaysNonPreferes = document.querySelectorAll('[name="non_pref_countries[]"]');
        const topPaysPreferes = document.getElementById('topPreferredCountries');
        const topPaysNonPreferes = document.getElementById('topNonPreferredCountries');

        const LIMITE_PAYS_PREFERES = 5;
        const LIMITE_PAYS_NON_PREFERES = 3;
        const DISABLED_TEXT_COLOR = 'red'; // Couleur du texte pour les pays désactivés

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
                if (casePays.disabled) {
                    casePays.parentNode.style.color = DISABLED_TEXT_COLOR;
                } else {
                    casePays.parentNode.style.color = ''; // Réinitialiser la couleur si activé
                }
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
        // Récupérer les éléments nécessaires pour les modales
        const choosePrefCountriesBtn = document.getElementById('choosePrefCountriesBtn');
        const prefCountriesModal = document.getElementById('prefCountriesModal');
        const closePrefCountriesModal = document.getElementById('closePrefCountriesModal');
        const chooseNonPrefCountriesBtn = document.getElementById('chooseNonPrefCountriesBtn');
        const nonPrefCountriesModal = document.getElementById('nonPrefCountriesModal');
        const closeNonPrefCountriesModal = document.getElementById('closeNonPrefCountriesModal');

        // Pays pref
        choosePrefCountriesBtn.addEventListener('click', function() {
            prefCountriesModal.style.display = 'block';
        });

        closePrefCountriesModal.addEventListener('click', function() {
            const selectedPrefCountries = [...document.querySelectorAll('[name="pref_countries[]"]:checked')].map(input => input.value);
            const prefCountriesSelectedInput = document.getElementById('pref_countries_selected');
            
            // Mettez à jour la valeur du champ de saisie avec les pays sélectionnés
            prefCountriesSelectedInput.value = selectedPrefCountries.join(', ');
            
            // Fermez la modal
            prefCountriesModal.style.display = 'none';
        });

        // Ajoutez ceci pour également mettre à jour le champ de saisie si l'utilisateur clique en dehors de la modal
        window.addEventListener('click', (event) => {
            if (event.target === prefCountriesModal) {
                const selectedPrefCountries = [...document.querySelectorAll('[name="pref_countries[]"]:checked')].map(input => input.value);
                const prefCountriesSelectedInput = document.getElementById('pref_countries_selected');
                
                // Mettez à jour la valeur du champ de saisie avec les pays sélectionnés
                prefCountriesSelectedInput.value = selectedPrefCountries.join(', ');
                
                // Fermez la modal
                prefCountriesModal.style.display = 'none';
            }
        });

        // Pays non pref
        chooseNonPrefCountriesBtn.addEventListener('click', () => {
            nonPrefCountriesModal.style.display = 'flex';
        });

        closeNonPrefCountriesModal.addEventListener('click', () => {
            const selectedNonPrefCountries = [...document.querySelectorAll('[name="non_pref_countries[]"]:checked')].map(input => input.value);
            const nonPrefCountriesSelectedInput = document.getElementById('non_pref_countries_selected');
            
            // Mettez à jour la valeur du champ de saisie avec les pays non préférés
            nonPrefCountriesSelectedInput.value = selectedNonPrefCountries.join(',');
            
            // Fermez la modal
            nonPrefCountriesModal.style.display = 'none';
        });

        // Ajoutez ceci pour également mettre à jour le champ de saisie si l'utilisateur clique en dehors de la modal
        window.addEventListener('click', (event) => {
            if (event.target === nonPrefCountriesModal) {
                const selectedNonPrefCountries = [...document.querySelectorAll('[name="non_pref_countries[]"]:checked')].map(input => input.value);
                const nonPrefCountriesSelectedInput = document.getElementById('non_pref_countries_selected');
                
                // Mettez à jour la valeur du champ de saisie avec les pays non préférés
                nonPrefCountriesSelectedInput.value = selectedNonPrefCountries.join(',');
                
                // Fermez la modal
                nonPrefCountriesModal.style.display = 'none';
            }
        });

        // Fermer la modal si l'utilisateur clique en dehors de la modal
        window.addEventListener('click', (event) => {
            if (event.target === prefCountriesModal) {
                const selectedPrefCountries = [...document.querySelectorAll('[name="pref_countries[]"]:checked')].map(input => input.value);
                document.querySelector('#prefCountries').innerHTML = selectedPrefCountries.join(', ');
                prefCountriesModal.style.display = 'none';
            } else if (event.target === nonPrefCountriesModal) {
                const selectedNonPrefCountries = [...document.querySelectorAll('[name="non_pref_countries[]"]:checked')].map(input => input.value);
                document.querySelector('#nonPrefCountries').innerHTML = selectedNonPrefCountries.join(', ');
                nonPrefCountriesModal.style.display = 'none';
            }
        });
    </script>
</body>
</html>
