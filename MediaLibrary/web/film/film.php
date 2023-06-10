<?php
require_once '../utils/auth.php';
require_once '../utils/config.php';

if (isset($_GET['title']) && isset($_GET['director'])) {
    $title = urlencode($_GET['title']);
    $director = urlencode($_GET['director']);
    $apiKey = 'YOUR_API_KEY'; // Remplacez par votre clé d'API OMDB

    $url = "http://www.omdbapi.com/?apikey=$apiKey&t=$title&y=$director";

    $response = file_get_contents($url);

    $data = json_decode($response, true);

    if ($data['Response'] == 'True') {
        $filmInfo = array(
            'title' => $data['Title'],
            'director' => $_GET['director'],
            'releaseYear' => $data['Year'],
            'externalHardDrive' => 'N/A'
        );

        echo json_encode($filmInfo);
    }
}
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

        <?php
        $connection = mysqli_connect($host, $username, $password, $dbName);

        if (!$connection) {
            die('Erreur de connexion à la base de données : ' . mysqli_connect_error());
        }

        // Ajout d'un film
        if (isset($_POST['title'])) {
            $title = $connection->real_escape_string($_POST['title']);
            $director = $_POST['director'] != '' ? $connection->real_escape_string($_POST['director']) : 'NULL';
            $releaseYear = $_POST['release_year'] != '' ? $connection->real_escape_string($_POST['release_year']) : 'NULL';
            $externalHardDrive = $_POST['external_hard_drive'] != '' ? intval($_POST['external_hard_drive']) : 'NULL';
        
            // Récupérer l'ID de l'utilisateur connecté à partir des informations de session
            $loggedInUser = getLoggedInUser();
        
            // Vérifier les doublons
            $duplicateSql = "SELECT * FROM films WHERE title = '$title' AND director = '$director' AND release_year = '$releaseYear' AND added_by = " . $loggedInUser['id'];
            $duplicateResult = $connection->query($duplicateSql);
        
            if ($duplicateResult->num_rows > 0) {
                echo '<div class="alert alert-error">Le film existe déjà dans la base de données.</div>';
            } else {
                $insertSql = "INSERT INTO films (title, director, release_year, external_hard_drive, added_by) VALUES (?, ?, ?, ?, ?)";
                $insertStmt = $connection->prepare($insertSql);
                $insertStmt->bind_param("ssisi", $title, $director, $releaseYear, $externalHardDrive, $loggedInUser['id']);
        
                if ($insertStmt->execute()) {
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
