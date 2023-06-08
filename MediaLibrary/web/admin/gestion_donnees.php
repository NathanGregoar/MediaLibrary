<?php
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

// Ajout d'une nouvelle donnée dans la table sélectionnée
if (isset($_POST['add_data'])) {
    $table_selected = $_POST['table_selected'];
    $columns = array();

    // Récupération des colonnes de la table
    $sql_columns = "SHOW COLUMNS FROM $table_selected";
    $result_columns = mysqli_query($conn, $sql_columns);

    if (mysqli_num_rows($result_columns) > 0) {
        while ($row_columns = mysqli_fetch_assoc($result_columns)) {
            $columns[] = $row_columns['Field'];
        }
    }

    $values = array();

    // Récupération des valeurs des colonnes soumises dans le formulaire
    foreach ($columns as $column) {
        $value = $_POST[$column];
        $values[] = "'" . mysqli_real_escape_string($conn, $value) . "'";
    }

    // Insertion des nouvelles données dans la table
    $sql_insert = "INSERT INTO $table_selected (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ")";

    if (mysqli_query($conn, $sql_insert)) {
        echo '<div class="alert success">La nouvelle donnée a été ajoutée avec succès.</div>';
    } else {
        echo '<div class="alert error">Erreur lors de l\'ajout de la donnée: ' . mysqli_error($conn) . '</div>';
    }
}

// Suppression d'une donnée de la table sélectionnée
if (isset($_POST['delete_data'])) {
    $table_selected = $_POST['table_selected'];
    $id = $_POST['delete_id'];

    // Suppression de la donnée de la table
    $sql_delete = "DELETE FROM $table_selected WHERE id = '$id'";

    if (mysqli_query($conn, $sql_delete)) {
        echo '<div class="alert success">La donnée a été supprimée avec succès.</div>';
    } else {
        echo '<div class="alert error">Erreur lors de la suppression de la donnée: ' . mysqli_error($conn) . '</div>';
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

// Fermeture de la connexion à la base de données
mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Gestion des données</title>
    <link rel="stylesheet" type="text/css" href="gestion_donnees.css">
</head>
<body>
<div class="container">
    <div class="sidebar">
        <h2>Tables</h2>
        <ul>
            <?php
            foreach ($tables as $table) {
                echo '<li><a href="#" onclick="selectTable(\'' . $table . '\')">' . $table . '</a></li>';
            }
            ?>
        </ul>
    </div>
    <div class="content">
        <?php if (!empty($table_selected)) { ?>
            <h2>Gérer les données de <?php echo $table_selected; ?></h2>
            <form method="post" class="add-data-form">
                <?php
                // Affichage des champs pour ajouter une nouvelle donnée
                foreach ($columns as $column) {
                    echo '<div class="form-group">';
                    echo '<label for="' . $column . '">' . $column . '</label>';
                    echo '<input type="text" name="' . $column . '" id="' . $column . '" required>';
                    echo '</div>';
                }
                ?>
                <input type="hidden" name="table_selected" value="<?php echo $table_selected; ?>">
                <button type="submit" name="add_data">Ajouter</button>
            </form>
            <h2>Liste des données</h2>
            <?php if (!empty($data)) { ?>
                <table>
                    <tr>
                        <?php
                        // Affichage des en-têtes de colonnes
                        foreach ($columns as $column) {
                            echo '<th>' . $column . '</th>';
                        }
                        echo '<th>Action</th>';
                        ?>
                    </tr>
                    <?php foreach ($data as $row) { ?>
                        <tr>
                            <?php
                            // Affichage des valeurs des données
                            foreach ($columns as $column) {
                                echo '<td>' . $row[$column] . '</td>';
                            }
                            ?>
                            <td>
                                <form method="post">
                                    <input type="hidden" name="table_selected"
                                           value="<?php echo $table_selected; ?>">
                                    <input type="hidden" name="delete_id"
                                           value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="delete_data">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            <?php } else { ?>
                <p>Aucune donnée trouvée.</p>
            <?php } ?>
        <?php } else { ?>
            <p>Veuillez sélectionner une table pour afficher les données.</p>
        <?php } ?>
    </div>
</div>
<script>
    function selectTable(table) {
        document.querySelector('input[name="table_selected"]').value = table;
        document.querySelector('.add-data-form').reset();
    }
</script>
</body>
</html>
