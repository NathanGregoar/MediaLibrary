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

    if ($result_row && mysqli_num_rows($result_row) > 0) {
        $row = mysqli_fetch_assoc($result_row);
        $fetch_fields = mysqli_fetch_fields($result_row);

        // Générer les champs du formulaire de modification avec les valeurs actuelles
        $form_fields = array();
        foreach ($row as $field_name => $field_value) {
            if ($field_name !== 'id') {
                $field_info = mysqli_fetch_field_direct($result_row, array_search($field_name, array_column($fetch_fields, 'name')));
                $field_type = $field_info->type;
                $escaped_value = htmlspecialchars($field_value);

                if (in_array($field_type, [MYSQLI_TYPE_TINY, MYSQLI_TYPE_SHORT, MYSQLI_TYPE_LONG, MYSQLI_TYPE_LONGLONG])) {
                    // Champ de type entier
                    $form_fields[] = '<label>' . $field_name . ':</label><input type="number" name="' . $field_name . '" value="' . $escaped_value . '" required>';
                } else {
                    // Champ de type texte
                    $form_fields[] = '<label>' . $field_name . ':</label><input type="text" name="' . $field_name . '" value="' . $escaped_value . '" required>';
                }
            }
        }

        // Afficher le formulaire de modification
        $edit_form_html = '<form method="post" action="" class="edit-form">' . implode('<br>', $form_fields) . '<input type="hidden" name="row_id" value="' . $row_id . '"><button type="submit" name="update" class="btn-update">Mettre à jour</button></form>';
    }
}

// Traitement de la mise à jour des données
if (isset($_POST['update'])) {
    $row_id = $_POST['row_id'];

    // Récupérer les nouvelles valeurs des champs à partir des données soumises dans le formulaire
    $update_values = array();
    foreach ($_POST as $field_name => $field_value) {
        if ($field_name !== 'row_id' && $field_name !== 'update') {
            $field_value = mysqli_real_escape_string($conn, $field_value);
            $update_values[] = "$field_name = '$field_value'";
        }
    }

    // Mettre à jour les données dans la base de données
    $sql_update = "UPDATE $table_selected SET " . implode(', ', $update_values) . " WHERE id = $row_id";
    if (mysqli_query($conn, $sql_update)) {
        $update_message = "La ligne de données a été mise à jour avec succès.";
        header("Refresh:0"); // Recharger la page
    } else {
        $update_message = "Erreur lors de la mise à jour de la ligne de données. Veuillez réessayer.";
    }
}

ob_end_flush(); // Activer le buffer de sortie
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Gestion des données</title>
    <style>
        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        .table-selector {
            margin-bottom: 20px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #ccc;
            padding: 8px;
        }

        .data-table th {
            background-color: #f2f2f2;
        }

        .edit-form {
            margin-top: 20px;
        }

        .btn-update {
            margin-top: 10px;
        }

        .message {
            margin-top: 20px;
            padding: 10px;
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Gestion des données</h2>
        <form method="post" action="">
            <label for="table">Sélectionner une table :</label>
            <select name="table_selected" id="table">
                <option value="">-- Sélectionner --</option>
                <?php
                foreach ($tables as $table) {
                    $selected = ($table_selected === $table) ? 'selected' : '';
                    echo "<option value='$table' $selected>$table</option>";
                }
                ?>
            </select>
            <button type="submit" name="submit" value="submit">Afficher les données</button>
        </form>

        <?php if (!empty($table_selected)) : ?>
            <h3>Table : <?php echo $table_selected; ?></h3>

            <?php if (!empty($data)) : ?>
                <table class="data-table">
                    <tr>
                        <?php
                        $table_headers = array_keys($data[0]);
                        foreach ($table_headers as $header) {
                            echo "<th>$header</th>";
                        }
                        ?>
                        <th>Actions</th>
                    </tr>
                    <?php foreach ($data as $row) : ?>
                        <tr>
                            <?php foreach ($row as $value) : ?>
                                <td><?php echo $value; ?></td>
                            <?php endforeach; ?>
                            <td>
                                <form method="post" action="">
                                    <input type="hidden" name="row_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="delete">Supprimer</button>
                                    <button type="submit" name="edit">Modifier</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else : ?>
                <p>Aucune donnée disponible pour cette table.</p>
            <?php endif; ?>

            <?php if (isset($delete_message)) : ?>
                <div class="message"><?php echo $delete_message; ?></div>
            <?php endif; ?>

            <?php if (isset($edit_form_html)) : ?>
                <h3>Modifier une ligne de données</h3>
                <?php echo $edit_form_html; ?>
            <?php endif; ?>

            <?php if (isset($update_message)) : ?>
                <div class="message"><?php echo $update_message; ?></div>
            <?php endif; ?>

        <?php endif; ?>
    </div>
</body>

</html>
