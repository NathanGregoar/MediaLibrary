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

// Vérification si une cellule a été sélectionnée
if (isset($_POST['selected_cell'])) {
    $cell_number = $_POST['selected_cell'];

    // Récupération de l'ID de l'utilisateur (à adapter selon comment vous stockez l'ID de l'utilisateur dans la session)
    $user_id = $_SESSION['user_id'];

    // Requête d'insertion de la case sélectionnée dans la table ecollyday
    $stmt = $pdo->prepare("INSERT INTO ecollyday (cell_number, user_id) VALUES (?, ?)");
    $stmt->execute([$cell_number, $user_id]);
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
                echo "<td onclick='toggleSelection($i)'>$i</td>";
                if ($i % 10 === 0) {
                    echo "</tr><tr>";
                }
            }
            ?>
        </tr>
    </table>

    <script>
        function toggleSelection(cellNumber) {
            const cell = document.querySelector(`td:nth-child(${cellNumber})`);
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

            // Envoi de la case sélectionnée en tant que formulaire pour enregistrement en base de données
            const form = document.createElement("form");
            form.method = "post";
            form.action = "ecollyday.php";
            const hiddenInput = document.createElement("input");
            hiddenInput.type = "hidden";
            hiddenInput.name = "selected_cell";
            hiddenInput.value = cellNumber;
            form.appendChild(hiddenInput);
            document.body.appendChild(form);
            form.submit();
        }
    </script>
</body>
</html>
