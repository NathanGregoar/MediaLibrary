<?php
require_once '../utils/auth.php';
require_once '../utils/config.php';

// Récupérer l'utilisateur connecté
$loggedInUser = getLoggedInUser();

// Connexion à la base de données
$connect = mysqli_connect($host, $username, $password, $dbName);
if (!$connect) {
    die('Erreur de connexion à la base de données : ' . mysqli_connect_error());
}

// Suppression d'un film
if (isset($_POST['delete'])) {
    $deleteId = $connect->real_escape_string($_POST['delete']);
    $deleteSql = "DELETE FROM films WHERE id = $deleteId AND added_by = " . $loggedInUser['id'];

    if ($connect->query($deleteSql) === TRUE) {
        $deleteAlert = '<div class="alert alert-success">Film supprimé avec succès !</div>';
    } else {
        $deleteAlert = '<div class="alert alert-error">Erreur lors de la suppression du film : ' . $connect->error . '</div>';
    }
}

// Modification d'un film
if (isset($_POST['edit'])) {
    $editId = $connect->real_escape_string($_POST['edit']);
    $editSql = "SELECT * FROM films WHERE id = $editId AND added_by = " . $loggedInUser['id'];
    $editResult = $connect->query($editSql);

    if ($editResult->num_rows > 0) {
        $editData = $editResult->fetch_assoc();
        $editFormVisible = true;
    }
}

// Mise à jour d'un film
if (isset($_POST['update'])) {
    $updateId = $connect->real_escape_string($_POST['movie_id']);
    $updateFields = ['title', 'director', 'release_year', 'external_hard_drive'];
    $updateValues = [];

    foreach ($updateFields as $field) {
        $updateValues[] = "$field = '" . $connect->real_escape_string($_POST[$field]) . "'";
    }

    $updateSql = "UPDATE films SET " . implode(', ', $updateValues) . " WHERE id = $updateId AND added_by = " . $loggedInUser['id'];

    if ($connect->query($updateSql) === TRUE) {
        $updateAlert = '<div class="alert alert-success">Film mis à jour avec succès !</div>';
    } else {
        $updateAlert = '<div class="alert alert-error">Erreur lors de la mise à jour du film : ' . $connect->error . '</div>';
    }
}

// Récupération des films correspondant à la recherche
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$searchSql = "SELECT * FROM films WHERE title LIKE '%$searchTerm%' AND added_by = " . $loggedInUser['id'] . " ORDER BY title ASC";
$searchResult = $connect->query($searchSql);
$numSearchResults = $searchResult->num_rows;

// Récupération de tous les films ajoutés par l'utilisateur connecté
$userMoviesSql = "SELECT * FROM films WHERE added_by = " . $loggedInUser['id'] . " ORDER BY title ASC";
$userMoviesResult = $connect->query($userMoviesSql);
$numUserMovies = $userMoviesResult->num_rows;

