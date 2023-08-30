<?php
require_once '../../utils/auth.php';
require_once '../../utils/config.php';

// Démarrage de la session
session_start();

$username = $_SESSION['username'] ?? '';
$email = $_SESSION['email'] ?? '';
$loggedInUser = getLoggedInUser();

// Vérification si l'utilisateur est autorisé à accéder à la page
if ($username !== "Nathan" || $email !== "nathan.gregoar@yahoo.fr") {
    header("Location: ../accueil/index.php");
    exit();
}

// Connexion à la base de données
$connection = new mysqli($host, $dbuser, $dbpassword, $dbname);

if ($connection->connect_error) {
    die('Erreur de connexion : ' . $connection->connect_error);
}

// Requête SQL pour compter le nombre total de dieux
$queryTotalGods = "SELECT COUNT(id) AS total FROM olympe";
$totalGodsResult = $connection->query($queryTotalGods);
$totalGods = ($totalGodsResult && $totalGodsResult->num_rows > 0) ? $totalGodsResult->fetch_assoc()['total'] : 0;

// Détermine si le texte doit être au singulier ou au pluriel
$text = ($totalGods == 1) ? "Dieu de l'Olympe a répondu" : "Dieux de l'Olympe ont répondu";

// Requête SQL pour récupérer les pays enregistrés
$queryPays = "SELECT pays_non, pays_oui FROM olympe WHERE pays_non IS NOT NULL OR pays_oui IS NOT NULL";
$resultPays = $connection->query($queryPays);

$paysData = [];
$paysNonData = [];

if ($resultPays) {
    while ($rowPays = $resultPays->fetch_assoc()) {
        $paysNonList = explode(',', $rowPays['pays_non'] ?? '');
        $paysOuiList = explode(',', $rowPays['pays_oui'] ?? '');

        foreach ($paysNonList as $paysNon) {
            $paysNon = trim($paysNon);
            if (!empty($paysNon)) {
                $paysNonData[$paysNon] = ($paysNonData[$paysNon] ?? 0) + 1;
            }
        }

        foreach ($paysOuiList as $paysOui) {
            $paysOui = trim($paysOui);
            if (!empty($paysOui) && !array_key_exists($paysOui, $paysNonData)) {
                $paysData[$paysOui] = ($paysData[$paysOui] ?? 0) + 1;
            }
        }
    }
}

// Requête SQL pour récupérer les budgets min et max
$queryBudgets = "SELECT MIN(budget_min) AS minBudget, MAX(budget_max) AS maxBudget FROM olympe";
$resultBudgets = $connection->query($queryBudgets);
$budgets = ($resultBudgets && $resultBudgets->num_rows > 0) ? $resultBudgets->fetch_assoc() : ['minBudget' => 0, 'maxBudget' => 0];

$minBudget = $budgets['minBudget'];
$maxBudget = $budgets['maxBudget'];
$averageBudget = ($minBudget + $maxBudget) / 2;

// Récupération des moyens de transport et des utilisateurs
$transportOptions = ['avion', 'train', 'bus', 'bateau'];
$queryTransportUsers = "SELECT DISTINCT added_by, transport FROM olympe";
$resultTransportUsers = $connection->query($queryTransportUsers);

$transportByUser = [];
$missingTransportByUser = [];

while ($rowTransportUser = $resultTransportUsers->fetch_assoc()) {
    $userId = $rowTransportUser['added_by'];
    $transportChoices = explode(',', $rowTransportUser['transport']);
    $transportChoices = array_map('trim', array_map('strtolower', $transportChoices));
    $transportByUser[$userId] = $transportChoices;

    $missingForUser = array_diff($transportOptions, $transportChoices);
    if (!empty($missingForUser)) {
        $missingTransportByUser[$userId] = $missingForUser;
    }
}

// Fonction pour récupérer le nom d'utilisateur à partir de l'ID
function getUserName($userId) {
    global $connection;
    $query = "SELECT username FROM users WHERE id = $userId";
    $result = $connection->query($query);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['username'];
    } else {
        return "Utilisateur inconnu";
    }
}

