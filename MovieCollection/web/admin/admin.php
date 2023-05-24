<!DOCTYPE html>
<html>
<head>
    <title>Admin</title>
</head>
<body>
    <?php
    // Configuration de la base de données
    $servername = "db";
    $username = "nathan";
    $password = "444719";
    $database = "movie_collection";

    // Connexion à la base de données
    $conn = new mysqli($servername, $username, $password, $database);

    // Vérification de la connexion
    if ($conn->connect_error) {
        die("Erreur de connexion à la base de données: " . $conn->connect_error);
    }

    // Vérification des actions à effectuer
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        switch ($action) {
            case 'ajouter_table':
                $table_name = $_POST['table_name'];

                // Création de la table
                $sql = "CREATE TABLE $table_name (
                    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    nom VARCHAR(30) NOT NULL,
                    email VARCHAR(50),
                    telephone VARCHAR(15)
                )";

                if ($conn->query($sql) === TRUE) {
                    echo "La table $table_name a été ajoutée avec succès.";
                } else {
                    echo "Erreur lors de la création de la table: " . $conn->error;
                }
                break;

            case 'supprimer_table':
                $table_name = $_POST['table_name'];

                // Suppression de la table
                $sql = "DROP TABLE $table_name";

                if ($conn->query($sql) === TRUE) {
                    echo "La table $table_name a été supprimée avec succès.";
                } else {
                    echo "Erreur lors de la suppression de la table: " . $conn->error;
                }
                break;

            case 'ajouter_element':
                $table_name = $_POST['table_name'];
                $champ_name = $_POST['champ_name'];
                $valeur = $_POST['valeur'];

                // Ajout de l'élément dans la table
                $sql = "INSERT INTO $table_name ($champ_name) VALUES ('$valeur')";

                if ($conn->query($sql) === TRUE) {
                    echo "L'élément a été ajouté avec succès dans la table $table_name.";
                } else {
                    echo "Erreur lors de l'ajout de l'élément: " . $conn->error;
                }
                break;

            case 'modifier_element':
                $table_name = $_POST['table_name'];
                $id = $_POST['id'];
                $champ_name = $_POST['champ_name'];
                $valeur = $_POST['valeur'];

                // Mise à jour de l'élément dans la table
                $sql = "UPDATE $table_name SET $champ_name = '$valeur' WHERE id = $id";

                if ($conn->query($sql) === TRUE) {
                    echo "L'élément a été modifié avec succès dans la table $table_name.";
                } else {
                    echo "Erreur lors de la modification de l'élément: " . $conn->error;
                }
                break;

            case 'supprimer_element':
                $table_name = $_POST['table_name'];
                $id = $_POST['id'];

                // Suppression de l'élément de la table
                $sql = "DELETE FROM $table_name WHERE id = $id";

                if ($conn->query($sql) === TRUE) {
                    echo "L'élément a été supprimé avec succès de la table $table_name.";
                } else {
                    echo "Erreur lors de la suppression de l'élément: " . $conn->error;
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

    <!-- Formulaire d'ajout d'élément -->
    <h2>Ajouter un élément</h2>
    <form method="post" action="">
        <input type="hidden" name="action" value="ajouter_element">
        <label for="table_name">Table:</label>
        <input type="text" name="table_name" required>
        <label for="champ_name">Champ:</label>
        <input type="text" name="champ_name" required>
        <label for="valeur">Valeur:</label>
        <input type="text" name="valeur" required>
        <input type="submit" value="Ajouter">
    </form>

    <!-- Formulaire de modification d'élément -->
    <h2>Modifier un élément</h2>
    <form method="post" action="">
        <input type="hidden" name="action" value="modifier_element">
        <label for="table_name">Table:</label>
        <input type="text" name="table_name" required>
        <label for="id">ID:</label>
        <input type="text" name="id" required>
        <label for="champ_name">Champ:</label>
        <input type="text" name="champ_name" required>
        <label for="valeur">Nouvelle valeur:</label>
        <input type="text" name="valeur" required>
        <input type="submit" value="Modifier">
    </form>

    <!-- Formulaire de suppression d'élément -->
    <h2>Supprimer un élément</h2>
    <form method="post" action="">
        <input type="hidden" name="action" value="supprimer_element">
        <label for="table_name">Table:</label>
        <input type="text" name="table_name" required>
        <label for="id">ID:</label>
        <input type="text" name="id" required>
        <input type="submit" value="Supprimer">
    </form>

    <!-- Affichage de la structure de la base de données -->
    <h2>Structure de la base de données</h2>
    <?php
    $sql = "SHOW TABLES";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $table = $row['Tables_in_' . $database];
            echo "<h3>$table</h3>";
            echo "<ul>";

            $sql_columns = "SHOW COLUMNS FROM $table";
            $result_columns = $conn->query($sql_columns);

            if ($result_columns->num_rows > 0) {
                while ($row_columns = $result_columns->fetch_assoc()) {
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
    $conn->close();
    ?>
</body>
</html>
