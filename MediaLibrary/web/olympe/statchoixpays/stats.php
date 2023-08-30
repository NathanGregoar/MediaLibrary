<?php
require_once '../../utils/auth.php';
require_once '../../utils/config.php';

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

// Connexion à la base de données (à adapter avec vos informations d'accès)
$host = 'db';
$dbuser = 'nathan';
$dbpassword = '444719';
$dbname = 'media_library';

$connection = new mysqli($host, $dbuser, $dbpassword, $dbname);

if ($connection->connect_error) {
    die('Erreur de connexion : ' . $connection->connect_error);
}

// Requête SQL pour compter le nombre d'enregistrements dans la table "olympe"
$query = "SELECT COUNT(id) AS total FROM olympe";
$result = $connection->query($query);

if ($result) {
    $row = $result->fetch_assoc();
    $totalGods = $row['total'];
} else {
    $totalGods = 0; // En cas d'erreur dans la requête
}

// Détermine si le texte doit être au singulier ou au pluriel
$text = ($totalGods == 1) ? "Dieu de l'Olympe a répondu" : "Dieux de l'Olympe ont répondu";

// Requête SQL pour récupérer les pays enregistrés dans le champ "pays_non" de tous les utilisateurs
$queryPaysNon = "SELECT pays_non FROM olympe WHERE pays_non IS NOT NULL";
$resultPaysNon = $connection->query($queryPaysNon);

$paysNonData = []; // Tableau pour stocker les données des pays non

if ($resultPaysNon) {
    while ($rowPaysNon = $resultPaysNon->fetch_assoc()) {
        $paysNonList = explode(',', $rowPaysNon['pays_non']); // Séparer les pays par des virgules
        foreach ($paysNonList as $paysNon) {
            $paysNon = trim($paysNon); // Supprimer les espaces autour du nom du pays
            if (!empty($paysNon)) {
                if (!isset($paysNonData[$paysNon])) {
                    $paysNonData[$paysNon] = 1;
                } else {
                    $paysNonData[$paysNon]++;
                }
            }
        }
    }
}

// Requête SQL pour récupérer les pays enregistrés dans le champ "pays_oui"
$queryPays = "SELECT pays_oui FROM olympe WHERE pays_oui IS NOT NULL";
$resultPays = $connection->query($queryPays);

$paysData = []; // Tableau pour stocker les données des pays

if ($resultPays) {
    while ($rowPays = $resultPays->fetch_assoc()) {
        $paysList = explode(',', $rowPays['pays_oui']); // Séparer les pays par des virgules
        foreach ($paysList as $pays) {
            $pays = trim($pays); // Supprimer les espaces autour du nom du pays
            if (!empty($pays) && !array_key_exists($pays, $paysNonData)) {
                if (!isset($paysData[$pays])) {
                    $paysData[$pays] = 1;
                } else {
                    $paysData[$pays]++;
                }
            }
        }
    }
}

// Récupère les budgets min et max
$queryBudgetMin = "SELECT MIN(budget_min) AS minBudget FROM olympe";
$queryBudgetMax = "SELECT MAX(budget_max) AS maxBudget FROM olympe";

$resultBudgetMin = $connection->query($queryBudgetMin);
$resultBudgetMax = $connection->query($queryBudgetMax);

$minBudget = 0;
$maxBudget = 0;

if ($resultBudgetMin && $resultBudgetMax) {
    $rowMin = $resultBudgetMin->fetch_assoc();
    $minBudget = $rowMin['minBudget'];

    $rowMax = $resultBudgetMax->fetch_assoc();
    $maxBudget = $rowMax['maxBudget'];
}

$averageBudget = ($minBudget + $maxBudget) / 2;

// Récupération des disponibilités de tous les utilisateurs
$queryAllDispos = "SELECT dispo FROM olympe WHERE dispo IS NOT NULL";
$resultAllDispos = $connection->query($queryAllDispos);

$userDispos = []; // Tableau pour stocker les disponibilités par utilisateur

if ($resultAllDispos) {
    while ($rowAllDispos = $resultAllDispos->fetch_assoc()) {
        $dispoDates = explode(',', $rowAllDispos['dispo']); // Séparer les dates par des virgules
        $userDispos[] = $dispoDates;
    }
}

// Trouver les dates communes
$commonDispos = call_user_func_array('array_intersect', $userDispos);
$commonDispos = array_unique($commonDispos); // Supprimer les doublons
sort($commonDispos); // Trier les dates par ordre croissant



$connection->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>L'Olympe - Stats choix de destination</title>
    <link rel="stylesheet" type="text/css" href="./stats.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.0/main.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.0/main.min.js"></script>

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

    <div style="max-width: 50%;">
        <h3>Calendrier des dates disponibles en commun :</h3>
        <div id="calendar"></div>
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: [
            <?php foreach ($commonDispos as $date) : ?>
            {
                title: 'Disponible',
                start: '<?php echo $date; ?>'
            },
            <?php endforeach; ?>
        ]
    });

    calendar.render();
});
</script>

</body>
</html>
