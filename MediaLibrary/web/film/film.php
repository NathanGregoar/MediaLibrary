<?php
require_once '../utils/auth.php';
require_once '../utils/config.php';

// Vérification si l'utilisateur est connecté
checkLoggedIn();

// Récupérer l'utilisateur connecté
$loggedInUser = getLoggedInUser();

// Vérifier si l'utilisateur a le droit d'ajouter un film
if (!$loggedInUser['can_add_film']) {
    die('Vous n\'avez pas le droit d\'ajouter un film.');
}

// Connexion à la base de données
$connection = mysqli_connect($host, $username, $password, $dbName);

if (!$connection) {
    die('Erreur de connexion à la base de données : ' . mysqli_connect_error());
}

// Ajout d'un film
if (isset($_POST['title'])) {
    $title = $connection->real_escape_string($_POST['title']);
    $director = $_POST['director'] != '' ? $connection->real_escape_string($_POST['director']) : null;
    $releaseYear = $_POST['release_year'] != '' ? $connection->real_escape_string($_POST['release_year']) : null;
    $externalHardDrive = $_POST['external_hard_drive'] != '' ? intval($_POST['external_hard_drive']) : null;

    // Vérifier les doublons
    $duplicateSql = "SELECT * FROM films WHERE title = '$title' AND director = " . ($director !== null ? "'$director'" : "NULL") . " AND release_year = " . ($releaseYear !== null ? "'$releaseYear'" : "NULL");
    $duplicateResult = $connection->query($duplicateSql);

    if ($duplicateResult->num_rows > 0) {
        echo '<div class="alert alert-error">Le film existe déjà dans la base de données.</div>';
    } else {
        $addedBy = $loggedInUser['id'];

        $insertSql = "INSERT INTO films (title, director, release_year, external_hard_drive, added_by) VALUES (?, ?, ?, ?, ?)";
        $insertStmt = $connection->prepare($insertSql);
        $insertStmt->bind_param("sssii", $title, $director, $releaseYear, $externalHardDrive, $addedBy);

        if ($insertStmt->execute()) {
            echo '<div class="alert alert-success">Film ajouté avec succès !</div>';
        } else {
            echo '<div class="alert alert-error">Erreur lors de l\'ajout du film : ' . $connection->error . '</div>';
        }
    }
}

// Fermer la connexion à la base de données
$connection->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ajouter un Film</title>
    <link rel="stylesheet" type="text/css" href="./film.css">
</head>
<body>
    <div class="container">
        <div class="navbar">
            <a href="../accueil/index.php">Accueil</a>
            <a href="./film.php">Ajouter un Film</a>
            <a href="./film_search.php">Consulter les Films</a>
        </div>

        <h1>Ajouter un Film</h1>

        <form method="POST">
            <label for="title">Titre :</label>
            <input type="text" id="title" name="title" required>

            <label for="director">Réalisateur :</label>
            <input type="text" id="director" name="director">

            <label for="release_year">Année de sortie :</label>
            <input type="text" id="release_year" name="release_year">

            <label for="external_hard_drive">Numéro du disque dur externe :</label>
            <input type="text" id="external_hard_drive" name="external_hard_drive">

            <input type="submit" value="Ajouter">
        </form>
    </div>
</body>
</html>
