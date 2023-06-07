<?php
require_once '../utils/auth.php';
require_once '../utils/config.php';
require_once '../utils/film_functions.php';

// Récupérer l'identifiant de l'utilisateur connecté
$userId = getLoggedInUserId();

// Vérifier si l'identifiant de l'utilisateur est valide
if ($userId === null) {
    // Gérer le cas où l'identifiant de l'utilisateur n'est pas disponible
    echo 'Erreur : Impossible de récupérer l\'identifiant de l\'utilisateur connecté.';
    exit;
}

// Vérifier si une recherche a été effectuée
if (isset($_GET['search'])) {
    $searchTerm = $_GET['search'];
    $films = searchFilms($conn, $searchTerm, $userId);
} else {
    // Récupérer tous les films de l'utilisateur connecté
    $films = getAllFilms($conn, $userId);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Rechercher des Films</title>
    <link rel="stylesheet" type="text/css" href="./film.css">
</head>
<body>
    <div class="container">
        <div class="navbar">
            <a href="../accueil/index.php">Accueil</a>
            <a href="./film.php">Ajouter un Film</a>
            <a href="./film_search.php">Consulter les Films</a>
        </div>

        <h1>Rechercher des Films</h1>

        <div class="search-bar">
            <form method="GET">
                <input type="text" name="search" placeholder="Rechercher un film" style="width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                <input type="submit" value="Rechercher" style="padding: 10px 20px; background-color: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer;">
            </form>
        </div>

        <?php
        if (count($films) > 0) {
            echo '<h2>Liste complète des films :</h2>';
            echo '<div class="movies-list">';
            foreach ($films as $film) {
                $id = $film['id'];
                $title = $film['title'];
                $director = $film['director'];
                $releaseYear = $film['release_year'];
                $externalHardDrive = $film['external_hard_drive'];

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

                echo '<div class="movie-item">';
                echo '<img src="' . $poster . '" alt="' . $title . '">';
                echo '<div class="movie-details">';
                echo '<h3>' . $title . '</h3>';
                echo '<p><strong>Réalisateur :</strong> ' . $director . '</p>';
                echo '<p><strong>Année de sortie :</strong> ' . $releaseYear . '</p>';
                echo '<p><strong>Disque dur externe :</strong> ' . $externalHardDrive . '</p>';
                echo '</div>';
                echo '</div>';
            }
            echo '</div>';
        } else {
            echo '<p>Aucun film trouvé.</p>';
        }
        ?>
    </div>
</body>
</html>
