<?php
// Fonctions pour la gestion des films

function getAllFilms($connection) {
    $allMoviesSql = "SELECT * FROM films";
    $allMoviesResult = $connection->query($allMoviesSql);

    $films = [];
    while ($row = $allMoviesResult->fetch_assoc()) {
        $films[] = $row;
    }

    return $films;
}

function searchFilms($connection, $searchTerm) {
    $searchSql = "SELECT * FROM films WHERE title LIKE '%$searchTerm%'";
    $searchResult = $connection->query($searchSql);

    $films = [];
    while ($row = $searchResult->fetch_assoc()) {
        $films[] = $row;
    }

    return $films;
}

function deleteFilm($connection, $filmId) {
    $deleteSql = "DELETE FROM films WHERE id = $filmId";

    if ($connection->query($deleteSql) === TRUE) {
        echo '<div class="alert alert-success">Film supprimé avec succès !</div>';
    } else {
        echo '<div class="alert alert-error">Erreur lors de la suppression du film : ' . $connection->error . '</div>';
    }
}

function displayFilms($films) {
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
            echo '<p><strong>Réalisateur :</strong> ' . ($director != 'NULL' ? $director : '') . '</p>';
            echo '<p><strong>Année de sortie :</strong> ' . ($releaseYear != 'NULL' ? $releaseYear : '') . '</p>';
            echo '<p><strong>Disque dur externe :</strong> ' . ($externalHardDrive != 'NULL' ? $externalHardDrive : '') . '</p>';

            echo '<form method="POST" style="display:inline">';
            echo '<input type="hidden" name="delete" value="' . $id . '">';
            echo '<input type="submit" value="Supprimer" class="delete-btn">';
            echo '</form>';

            echo '</div>'; // .movie-details
            echo '</div>'; // .movie-item
        }
        echo '</div>'; // .movies-list
    } else {
        echo '<div class="alert">Aucun film trouvé.</div>';
    }
}
?>
