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

$budgetMinDefaultValue = ''; // Valeur par défaut pour le budget min
$budgetMaxDefaultValue = ''; // Valeur par défaut pour le budget max

// Récupérer les valeurs de budget min et max depuis la base de données
if ($existingRecords > 0) {
    $get_budget_query = "SELECT budget_min, budget_max FROM olympe WHERE added_by = ?";
    $stmt_get_budget = $connection->prepare($get_budget_query);
    $stmt_get_budget->bind_param("i", $loggedInUser['id']);
    $stmt_get_budget->execute();
    $stmt_get_budget->bind_result($budgetMin, $budgetMax);
    $stmt_get_budget->fetch();
    $stmt_get_budget->close();

    $budgetMinDefaultValue = $budgetMin;
    $budgetMaxDefaultValue = $budgetMax;
}

$dispoDatesDefaultValue = ''; // Valeur par défaut pour les dates de disponibilité
$notDispoDatesDefaultValue = ''; // Valeur par défaut pour les dates de non-disponibilité

// Récupérez les dates de disponibilité et de non-disponibilité depuis la base de données
if ($existingRecords > 0) {
    $get_dates_query = "SELECT dispo, indispo FROM olympe WHERE added_by = ?";
    $stmt_get_dates = $connection->prepare($get_dates_query);
    $stmt_get_dates->bind_param("i", $loggedInUser['id']);
    $stmt_get_dates->execute();
    $stmt_get_dates->bind_result($dispoDates, $notDispoDates);
    $stmt_get_dates->fetch();
    $stmt_get_dates->close();

    $dispoDatesDefaultValue = $dispoDates;
    $notDispoDatesDefaultValue = $notDispoDates;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $budget_min = $_POST['budget_min'];
    $budget_max = $_POST['budget_max'];
    $dispo_dates = $_POST['dispo_date'];
    $not_dispo_dates = $_POST['not_dispo_date'];
    $transport = isset($_POST['transport']) ? implode(', ', $_POST['transport']) : '';
    $pref_countries = $_POST['pref_countries_selected'] ?? '';
    $non_pref_countries = $_POST['non_pref_countries_selected'] ?? '';

    $dispo_dates_array = explode(",", $dispo_dates);
    $not_dispo_dates_array = explode(",", $not_dispo_dates);

    if (!empty(array_intersect($dispo_dates_array, $not_dispo_dates_array))) {
        $errorMessage = "Des dates identiques ont été sélectionnées dans les calendriers dispo et indispo. Veuillez corriger votre sélection.";
    } else {
        $insert_query = "INSERT INTO olympe (added_by, budget_min, budget_max, dispo, indispo, transport, pays_oui, pays_non)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $connection->prepare($insert_query);
        $stmt->bind_param("iiisssss", $loggedInUser['id'], $budget_min, $budget_max, $dispo_dates, $not_dispo_dates, $transport, $pref_countries, $non_pref_countries);

        if ($stmt->execute()) {
            $successMessage = "Enregistrement réussi !";
            echo '<script>
                setTimeout(function() {
                    window.location.href = "../olympe/statchoixpays/stats.php";
                }, 3000);
            </script>';
        } else {
            $errorMessage = "Erreur lors de l'enregistrement : " . $stmt->error;
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>L'Olympe - Choix de destination</title>
    <link rel="icon" type="image/png" href="https://static.vecteezy.com/system/resources/thumbnails/009/399/550/small/sun-icon-set-clipart-design-illustration-free-png.png">
    <link rel="stylesheet" type="text/css" href="./modifdispo.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>
<body>
    <div class="navbar">
        <a href="../../accueil/index.php">Accueil</a>
        <a href="../../olympe/olympe.php">L'Olympe</a>
        <a href="../../olympe/modifdispo/modifdispo.php" style="color: #D7EBF3;">Modifier mes dispo</a>  
        <a href="../../ecollyday/ecollyday.php">Ecollyday</a>        
    </div>
    <h1>Bienvenue dans l'Olympe <?= $username; ?> - Modifications des disponibilités</h1>

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
        const form = document.querySelector('.form-container');
        const messageContainer = document.getElementById('messageContainer');

        form.addEventListener('submit', function (event) {
            const isValid = validateForm();
            
            if (!isValid) {
                event.preventDefault();
                messageContainer.innerHTML = '<div class="alert alert-error">Complétez le formulaire correctement !</div>';
                messageContainer.style.display = 'block';
            }
        });

        function validateForm() {
            const budgetMinInput = document.getElementById('budget_min');
            const budgetMaxInput = document.getElementById('budget_max');
            const dispoDateInput = document.getElementById('dispo_date');
            const notDispoDateInput = document.getElementById('not_dispo_date');

            const isValidBudget = validateBudget(budgetMinInput.value, budgetMaxInput.value);
            const isValidDispoDate = dispoDateInput.value.trim() !== '';
            const isValidNotDispoDate = notDispoDateInput.value.trim() !== '';

            return isValidBudget && isValidDispoDate && isValidNotDispoDate;
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

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const dispoDateInput = document.getElementById('dispo_date');
            const nonDispoDateInput = document.getElementById('not_dispo_date');

            const dispoDatesDefaultValue = '<?= $dispoDatesDefaultValue ?>'; // Récupérez la valeur par défaut des dates de disponibilité depuis PHP
            const notDispoDatesDefaultValue = '<?= $notDispoDatesDefaultValue ?>'; // Récupérez la valeur par défaut des dates de non-disponibilité depuis PHP

            flatpickr("#dispo_date", {
                mode: "multiple",
                dateFormat: "Y-m-d",
                inline: true,
                defaultDate: dispoDatesDefaultValue.split(',').map(date => new Date(date)),
                onChange: function (selectedDates) {
                    // Mettre à jour la valeur du champ de texte avec les dates sélectionnées
                    dispoDateInput.value = selectedDates.map(date => date.toISOString().slice(0, 10)).join(', ');
                }
            });

            flatpickr("#not_dispo_date", {
                mode: "multiple",
                dateFormat: "Y-m-d",
                inline: true,
                defaultDate: notDispoDatesDefaultValue.split(',').map(date => new Date(date)),
                onChange: function (selectedDates) {
                    // Mettre à jour la valeur du champ de texte avec les dates sélectionnées
                    nonDispoDateInput.value = selectedDates.map(date => date.toISOString().slice(0, 10)).join(', ');
                }
            });
        });
    </script>
</body>
</html>
