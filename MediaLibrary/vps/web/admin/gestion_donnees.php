<?php
ob_start(); // Désactiver le buffer de sortie

require_once '../utils/auth.php';
require_once '../utils/config.php';

$username = $_SESSION['username'];
$email = $_SESSION['email'];

$table_selected = isset($_GET['table_selected']) ? $_GET['table_selected'] : $table_selected;

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
if (isset($_POST['confirm_delete'])) {
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

    if ($result_row && mysqli_num_rows($result_row) > 0) {
        $row = mysqli_fetch_assoc($result_row);
        $fetch_fields = $result_row->fetch_fields();

        // Générer les champs du formulaire de modification avec les valeurs actuelles
        $form_fields = array();
        $field_info = array_column($fetch_fields, null, 'name'); // Récupérer les informations des champs dans un tableau associatif
        foreach ($row as $field_name => $field_value) {
            if ($field_name !== 'id' && $field_name !== 'added_by') {
                $field_type = $field_info[$field_name]->type;
                $escaped_value = htmlspecialchars($field_value);

                if (in_array($field_type, [MYSQLI_TYPE_TINY, MYSQLI_TYPE_SHORT, MYSQLI_TYPE_LONG, MYSQLI_TYPE_LONGLONG])) {
                    // Champ de type entier
                    $form_fields[] = '<label>' . $field_name . ':</label><input type="number" name="' . $field_name . '" value="' . $escaped_value . '" required>';
                } elseif (in_array($field_type, [MYSQLI_TYPE_FLOAT, MYSQLI_TYPE_DOUBLE, MYSQLI_TYPE_DECIMAL])) {
                    // Champ de type décimal
                    $form_fields[] = '<label>' . $field_name . ':</label><input type="number" step="0.01" name="' . $field_name . '" value="' . $escaped_value . '" required>';
                } else {
                    // Autres types de champ (chaîne, date, etc.)
                    $form_fields[] = '<label>' . $field_name . ':</label><input type="text" name="' . $field_name . '" value="' . $escaped_value . '" required>';
                }
            }
        }

        // Affichage du formulaire de modification
        $edit_form_html = '<form action="" method="post" class="edit-form">';
        $edit_form_html .= implode('<br>', $form_fields);
        $edit_form_html .= '<input type="hidden" name="table_selected" value="' . $table_selected . '">';
        $edit_form_html .= '<input type="hidden" name="row_id" value="' . $row_id . '">';
        $edit_form_html .= '<input type="submit" name="save" value="Enregistrer">';
        $edit_form_html .= '</form>';
    } else {
        $edit_form_html = "Erreur lors de la récupération des données de la ligne à modifier.";
    }
}

// Enregistrement des modifications
if (isset($_POST['save'])) {
    $row_id = $_POST['row_id'];

    // Récupérer les valeurs modifiées à partir du formulaire
    $updated_values = array();
    foreach ($_POST as $field_name => $field_value) {
        if ($field_name !== 'table_selected' && $field_name !== 'row_id' && $field_name !== 'save') {
            $field_value = mysqli_real_escape_string($conn, $field_value);
            $updated_values[] = "$field_name = '$field_value'";
        }
    }

    // Générer la requête de mise à jour
    $sql_update = "UPDATE $table_selected SET " . implode(', ', $updated_values) . " WHERE id = $row_id";

    if (mysqli_query($conn, $sql_update)) {
        $update_message = "Les modifications ont été enregistrées avec succès.";
        header("Refresh:0; url=./gestion_donnees.php?table_selected=" . urlencode($table_selected));
        exit();
    } else {
        $update_message = "Erreur lors de l'enregistrement des modifications. Veuillez réessayer.";
    }    
}

ob_end_flush(); // Activer à nouveau le buffer de sortie
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
                                    <button type="button" class="btn-delete" data-row-id="<?php echo $row['id']; ?>">Supprimer</button>
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const deleteButtons = document.querySelectorAll('.btn-delete');

    deleteButtons.forEach(function (button) {
        button.addEventListener('click', function (event) {
            const rowId = this.getAttribute('data-row-id');
            const confirmDelete = window.confirm("Êtes-vous sûr de vouloir supprimer cette donnée ?");

            if (confirmDelete) {
                // L'utilisateur a confirmé la suppression, envoi de la demande de suppression au serveur
                const form = document.createElement('form');
                form.method = 'post';
                form.action = ''; // Votre URL de suppression ici
                form.style.display = 'none';

                const inputTable = document.createElement('input');
                inputTable.type = 'hidden';
                inputTable.name = 'table_selected';
                inputTable.value = '<?php echo $table_selected; ?>';

                const inputRowId = document.createElement('input');
                inputRowId.type = 'hidden';
                inputRowId.name = 'row_id';
                inputRowId.value = rowId;

                const inputConfirmDelete = document.createElement('input');
                inputConfirmDelete.type = 'hidden';
                inputConfirmDelete.name = 'confirm_delete';
                inputConfirmDelete.value = '1';

                form.appendChild(inputTable);
                form.appendChild(inputRowId);
                form.appendChild(inputConfirmDelete);
                document.body.appendChild(form);
                form.submit();
            }
        });
    });
});
</script>
</body>
</html>
