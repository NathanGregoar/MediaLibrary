<!DOCTYPE html>
<html>
<head>
    <title>Ajouter une Série</title>
    <link rel="stylesheet" type="text/css" href="serie.css">
</head>
<body>
    <div class="container">
        <div class="navbar">
            <a href="../index.php">Accueil</a>
            <a href="serie.php">Ajouter une Série</a>
            <a href="serie_search.php">Consulter les Séries</a>
        </div>

        <h1>Ajouter une Série</h1>

        <?php
        $host = 'db';
        $user = 'nathan';
        $password = '444719';
        $database = 'movie_collection';

        $connection = new mysqli($host, $user, $password, $database);

        if ($connection->connect_error) {
            die('Erreur de connexion : ' . $connection->connect_error);
        }

        // Ajout d'une série
        if (isset($_POST['title'])) {
            $title = $connection->real_escape_string($_POST['title']);
            $langueSerie = $_POST['langue_serie'] != '' ? "'" . $connection->real_escape_string($_POST['langue_serie']) . "'" : 'NULL';
            $completeSeason = isset($_POST['complete_season']) ? 1 : 0;
            $episodeCount = $_POST['episode_count'] != '' ? $connection->real_escape_string($_POST['episode_count']) : 'NULL';
            $seasonNumber = $_POST['season_number'] != '' ? $connection->real_escape_string($_POST['season_number']) : 'NULL';
            $externalHardDrive = $_POST['external_hard_drive'] != '' ? "'" . $connection->real_escape_string($_POST['external_hard_drive']) . "'" : 'NULL';

            // Vérifier les doublons
            $duplicateSql = "SELECT * FROM series WHERE title = '$title' AND langue_serie = $langueSerie AND season_number = $seasonNumber";
            $duplicateResult = $connection->query($duplicateSql);

            if ($duplicateResult->num_rows > 0) {
                echo '<div class="alert alert-error">La série existe déjà dans la base de données.</div>';
            } else {
                $insertSql = "INSERT INTO series (title, langue_serie, complete_season, episode_count, season_number, external_hard_drive) VALUES ('$title', $langueSerie, $completeSeason, $episodeCount, $seasonNumber, $externalHardDrive)";

                if ($connection->query($insertSql) === TRUE) {
                    echo '<div class="alert alert-success">Série ajoutée avec succès !</div>';
                } else {
                    echo '<div class="alert alert-error">Erreur lors de l\'ajout de la série : ' . $connection->error . '</div>';
                }
            }
        }
        ?>

        <form method="POST">
            <div class="form-group">
                <label for="title">Titre :</label>
                <input type="text" id="title" name="title" required>
            </div>

            <div class="form-group">
                <label for="langue_serie">Langue de la série :</label>
                <select id="langue_serie" name="langue_serie">
                    <option value="Français" selected>Français</option>
                    <option value="Anglais">Anglais</option>
                    <option value="Coréen">Coréen</option>
                    <option value="Autre">Autre</option>
                </select>
            </div>

            <div class="form-group">
                <label for="complete_season">Saison complète :</label>
                <input type="checkbox" id="complete_season" name="complete_season">
            </div>

            <div class="form-group">
                <label for="episode_count">Nombre d'épisodes :</label>
                <input type="number" id="episode_count" name="episode_count">
            </div>

            <div class="form-group">
                <label for="season_number">Numéro de saison :</label>
                <input type="number" id="season_number" name="season_number">
            </div>

            <div class="form-group">
                <label for="external_hard_drive">Numéro du disque dur externe :</label>
                <input type="text" id="external_hard_drive" name="external_hard_drive">
            </div>

            <input type="submit" value="Ajouter" class="btn-submit">
        </form>

        <?php
        $connection->close();
        ?>

    </div>
</body>
</html>
