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

// Vérification si une cellule a été sélectionnée ou dé-sélectionnée
if (isset($_POST['selected_cell'])) {
    $cell_number = $_POST['selected_cell'];
    $action = $_POST['action']; // Action = "select" si la cellule est sélectionnée, "deselect" si elle est dé-sélectionnée

    // Connexion à la base de données (à adapter avec vos informations d'accès)
    $host = 'db';
    $dbuser = 'nathan';
    $dbpassword = '444719';
    $dbname = 'media_library';

    $connection = new mysqli($host, $dbuser, $dbpassword, $dbname);

    if ($connection->connect_error) {
        die('Erreur de connexion : ' . $connection->connect_error);
    }

    if ($action === "select") {
        // Requête d'insertion de la case sélectionnée dans la table ecollyday
        $query = "INSERT INTO ecollyday (cell_number, user_id) VALUES (?, ?)";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("ii", $cell_number, $user_id);
        $stmt->execute();
        $stmt->close();
    } else if ($action === "deselect") {
        // Requête de suppression de la case dé-sélectionnée de la table ecollyday
        $query = "DELETE FROM ecollyday WHERE cell_number = ? AND user_id = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("ii", $cell_number, $user_id);
        $stmt->execute();
        $stmt->close();
    }

    // Fermer la connexion à la base de données
    $connection->close();

    // Envoi d'une réponse JSON indiquant le succès de l'opération
    header('Content-Type: application/json');
    echo json_encode(array('success' => true));
    exit();
}

// Si aucune cellule n'a été sélectionnée ou dé-sélectionnée, calculer la somme des cellules sélectionnées
// à partir de la base de données lors du chargement initial de la page
$sum = 0;
$selected_cells = array();

// Connexion à la base de données (à adapter avec vos informations d'accès)
$host = 'db';
$dbuser = 'nathan';
$dbpassword = '444719';
$dbname = 'media_library';

$connection = new mysqli($host, $dbuser, $dbpassword, $dbname);

if ($connection->connect_error) {
    die('Erreur de connexion : ' . $connection->connect_error);
}

$query = "SELECT cell_number FROM ecollyday WHERE user_id = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $sum += intval($row['cell_number']);
    $selected_cells[] = intval($row['cell_number']);
}

$stmt->close();
$connection->close();
?>
<!DOCTYPE html><head>
    <title>Ecollyday</title>
    <link rel="stylesheet" type="text/css" href="./ecollyday.css">
    <!-- Inclure l'icône de page -->
    <link rel="icon" type="image/png" href="https://static.vecteezy.com/system/resources/thumbnails/009/399/550/small/sun-icon-set-clipart-design-illustration-free-png.png">
    <!-- Inclure jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<html>
<head>
    <title>Ecollyday</title>
    <link rel="stylesheet" type="text/css" href="./ecollyday.css">
    <!-- Inclure l'icône de page -->
    <link rel="icon" type="image/png" href="https://static.vecteezy.com/system/resources/thumbnails/009/399/550/small/sun-icon-set-clipart-design-illustration-free-png.png">
    <!-- Inclure jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="navbar">
        <a href="../accueil/index.php">Accueil</a>
    </div>

    <h1><?php echo $username; ?>, tu as économisé : <?php echo $sum; ?></h1>

    <table id="table">
        <tr>
            <?php
            for ($i = 1; $i <= 100; $i++) {
                $selected = in_array($i, $selected_cells) ? 'selected' : '';
                echo "<td data-cell='$i' class='$selected hoverable'>$i</td>";
                if ($i % 10 === 0) {
                    echo "</tr><tr>";
                }
            }
            ?>
        </tr>
    </table>

    <script>
        // Lorsque le document est prêt
        $(document).ready(function() {
            // Ajouter un gestionnaire d'événement click sur les cellules
            $('#table td').on('click', function() {
                const cellNumber = $(this).data('cell');
                const isSelected = $(this).hasClass('selected');

                // Inverser l'état de sélection de la cellule
                $(this).toggleClass('selected');

                // Enregistrer les cellules sélectionnées dans le stockage local
                const selectedCells = [];
                $('.selected').each(function() {
                    selectedCells.push($(this).data('cell'));
                });

                // Calculer la somme des nombres sélectionnés
                let sum = 0;
                $('.selected').each(function() {
                    sum += parseInt($(this).text());
                });

                // Mise à jour du titre h1 avec la somme
                $('h1').text(`<?php echo $username; ?>, tu as économisé : ${sum}`);

                // Envoyer une requête AJAX pour mettre à jour la base de données
                $.ajax({
                    method: 'POST',
                    url: 'ecollyday.php',
                    data: {
                        selected_cell: cellNumber,
                        action: isSelected ? 'deselect' : 'select'
                    },
                    dataType: 'json',
                    success: function(data) {
                        console.log('Données enregistrées en DB avec succès.');
                    },
                    error: function(error) {
                        console.error('Erreur lors de l\'enregistrement des données en DB : ', error);
                    }
                });
            });
        });
    </script>
</body>
</html>
