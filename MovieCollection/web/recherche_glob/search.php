<!DOCTYPE html>
<html>
<head>
    <title>Recherche</title>
    <link rel="stylesheet" type="text/css" href="search.css">
</head>
<body>
    <div class="container">
        <div class="navbar">
            <a href="../index.php">Accueil</a>
            <a href="search.php">Rechercher</a>
        </div>

        <h1>Recherche</h1>

        <div class="search-bar">
            <form method="GET">
                <input type="text" name="search" placeholder="Rechercher..." style="width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                <input type="submit" value="Rechercher" style="padding: 10px 20px; background-color: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer;">
            </form>
        </div>

        <?php
        $servername = "db";
        $username = "nathan";
        $password = "444719";
        $dbname = "movie_collection";

        // Connexion à la base de données
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connexion échouée: " . $conn->connect_error);
        }

        // Vérification de la soumission du formulaire de recherche
        if (isset($_GET['search'])) {
            $searchTerm = $_GET['search'];

            // Requête de recherche dans les tables films, series, livres, concerts et documentaires
            $sql = "SELECT * FROM films WHERE title LIKE '%$searchTerm%'
                    UNION
                    SELECT * FROM series WHERE title LIKE '%$searchTerm%'
                    UNION
                    SELECT * FROM livres WHERE title LIKE '%$searchTerm%'
                    UNION
                    SELECT * FROM concerts WHERE artist LIKE '%$searchTerm%'
                    UNION
                    SELECT * FROM documentaires WHERE title LIKE '%$searchTerm%'
                    ORDER BY title";

            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                echo "<h2>Résultats de la recherche :</h2>";
                echo "<ul>";
                while ($row = $result->fetch_assoc()) {
                    echo "<li>" . $row['title'] . "</li>";
                }
                echo "</ul>";
            } else {
                echo "<p>Aucun résultat trouvé pour la recherche : $searchTerm</p>";
            }
        }

        // Fermeture de la connexion à la base de données
        $conn->close();
        ?>

    </div>
</body>
</html>
