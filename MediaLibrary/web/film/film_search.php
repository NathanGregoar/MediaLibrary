<?php
require_once '../utils/auth.php';
require_once '../utils/config.php';

// Récupérer l'utilisateur connecté
$loggedInUser = getLoggedInUser();

// Définition des variables de recherche
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$searchSql = "SELECT * FROM films WHERE title LIKE '%$searchTerm%' AND added_by = " . $loggedInUser['id'];

// Connexion à la base de données
$connection = mysqli_connect($host, $username, $password, $dbName);
if (!$connection) {
    die('Erreur de connexion à la base de données : ' . mysqli_connect_error());
}

// Suppression d'un film
if (isset($_POST['delete'])) {
    $deleteId = $connection->real_escape_string($_POST['delete']);
    $deleteSql = "DELETE FROM films WHERE id = $deleteId AND added_by = " . $loggedInUser['id'];

    if ($connection->query($deleteSql) === TRUE) {
        $deleteAlert = '<div class="alert alert-success">Film supprimé avec succès !</div>';
    } else {
        $deleteAlert = '<div class="alert alert-error">Erreur lors de la suppression du film : ' . $connection->error . '</div>';
    }
}

// Mise à jour d'un film
if (isset($_POST['update'])) {
    $updateId = $connection->real_escape_string($_POST['update']);
    $title = $connection->real_escape_string($_POST['title']);
    $director = $connection->real_escape_string($_POST['director']);
    $releaseYear = $connection->real_escape_string($_POST['release_year']);
    $externalHardDrive = $connection->real_escape_string($_POST['external_hard_drive']);

    $updateSql = "UPDATE films SET title = '$title', director = '$director', release_year = '$releaseYear', external_hard_drive = '$externalHardDrive' WHERE id = $updateId AND added_by = " . $loggedInUser['id'];

    if ($connection->query($updateSql) === TRUE) {
        $updateAlert = '<div class="alert alert-success">Film mis à jour avec succès !</div>';
    } else {
        $updateAlert = '<div class="alert alert-error">Erreur lors de la mise à jour du film : ' . $connection->error . '</div>';
    }
}

// Récupération des films correspondant à la recherche
$searchResult = $connection->query($searchSql);
$numSearchResults = $searchResult->num_rows;

// Récupération de tous les films ajoutés par l'utilisateur connecté
$userMoviesSql = "SELECT * FROM films WHERE added_by = " . $loggedInUser['id'];
$userMoviesResult = $connection->query($userMoviesSql);
$numUserMovies = $userMoviesResult->num_rows;

// Fermeture de la connexion à la base de données
$connection->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Rechercher des Films</title>
    <link rel="stylesheet" type="text/css" href="film.css">
    <style>
        .update-form {
            display: none;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="../accueil/index.php">Accueil</a>
        <a href="./film.php">Ajouter un Film</a>
        <a href="./film_search.php">Consulter les Films</a>
    </div>

    <div class="container">
        <h1>Rechercher des Films</h1>

        <form method="GET" action="">
            <input type="text" name="search" placeholder="Titre du film" value="<?php echo $searchTerm; ?>">
            <input type="submit" value="Rechercher">
        </form>

        <h2>Résultats de la recherche (<?php echo $numSearchResults; ?>)</h2>

        <?php
        if ($numSearchResults > 0) {
            while ($row = $searchResult->fetch_assoc()) {
                $id = $row['id'];
                $title = $row['title'];
                $director = $row['director'];
                $releaseYear = $row['release_year'];
                $externalHardDrive = $row['external_hard_drive'];
                $updateFormId = "update-form-$id";
                $deleteFormId = "delete-form-$id";
        ?>
                <div class="film-item">
                    <h3><?php echo $title; ?></h3>
                    <p>Réalisateur : <?php echo $director; ?></p>
                    <p>Année de sortie : <?php echo $releaseYear; ?></p>
                    <p>Disque dur externe : <?php echo $externalHardDrive; ?></p>
                    <button onclick="toggleForm('<?php echo $updateFormId; ?>')">Modifier</button>
                    <button onclick="toggleForm('<?php echo $deleteFormId; ?>')">Supprimer</button>
                    <form id="<?php echo $updateFormId; ?>" class="update-form" method="POST" action="">
                        <input type="hidden" name="update" value="<?php echo $id; ?>">
                        <input type="text" name="title" placeholder="Titre" value="<?php echo $title; ?>">
                        <input type="text" name="director" placeholder="Réalisateur" value="<?php echo $director; ?>">
                        <input type="text" name="release_year" placeholder="Année de sortie" value="<?php echo $releaseYear; ?>">
                        <input type="text" name="external_hard_drive" placeholder="Disque dur externe" value="<?php echo $externalHardDrive; ?>">
                        <button type="submit">Mettre à jour</button>
                    </form>
                    <form id="<?php echo $deleteFormId; ?>" class="delete-form" method="POST" action="">
                        <input type="hidden" name="delete" value="<?php echo $id; ?>">
                        <button type="submit">Confirmer la suppression</button>
                    </form>
                </div>
        <?php
            }
        } else {
            echo '<p>Aucun résultat trouvé.</p>';
        }
        ?>

        <h2>Mes Films (<?php echo $numUserMovies; ?>)</h2>

        <?php
        if ($numUserMovies > 0) {
            while ($row = $userMoviesResult->fetch_assoc()) {
                $id = $row['id'];
                $title = $row['title'];
                $director = $row['director'];
                $releaseYear = $row['release_year'];
                $externalHardDrive = $row['external_hard_drive'];
                $updateFormId = "update-form-$id";
                $deleteFormId = "delete-form-$id";
        ?>
                <div class="film-item">
                    <h3><?php echo $title; ?></h3>
                    <p>Réalisateur : <?php echo $director; ?></p>
                    <p>Année de sortie : <?php echo $releaseYear; ?></p>
                    <p>Disque dur externe : <?php echo $externalHardDrive; ?></p>
                    <button onclick="toggleForm('<?php echo $updateFormId; ?>')">Modifier</button>
                    <button onclick="toggleForm('<?php echo $deleteFormId; ?>')">Supprimer</button>
                    <form id="<?php echo $updateFormId; ?>" class="update-form" method="POST" action="">
                        <input type="hidden" name="update" value="<?php echo $id; ?>">
                        <input type="text" name="title" placeholder="Titre" value="<?php echo $title; ?>">
                        <input type="text" name="director" placeholder="Réalisateur" value="<?php echo $director; ?>">
                        <input type="text" name="release_year" placeholder="Année de sortie" value="<?php echo $releaseYear; ?>">
                        <input type="text" name="external_hard_drive" placeholder="Disque dur externe" value="<?php echo $externalHardDrive; ?>">
                        <button type="submit">Mettre à jour</button>
                    </form>
                    <form id="<?php echo $deleteFormId; ?>" class="delete-form" method="POST" action="">
                        <input type="hidden" name="delete" value="<?php echo $id; ?>">
                        <button type="submit">Confirmer la suppression</button>
                    </form>
                </div>
        <?php
            }
        } else {
            echo '<p>Aucun film trouvé.</p>';
        }
        ?>

    </div>

    <script>
        function toggleForm(formId) {
            var form = document.getElementById(formId);
            if (form.style.display === "none") {
                form.style.display = "block";
            } else {
                form.style.display = "none";
            }
        }
    </script>
</body>
</html>
