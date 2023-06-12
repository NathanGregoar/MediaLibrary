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
if (isset($_POST['update'])) {
    $updateId = $connection->real_escape_string($_POST['update']);
    $newTitle = $connection->real_escape_string($_POST['edit-title']);
    $newDirector = $connection->real_escape_string($_POST['edit-director']);
    $newReleaseYear = $connection->real_escape_string($_POST['edit-release-year']);
    $newExternalHardDrive = $connection->real_escape_string($_POST['edit-external-hard-drive']);

    $updateSql = "UPDATE films SET title = '$newTitle', director = '$newDirector', release_year = '$newReleaseYear', external_hard_drive = '$newExternalHardDrive' WHERE id = $updateId AND added_by = " . $loggedInUser['id'];

    if ($connection->query($updateSql) === TRUE) {
        $updateAlert = '<div class="alert alert-success">Film modifié avec succès !</div>';
    } else {
        $updateAlert = '<div class="alert alert-error">Erreur lors de la modification du film : ' . $connection->error . '</div>';
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

        <?php if (isset($updateAlert)) {
            echo $updateAlert;
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

                            <button class="edit-btn" onclick="openEditForm(<?php echo $id; ?>)">Modifier</button>
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
                    <h4>ID: <?php echo $id; ?></h4>
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

                        <button class="edit-btn" onclick="openEditForm(<?php echo $id; ?>)">Modifier</button>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>

<script>
    function openEditForm(id) {
    // Créer une requête AJAX pour récupérer les informations du film à modifier
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            // Récupérer les données du film
            var movieData = JSON.parse(xhr.responseText);

            // Vérifier si les données ont été récupérées avec succès
            if (movieData !== null) {
                // Créer un formulaire de modification
                var form = document.createElement('form');

                // Ajouter un champ caché pour l'ID du film
                var idInput = document.createElement('input');
                idInput.setAttribute('type', 'hidden');
                idInput.setAttribute('name', 'id');
                idInput.setAttribute('value', movieData.id);
                form.appendChild(idInput);

                // Ajouter les champs de modification pour le titre, le réalisateur, l'année de sortie, et le disque dur externe
                var titleLabel = document.createElement('label');
                titleLabel.textContent = 'Titre:';
                var titleInput = document.createElement('input');
                titleInput.setAttribute('type', 'text');
                titleInput.setAttribute('name', 'title');
                titleInput.setAttribute('value', movieData.title);
                form.appendChild(titleLabel);
                form.appendChild(titleInput);

                var directorLabel = document.createElement('label');
                directorLabel.textContent = 'Réalisateur:';
                var directorInput = document.createElement('input');
                directorInput.setAttribute('type', 'text');
                directorInput.setAttribute('name', 'director');
                directorInput.setAttribute('value', movieData.director);
                form.appendChild(directorLabel);
                form.appendChild(directorInput);

                var releaseYearLabel = document.createElement('label');
                releaseYearLabel.textContent = 'Année de sortie:';
                var releaseYearInput = document.createElement('input');
                releaseYearInput.setAttribute('type', 'text');
                releaseYearInput.setAttribute('name', 'release_year');
                releaseYearInput.setAttribute('value', movieData.release_year);
                form.appendChild(releaseYearLabel);
                form.appendChild(releaseYearInput);

                var externalHardDriveLabel = document.createElement('label');
                externalHardDriveLabel.textContent = 'Disque dur externe:';
                var externalHardDriveInput = document.createElement('input');
                externalHardDriveInput.setAttribute('type', 'text');
                externalHardDriveInput.setAttribute('name', 'external_hard_drive');
                externalHardDriveInput.setAttribute('value', movieData.external_hard_drive);
                form.appendChild(externalHardDriveLabel);
                form.appendChild(externalHardDriveInput);

                // Ajouter un bouton de soumission pour le formulaire de modification
                var submitButton = document.createElement('input');
                submitButton.setAttribute('type', 'submit');
                submitButton.setAttribute('value', 'Enregistrer');
                form.appendChild(submitButton);

                // Ajouter l'événement de soumission du formulaire
                form.addEventListener('submit', function(event) {
                    event.preventDefault();

                    // Récupérer les valeurs modifiées du formulaire
                    var updatedTitle = titleInput.value;
                    var updatedDirector = directorInput.value;
                    var updatedReleaseYear = releaseYearInput.value;
                    var updatedExternalHardDrive = externalHardDriveInput.value;

                    // Créer une requête AJAX pour mettre à jour les informations du film
                    var updateXhr = new XMLHttpRequest();
                    updateXhr.onreadystatechange = function() {
                        if (updateXhr.readyState === 4 && updateXhr.status === 200) {
                            // Afficher un message de succès ou de traitement réussi
                            console.log('Les informations du film ont été mises à jour avec succès.');
                        }
                    };

                    // Envoyer la requête AJAX pour mettre à jour les informations du film
                    updateXhr.open('POST', 'update_movie.php');
                    updateXhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    updateXhr.send('id=' + id + '&title=' + encodeURIComponent(updatedTitle) +
                        '&director=' + encodeURIComponent(updatedDirector) +
                        '&release_year=' + encodeURIComponent(updatedReleaseYear) +
                        '&external_hard_drive=' + encodeURIComponent(updatedExternalHardDrive));
                });

                // Afficher le formulaire de modification dans une nouvelle fenêtre ou un dialogue modal
                // en remplaçant cette ligne par celle appropriée pour votre cas d'utilisation
                document.body.appendChild(form);
            }
        }
    };

    // Envoyer la requête AJAX pour récupérer les informations du film à modifier
    xhr.open('GET', 'get_movie.php?id=' + id);
    xhr.send();
}
</script>
