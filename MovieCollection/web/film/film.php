<!DOCTYPE html>
<html>
<head>
    <title>Ajouter un Film</title>
    <link rel="stylesheet" type="text/css" href="film.css">
</head>
<body>
    <div class="container">
        <div class="navbar">
            <a href="../index.php">Accueil</a>
            <a href="film.php">Ajouter un Film</a>
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
            $externalHardDrive = $_POST['external_hard_drive'] != '' ? "'" . $connection->real_escape_string($_POST['external_hard_drive']) . "'" : 'NULL';

            // Vérifier les doublons
            $duplicateSql = "SELECT * FROM films WHERE title = '$title' AND director = $director AND release_year = $releaseYear";
            $duplicateResult = $connection->query($duplicateSql);

            if ($duplicateResult->num_rows > 0) {
                echo '<div class="alert alert-error">Le film existe déjà dans la base de données.</div>';
            } else {
                $insertSql = "INSERT INTO films (title, director, release_year, external_hard_drive) VALUES ('$title', $director, $releaseYear, $externalHardDrive)";

                if ($connection->query($insertSql) === TRUE) {
                    echo '<div class="alert alert-success">Film ajouté avec succès !</div>';
                } else {
                    echo '<div class="alert alert-error">Erreur lors de l\'ajout du film : ' . $connection->error . '</div>';
                }
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

            <label for="external_hard_drive">Numéro du disque dur externe :</label>
            <input type="text" id="external_hard_drive" name="external_hard_drive">

            <input type="submit" value="Ajouter">
        </form>

        <?php
        $connection->close();
        ?>
    </div>
</body>
</html>