$connection->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>L'Olympe - Stats choix de destination</title>
    <link rel="stylesheet" type="text/css" href="./stats.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="navbar">
        <a href="../../accueil/index.php">Accueil</a>
        <a href="../../olympe/olympe.php">L'Olympe</a>
        <a href="../../olympe/statchoixpays/stats.php" style="color: #D7EBF3;">Stats</a>
        <a href="../../ecollyday/ecollyday.php">Ecollyday</a>        
    </div>
    <h1>Bienvenue dans l'Olympe <?php echo $username;?> - Stats choix de la destination Summer 2024</h1>
    <h2><?php echo $totalGods . " " . $text; ?> au formulaire !</h2>

    <div style="max-width: 20%;">
        <canvas id="barChartBudget" aria-label="Diagramme des budgets min, moyenne et max"></canvas>
    </div>

    <div style="max-width: 20%;">
        <canvas id="pieChartPaysOui" aria-label="Diagramme des pays où l'Olympe veut partir"></canvas>
    </div>

    <div style="max-width: 20%;">
        <canvas id="pieChartPaysNon" aria-label="Diagramme des pays où l'Olympe ne veut pas partir"></canvas>
    </div>

    <?php
require_once '../../utils/auth.php';
require_once '../../utils/config.php';

// Connexion à la base de données
$connection = new mysqli($host, $dbuser, $dbpassword, $dbname);

if ($connection->connect_error) {
    die('Erreur de connexion : ' . $connection->connect_error);
}

// Récupération des utilisateurs ayant des enregistrements dans la table olympe
$queryUsers = "SELECT DISTINCT added_by FROM olympe";
$resultUsers = $connection->query($queryUsers);

// Récupération des moyens de transport
$transportOptions = ['avion', 'train', 'bus', 'bateau']; // Noms en minuscule et sans espace

// Tableau pour stocker les moyens de transport manquants par utilisateur
$missingTransportByUser = [];

// Tableau pour stocker les moyens de transport par utilisateur
$transportByUser = [];

// Parcourir chaque utilisateur
while ($rowUser = $resultUsers->fetch_assoc()) {
    $userId = $rowUser['added_by'];

    $queryTransport = "SELECT transport FROM olympe WHERE added_by = $userId";
    $resultTransport = $connection->query($queryTransport);

    $transportChoices = [];

    while ($rowTransport = $resultTransport->fetch_assoc()) {
        $transportChoices = explode(',', $rowTransport['transport']); // Utilisation de la virgule comme séparateur
        $transportChoices = array_map('trim', $transportChoices); // Supprimer les espaces autour des noms
        $transportChoices = array_map('strtolower', $transportChoices); // Convertir en minuscules
    }

    // Vérifier les moyens de transport manquants pour cet utilisateur
    $missingForUser = array_diff($transportOptions, $transportChoices);

    // Stocker les moyens de transport pour cet utilisateur
    $transportByUser[$userId] = $transportChoices;

    // Stocker les moyens de transport manquants par utilisateur
    if (!empty($missingForUser)) {
        $missingTransportByUser[$userId] = $missingForUser;
    }
}

// Afficher les utilisateurs et leurs moyens de transport sélectionnés
echo '<h4>Transport souhaités :</h4>';
echo '<ul>';
foreach ($transportByUser as $userId => $transportChoices) {
    $userName = getUserName($userId); // Récupérer le nom d'utilisateur
    echo '<li>' . $userName . ': ' . implode(', ', $transportChoices) . '</li>';
}
echo '</ul>';

// Supprimer les doublons des moyens de transport manquants
$missingTransportByUser = array_unique($missingTransportByUser, SORT_REGULAR);

// Afficher les moyens de transport manquants par utilisateur 
if (!empty($missingTransportByUser)) {
    echo '<h4>Transport non-souhaités :</h4>';
    echo '<ul>';
    foreach ($missingTransportByUser as $userId => $missingForUser) {
        $userName = getUserName($userId); // Récupérer le nom d'utilisateur
        echo '<li>' . $userName . ' : ' . implode(', ', $missingForUser) . '</li>';
    }
    echo '</ul>';
} else {
    echo '<h4>Tous les utilisateurs ont sélectionné tous les moyens de transport.</h4>';
}

