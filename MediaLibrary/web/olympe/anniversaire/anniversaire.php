<?php
require_once '../../utils/auth.php';
require_once '../../utils/config.php';
session_start();

$loggedInUser = getLoggedInUser();

$connection = new mysqli('db', 'nathan', '444719', 'media_library');

if ($connection->connect_error) {
    die('Erreur de connexion : ' . $connection->connect_error);
}

$username = $loggedInUser['username'];

// Effectuer une requête SQL pour vérifier si une date d'anniversaire est enregistrée
$query = "SELECT COUNT(*) as count FROM anniversaire WHERE username = '$username'";
$result = $connection->query($query);

if ($result) {
    $row = $result->fetch_assoc();
    $hasBirthday = ($row['count'] > 0);
    echo json_encode(['hasBirthday' => $hasBirthday]);
} else {
    echo json_encode(['hasBirthday' => false]);
}

$connection->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>L'Olympe - Anniversaires</title>
    <link rel="icon" type="image/png" href="https://i.goopics.net/oqioit.png">
    <link rel="stylesheet" type="text/css" href="./anniversaire.css">
</head>
<body>
    <div class="navbar">
        <a href="../../accueil/index.php">Accueil</a>
        <a href="../../olympe/olympe.php">L'Olympe</a> 
        <a href="../../olympe/anniversaire/anniversaire.php" class="select">Anniversaire</a> 
    </div>

    <section class="form">
        <!-- Formulaire de Date d'Anniversaire -->
        <section class="form_anniv">
            <h2>Date d'Anniversaire</h2>
            <form action="traitement_date_anniversaire.php" method="post">
                <label for="date_anniversaire">Date d'anniversaire :</label>
                <input type="date" id="date_anniversaire" name="date_anniversaire">
                <input type="submit" value="Enregistrer">
            </form>
        </section>

        <!-- Formulaire pour Enregistrer un Cadeau Souhaité -->
        <section class="form_gift">
            <h2>Enregistrer un Cadeau Souhaité</h2>
            <form action="traitement_cadeau.php" method="post" enctype="multipart/form-data">
                <div class="grid-container">
                    <div class="grid-item">
                        <label for="photo_cadeau">Photo de l'objet :</label>
                        <input type="file" id="photo_cadeau" name="photo_cadeau" onchange="previewImage()">
                    </div>

                <!-- Ajout d'une div pour la prévisualisation de l'image -->
                    <div class="grid-item">
                        <div id="imagePreview" style="display: none;">
                            <img id="preview" src="" alt="Image Preview" width="200">
                        </div>
                    </div>

                    <div class="grid-items">
                        <label for="description_cadeau">Nom de l'objet :</label>
                        <input type="text" id="description_cadeau" name="description_cadeau">
                    </div>
                    
                    <div class="grid-items">
                        <label for="categorie_cadeau">Catégorie/Thème :</label>
                        <select id="categorie_cadeau" name="categorie_cadeau">
                            <option value="Mode">Mode</option>
                            <option selected value="Technologie">Technologie</option>
                            <option value="Jeux">Jeux</option>
                            <option value="Cosmetiques">Cosmetiques</option>
                            <option value="Livres">Livres</option>
                            <option value="Cuisine">Cuisine</option>
                            <option value="Beauté">Beauté</option>
                            <option value="Sports">Sports</option>
                            <option value="Art">Créativité</option>
                            <option value="Musique">Musique</option>
                            <option value="Voyages">Voyages</option>
                            <option value="Déco">Déco</option>
                            <option value="Personnalisation">Personnalisation</option>
                            <option value="Autres">Autres</option>
                        </select>
                    </div>

                    <div class="grid-items">
                        <input type="submit" value="Enregistrer">
                    </div>
                </div>
            </form>
        </section>
    </section>

    <script>
        // Fonction pour afficher la prévisualisation de l'image
        function previewImage() {
            const fileInput = document.getElementById('photo_cadeau');
            const preview = document.getElementById('preview');
            const imagePreview = document.getElementById('imagePreview');

            if (fileInput.files && fileInput.files[0]) {
                const reader = new FileReader();

                reader.onload = function (e) {
                    preview.src = e.target.result;
                    imagePreview.style.display = 'block'; // Affiche la prévisualisation
                };

                reader.readAsDataURL(fileInput.files[0]);
            } else {
                preview.src = '';
                imagePreview.style.display = 'none'; // Masque la prévisualisation
            }
        }
    </script>

    <script>
        // Fonction pour vérifier si une date d'anniversaire est enregistrée en DB
        function checkBirthday() {
            const xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.hasBirthday) {
                        // Affiche le formulaire de cadeau et masque le formulaire d'anniversaire
                        document.querySelector('.form_gift').style.display = 'block';
                        document.querySelector('.form_anniv').style.display = 'none';
                    } else {
                        // Affiche le formulaire d'anniversaire et masque le formulaire de cadeau
                        document.querySelector('.form_anniv').style.display = 'block';
                        document.querySelector('.form_gift').style.display = 'none';
                    }
                }
            };

            xhr.open('GET', 'check_birthday.php', true);
            xhr.send();
        }

        // Appel de la fonction pour vérifier l'état de la date d'anniversaire lors du chargement de la page
        window.onload = checkBirthday;
    </script>
</body>
</html>
