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

    <div class="alert-container">
        <?php if ($searchTerm !== '') : ?>
            <?php if ($numSearchResults > 0) : ?>
                <div class="alert alert-success">Résultats de la recherche (<?php echo $numSearchResults; ?>) :</div>
            <?php else : ?>
                <div class="alert alert-info">Aucun résultat trouvé pour la recherche "<?php echo $searchTerm; ?>"</div>
            <?php endif; ?>
        <?php endif; ?>
        <?php
        if ($connection->query($deleteSql) === TRUE) {
            echo '<div class="alert alert-success">Film supprimé avec succès !</div>';
        } else {
            echo '<div class="alert alert-error">Erreur lors de la suppression du film : ' . $connection->error . '</div>';
        }
        ?>
    </div>

    <div class="container_search">
        <div class="search-bar">
            <form method="GET">
                <input type="text" name="search" placeholder="Rechercher un film" style="width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                <input type="submit" value="Rechercher" style="padding: 10px 20px; background-color: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer;">
            </form>
        </div>

        <?php if ($searchTerm !== '') : ?>
            <h2>Résultats de la recherche (<?php echo $numSearchResults; ?>) :</h2>
            <div class="movies-list">
                <?php while ($row = $searchResult->fetch_assoc()) : ?>
                    <?php
                    $id = $row['id'];
                    $title = $row['title'];
                    $director = $row['director'];
                    $releaseYear = $row['release_year'];
                    $externalHardDrive = $row['external_hard_drive'];

                    // Appel à l'API OMDB pour récupérer les informations du film
                    $apiUrl = "http://www.omdbapi.com/?apikey=f1e681ff&t=" . urlencode($title);
                    $response = file_get_contents($apiUrl);
                    $data = json_decode($response, true);

                    // Vérifier si la requête a réussi et si l'affiche est disponible
                    if ($data['Response'] === 'True' && $data['Poster'] !== 'N/A') {
                        $poster = $data['Poster'];
                    } else {
                        $poster = 'placeholder.png'; // Affiche par défaut en cas d'erreur ou d'affiche indisponible
                    }
                    ?>
                    <div class="movie-item">
                        <img src="<?php echo $poster; ?>" alt="<?php echo $title; ?>">
                        <div class="movie-details">
                            <h3><?php echo $title; ?></h3>
                            <p><strong>Réalisateur :</strong> <?php echo ($director != 'NULL' ? $director : ''); ?></p>
                            <p><strong>Année de sortie :</strong> <?php echo ($releaseYear != 'NULL' ? $releaseYear : ''); ?></p>
                            <p><strong>Disque dur externe :</strong> <?php echo ($externalHardDrive != 'NULL' ? $externalHardDrive : ''); ?></p>

                            <form method="POST" style="display:inline">
                                <input type="hidden" name="delete" value="<?php echo $id; ?>">
                                <input type="submit" value="Supprimer" class="delete-btn">
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>

        <h2>Vos films (<?php echo $numUserMovies; ?>) :</h2>
        <div class="movies-list">
            <?php while ($row = $userMoviesResult->fetch_assoc()) : ?>
                <?php
                $id = $row['id'];
                $title = $row['title'];
                $director = $row['director'];
                $releaseYear = $row['release_year'];
                $externalHardDrive = $row['external_hard_drive'];

                // Appel à l'API OMDB pour récupérer les informations du film
                $apiUrl = "http://www.omdbapi.com/?apikey=f1e681ff&t=" . urlencode($title);
                $response = file_get_contents($apiUrl);
                $data = json_decode($response, true);

                // Vérifier si la requête a réussi et si l'affiche est disponible
                if ($data['Response'] === 'True' && $data['Poster'] !== 'N/A') {
                    $poster = $data['Poster'];
                } else {
                    $poster = 'placeholder.png'; // Affiche par défaut en cas d'erreur ou d'affiche indisponible
                }
                ?>
                <div class="movie-item">
                    <img src="<?php echo $poster; ?>" alt="<?php echo $title; ?>">
                    <div class="movie-details">
                        <h3><?php echo $title; ?></h3>
                        <p><strong>Réalisateur :</strong> <?php echo ($director != 'NULL' ? $director : ''); ?></p>
                        <p><strong>Année de sortie :</strong> <?php echo ($releaseYear != 'NULL' ? $releaseYear : ''); ?></p>
                        <p><strong>Disque dur externe :</strong> <?php echo ($externalHardDrive != 'NULL' ? $externalHardDrive : ''); ?></p>

                        <form method="POST" style="display:inline">
                            <input type="hidden" name="delete" value="<?php echo $id; ?>">
                            <input type="submit" value="Supprimer" class="delete-btn">
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
