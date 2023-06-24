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

// Modification d'un livre souhaité
if (isset($_POST['edit'])) {
    $editId = $connection->real_escape_string($_POST['edit']);
    $editSql = "SELECT * FROM livres_souhaites WHERE id = $editId AND added_by = " . $loggedInUser['id'];
    $editResult = $connection->query($editSql);

    if ($editResult->num_rows > 0) {
        $editData = $editResult->fetch_assoc();
        $editTitle = $editData['titre'];
        $editAuteur = $editData['auteur'];
        $editNumeroTome = $editData['numero_tome'];
        $editNombreTotalTomes = $editData['nombre_total_tomes'];
        $editPrix = $editData['prix'];
        $editFormat = $editData['format'];
        $editMaisonEdition = $editData['maison_edition'];
        $editResumeLivre = $editData['resume_livre'];
        $editFormVisible = true;
    }
}

// Mise à jour d'un livre souhaité
if (isset($_POST['update'])) {
    $updateId = $connection->real_escape_string($_POST['livre_id']);
    $updateTitle = $connection->real_escape_string($_POST['titre']);
    $updateAuteur = $connection->real_escape_string($_POST['auteur']);
    $updateNumeroTome = $connection->real_escape_string($_POST['numero_tome']);
    $updateNombreTotalTomes = $connection->real_escape_string($_POST['nombre_total_tomes']);
    $updatePrix = $connection->real_escape_string($_POST['prix']);
    $updateFormat = $connection->real_escape_string($_POST['format']);
    $updateMaisonEdition = $connection->real_escape_string($_POST['maison_edition']);
    $updateResumeLivre = $connection->real_escape_string($_POST['resume_livre']);

    $updateSql = "UPDATE livres_souhaites SET titre = '$updateTitle', auteur = '$updateAuteur', numero_tome = '$updateNumeroTome', nombre_total_tomes = '$updateNombreTotalTomes', prix = '$updatePrix', format = '$updateFormat', maison_edition = '$updateMaisonEdition', resume_livre = '$updateResumeLivre' WHERE id = $updateId AND added_by = " . $loggedInUser['id'];

    if ($connection->query($updateSql) === TRUE) {
        $updateAlert = '<div class="alert alert-success">Livre souhaité mis à jour avec succès !</div>';
    } else {
        $updateAlert = '<div class="alert alert-error">Erreur lors de la mise à jour du livre souhaité : ' . $connection->error . '</div>';
    }
}

// Récupérer l'utilisateur connecté
$loggedInUser = getLoggedInUser();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Rechercher des Livres</title>
    <link rel="stylesheet" type="text/css" href="livres.css">
