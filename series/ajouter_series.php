<?php
require_once '../utils/auth.php';
require_once '../utils/config.php';
include '../utils/bootstrap.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $connect = mysqli_connect($host, $username, $password, $dbName);
    if (!$connect) {
        die('Erreur de connexion à la base de données : ' . mysqli_connect_error());
    }

    // Nettoyage et validation des entrées
    $titre = isset($_POST['title']) ? $_POST['title'] : '';
    $langue = isset($_POST['langue_serie']) ? $_POST['langue_serie'] : '/';
    $saisonsComplete = isset($_POST['complete_season']) ? 1 : 0; // Si la checkbox est cochée, attribuer la valeur 1
    $nombreEpisodes = isset($_POST['episode_count']) ? intval($_POST['episode_count']) : 0;
    $nombreSaisons = isset($_POST['season_number']) ? intval($_POST['season_number']) : 0;
    $disqueDurExterne = isset($_POST['external_hard_drive']) ? $_POST['external_hard_drive'] : '/';

    // Vérification si une série avec le même titre existe déjà dans la table series
    $query = "SELECT COUNT(*) AS total FROM series WHERE title = ?";
    $stmt = $connect->prepare($query);
    $stmt->bind_param("s", $titre);
    $stmt->execute();
    $result = $stmt->get_result();
    $existingSeriesCount = $result->fetch_assoc()['total'];

    if ($existingSeriesCount > 0) {
        $message = '<div class="alert alert-danger">Cette série existe déjà !</div>';
    } else {
        // Insertion des données dans la base de données
        $loggedInUser = getLoggedInUser();
        $insertSql = "INSERT INTO series (title, langue_serie, complete_season, episode_count, season_number, external_hard_drive, added_by) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $insertStmt = $connect->prepare($insertSql);
        $insertStmt->bind_param("ssiissi", $titre, $langue, $saisonsComplete, $nombreEpisodes, $nombreSaisons, $disqueDurExterne, $loggedInUser['id']);

        if ($insertStmt->execute()) {
            $message = '<div class="alert alert-success">Série ajoutée avec succès !</div>';
        } else {
            $message = '<div class="alert alert-error">Erreur lors de l\'ajout de la série : ' . $connect->error . '</div>';
        }
    }

    $connect->close();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Ajouter une série</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" type="text/css" href="../utils/global.css">
    <link rel="stylesheet" type="text/css" href="series.css">
    <script src="https://cdn.jsdelivr.net/npm/quagga@0.12.1/dist/quagga.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link rel="icon" href="../images/icon.png" type="image/x-icon">
</head>

<body class="bg-secondary">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-5">
        <div class="container-fluid">
            <a class="navbar-brand" href="../accueil/index.php">Accueil</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link text-warning" href="./ajouter_series.php">Ajouter une série</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./mes_series.php">Mes séries</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container bg-dark text-light p-4 mt-4 rounded">
        <h1 class="display-5 text-center text-decoration-underline mb-4">Ajouter une série</h1>
        <div id="result" class="d-flex justify-content-center"></div>
        <?php echo $message; ?>
        <form method="POST" class="mt-4 text-secondary">
            <div class="row mb-3">
                <div class="col-md-6 mb-3">
                    <div class="form-floating">
                        <input type="text" id="title" name="title" class="form-control" required placeholder=" ">
                        <label for="title">Titre :</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch">
                                <div class="checkbox-animated">
                                    <input id="complete_season" type="checkbox" name="complete_season">
                                    <label for="complete_season">
                                        <span class="check"></span>
                                        <span class="box"></span>
                                        Saisons complètes
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="number" id="season_number" name="season_number" value="1" min="1" class="form-control" placeholder=" ">
                                <label for="season_number">Nombre de saisons :</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6 mb-3">
                    <div class="form-floating">
                        <input type="text" id="langue_serie" name="langue_serie" class="form-control" placeholder=" ">
                        <label for="langue_serie">Langue :</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="number" id="episode_count" name="episode_count" min="0" class="form-control" placeholder=" ">
                                <label for="episode_count">Nombre d'épisodes :</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" id="external_hard_drive" name="external_hard_drive" class="form-control" placeholder=" ">
                                <label for="external_hard_drive">Disque dur externe :</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <input type="submit" value="Ajouter" class="btn btn-success">
        </form>

    </div>
</body>

</html>