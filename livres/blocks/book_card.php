<?php
$id = $row['id'];
$title = $row['titre'];
$author = $row['auteur'];
$volumeNumber = isset($row['numero_tome']) ? $row['numero_tome'] : '';
$totalVolumes = isset($row['nombre_total_tomes']) ? $row['nombre_total_tomes'] : '';
$publisher = $row['maison_edition'];
$cover = isset($row['cover']) ? $row['cover'] : null;
$price = $row['prix'];
$format = $row['format'];
$summary = $row['resume_livre'];
$theme = isset($row['theme']) ? $row['theme'] : '';
$notation_etoile = isset($row['notation_etoile']) ? $row['notation_etoile'] : '';
$commentaire = isset($row['commentaire']) ? $row['commentaire'] : '';
$favori = isset($row['favori']) && $row['favori'] == 1 ? 'checked' : '';
$notation_piments = isset($row['notation_piments']) ? $row['notation_piments'] : '';
$ecole = isset($row['ecole']) && $row['ecole'] == 1 ? 'checked' : '';

// Vérifier si une image de couverture est disponible
if ($cover) {
    $coverImage = 'data:image/jpeg;base64,' . base64_encode($cover);
} else {
    $coverImage = 'https://e0.pxfuel.com/wallpapers/1021/882/desktop-wallpaper-dual-monitor-firewatch-wengerluggagesave-vertical-dual-monitor.jpg';
}
?>

<div class="col">
    <div class="card h-100 d-flex flex-column">
        <img data-bs-toggle="modal" data-bs-target="#viewModal_<?php echo $id; ?>" src="<?php echo $coverImage; ?>" class="card-img-top lazyload" alt="Couverture du livre" style="object-fit: cover; width: 100%; height: 300px;" loading="lazy">
        <div data-bs-toggle="modal" data-bs-target="#viewModal_<?php echo $id; ?>" class="card-body d-flex flex-column">
            <h5 class="card-title text-center flex-grow-1 border-bottom"><?php echo $title; ?></h5>
            <p class="card-text"><strong>Auteur :</strong><br> <?php echo $author != '/' ? $author : ''; ?></p>
            <p class="card-text"><strong>N° de tome :</strong><br> <?php echo $volumeNumber != '' ? $volumeNumber : 'Non spécifié'; ?></p>
            <p class="card-text"><strong>Nombre total de tomes :</strong><br> <?php echo $totalVolumes != '' ? $totalVolumes : 'Non spécifié'; ?></p>
            <p class="card-text"><strong>Maison d'édition :</strong><br> <?php echo $publisher !== null ? $publisher : ''; ?></p>
        </div>
        <div class="card-footer text-center d-flex justify-content-center gap-2">
            <?php if (strpos($_SERVER['REQUEST_URI'], 'ma_bibliotheque.php') === false) { ?>
                <form method="POST" action="./blocks/ajouter_bibliotheque.php">
                    <input type="hidden" name="id" value="<?php echo $id; ?>">
                    <button type="submit" class="btn btn-warning btn-block" style="width: 40px; height: 40px;"><i class="bi bi-book"></i></button>
                </form>
            <?php } ?>
            <button type="button" class="btn btn-primary btn-block" style="width: 40px; height: 40px;" data-bs-toggle="modal" data-bs-target="#editModal_<?php echo $id; ?>"><i class="bi bi-pencil"></i></button>
            <form id="deleteForm_<?php echo $id; ?>" method="POST" action="./blocks/supprimer_livre.php">
                <input type="hidden" name="delete" value="<?php echo $id; ?>">
                <button type="button" class="btn btn-danger btn-block" style="width: 40px; height: 40px;" onclick="confirmDelete('<?php echo addslashes($title); ?>', <?php echo $id; ?>)"><i class="bi bi-trash3"></i></button>
            </form>
        </div>
    </div>
</div>

<?php include 'modifier_modale.php'; ?>
<?php include 'view_modale.php'; ?>

<script>
    function confirmDelete(title, id) {
        if (confirm("Êtes-vous sûr de vouloir supprimer le livre suivant : " + title + " ?")) {
            document.getElementById('deleteForm_' + id).submit();
        }
    }
</script>
