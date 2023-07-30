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

// Vérification si une cellule a été sélectionnée
if (isset($_POST['selected_cell'])) {
    $cell_number = $_POST['selected_cell'];

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
                echo "<td onclick='toggleSelection(this)'>$i</td>";
                if ($i % 10 === 0) {
                    echo "</tr><tr>";
                }
            }
            ?>
        </tr>
    </table>

    <script>
        function toggleSelection(cell) {
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
        }
    </script>
</body>
</html>
