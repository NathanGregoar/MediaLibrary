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
    <div class="left-section">
        <h2>Gérer les données</h2>
        <form method="post" action="">
            <label for="table_selected">Table:</label>
            <select name="table_selected">
                <?php foreach ($tables as $table) { ?>
                    <option value="<?php echo $table; ?>" <?php if ($table === $table_selected) echo 'selected'; ?>><?php echo $table; ?></option>
                <?php } ?>
            </select>
            <input type="submit" value="Afficher">
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
                    </tr>
                    <?php foreach ($data as $row) { ?>
                        <tr>
                            <?php foreach ($row as $value) { ?>
                                <td><?php echo $value; ?></td>
                            <?php } ?>
                        </tr>
                    <?php } ?>
                </table>
            <?php } ?>
        <?php } ?>
    </div>
    <div class="right-section">
        <h2>Structure de la base de données</h2>
        <?php
        foreach ($tables as $table) {
            echo "<h3>$table</h3>";
            echo "<ul>";

            $sql_columns = "SHOW COLUMNS FROM $table";
            $result_columns = mysqli_query($conn, $sql_columns);

            if (mysqli_num_rows($result_columns) > 0) {
                while ($row_columns = mysqli_fetch_assoc($result_columns)) {
                    echo "<li>" . $row_columns['Field'] . " (" . $row_columns['Type'] . ")</li>";
                }
            } else {
                echo "<li>Aucune colonne trouvée.</li>";
            }

            echo "</ul>";
        }

        // Fermeture de la connexion à la base de données
        mysqli_close($conn);
        ?>
    </div>
</div>
</body>
</html>
