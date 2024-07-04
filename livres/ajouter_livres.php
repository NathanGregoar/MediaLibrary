<?php
require_once '../utils/auth.php';
require_once '../utils/config.php';
include '../utils/bootstrap.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $connect = mysqli_connect($host, $username, $password, $dbName);
    if (!$connect) {
        die('Erreur de connexion à la base de données : ' . mysqli_connect_error());
    }

    // Nettoyage et validation des entrées
    $titre = isset($_POST['titre']) ? $_POST['titre'] : '';
    $auteur = isset($_POST['auteur']) ? $_POST['auteur'] : '/';
    $numeroTome = isset($_POST['numero_tome']) ? intval($_POST['numero_tome']) : 1;
    $nombreTotalTomes = isset($_POST['nombre_total_tomes']) ? intval($_POST['nombre_total_tomes']) : 1;
    $prix = isset($_POST['prix']) ? floatval($_POST['prix']) : 0;
    $format = isset($_POST['format']) ? $_POST['format'] : '/';
    $maisonEdition = isset($_POST['maison_edition']) ? $_POST['maison_edition'] : '/';
    $resume = isset($_POST['resume']) ? $_POST['resume'] : '/';
    $coverBase64 = isset($_POST['cover_base64']) ? $_POST['cover_base64'] : null;

    // Traitement de l'image de couverture
    if ($coverBase64) {
        $coverBlob = base64_to_blob($coverBase64);
    } else {
        $coverBlob = null;
    }

    // Insertion des données dans la base de données
    $loggedInUser = getLoggedInUser();

    // Vérifier si le livre existe déjà dans livres_souhaites
    $selectSql1 = "SELECT * FROM livres_souhaites WHERE titre = ? AND auteur = ? AND added_by = ?";
    $selectStmt1 = $connect->prepare($selectSql1);
    $selectStmt1->bind_param("ssi", $titre, $auteur, $loggedInUser['id']);
    $selectStmt1->execute();
    $result1 = $selectStmt1->get_result();

    // Vérifier si le livre existe déjà dans livres_possedes
    $selectSql2 = "SELECT * FROM livres_possedes WHERE titre = ? AND auteur = ? AND added_by = ?";
    $selectStmt2 = $connect->prepare($selectSql2);
    $selectStmt2->bind_param("ssi", $titre, $auteur, $loggedInUser['id']);
    $selectStmt2->execute();
    $result2 = $selectStmt2->get_result();

    if ($result1->num_rows > 0) {
        $message = '<div class="alert alert-danger">Le livre "' . $titre . '" existe déjà dans "Mes envies".</div>';
    } elseif ($result2->num_rows > 0) {
        $message = '<div class="alert alert-danger">Le livre "' . $titre . '" existe déjà dans "Ma bibliothèque".</div>';
    } else {
        // Le livre n'existe pas encore dans les tables, on peut l'ajouter
        $insertSql = "INSERT INTO livres_souhaites (titre, auteur, numero_tome, nombre_total_tomes, prix, format, maison_edition, resume_livre, added_by, cover) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $insertStmt = $connect->prepare($insertSql);
        $insertStmt->bind_param("ssiiisssis", $titre, $auteur, $numeroTome, $nombreTotalTomes, $prix, $format, $maisonEdition, $resume, $loggedInUser['id'], $coverBlob);

        if ($insertStmt->execute()) {
            $message = '<div class="alert alert-success">"' . $titre . '" a été ajouté avec succès !</div>';
        } else {
            $message = '<div class="alert alert-error">Erreur lors de l\'ajout du livre : ' . $connect->error . '</div>';
        }
    }

    $connect->close();
}

function base64_to_blob($base64_string) {
    // Supprimer la partie URL de la donnée
    $base64_string = preg_replace('/^data:image\/\w+;base64,/', '', $base64_string);
    $binary_data = base64_decode($base64_string);
    return $binary_data;
}

