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

// Suppression d'un livre
if (isset($_POST['delete'])) {
    $deleteId = $connection->real_escape_string($_POST['delete']);
    $deleteSql = "DELETE FROM livres WHERE id = $deleteId AND added_by = " . $loggedInUser['id'];

    if ($connection->query($deleteSql) === TRUE) {
        $deleteAlert = '<div class="alert alert-success">Livre supprimé avec succès !</div>';
    } else {
        $deleteAlert = '<div class="alert alert-error">Erreur lors de la suppression du livre : ' . $connection->error . '</div>';
    }
}

// Modification d'un livre
if (isset($_POST['edit'])) {
    $editId = $connection->real_escape_string($_POST['edit']);
    $editSql = "SELECT * FROM livres WHERE id = $editId AND added_by = " . $loggedInUser['id'];
    $editResult = $connection->query($editSql);

    if ($editResult->num_rows > 0) {
        $editData = $editResult->fetch_assoc();
        $editTitle = $editData['title'];
        $editAuthor = $editData['author'];
        $editFormVisible = true;
    }
}

// Mise à jour d'un livre
if (isset($_POST['update'])) {
    $updateId = $connection->real_escape_string($_POST['book_id']);
    $updateTitle = $connection->real_escape_string($_POST['title']);
    $updateAuthor = $connection->real_escape_string($_POST['author']);

    $updateSql = "UPDATE livres SET title = '$updateTitle', author = '$updateAuthor' WHERE id = $updateId AND added_by = " . $loggedInUser['id'];

    if ($connection->query($updateSql) === TRUE) {
        $updateAlert = '<div class="alert alert-success">Livre mis à jour avec succès !</div>';
    } else {
        $updateAlert = '<div class="alert alert-error">Erreur lors de la mise à jour du livre : ' . $connection->error . '</div>';
    }
}

// Récupération des livres correspondant à la recherche
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$searchSql = "SELECT * FROM livres WHERE title LIKE '%$searchTerm%' AND added_by = " . $loggedInUser['id'];
$searchResult = $connection->query($searchSql);
$numSearchResults = $searchResult->num_rows;

// Récupération de tous les livres de l'utilisateur connecté
$booksSql = "SELECT * FROM livres WHERE added_by = " . $loggedInUser['id'];
$booksResult = $connection->query($booksSql);
$numBooks = $booksResult->num_rows;

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
        <a href="./livre.php">Ajouter un Livre</a>
        <a href="./livre_search.php">Consulter les Livres</a>
    </div>

    <h1>Rechercher des Livres</h1>

    <div class="alert-container">
        <?php echo isset($deleteAlert) ? $deleteAlert : ''; ?>
        <?php echo isset($updateAlert) ? $updateAlert : ''; ?>
    </div>

    <div class="container_search">
        <div class="search-bar">
            <form method="GET">
                <input type="text" name="search" placeholder="Rechercher un livre" style="width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                <input type="submit" value="Rechercher" style="padding: 10px 20px; background-color: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer;">
            </form>
        </div>

        <?php if ($numSearchResults > 0) { ?>
            <h2>Résultats de recherche (<?php echo $numSearchResults; ?>)</h2>
            <ul class="book-list">
                <?php while ($row = $searchResult->fetch_assoc()) { ?>
                    <li class="book-item">
                        <div class="book-info">
                            <div class="book-cover">
                                <?php
                                $bookCover = getBookCover($row['title'], $row['author']);
                                if ($bookCover) {
                                    echo '<img src="' . $bookCover . '" alt="' . $row['title'] . '">';
                                } else {
                                    echo '<div class="no-cover">Pas de couverture disponible</div>';
                                }
                                ?>
                            </div>
                            <div class="book-details">
                                <div class="book-title"><?php echo $row['title']; ?></div>
                                <div class="book-author"><?php echo $row['author']; ?></div>
                            </div>
                        </div>
                        <div class="book-actions">
                            <form method="POST">
                                <input type="hidden" name="delete" value="<?php echo $row['id']; ?>">
                                <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce livre ?')">Supprimer</button>
                            </form>
                            <form method="POST">
                                <input type="hidden" name="edit" value="<?php echo $row['id']; ?>">
                                <button type="submit">Modifier</button>
                            </form>
                        </div>
                    </li>
                <?php } ?>
            </ul>
        <?php } else if ($searchTerm !== '') { ?>
            <div class="no-results">Aucun livre trouvé pour "<?php echo $searchTerm; ?>"</div>
        <?php } ?>

        <h2>Tous les Livres (<?php echo $numBooks; ?>)</h2>
        <ul class="book-list">
            <?php while ($row = $booksResult->fetch_assoc()) { ?>
                <li class="book-item">
                    <div class="book-info">
                        <div class="book-cover">
                            <?php
                            $bookCover = getBookCover($row['title'], $row['author']);
                            if ($bookCover) {
                                echo '<img src="' . $bookCover . '" alt="' . $row['title'] . '">';
                            } else {
                                echo '<div class="no-cover">Pas de couverture disponible</div>';
                            }
                            ?>
                        </div>
                        <div class="book-details">
                            <div class="book-title"><?php echo $row['title']; ?></div>
                            <div class="book-author"><?php echo $row['author']; ?></div>
                        </div>
                    </div>
                    <div class="book-actions">
                        <form method="POST">
                            <input type="hidden" name="delete" value="<?php echo $row['id']; ?>">
                            <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce livre ?')">Supprimer</button>
                        </form>
                        <form method="POST">
                            <input type="hidden" name="edit" value="<?php echo $row['id']; ?>">
                            <button type="submit">Modifier</button>
                        </form>
                    </div>
                </li>
            <?php } ?>
        </ul>
    </div>
</body>
</html>
