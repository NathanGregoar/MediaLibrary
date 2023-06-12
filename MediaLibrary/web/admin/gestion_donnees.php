<?php
ob_start(); // Désactiver le buffer de sortie

require_once '../utils/auth.php';
require_once '../utils/config.php';

// Récupérer l'utilisateur connecté
$loggedInUser = getLoggedInUser();

// Définition des variables de recherche
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$searchSql = "SELECT * FROM films WHERE title LIKE '%$searchTerm%' AND added_by = " . $loggedInUser['id'];

// Connexion à la base de données
$connection = mysqli_connect($host, $username, $password, $dbName);
if (!$connection) {
    die('Erreur de connexion à la base de données : ' . mysqli_connect_error());
}

// Suppression d'un film
if (isset($_POST['delete'])) {
    $deleteId = $connection->real_escape_string($_POST['delete']);
    $deleteSql = "DELETE FROM films WHERE id = $deleteId AND added_by = " . $loggedInUser['id'];

    if ($connection->query($deleteSql) === TRUE) {
        $deleteAlert = '<div class="alert alert-success">Film supprimé avec succès !</div>';
    } else {
        $deleteAlert = '<div class="alert alert-error">Erreur lors de la suppression du film : ' . $connection->error . '</div>';
    }
}

// Modification d'un film
if (isset($_POST['edit'])) {
    $editId = $connection->real_escape_string($_POST['edit']);
    $editSql = "SELECT * FROM films WHERE id = $editId AND added_by = " . $loggedInUser['id'];
    $editResult = $connection->query($editSql);

    if ($editResult->num_rows === 1) {
        $editRow = $editResult->fetch_assoc();
        $editTitle = $editRow['title'];
        $editDirector = $editRow['director'];
        $editReleaseYear = $editRow['release_year'];
        $editExternalHardDrive = $editRow['external_hard_drive'];

        // Display the edit form at the bottom of the page
        echo '<div class="edit-form">
                <h2>Modifier le film :</h2>
                <form method="POST" action="update_movie.php">
                    <input type="hidden" name="edit_id" value="' . $editId . '">
                    <label for="edit_title">Titre :</label>
                    <input type="text" name="edit_title" id="edit_title" value="' . $editTitle . '">

                    <label for="edit_director">Réalisateur :</label>
                    <input type="text" name="edit_director" id="edit_director" value="' . $editDirector . '">

                    <label for="edit_release_year">Année de sortie :</label>
                    <input type="text" name="edit_release_year" id="edit_release_year" value="' . $editReleaseYear . '">

                    <label for="edit_external_hard_drive">Disque dur externe :</label>
                    <input type="text" name="edit_external_hard_drive" id="edit_external_hard_drive" value="' . $editExternalHardDrive . '">

                    <input type="submit" value="Modifier le film" class="edit-btn">
                </form>
            </div>';
    } else {
        $editAlert = '<div class="alert alert-error">Erreur lors de la récupération des informations du film à modifier.</div>';
    }
}

// Récupération des films correspondant à la recherche
$searchResult = $connection->query($searchSql);

// Buffer de sortie
ob_end_flush();
?>



<!DOCTYPE html>
<html>
<head>
    <title>Gestion des données</title>
    <link rel="stylesheet" type="text/css" href="./gestion_donnees.css">
</head>
<body>
<div class="navbar">
    <a href="../accueil/index.php">Accueil</a>
    <a href="./admin.php">Accueil admin</a>
    <a href="./gestion_db.php">Gestion DB</a>
    <a href="./gestion_donnees.php" style="color: #ff0000;">Gestion données</a>
</div>
<div class="container">
    <div class="content">
        <h2>Gérer les données</h2>
        <form method="post" action="" class="form-container">
            <label for="table_selected">Table:</label>
            <select name="table_selected" onchange="this.form.submit()">
                <option value="">Sélectionner une table</option>
                <?php foreach ($tables as $table) { ?>
                    <option value="<?php echo $table; ?>" <?php if ($table === $table_selected) echo 'selected'; ?>><?php echo $table; ?></option>
                <?php } ?>
            </select>
        </form>

        <?php if (!empty($table_selected)) { ?>
            <h2>Données de la table "<?php echo $table_selected; ?>"</h2>
            <?php if (empty($data)) { ?>
                <p>Aucune donnée trouvée.</p>
            <?php } else { ?>
                <table>
                    <tr>
                        <?php foreach (array_keys($data[0]) as $column_name) { ?>
                            <th><?php echo $column_name; ?></th>
                        <?php } ?>
                        <th>Actions</th>
                    </tr>
                    <?php foreach ($data as $row) { ?>
                        <tr>
                            <?php foreach ($row as $value) { ?>
                                <td><?php echo $value; ?></td>
                            <?php } ?>
                            <td>
                                <form method="post" action="" class="data-actions">
                                    <input type="hidden" name="table_selected" value="<?php echo $table_selected; ?>">
                                    <input type="hidden" name="row_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="edit" class="btn-edit">Modifier</button>
                                    <button type="submit" name="delete" class="btn-delete">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            <?php } ?>
        <?php } ?>
    </div>

    <?php if (isset($edit_form_html)) { ?>
        <div class="edit-form-container">
            <h2>Modifier la ligne de données</h2>
            <?php echo $edit_form_html; ?>
        </div>
    <?php } ?>

</div>
<?php if (isset($delete_message)) { ?>
    <div class="alert success"><?php echo $delete_message; ?></div>
<?php } ?>
<?php if (isset($update_message)) { ?>
    <div class="alert success"><?php echo $update_message; ?></div>
<?php } ?>
</body>
</html>
