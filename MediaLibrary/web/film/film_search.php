<?php
require_once '../utils/auth.php';
require_once '../utils/config.php';

// Récupérer l'utilisateur connecté
$loggedInUser = getLoggedInUser();

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

// Modification d'un film
if (isset($_POST['edit'])) {
    $editId = $connection->real_escape_string($_POST['edit']);
    $editSql = "SELECT * FROM films WHERE id = $editId AND added_by = " . $loggedInUser['id'];
    $editResult = $connection->query($editSql);

    if ($editResult->num_rows > 0) {
        $editData = $editResult->fetch_assoc();
        $editTitle = $editData['title'];
        $editDirector = $editData['director'];
        $editReleaseYear = $editData['release_year'];
        $editExternalHardDrive = $editData['external_hard_drive'];
        $editFormVisible = true;
    }
}

// Mise à jour d'un film
if (isset($_POST['update'])) {
    $updateId = $connection->real_escape_string($_POST['movie_id']);
    $updateTitle = $connection->real_escape_string($_POST['title']);
    $updateDirector = $connection->real_escape_string($_POST['director']);
    $updateReleaseYear = $connection->real_escape_string($_POST['release_year']);
    $updateExternalHardDrive = $connection->real_escape_string($_POST['external_hard_drive']);

    $updateSql = "UPDATE films SET title = '$updateTitle', director = '$updateDirector', release_year = '$updateReleaseYear', external_hard_drive = '$updateExternalHardDrive' WHERE id = $updateId AND added_by = " . $loggedInUser['id'];

    if ($connection->query($updateSql) === TRUE) {
        $updateAlert = '<div class="alert alert-success">Film mis à jour avec succès !</div>';
    } else {
        $updateAlert = '<div class="alert alert-error">Erreur lors de la mise à jour du film : ' . $connection->error . '</div>';
    }
}

// Récupération des films correspondant à la recherche
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$searchSql = "SELECT * FROM films WHERE title LIKE '%$searchTerm%' AND added_by = " . $loggedInUser['id'] . " ORDER BY title ASC";
$searchResult = $connection->query($searchSql);
$numSearchResults = $searchResult->num_rows;

// Récupération de tous les films ajoutés par l'utilisateur connecté
$userMoviesSql = "SELECT * FROM films WHERE added_by = " . $loggedInUser['id'] . " ORDER BY title ASC";
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
        <?php
        if ($searchTerm !== '') {
            if ($numSearchResults > 0) {
                echo '<div class="alert alert-success">Résultats de la recherche (' . $numSearchResults . ') :</div>';
            } else {
                echo '<div class="alert alert-info">Aucun résultat trouvé pour la recherche "' . $searchTerm . '"</div>';
            }
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

        <?php
        if ($searchTerm !== '') {
            echo '<h2>Résultats de la recherche (' . $numSearchResults . ') :</h2>';
            echo '<div class="movies-list">';
            while ($row = $searchResult->fetch_assoc()) {
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

                echo '<div class="movie-item">';
                echo '<img src="' . $poster . '" alt="' . $title . '">';
                echo '<div class="movie-details">';
                echo '<h3>' . $title . '</h3>';

                // Affichage du réalisateur avec la gestion du cas où il n'est pas spécifié
                if (!empty($director) && $director != 'NULL') {
                    echo '<p><strong>Réalisateur :</strong> ' . $director . '</p>';
                } else {
                    echo '<p><strong>Réalisateur :</strong> /</p>';
                }

                // Affichage de la date de sortie avec la gestion du cas où elle n'est pas spécifiée
                if (!empty($releaseYear) && $releaseYear != 'NULL') {
                    echo '<p><strong>Année de sortie :</strong> ' . $releaseYear . '</p>';
                } else {
                    echo '<p><strong>Année de sortie :</strong> /</p>';
                }

                echo '<p><strong>Disque dur externe :</strong> ' . ($externalHardDrive != 'NULL' ? $externalHardDrive : '') . '</p>';
                echo '<form method="POST" style="display:inline">';
                echo '<input type="hidden" name="delete" value="' . $id . '">';
                echo '<input type="submit" value="Supprimer" class="delete-btn">';
                echo '</form>';
                echo '<button class="edit-btn" onclick="showEditForm(' . $id . ', \'' . $title . '\', \'' . $director . '\', \'' . $releaseYear . '\', \'' . $externalHardDrive . '\')">Modifier</button>';
                echo '</div>';
                echo '</div>';
            }
            echo '</div>';
        }
        ?>

        <h2>Vos films (<?php echo $numUserMovies; ?>) :</h2>
        <div class="movies-list">
            <?php
            while ($row = $userMoviesResult->fetch_assoc()) {
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

                echo '<div class="movie-item">';
                echo '<img src="' . $poster . '" alt="' . $title . '">';
                echo '<div class="movie-details">';
                echo '<h3>' . $title . '</h3>';

                // Affichage du réalisateur avec la gestion du cas où il n'est pas spécifié
                if (!empty($director) && $director != 'NULL') {
                    echo '<p><strong>Réalisateur :</strong> ' . $director . '</p>';
                } else {
                    echo '<p><strong>Réalisateur :</strong> /</p>';
                }

                // Affichage de la date de sortie avec la gestion du cas où elle n'est pas spécifiée
                if (!empty($releaseYear) && $releaseYear != 'NULL') {
                    echo '<p><strong>Année de sortie :</strong> ' . $releaseYear . '</p>';
                } else {
                    echo '<p><strong>Année de sortie :</strong> /</p>';
                }

                echo '<p><strong>Disque dur externe :</strong> ' . ($externalHardDrive != 'NULL' ? $externalHardDrive : '') . '</p>';
                echo '<form method="POST" style="display:inline">';
                echo '<input type="hidden" name="delete" value="' . $id . '">';
                echo '<input type="submit" value="Supprimer" class="delete-btn">';
                echo '</form>';
                echo '<button class="edit-btn" onclick="showEditForm(' . $id . ', \'' . $title . '\', \'' . $director . '\', \'' . $releaseYear . '\', \'' . $externalHardDrive . '\')">Modifier</button>';
                echo '</div>';
                echo '</div>';
            }
            ?>
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

            var movieIdDisplay = document.getElementById('edit-movie-id-display');
            movieIdDisplay.textContent = movieId;
        }
    </script>
</body>
</html>
