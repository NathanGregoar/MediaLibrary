<?php
require_once '../utils/auth.php';
require_once '../utils/config.php';
include '../utils/bootstrap.php';

function getBookCover($title, $author)
{
    $url = 'https://www.googleapis.com/books/v1/volumes?q=intitle:' . urlencode($title) . '+inauthor:' . urlencode($author);
    $response = file_get_contents($url);
    $data = json_decode($response, true);

    return $data && isset($data['items'][0]['volumeInfo']['imageLinks']['thumbnail']) ? $data['items'][0]['volumeInfo']['imageLinks']['thumbnail'] : null;
}

$loggedInUser = getLoggedInUser();
$userId = $loggedInUser['id'];
$searchTerm = $_GET['search'] ?? '';

$searchSql = "SELECT * FROM livres_possedes WHERE added_by = $userId ";
if ($searchTerm !== '') {
    $searchSql .= "AND (titre LIKE '%$searchTerm%' OR auteur LIKE '%$searchTerm%') ";
}
$searchSql .= "ORDER BY titre ASC";

$searchResult = $connect->query($searchSql);
$numSearchResults = $searchResult->num_rows;

$userBooksSql = "SELECT * FROM livres_possedes WHERE added_by = $userId ORDER BY titre ASC";
$userBooksResult = $connect->query($userBooksSql);
$numUserBooks = $userBooksResult->num_rows;

$connect->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ma bibliothèque</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" type="text/css" href="../utils/global.css">
    <link rel="stylesheet" type="text/css" href="livres.css">
</head>
<body class="bg-secondary">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-5">
        <div class="container-fluid">
            <a class="navbar-brand" href="../accueil/index.php">Accueil</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="./ajouter_livres.php">Ajouter un livre</a></li>
                    <li class="nav-item"><a class="nav-link" href="./mes_envies.php">Mes envies</a></li>
                    <li class="nav-item"><a class="nav-link text-warning" href="./ma_bibliotheque.php">Ma bibliothèque</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container_search container bg-dark text-light p-4 mt-4 rounded">
        <h1 class="display-5 text-center text-decoration-underline mb-4">Ma bibliothèque</h1>
        <div class="search-bar pb-3">
            <form method="GET" class="d-flex">
                <input type="text" name="search" placeholder="Rechercher par titre ou par auteur" class="form-control me-2">
                <button type="submit" class="btn btn-success"><i class="bi bi-search"></i></button>
            </form>
        </div>
        
        <?php if ($searchTerm !== '' && $numSearchResults > 0): ?>
            <h2>Résultats de la recherche (<?php echo $numSearchResults; ?>) :</h2>
            <div class="books-list row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-5 g-4 pb-3">
                <?php while ($row = $searchResult->fetch_assoc()): ?>
                    <?php include './blocks/book_card.php'; ?>
                <?php endwhile; ?>
            </div>
        <?php elseif ($searchTerm !== ''): ?>
            <div class="alert alert-info">Aucun résultat trouvé pour la recherche "<?php echo $searchTerm; ?>"</div>
        <?php endif; ?>

        <h2 class="fs-3 mb-4">Vos livres (<?php echo $numUserBooks; ?>) :</h2>
        <div class="row row-cols-2 row-cols-md-5 g-4 books-list">
            <?php while ($row = $userBooksResult->fetch_assoc()): ?>
                <?php include './blocks/book_card.php'; ?>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
