<?php
require_once '../utils/auth.php';
require_once '../utils/config.php';

function getBookCover($title, $author) {
    $url = 'https://www.googleapis.com/books/v1/volumes?q=intitle:' . urlencode($title) . '+inauthor:' . urlencode($author);
    $response = file_get_contents($url);
    $data = json_decode($response, true);

    if ($data && isset($data['items'][0]['volumeInfo']['imageLinks']['thumbnail'])) {
        return $data['items'][0]['volumeInfo']['imageLinks']['thumbnail'];
    }

    return null;
}

// Récupérer l'utilisateur connecté
$loggedInUser = getLoggedInUser();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Livres Possédés</title>
    <link rel="stylesheet" type="text/css" href="livres.css">
</head>
<body>
    <div class="navbar">
        <a href="../accueil/index.php">Accueil</a>
        <a href="./livres_souhaite.php">Ajouter un Livre Souhaité</a>
        <a href="./livres_souhaite_search.php">Consulter les Livres Souhaités</a>
        <a href="./livres_possede.php">Consulter les Livres Possédés</a>
    </div>

    <h1>Livres Possédés</h1>

    <div class="container">
        <div class="search-bar">
            <form method="GET">
                <input type="text" name="search" placeholder="Rechercher un livre" style="width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                <input type="submit" value="Rechercher" style="padding: 10px 20px; background-color: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer;">
            </form>
        </div>

        <?php
        $connect = mysqli_connect($host, $username, $password, $dbName);

        if (!$connect) {
            die('Erreur de connexion à la base de données : ' . mysqli_connect_error());
        }

        // Suppression d'un livre
        if (isset($_POST['delete'])) {
            $deleteId = $connect->real_escape_string($_POST['delete']);
            $deleteSql = "DELETE FROM livres_possedes WHERE id = $deleteId AND added_by = $loggedInUser";

            if ($connect->query($deleteSql) === TRUE) {
                echo '<div class="alert alert-success">Livre supprimé avec succès !</div>';
            } else {
                echo '<div class="alert alert-error">Erreur lors de la suppression du livre : ' . $connect->error . '</div>';
            }
        }

        // Affichage des livres correspondant à la recherche
        if (isset($_GET['search'])) {
            $searchTerm = $connect->real_escape_string($_GET['search']);
            $searchSql = "SELECT * FROM livres_possedes WHERE titre LIKE '%$searchTerm%' AND added_by = " . $loggedInUser['id'];
            $searchResult = $connect->query($searchSql);

            if ($searchResult->num_rows > 0) {
                echo '<h2>Résultats de la recherche :</h2>';
                echo '<div class="livres-liste">';
                while ($row = $searchResult->fetch_assoc()) {
                    $id = $row['id'];
                    $title = $row['titre'];
                    $author = $row['auteur'];
                    $cover = getBookCover($title, $author);

                    echo '<div class="livre">';
                    echo '<img src="' . $cover . '" alt="Couverture">';
                    echo '<h3>' . $title . '</h3>';
                    echo '<p>Auteur : ' . $author . '</p>';
                    echo '<form method="POST" onsubmit="return confirm(\'Voulez-vous vraiment supprimer ce livre ?\');">';
                    echo '<input type="hidden" name="delete" value="' . $id . '">';
                    echo '<input type="submit" value="Supprimer">';
                    echo '</form>';
                    echo '</div>';
                }
                echo '</div>';
            } else {
                echo '<div class="alert alert-info">Aucun livre trouvé.</div>';
            }
        }

        // Affichage de tous les livres possédés
        $allSql = "SELECT * FROM livres_possedes WHERE added_by = " . $loggedInUser['id'];
        $allResult = $connect->query($allSql);

        if ($allResult->num_rows > 0) {
            echo '<h2>Tous les livres :</h2>';
            echo '<div class="livres-liste">';
            while ($row = $allResult->fetch_assoc()) {
                $id = $row['id'];
                $title = $row['titre'];
                $author = $row['auteur'];
                $cover = getBookCover($title, $author);

                echo '<div class="livre">';
                echo '<img src="' . $cover . '" alt="Couverture">';
                echo '<h3>' . $title . '</h3>';
                echo '<p>Auteur : ' . $author . '</p>';
                echo '<form method="POST" onsubmit="return confirm(\'Voulez-vous vraiment supprimer ce livre ?\');">';
                echo '<input type="hidden" name="delete" value="' . $id . '">';
                echo '<input type="submit" value="Supprimer">';
                echo '</form>';
                echo '</div>';
            }
            echo '</div>';
        } else {
            echo '<div class="alert alert-info">Vous n\'avez aucun livre.</div>';
        }

        // Fermer la connexion à la base de données
        $connect->close();
        ?>
    </div>
</body>
</html>
