<?php
require_once '../utils/auth.php';
require_once '../utils/config.php';

// Récupérer l'utilisateur connecté
$loggedInUser = getLoggedInUser();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Rechercher des Livres</title>
    <link rel="stylesheet" type="text/css" href="livre.css">
</head>
<body>
    <div class="container">
        <div class="navbar">
            <a href="../accueil/index.php">Accueil</a>
            <a href="./livre.php">Ajouter un Livre</a>
            <a href="./livres_souhaites_search.php">Consulter les Livres</a>
        </div>

        <h1>Rechercher des Livres</h1>

        <div class="search-bar">
            <form method="GET">
                <input type="text" name="search" placeholder="Rechercher un livre" style="width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                <input type="submit" value="Rechercher" style="padding: 10px 20px; background-color: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer;">
            </form>
        </div>


        <?php
        $connection = mysqli_connect($host, $username, $password, $dbName);

        if (!$connection) {
            die('Erreur de connexion à la base de données : ' . mysqli_connect_error());
        }

        // Suppression d'un livre
        if (isset($_POST['delete'])) {
            $deleteId = $connection->real_escape_string($_POST['delete']);
            $deleteSql = "DELETE FROM livres_souhaites WHERE id = $deleteId AND added_by = $loggedInUser";

            if ($connection->query($deleteSql) === TRUE) {
                echo '<div class="alert alert-success">Livre supprimé avec succès !</div>';
            } else {
                echo '<div class="alert alert-error">Erreur lors de la suppression du livre : ' . $connection->error . '</div>';
            }
        }

        // Affichage des livres correspondant à la recherche
        if (isset($_GET['search'])) {
            $searchTerm = $connection->real_escape_string($_GET['search']);
            $searchSql = "SELECT * FROM livres_souhaites WHERE titre LIKE '%$searchTerm%' AND added_by = " . $loggedInUser['id'];
            $searchResult = $connection->query($searchSql);

            if ($searchResult->num_rows > 0) {
                echo '<h2>Résultats de la recherche :</h2>';
                echo '<div class="livres-liste">';
                while ($row = $searchResult->fetch_assoc()) {
                    $id = $row['id'];
                    $titre = stripslashes($row['titre']);
                    $auteur = $row['auteur'];
                    $numero_tome = $row['numero_tome'];
                    $nombre_total_tomes = $row['nombre_total_tomes'];
                    $prix = $row['prix'];
                    $format = $row['format'];
                    $maison_edition = $row['maison_edition'];
                    $resume = $row['resume'];

                    echo '<div class="livre-item">';
                    echo '<h3>' . $titre . '</h3>';
                    echo '<p><strong>Auteur :</strong> ' . ($auteur != 'NULL' ? $auteur : '') . '</p>';
                    echo '<p><strong>Numéro de tome :</strong> ' . ($numero_tome != 'NULL' ? $numero_tome : '') . '</p>';
                    echo '<p><strong>Nombre total de tomes :</strong> ' . ($nombre_total_tomes != 'NULL' ? $nombre_total_tomes : '') . '</p>';
                    echo '<p><strong>Prix :</strong> ' . ($prix != 'NULL' ? $prix : '') . '</p>';
                    echo '<p><strong>Format :</strong> ' . ($format != 'NULL' ? $format : '') . '</p>';
                    echo '<p><strong>Maison d\'édition :</strong> ' . ($maison_edition != 'NULL' ? $maison_edition : '') . '</p>';
                    echo '<p><strong>Résumé :</strong> ' . ($resume != 'NULL' ? $resume : '') . '</p>';

                    echo '<form method="POST" style="display:inline">';
                    echo '<input type="hidden" name="delete" value="' . $id . '">';
                    echo '<input type="submit" value="Supprimer" class="delete-btn">';
                    echo '</form>';

                    echo '</div>'; // .livre-item
                }
                echo '</div>'; // .livres-liste
            } else {
                echo '<div class="alert">Aucun résultat trouvé pour votre recherche.</div>';
            }
        }

        // Affichage de tous les livres ajoutés par l'utilisateur connecté
        $userLivresSql = "SELECT * FROM livres_souhaites WHERE added_by = " . $loggedInUser['id'];
        $userLivresResult = $connection->query($userLivresSql);

        echo '<h2>Vos livres :</h2>';
        echo '<div class="livres-liste">';
        while ($row = $userLivresResult->fetch_assoc()) {
            $id = $row['id'];
            $titre = stripslashes($row['titre']);
            $auteur = $row['auteur'];
            $numero_tome = $row['numero_tome'];
            $nombre_total_tomes = $row['nombre_total_tomes'];
            $prix = $row['prix'];
            $format = $row['format'];
            $maison_edition = $row['maison_edition'];
            $resume = $row['resume'];

            echo '<div class="livre-item">';
            echo '<h3>' . $titre . '</h3>';
            echo '<p><strong>Auteur :</strong> ' . ($auteur != 'NULL' ? $auteur : '') . '</p>';
            echo '<p><strong>Numéro de tome :</strong> ' . ($numero_tome != 'NULL' ? $numero_tome : '') . '</p>';
            echo '<p><strong>Nombre total de tomes :</strong> ' . ($nombre_total_tomes != 'NULL' ? $nombre_total_tomes : '') . '</p>';
            echo '<p><strong>Prix :</strong> ' . ($prix != 'NULL' ? $prix : '') . '</p>';
            echo '<p><strong>Format :</strong> ' . ($format != 'NULL' ? $format : '') . '</p>';
            echo '<p><strong>Maison d\'édition :</strong> ' . ($maison_edition != 'NULL' ? $maison_edition : '') . '</p>';
            echo '<p><strong>Résumé :</strong> ' . ($resume != 'NULL' ? $resume : '') . '</p>';

            echo '<form method="POST" style="display:inline">';
            echo '<input type="hidden" name="delete" value="' . $id . '">';
            echo '<input type="submit" value="Supprimer" class="delete-btn">';
            echo '</form>';

            echo '</div>'; // .livre-item
        }
        echo '</div>'; // .livres-liste

        $connection->close();
        ?>
    </div>
</body>
</html>
