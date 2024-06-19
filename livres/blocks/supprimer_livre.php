<?php
require_once '../../utils/auth.php';
require_once '../../utils/config.php';
include '../../utils/bootstrap.php';

$previousPage = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
echo "URL de la page précédente : " . $previousPage . "<br>"; // Affiche l'URL de la page précédente

$currentURL = basename($previousPage);
echo "URL actuelle : " . $currentURL . "<br>"; // Affiche l'URL récupérée

if (strpos($currentURL, 'mes_envies.php') !== false) {
    $tableName = "livres_souhaites";
} elseif (strpos($currentURL, 'ma_bibliotheque.php') !== false) {
    $tableName = "livres_possedes";
} else {
    // Par défaut, utiliser livres_souhaites si l'URL ne correspond à aucun des cas précédents
    $tableName = "livres_souhaites";
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    $deleteId = $_POST['delete'];
    $loggedInUser = getLoggedInUser();
    $userId = $loggedInUser['id'];

    // Vérification si le livre appartient à l'utilisateur connecté
    $checkOwnershipSql = "SELECT * FROM $tableName WHERE id = $deleteId AND added_by = $userId";
    $checkOwnershipResult = $connect->query($checkOwnershipSql);

    if ($checkOwnershipResult->num_rows > 0) {
        // Suppression du livre
        $deleteSql = "DELETE FROM $tableName WHERE id = $deleteId";
        if ($connect->query($deleteSql) === TRUE) {
            // Redirection vers la page Mes envies ou Ma bibliothèque selon l'URL précédente
            header("Location: $previousPage");
            exit();
        } else {
            echo "Erreur lors de la suppression du livre : " . $connect->error;
        }
    } else {
        echo "Ce livre n'appartient pas à l'utilisateur connecté.";
    }

    $connect->close();
} else {
    // Redirection vers la page d'accueil si le formulaire n'a pas été soumis correctement
    header("Location: ../../accueil/index.php");
    exit();
}
?>
