<?php
require_once '../utils/auth.php';
require_once '../utils/config.php';

// Récupérer l'utilisateur connecté
$loggedInUser = getLoggedInUser();

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

// Récupération des films correspondant à la recherche
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$searchSql = "SELECT * FROM films WHERE title LIKE '%$searchTerm%' AND added_by = " . $loggedInUser['id'];
$searchResult = $connection->query($searchSql);
$numSearchResults = $searchResult->num_rows;

// Récupération de tous les films ajoutés par l'utilisateur connecté
$userMoviesSql = "SELECT * FROM films WHERE added_by = " . $loggedInUser['id'];
$userMoviesResult = $connection->query($userMoviesSql);
$numUserMovies = $userMoviesResult->num_rows;

// Formulaire de modification
if (isset($_POST['edit'])) {
    $row_id = $_POST['row_id'];

    // Récupérer les données de la ligne à modifier à partir de la base de données
    $sql_row = "SELECT * FROM films WHERE id = $row_id AND added_by = " . $loggedInUser['id'];
    $result_row = $connection->query($sql_row);

    if ($result_row && $result_row->num_rows > 0) {
        $row = $result_row->fetch_assoc();
        $fetch_fields = $result_row->fetch_fields();

        // Générer les champs du formulaire de modification avec les valeurs actuelles
        $form_fields = array();
        $field_info = array_column($fetch_fields, null, 'name'); // Récupérer les informations des champs dans un tableau associatif
        foreach ($row as $field_name => $field_value) {
            if ($field_name !== 'id' && $field_name !== 'added_by') {
                $field_type = $field_info[$field_name]->type;
                $escaped_value = htmlspecialchars($field_value);

                if (in_array($field_type, [MYSQLI_TYPE_TINY, MYSQLI_TYPE_SHORT, MYSQLI_TYPE_LONG, MYSQLI_TYPE_LONGLONG])) {
                    // Champ de type entier
                    $form_fields[] = '<label>' . $field_name . ':</label><input type="number" name="' . $field_name . '" value="' . $escaped_value . '" required>';
                } elseif (in_array($field_type, [MYSQLI_TYPE_FLOAT, MYSQLI_TYPE_DOUBLE, MYSQLI_TYPE_DECIMAL])) {
                    // Champ de type décimal
                    $form_fields[] = '<label>' . $field_name . ':</label><input type="number" step="0.01" name="' . $field_name . '" value="' . $escaped_value . '" required>';
                } else {
                    // Autres types de champ (chaîne, date, etc.)
                    $form_fields[] = '<label>' . $field_name . ':</label><input type="text" name="' . $field_name . '" value="' . $escaped_value . '" required>';
                }
            }
        }

        // Affichage du formulaire de modification
        $edit_form_html = '<form action="" method="post" class="edit-form">';
        $edit_form_html .= implode('<br>', $form_fields);
        $edit_form_html .= '<input type="hidden" name="row_id" value="' . $row_id . '">';
        $edit_form_html .= '<input type="submit" name="save" value="Enregistrer">';
        $edit_form_html .= '</form>';
    } else {
        $edit_form_html = "Erreur lors de la récupération des données de la ligne à modifier.";
    }
}

// Enregistrement des modifications
if (isset($_POST['save'])) {
    $row_id = $_POST['row_id'];

    // Récupérer les valeurs modifiées à partir du formulaire
    $updated_values = array();
    foreach ($_POST as $field_name => $field_value) {
        if ($field_name !== 'row_id' && $field_name !== 'save') {
            $field_value = mysqli_real_escape_string($connection, $field_value);
            $updated_values[] = "$field_name = '$field_value'";
        }
    }

    // Générer la requête de mise à jour
    $sql_update = "UPDATE films SET " . implode(', ', $updated_values) . " WHERE id = $row_id AND added_by = " . $loggedInUser['id'];

    if ($connection->query($sql_update) === true) {
        $update_message = "Les modifications ont été enregistrées avec succès.";
        header("Refresh:0; url=./film_search.php?" . urlencode($table_selected));
        exit();
    } else {
        $update_message = "Erreur lors de l'enregistrement des modifications. Veuillez réessayer.";
    }
}


// Fermeture de la connexion à la base de données
$connection->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Rechercher des Films</title>
    <link rel="stylesheet" type="text/css" href="film.css">
