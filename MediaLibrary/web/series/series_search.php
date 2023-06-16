<?php
require_once '../utils/auth.php';
require_once '../utils/config.php';

// Récupérer l'utilisateur connecté
$loggedInUser = getLoggedInUser();

// Connexion à la base de données
$connection = mysqli_connect($host, $username, $password, $dbName);
if (!$connection) {
    die('Erreur de connexion à la base de données : ' . mysqli_connect_error());
}

// Suppression d'une série
if (isset($_POST['delete'])) {
    $deleteId = $connection->real_escape_string($_POST['delete']);
    $deleteSql = "DELETE FROM series WHERE id = $deleteId AND added_by = " . $loggedInUser['id'];

    if ($connection->query($deleteSql) === TRUE) {
        $deleteAlert = '<div class="alert alert-success">Série supprimée avec succès !</div>';
    } else {
        $deleteAlert = '<div class="alert alert-error">Erreur lors de la suppression de la série : ' . $connection->error . '</div>';
    }
}

// Modification d'une série
if (isset($_POST['edit'])) {
    $editId = $connection->real_escape_string($_POST['edit']);
    $editSql = "SELECT * FROM series WHERE id = $editId AND added_by = " . $loggedInUser['id'];
    $editResult = $connection->query($editSql);

    if ($editResult->num_rows > 0) {
        $editData = $editResult->fetch_assoc();
        $editTitle = $editData['title'];
        $editLangueSerie = $editData['langue_serie'];
        $editCompleteSeason = $editData['complete_season'];
        $editEpisodeCount = $editData['episode_count'];
        $editSeasonNumber = $editData['season_number'];
        $editExternalHardDrive = $editData['external_hard_drive'];
        $editFormVisible = true;
    }
}

// Mise à jour d'une série
if (isset($_POST['update'])) {
    $updateId = $connection->real_escape_string($_POST['series_id']);
    $updateTitle = $connection->real_escape_string($_POST['title']);
    $updateLangueSerie = $connection->real_escape_string($_POST['langue_serie']);
    $updateCompleteSeason = isset($_POST['complete_season']) ? 1 : 0;
    $updateEpisodeCount = $connection->real_escape_string($_POST['episode_count']);
    $updateSeasonNumber = $connection->real_escape_string($_POST['season_number']);
    $updateExternalHardDrive = $connection->real_escape_string($_POST['external_hard_drive']);

    $updateSql = "UPDATE series SET title = '$updateTitle', langue_serie = '$updateLangueSerie', complete_season = $updateCompleteSeason, episode_count = $updateEpisodeCount, season_number = $updateSeasonNumber, external_hard_drive = '$updateExternalHardDrive' WHERE id = $updateId AND added_by = " . $loggedInUser['id'];

    if ($connection->query($updateSql) === TRUE) {
        $updateAlert = '<div class="alert alert-success">Série mise à jour avec succès !</div>';
    } else {
        $updateAlert = '<div class="alert alert-error">Erreur lors de la mise à jour de la série : ' . $connection->error . '</div>';
    }
}

// Récupération des séries correspondant à la recherche
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$searchSql = "SELECT * FROM series WHERE title LIKE '%$searchTerm%' AND added_by = " . $loggedInUser['id'];
$searchResult = $connection->query($searchSql);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Recherche de séries</title>
    <link rel="stylesheet" type="text/css" href="../styles.css">
</head>

<body>
    <div class="container">
        <h1>Recherche de séries</h1>

        <?php if ($deleteAlert) {
            echo $deleteAlert;
        } ?>

        <?php if ($updateAlert) {
            echo $updateAlert;
        } ?>

        <form class="search-form" method="GET" action="series_search.php">
            <input type="text" name="search" placeholder="Rechercher une série" value="<?php echo $searchTerm; ?>">
            <button type="submit">Rechercher</button>
        </form>

        <table>
            <tr>
                <th>Titre</th>
                <th>Langue</th>
                <th>Saison complète</th>
                <th>Nombre d'épisodes</th>
                <th>Numéro de saison</th>
                <th>Disque dur externe</th>
                <th>Actions</th>
            </tr>

            <?php
            if ($searchResult->num_rows > 0) {
                while ($row = $searchResult->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td>' . $row['title'] . '</td>';
                    echo '<td>' . $row['langue_serie'] . '</td>';
                    echo '<td>' . ($row['complete_season'] ? 'Oui' : 'Non') . '</td>';
                    echo '<td>' . $row['episode_count'] . '</td>';
                    echo '<td>' . $row['season_number'] . '</td>';
                    echo '<td>' . $row['external_hard_drive'] . '</td>';
                    echo '<td>';
                    echo '<form method="POST" action="series_search.php">';
                    echo '<input type="hidden" name="delete" value="' . $row['id'] . '">';
                    echo '<button type="submit" class="delete-button">Supprimer</button>';
                    echo '</form>';
                    echo '<form method="POST" action="series_search.php">';
                    echo '<input type="hidden" name="edit" value="' . $row['id'] . '">';
                    echo '<button type="submit" class="edit-button">Modifier</button>';
                    echo '</form>';
                    echo '</td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="7">Aucune série trouvée.</td></tr>';
            }
            ?>

        </table>

        <?php if ($editFormVisible) { ?>
            <h2>Modifier une série</h2>

            <form method="POST" action="series_search.php">
                <input type="hidden" name="series_id" value="<?php echo $editId; ?>">
                <label for="title">Titre :</label>
                <input type="text" name="title" value="<?php echo $editTitle; ?>" required>
                <label for="langue_serie">Langue :</label>
                <input type="text" name="langue_serie" value="<?php echo $editLangueSerie; ?>" required>
                <label for="complete_season">Saison complète :</label>
                <input type="checkbox" name="complete_season" <?php echo $editCompleteSeason ? 'checked' : ''; ?>>
                <label for="episode_count">Nombre d'épisodes :</label>
                <input type="number" name="episode_count" value="<?php echo $editEpisodeCount; ?>" required>
                <label for="season_number">Numéro de saison :</label>
                <input type="number" name="season_number" value="<?php echo $editSeasonNumber; ?>" required>
                <label for="external_hard_drive">Disque dur externe :</label>
                <input type="text" name="external_hard_drive" value="<?php echo $editExternalHardDrive; ?>" required>
                <button type="submit" name="update">Mettre à jour</button>
            </form>
        <?php } ?>

        <a href="../dashboard.php">Retour au tableau de bord</a>
    </div>
</body>

</html>

<?php
// Fermer la connexion à la base de données
$connection->close();
?>