// Fonction pour récupérer le nom d'utilisateur à partir de l'ID
function getUserName($userId) {
    global $connection; // Assurez-vous que la connexion à la base de données est accessible ici

    $query = "SELECT username FROM users WHERE id = $userId";
    $result = $connection->query($query);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['username'];
    } else {
        return "Utilisateur inconnu";
    }
}

$connection->close();
?>




    <!-- Diagramme camembert pays -->
    <script>
    // Récupération du contexte du canvas pour le diagramme des pays oui
    var pieChartPaysOui = document.getElementById('pieChartPaysOui').getContext('2d');

    // Configuration des données pour le graphique des pays oui
    var chartDataPaysOui = {
        datasets: [{
            data: [<?php echo implode(",", array_values($paysData)); ?>],
            backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#8E44AD', '#16A085'], // Ajoutez plus de couleurs si nécessaire
        }],
        labels: <?php echo json_encode(array_keys($paysData)); ?>,
    };

    // Configuration du graphique camembert pour les pays oui
    var pieConfigPaysOui = {
        type: 'pie',
        data: chartDataPaysOui,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: "Diagramme des pays où l'Olympe veut partir",
                },
            },
            legend: {
                position: 'bottom',
            },
        },
    };

    // Création du graphique camembert pour les pays oui
    var myPieChartPaysOui = new Chart(pieChartPaysOui, pieConfigPaysOui);

    // Récupération du contexte du canvas pour le diagramme des pays non
    var pieChartPaysNon = document.getElementById('pieChartPaysNon').getContext('2d');

    // Configuration des données pour le graphique des pays non
    var chartDataPaysNon = {
        datasets: [{
            data: [<?php echo implode(",", array_values($paysNonData)); ?>],
            backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#8E44AD', '#16A085'], // Ajoutez plus de couleurs si nécessaire
        }],
        labels: <?php echo json_encode(array_keys($paysNonData)); ?>,
    };

    // Configuration du graphique camembert pour les pays non
    var pieConfigPaysNon = {
        type: 'pie',
        data: chartDataPaysNon,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: "Diagramme des pays où l'Olympe ne veut pas partir",
                },
            },
            legend: {
                position: 'bottom',
            },
        },
    };

    // Création du graphique camembert pour les pays non
    var myPieChartPaysNon = new Chart(pieChartPaysNon, pieConfigPaysNon);
    </script>

    <!-- Budget min et max -->
    <script>
    var barChartBudget = document.getElementById('barChartBudget').getContext('2d');

    var chartDataBudget = {
        labels: ['Budget Min', 'Moyenne', 'Budget Max'],
        datasets: [{
            label: 'Budget Min',
            data: [<?php echo $minBudget; ?>, 0, 0], // Notez l'utilisation de 0 pour les autres valeurs
            backgroundColor: 'rgba(255, 99, 132, 0.7)', // Couleur pour le budget min
            borderWidth: 1
        }, {
            label: 'Moyenne',
            data: [0, <?php echo $averageBudget; ?>, 0], // Notez l'utilisation de 0 pour les autres valeurs
            backgroundColor: 'rgba(54, 162, 235, 0.7)', // Couleur pour la moyenne
            borderWidth: 1
        }, {
            label: 'Budget Max',
            data: [0, 0, <?php echo $maxBudget; ?>], // Notez l'utilisation de 0 pour les autres valeurs
            backgroundColor: 'rgba(255, 206, 86, 0.7)', // Couleur pour le budget max
            borderWidth: 1
        }]
    };

    var barConfigBudget = {
        type: 'bar',
        data: chartDataBudget,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: "Diagramme des budgets", // Titre correctement configuré
                    font: {
                        size: 14 // Ajustez la taille de la police ici
                    }
                }
            },
            scales: {
                x: {
                    stacked: true // Les barres seront empilées horizontalement
                },
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    position: 'top', // Vous pouvez ajuster la position ici
                    labels: {
                        font: {
                            size: 10 // Vous pouvez ajuster la taille de la police ici
                        }
                    }
                }
            }
        }
    };

    var myBarChartBudget = new Chart(barChartBudget, barConfigBudget);
    </script>

</body>
</html>
