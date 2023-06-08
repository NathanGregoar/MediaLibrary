<?php
ob_start(); // Désactiver le buffer de sortie

require_once '../utils/auth.php';
require_once '../utils/config.php';

$username = $_SESSION['username'];
$email = $_SESSION['email'];

// Vérification si l'utilisateur est autorisé à accéder à la page de gestion des données
if ($username !== "Nathan" || $email !== "nathan.gregoar@yahoo.fr") {
    // Redirection vers la page d'accueil
    header("Location: ../accueil/index.php");
    exit();
}

// Récupération de la liste des tables de la base de données
$tables = array();
$sql_tables = "SHOW TABLES";
$result_tables = mysqli_query($conn, $sql_tables);

if (mysqli_num_rows($result_tables) > 0) {
    while ($row_tables = mysqli_fetch_assoc($result_tables)) {
        $tables[] = $row_tables['Tables_in_' . $dbName];
    }
}

// Récupération des données de la table sélectionnée
$table_selected = isset($_POST['table_selected']) ? $_POST['table_selected'] : '';

$data = array();
if (!empty($table_selected)) {
    $sql_data = "SELECT * FROM $table_selected";
    $result_data = mysqli_query($conn, $sql_data);

    if (mysqli_num_rows($result_data) > 0) {
        while ($row_data = mysqli_fetch_assoc($result_data)) {
            $data[] = $row_data;
        }
    }
}

// Suppression d'une ligne de données
if (isset($_POST['delete'])) {
    $row_id = $_POST['row_id'];
    $sql_delete = "DELETE FROM $table_selected WHERE id = $row_id";
    if (mysqli_query($conn, $sql_delete)) {
        $delete_message = "La ligne de données a été supprimée avec succès.";
        header("Refresh:0"); // Recharger la page
    } else {
        $delete_message = "Erreur lors de la suppression de la ligne de données. Veuillez réessayer.";
    }
}

// Formulaire de modification
if (isset($_POST['edit'])) {
    $row_id = $_POST['row_id'];

    // Récupérer les données de la ligne à modifier à partir de la base de données
    $sql_row = "SELECT * FROM $table_selected WHERE id = $row_id";
    $result_row = mysqli_query($conn, $sql_row);
    $row = mysqli_fetch_assoc($result_row);

    // Générer les champs du formulaire de modification avec les valeurs actuelles
    $form_fields = array();
    foreach ($row as $field_name => $field_value) {
        if ($field_name !== 'id') {
            $field_type = mysqli_fetch_field_direct($result_row, array_search($field_name, array_column($result_row->fetch_fields(), 'name')))->type;

            if (in_array($field_type, [MYSQLI_TYPE_TINY, MYSQLI_TYPE_SHORT, MYSQLI_TYPE_LONG, MYSQLI_TYPE_LONGLONG])) {
                // Champ de type entier
                $form_fields[] = '<label>' . $field_name . ':</label><input type="number" name="' . $field_name . '" value="' . $field_value . '" required>';
            } else {
                // Champ de type texte
                $form_fields[] = '<label>' . $field_name . ':</label><input type="text" name="' . $field_name . '" value="' . $field_value . '" required>';
            }
        }
    }

    // Afficher le formulaire de modification
    $edit_form_html = '<form method="post" action="" class="edit-form">' . implode('<br>', $form_fields) . '<input type="hidden" name="row_id" value="' . $row_id . '"><button type="submit" name="update" class="btn-update">Mettre à jour</button></form>';
}

// Mise à jour des données
if (isset($_POST['update'])) {
    $row_id = $_POST['row_id'];

    // Construction de la requête de mise à jour en utilisant les valeurs des champs du formulaire
    $update_values = array();
    foreach ($_POST as $field_name => $field_value) {
        if ($field_name !== 'row_id' && $field_name !== 'update') {
            $field_type = mysqli_fetch_field_direct($result_row, array_search($field_name, array_column($result_row->fetch_fields(), 'name')))->type;
            $escaped_value = mysqli_real_escape_string($conn, $field_value);

            if (in_array($field_type, [MYSQLI_TYPE_TINY, MYSQLI_TYPE_SHORT, MYSQLI_TYPE_LONG, MYSQLI_TYPE_LONGLONG])) {
                // Champ de type entier
                $update_values[] = $field_name . ' = ' . $escaped_value;
            } else {
                // Champ de type texte
                $update_values[] = $field_name . ' = "' . $escaped_value . '"';
            }
        }
    }
    $sql_update = "UPDATE $table_selected SET " . implode(', ', $update_values) . " WHERE id = $row_id";
    if (mysqli_query($conn, $sql_update)) {
        $update_message = "La ligne de données a été mise à jour avec succès.";
        header("Refresh:0"); // Recharger la page
    } else {
        $update_message = "Erreur lors de la mise à jour de la ligne de données. Veuillez réessayer.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestion des données</title>
    <link rel="stylesheet" type="text/css" href="./gestion_donnees.css">
</head>
<body>
<div class="navbar">
    <a href="./admin.php">Retour à l'administration</a>
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
</div>
<?php if (isset($delete_message)) { ?>
    <div class="alert success"><?php echo $delete_message; ?></div>
<?php } ?>
<?php if (isset($edit_form_html)) { ?>
    <div class="edit-form-container">
        <h2>Modifier la ligne de données</h2>
        <?php echo $edit_form_html; ?>
    </div>
<?php } ?>
<?php if (isset($update_message)) { ?>
    <div class="alert success"><?php echo $update_message; ?></div>
<?php } ?>
</body>
</html>
