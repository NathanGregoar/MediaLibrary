<?php
require_once '../utils/auth.php';
require_once '../utils/config.php';
include '../utils/bootstrap.php';

$loggedInUser = getLoggedInUser();
$userId = $loggedInUser['id'];
$searchTerm = $_GET['search'] ?? '';

$searchSql = "SELECT * FROM series WHERE added_by = $userId ";
if ($searchTerm !== '') {
    $searchSql .= "AND (title LIKE '%$searchTerm%') ";
}
$searchSql .= "ORDER BY title ASC";

$searchResult = $connect->query($searchSql);
$numSearchResults = $searchResult->num_rows;

$userSeriesSql = "SELECT * FROM series WHERE added_by = $userId ORDER BY title ASC";
$userSeriesResult = $connect->query($userSeriesSql);
$numUserSeries = $userSeriesResult->num_rows;

$connect->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Mes séries</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" type="text/css" href="../utils/global.css">
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
                    <li class="nav-item"><a class="nav-link" href="./ajouter_series.php">Ajouter une série</a></li>
                    <li class="nav-item"><a class="nav-link text-warning" href="./mes_series.php">Mes séries</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container_search container bg-dark text-light p-4 mt-4 rounded">
        <h1 class="display-5 text-center text-decoration-underline mb-4">Mes séries</h1>
        <div class="search-bar pb-3">
            <form method="GET" class="d-flex">
                <input type="text" name="search" placeholder="Rechercher par titre" class="form-control me-2">
                <button type="submit" class="btn btn-success"><i class="bi bi-search"></i></button>
            </form>
        </div>
        
        <?php if ($searchTerm !== '' && $numSearchResults > 0): ?>
            <h2>Résultats de la recherche (<?php echo $numSearchResults; ?>) :</h2>
            <div class="series-list row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-5 g-4 pb-3">
                <?php while ($row = $searchResult->fetch_assoc()): ?>
                    <?php include './blocks/series_card.php'; ?>
                <?php endwhile; ?>
            </div>
        <?php elseif ($searchTerm !== ''): ?>
            <div class="alert alert-info">Aucun résultat trouvé pour la recherche "<?php echo $searchTerm; ?>"</div>
        <?php endif; ?>

        <h2 class="fs-3 mb-4">Vos séries (<?php echo $numUserSeries; ?>) :</h2>
        <div class="row row-cols-2 row-cols-md-5 g-4 series-list">
            <?php while ($row = $userSeriesResult->fetch_assoc()): ?>
                <?php include './blocks/series_card.php'; ?>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
