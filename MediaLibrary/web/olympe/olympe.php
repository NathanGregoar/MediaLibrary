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
        <input type="number" id="budget_min" name="budget_min" min="1" required>
        <label for="budget_max">Budget max :</label>
        <input type="number" id="budget_max" name="budget_max" min="1" required>
        <br>
        <label>Disponibilité :</label>
        <input type="text" id="dispo_date" name="dispo_date" class="flatpickr" required>
        <label>Pas de disponibilité :</label>
        <input type="text" id="not_dispo_date" name="not_dispo_date" class="flatpickr" required>
        <br>
        <label for="pays_pref">Pays préférés :</label>
        <select id="pays_pref" name="pays_pref[]" multiple size="5">
            <!-- Ajouter ici les pays européens -->
        </select>
        <div>Classement pays préférés :</div>
        <input type="number" name="classement_pref[]" min="1" max="5">
        <br>
        <label for="pays_non_pref">Pays non préférés :</label>
        <select id="pays_non_pref" name="pays_non_pref[]" multiple size="3">
            <!-- Ajouter ici les pays européens -->
        </select>
        <div>Top 3 pays non préférés :</div>
        <input type="text" name="top_non_pref[]">
        <br>
        <label>Transport :</label>
        <input type="checkbox" id="train" name="transport[]" value="train">
        <label for="train">Train</label>
        <input type="checkbox" id="avion" name="transport[]" value="avion">
        <label for="avion">Avion</label>
        <!-- Ajouter d'autres options de transport -->
        <br>
        <button type="submit">Enregistrer</button>
    </form>

    <!-- Inclure le script pour le calendrier -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        flatpickr(".flatpickr", {
            mode: "multiple",
            dateFormat: "Y-m-d",
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
