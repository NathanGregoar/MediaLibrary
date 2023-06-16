<?php
require_once '../utils/auth.php';
require_once '../utils/config.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ajouter une Série</title>
    <link rel="stylesheet" type="text/css" href="./series.css">
    <style>
        body {
            overflow: hidden;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="../accueil/index.php">Accueil</a>
        <a href="./series.php">Ajouter une Série</a>
        <a href="./series_search.php">Consulter les Séries</a>
    </div>
    <h1>Ajouter une Série</h1>
    <div class="container">
        <?php
        $connection = mysqli_connect($host, $username, $password, $dbName);

        if (!$connection) {
            die('Erreur de connexion à la base de données : ' . mysqli_connect_error());
        }

        // Ajout d'une série
        if (isset($_POST['title'])) {
            $title = $connection->real_escape_string($_POST['title']);
            $language = $_POST['language'] != '' ? $connection->real_escape_string($_POST['language']) : '/';
            $completeSeason = isset($_POST['complete_season']) ? 1 : 0;
            $episodeCount = $_POST['episode_count'] != '' ? intval($_POST['episode_count']) : 0;
            $seasonNumber = $_POST['season_number'] != '' ? intval($_POST['season_number']) : 0;
            $externalHardDrive = $_POST['external_hard_drive'] != '' ? intval($_POST['external_hard_drive']) : 'NULL';
        
            // Récupérer l'ID de l'utilisateur connecté à partir des informations de session
            $loggedInUser = getLoggedInUser();
        
            // Vérifier les doublons
            $duplicateSql = "SELECT * FROM series WHERE title = '$title' AND added_by = " . $loggedInUser['id'];
            $duplicateResult = $connection->query($duplicateSql);
        
            if ($duplicateResult->num_rows > 0) {
                echo '<div class="alert alert-error">La série existe déjà dans la base de données.</div>';
            } else {
                $insertSql = "INSERT INTO series (title, langue_serie, complete_season, episode_count, season_number, external_hard_drive, added_by) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $insertStmt = $connection->prepare($insertSql);
                $insertStmt->bind_param("ssiiiii", $title, $language, $completeSeason, $episodeCount, $seasonNumber, $externalHardDrive, $loggedInUser['id']);
        
                if ($insertStmt->execute()) {
                    echo '<div class="alert alert-success">Série ajoutée avec succès !</div>';
                } else {
                    echo '<div class="alert alert-error">Erreur lors de l\'ajout de la série : ' . $connection->error . '</div>';
                }
            }
        }
        ?>

        <form method="POST">
            <label for="title">Titre :</label>
            <input type="text" id="title" name="title" required>

            <label for="language">Langue :</label>
            <input type="text" id="language" name="language">

            <label for="complete_season">Saison complète :</label>
            <input type="checkbox" id="complete_season" name="complete_season"></br></br>

            <label for="episode_count">Nombre d'épisodes :</label>
            <input type="number" id="episode_count" name="episode_count" min="0">

            <label for="season_number">Numéro de saison :</label>
            <input type="number" id="season_number" name="season_number" min="0">

            <label for="external_hard_drive">Numéro du disque dur externe :</label>
            <input type="text" id="external_hard_drive" name="external_hard_drive">

            <input type="submit" value="Ajouter">
        </form>

        <?php
        $connection->close();
        ?>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#title').blur(function() {
                    var title = $(this).val();
                    if (title !== '') {
                        $.ajax({
                            url: 'http://www.omdbapi.com/',
                            data: { t: title, apiKey: 'f1e681ff' },
                            dataType: 'json',
                            success: function(data) {
                                if (data.Response === 'True') {
                                    $('#language').val(data.Language ? data.Language : '');
                                }
                            }
                        });
                    }
                });
            });
        </script>
    </div>
</body>
</html>
