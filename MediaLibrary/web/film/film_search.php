<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <div class="container">
        <h1>Gestionnaire de films</h1>

        <?php if (!empty($successMessage)) : ?>
            <div class="alert-container">
                <div class="alert alert-success"><?php echo $successMessage; ?></div>
            </div>
        <?php endif; ?>

        <?php if (!empty($errorMessage)) : ?>
            <div class="alert-container">
                <div class="alert alert-info"><?php echo $errorMessage; ?></div>
            </div>
        <?php endif; ?>

        <div class="container_search">
            <div class="search-bar">
                <form method="POST">
                    <input type="text" name="search" placeholder="Rechercher un film...">
                    <input type="submit" value="Rechercher">
                </form>
            </div>

            <h2>Tous les films (<?php echo $numMovies; ?>) :</h2>
            <div class="movies-list">
                <?php while ($row = $moviesResult->fetch_assoc()) : ?>
                    <?php
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
                    if ($data['Response'] === 'True' && $data['Poster'] !== 'N/A') {
                        $poster = $data['Poster'];
                    } else {
                        $poster = 'placeholder.png'; // Affiche par défaut en cas d'erreur ou d'affiche indisponible
                    }
                    ?>
                    <div class="movie-item">
                        <img src="<?php echo $poster; ?>" alt="<?php echo $title; ?>">
                        <div class="movie-details">
                            <h3><?php echo $title; ?></h3>
                            <p><strong>Réalisateur :</strong> <?php echo ($director != 'NULL' ? $director : ''); ?></p>
                            <p><strong>Année de sortie :</strong> <?php echo ($releaseYear != 'NULL' ? $releaseYear : ''); ?></p>
                            <p><strong>Disque dur externe :</strong> <?php echo ($externalHardDrive != 'NULL' ? $externalHardDrive : ''); ?></p>
                        </div>
                        <div class="actions">
                            <button class="edit-btn" data-id="<?php echo $id; ?>">Modifier</button>
                            <button class="delete-btn" data-id="<?php echo $id; ?>">Supprimer</button>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

        <div class="edit-form-container">
            <div class="edit-form-box">
                <button class="close-btn">Fermer</button>
                <h2>Modifier le film</h2>
                <form id="edit-form" method="POST">
                    <input type="hidden" id="edit-movie-id" name="edit_movie_id">
                    <label for="edit-movie-title">Titre :</label>
                    <input type="text" id="edit-movie-title" name="edit_movie_title" required>
                    <label for="edit-movie-director">Réalisateur :</label>
                    <input type="text" id="edit-movie-director" name="edit_movie_director" required>
                    <label for="edit-movie-release-year">Année de sortie :</label>
                    <input type="text" id="edit-movie-release-year" name="edit_movie_release_year" required>
                    <label for="edit-movie-external-hard-drive">Disque dur externe :</label>
                    <input type="text" id="edit-movie-external-hard-drive" name="edit_movie_external_hard_drive" required>
                    <input type="submit" value="Enregistrer">
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var editFormContainer = document.querySelector(".edit-form-container");
            var editFormBox = document.querySelector(".edit-form-box");
            var closeBtn = document.querySelector(".close-btn");
            var editBtns = document.querySelectorAll(".edit-btn");
            var editForm = document.getElementById("edit-form");
            var editMovieId = document.getElementById("edit-movie-id");
            var editMovieTitle = document.getElementById("edit-movie-title");
            var editMovieDirector = document.getElementById("edit-movie-director");
            var editMovieReleaseYear = document.getElementById("edit-movie-release-year");
            var editMovieExternalHardDrive = document.getElementById("edit-movie-external-hard-drive");

            editBtns.forEach(function(btn) {
                btn.addEventListener("click", function() {
                    var movieId = btn.getAttribute("data-id");
                    var movieItem = btn.closest(".movie-item");
                    var movieTitle = movieItem.querySelector("h3").textContent;
                    var movieDirector = movieItem.querySelector("p:nth-child(2)").textContent.split(": ")[1];
                    var movieReleaseYear = movieItem.querySelector("p:nth-child(3)").textContent.split(": ")[1];
                    var movieExternalHardDrive = movieItem.querySelector("p:nth-child(4)").textContent.split(": ")[1];

                    editMovieId.value = movieId;
                    editMovieTitle.value = movieTitle;
                    editMovieDirector.value = movieDirector;
                    editMovieReleaseYear.value = movieReleaseYear;
                    editMovieExternalHardDrive.value = movieExternalHardDrive;

                    editFormContainer.style.display = "flex";
                    setTimeout(function() {
                        editFormBox.style.transform = "translateY(0)";
                    }, 10);
                });
            });

            closeBtn.addEventListener("click", function() {
                editFormBox.style.transform = "translateY(-100%)";
                setTimeout(function() {
                    editFormContainer.style.display = "none";
                }, 200);
            });
        });
    </script>
</body>

</html>
