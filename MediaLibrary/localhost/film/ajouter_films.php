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
    $realisateur = isset($_POST['director']) ? $_POST['director'] : '';
    $anneeSortie = isset($_POST['release_year']) ? intval($_POST['release_year']) : null;
    $disqueDurExterne = isset($_POST['external_hard_drive']) ? $_POST['external_hard_drive'] : '/';

    // Vérification si un film avec le même titre existe déjà dans la table films
    $query = "SELECT COUNT(*) AS total FROM films WHERE title = ?";
    $stmt = $connect->prepare($query);
    $stmt->bind_param("s", $titre);
    $stmt->execute();
    $result = $stmt->get_result();
    $existingFilmsCount = $result->fetch_assoc()['total'];

    if ($existingFilmsCount > 0) {
        $message = '<div class="alert alert-danger">Ce film existe déjà !</div>';
    } else {
        // Insertion des données dans la base de données
        $loggedInUser = getLoggedInUser();
        $insertSql = "INSERT INTO films (title, director, release_year, external_hard_drive, added_by) VALUES (?, ?, ?, ?, ?)";
        $insertStmt = $connect->prepare($insertSql);
        $insertStmt->bind_param("ssisi", $titre, $realisateur, $anneeSortie, $disqueDurExterne, $loggedInUser['id']);

        if ($insertStmt->execute()) {
            $message = '<div class="alert alert-success">Film ajouté avec succès !</div>';
        } else {
            $message = '<div class="alert alert-error">Erreur lors de l\'ajout du film : ' . $connect->error . '</div>';
        }
    }

    $connect->close();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Ajouter un film</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" type="text/css" href="../utils/global.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="./tools/auto_completion.js"></script>
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
                        <a class="nav-link text-warning" href="./ajouter_films.php">Ajouter un film</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./mes_films.php">Mes films</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container bg-dark text-light p-4 mt-4 rounded">
        <h1 class="display-5 text-center text-decoration-underline mb-4">Ajouter un film</h1>
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
                <div class="col-md-6 mb-3">
                    <div class="form-floating">
                        <input type="text" id="director" name="director" class="form-control" placeholder=" ">
                        <label for="director">Réalisateur :</label>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6 mb-3">
                    <div class="form-floating">
                        <input type="number" id="release_year" name="release_year" class="form-control" placeholder=" ">
                        <label for="release_year">Année de sortie :</label>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="form-floating">
                        <input type="text" id="external_hard_drive" name="external_hard_drive" class="form-control" placeholder=" ">
                        <label for="external_hard_drive">Disque dur externe :</label>
                    </div>
                </div>
            </div>
            <input type="submit" value="Ajouter" class="btn btn-success">
        </form>
    </div>
</body>

</html>
