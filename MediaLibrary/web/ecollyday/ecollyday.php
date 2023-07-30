<?php
require_once '../utils/auth.php';

$username = $_SESSION['username'];
$email = $_SESSION['email'];

// Vérification si l'utilisateur est autorisé à accéder à la page Ecollyday
if ($username !== "Nathan" && $email !== "nathan.gregoar@yahoo.fr") {
    // Redirection vers la page d'accueil
    header("Location: ../accueil/index.php");
    exit();
}

// Récupération de l'utilisateur connecté
$loggedInUser = getLoggedInUser();
$user_id = $loggedInUser['id'];

// Fonction pour insérer une cellule sélectionnée en DB
function insertSelectedCell($cell_number, $user_id) {
    // Connexion à la base de données (à adapter avec vos informations d'accès)
    $host = 'db';
    $dbuser = 'nathan';
    $dbpassword = '444719';
    $dbname = 'media_library';

    $connection = new mysqli($host, $dbuser, $dbpassword, $dbname);

    if ($connection->connect_error) {
        die('Erreur de connexion : ' . $connection->connect_error);
    }

    // Requête d'insertion de la case sélectionnée dans la table ecollyday
    $query = "INSERT INTO ecollyday (cell_number, user_id) VALUES (?, ?)";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("ii", $cell_number, $user_id);
    $stmt->execute();
    $stmt->close();

    // Fermer la connexion à la base de données
    $connection->close();
}

// Fonction pour supprimer une cellule dé-sélectionnée de la DB
function deleteSelectedCell($cell_number, $user_id) {
    // Connexion à la base de données (à adapter avec vos informations d'accès)
    $host = 'db';
    $dbuser = 'nathan';
    $dbpassword = '444719';
    $dbname = 'media_library';

    $connection = new mysqli($host, $dbuser, $dbpassword, $dbname);

    if ($connection->connect_error) {
        die('Erreur de connexion : ' . $connection->connect_error);
    }

    // Requête de suppression de la case dé-sélectionnée de la table ecollyday
    $query = "DELETE FROM ecollyday WHERE cell_number = ? AND user_id = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("ii", $cell_number, $user_id);
    $stmt->execute();
    $stmt->close();

    // Fermer la connexion à la base de données
    $connection->close();
}

// Vérification si une cellule a été sélectionnée ou dé-sélectionnée
if (isset($_POST['selected_cell'])) {
    $cell_number = $_POST['selected_cell'];
    $action = $_POST['action']; // Action = "select" si la cellule est sélectionnée, "deselect" si elle est dé-sélectionnée

    // Mettre à jour l'état de la cellule en base de données en fonction de l'action
    if ($action === "select") {
        insertSelectedCell($cell_number, $user_id);
    } else if ($action === "deselect") {
        deleteSelectedCell($cell_number, $user_id);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ecollyday</title>
    <link rel="stylesheet" type="text/css" href="./ecollyday.css">
</head>
<body>
    <div class="navbar">
        <a href="../accueil/index.php">Accueil</a>
    </div>

    <h1>Tableau de 100 cases numérotées de 1 à 100:</h1>
    <table id="table">
        <tr>
            <?php
            for ($i = 1; $i <= 100; $i++) {
                echo "<td onclick='toggleSelection(this, $i)'>$i</td>";
                if ($i % 10 === 0) {
                    echo "</tr><tr>";
                }
            }
            ?>
        </tr>
    </table>

    <script>
        function toggleSelection(cell, cellNumber) {
            cell.classList.toggle("selected");

            // Calcul de la somme des nombres sélectionnés
            const selectedCells = document.querySelectorAll(".selected");
            let sum = 0;
            selectedCells.forEach(selectedCell => {
                sum += parseInt(selectedCell.textContent);
            });

            // Mise à jour du titre h1 avec la somme
            const h1 = document.querySelector("h1");
            h1.textContent = `Tableau de 100 cases numérotées de 1 à 100 - Somme : ${sum}`;

            // Envoi de la cellule sélectionnée ou dé-sélectionnée en tant que formulaire pour mise à jour en base de données
            const form = document.createElement("form");
            form.method = "post";
            form.action = "ecollyday.php";
            const hiddenInputCell = document.createElement("input");
            hiddenInputCell.type = "hidden";
            hiddenInputCell.name = "selected_cell";
            hiddenInputCell.value = cellNumber;
            const hiddenInputAction = document.createElement("input");
            hiddenInputAction.type = "hidden";
            hiddenInputAction.name = "action";
            hiddenInputAction.value = cell.classList.contains("selected") ? "select" : "deselect";
            form.appendChild(hiddenInputCell);
            form.appendChild(hiddenInputAction);
            document.body.appendChild(form);
            form.submit();
        }
    </script>
</body>
</html>
