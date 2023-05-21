<!DOCTYPE html>
<html>
<head>
    <title>Ma Collection de Films</title>
</head>
<body>
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
        $title = $_POST['title'];
        $director = $_POST['director'];
        $releaseYear = $_POST['release_year'];

        $sql = "INSERT INTO films (title, director, release_year) VALUES ('$title', '$director', '$releaseYear')";

        if ($connection->query($sql) === TRUE) {
            echo '<p>Film ajouté avec succès !</p>';
        } else {
            echo '<p>Erreur lors de l\'ajout du film : ' . $connection->error . '</p>';
        }
    }

    if (isset($_GET['delete'])) {
        $filmId = $_GET['delete'];

        $deleteSql = "DELETE FROM films WHERE id = '$filmId'";

        if ($connection->query($deleteSql) === TRUE) {
            echo '<p>Film supprimé avec succès !</p>';
        } else {
            echo '<p>Erreur lors de la suppression du film : ' . $connection->error . '</p>';
        }
    }
    ?>

    <h2>Ajouter un Film</h2>
    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <label for="title">Titre :</label>
        <input type="text" name="title" required><br>

        <label for="director">Réalisateur :</label>
        <input type="text" name="director" required><br>

        <label for="release_year">Année de sortie :</label>
        <input type="number" name="release_year" required><br>

        <input type="submit" value="Ajouter le Film">
    </form>

    <h2>Mes Films</h2>

    <?php
    $sql = "SELECT * FROM films";
    $result = $connection->query($sql);

    if ($result->num_rows > 0) {
        echo '<ul>';

        while ($row = $result->fetch_assoc()) {
            echo '<li>' . $row['title'] . ' (' . $row['director'] . ', ' . $row['release_year'] . ') ';
            echo '<a href="?delete=' . $row['id'] . '">Supprimer</a></li>';
        }

        echo '</ul>';
    } else {
        echo '<p>Aucun film trouvé.</p>';
    }

    $connection->close();
    ?>
</body>
</html>
