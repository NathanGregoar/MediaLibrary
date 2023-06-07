<?php
require_once '../utils/auth.php';
require_once '../utils/config.php';

// Vérification si l'utilisateur est connecté
checkLoggedIn();

// Récupérer l'ID de l'utilisateur connecté
$loggedInUserId = getLoggedInUserId();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Rechercher des Films</title>
    <link rel="stylesheet" type="text/css" href="film.css">
</head>
<body>
    <div class="container">
        <div class="navbar">
            <a href="../accueil/index.php">Accueil</a>
            <a href="./film.php">Ajouter un Film</a>
            <a href="./film_search.php">Consulter les Films</a>
        </div>

        <h1>Rechercher des Films</h1>

        <div class="search-bar">
            <form method="GET">
                <input type="text" name="search" placeholder="Rechercher un film" style="width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                <input type="submit" value="Rechercher" style="padding: 10px 20px; background-color: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer;">
            </form>
        </div>

        <?php
        $connection = mysqli_connect($host, $username, $password, $dbName);

        if (!$connection) {
            die('Erreur de connexion à la base de données : ' . mysqli_connect_error());
        }

        // Suppression d'un film
        if (isset($_POST['delete'])) {
            $deleteId = $connection->real_escape_string($_POST['delete']);
            $deleteSql = "DELETE FROM films WHERE id = $deleteId AND added_by = $loggedInUserId";

            if ($connection->query($deleteSql) === TRUE) {
                echo '<div class="alert alert-success">Film supprimé avec succès !</div>';
            } else {
                echo '<div class="alert alert-error">Erreur lors de la suppression du film : ' . $connection->error . '</div>';
            }
        }

        // Affichage des films correspondant à la recherche
        if (isset($_GET['search'])) {
            $searchTerm = $connection->real_escape_string($_GET['search']);
            $searchSql = "SELECT * FROM films WHERE title LIKE '%$searchTerm%' AND added_by = $loggedInUserId";
            $searchResult = $connection->query($searchSql);

            if ($searchResult->num_rows > 0) {
                echo '<h2>Résultats de la recherche :</h2>';
                echo '<div class="movies-list">';
                while ($row = $searchResult->fetch_assoc()) {
                    // Affichage des détails du film...
                }
                echo '</div>'; // .movies-list
            } else {
                echo '<div class="alert">Aucun résultat trouvé pour votre recherche.</div>';
            }
        }

        // Affichage de tous les films ajoutés par l'utilisateur connecté
        $allMoviesSql = "SELECT * FROM films WHERE added_by = $loggedInUserId";
        $allMoviesResult = $connection->query($allMoviesSql);

        echo '<h2>Liste complète des films :</h2>';
        echo '<div class="movies-list">';
        while ($row = $allMoviesResult->fetch_assoc()) {
            // Affichage des détails du film...
        }
        echo '</div>'; // .movies-list

        $connection->close();
        ?>
    </div>
</body>
</html>
