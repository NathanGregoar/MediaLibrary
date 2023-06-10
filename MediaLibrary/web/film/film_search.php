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
        echo '<div class="alert alert-success">Film supprimé avec succès !</div>';
    } else {
        echo '<div class="alert alert-error">Erreur lors de la suppression du film : ' . $connection->error . '</div>';
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
</head>
<body>
    <div class="navbar">
        <a href="../accueil/index.php">Accueil</a>
        <a href="./film.php">Ajouter un Film</a>
        <a href="./film_search.php">Consulter les Films</a>
    </div>

    <h1>Rechercher des Films</h1>

    <div class="container">
        <div class="search-bar">
            <form method="GET">
                <label for="search">Rechercher un film</label>
                <input type="text" id="search" name="search" placeholder="Rechercher un film">
                <input type="submit" value="Rechercher">
            </form>
        </div>

        <?php if ($searchTerm !== '') : ?>
            <h2>Résultats de la recherche (<?php echo $numSearchResults; ?>) :</h2>
            <div class="movies-list">
                <?php while ($row = $searchResult->fetch_assoc()) : ?>
                    <div class="movie-item">
                        <?php
                        $id = $row['id'];
                        $title = $row['title'];
                        $director = $row['director'];
                        $releaseYear = $row['release_year'];
                        $externalHardDrive = $row['external_hard_drive'];

                        // Appel à l'API OMDB pour récupérer les informations du film
                        $apiUrl = "http://www.omdbapi.com/?apikey=f1e681ff&t=" . urlencode($title) . "&y=" . $releaseYear;
                        $apiResponse = file_get_contents($apiUrl);
                        $apiData = json_decode($apiResponse, true);

                        $posterUrl = $apiData['Poster'];
                        ?>
                        <img src="<?php echo $posterUrl; ?>" alt="<?php echo $title; ?>">
                        <h3><?php echo $title; ?></h3>
                        <p>Réalisateur : <?php echo $director; ?></p>
                        <p>Année de sortie : <?php echo $releaseYear; ?></p>
                        <p>Disque dur externe : <?php echo $externalHardDrive; ?></p>
                        <form method="POST" onsubmit="return confirm('Voulez-vous vraiment supprimer ce film ?');">
                            <input type="hidden" name="delete" value="<?php echo $id; ?>">
                            <button type="submit">Supprimer</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>

        <h2>Tous vos films (<?php echo $numUserMovies; ?>) :</h2>
        <div class="movies-list">
            <?php while ($row = $userMoviesResult->fetch_assoc()) : ?>
                <div class="movie-item">
                    <?php
                    $id = $row['id'];
                    $title = $row['title'];
                    $director = $row['director'];
                    $releaseYear = $row['release_year'];
                    $externalHardDrive = $row['external_hard_drive'];

                    // Appel à l'API OMDB pour récupérer les informations du film
                    $apiUrl = "http://www.omdbapi.com/?apikey=f1e681ff&t=" . urlencode($title) . "&y=" . $releaseYear;
                    $apiResponse = file_get_contents($apiUrl);
                    $apiData = json_decode($apiResponse, true);

                    $posterUrl = $apiData['Poster'];
                    ?>
                    <img src="<?php echo $posterUrl; ?>" alt="<?php echo $title; ?>">
                    <h3><?php echo $title; ?></h3>
                    <p>Réalisateur : <?php echo $director; ?></p>
                    <p>Année de sortie : <?php echo $releaseYear; ?></p>
                    <p>Disque dur externe : <?php echo $externalHardDrive; ?></p>
                    <form method="POST" onsubmit="return confirm('Voulez-vous vraiment supprimer ce film ?');">
                        <input type="hidden" name="delete" value="<?php echo $id; ?>">
                        <button type="submit">Supprimer</button>
                    </form>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
