<!DOCTYPE html>
<html>
<head>
    <title>Ma Collection de Films</title>
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

        input[type="text"], input[type="number"] {
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

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            margin-bottom: 5px;
        }

        .movie-info {
            font-style: italic;
            color: #777;
        }

        .movie-delete {
            color: #ff0000;
            text-decoration: none;
            margin-left: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="navbar">
            <a href="index.php">Ajouter un Film</a>
            <a href="search.php">Consulter les Films</a>
        </div>

        <h1>Ma Collection de Films</h1>

        <?php
        $host = 'db';
        $user = 'nathan';
        $password = '444719';
        $database = 'movie_collection';

        $connection = new mysqli($host, $user, $password, $database);

        if ($connection->connect_error) {
            die('Erreur de connexion : ' . $connection->connect_error);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title']);
            $director = trim($_POST['director']);
            $releaseYear = trim($_POST['release_year']);

            if (empty($title)) {
                echo '<div class="alert alert-error">Veuillez renseigner le titre du film.</div>';
            } else {
                $title = $connection->real_escape_string($title);
                $director = $connection->real_escape_string($director);
                $releaseYear = $connection->real_escape_string($releaseYear);

                if (empty($director)) {
                    $director = '/';
                }
                if (empty($releaseYear)) {
                    $releaseYear = '/';
                }

                $checkDuplicateSql = "SELECT * FROM films WHERE title = '$title' AND director = '$director' AND release_year = '$releaseYear'";
                $duplicateResult = $connection->query($checkDuplicateSql);

                if ($duplicateResult->num_rows > 0) {
                    echo '<div class="alert alert-error">Ce film existe déjà dans la collection.</div>';
                } else {
                    $insertSql = "INSERT INTO films (title, director, release_year) VALUES ('$title', '$director', '$releaseYear')";

                    if ($connection->query($insertSql) === TRUE) {
                        echo '<div class="alert alert-success">Film ajouté avec succès !</div>';
                    } else {
                        echo '<div class="alert alert-error">Erreur lors de l\'ajout du film : ' . $connection->error . '</div>';
                    }
                }
            }
        }

        if (isset($_GET['delete'])) {
            $filmId = $_GET['delete'];

            $deleteSql = "DELETE FROM films WHERE id = $filmId";

            if ($connection->query($deleteSql) === TRUE) {
                echo '<div class="alert alert-success">Film supprimé avec succès !</div>';
            } else {
                echo '<div class="alert alert-error">Erreur lors de la suppression du film : ' . $connection->error . '</div>';
            }
        }
        ?>

        <h2>Ajouter un Film</h2>
        <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <label for="title">Titre :</label>
            <input type="text" name="title" required><br>

            <label for="director">Réalisateur :</label>
            <input type="text" name="director"><br>

            <label for="release_year">Année de sortie :</label>
            <input type="number" name="release_year"><br>

            <input type="submit" value="Ajouter le Film">
        </form>

        <h2>Mes Films</h2>

        <?php
        $sql = "SELECT * FROM films";
        $result = $connection->query($sql);

        if ($result->num_rows > 0) {
            echo '<ul>';

            while ($row = $result->fetch_assoc()) {
                $director = $row['director'] != '/' ? $row['director'] : '';
                $releaseYear = $row['release_year'] != '/' ? $row['release_year'] : '';

                echo '<li><strong>' . $row['title'] . '</strong> <span class="movie-info">(' . $director . ', ' . $releaseYear . ')</span>';
                echo '<a class="movie-delete" href="?delete=' . $row['id'] . '">Supprimer</a></li>';
            }

            echo '</ul>';
        } else {
            echo '<p>Aucun film trouvé.</p>';
        }

        $connection->close();
        ?>
    </div>
</body>
</html>
