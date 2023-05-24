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

            case 'ajouter_colonne':
                $table_name = $_POST['table_name'];
                $colonne_name = $_POST['colonne_name'];
                $colonne_type = $_POST['colonne_type'];

                // Ajout de la colonne dans la table
                $sql = "ALTER TABLE $table_name ADD COLUMN $colonne_name $colonne_type";

                if ($conn->query($sql) === TRUE) {
                    echo "La colonne $colonne_name a été ajoutée avec succès à la table $table_name.";
                } else {
                    echo "Erreur lors de l'ajout de la colonne: " . $conn->error;
                }
                break;

            case 'supprimer_colonne':
                $table_name = $_POST['table_name'];
                $colonne_name = $_POST['colonne_name'];

                // Suppression de la colonne de la table
                $sql = "ALTER TABLE $table_name DROP COLUMN $colonne_name";

                if ($conn->query($sql) === TRUE) {
                    echo "La colonne $colonne_name a été supprimée avec succès de la table $table_name.";
                } else {
                    echo "Erreur lors de la suppression de la colonne: " . $conn->error;
                }
                break;

            case 'modifier_colonne':
                $table_name = $_POST['table_name'];
                $colonne_name = $_POST['colonne_name'];
                $nouveau_nom = $_POST['nouveau_nom'];
                $nouveau_type = $_POST['nouveau_type'];

                // Modification de la colonne dans la table
                $sql = "ALTER TABLE $table_name CHANGE COLUMN $colonne_name $nouveau_nom $nouveau_type";

                if ($conn->query($sql) === TRUE) {
                    echo "La colonne $colonne_name a été modifiée avec succès dans la table $table_name.";
                } else {
                    echo "Erreur lors de la modification de la colonne: " . $conn->error;
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
        <input type="text" name="colonne_type" required>
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
        <input type="text" name="nouveau_type" required>
        <input type="submit" value="Modifier">
    </form>

    <?php
    // Fermeture de la connexion à la base de données
    $conn->close();
    ?>
</body>
</html>
