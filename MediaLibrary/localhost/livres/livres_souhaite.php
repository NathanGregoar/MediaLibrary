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
    <div class="navbar">
        <a href="../accueil/index.php">Accueil</a>
        <a href="./livres_souhaite.php">Ajouter un Livre Souhaité</a>
        <a href="./livres_souhaite_search.php">Consulter les Livres Souhaités</a>
        <a href="./livres_possede.php">Consulter les Livres Possédés</a>
    </div>

    <h1>Ajouter un Livre Souhaité</h1>
    <div class="container">
        <?php
        $connect = mysqli_connect($host, $username, $password, $dbName);

        if (!$connect) {
            die('Erreur de connexion à la base de données : ' . mysqli_connect_error());
        }

        // Ajout d'un livre souhaité
        if (isset($_POST['titre'])) {
            $titre = $connect->real_escape_string($_POST['titre']);
            $auteur = $_POST['auteur'] != '' ? $connect->real_escape_string($_POST['auteur']) : 'NULL';

            // Récupérer le nombre total de tomes de la série
            $query = "SELECT COUNT(*) AS total_tomes FROM livres_souhaites WHERE titre = '$titre'";
            $result = mysqli_query($connect, $query);
            $row = mysqli_fetch_assoc($result);
            $nombreTotalTomes = $row['total_tomes'];

            $numeroTome = $_POST['numero_tome'] != '' ? intval($_POST['numero_tome']) : 1;
            $prix = $_POST['prix'] != '' ? floatval($_POST['prix']) : 0.00;
            $format = $_POST['format'] != '' ? $connect->real_escape_string($_POST['format']) : 'NULL';
            $maisonEdition = $_POST['maison_edition'] != '' ? $connect->real_escape_string($_POST['maison_edition']) : 'NULL';
            $resume = $_POST['resume'] != '' ? $connect->real_escape_string($_POST['resume']) : 'NULL';

            // Récupérer l'ID de l'utilisateur connecté à partir des informations de session
            $loggedInUser = getLoggedInUser();

            $insertSql = "INSERT INTO livres_souhaites (titre, auteur, numero_tome, nombre_total_tomes, prix, format, maison_edition, resume_livre, added_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $insertStmt = $connect->prepare($insertSql);
            $insertStmt->bind_param("ssiidsssi", $titre, $auteur, $numeroTome, $nombreTotalTomes, $prix, $format, $maisonEdition, $resume, $loggedInUser['id']);

            if ($insertStmt->execute()) {
                echo '<div class="alert alert-success">Livre souhaité ajouté avec succès !</div>';
            } else {
                echo '<div class="alert alert-error">Erreur lors de l\'ajout du livre souhaité : ' . $connect->error . '</div>';
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
                <option value="broche" selected>Broché</option>
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

        <div style="position: relative;">
            <button id="scanButton">Scanner le code-barres</button>
            <button id="closeButton">Fermer</button>
            <select id="cameraSelect"></select>
            <div id="video-container"></div>
        </div>

        <?php
        $connect->close();
        ?>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#titre').blur(function() {
                    var titre = $(this).val();
                    if (titre !== '') {
                        $.ajax({
                            url: 'https://www.googleapis.com/books/v1/volumes',
                            data: {
                                q: 'intitle:' + titre,
                                maxResults: 1
                            },
                            dataType: 'json',
                            success: function(data) {
                                if (data.totalItems > 0) {
                                    var book = data.items[0];
                                    $('#auteur').val(book.volumeInfo.authors ? book.volumeInfo.authors[0] : '');
                                    $('#prix').val(book.saleInfo.listPrice ? book.saleInfo.listPrice.amount : 0);
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
                            data: {
                                q: 'intitle:' + titre + '+inauthor:' + auteur,
                                maxResults: 1
                            },
                            dataType: 'json',
                            success: function(data) {
                                if (data.totalItems > 0) {
                                    var book = data.items[0];
                                    $('#maison_edition').val(book.volumeInfo.publisher ? book.volumeInfo.publisher : '');
                                    $('#resume').val(book.volumeInfo.description ? book.volumeInfo.description : '');
                                }
                            }
                        });
                    }
                });
            });
        </script>


        <script src="https://rawgit.com/serratus/quaggaJS/master/dist/quagga.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/quagga@latest/dist/quagga.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

        <script>
            var lastScannedBarcode = null;
            var videoStream;
            var cameras;

            // Récupérer la liste des périphériques multimédias
            navigator.mediaDevices.enumerateDevices()
                .then(function(devices) {
                    var cameraSelect = document.getElementById('cameraSelect');

                    // Filtrer les périphériques pour obtenir uniquement les caméras
                    cameras = devices.filter(function(device) {
                        return device.kind === 'videoinput';
                    });

                    // Remplir le menu déroulant avec les options des caméras disponibles
                    cameras.forEach(function(camera, index) {
                        var option = document.createElement('option');
                        option.value = index;
                        option.text = camera.label || 'Caméra ' + (index + 1);
                        cameraSelect.appendChild(option);
                    });
                })
                .catch(function(error) {
                    console.error('Erreur lors de la récupération des périphériques multimédias :', error);
                });

            document.getElementById('scanButton').addEventListener('click', function() {
                var cameraSelect = document.getElementById('cameraSelect');
                var selectedCameraIndex = cameraSelect.value;

                if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                    // Définir les contraintes de la caméra en utilisant la caméra sélectionnée
                    var constraints = {
                        video: {
                            facingMode: 'environment',
                            deviceId: {
                                exact: cameras[selectedCameraIndex].deviceId
                            },
                            focusMode: 'continuous'
                        }
                    };

                    navigator.mediaDevices.getUserMedia(constraints)
                        .then(function(stream) {
                            videoStream = stream;

                            var video = document.createElement('video');
                            video.setAttribute('id', 'barcode-video');
                            document.getElementById('video-container').appendChild(video);
                            video.srcObject = stream;
                            video.play();

                            Quagga.init({
                                inputStream: {
                                    name: 'Live',
                                    type: 'LiveStream',
                                    target: document.getElementById('barcode-video'),
                                },
                                decoder: {
                                    readers: ['code_128_reader', 'ean_reader', 'upc_reader', 'code_39_reader', 'code_39_vin_reader', 'codabar_reader', 'i2of5_reader', '2of5_reader', 'code_93_reader'],
                                },
                            }, function(err) {
                                if (err) {
                                    console.error('Erreur lors de l\'initialisation de QuaggaJS :', err);
                                    return;
                                }

                                Quagga.onDetected(function(result) {
                                    var barcode = result.codeResult.code;

                                    if (barcode !== lastScannedBarcode) {
                                        lastScannedBarcode = barcode;

                                        // Afficher le numéro de code-barres en bas de la page
                                        var resultDisplay = document.getElementById('resultDisplay');
                                        resultDisplay.textContent = 'Code-barres scanné : ' + barcode;

                                        $.ajax({
                                            url: 'https://www.googleapis.com/books/v1/volumes?q=isbn:' + barcode,
                                            dataType: 'json',
                                            success: function(data) {
                                                if (data.totalItems > 0) {
                                                    var bookInfo = data.items[0].volumeInfo;

                                                    console.log(bookInfo);

                                                    $('#titre').val(bookInfo.title || '');
                                                    $('#auteur').val(bookInfo.authors ? bookInfo.authors[0] : '');
                                                    $('#maison_edition').val(bookInfo.publisher || '');
                                                    $('#resume').val(bookInfo.description || '');
                                                    $('#prix').val(bookInfo.saleInfo && bookInfo.saleInfo.listPrice ? bookInfo.saleInfo.listPrice.amount : 0);
                                                } else {
                                                    console.log('Aucunes infos');
                                                }
                                            },
                                            error: function() {
                                                console.error('Erreur lors de la requête à l\'API Google Books.');
                                            }
                                        });
                                    }
                                });

                                Quagga.start();
                            });
                        })
                        .catch(function(error) {
                            console.error('Erreur lors de l\'accès à la caméra :', error);
                        });
                } else {
                    console.error('Votre navigateur ne prend pas en charge WebRTC');
                }
            });

            document.getElementById('closeButton').addEventListener('click', function() {
                if (videoStream) {
                    var tracks = videoStream.getTracks();
                    tracks.forEach(track => track.stop());
                    videoStream = null;
                }

                Quagga.stop();

                var videoElement = document.getElementById('barcode-video');
                if (videoElement) {
                    videoElement.parentNode.removeChild(videoElement);
                }
            });
        </script>






    </div>

    <div id="resultDisplay"></div>
</body>

</html>