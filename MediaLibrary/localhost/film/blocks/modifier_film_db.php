<?php
require_once '../../utils/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $director = $_POST['director'];
    $releaseYear = $_POST['release_year'];
    $externalHardDrive = $_POST['external_hard_drive'];

    // Requête SQL pour mettre à jour ou insérer les données
    $sql = "UPDATE films SET title = ?, director = ?, release_year = ?, external_hard_drive = ? WHERE id = ?";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("ssisi", $title, $director, $releaseYear, $externalHardDrive, $id);

    if ($stmt->execute()) {
        header("Location: ../mes_films.php?success=1");
        exit();
    } else {
        echo "Erreur lors de l'exécution de la requête : " . $stmt->error;
    }

    $stmt->close();
}

$connect->close();
?>
