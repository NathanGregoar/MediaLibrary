<?php
require_once '../utils/auth.php';
require_once '../utils/config.php';

$username = $_SESSION['username'];
$email = $_SESSION['email'];

// Vérification si l'utilisateur est autorisé à accéder à la page de gestion de la base de données
if ($username !== "Nathan" || $email !== "nathan.gregoar@yahoo.fr") {
    // Redirection vers la page d'accueil
    header("Location: ../accueil/index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestion de la base de données</title>
    <link rel="stylesheet" type="text/css" href="./gestion_db.css">
</head>
<body>
    <div class="navbar">
        <a href="./admin.php">Retour à l'administration</a>
    </div>
    <div class="container">
        <div class="left-section">
        <?php
        // Vérification des actions à effectuer
        if (isset($_POST['action'])) {
            $action = $_POST['action'];

            switch ($action) {
                case 'ajouter_table':
                    $table_name = $_POST['table_name'];

                    // Création de la table
                    $sql = "CREATE TABLE $table_name (
                        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY
                    )";

                    if (mysqli_query($conn, $sql)) {
                        echo "La table $table_name a été ajoutée avec succès.";
                    } else {
                        echo "Erreur lors de la création de la table: " . mysqli_error($conn);
                    }
                    break;

                case 'supprimer_table':
                    $table_name = $_POST['table_name'];

                    // Suppression de la table
                    $sql = "DROP TABLE $table_name";

                    if (mysqli_query($conn, $sql)) {
                        echo "La table $table_name a été supprimée avec succès.";
                    } else {
                        echo "Erreur lors de la suppression de la table: " . mysqli_error($conn);
                    }
                    break;

                case 'ajouter_colonne':
                    $table_name = $_POST['table_name'];
                    $colonne_name = $_POST['colonne_name'];
                    $colonne_type = $_POST['colonne_type'];

                    // Ajout de la colonne dans la table
                    $sql = "ALTER TABLE $table_name ADD COLUMN $colonne_name $colonne_type";

                    if (mysqli_query($conn, $sql)) {
                        echo "La colonne $colonne_name a été ajoutée avec succès à la table $table_name.";
                    } else {
                        echo "Erreur lors de l'ajout de la colonne: " . mysqli_error($conn);
                    }
                    break;

                case 'supprimer_colonne':
                    $table_name = $_POST['table_name'];
                    $colonne_name = $_POST['colonne_name'];

                    // Suppression de la colonne de la table
                    $sql = "ALTER TABLE $table_name DROP COLUMN $colonne_name";

                    if (mysqli_query($conn, $sql)) {
                        echo "La colonne $colonne_name a été supprimée avec succès de la table $table_name.";
                    } else {
                        echo "Erreur lors de la suppression de la colonne: " . mysqli_error($conn);
                    }
                    break;

                case 'modifier_colonne':
                    $table_name = $_POST['table_name'];
                    $colonne_name = $_POST['colonne_name'];
                    $nouveau_nom = $_POST['nouveau_nom'];
                    $nouveau_type = $_POST['nouveau_type'];

                    // Modification de la colonne dans la table
                    $sql = "ALTER TABLE $table_name CHANGE COLUMN $colonne_name $nouveau_nom $nouveau_type";

                    if (mysqli_query($conn, $sql)) {
                        echo "La colonne $colonne_name a été modifiée avec succès dans la table $table_name.";
                    } else {
                        echo "Erreur lors de la modification de la colonne: " . mysqli_error($conn);
                    }
                    break;

                default:
                    echo "Action non valide.";
                    break;
            }
        }
        ?>

        <!-- Formulaire d'ajout de table -->
        <h2>Ajouter une table</h2>
        <form method="post" action="">
            <input type="hidden" name="action" value="ajouter_table">
            <label for="table_name">Nom de la table:</label>
            <input type="text" name="table_name" required>
            <input type="submit" value="Ajouter">
        </form>

        <!-- Formulaire de suppression de table -->
        <h2>Supprimer une table</h2>
        <form method="post" action="">
            <input type="hidden" name="action" value="supprimer_table">
            <label for="table_name">Nom de la table:</label>
            <input type="text" name="table_name" required>
            <input type="submit" value="Supprimer">
        </form>

        <!-- Formulaire d'ajout de colonne -->
        <h2>Ajouter une colonne</h2>
        <form method="post" action="">
            <input type="hidden" name="action" value="ajouter_colonne">
            <label for="table_name">Table:</label>
            <input type="text" name="table_name" required>
            <label for="colonne_name">Nom de la colonne:</label>
            <input type="text" name="colonne_name" required>
            <label for="colonne_type">Type de la colonne:</label>
            <select name="colonne_type">
                <option value="INT(6)">INT(6)</option>
                <option value="VARCHAR(255)" selected>VARCHAR(255)</option>
                <option value="DATE">DATE</option>
                <!-- Ajoutez d'autres options de type de colonne si nécessaire -->
            </select>
            <input type="submit" value="Ajouter">
        </form>

        <!-- Formulaire de suppression de colonne -->
        <h2>Supprimer une colonne</h2>
        <form method="post" action="">
            <input type="hidden" name="action" value="supprimer_colonne">
            <label for="table_name">Table:</label>
            <input type="text" name="table_name" required>
            <label for="colonne_name">Nom de la colonne:</label>
            <input type="text" name="colonne_name" required>
            <input type="submit" value="Supprimer">
        </form>

        <!-- Formulaire de modification de colonne -->
        <h2>Modifier une colonne</h2>
        <form method="post" action="">
            <input type="hidden" name="action" value="modifier_colonne">
            <label for="table_name">Table:</label>
            <input type="text" name="table_name" required>
            <label for="colonne_name">Nom de la colonne:</label>
            <input type="text" name="colonne_name" required>
            <label for="nouveau_nom">Nouveau nom:</label>
            <input type="text" name="nouveau_nom" required>
            <label for="nouveau_type">Nouveau type:</label>
            <select name="nouveau_type">
                <option value="INT(6)">INT(6)</option>
                <option value="VARCHAR(255)" selected>VARCHAR(255)</option>
                <option value="DATE">DATE</option>
                <!-- Ajoutez d'autres options de type de colonne si nécessaire -->
            </select>
            <input type="submit" value="Modifier">
        </form>
    </div>
    <div class="right-section">
        <!-- Affichage de la structure de la base de données -->
        <h2>Structure de la base de données</h2>
        <?php
        $sql = "SHOW TABLES";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $table = $row['Tables_in_' . $dbName];
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
        } else {
            echo "Aucune table trouvée dans la base de données.";
        }

        // Fermeture de la connexion à la base de données
        mysqli_close($conn);
        ?>
    </div>
</div>
</body>
</html>
