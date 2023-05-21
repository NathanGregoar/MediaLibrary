<!DOCTYPE html>
<html>
<head>
    <title>Consulter les Films</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="container">
        <div class="navbar">
            <a href="index.php">Ajouter un Film</a>
            <a href="search.php">Consulter les Films</a>
        </div>

        <h1>Consulter les Films</h1>

        <?php
        $host = 'db';
        $user = 'nathan';
        $password = '444719';
        $database = 'movie_collection';

        $connection = new mysqli($host, $user, $password, $database);

        if ($connection->connect_error) {
            die('Erreur de connexion : ' . $connection->connect_error);
        }

        // Recherche de films
        $search = isset($_GET['search']) ? $connection->real_escape_string($_GET['search']) : '';

        $selectSql = "SELECT * FROM films WHERE title LIKE '%$search%' OR director LIKE '%$search%'";

        $result = $connection->query($selectSql);

        if ($result->num_rows > 0) {
            echo '<table>';
            echo '<tr><th>Titre</th><th>Réalisateur</th><th>Année de sortie</th></tr>';

            while ($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . $row['title'] . '</td>';
                echo '<td>' . $row['director'] . '</td>';
                echo '<td>' . $row['release_year'] . '</td>';
                echo '</tr>';
            }

            echo '</table>';
        } else {
            echo '<div class="alert alert-info">Aucun film trouvé.</div>';
        }

        $connection->close();
        ?>
    </div>
</body>
</html>
