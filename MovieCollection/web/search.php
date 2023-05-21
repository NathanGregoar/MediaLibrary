<!DOCTYPE html>
<html>
<head>
    <title>Rechercher des Films</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f8f8;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .navbar {
            background-color: #f8f9fa;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .navbar a {
            margin-right: 10px;
            text-decoration: none;
            color: #000;
            font-weight: bold;
        }

        h1, h2 {
            color: #333;
        }

        .alert {
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 4px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            margin-bottom: 10px;
        }

        input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .movies-list {
            margin-top: 20px;
        }

        .movie-item {
            background-color: #f0f0f0;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 4px;
        }

        .movie-item span {
            font-weight: bold;
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

        // Recherche de films
        $searchQuery = '';

        if (isset($_GET['search'])) {
            $searchQuery = $connection->real_escape_string($_GET['search']);
        }

        $searchSql = "SELECT * FROM films WHERE title LIKE '%$searchQuery%' OR director LIKE '%$searchQuery%' OR release_year LIKE '%$searchQuery%'";
        $result = $connection->query($searchSql);

        if ($result->num_rows > 0) {
            echo '<h2>Résultats de la recherche :</h2>';

            echo '<div class="movies-list">';
            while ($row = $result->fetch_assoc()) {
                $id = $row['id'];
                $title = $row['title'];
                $director = $row['director'];
                $releaseYear = $row['release_year'];

                echo '<div class="movie-item">';
                echo '<span>Titre :</span> ' . $title . '<br>';
                echo '<span>Réalisateur :</span> ' . ($director != 'NULL' ? $director : '') . '<br>';
                echo '<span>Année de sortie :</span> ' . ($releaseYear != 'NULL' ? $releaseYear : '') . '<br>';

                echo '<form method="POST" style="display:inline">';
                echo '<input type="hidden" name="delete" value="' . $id . '">';
                echo '<input type="submit" value="Supprimer" class="delete-btn">';
                echo '</form>';

                echo '</div>';
            }
            echo '</div>';
        } else {
            echo '<div class="alert">Aucun résultat trouvé.</div>';
        }

        $connection->close();
        ?>

        <h2>Tous les Films :</h2>

        <?php
        $connection = new mysqli($host, $user, $password, $database);

        if ($connection->connect_error) {
            die('Erreur de connexion : ' . $connection->connect_error);
        }

        $allMoviesSql = "SELECT * FROM films";
        $allMoviesResult = $connection->query($allMoviesSql);

        if ($allMoviesResult->num_rows > 0) {
            echo '<div class="movies-list">';
            while ($row = $allMoviesResult->fetch_assoc()) {
                $id = $row['id'];
                $title = $row['title'];
                $director = $row['director'];
                $releaseYear = $row['release_year'];

                echo '<div class="movie-item">';
                echo '<span>Titre :</span> ' . $title . '<br>';
                echo '<span>Réalisateur :</span> ' . ($director != 'NULL' ? $director : '') . '<br>';
                echo '<span>Année de sortie :</span> ' . ($releaseYear != 'NULL' ? $releaseYear : '') . '<br>';

                echo '<form method="POST" style="display:inline">';
                echo '<input type="hidden" name="delete" value="' . $id . '">';
                echo '<input type="submit" value="Supprimer" class="delete-btn">';
                echo '</form>';

                echo '</div>';
            }
            echo '</div>';
        } else {
            echo '<div class="alert">Aucun film trouvé.</div>';
        }

        $connection->close();
        ?>
    </div>
</body>
</html>
