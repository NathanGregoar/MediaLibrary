<?php
$id = $row['id'];
$title = $row['title'];
$langue = $row['langue_serie'];
$saisonsComplete = $row['complete_season'] ? 'Oui' : 'Non';
$nombreEpisodes = $row['episode_count'];
$nombreSaisons = $row['season_number'];
$disqueDurExterne = $row['external_hard_drive'];
?>

<div class="col">
    <div class="card h-100 d-flex flex-column">
        <div class="card-body d-flex flex-column">
            <h5 class="card-title text-center flex-grow-1 border-bottom"><?php echo $title; ?></h5>
            <p class="card-text"><strong>Langue :</strong><br> <?php echo $langue !== '/' ? $langue : 'Non spécifiée'; ?></p>
            <p class="card-text"><strong>Saison complète :</strong><br> <?php echo $saisonsComplete; ?></p>
            <p class="card-text"><strong>Nombre d'épisodes :</strong><br> <?php echo $nombreEpisodes; ?></p>
            <p class="card-text"><strong>Nombre de saisons :</strong><br> <?php echo $nombreSaisons; ?></p>
            <p class="card-text"><strong>Disque dur :</strong><br> <?php echo $disqueDurExterne !== '/' ? $disqueDurExterne : 'Non spécifié'; ?></p>
        </div>
        <div class="card-footer text-center d-flex justify-content-center gap-2">
            <!-- Bouton "Modifier" -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editModal_<?php echo $id; ?>"><i class="bi bi-pencil"></i></button>
            <!-- Formulaire de suppression -->
            <form id="deleteForm_<?php echo $id; ?>" method="POST" action="./blocks/supprimer_series.php">
                <input type="hidden" name="delete" value="<?php echo $id; ?>">
                <button type="button" class="btn btn-danger" onclick="confirmDelete('<?php echo addslashes($title); ?>', <?php echo $id; ?>)"><i class="bi bi-trash3"></i></button>
            </form>
        </div>
    </div>
</div>

<?php include 'modifier_series.php'; ?>

<script>
    function confirmDelete(title, id) {
        if (confirm("Êtes-vous sûr de vouloir supprimer la série suivante : " + title + " ?")) {
            // Si l'utilisateur confirme, soumettre le formulaire
            document.getElementById('deleteForm_' + id).submit();
        }
    }
</script>