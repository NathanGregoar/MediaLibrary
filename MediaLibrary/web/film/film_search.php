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
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Sélectionnez tous les boutons "Modifier"
            var editButtons = document.querySelectorAll(".edit-btn");

            // Parcourez chaque bouton "Modifier" et ajoutez un gestionnaire d'événement de clic
            for (var i = 0; i < editButtons.length; i++) {
                editButtons[i].addEventListener("click", function(event) {
                    event.preventDefault();

                    // Masquer tous les formulaires de modification existants
                    var updateForms = document.querySelectorAll(".update-form");
                    for (var j = 0; j < updateForms.length; j++) {
                        updateForms[j].style.display = "none";
                    }

                    // Afficher le formulaire de modification correspondant
                    var movieId = this.dataset.movieId;
                    var updateForm = document.getElementById("update-form-" + movieId);
                    updateForm.style.display = "block";
                });
            }
        });
    </script>
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

        <?php if (isset($deleteAlert)) : ?>
            <?php echo $deleteAlert; ?>
        <?php endif; ?>

        <?php if (isset($updateAlert)) : ?>
            <?php echo $updateAlert; ?>
        <?php endif; ?>
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
                        $poster = '../assets/no_poster_available.jpg';
                    }
                    ?>

                    <div class="movie-card">
                        <img src="<?php echo $poster; ?>" alt="Poster">
                        <h3><?php echo $title; ?></h3>
                        <p>Réalisateur : <?php echo $director; ?></p>
                        <p>Année de sortie : <?php echo $releaseYear; ?></p>
                        <p>Disponible sur disque dur externe : <?php echo $externalHardDrive ? 'Oui' : 'Non'; ?></p>

                        <form method="POST" style="display: inline;">
                            <button type="submit" name="delete" value="<?php echo $id; ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce film ?');">Supprimer</button>
                        </form>

                        <button class="edit-btn" data-movie-id="<?php echo $id; ?>">Modifier</button>

                        <div class="update-form" id="update-form-<?php echo $id; ?>">
                            <form method="POST">
                                <input type="hidden" name="update" value="<?php echo $id; ?>">
                                <label for="title">Titre :</label>
                                <input type="text" name="title" value="<?php echo $title; ?>"><br>
                                <label for="director">Réalisateur :</label>
                                <input type="text" name="director" value="<?php echo $director; ?>"><br>
                                <label for="release_year">Année de sortie :</label>
                                <input type="text" name="release_year" value="<?php echo $releaseYear; ?>"><br>
                                <label for="external_hard_drive">Disponible sur disque dur externe :</label>
                                <input type="checkbox" name="external_hard_drive" <?php echo $externalHardDrive ? 'checked' : ''; ?>><br>
                                <input type="submit" value="Mettre à jour">
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>

        <h2>Tous vos films (<?php echo $numUserMovies; ?>) :</h2>
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
                    $poster = '../assets/no_poster_available.jpg';
                }
                ?>

                <div class="movie-card">
                    <img src="<?php echo $poster; ?>" alt="Poster">
                    <h3><?php echo $title; ?></h3>
                    <p>Réalisateur : <?php echo $director; ?></p>
                    <p>Année de sortie : <?php echo $releaseYear; ?></p>
                    <p>Disponible sur disque dur externe : <?php echo $externalHardDrive ? 'Oui' : 'Non'; ?></p>

                    <form method="POST" style="display: inline;">
                        <button type="submit" name="delete" value="<?php echo $id; ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce film ?');">Supprimer</button>
                    </form>

                    <button class="edit-btn" data-movie-id="<?php echo $id; ?>">Modifier</button>

                    <div class="update-form" id="update-form-<?php echo $id; ?>">
                        <form method="POST">
                            <input type="hidden" name="update" value="<?php echo $id; ?>">
                            <label for="title">Titre :</label>
                            <input type="text" name="title" value="<?php echo $title; ?>"><br>
                            <label for="director">Réalisateur :</label>
                            <input type="text" name="director" value="<?php echo $director; ?>"><br>
                            <label for="release_year">Année de sortie :</label>
                            <input type="text" name="release_year" value="<?php echo $releaseYear; ?>"><br>
                            <label for="external_hard_drive">Disponible sur disque dur externe :</label>
                            <input type="checkbox" name="external_hard_drive" <?php echo $externalHardDrive ? 'checked' : ''; ?>><br>
                            <input type="submit" value="Mettre à jour">
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
