<?php
require_once '../utils/auth.php';
require_once '../utils/config.php';

// Récupérer l'utilisateur connecté
$loggedInUser = getLoggedInUser();

// Définition des variables de recherche
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$searchSql = "SELECT * FROM films WHERE title LIKE '%$searchTerm%' AND added_by = " . $loggedInUser['id'];

// Connexion à la base de données
$connection = mysqli_connect($host, $username, $password, $dbName);
if (!$connection) {
    die('Erreur de connexion à la base de données : ' . mysqli_connect_error());
}

// Suppression d'un film
if (isset($_POST['delete'])) {
    $deleteId = $connection->real_escape_string($_POST['delete']);
    $deleteSql = "DELETE FROM films WHERE id = $deleteId AND added_by = " . $loggedInUser['id'];

    if ($connection->query($deleteSql) === TRUE) {
        $deleteAlert = '<div class="alert alert-success">Film supprimé avec succès !</div>';
    } else {
        $deleteAlert = '<div class="alert alert-error">Erreur lors de la suppression du film : ' . $connection->error . '</div>';
    }
}

// Mise à jour d'un film
if (isset($_POST['update'])) {
    $updateId = $connection->real_escape_string($_POST['update']);
    $title = $connection->real_escape_string($_POST['title']);
    $director = $connection->real_escape_string($_POST['director']);
    $releaseYear = $connection->real_escape_string($_POST['release_year']);
    $externalHardDrive = $connection->real_escape_string($_POST['external_hard_drive']);

    $updateSql = "UPDATE films SET title = '$title', director = '$director', release_year = '$releaseYear', external_hard_drive = '$externalHardDrive' WHERE id = $updateId AND added_by = " . $loggedInUser['id'];

    if ($connection->query($updateSql) === TRUE) {
        $updateAlert = '<div class="alert alert-success">Film mis à jour avec succès !</div>';
    } else {
        $updateAlert = '<div class="alert alert-error">Erreur lors de la mise à jour du film : ' . $connection->error . '</div>';
    }
}

// Récupération des films correspondant à la recherche
$searchResult = $connection->query($searchSql);
$numSearchResults = $searchResult->num_rows;

// Récupération de tous les films ajoutés par l'utilisateur connecté
$userMoviesSql = "SELECT * FROM films WHERE added_by = " . $loggedInUser['id'];
$userMoviesResult = $connection->query($userMoviesSql);
$numUserMovies = $userMoviesResult->num_rows;

// Fermeture de la connexion à la base de données
$connection->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Rechercher des Films</title>
    <link rel="stylesheet" type="text/css" href="film.css">
    <style>
        .update-form {
            display: none;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="../accueil/index.php">Accueil</a>
        <a href="./film.php">Ajouter un Film</a>
        <a href="./film_search.php">Consulter les Films</a>
    </div>

    <h1>Rechercher des Films</h1>

    <form action="" method="GET">
        <input type="text" name="search" placeholder="Rechercher un film..." value="<?php echo $searchTerm; ?>">
        <button type="submit">Rechercher</button>
    </form>

    <?php echo $deleteAlert; ?>
    <?php echo $updateAlert; ?>

    <h2><?php echo $numSearchResults; ?> résultats pour "<?php echo $searchTerm; ?>"</h2>

    <table>
        <tr>
            <th>Titre</th>
            <th>Réalisateur</th>
            <th>Année de sortie</th>
            <th>Disque dur externe</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $searchResult->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['title']; ?></td>
                <td><?php echo $row['director']; ?></td>
                <td><?php echo $row['release_year']; ?></td>
                <td><?php echo $row['external_hard_drive']; ?></td>
                <td>
                    <form class="update-form" action="" method="POST">
                        <input type="hidden" name="update" value="<?php echo $row['id']; ?>">
                        <input type="text" name="title" placeholder="Titre" value="<?php echo $row['title']; ?>">
                        <input type="text" name="director" placeholder="Réalisateur" value="<?php echo $row['director']; ?>">
                        <input type="text" name="release_year" placeholder="Année de sortie" value="<?php echo $row['release_year']; ?>">
                        <input type="text" name="external_hard_drive" placeholder="Disque dur externe" value="<?php echo $row['external_hard_drive']; ?>">
                        <button type="submit">Mettre à jour</button>
                    </form>
                    <form action="" method="POST">
                        <input type="hidden" name="delete" value="<?php echo $row['id']; ?>">
                        <button type="submit">Supprimer</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
    </table>

    <h2><?php echo $numUserMovies; ?> films ajoutés par vous</h2>

    <table>
        <tr>
            <th>Titre</th>
            <th>Réalisateur</th>
            <th>Année de sortie</th>
            <th>Disque dur externe</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $userMoviesResult->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['title']; ?></td>
                <td><?php echo $row['director']; ?></td>
                <td><?php echo $row['release_year']; ?></td>
                <td><?php echo $row['external_hard_drive']; ?></td>
                <td>
                    <form class="update-form" action="" method="POST">
                        <input type="hidden" name="update" value="<?php echo $row['id']; ?>">
                        <input type="text" name="title" placeholder="Titre" value="<?php echo $row['title']; ?>">
                        <input type="text" name="director" placeholder="Réalisateur" value="<?php echo $row['director']; ?>">
                        <input type="text" name="release_year" placeholder="Année de sortie" value="<?php echo $row['release_year']; ?>">
                        <input type="text" name="external_hard_drive" placeholder="Disque dur externe" value="<?php echo $row['external_hard_drive']; ?>">
                        <button type="submit">Mettre à jour</button>
                    </form>
                    <form action="" method="POST">
                        <input type="hidden" name="delete" value="<?php echo $row['id']; ?>">
                        <button type="submit">Supprimer</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
    </table>

</body>
</html>
