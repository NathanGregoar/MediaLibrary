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

// Modification d'un film
if (isset($_POST['edit'])) {
    $editId = $connection->real_escape_string($_POST['edit']);
    $editSql = "SELECT * FROM films WHERE id = $editId AND added_by = " . $loggedInUser['id'];
    $editResult = $connection->query($editSql);

    if ($editResult->num_rows === 1) {
        $editRow = $editResult->fetch_assoc();
        $editTitle = $editRow['title'];
        $editDirector = $editRow['director'];
        $editReleaseYear = $editRow['release_year'];
        $editExternalHardDrive = $editRow['external_hard_drive'];

        // Display the edit form at the bottom of the page
        echo '<div class="edit-form">
                <h2>Modifier le film :</h2>
                <form method="POST" action="update_movie.php">
                    <input type="hidden" name="edit_id" value="' . $editId . '">
                    <label for="edit_title">Titre :</label>
                    <input type="text" name="edit_title" id="edit_title" value="' . $editTitle . '">

                    <label for="edit_director">Réalisateur :</label>
                    <input type="text" name="edit_director" id="edit_director" value="' . $editDirector . '">

                    <label for="edit_release_year">Année de sortie :</label>
                    <input type="text" name="edit_release_year" id="edit_release_year" value="' . $editReleaseYear . '">

                    <label for="edit_external_hard_drive">Disque dur externe :</label>
                    <input type="text" name="edit_external_hard_drive" id="edit_external_hard_drive" value="' . $editExternalHardDrive . '">

                    <input type="submit" value="Modifier le film" class="edit-btn">
                </form>
            </div>';
    } else {
        $editAlert = '<div class="alert alert-error">Erreur lors de la récupération des informations du film à modifier.</div>';
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

    <div class="alert-container">
        <?php if ($searchTerm !== '') : ?>
            <?php if ($numSearchResults > 0) : ?>
                <div class="alert alert-success">Résultats de la recherche (<?php echo $numSearchResults; ?>) :</div>
            <?php else : ?>
                <div class="alert alert-info">Aucun résultat trouvé pour la recherche "<?php echo $searchTerm; ?>"</div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (isset($deleteAlert)) {
            echo $deleteAlert;
        } ?>

        <?php if (isset($editAlert)) {
            echo $editAlert;
        } ?>
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

                            <form method="POST" style="display:inline">
                                <input type="hidden" name="edit" value="<?php echo $id; ?>">
                                <input type="submit" value="Modifier" class="edit-btn">
                            </form>
                            <input type="hidden" name="edit_id" value="<?php echo $id; ?>">
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
                    $poster = 'https://e0.pxfuel.com/wallpapers/1021/882/desktop-wallpaper-dual-monitor-firewatch-wengerluggagesave-vertical-dual-monitor.jpg'; // Affiche par défaut en cas d'erreur ou d'affiche indisponible
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

                        <form method="POST" style="display:inline">
                            <input type="hidden" name="edit" value="<?php echo $id; ?>">
                            <input type="submit" value="Modifier" class="edit-btn">
                        </form>
                        <input type="hidden" name="edit_id" value="<?php echo $id; ?>">
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <div class="movie-details-container">
        <h2>Détails du film :</h2>
        <form method="POST" action="update_movie.php" id="edit-form">
            <input type="hidden" name="edit_id" value="">
            <label for="edit_title">Titre :</label>
            <input type="text" name="edit_title" id="edit_title">

            <label for="edit_director">Réalisateur :</label>
            <input type="text" name="edit_director" id="edit_director">

            <label for="edit_release_year">Année de sortie :</label>
            <input type="text" name="edit_release_year" id="edit_release_year">

            <label for="edit_external_hard_drive">Disque dur externe :</label>
            <input type="text" name="edit_external_hard_drive" id="edit_external_hard_drive">

            <input type="submit" value="Modifier le film" class="edit-submit-btn">
        </form>
    </div>

    <script>
        // Code JavaScript pour gérer la modification des films
        var editButtons = document.getElementsByClassName('edit-btn');
        var editForm = document.getElementById('edit-form');

        for (var i = 0; i < editButtons.length; i++) {
            editButtons[i].addEventListener('click', function(event) {
                event.preventDefault();
                var movieContainer = this.parentNode.parentNode;
                var movieDetails = movieContainer.getElementsByClassName('movie-details')[0];
                var editId = movieContainer.querySelector('input[name="edit"]').value;
                var editTitle = movieDetails.querySelector('.movie-title').textContent;
                var editDirector = movieDetails.querySelector('.movie-director').textContent.replace('Réalisateur:', '').trim();
                var editReleaseYear = movieDetails.querySelector('.movie-release-year').textContent.replace('Année de sortie:', '').trim();
                var editExternalHardDrive = movieDetails.querySelector('.movie-external-hard-drive').textContent.replace('Disque dur externe:', '').trim();

                // Préremplir le formulaire de modification avec les détails du film sélectionné
                editForm.elements.edit_id.value = editId;
                editForm.elements.edit_title.value = editTitle;
                editForm.elements.edit_director.value = editDirector;
                editForm.elements.edit_release_year.value = editReleaseYear;
                editForm.elements.edit_external_hard_drive.value = editExternalHardDrive;

                // Afficher l'ID du film sous le bouton "Modifier"
                var editIdDisplay = document.createElement('p');
                editIdDisplay.textContent = 'ID du film : ' + editId;
                editIdDisplay.style.marginTop = '5px';
                this.parentNode.appendChild(editIdDisplay);

                // Faire défiler jusqu'au formulaire de modification
                editForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        }
    </script>
</body>
</html>
