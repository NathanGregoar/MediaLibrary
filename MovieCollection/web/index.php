<!DOCTYPE html>
<html>
<head>
    <title>Ajouter un Film</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="container">
        <div class="navbar">
            <a href="index.php">Ajouter un Film</a>
            <a href="search.php">Consulter les Films</a>
        </div>

        <h1>Ajouter un Film</h1>

        <?php
        $host = 'db';
        $user = 'nathan';
        $password = '444719';
        $database = 'movie_collection';

        $connection = new mysqli($host, $user, $password, $database);

        if ($connection->connect_error) {
            die('Erreur de connexion : ' . $connection->connect_error);
        }

        // Ajout d'un film
        if (isset($_POST['title'])) {
            $title = $connection->real_escape_string($_POST['title']);
            $director = $_POST['director'] != '' ? "'" . $connection->real_escape_string($_POST['director']) . "'" : 'NULL';
            $releaseYear = $_POST['release_year'] != '' ? $connection->real_escape_string($_POST['release_year']) : 'NULL';

            $insertSql = "INSERT INTO films (title, director, release_year) VALUES ('$title', $director, $releaseYear)";

            if ($connection->query($insertSql) === TRUE) {
                echo '<div class="alert alert-success">Film ajouté avec succès !</div>';
            } else {
                echo '<div class="alert alert-error">Erreur lors de l\'ajout du film : ' . $connection->error . '</div>';
            }
        }
        ?>

        <form method="POST">
            <label for="title">Titre :</label>
            <input type="text" id="title" name="title" required>

            <label for="director">Réalisateur :</label>
            <input type="text" id="director" name="director">

            <label for="release_year">Année de sortie :</label>
            <input type="text" id="release_year" name="release_year">

            <input type="submit" value="Ajouter">
        </form>

        <?php
        $connection->close();
        ?>
    </div>
</body>
</html>
