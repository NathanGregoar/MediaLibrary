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

        <h2>Rechercher un Film</h2>
        <form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <label for="search">Titre :</label>
            <input type="text" name="search" required>
            <input type="submit" value="Rechercher">
        </form>

        <?php
        $host = 'db';
        $user = 'nathan';
        $password = '444719';
        $database = 'movie_collection';

        $connection = new mysqli($host, $user, $password, $database);

        if ($connection->connect_error) {
            die('Erreur de connexion : ' . $connection->connect_error);
        }

        if (isset($_GET['search'])) {
            $search = $connection->real_escape_string($_GET['search']);

            $sql = "SELECT * FROM films WHERE title LIKE '%$search%'";
            $result = $connection->query($sql);

            if ($result->num_rows > 0) {
                echo '<h2>Résultats de la recherche pour "' . $search . '" :</h2>';
                echo '<ul>';

                while ($row = $result->fetch_assoc()) {
                    $director = $row['director'] != '/' ? $row['director'] : '';
                    $releaseYear = $row['release_year'] != '/' ? $row['release_year'] : '';

                    echo '<li><strong>' . $row['title'] . '</strong> <span class="movie-info">(' . $director . ', ' . $releaseYear . ')</span></li>';
                }

                echo '</ul>';
            } else {
                echo '<p>Aucun film trouvé pour "' . $search . '".</p>';
            }
        }

        $connection->close();
        ?>
    </div>
</body>
</html>
