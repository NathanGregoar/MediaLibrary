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
                <input type="text" name="search" placeholder="Rechercher sur base du titre" style="width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
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

            // Requête de recherche dans la table films
            $filmsSql = "SELECT * FROM films WHERE title LIKE '%$searchTerm%'";
            $filmsResult = $conn->query($filmsSql);

            // Requête de recherche dans la table series
            $seriesSql = "SELECT * FROM series WHERE title LIKE '%$searchTerm%'";
            $seriesResult = $conn->query($seriesSql);

            // Requête de recherche dans la table livres
            $livresSql = "SELECT * FROM livres WHERE title LIKE '%$searchTerm%'";
            $livresResult = $conn->query($livresSql);

            // Requête de recherche dans la table concerts
            $concertsSql = "SELECT * FROM concerts WHERE artist LIKE '%$searchTerm%'";
            $concertsResult = $conn->query($concertsSql);

            // Requête de recherche dans la table documentaires
            $documentairesSql = "SELECT * FROM documentaires WHERE title LIKE '%$searchTerm%'";
            $documentairesResult = $conn->query($documentairesSql);

            // Affichage des résultats
            $foundResults = false;

            // Résultats de la table films
            if ($filmsResult->num_rows > 0) {
                echo "<h2>Résultats de recherche - Films :</h2>";
                echo "<ul>";
                while ($row = $filmsResult->fetch_assoc()) {
                    $filmId = $row['id'];
                    $filmTitle = $row['title'];
                    echo "<li><a href='../film/film_search.php?search=$searchTerm'>$filmTitle</a></li>";
                }
                echo "</ul>";
                $foundResults = true;
            }

            // Résultats de la table series
            if ($seriesResult->num_rows > 0) {
                echo "<h2>Résultats de recherche - Séries :</h2>";
                echo "<ul>";
                while ($row = $seriesResult->fetch_assoc()) {
                    $serieId = $row['id'];
                    $serieTitle = $row['title'];
                    echo "<li><a href='../serie/serie_search.php?search=$searchTerm'>$serieTitle</a></li>";
                }
                echo "</ul>";
                $foundResults = true;
            }

            // Résultats de la table livres
            if ($livresResult->num_rows > 0) {
                echo "<h2>Résultats de recherche - Livres :</h2>";
                echo "<ul>";
                while ($row = $livresResult->fetch_assoc()) {
                    $livreId = $row['id'];
                    $livreTitle = $row['title'];
                    echo "<li><a href='../livre/livre.php'>$livreTitle</a></li>";
                }
                echo "</ul>";
                $foundResults = true;
            }

            // Résultats de la table concerts
            if ($concertsResult->num_rows > 0) {
                echo "<h2>Résultats de recherche - Concerts :</h2>";
                echo "<ul>";
                while ($row = $concertsResult->fetch_assoc()) {
                    $concertId = $row['id'];
                    $concertArtist = $row['artist'];
                    echo "<li><a href='../concert/concert.php'>$concertArtist</a></li>";
                }
                echo "</ul>";
                $foundResults = true;
            }

            // Résultats de la table documentaires
            if ($documentairesResult->num_rows > 0) {
                echo "<h2>Résultats de recherche - Documentaires :</h2>";
                echo "<ul>";
                while ($row = $documentairesResult->fetch_assoc()) {
                    $documentaireId = $row['id'];
                    $documentaireTitle = $row['title'];
                    echo "<li><a href='../documentaire/documentaire.php'>$documentaireTitle</a></li>";
                }
                echo "</ul>";
                $foundResults = true;
            }

            if (!$foundResults) {
                echo "<p>Aucun résultat trouvé pour la recherche : $searchTerm</p>";
            }
        }

        // Fermeture de la connexion à la base de données
        $conn->close();
        ?>

    </div>
</body>
</html>
