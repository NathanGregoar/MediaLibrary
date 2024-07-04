<?php
require_once '../utils/auth.php';
require_once '../utils/config.php';
include '../utils/bootstrap.php';

$loggedInUser = getLoggedInUser();
$userId = $loggedInUser['id'];
$allowedUserIds = array(2, 3, 4, 5, 6); // IDs des utilisateurs autorisés
$searchTerm = $_GET['search'] ?? '';

// Construction de la clause WHERE pour inclure les films ajoutés par les IDs autorisées
$whereClause = "WHERE ";
if (in_array($userId, $allowedUserIds)) {
    $whereClause .= "added_by IN (".implode(',', $allowedUserIds).") ";
} else {
    $whereClause .= "added_by = $userId ";
}

// Construire la requête SQL pour récupérer tous les films ajoutés par les utilisateurs autorisés
$userFilmsSql = "SELECT * FROM films $whereClause ORDER BY title ASC";

// Exécuter la requête SQL pour récupérer les résultats
$userFilmsResult = $connect->query($userFilmsSql);
$numUserFilms = $userFilmsResult->num_rows;

// Requête SQL pour récupérer les résultats de la recherche
$searchSql = "SELECT * FROM films $whereClause";

// Ajouter la condition de recherche par titre si un terme de recherche est spécifié
if ($searchTerm !== '') {
    $searchSql .= "AND (title LIKE '%$searchTerm%') ";
}

$searchSql .= "ORDER BY title ASC";

// Exécuter la requête SQL pour récupérer les résultats de recherche
$searchResult = $connect->query($searchSql);
$numSearchResults = $searchResult->num_rows;

$connect->close();
?>



<!DOCTYPE html>
<html>
<head>
    <title>Mes films</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" type="text/css" href="../utils/global.css">
    <script src="./tools/auto_completion.js"></script>
    <link rel="icon" href="../images/icon.png" type="image/x-icon">
    <link rel="manifest" href="/manifest.json">
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
                    <li class="nav-item"><a class="nav-link" href="./ajouter_films.php">Ajouter un film</a></li>
                    <li class="nav-item"><a class="nav-link text-warning" href="./mes_films.php">Mes films</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container_search container bg-dark text-light p-4 mt-4 rounded">
        <h1 class="display-5 text-center text-decoration-underline mb-4">Mes films</h1>
        <div class="search-bar pb-3">
            <form method="GET" class="d-flex">
                <input type="text" name="search" placeholder="Rechercher par titre" class="form-control me-2">
                <button type="submit" class="btn btn-success"><i class="bi bi-search"></i></button>
            </form>
        </div>
        
        <?php if ($searchTerm !== '' && $numSearchResults > 0): ?>
            <h2>Résultats de la recherche (<?php echo $numSearchResults; ?>) :</h2>
            <div class="films-list row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-5 g-4 pb-3">
                <?php while ($row = $searchResult->fetch_assoc()): ?>
                    <?php include './blocks/film_card.php'; ?>
                <?php endwhile; ?>
            </div>
        <?php elseif ($searchTerm !== ''): ?>
            <div class="alert alert-info">Aucun résultat trouvé pour la recherche "<?php echo $searchTerm; ?>"</div>
        <?php endif; ?>

        <h2 class="fs-3 mb-4">Vos films (<?php echo $numUserFilms; ?>) :</h2>
        <div class="row row-cols-2 row-cols-md-5 g-4 films-list">
            <?php while ($row = $userFilmsResult->fetch_assoc()): ?>
                <?php include './blocks/film_card.php'; ?>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>


<script>

    // Fonction pour récupérer l'URL de l'affiche du film
function getMoviePoster($title) {
    $apiKey = 'f1e681ff'; // Votre clé API OMDb
    $query = urlencode($title);
    $url = "http://www.omdbapi.com/?apikey=$apiKey&t=$query";

    // Effectuer la requête HTTP GET
    $response = file_get_contents($url);

    // Vérifier si la réponse est valide
    if ($response) {
        $data = json_decode($response, true);
        if ($data['Response'] == 'True') {
            return $data['Poster']; // Renvoie l'URL de l'affiche du film
        }
    }

    return null; // Si aucune affiche n'est trouvée ou si la requête échoue
}

// Récupérer l'URL de l'affiche du film
$posterUrl = getMoviePoster($title);
</script>