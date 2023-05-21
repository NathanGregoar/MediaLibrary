<!DOCTYPE html>
<html>
<head>
    <title>Rechercher des Films</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        .navbar {
            background-color: #f0f0f0;
            padding: 10px;
            margin-bottom: 20px;
        }

        .navbar a {
            margin-right: 10px;
            text-decoration: none;
            color: #333;
        }

        h1 {
            margin-bottom: 20px;
        }

        .search-bar {
            margin-bottom: 20px;
        }

        .movies-list {
            margin-bottom: 20px;
        }

        .movie-item {
            background-color: #f0f0f0;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 4px;
            display: flex;
        }

        .movie-item img {
            max-width: 150px;
            margin-right: 10px;
        }

        .movie-details {
            flex-grow: 1;
        }

        .movie-details h3 {
            margin: 0;
            margin-bottom: 5px;
        }

        .movie-details p {
            margin: 0;
            margin-bottom: 10px;
        }

        .movie-item .delete-btn {
            background-color: #dc3545;
            color: #fff;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            float: right;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="navbar">
            <a href="index.php">Ajouter un Film</a>
            <a href="search.php">Consulter les Films</a>
        </div>

        <h1>Rechercher des Films</h1>

        <div class="search-bar">
            <form method="GET">
                <input type="text" name="search" placeholder="Rechercher un film">
                <input type="submit" value="Rechercher">
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

        // Suppression d'un film
        if (isset($_POST['delete'])) {
            $deleteId = $connection->real_escape_string($_POST['delete']);
            $deleteSql = "DELETE FROM films WHERE id = $deleteId";

            if ($connection->query($deleteSql) === TRUE) {
                echo '<div class="alert alert-success">Film supprimé avec succès !</div>';
            } else {
                echo '<div class="alert alert-error">Erreur lors de la suppression du film : ' . $connection->error . '</div>';
            }
        }

        // Affichage des films correspondant à la recherche
        if (isset($_GET['search'])) {
            $searchTerm = $connection->real_escape_string($_GET['search']);
            $searchSql = "SELECT * FROM films WHERE title LIKE '%$searchTerm%'";
            $searchResult = $connection->query($searchSql);

            if ($searchResult->num_rows > 0) {
                echo '<h2>Résultats de la recherche :</h2>';
                echo '<div class="movies-list">';
                while ($row = $searchResult->fetch_assoc()) {
                    $id = $row['id'];
                    $title = $row['title'];
                    $director = $row['director'];
                    $releaseYear = $row['release_year'];

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

        // Affichage de tous les films
        $allMoviesSql = "SELECT * FROM films";
        $allMoviesResult = $connection->query($allMoviesSql);

        echo '<h2>Liste complète des films :</h2>';
        echo '<div class="movies-list">';
        while ($row = $allMoviesResult->fetch_assoc()) {
            $id = $row['id'];
            $title = $row['title'];
            $director = $row['director'];
            $releaseYear = $row['release_year'];

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
