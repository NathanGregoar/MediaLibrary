<!-- View Modale -->
<div class="modal fade" id="viewModal_<?php echo $id; ?>" tabindex="-1" aria-labelledby="editModalLabel_<?php echo $id; ?>" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-dark">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel_<?php echo $id; ?>">Détails du livre "<?php echo $title; ?>"</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pb-3 pb-md-0">
                <div class="row">
                    <div class="col-md-2">
                        <img data-bs-toggle="modal" data-bs-target="#viewModal_<?php echo $id; ?>" src="<?php echo $coverImage; ?>" class="card-img-top lazyload" alt="Couverture du livre" style="object-fit: cover; max-height: 200px;" loading="lazy">
                    </div>
                    <div class="col-md-5">
                        <p><strong>Titre :</strong> <?php echo $title; ?></p>
                        <p><strong>Auteur :</strong> <?php echo $author != '/' ? $author : 'Non spécifié'; ?></p>
                        <p><strong>N° de tome :</strong> <?php echo $volumeNumber != '' ? $volumeNumber : 'Non spécifié'; ?></p>
                        <p><strong>Nombre de tomes :</strong> <?php echo $totalVolumes != '' ? $totalVolumes : 'Non spécifié'; ?></p>
                    </div>
                    <div class="col-md-5">
                        <p><strong>Maison d'édition :</strong> <?php echo $publisher !== null ? $publisher : 'Non spécifié'; ?></p>
                        <p><strong>Prix :</strong> <?php echo $price != '' ? $price . ' EUR' : 'Non spécifié'; ?></p>
                        <p><strong>Format :</strong> <?php echo $format != '' ? $format : 'Non spécifié'; ?></p>
                        <p><strong>Thème :</strong> <?php echo $theme != '' ? $theme : 'Non spécifié'; ?></p>
                    </div>
                </div>
                <!-- Résumé dans un bloc séparé avec hauteur maximale et possibilité de scroll -->
                <div class="mb-3">
                    <p><strong>Résumé :</strong></p>
                    <div style="max-height: 200px; overflow-y: auto;">
                        <p><?php echo $summary != '' ? $summary : 'Aucun résumé disponible'; ?></p>
                    </div>
                </div>
                <!-- Ligne séparatrice -->
                <hr>
                <?php if (strpos($_SERVER['REQUEST_URI'], 'ma_bibliotheque.php') !== false) { ?>
                    <!-- Affichage des éléments de thème à école uniquement si l'URL contient "ma_bibliotheque.php" -->
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Note :</strong>
                                <?php
                                if ($notation_etoile != '') {
                                    $stars_count = min(5, $notation_etoile);
                                    for ($i = 0; $i < $stars_count; $i++) {
                                        echo '<img src="https://cdn-icons-png.flaticon.com/512/541/541415.png" style="max-height: 30px;" alt="Étoile">';
                                    }
                                    $empty_stars_count = max(0, 5 - $notation_etoile);
                                    for ($i = 0; $i < $empty_stars_count; $i++) {
                                        echo '<img src="https://cdn.iconscout.com/icon/free/png-256/free-star-3661048-3095468.png" style="max-height: 30px;" alt="Étoile vide">';
                                    }
                                } else {
                                    echo 'Non spécifiée';
                                }
                                ?>
                            </p>
                            <p><strong>Spicy :</strong> <?php echo $notation_piments != '' ? str_repeat('<img src="https://upload.wikimedia.org/wikipedia/commons/2/2b/Hot_chili_red_pepper_icon_emote.png" style="max-height: 30px;" alt="Piment">', $notation_piments) . str_repeat('<img src="https://cdn-icons-png.flaticon.com/512/3412/3412950.png" style="max-height: 30px;" alt="Non">', 5 - $notation_piments) : 'Non spécifiée'; ?></p>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Favori :</strong> <?php echo $favori == 'checked' ? '<img src="https://cdn-icons-png.flaticon.com/512/9484/9484251.png" style="max-height: 30px;" alt="Oui">' : '<img src="https://cdn-icons-png.flaticon.com/512/2/2267.png" style="max-height: 30px;" alt="Non">'; ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Ecole :</strong> <?php echo $ecole == 'checked' ? '<img src="https://cdn-icons-png.flaticon.com/512/5310/5310672.png" style="max-height: 30px;" alt="Oui">' : '<img src="https://cdn-icons-png.flaticon.com/512/4615/4615221.png" style="max-height: 30px;" alt="Non">'; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Commentaire :</strong></p>
                            <div style="max-height: 200px; overflow-y: auto;">
                                <p><?php echo $commentaire != '' ? $commentaire : 'Aucun commentaire'; ?></p>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>