function url_to_base64($url) {
    $imageData = file_get_contents($url);
    $base64 = base64_encode($imageData);
    return 'data:image/jpeg;base64,' . $base64;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ajouter un Livre</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" type="text/css" href="../utils/global.css">
    <link rel="stylesheet" type="text/css" href="livres.css">
    <script src="https://unpkg.com/@zxing/library@latest"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tesseract.js@2.1.0/dist/tesseract.min.js"></script>
    <script src="./tools/code_barre.js"></script>
    <script src="./tools/auto_complete.js"></script>
    <link rel="icon" type="image/png" href="../images/icon.png">
    <link rel="manifest" href="/manifest.json">
    <style>
        .autocomplete-suggestions {
            border: 1px solid #e4e4e4;
            max-height: 200px;
            overflow-y: auto;
            position: absolute;
            background: #fff;
            z-index: 9999;
        }
        .autocomplete-suggestion {
            padding: 10px;
            cursor: pointer;
        }
        .autocomplete-suggestion:hover {
            background: #f0f0f0;
        }
        .cover-preview {
            display: none;
            justify-content: center;
            margin-bottom: 20px;
            cursor: pointer;
        }
        .cover-preview img {
            max-height: 150px;
        }
        #coverInput {
            display: none;
        }
    </style>
    <script>
        function previewCover(event) {
            const coverInput = event.target;
            const coverPreview = document.getElementById('coverPreview');
            const coverPlaceholder = 'https://e0.pxfuel.com/wallpapers/1021/882/desktop-wallpaper-dual-monitor-firewatch-wengerluggagesave-vertical-dual-monitor.jpg';

            if (coverInput.files && coverInput.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    coverPreview.src = e.target.result;
                    document.getElementById('cover_base64').value = e.target.result;
                    document.querySelector('.cover-preview').style.display = 'flex';
                }
                reader.readAsDataURL(coverInput.files[0]);
            } else {
                coverPreview.src = coverPlaceholder;
                document.getElementById('cover_base64').value = coverPlaceholder;
                document.querySelector('.cover-preview').style.display = 'flex';
            }
        }

        function updateCoverPreview() {
            const titre = document.getElementById('titre').value;
            const auteur = document.getElementById('auteur').value;

            if (titre.trim() === '' && auteur.trim() === '') {
                document.querySelector('.cover-preview').style.display = 'none';
                document.getElementById('cover_base64').value = '';
                return;
            }

            const query = `${titre} ${auteur}`;
            const url = `https://www.googleapis.com/books/v1/volumes?q=${encodeURIComponent(query)}`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    const coverPreview = document.getElementById('coverPreview');
                    let coverImage = 'https://e0.pxfuel.com/wallpapers/1021/882/desktop-wallpaper-dual-monitor-firewatch-wengerluggagesave-vertical-dual-monitor.jpg';
                    if (data.items && data.items.length > 0) {
                        const book = data.items[0];
                        coverImage = book.volumeInfo.imageLinks?.thumbnail || coverImage;
                    }
                    coverPreview.src = coverImage;
                    fetch(`blocks/url_to_base64.php?url=${encodeURIComponent(coverImage)}`)
                        .then(response => response.text())
                        .then(base64 => {
                            document.getElementById('cover_base64').value = base64;
                        });
                    document.querySelector('.cover-preview').style.display = 'flex';
                });
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('coverPreview').addEventListener('click', () => {
                document.getElementById('coverInput').click();
            });
            document.getElementById('titre').addEventListener('input', updateCoverPreview);
            document.getElementById('auteur').addEventListener('input', updateCoverPreview);
            document.getElementById('openCameraButton').addEventListener('click', openCamera);
            document.getElementById('captureButton').addEventListener('click', capturePhoto);
            document.getElementById('closeCameraButton').addEventListener('click', closeCamera);
        });
    </script>
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
                    <li class="nav-item">
                        <a class="nav-link text-warning" href="./ajouter_livres.php">Ajouter un livre</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./mes_envies.php">Mes envies</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./ma_bibliotheque.php">Ma bibliothèque</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container bg-dark text-light p-4 mt-4 rounded">
        <button id="openCamera" class="btn btn-warning float-end"><i class="bi bi-upc-scan"></i></button>
        <h1 class="display-5 text-center text-decoration-underline mb-4">Ajouter un livre</h1>
        <div id="result" class="d-flex justify-content-center"></div>
        <?php echo $message; ?>
        <div class="cover-preview">
            <img id="coverPreview" src="https://e0.pxfuel.com/wallpapers/1021/882/desktop-wallpaper-dual-monitor-firewatch-wengerluggagesave-vertical-dual-monitor.jpg" alt="Preview de la couverture">
        </div>
        <input type="file" id="coverInput" name="cover" accept="image/*" onchange="previewCover(event)">
        <form method="POST" enctype="multipart/form-data" class="mt-4 text-secondary">
            <input type="hidden" id="cover_base64" name="cover_base64" value="">
            <div class="row mb-3">
                <div class="col-md-6 mb-2 mb-md-0">
                    <div class="form-floating">
                        <input type="text" id="titre" name="titre" class="form-control" required placeholder=" ">
                        <label for="titre">Titre :</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-6 mb-2 mb-md-0">
                            <div class="form-floating">
                                <input type="number" id="numero_tome" name="numero_tome" value="1" min="1" class="form-control" placeholder=" ">
                                <label for="numero_tome">Numéro du tome :</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="number" id="nombre_total_tomes" name="nombre_total_tomes" value="1" min="1" class="form-control" placeholder=" ">
                                <label for="nombre_total_tomes">Nombre total de tomes :</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6 mb-2 mb-md-0">
                    <div class="form-floating">
                        <input type="text" id="auteur" name="auteur" class="form-control" placeholder=" " oninput="updateCoverPreview()">
                        <label for="auteur">Auteur :</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-6 mb-2 mb-md-0">
                            <div class="form-floating">
                                <input type="number" id="prix" name="prix" min="0" step="0.01" class="form-control" placeholder=" ">
                                <label for="prix">Prix :</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" id="maison_edition" name="maison_edition" class="form-control" placeholder=" ">
                                <label for="maison_edition">Maison d'édition :</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12 mb-2 mb-md-0">
                    <div class="form-floating">
                        <select id="format" name="format" class="form-select" placeholder=" ">
                            <option value="broche" selected>Broché</option>
                            <option value="poche">Poche</option>
                            <option value="ebook">E-book</option>
                            <option value="relie">Relié</option>
                            <option value="audio">Audio</option>
                        </select>
                        <label for="format">Format :</label>
                    </div>
                </div>
            </div>
            <div class="form-floating mb-3">
                <textarea style="height: 150px;" id="resume" name="resume" class="form-control" placeholder=" "></textarea>
                <label for="resume">Résumé :</label>
            </div>
            <input type="submit" value="Ajouter" class="btn btn-success">
        </form>
    </div>
</body>
</html>
