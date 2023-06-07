<?php
require_once '../utils/db_connection.php';
require_once '../utils/film_functions.php';
require_once '../utils/auth.php';

// Récupérer l'identifiant de l'utilisateur connecté
$userId = getLoggedInUserId();

// Vérifier si l'identifiant de l'utilisateur est valide
if ($userId === null) {
    // Gérer le cas où l'identifiant de l'utilisateur n'est pas disponible
    echo 'Erreur : Impossible de récupérer l\'identifiant de l\'utilisateur connecté.';
    exit;
}

// Vérifier si une recherche a été effectuée
if (isset($_POST['search'])) {
    $searchTerm = $_POST['searchTerm'];
    $films = searchFilms($connection, $searchTerm, $userId);
} else {
    // Récupérer tous les films de l'utilisateur connecté
    $films = getAllFilms($connection, $userId);
}

// Afficher les films
?>
<!DOCTYPE html>
<html>
<head>
    <title>Résultats de recherche des films</title>
    <link rel="stylesheet" type="text/css" href="./film.css">
</head>
<body>
    <div class="container">
        <div class="navbar">
            <a href="../accueil/index.php">Accueil</a>
            <a href="./film.php">Ajouter un Film</a>
            <a href="./film_search.php">Consulter les Films</a>
        </div>

        <h1>Résultats de recherche des films</h1>

        <div class="search-section">
            <form method="POST" action="">
                <input type="text" name="searchTerm" placeholder="Rechercher un film">
                <input type="submit" name="search" value="Rechercher">
            </form>
        </div>

        <?php
        // Afficher les films
        if (isset($_POST['search'])) {
            echo '<h2>Résultats de recherche :</h2>';
        } else {
            echo '<h2>Tous les films :</h2>';
        }
        
        displayFilms($films);
        ?>

    </div>
</body>
</html>
