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

// Fermeture de la connexion à la base de données
$connection->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Recherche de séries</title>
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1>Recherche de séries</h1>

        <div>
            <?php echo $deleteAlert; ?>
            <?php echo $updateAlert; ?>
        </div>

        <div>
            <form action="series_search.php" method="GET">
                <input type="text" name="search" placeholder="Rechercher une série" value="<?php echo $searchTerm; ?>">
                <button type="submit" class="btn btn-primary">Rechercher</button>
            </form>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Langue de la série</th>
                    <th>Saison complète</th>
                    <th>Nombre d'épisodes</th>
                    <th>Numéro de saison</th>
                    <th>Disque dur externe</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $searchResult->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo $row['title']; ?></td>
                        <td><?php echo $row['langue_serie']; ?></td>
                        <td><?php echo $row['complete_season'] ? 'Oui' : 'Non'; ?></td>
                        <td><?php echo $row['episode_count']; ?></td>
                        <td><?php echo $row['season_number']; ?></td>
                        <td><?php echo $row['external_hard_drive']; ?></td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="delete" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="btn btn-danger">Supprimer</button>
                            </form>
                            <form method="POST">
                                <input type="hidden" name="edit" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="btn btn-primary">Modifier</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <?php if ($editFormVisible) : ?>
            <h2>Modifier la série</h2>
            <form method="POST">
                <input type="hidden" name="series_id" value="<?php echo $editId; ?>">
                <div class="form-group">
                    <label>Titre :</label>
                    <input type="text" name="title" class="form-control" value="<?php echo $editTitle; ?>" required>
                </div>
                <div class="form-group">
                    <label>Langue de la série :</label>
                    <input type="text" name="langue_serie" class="form-control" value="<?php echo $editLangueSerie; ?>" required>
                </div>
                <div class="form-group">
                    <label>Saison complète :</label>
                    <input type="checkbox" name="complete_season" <?php echo $editCompleteSeason ? 'checked' : ''; ?>>
                </div>
                <div class="form-group">
                    <label>Nombre d'épisodes :</label>
                    <input type="number" name="episode_count" class="form-control" value="<?php echo $editEpisodeCount; ?>" required>
                </div>
                <div class="form-group">
                    <label>Numéro de saison :</label>
                    <input type="number" name="season_number" class="form-control" value="<?php echo $editSeasonNumber; ?>" required>
                </div>
                <div class="form-group">
                    <label>Disque dur externe :</label>
                    <input type="text" name="external_hard_drive" class="form-control" value="<?php echo $editExternalHardDrive; ?>" required>
                </div>
                <button type="submit" name="update" class="btn btn-primary">Mettre à jour</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
