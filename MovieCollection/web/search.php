<!DOCTYPE html>
<html>
<head>
    <title>Recherche de Films</title>
</head>
<body>
    <h1>Recherche de Films</h1>

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

    $search = isset($_GET['search']) ? trim($_GET['search']) : '';

    $searchSql = "SELECT * FROM films";
    if (!empty($search)) {
        $search = $connection->real_escape_string($search);
        $searchSql .= " WHERE title LIKE '%$search%'";
    }

    $result = $connection->query($searchSql);

    if ($result->num_rows > 0) {
        echo '<ul>';

        while ($row = $result->fetch_assoc()) {
            echo '<li>' . $row['title'] . ' (' . $row['director'] . ', ' . $row['release_year'] . ')</li>';
        }

        echo '</ul>';
    } else {
        echo '<p>Aucun film trouvé.</p>';
    }

    $connection->close();
    ?>
</body>
</html>
