<?php
require_once '../../utils/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $langue = $_POST['langue_serie'];
    $completeSeason = isset($_POST['complete_season']) ? 1 : 0;
    $seasonNumber = $_POST['season_number'];
    $episodeCount = $_POST['episode_count'];
    $externalHardDrive = $_POST['external_hard_drive'];

    // Requête SQL pour mettre à jour ou insérer les données
    $sql = "UPDATE series SET title = ?, langue_serie = ?, complete_season = ?, season_number = ?, episode_count = ?, external_hard_drive = ? WHERE id = ?";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("ssiisii", $title, $langue, $completeSeason, $seasonNumber, $episodeCount, $externalHardDrive, $id);

    if ($stmt->execute()) {
        header("Location: ../mes_series.php?success=1");
        exit();
    } else {
        echo "Erreur lors de l'exécution de la requête : " . $stmt->error;
    }

    $stmt->close();
}

$connect->close();
?>
