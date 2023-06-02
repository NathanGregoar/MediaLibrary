<!DOCTYPE html>
<html>
<head>
    <title>Rechercher des Séries</title>
    <link rel="stylesheet" type="text/css" href="film.css">
</head>
<body>
    <div class="container">
        <div class="navbar">
            <a href="../index.php">Accueil</a>
            <a href="serie.php">Ajouter une Série</a>
            <a href="serie_search.php">Consulter les Séries</a>
        </div>

        <h1>Rechercher des Séries</h1>

        <div class="search-bar">
            <form method="GET">
                <input type="text" name="search" placeholder="Rechercher une série" style="width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                <input type="submit" value="Rechercher" style="padding: 10px 20px; background-color: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer;">
            </form>
        </div>

        <?php
        $host = 'db';
        $user = 'nathan';
        $password = '444719';
        $database = 'movie_collection';

        $connection = new mysqli($host, $user, $password, $database);

        if ($connection->connect_error) {
            die('Erreur de connexion : ' . $connection->connect_error);
        }

        // Suppression d'une série
        if (isset($_POST['delete'])) {
            $deleteId = $connection->real_escape_string($_POST['delete']);
            $deleteSql = "DELETE FROM series WHERE id = $deleteId";

            if ($connection->query($deleteSql) === TRUE) {
                echo '<div class="alert alert-success">Série supprimée avec succès !</div>';
            } else {
                echo '<div class="alert alert-error">Erreur lors de la suppression de la série : ' . $connection->error . '</div>';
            }
        }

        // Affichage des séries correspondant à la recherche
        if (isset($_GET['search'])) {
            $searchTerm = $connection->real_escape_string($_GET['search']);
            $searchSql = "SELECT * FROM series WHERE title LIKE '%$searchTerm%'";
            $searchResult = $connection->query($searchSql);

            $searchCount = $searchResult->num_rows;

            if ($searchCount > 0) {
                echo '<h2>Résultats de la recherche (' . $searchCount . ') :</h2>';
                echo '<div class="movies-list">';
                while ($row = $searchResult->fetch_assoc()) {
                    $id = $row['id'];
                    $title = $row['title'];
                    $langueSerie = $row['langue_serie'];
                    $completeSeason = $row['complete_season'];
                    $episodeCount = $row['episode_count'];
                    $seasonNumber = $row['season_number'];
                    $externalHardDrive = $row['external_hard_drive'];

                    echo '<div class="movie-item">';
                    echo '<div class="movie-details">';
                    echo '<h3>' . $title . '</h3>';
                    echo '<p><strong>Langue de la série :</strong> ' . ($langueSerie != '' ? $langueSerie : '/') . '</p>';
                    echo '<p><strong>Saison complète :</strong> ' . ($completeSeason == '1' ? 'Oui' : 'Non') . '</p>';
                    echo '<p><strong>Nombre d\'épisodes :</strong> ' . ($episodeCount != '' ? $episodeCount : '/') . '</p>';
                    echo '<p><strong>Numéro de saison :</strong> ' . ($seasonNumber != '' ? $seasonNumber : '/') . '</p>';
                    echo '<p><strong>Numéro du disque dur externe :</strong> ' . ($externalHardDrive != '' ? $externalHardDrive : '/') . '</p>';

                    echo '<form method="POST" style="display:inline">';
                    echo '<input type="hidden" name="delete" value="' . $id . '">';
                    echo '<input type="submit" value="Supprimer" class="delete-btn">';
                    echo '</form>';

                    echo '</div>'; // .movie-details
                    echo '</div>'; // .movie-item
                }
                echo '</div>'; // .movies-list
            } else {
                echo '<div class="alert">Aucun résultat trouvé pour votre recherche.</div>';
            }
        }

        // Affichage de toutes les séries
        $allSeriesSql = "SELECT * FROM series";
        $allSeriesResult = $connection->query($allSeriesSql);

        $allSeriesCount = $allSeriesResult->num_rows;

        echo '<h2>Liste complète des séries (' . $allSeriesCount . ') :</h2>';
        echo '<div class="movies-list">';
        while ($row = $allSeriesResult->fetch_assoc()) {
            $id = $row['id'];
            $title = $row['title'];
            $langueSerie = $row['langue_serie'];
            $completeSeason = $row['complete_season'];
            $episodeCount = $row['episode_count'];
            $seasonNumber = $row['season_number'];
            $externalHardDrive = $row['external_hard_drive'];

            echo '<div class="movie-item">';
            echo '<div class="movie-details">';
            echo '<h3>' . $title . '</h3>';
            echo '<p><strong>Langue de la série :</strong> ' . ($langueSerie != '' ? $langueSerie : '/') . '</p>';
            echo '<p><strong>Saison complète :</strong> ' . ($completeSeason == '1' ? 'Oui' : 'Non') . '</p>';
            echo '<p><strong>Nombre d\'épisodes :</strong> ' . ($episodeCount != '' ? $episodeCount : '/') . '</p>';
            echo '<p><strong>Numéro de saison :</strong> ' . ($seasonNumber != '' ? $seasonNumber : '/') . '</p>';
            echo '<p><strong>Numéro du disque dur externe :</strong> ' . ($externalHardDrive != '' ? $externalHardDrive : '/') . '</p>';

            echo '<form method="POST" style="display:inline">';
            echo '<input type="hidden" name="delete" value="' . $id . '">';
            echo '<input type="submit" value="Supprimer" class="delete-btn">';
            echo '</form>';

            echo '</div>'; // .movie-details
            echo '</div>'; // .movie-item
        }
        echo '</div>'; // .movies-list

        $connection->close();
        ?>
    </div>
</body>
</html>
