<!-- Modal de modification -->
<div class="modal fade" id="editModal_<?php echo $id; ?>" tabindex="-1" aria-labelledby="editModalLabel_<?php echo $id; ?>" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-dark">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel_<?php echo $id; ?>">Modifier le film "<?php echo $title; ?>"</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pb-3 pb-md-0">
                <!-- Contenu des onglets -->
                <div class="tab-content" id="editTabsContent_<?php echo $id; ?>">
                    <!-- Formulaire de modification du film -->
                    <div class="tab-pane fade show active" id="edit_form_<?php echo $id; ?>" role="tabpanel" aria-labelledby="edit_form-tab_<?php echo $id; ?>">
                        <form method="POST" action="./blocks/modifier_film_db.php" class="mt-4 text-secondary pb-2" id="edit_form_<?php echo $id; ?>">
                            <input type="hidden" name="id" value="<?php echo $id; ?>">
                            <div class="row mb-3">
                                <div class="col-md-6 mb-2 mb-md-0">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="edit_title_<?php echo $id; ?>" name="title" value="<?php echo $title; ?>" required placeholder=" ">
                                        <label for="edit_title_<?php echo $id; ?>">Titre :</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="edit_director_<?php echo $id; ?>" name="director" value="<?php echo $director; ?>" placeholder=" ">
                                        <label for="edit_director_<?php echo $id; ?>">Réalisateur :</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6 mb-2 mb-md-0">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" id="edit_release_year_<?php echo $id; ?>" name="release_year" value="<?php echo $releaseYear; ?>" placeholder=" ">
                                        <label for="edit_release_year_<?php echo $id; ?>">Année de sortie :</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="edit_external_hard_drive_<?php echo $id; ?>" name="external_hard_drive" value="<?php echo $externalHardDrive; ?>" placeholder=" ">
                                        <label for="edit_external_hard_drive_<?php echo $id; ?>">Disque dur externe :</label>
                                    </div>
                                </div>
                            </div>
                            <input type="submit" value="Enregistrer les modifications" class="btn btn-primary">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