</head>
<body>
    <div class="navbar">
        <a href="../accueil/index.php">Accueil</a>
        <a href="./film.php">Ajouter un Film</a>
        <a href="./film_search.php">Consulter les Films</a>
    </div>

    <h1>Rechercher des Films</h1>

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
        ?>
    </div>

    <div class="container_search">
        <div class="search-bar">
            <form method="GET">
                <input type="text" name="search" placeholder="Rechercher un film" style="width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                <input type="submit" value="Rechercher" style="padding: 10px 20px; background-color: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer;">
            </form>
        </div>

        <?php
        if ($searchTerm !== '') {
            echo '<h2>Résultats de la recherche (' . $numSearchResults . ') :</h2>';
            echo '<div class="movies-list">';
            while ($row = $searchResult->fetch_assoc()) {
                $id = $row['id'];
                $title = $row['title'];
                $director = $row['director'];
                $releaseYear = $row['release_year'];
                $externalHardDrive = $row['external_hard_drive'];

                // Appel à l'API OMDB pour récupérer les informations du film
                $apiUrl = "http://www.omdbapi.com/?apikey=f1e681ff&t=" . urlencode($title);
                $response = file_get_contents($apiUrl);
                $data = json_decode($response, true);

                // Vérifier si la requête a réussi et si l'affiche est disponible
                $poster = ($data['Response'] === 'True' && $data['Poster'] !== 'N/A') ? $data['Poster'] : 'https://e0.pxfuel.com/wallpapers/1021/882/desktop-wallpaper-dual-monitor-firewatch-wengerluggagesave-vertical-dual-monitor.jpg';

                echo '<div class="movie-item">';
                echo '<img src="' . $poster . '" alt="' . $title . '">';
                echo '<div class="movie-details">';
                echo '<h3>' . $title . '</h3>';
                echo '<p><strong>Réalisateur :</strong> ' . ($director != 'NULL' ? $director : '') . '</p>';
                echo '<p><strong>Année de sortie :</strong> ' . ($releaseYear != 'NULL' ? $releaseYear : '') . '</p>';
                echo '<p><strong>Disque dur externe :</strong> ' . ($externalHardDrive != 'NULL' ? $externalHardDrive : '') . '</p>';
                echo '<form method="POST" style="display:inline">';
                echo '<input type="hidden" name="delete" value="' . $id . '">';
                echo '<input type="submit" value="Supprimer" class="delete-btn">';
                echo '<button type="submit" name="edit" class="btn-edit">Modifier</button>';
                echo '</form>';
                echo '</div>';
                echo '</div>';
            }
            echo '</div>';
        }
        ?>

        <h2>Vos films (<?php echo $numUserMovies; ?>) :</h2>
        <div class="movies-list">
            <?php
            while ($row = $userMoviesResult->fetch_assoc()) {
                $id = $row['id'];
                $title = $row['title'];
                $director = $row['director'];
                $releaseYear = $row['release_year'];
                $externalHardDrive = $row['external_hard_drive'];

                // Appel à l'API OMDB pour récupérer les informations du film
                $apiUrl = "http://www.omdbapi.com/?apikey=f1e681ff&t=" . urlencode($title);
                $response = file_get_contents($apiUrl);
                $data = json_decode($response, true);

                // Vérifier si la requête a réussi et si l'affiche est disponible
                $poster = ($data['Response'] === 'True' && $data['Poster'] !== 'N/A') ? $data['Poster'] : 'https://e0.pxfuel.com/wallpapers/1021/882/desktop-wallpaper-dual-monitor-firewatch-wengerluggagesave-vertical-dual-monitor.jpg';

                echo '<div class="movie-item">';
                echo '<img src="' . $poster . '" alt="' . $title . '">';
                echo '<div class="movie-details">';
                echo '<h3>' . $title . '</h3>';
                echo '<p><strong>Réalisateur :</strong> ' . ($director != 'NULL' ? $director : '') . '</p>';
                echo '<p><strong>Année de sortie :</strong> ' . ($releaseYear != 'NULL' ? $releaseYear : '') . '</p>';
                echo '<p><strong>Disque dur externe :</strong> ' . ($externalHardDrive != 'NULL' ? $externalHardDrive : '') . '</p>';
                echo '<form method="POST" style="display:inline">';
                echo '<input type="hidden" name="delete" value="' . $id . '">';
                echo '<input type="submit" value="Supprimer" class="delete-btn">';
                echo '<button type="submit" name="edit" class="btn-edit">Modifier</button>';
                echo '</form>';
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>
        <?php if (isset($edit_form_html)) { ?>
    <div class="edit-form-container">
        <h2>Modifier la ligne de données</h2>
        <?php echo $edit_form_html; ?>
        <form action="" method="post" class="edit-form">
            <?php echo $edit_form_html; ?>
            <input type="hidden" name="table_selected" value="<?php echo $table_selected; ?>">
            <input type="hidden" name="row_id" value="<?php echo $row_id; ?>">
            <input type="submit" name="save" value="Enregistrer">
        </form>
    </div>
<?php } ?>
    </div>
</body>
</html>