// Fermeture de la connexion à la base de données
$connect->close();
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
        <?php
        if ($searchTerm !== '') {
            echo ($numSearchResults > 0) ? '<div class="alert alert-success">Résultats de la recherche (' . $numSearchResults . ') :</div>' : '<div class="alert alert-info">Aucun résultat trouvé pour la recherche "' . $searchTerm . '"</div>';
        }

        if (isset($deleteAlert)) {
            echo $deleteAlert;
        }

        if (isset($updateAlert)) {
            echo $updateAlert;
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
                <?php
                $maxMoviesPerPage = 10; // Nombre maximum de films affichés par page
                $moviesCount = 0;

                while ($row = $searchResult->fetch_assoc()) {
                    if ($moviesCount >= $maxMoviesPerPage) {
                        echo '<a href="?search=' . urlencode($searchTerm) . '&page=2">Page suivante</a>';
                        break;
                    }

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
                    $poster = ($data['Response'] === 'True' && $data['Poster'] !== 'N/A') ? $data['Poster'] : 'https://e0.pxfuel.com/wallpapers/1021/882/desktop-wallpaper-dual-monitor-firewatch-wengerluggagesave-vertical-dual-monitor.jpg';
                ?>
                    <div class="movie-item">
                        <img src="<?php echo $poster; ?>" alt="<?php echo $title; ?>" loading="lazy">
                        <div class="movie-details">
                            <h3><?php echo $title; ?></h3>
                            <p><strong>Réalisateur :</strong> <?php echo (!empty($director) && $director != 'NULL') ? $director : '/'; ?></p>
                            <p><strong>Année de sortie :</strong> <?php echo (!empty($releaseYear) && $releaseYear != 'NULL') ? $releaseYear : '/'; ?></p>
                            <p><strong>Disque dur externe :</strong> <?php echo ($externalHardDrive != 'NULL') ? $externalHardDrive : ''; ?></p>
                            <form method="POST" style="display:inline">
                                <input type="hidden" name="delete" value="<?php echo $id; ?>">
                                <input type="submit" value="Supprimer" class="delete-btn">
                            </form>
                            <button class="edit-btn" onclick="showEditForm(<?php echo $id; ?>, '<?php echo $title; ?>', '<?php echo $director; ?>', '<?php echo $releaseYear; ?>', '<?php echo $externalHardDrive; ?>')">Modifier</button>
                        </div>
                    </div>
                <?php
                    $moviesCount++;
                }

                if ($numSearchResults > $maxMoviesPerPage) {
                    echo '<a href="?search=' . urlencode($searchTerm) . '&page=2">Page suivante</a>';
                }
                ?>
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
                $poster = ($data['Response'] === 'True' && $data['Poster'] !== 'N/A') ? $data['Poster'] : 'https://e0.pxfuel.com/wallpapers/1021/882/desktop-wallpaper-dual-monitor-firewatch-wengerluggagesave-vertical-dual-monitor.jpg';
                ?>
                <div class="movie-item">
                    <img src="<?php echo $poster; ?>" alt="<?php echo $title; ?>" loading="lazy">
                    <div class="movie-details">
                        <h3><?php echo $title; ?></h3>
                        <p><strong>Réalisateur :</strong> <?php echo (!empty($director) && $director != 'NULL') ? $director : '/'; ?></p>
                        <p><strong>Année de sortie :</strong> <?php echo (!empty($releaseYear) && $releaseYear != 'NULL') ? $releaseYear : '/'; ?></p>
                        <p><strong>Disque dur externe :</strong> <?php echo ($externalHardDrive != 'NULL') ? $externalHardDrive : ''; ?></p>
                        <form method="POST" style="display:inline">
                            <input type="hidden" name="delete" value="<?php echo $id; ?>">
                            <input type="submit" value="Supprimer" class="delete-btn">
                        </form>
                        <button class="edit-btn" onclick="showEditForm(<?php echo $id; ?>, '<?php echo $title; ?>', '<?php echo $director; ?>', '<?php echo $releaseYear; ?>', '<?php echo $externalHardDrive; ?>')">Modifier</button>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <div id="edit-form-container" style="display: none;">
        <h2>Modifier un film</h2>
        <form method="POST" action="film_search.php">
            <input type="hidden" name="movie_id" id="edit-movie-id">
            <label for="edit-movie-title">Titre :</label>
            <input type="text" name="title" id="edit-movie-title">
            <label for="edit-movie-director">Réalisateur :</label>
            <input type="text" name="director" id="edit-movie-director">
            <label for="edit-movie-release-year">Année de sortie :</label>
            <input type="text" name="release_year" id="edit-movie-release-year">
            <label for="edit-movie-external-hard-drive">Disque dur externe :</label>
            <input type="text" name="external_hard_drive" id="edit-movie-external-hard-drive">
            <input type="submit" name="update" value="Enregistrer">
        </form>
    </div>

    <script>
        function showEditForm(movieId, movieTitle, movieDirector, movieReleaseYear, movieExternalHardDrive) {
            var editFormContainer = document.getElementById('edit-form-container');
            editFormContainer.style.display = 'block';

            var movieIdInput = document.getElementById('edit-movie-id');
            movieIdInput.value = movieId;

            var movieTitleInput = document.getElementById('edit-movie-title');
            var movieDirectorInput = document.getElementById('edit-movie-director');
            var movieReleaseYearInput = document.getElementById('edit-movie-release-year');
            var movieExternalHardDriveInput = document.getElementById('edit-movie-external-hard-drive');

            // Remplir le formulaire avec les informations du film correspondant à l'ID
            movieTitleInput.value = movieTitle;
            movieDirectorInput.value = movieDirector;
            movieReleaseYearInput.value = movieReleaseYear;
            movieExternalHardDriveInput.value = movieExternalHardDrive;
        }
    </script>
</body>
</html>
