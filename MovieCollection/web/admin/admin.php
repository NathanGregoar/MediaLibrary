<!DOCTYPE html>
<html>
<head>
    <title>Admin</title>
    <link rel="stylesheet" type="text/css" href="admin.css">
</head>
<body>
    <div class="navbar">
        <a href="../index.php">Accueil</a>
    </div>
    <div class="container">
        <div class="left-section">
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
                            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY
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
        </div>
    </div>
    <section id="commit-section">
        <!-- Liste des 10 derniers commits d'un repository -->
        <h1>10 derniers commits</h1>

        <?php
            $repositoryOwner = "NathanGregoar"; // Remplacez par le nom du propriétaire du référentiel
            $repositoryName = "MovieCollection"; // Remplacez par le nom du référentiel

            $url = "https://api.github.com/repos/{$repositoryOwner}/{$repositoryName}/commits";
            $options = [
                "http" => [
                    "header" => "User-Agent: PHP"
                ]
            ];
            $context = stream_context_create($options);
            $response = file_get_contents($url, false, $context);
            $commits = json_decode($response, true);

            if ($commits) {
                echo "<ul class='commit-list'>";
                for ($i = 0; $i < 10 && isset($commits[$i]['commit']['message']); $i++) {
                    $commitTitle = $commits[$i]['commit']['message'];
                    $commitDescription = $commits[$i]['commit']['message'];
                    echo "<li class='commit-item'><strong class='commit-title'>{$commitTitle}</strong><br>{$commitDescription}</li>";
                }
                echo "</ul>";
            } else {
                echo "<p>Impossible de récupérer les commits.</p>";
            }
        ?>
    </section>
</body>
</html>
