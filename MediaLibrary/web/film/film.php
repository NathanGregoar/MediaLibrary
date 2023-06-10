<?php
require_once '../utils/auth.php';
require_once '../utils/config.php';

$connection = mysqli_connect($host, $username, $password, $dbName);

if (!$connection) {
    die('Erreur de connexion à la base de données : ' . mysqli_connect_error());
}

// Ajout d'un film
if (isset($_POST['title'])) {
    $title = $connection->real_escape_string($_POST['title']);
    $director = $connection->real_escape_string($_POST['director']);
    $releaseYear = $connection->real_escape_string($_POST['release_year']);
    $externalHardDrive = $connection->real_escape_string($_POST['external_hard_drive']);

    $query = "INSERT INTO films (title, director, release_year, external_hard_drive) VALUES ('$title', '$director', '$releaseYear', '$externalHardDrive')";

    if (mysqli_query($connection, $query)) {
        echo 'Le film a été ajouté avec succès.';
    } else {
        echo 'Erreur lors de l\'ajout du film : ' . mysqli_error($connection);
    }
}

mysqli_close($connection);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ajouter un Film</title>
    <link rel="stylesheet" type="text/css" href="./film.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#title').on('blur', function() {
                var title = $(this).val();
                var director = $('#director').val();

                if (title !== '' && director !== '') {
                    $.ajax({
                        url: 'get_film_info.php',
                        method: 'GET',
                        data: { title: title, director: director },
                        dataType: 'json',
                        success: function(response) {
                            if (response) {
                                $('#director').val(response.director);
                                $('#release_year').val(response.releaseYear);
                                $('#external_hard_drive').val(response.externalHardDrive);
                            }
                        }
                    });
                }
            });

            $('#director').on('blur', function() {
                var title = $('#title').val();
                var director = $(this).val();

                if (title !== '' && director !== '') {
                    $.ajax({
                        url: 'get_film_info.php',
                        method: 'GET',
                        data: { title: title, director: director },
                        dataType: 'json',
                        success: function(response) {
                            if (response) {
                                $('#release_year').val(response.releaseYear);
                                $('#external_hard_drive').val(response.externalHardDrive);
                            }
                        }
                    });
                }
            });
        });
    </script>
</head>
<body>
    <div class="container">
        <div class="navbar">
            <a href="../accueil/index.php">Accueil</a>
            <a href="./film.php">Ajouter un Film</a>
            <a href="./film_search.php">Consulter les Films</a>
        </div>

        <h1>Ajouter un Film</h1>

        <form method="POST" action="">
            <label for="title">Titre :</label>
            <input type="text" id="title" name="title" required><br><br>

            <label for="director">Réalisateur :</label>
            <input type="text" id="director" name="director" required><br><br>

            <label for="release_year">Année de sortie :</label>
            <input type="number" id="release_year" name="release_year" required><br><br>

            <label for="external_hard_drive">Disque dur externe :</label>
            <input type="text" id="external_hard_drive" name="external_hard_drive"><br><br>

            <input type="submit" value="Ajouter">
        </form>
    </div>
</body>
</html>
