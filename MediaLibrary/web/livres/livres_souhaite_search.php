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

// Connexion à la base de données
$connection = mysqli_connect($host, $username, $password, $dbName);
if (!$connection) {
    die('Erreur de connexion à la base de données : ' . mysqli_connect_error());
}

// Suppression d'un livre souhaité
if (isset($_POST['delete'])) {
    $deleteId = $connection->real_escape_string($_POST['delete']);
    $deleteSql = "DELETE FROM livres_souhaites WHERE id = $deleteId AND added_by = " . $loggedInUser['id'];

    if ($connection->query($deleteSql) === TRUE) {
        $deleteAlert = '<div class="alert alert-success">Livre souhaité supprimé avec succès !</div>';
    } else {
        $deleteAlert = '<div class="alert alert-error">Erreur lors de la suppression du livre souhaité : ' . $connection->error . '</div>';
    }
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
    $updateId = $connection->real_escape_string($_POST['book_id']);
    $updateTitle = $connection->real_escape_string($_POST['title']);
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

// Récupération des films correspondant à la recherche
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$searchSql = "SELECT * FROM livres_souhaites WHERE titre LIKE '%$searchTerm%' AND added_by = " . $loggedInUser['id'] . " ORDER BY titre ASC";
$searchResult = $connection->query($searchSql);
$numSearchResults = $searchResult->num_rows;

// Récupération de tous les livres souhaités ajoutés par l'utilisateur connecté
$userBooksSql = "SELECT * FROM livres_souhaites WHERE added_by = " . $loggedInUser['id'] . " ORDER BY titre ASC";
$userBooksResult = $connection->query($userBooksSql);
$numUserBooks = $userBooksResult->num_rows;

// Fermeture de la connexion à la base de données
$connection->close();

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
        <a href="./livres_souhaite.php">Ajouter un Livre</a>
        <a href="./livres_souhaite_search.php">Consulter les Livres</a>
        <a href="./livres_possede.php">Livres possédé</a>
    </div>

    <h1>Rechercher des Livres</h1>

    <div class="alert-container">
        <?php
        if ($searchTerm !== '') {
            if ($numSearchResults > 0) {
                echo '<div class="alert alert-success">Résultats de la recherche (' . $numSearchResults . ') :</div>';
            } else {
                echo '<div class="alert alert-info">Aucun résultat trouvé pour la recherche "' . $searchTerm . '"</div>';
            }
        }

        if (isset($deleteAlert)) {
            echo $deleteAlert;
        }

        if (isset($updateAlert)) {
            echo $updateAlert;
        }
        ?>
    </div>

    <div class="container_search">
        <div class="search-bar">
            <form method="GET">
                <input type="text" name="search" placeholder="Rechercher un livre souhaité" style="width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                <input type="submit" value="Rechercher" style="padding: 10px 20px; background-color: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer;">
            </form>
        </div>

        <?php
        if ($searchTerm !== '') {
            echo '<h2>Résultats de la recherche (' . $numSearchResults . ') :</h2>';
            echo '<div class="books-list">';
            while ($row = $searchResult->fetch_assoc()) {
                $id = $row['id'];
                $title = $row['titre'];
                $author = $row['auteur'];
                $volumeNumber = $row['numero_tome'];
                $totalVolumes = $row['nombre_total_tomes'];
                $price = $row['prix'];
                $format = $row['format'];
                $publisher = $row['maison_edition'];
                $summary = $row['resume_livre'];

                echo '<div class="book-item">';
                $bookCover = getBookCover($titre, $auteur);
                if ($bookCover) {
                    echo '<img src="' . $bookCover . '" alt="Couverture du livre">';
                }
                echo '<h3>' . $title . '</h3>';
                echo '<p><strong>Auteur :</strong> ' . ($author != '/' ? $author : '') . '</p>';
                echo '<p><strong>Numéro de tome :</strong> ' . ($volumeNumber != 1 ? $volumeNumber : '') . '</p>';
                echo '<p><strong>Nombre total de tomes :</strong> ' . ($totalVolumes != 1 ? $totalVolumes : '') . '</p>';
                echo '<p><strong>Prix :</strong> ' . number_format($price, 2) . ' €</p>';
                echo '<p><strong>Format :</strong> ' . ($format != null ? $format : '') . '</p>';
                echo '<p><strong>Maison d\'édition :</strong> ' . ($publisher != null ? $publisher : '') . '</p>';
                echo '<p><strong>Résumé :</strong> ' . ($summary != null ? $summary : '') . '</p>';
                echo '<form method="POST" style="display:inline">';
                echo '<input type="hidden" name="delete" value="' . $id . '">';
                echo '<input type="submit" value="Supprimer" class="delete-btn">';
                echo '</form>';
                echo '<button class="edit-btn" onclick="showEditForm(' . $id . ', \'' . $title . '\', \'' . $author . '\', ' . $volumeNumber . ', ' . $totalVolumes . ', ' . $price . ', \'' . $format . '\', \'' . $publisher . '\', \'' . $summary . '\')">Modifier</button>';
                echo '</div>';
            }
            echo '</div>';
        }
        ?>

        <h2>Vos livres (<?php echo $numUserBooks; ?>) :</h2>
        <div class="books-list">
            <?php
            while ($row = $userBooksResult->fetch_assoc()) {
                $id = $row['id'];
                $titre = $row['titre'];
                $auteur = $row['auteur'];
                $numeroTome = $row['numero_tome'];
                $nombreTotalTomes = $row['nombre_total_tomes'];
                $prix = $row['prix'];
                $format = $row['format'];
                $maisonEdition = $row['maison_edition'];
                $resumeLivre = $row['resume_livre'];

                echo '<div class="book-item">';
                $bookCover = getBookCover($titre, $auteur);
                if ($bookCover) {
                    echo '<img src="' . $bookCover . '" alt="Couverture du livre">';
                }
                echo '<h3>' . $titre . '</h3>';
                echo '<p><strong>Auteur :</strong> ' . ($auteur != '/' ? $auteur : '') . '</p>';
                echo '<p><strong>Numéro de tome :</strong> ' . ($numeroTome != 1 ? $numeroTome : '') . '</p>';
                echo '<p><strong>Nombre total de tomes :</strong> ' . ($nombreTotalTomes != 1 ? $nombreTotalTomes : '') . '</p>';
                echo '<p><strong>Prix :</strong> ' . ($prix != 0.00 ? $prix : '') . '</p>';
                echo '<p><strong>Format :</strong> ' . ($format != null ? $format : '') . '</p>';
                echo '<p><strong>Maison d\'édition :</strong> ' . ($maisonEdition != null ? $maisonEdition : '') . '</p>';
                
                $resumeLivre = ($resumeLivre != null ? $resumeLivre : '');

                $words = explode(' ', $resumeLivre); // Sépare la chaîne en mots
                $limitedSummary = implode(' ', array_slice($words, 0, 20)); // Concatène les 20 premiers mots

                echo '<p><strong>Résumé :</strong> ' . $limitedSummary . '</p>';

                echo '<form method="POST" style="display:inline">';
                echo '<input type="hidden" name="delete" value="' . $id . '">';
                echo '<input type="submit" value="Supprimer" class="delete-btn">';
                echo '</form>';
                echo '<button class="edit-btn" onclick="showEditForm(' . $id . ', \'' . $titre . '\', \'' . $auteur . '\', ' . $numeroTome . ', ' . $nombreTotalTomes . ', ' . $prix . ', \'' . $format . '\', \'' . $maisonEdition . '\', \'' . $resumeLivre . '\')">Modifier</button>';
                echo '</div>';
            }
            ?>
        </div>
    </div>

    <div id="edit-form-container" style="display: none;">
        <h2>Modifier un livre souhaité</h2>
        <form method="POST" action="livres_souhaite_search.php">
            <input type="hidden" name="book_id" id="edit-book-id">
            <label for="edit-book-title">Titre :</label>
            <input type="text" name="titre" id="edit-book-title">
            <label for="edit-book-author">Auteur :</label>
            <input type="text" name="auteur" id="edit-book-author">
            <label for="edit-book-tome">Numéro de tome :</label>
            <input type="number" name="numero_tome" id="edit-book-tome">
            <label for="edit-book-total-tomes">Nombre total de tomes :</label>
            <input type="number" name="nombre_total_tomes" id="edit-book-total-tomes">
            <label for="edit-book-price">Prix :</label>
            <input type="text" name="prix" id="edit-book-price">
            <label for="edit-book-format">Format :</label>
            <input type="text" name="format" id="edit-book-format">
            <label for="edit-book-publisher">Maison d'édition :</label>
            <input type="text" name="maison_edition" id="edit-book-publisher">
            <label for="edit-book-summary">Résumé :</label>
            <textarea name="resume_livre" id="edit-book-summary"></textarea>
            <input type="submit" name="update" value="Enregistrer">
        </form>
    </div>

    <script>
        function showEditForm(bookId, bookTitle, bookAuthor, bookTome, bookTotalTomes, bookPrice, bookFormat, bookPublisher, bookSummary) {
            var editFormContainer = document.getElementById('edit-form-container');
            editFormContainer.style.display = 'block';

            var bookIdInput = document.getElementById('edit-book-id');
            bookIdInput.value = bookId;

            var bookTitleInput = document.getElementById('edit-book-title');
            var bookAuthorInput = document.getElementById('edit-book-author');
            var bookTomeInput = document.getElementById('edit-book-tome');
            var bookTotalTomesInput = document.getElementById('edit-book-total-tomes');
            var bookPriceInput = document.getElementById('edit-book-price');
            var bookFormatInput = document.getElementById('edit-book-format');
            var bookPublisherInput = document.getElementById('edit-book-publisher');
            var bookSummaryInput = document.getElementById('edit-book-summary');

            // Remplir le formulaire avec les informations du livre souhaité correspondant à l'ID
            bookTitleInput.value = bookTitle;
            bookAuthorInput.value = bookAuthor;
            bookTomeInput.value = bookTome;
            bookTotalTomesInput.value = bookTotalTomes;
            bookPriceInput.value = bookPrice;
            bookFormatInput.value = bookFormat;
            bookPublisherInput.value = bookPublisher;
            bookSummaryInput.value = bookSummary;

            var bookIdDisplay = document.getElementById('edit-book-id-display');
            bookIdDisplay.textContent = bookId;
        }
    </script>
</body>
</html>
