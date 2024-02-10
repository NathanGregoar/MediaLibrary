<?php
include_once('affiche.php');

$id = $row['id'];
$title = $row['title'];
$director = $row['director'];
$releaseYear = $row['release_year'];
$externalHardDrive = $row['external_hard_drive'];


// Récupérer l'URL de l'affiche du film
$posterUrl = getMoviePoster($title);
?>

<div class="col">
    <div class="card h-100 d-flex flex-column">
        <!-- Affiche du film -->
        <img src="<?php echo $posterUrl; ?>" class="card-img-top" alt="Affiche du film <?php echo $title; ?>">

        <div class="card-body d-flex flex-column">
            <h4 class="card-title text-center flex-grow-1 border-bottom"><?php echo $title; ?></h4>
            <p class="card-text"><strong>Réalisateur :</strong><br> <?php echo $director; ?></p>
            <p class="card-text"><strong>Année de sortie :</strong><br> <?php echo $releaseYear; ?></p>
            <p class="card-text"><strong>Disque dur externe :</strong><br> <?php echo $externalHardDrive !== '/' ? $externalHardDrive : 'Non spécifié'; ?></p>
        </div>
        <div class="card-footer text-center d-flex justify-content-center gap-2">
            <!-- Bouton "Modifier" -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editModal_<?php echo $id; ?>"><i class="bi bi-pencil"></i></button>
            <!-- Formulaire de suppression -->
            <form id="deleteForm_<?php echo $id; ?>" method="POST" action="./blocks/supprimer_film.php">
                <input type="hidden" name="delete" value="<?php echo $id; ?>">
                <button type="button" class="btn btn-danger" onclick="confirmDelete('<?php echo addslashes($title); ?>', <?php echo $id; ?>)"><i class="bi bi-trash3"></i></button>
            </form>
        </div>
    </div>
</div>

<?php include 'modifier_film.php'; ?>

<script>
    function confirmDelete(title, id) {
        if (confirm("Êtes-vous sûr de vouloir supprimer le film suivant : " + title + " ?")) {
            // Si l'utilisateur confirme, soumettre le formulaire
            document.getElementById('deleteForm_' + id).submit();
        }
    }
</script>
