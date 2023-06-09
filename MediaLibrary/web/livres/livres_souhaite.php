<?php
require_once '../utils/auth.php';
require_once '../utils/config.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ajouter un Livre</title>
    <link rel="stylesheet" type="text/css" href="./livres.css">
</head>
<body>
    <div class="container">
        <div class="navbar">
            <a href="../accueil/index.php">Accueil</a>
            <a href="./livre_souhaite.php">Ajouter un Livre Souhaité</a>
            <a href="./livre_souhaite_search.php">Consulter les Livres Souhaités</a>
        </div>

        <h1>Ajouter un Livre Souhaité</h1>

        <?php
        $connection = mysqli_connect($host, $username, $password, $dbName);

        if (!$connection) {
            die('Erreur de connexion à la base de données : ' . mysqli_connect_error());
        }

        // Ajout d'un livre souhaité
        if (isset($_POST['titre'])) {
            $titre = $connection->real_escape_string($_POST['titre']);
            $auteur = $_POST['auteur'] != '' ? $connection->real_escape_string($_POST['auteur']) : 'NULL';

            // Récupérer le nombre total de tomes de la série
            $query = "SELECT COUNT(*) AS total_tomes FROM livres_souhaites WHERE titre = '$titre'";
            $result = mysqli_query($connection, $query);
            $row = mysqli_fetch_assoc($result);
            $nombreTotalTomes = $row['total_tomes'];

            $numeroTome = $_POST['numero_tome'] != '' ? intval($_POST['numero_tome']) : 1;
            $prix = $_POST['prix'] != '' ? floatval($_POST['prix']) : 0.00;
            $format = $_POST['format'] != '' ? $connection->real_escape_string($_POST['format']) : 'NULL';
            $maisonEdition = $_POST['maison_edition'] != '' ? $connection->real_escape_string($_POST['maison_edition']) : 'NULL';
            $resume = $_POST['resume'] != '' ? $connection->real_escape_string($_POST['resume']) : 'NULL';

            // Récupérer l'ID de l'utilisateur connecté à partir des informations de session
            $loggedInUser = getLoggedInUser();

            $insertSql = "INSERT INTO livres_souhaites (titre, auteur, numero_tome, nombre_total_tomes, prix, format, maison_edition, resume_livre, added_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $insertStmt = $connection->prepare($insertSql);
            $insertStmt->bind_param("ssiidsssi", $titre, $auteur, $numeroTome, $nombreTotalTomes, $prix, $format, $maisonEdition, $resume, $loggedInUser['id']);

            if ($insertStmt->execute()) {
                echo '<div class="alert alert-success">Livre souhaité ajouté avec succès !</div>';
            } else {
                echo '<div class="alert alert-error">Erreur lors de l\'ajout du livre souhaité : ' . $connection->error . '</div>';
            }
        }
        ?>

        <form method="POST">
            <label for="titre">Titre :</label>
            <input type="text" id="titre" name="titre" required>

            <label for="auteur">Auteur :</label>
            <input type="text" id="auteur" name="auteur">

            <label for="numero_tome">Numéro du tome :</label>
            <input type="number" id="numero_tome" name="numero_tome" value="1" min="1">

            <label for="nombre_total_tomes">Nombre total de tomes :</label>
            <input type="number" id="nombre_total_tomes" name="nombre_total_tomes" value="1" min="1">

            <label for="prix">Prix :</label>
            <input type="number" id="prix" name="prix" min="0" step="0.01">

            <label for="format">Format :</label>
            <select id="format" name="format">
                <option value="broche">Broché</option>
                <option value="poche">Poche</option>
                <option value="ebook">E-book</option>
                <option value="relie">Relié</option>
                <option value="audio">Audio</option>
            </select>

            <label for="maison_edition">Maison d'édition :</label>
            <input type="text" id="maison_edition" name="maison_edition">

            <label for="resume">Résumé :</label>
            <textarea id="resume" name="resume"></textarea>

            <input type="submit" value="Ajouter">
        </form>

        <?php
        $connection->close();
        ?>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#titre').blur(function() {
                    var titre = $(this).val();
                    if (titre !== '') {
                        $.ajax({
                            url: 'https://www.googleapis.com/books/v1/volumes',
                            data: { q: 'intitle:' + titre, maxResults: 1 },
                            dataType: 'json',
                            success: function(data) {
                                if (data.totalItems > 0) {
                                    var book = data.items[0];
                                    $('#auteur').val(book.volumeInfo.authors ? book.volumeInfo.authors[0] : '');
                                    $('#prix').val(book.saleInfo.listPrice ? book.saleInfo.listPrice.amount : 0);
                                    $('#format').val(book.volumeInfo.printType ? book.volumeInfo.printType : '');
                                }
                            }
                        });
                    }
                });

                $('#auteur').blur(function() {
                    var titre = $('#titre').val();
                    var auteur = $(this).val();
                    if (titre !== '' && auteur !== '') {
                        $.ajax({
                            url: 'https://www.googleapis.com/books/v1/volumes',
                            data: { q: 'intitle:' + titre + '+inauthor:' + auteur, maxResults: 1 },
                            dataType: 'json',
                            success: function(data) {
                                if (data.totalItems > 0) {
                                    var book = data.items[0];
                                    $('#format').val(book.volumeInfo.printType ? book.volumeInfo.printType : '');
                                    $('#maison_edition').val(book.volumeInfo.publisher ? book.volumeInfo.publisher : '');
                                    $('#resume').val(book.volumeInfo.description ? book.volumeInfo.description : '');
                                }
                            }
                        });
                    }
                });
            });
        </script>
    </div>
</body>
</html>
