<!-- Modal de modification -->
<div class="modal fade" id="editModal_<?php echo $id; ?>" tabindex="-1" aria-labelledby="editModalLabel_<?php echo $id; ?>" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-dark">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel_<?php echo $id; ?>">Modifier le livre "<?php echo $title; ?>"</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pb-3 pb-md-0">
                <!-- Onglets pour basculer entre les formulaires -->
                <?php if (strpos($_SERVER['REQUEST_URI'], 'ma_bibliotheque.php') !== false) { ?>
                    <ul class="nav nav-tabs mb-3" id="editTabs_<?php echo $id; ?>" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="form_non_lu-tab_<?php echo $id; ?>" data-bs-toggle="tab" data-bs-target="#form_non_lu_<?php echo $id; ?>" type="button" role="tab" aria-controls="form_non_lu_<?php echo $id; ?>" aria-selected="true">Mes Envies</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="form_lu-tab_<?php echo $id; ?>" data-bs-toggle="tab" data-bs-target="#form_lu_<?php echo $id; ?>" type="button" role="tab" aria-controls="form_lu_<?php echo $id; ?>" aria-selected="false">Ma Bibliothèque</button>
                        </li>
                    </ul>
                <?php } ?>

                <!-- Contenu des onglets -->
                <div class="tab-content" id="editTabsContent_<?php echo $id; ?>">
                    <!-- Formulaire de modification dans Mes Envies -->
                    <div class="tab-pane fade show active" id="form_non_lu_<?php echo $id; ?>" role="tabpanel" aria-labelledby="form_non_lu-tab_<?php echo $id; ?>">
                        <form method="POST" action="./blocks/modifier_livre.php" class="mt-4 text-secondary pb-2" id="form_non_lu">
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

                    <!-- Formulaire de modification dans Ma Bibliothèque -->
                    <?php if (strpos($_SERVER['REQUEST_URI'], 'ma_bibliotheque.php') !== false) { ?>
                        <div class="tab-pane fade" id="form_lu_<?php echo $id; ?>" role="tabpanel" aria-labelledby="form_lu-tab_<?php echo $id; ?>">
                            <form method="POST" action="./blocks/avis_livre.php" class="mt-4 text-secondary pb-2" id="form_lu">
                                <input type="hidden" name="id" value="<?php echo $id; ?>">
                                <div class="row mb-3">
                                    <div class="col-md-4 pb-3">
                                        <div class="d-flex justify-content-center"> <!-- Ajout de cette classe -->
                                            <div>
                                                <!-- <label for="edit_notation_etoile_<?php echo $id; ?>">Évaluation :</label><br> -->
                                                <div class="notation_etoile">
                                                    <input type="radio" id="star1_<?php echo $id; ?>" name="notation_etoile" value="1" style="display: none;" <?php if ($notation_etoile == 1) echo 'checked'; ?>>
                                                    <label for="star1_<?php echo $id; ?>"><img src="https://cdn.iconscout.com/icon/free/png-256/free-star-3661048-3095468.png" alt="1" style="max-width: 35px;"></label>
                                                    <input type="radio" id="star2_<?php echo $id; ?>" name="notation_etoile" value="2" style="display: none;" <?php if ($notation_etoile == 2) echo 'checked'; ?>>
                                                    <label for="star2_<?php echo $id; ?>"><img src="https://cdn.iconscout.com/icon/free/png-256/free-star-3661048-3095468.png" alt="2" style="max-width: 35px;"></label>
                                                    <input type="radio" id="star3_<?php echo $id; ?>" name="notation_etoile" value="3" style="display: none;" <?php if ($notation_etoile == 3) echo 'checked'; ?>>
                                                    <label for="star3_<?php echo $id; ?>"><img src="https://cdn.iconscout.com/icon/free/png-256/free-star-3661048-3095468.png" alt="3" style="max-width: 35px;"></label>
                                                    <input type="radio" id="star4_<?php echo $id; ?>" name="notation_etoile" value="4" style="display: none;" <?php if ($notation_etoile == 4) echo 'checked'; ?>>
                                                    <label for="star4_<?php echo $id; ?>"><img src="https://cdn.iconscout.com/icon/free/png-256/free-star-3661048-3095468.png" alt="4" style="max-width: 35px;"></label>
                                                    <input type="radio" id="star5_<?php echo $id; ?>" name="notation_etoile" value="5" style="display: none;" <?php if ($notation_etoile == 5) echo 'checked'; ?>>
                                                    <label for="star5_<?php echo $id; ?>"><img src="https://cdn.iconscout.com/icon/free/png-256/free-star-3661048-3095468.png" alt="5" style="max-width: 35px;"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 pb-3">
                                        <div class="d-flex justify-content-center"> <!-- Ajout de cette classe -->
                                            <div>
                                                <!-- <label for="notation_piments_<?php echo $id; ?>">Notation Piments :</label><br> -->
                                                <div class="notation_etoile">
                                                    <input type="radio" id="piment1_<?php echo $id; ?>" name="notation_piments" value="1" style="display: none;" <?php if ($notation_piments == 1) echo 'checked'; ?>>
                                                    <label for="piment1_<?php echo $id; ?>"><img src="https://cdn-icons-png.flaticon.com/512/3412/3412950.png" alt="1" style="max-width: 35px;"></label>
                                                    <input type="radio" id="piment2_<?php echo $id; ?>" name="notation_piments" value="2" style="display: none;" <?php if ($notation_piments == 2) echo 'checked'; ?>>
                                                    <label for="piment2_<?php echo $id; ?>"><img src="https://cdn-icons-png.flaticon.com/512/3412/3412950.png" alt="2" style="max-width: 35px;"></label>
                                                    <input type="radio" id="piment3_<?php echo $id; ?>" name="notation_piments" value="3" style="display: none;" <?php if ($notation_piments == 3) echo 'checked'; ?>>
                                                    <label for="piment3_<?php echo $id; ?>"><img src="https://cdn-icons-png.flaticon.com/512/3412/3412950.png" alt="3" style="max-width: 35px;"></label>
                                                    <input type="radio" id="piment4_<?php echo $id; ?>" name="notation_piments" value="4" style="display: none;" <?php if ($notation_piments == 4) echo 'checked'; ?>>
                                                    <label for="piment4_<?php echo $id; ?>"><img src="https://cdn-icons-png.flaticon.com/512/3412/3412950.png" alt="4" style="max-width: 35px;"></label>
                                                    <input type="radio" id="piment5_<?php echo $id; ?>" name="notation_piments" value="5" style="display: none;" <?php if ($notation_piments == 5) echo 'checked'; ?>>
                                                    <label for="piment5_<?php echo $id; ?>"><img src="https://cdn-icons-png.flaticon.com/512/3412/3412950.png" alt="5" style="max-width: 35px;"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 pb-3">
                                        <div class="d-flex justify-content-center"> <!-- Ajout de cette classe -->
                                            <div class="form-check">
                                                <input style="display: none;" type="checkbox" id="favori_<?php echo $id; ?>" name="favori" class="form-check-input" <?php if ($favori) echo 'checked'; ?>>
                                                <label for="favori_<?php echo $id; ?>" class="form-check-label">
                                                    <img id="icon_<?php echo $id; ?>" src="<?php echo $favori ? 'https://cdn-icons-png.flaticon.com/512/9484/9484251.png' : 'https://cdn-icons-png.flaticon.com/512/2/2267.png'; ?>" width="35" height="35">
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 pb-3">
                                        <div class="d-flex justify-content-center"> <!-- Ajout de cette classe -->
                                            <div class="form-check">
                                                <input type="checkbox" id="ecole_<?php echo $id; ?>" name="ecole" class="form-check-input" style="display: none;" <?php if ($ecole) echo 'checked'; ?>>
                                                <label for="ecole_<?php echo $id; ?>" class="form-check-label">
                                                    <img id="ecole_icon_<?php echo $id; ?>" src="<?php echo $ecole ? 'https://cdn-icons-png.flaticon.com/512/5310/5310672.png' : 'https://cdn-icons-png.flaticon.com/512/4615/4615221.png'; ?>" width="35" height="35">
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6 pb-3">
                                        <div class="form-floating">
                                            <input type="text" id="edit_theme_<?php echo $id; ?>" name="theme" class="form-control" value="<?php echo $theme; ?>" placeholder="Thème">
                                            <label for="edit_theme_<?php echo $id; ?>">Thème :</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pb-3">
                                        <div class="form-floating">
                                            <textarea id="commentaire_<?php echo $id; ?>" name="commentaire" class="form-control" placeholder=" "><?php echo $commentaire; ?></textarea>
                                            <label for="commentaire_<?php echo $id; ?>">Commentaire :</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <div class="d-flex justify-content-center"> <!-- Ajout de cette classe -->
                                            <input type="submit" value="Enregistrer les modifications" class="btn btn-primary">
                                        </div>
                                    </div>
                                </div>
                            </form>

                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        updateImages_<?php echo $id; ?>();
        updateEvaluationImages_<?php echo $id; ?>();
    });

    function toggleTabs_<?php echo $id; ?>(tabId) {
        var tab = document.getElementById(tabId);
        var tabContent = document.getElementById('editTabsContent_<?php echo $id; ?>');
        var activeTab = tabContent.querySelector('.tab-pane.fade.show.active');
        activeTab.classList.remove('show', 'active');
        tab.classList.add('show', 'active');
    }

    document.getElementById('favori_<?php echo $id; ?>').addEventListener('change', function() {
        var icon = document.getElementById('icon_<?php echo $id; ?>');
        if (this.checked) {
            icon.src = 'https://cdn-icons-png.flaticon.com/512/9484/9484251.png';
        } else {
            icon.src = 'https://cdn-icons-png.flaticon.com/512/2/2267.png';
        }
    });

    const ecoleCheckbox_<?php echo $id; ?> = document.getElementById('ecole_<?php echo $id; ?>');
    const ecoleIcon_<?php echo $id; ?> = document.getElementById('ecole_icon_<?php echo $id; ?>');

    ecoleCheckbox_<?php echo $id; ?>.addEventListener('change', function() {
        if (this.checked) {
            ecoleIcon_<?php echo $id; ?>.src = 'https://cdn-icons-png.flaticon.com/512/5310/5310672.png';
        } else {
            ecoleIcon_<?php echo $id; ?>.src = 'https://cdn-icons-png.flaticon.com/512/4615/4615221.png';
        }
    });

    function updateImages_<?php echo $id; ?>() {
        let checkedInput = document.querySelector('#editModal_<?php echo $id; ?> input[name="notation_piments"]:checked');
        if (checkedInput) {
            let inputs = document.querySelectorAll('#editModal_<?php echo $id; ?> input[name="notation_piments"]');
            for (let i = 0; i < inputs.length; i++) {
                if (inputs[i] === checkedInput) {
                    for (let j = 0; j <= i; j++) {
                        inputs[j].nextElementSibling.querySelector('img').src = "https://upload.wikimedia.org/wikipedia/commons/2/2b/Hot_chili_red_pepper_icon_emote.png";
                    }
                } else {
                    inputs[i].nextElementSibling.querySelector('img').src = "https://cdn-icons-png.flaticon.com/512/3412/3412950.png";
                }
            }
        }
    }

    document.querySelectorAll('#editModal_<?php echo $id; ?> input[name="notation_piments"]').forEach((input) => {
        input.addEventListener('change', function() {
            updateImages_<?php echo $id; ?>();
        });
    });

    function updateEvaluationImages_<?php echo $id; ?>() {
        let checkedInput = document.querySelector('#editModal_<?php echo $id; ?> input[name="notation_etoile"]:checked');
        if (checkedInput) {
            let inputs = document.querySelectorAll('#editModal_<?php echo $id; ?> input[name="notation_etoile"]');
            for (let i = 0; i < inputs.length; i++) {
                if (inputs[i] === checkedInput) {
                    for (let j = 0; j <= i; j++) {
                        inputs[j].nextElementSibling.querySelector('img').src = "https://cdn-icons-png.flaticon.com/512/541/541415.png";
                    }
                } else {
                    inputs[i].nextElementSibling.querySelector('img').src = "https://cdn.iconscout.com/icon/free/png-256/free-star-3661048-3095468.png";
                }
            }
        }
    }

    document.querySelectorAll('#editModal_<?php echo $id; ?> input[name="notation_etoile"]').forEach((input) => {
        input.addEventListener('change', function() {
            updateEvaluationImages_<?php echo $id; ?>();
        });
    });
</script>