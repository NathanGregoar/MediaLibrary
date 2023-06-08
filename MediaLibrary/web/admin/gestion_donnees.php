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
        echo '<h3>Modifier la ligne de données</h3>';
        echo '<form method="post" action="">';
        echo implode('<br>', $form_fields);
        echo '<input type="hidden" name="row_id" value="' . $row_id . '">';
        echo '<input type="submit" name="update" value="Enregistrer les modifications">';
        echo '</form>';
    } else {
        echo 'La ligne de données sélectionnée n\'existe pas.';
    }
}

// Enregistrement des modifications
if (isset($_POST['update'])) {
    $row_id = $_POST['row_id'];

    // Construire la requête de mise à jour avec les valeurs des champs modifiés
    $sql_update = "UPDATE $table_selected SET ";
    $update_fields = array();
    foreach ($_POST as $field_name => $field_value) {
        if ($field_name !== 'row_id' && $field_name !== 'update') {
            $field_value = mysqli_real_escape_string($conn, $field_value);
            $update_fields[] = $field_name . " = '" . $field_value . "'";
        }
    }
    $sql_update .= implode(', ', $update_fields);
    $sql_update .= " WHERE id = $row_id";

    if (mysqli_query($conn, $sql_update)) {
        $update_message = "Les modifications ont été enregistrées avec succès.";
        header("Refresh:0"); // Recharger la page
    } else {
        $update_message = "Erreur lors de l'enregistrement des modifications. Veuillez réessayer.";
    }
}

ob_end_flush(); // Activer le buffer de sortie
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Gestion des données</title>

</head>
<body>
    <h1>Gestion des données</h1>
    
    <!-- Affichage du message de suppression -->
    <?php if (isset($delete_message)) { ?>
        <p class="error"><?php echo $delete_message; ?></p>
    <?php } ?>

    <!-- Affichage du message de mise à jour -->
    <?php if (isset($update_message)) { ?>
        <p class="success"><?php echo $update_message; ?></p>
    <?php } ?>

    <!-- Sélection de la table -->
    <form method="post" action="">
        <label for="table_selected">Sélectionner une table :</label>
        <select id="table_selected" name="table_selected">
            <?php foreach ($tables as $table) { ?>
                <option value="<?php echo $table; ?>" <?php if ($table_selected === $table) echo 'selected'; ?>><?php echo $table; ?></option>
            <?php } ?>
        </select>
        <input type="submit" value="Afficher les données">
    </form>

    <!-- Affichage des données de la table sélectionnée -->
    <?php if (!empty($data)) { ?>
        <h2>Table : <?php echo $table_selected; ?></h2>
        <table>
            <thead>
                <tr>
                    <?php foreach ($data[0] as $column_name => $column_value) { ?>
                        <th><?php echo $column_name; ?></th>
                    <?php } ?>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $row) { ?>
                    <tr>
                        <?php foreach ($row as $field_name => $field_value) { ?>
                            <td><?php echo $field_value; ?></td>
                        <?php } ?>
                        <td>
                            <form method="post" action="">
                                <input type="hidden" name="row_id" value="<?php echo $row['id']; ?>">
                                <input type="submit" name="edit" value="Modifier">
                                <input type="submit" name="delete" value="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette ligne de données ?')">
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } ?>
</body>
</html>
