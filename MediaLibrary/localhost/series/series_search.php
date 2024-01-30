<?php
require_once '../utils/auth.php';
require_once '../utils/config.php';

// Récupérer l'utilisateur connecté
$loggedInUser = getLoggedInUser();

// Connexion à la base de données
$connect = mysqli_connect($host, $username, $password, $dbName);
if (!$connect) {
    die('Erreur de connexion à la base de données : ' . mysqli_connect_error());
}

// Suppression d'une série
if (isset($_POST['delete'])) {
    $deleteId = $connect->real_escape_string($_POST['delete']);
    $deleteSql = "DELETE FROM series WHERE id = $deleteId AND added_by = " . $loggedInUser['id'];

    if ($connect->query($deleteSql) === TRUE) {
        $deleteAlert = '<div class="alert alert-success">Série supprimée avec succès !</div>';
    } else {
        $deleteAlert = '<div class="alert alert-error">Erreur lors de la suppression de la série : ' . $connect->error . '</div>';
    }
}

// Modification d'une série
if (isset($_POST['edit'])) {
    $editId = $connect->real_escape_string($_POST['edit']);
    $editSql = "SELECT * FROM series WHERE id = $editId AND added_by = " . $loggedInUser['id'];
    $editResult = $connect->query($editSql);

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
    $updateId = $connect->real_escape_string($_POST['series_id']);
    $updateTitle = $connect->real_escape_string($_POST['title']);
    $updateLangueSerie = $connect->real_escape_string($_POST['langue_serie']);
    $updateCompleteSeason = isset($_POST['complete_season']) ? 1 : 0;
    $updateEpisodeCount = $connect->real_escape_string($_POST['episode_count']);
    $updateSeasonNumber = $connect->real_escape_string($_POST['season_number']);
    $updateExternalHardDrive = $connect->real_escape_string($_POST['external_hard_drive']);

    $updateSql = "UPDATE series SET title = '$updateTitle', langue_serie = '$updateLangueSerie', complete_season = $updateCompleteSeason, episode_count = $updateEpisodeCount, season_number = $updateSeasonNumber, external_hard_drive = '$updateExternalHardDrive' WHERE id = $updateId AND added_by = " . $loggedInUser['id'];

    if ($connect->query($updateSql) === TRUE) {
        $updateAlert = '<div class="alert alert-success">Série mise à jour avec succès !</div>';
    } else {
        $updateAlert = '<div class="alert alert-error">Erreur lors de la mise à jour de la série : ' . $connect->error . '</div>';
    }
}

// Récupération des séries correspondant à la recherche
$searchTerm = isset($_GET['search']) ? $connect->real_escape_string($_GET['search']) : '';
$searchSql = "SELECT * FROM series WHERE title LIKE '%$searchTerm%' AND added_by = " . $loggedInUser['id'];
$searchResult = $connect->query($searchSql);

// Récupération de toutes les séries ajoutées par l'utilisateur connecté
$allSeriesSql = "SELECT * FROM series WHERE added_by = " . $loggedInUser['id'];
$allSeriesResult = $connect->query($allSeriesSql);

// Fermeture de la connexion à la base de données
$connect->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Recherche de séries</title>
    <link rel="stylesheet" type="text/css" href="./series.css">
    <script>
        function showEditForm(seriesId, title, langueSerie, completeSeason, episodeCount, seasonNumber, externalHardDrive) {
            document.getElementById('series_id').value = seriesId;
            document.getElementById('edit-title').value = title;
            document.getElementById('edit-langue-serie').value = langueSerie;
            document.getElementById('edit-complete-season').checked = completeSeason === '1';
            document.getElementById('edit-episode-count').value = episodeCount;
            document.getElementById('edit-season-number').value = seasonNumber;
            document.getElementById('edit-external-hard-drive').value = externalHardDrive;
            document.getElementById('edit-form-container').style.display = 'block';
        }
    </script>
</head>
<body>
    <div class="navbar">
        <a href="../accueil/index.php">Accueil</a>
        <a href="./series.php">Ajouter une Série</a>
        <a href="./series_search.php">Consulter les Séries</a>
    </div>

    <h1>Recherche de séries</h1>

    <div class="alert-container">
        <?php echo isset($deleteAlert) ? $deleteAlert : ''; ?>
        <?php echo isset($updateAlert) ? $updateAlert : ''; ?>
    </div>

    <div class="container_search">
        <div class="search-bar">
            <form method="GET">
                <input type="text" name="search" placeholder="Rechercher une série" style="width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                <input type="submit" value="Rechercher" style="padding: 10px 20px; background-color: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer;">
            </form>
        </div>
        <h2>Résultats de la recherche</h2>
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
            <?php if ($searchResult->num_rows > 0) : ?>
                <?php while ($row = $searchResult->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo $row['title']; ?></td>
                        <td><?php echo $row['langue_serie']; ?></td>
                        <td><?php echo $row['complete_season'] ? 'Oui' : 'Non'; ?></td>
                        <td><?php echo $row['episode_count']; ?></td>
                        <td><?php echo $row['season_number']; ?></td>
                        <td><?php echo $row['external_hard_drive']; ?></td>
                        <td>
                            <form method="POST" action="series_search.php" style="display: inline;">
                                <input type="hidden" name="delete" value="<?php echo $row['id']; ?>">
                                <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette série ?')">Supprimer</button>
                            </form>
                            <button onclick="showEditForm(<?php echo $row['id']; ?>, '<?php echo $row['title']; ?>', '<?php echo $row['langue_serie']; ?>', '<?php echo $row['complete_season']; ?>', <?php echo $row['episode_count']; ?>, <?php echo $row['season_number']; ?>, '<?php echo $row['external_hard_drive']; ?>')">Modifier</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else : ?>
                <tr>
                    <td colspan="7">Aucun résultat trouvé.</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>

    <div id="edit-form-container" style="display: none;">
        <h2>Modifier la série</h2>
        <form method="POST" action="series_search.php">
            <input type="hidden" name="series_id" id="series_id">
            <label for="edit-title">Titre :</label>
            <input type="text" name="title" id="edit-title" required>
            <label for="edit-langue-serie">Langue :</label>
            <input type="text" name="langue_serie" id="edit-langue-serie">
            <label for="edit-complete-season">Saison complète :</label>
            <input type="checkbox" name="complete_season" id="edit-complete-season">
            <label for="edit-episode-count">Nombre d'épisodes :</label>
            <input type="number" name="episode_count" id="edit-episode-count" min="0">
            <label for="edit-season-number">Numéro de saison :</label>
            <input type="number" name="season_number" id="edit-season-number" min="0">
            <label for="edit-external-hard-drive">Disque dur externe :</label>
            <input type="text" name="external_hard_drive" id="edit-external-hard-drive">
            <input type="submit" name="update" value="Enregistrer">
        </form>
    </div>
</body>
</html>
