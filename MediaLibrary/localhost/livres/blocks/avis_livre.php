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
    
    // Nouveaux champs à insérer dans la table livres_possedes
    $theme = isset($_POST['theme']) ? $_POST['theme'] : null;
    $notation_etoile = isset($_POST['notation_etoile']) ? $_POST['notation_etoile'] : null;
    $commentaire = isset($_POST['commentaire']) ? $_POST['commentaire'] : null;
    $favori = isset($_POST['favori']) ? 1 : 0;
    $notation_piments = isset($_POST['notation_piments']) ? $_POST['notation_piments'] : null;
    $ecole = isset($_POST['ecole']) ? 1 : 0;

    // Requête SQL pour mettre à jour ou insérer les données
    $sql = "UPDATE $tableName SET theme = IFNULL(?, theme), notation_etoile = IFNULL(?, notation_etoile), commentaire = IFNULL(?, commentaire), favori = ?, notation_piments = IFNULL(?, notation_piments), ecole = ? WHERE id = ?";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("sssiisi", $theme, $notation_etoile, $commentaire, $favori, $notation_piments, $ecole, $id);

    if ($stmt->execute()) {
        header("Location: ../$redirectPage?success=1");
        exit();
    } else {
        echo "Erreur lors de l'exécution de la requête : " . $stmt->error;
    }

    $stmt->close();
}

$connect->close();
?>