</head>
<body>
    <div class="navbar">
        <a href="../accueil/index.php">Accueil</a>
        <a href="./livres_souhaite.php">Ajouter un Livre Souhaité</a>
        <a href="./livres_souhaite_search.php">Consulter les Livres Souhaités</a>
        <a href="./livres_possede.php">Consulter les Livres Possédés</a>
    </div>

    <h1>Rechercher des Livres</h1>

    <div class="container_search">
        <div class="search-bar">
            <form class="search_book" method="GET">
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
            $deleteSql = "DELETE FROM livres_souhaites WHERE id = $deleteId AND added_by = " . $loggedInUser['id'];

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
                $numSearchResults = $searchResult->num_rows;
                echo '<h2>Résultats de la recherche (' . $numSearchResults . ') :</h2>';
                echo '<div class="livres-liste">';
                while ($row = $searchResult->fetch_assoc()) {
                    $id = $row['id'];
                    $titre = stripslashes($row['titre']);
                    $auteur = stripslashes($row['auteur']);
                    $numero_tome = stripslashes($row['numero_tome']);
                    $nombre_total_tomes = stripslashes($row['nombre_total_tomes']);
                    $prix = stripslashes($row['prix']);
                    $format = stripslashes($row['format']);
                    $maison_edition = stripslashes($row['maison_edition']);
                    $resume = stripslashes($row['resume_livre']);

                    echo '<div class="livre-item">';
                    $bookCover = getBookCover($titre, $auteur);
                    if ($bookCover) {
                        echo '<img src="' . $bookCover . '" alt="Couverture du livre">';
                    }
                    echo '<h3>' . $titre . '</h3>';
                    echo '<p><strong>Auteur :</strong> ' . ($auteur != 'NULL' ? $auteur : '') . '</p>';
                    echo '<p><strong>Numéro de tome :</strong> ' . ($numero_tome != 'NULL' ? $numero_tome : '') . '</p>';
                    echo '<p><strong>Nombre total de tomes :</strong> ' . ($nombre_total_tomes != 'NULL' ? $nombre_total_tomes : '') . '</p>';
                    echo '<p><strong>Prix :</strong> ' . ($prix != 'NULL' ? $prix : '') . '</p>';
                    echo '<p><strong>Format :</strong> ' . ($format != 'NULL' ? $format : '') . '</p>';
                    echo '<p><strong>Maison d\'édition :</strong> ' . ($maison_edition != 'NULL' ? $maison_edition : '') . '</p>';
                    echo '<p><strong>Résumé :</strong> ' . ($resume != 'NULL' ? implode(' ', array_slice(explode(' ', $resume), 0, 20)) : '') . '</p>';

                    echo '<form method="POST" style="display:inline">';
                    echo '<input type="hidden" name="delete" value="' . $id . '">';
                    echo '<input type="submit" value="Supprimer" class="delete-btn">';
                    echo '</form>';

                    // Ajout du bouton "Modifier"
                    echo '<button class="edit-btn" onclick="showEditForm(' . $id . ', \'' . $titre . '\', \'' . $auteur . '\', \'' . $numero_tome . '\', \'' . $nombre_total_tomes . '\', \'' . $prix . '\', \'' . $format . '\', \'' . $maison_edition . '\', \'' . $resume_livre . '\')">Modifier</button>';

                    echo '</div>'; // .livre-item
                }
                echo '</div>'; // .livres-liste
            } else {
                echo '<div class="alert">Aucun résultat trouvé pour votre recherche.</div>';
            }
        }

        // Affichage de tous les livres ajoutés par l'utilisateur connecté
        $userLivresSql = "SELECT * FROM livres_souhaites WHERE added_by = " . $loggedInUser['id'] . " ORDER BY titre ASC";
        $userLivresResult = $connection->query($userLivresSql);

        $numUserLivres = $userLivresResult->num_rows;

        echo '<h2>Vos livres (' . $numUserLivres . ') :</h2>';
        echo '<div class="livres-liste">';
        while ($row = $userLivresResult->fetch_assoc()) {
            $id = $row['id'];
            $titre = stripslashes($row['titre']);
            $auteur = stripslashes($row['auteur']);
            $numero_tome = stripslashes($row['numero_tome']);
            $nombre_total_tomes = stripslashes($row['nombre_total_tomes']);
            $prix = stripslashes($row['prix']);
            $format = stripslashes($row['format']);
            $maison_edition = stripslashes($row['maison_edition']);
            $resume = stripslashes($row['resume_livre']);

            echo '<div class="livre-item">';
            $bookCover = getBookCover($titre, $auteur);
            if ($bookCover) {
                echo '<img src="' . $bookCover . '" alt="Couverture du livre">';
            }
            echo '<h3>' . $id . '</h3>';
            echo '<h3>' . $titre . '</h3>';
            echo '<p><strong>Auteur :</strong> ' . ($auteur != 'NULL' ? $auteur : '') . '</p>';
            echo '<p><strong>Numéro de tome :</strong> ' . ($numero_tome != 'NULL' ? $numero_tome : '') . '</p>';
            echo '<p><strong>Nombre total de tomes :</strong> ' . ($nombre_total_tomes != 'NULL' ? $nombre_total_tomes : '') . '</p>';
            echo '<p><strong>Prix :</strong> ' . ($prix != 'NULL' ? $prix : '') . '</p>';
            echo '<p><strong>Format :</strong> ' . ($format != 'NULL' ? $format : '') . '</p>';
            echo '<p><strong>Maison d\'édition :</strong> ' . ($maison_edition != 'NULL' ? $maison_edition : '') . '</p>';
            echo '<p><strong>Résumé :</strong> ' . ($resume != 'NULL' ? implode(' ', array_slice(explode(' ', $resume), 0, 20)) : '') . '</p>';

            echo '<form method="POST">';
            echo '<input type="hidden" name="delete" value="' . $id . '">';
            echo '<input type="submit" value="Supprimer" class="delete-btn">';
            echo '</form>';

            // Ajout du bouton "Modifier"
            echo '<button class="edit-btn" onclick="showEditForm(' . $id . ', \'' . $titre . '\', \'' . $auteur . '\', \'' . $numero_tome . '\', \'' . $nombre_total_tomes . '\', \'' . $prix . '\', \'' . $format . '\', \'' . $maison_edition . '\', \'' . $resume_livre . '\')">Modifier</button>';

            echo '</div>'; // .livre-item
        }
        echo '</div>'; // .livres-liste

        mysqli_close($connection);
        ?>
    </div>

    <div id="edit-form-container" style="display: none;">
        <h2>Modifier un livre souhaité <?php echo $id; ?></h2>
        <form method="POST" action="livres_souhaite_search.php">
            <input type="hidden" name="<?php echo $id; ?>" id="edit-livre-id">
            <label for="edit-livre-titre">Titre :</label>
            <input type="text" name="titre" id="edit-livre-titre">
            <label for="edit-livre-auteur">Auteur :</label>
            <input type="text" name="auteur" id="edit-livre-auteur">
            <label for="edit-livre-numero-tome">Numéro de tome :</label>
            <input type="text" name="numero_tome" id="edit-livre-numero-tome">
            <label for="edit-livre-nombre-total-tomes">Nombre total de tomes :</label>
            <input type="text" name="nombre_total_tomes" id="edit-livre-nombre-total-tomes">
            <label for="edit-livre-prix">Prix :</label>
            <input type="text" name="prix" id="edit-livre-prix">
            <label for="edit-livre-format">Format :</label>
            <input type="text" name="format" id="edit-livre-format">
            <label for="edit-livre-maison-edition">Maison d'édition :</label>
            <input type="text" name="maison_edition" id="edit-livre-maison-edition">
            <label for="edit-livre-resume-livre">Résumé :</label>
            <textarea name="resume_livre" id="edit-livre-resume-livre"></textarea>
            <input type="submit" name="update" value="Enregistrer">
        </form>
    </div>

    <script>
        function showEditForm(livreId, livreTitre, livreAuteur, livreNumeroTome, livreNombreTotalTomes, livrePrix, livreFormat, livreMaisonEdition, livreResumeLivre) {
            var editFormContainer = document.getElementById('edit-form-container');
            editFormContainer.style.display = 'block';

            var livreIdInput = document.getElementById('edit-livre-id');
            livreIdInput.value = livreId;

            var livreTitreInput = document.getElementById('edit-livre-titre');
            var livreAuteurInput = document.getElementById('edit-livre-auteur');
            var livreNumeroTomeInput = document.getElementById('edit-livre-numero-tome');
            var livreNombreTotalTomesInput = document.getElementById('edit-livre-nombre-total-tomes');
            var livrePrixInput = document.getElementById('edit-livre-prix');
            var livreFormatInput = document.getElementById('edit-livre-format');
            var livreMaisonEditionInput = document.getElementById('edit-livre-maison-edition');
            var livreResumeLivreInput = document.getElementById('edit-livre-resume-livre');

            // Remplir le formulaire avec les informations du livre correspondant à l'ID
            livreTitreInput.value = livreTitre;
            livreAuteurInput.value = livreAuteur;
            livreNumeroTomeInput.value = livreNumeroTome;
            livreNombreTotalTomesInput.value = livreNombreTotalTomes;
            livrePrixInput.value = livrePrix;
            livreFormatInput.value = livreFormat;
            livreMaisonEditionInput.value = livreMaisonEdition;
            livreResumeLivreInput.value = livreResumeLivre;

            var livreIdDisplay = document.getElementById('edit-livre-id-display');
            livreIdDisplay.textContent = livreId;
        }
    </script>

</body>
</html>
