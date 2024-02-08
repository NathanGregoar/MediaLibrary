<!-- Modal de modification -->
<div class="modal fade" id="editModal_<?php echo $id; ?>" tabindex="-1" aria-labelledby="editModalLabel_<?php echo $id; ?>" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-dark">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel_<?php echo $id; ?>">Modifier le livre "<?php echo $title; ?>"</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pb-3 pb-md-0"> <!-- Ajout d'une classe pb-3 pour le padding en bas -->
                <!-- Formulaire de modification -->
                <form method="POST" action="./blocks/modifier_livre.php" class="mt-4 text-secondary pb-2">
                    <input type="hidden" name="id" value="<?php echo $id; ?>">
                    <div class="row mb-3">
                        <div class="col-md-6 mb-2 mb-md-0">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="edit_title_<?php echo $id; ?>" name="title" value="<?php echo $title; ?>" required placeholder=" ">
                                <label for="edit_title_<?php echo $id; ?>">Titre :</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-6 mb-2 mb-md-0">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" id="edit_volume_number_<?php echo $id; ?>" name="volume_number" value="<?php echo $volumeNumber; ?>" min="1" placeholder=" ">
                                        <label for="edit_volume_number_<?php echo $id; ?>">Numéro du tome :</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" id="edit_nombre_total_tomes_<?php echo $id; ?>" name="nombre_total_tomes" value="<?php echo $totalVolumes; ?>" min="1" placeholder=" ">
                                        <label for="edit_nombre_total_tomes_<?php echo $id; ?>">Nombre total de tomes :</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6 mb-2 mb-md-0">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="edit_auteur_<?php echo $id; ?>" name="auteur" value="<?php echo $author; ?>" placeholder=" ">
                                <label for="edit_auteur_<?php echo $id; ?>">Auteur :</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-6 mb-2 mb-md-0">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" id="edit_prix_<?php echo $id; ?>" name="prix" value="<?php echo $price; ?>" min="0" step="0.01" placeholder=" ">
                                        <label for="edit_prix_<?php echo $id; ?>">Prix :</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="edit_maison_edition_<?php echo $id; ?>" name="maison_edition" value="<?php echo $publisher; ?>" placeholder=" ">
                                        <label for="edit_maison_edition_<?php echo $id; ?>">Maison d'édition :</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12 mb-2 mb-md-0">
                            <div class="form-floating">
                                <select id="edit_format_<?php echo $id; ?>" name="format" class="form-select" placeholder=" ">
                                    <option value="broche" <?php if ($format == 'broche') echo 'selected'; ?>>Broché</option>
                                    <option value="poche" <?php if ($format == 'poche') echo 'selected'; ?>>Poche</option>
                                    <option value="ebook" <?php if ($format == 'ebook') echo 'selected'; ?>>E-book</option>
                                    <option value="relie" <?php if ($format == 'relie') echo 'selected'; ?>>Relié</option>
                                    <option value="audio" <?php if ($format == 'audio') echo 'selected'; ?>>Audio</option>
                                </select>
                                <label for="edit_format_<?php echo $id; ?>">Format :</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-floating mb-3">
                        <textarea style="height: 150px;" id="edit_resume_<?php echo $id; ?>" name="resume" class="form-control" placeholder=" "><?php echo $summary; ?></textarea>
                        <label for="edit_resume_<?php echo $id; ?>">Résumé :</label>
                    </div>
                    <input type="submit" value="Enregistrer les modifications" class="btn btn-primary">
                </form>
            </div>
        </div>
    </div>
</div>