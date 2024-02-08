<?php
$id = $row['id'];
$title = $row['titre'];
$author = $row['auteur'];
$volumeNumber = isset($row['numero_tome']) ? $row['numero_tome'] : '';
$totalVolumes = isset($row['nombre_total_tomes']) ? $row['nombre_total_tomes'] : '';
$publisher = $row['maison_edition'];
$bookCover = getBookCover($title, $author);
$price = $row['prix'];
$format = $row['format'];
$summary = $row['resume_livre'];
?>


<div class="col">
    <div class="card h-100 d-flex flex-column">
        <img src="<?php echo $bookCover ? $bookCover : 'https://e0.pxfuel.com/wallpapers/1021/882/desktop-wallpaper-dual-monitor-firewatch-wengerluggagesave-vertical-dual-monitor.jpg'; ?>" class="card-img-top lazyload" alt="Couverture du livre" style="object-fit: cover; width: 100%; height: 300px;" loading="lazy">
        <div class="card-body d-flex flex-column">
            <h5 class="card-title text-center flex-grow-1 border-bottom"><?php echo $title; ?></h5>
            <p class="card-text"><strong>Auteur :<br></strong> <?php echo $author != '/' ? $author : ''; ?></p>
            <p class="card-text"><strong>N° de tome :<br></strong> <?php echo $volumeNumber != '' ? $volumeNumber : 'Non spécifié'; ?></p>
            <p class="card-text"><strong>Nombre total de tomes :<br></strong> <?php echo $totalVolumes != '' ? $totalVolumes : 'Non spécifié'; ?></p>
            <p class="card-text"><strong>Maison d'édition :<br></strong> <?php echo $publisher !== null ? $publisher : ''; ?></p>
        </div>
        <div class="card-footer text-center d-flex justify-content-center gap-2">
            <!-- Bouton "Bibliothèque" -->
            <?php
            // Vérifier si l'URL contient ma_bibliotheque.php
            if (strpos($_SERVER['REQUEST_URI'], 'ma_bibliotheque.php') === false) {
                // Le formulaire sera affiché seulement si l'URL ne contient pas ma_bibliotheque.php
            ?>
                <form method="POST" action="./blocks/ajouter_bibliotheque.php">
                    <input type="hidden" name="id" value="<?php echo $id; ?>">
                    <button type="submit" class="btn btn-warning"><i class="bi bi-book"></i></button>
                </form>
            <?php
            }
            ?>
            <!-- Bouton "Modifier" -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editModal_<?php echo $id; ?>"><i class="bi bi-pencil"></i></button>
            <!-- Formulaire de suppression -->
            <form method="POST" action="supprimer_livre.php">
                <input type="hidden" name="delete" value="<?php echo $id; ?>">
                <button type="button" class="btn btn-danger" onclick="confirmDelete('<?php echo addslashes($title); ?>')"><i class="bi bi-trash3"></i></button>
            </form>
        </div>
    </div>
</div>


<?php include 'modifier_modale.php'; ?>

<script>
    function confirmDelete(title) {
        if (confirm("Êtes-vous sûr de vouloir supprimer le livre suivant : " + title + " ?")) {
            // Si l'utilisateur confirme, soumettre le formulaire
            event.target.closest('form').submit();
        }
    }
</script>