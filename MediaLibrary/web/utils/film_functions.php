<?php
// Fonctions pour la gestion des films

require_once 'config.php';

function getAllFilms($connection, $userId) {
    $allMoviesSql = "SELECT * FROM films WHERE user_id = $userId";
    $allMoviesResult = $connection->query($allMoviesSql);

    $films = [];
    while ($row = $allMoviesResult->fetch_assoc()) {
        $films[] = $row;
    }

    return $films;
}

function searchFilms($connection, $searchTerm, $userId) {
    $searchSql = "SELECT * FROM films WHERE title LIKE '%$searchTerm%' AND user_id = $userId";
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
            $year = $film['year'];

            echo '<div class="movie">';
            echo '<h3>' . $title . '</h3>';
            echo '<p>Réalisateur : ' . $director . '</p>';
            echo '<p>Année : ' . $year . '</p>';
            echo '<a href="film_delete.php?id=' . $id . '">Supprimer</a>';
            echo '</div>';
        }
        echo '</div>';
    } else {
        echo '<p>Aucun film trouvé.</p>';
    }
}
?>
