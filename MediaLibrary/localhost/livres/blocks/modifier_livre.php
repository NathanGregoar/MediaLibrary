<?php
require_once '../../utils/config.php';

$previousPage = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
echo "URL de la page précédente : " . $previousPage . "<br>";

if (strpos($previousPage, 'mes_envies.php') !== false) {
    $tableName = "livres_souhaites";
    $redirectPage = "mes_envies.php";
} elseif (strpos($previousPage, 'ma_bibliotheque.php') !== false) {
    $tableName = "livres_possedes";
    $redirectPage = "ma_bibliotheque.php";
} else {
    $tableName = "livres_souhaites";
    $redirectPage = "mes_envies.php";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $author = $_POST['auteur'];
    $volumeNumber = $_POST['volume_number'];
    $totalVolumes = $_POST['nombre_total_tomes'];
    $price = $_POST['prix'];
    $format = $_POST['format'];
    $publisher = $_POST['maison_edition'];
    $summary = $_POST['resume'];

    $updateSql = "UPDATE $tableName SET titre = ?, auteur = ?, numero_tome = ?, nombre_total_tomes = ?, prix = ?, format = ?, maison_edition = ?, resume_livre = ? WHERE id = ?";

    $stmt = $connect->prepare($updateSql);

    $stmt->bind_param("ssiiisssi", $title, $author, $volumeNumber, $totalVolumes, $price, $format, $publisher, $summary, $id);

    if ($stmt->execute()) {
        header("Location: ../$redirectPage?success=1");
        exit();
    } else {
        echo "Erreur lors de l'exécution de la requête : " . $stmt->error;
    }

    $stmt->close();
}

$connect->close();
