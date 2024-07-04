<?php
require_once '../../utils/config.php';

$previousPage = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
echo "URL de la page précédente: " . $previousPage . "<br>";

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
    $coverBase64 = $_POST['cover_base64']; // Nouveau champ pour l'image encodée en base64

    // Requête SQL pour mettre à jour ou insérer les données
    $sql = "UPDATE $tableName SET titre = ?, auteur = ?, numero_tome = ?, nombre_total_tomes = ?, prix = ?, format = ?, maison_edition = ?, resume_livre = ? WHERE id = ?";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("ssiiisssi", $title, $author, $volumeNumber, $totalVolumes, $price, $format, $publisher, $summary, $id);

    if ($stmt->execute()) {
        // Mettre à jour l'image de couverture si un fichier a été téléchargé
        if (!empty($coverBase64)) {
            $coverBlob = base64_to_blob($coverBase64);

            $sqlCover = "UPDATE $tableName SET cover = ? WHERE id = ?";
            $stmtCover = $connect->prepare($sqlCover);
            $stmtCover->bind_param("bi", $coverBlob, $id);
            $stmtCover->send_long_data(0, $coverBlob);
            $stmtCover->execute();
            $stmtCover->close();
        }

        header("Location: ../$redirectPage?success=1");
        exit();
    } else {
        echo "Erreur lors de l'exécution de la requête : " . $stmt->error;
    }

    $stmt->close();
}

$connect->close();

function base64_to_blob($base64_string) {
    // Remove the data URL part
    $base64_string = preg_replace('/^data:image\/\w+;base64,/', '', $base64_string);
    $binary_data = base64_decode($base64_string);
    return $binary_data;
}
?>
