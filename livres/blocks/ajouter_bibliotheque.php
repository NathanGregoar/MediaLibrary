<?php
require_once '../../utils/config.php';
require_once '../../utils/auth.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    global $connect;

    $id = $_POST['id'];
    $sql_select_info = "SELECT titre, auteur, numero_tome, nombre_total_tomes, prix, format, maison_edition, resume_livre, cover FROM livres_souhaites WHERE id = ?";
    $stmt_select_info = mysqli_prepare($connect, $sql_select_info);
    mysqli_stmt_bind_param($stmt_select_info, 'i', $id);
    mysqli_stmt_execute($stmt_select_info);
    mysqli_stmt_store_result($stmt_select_info);
    mysqli_stmt_bind_result($stmt_select_info, $title, $author, $volumeNumber, $totalVolumes, $price, $format, $publisher, $summary, $cover);
    mysqli_stmt_fetch($stmt_select_info);
    mysqli_stmt_close($stmt_select_info);

    $loggedInUser = getLoggedInUser();

    if ($loggedInUser) {
        $added_by = $loggedInUser['id'];
    } else {
        header("Location: ../login.php");
        exit();
    }

    $sql_insert = "INSERT INTO livres_possedes (titre, auteur, numero_tome, nombre_total_tomes, prix, format, maison_edition, resume_livre, cover, added_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt_insert = mysqli_prepare($connect, $sql_insert);
    mysqli_stmt_bind_param($stmt_insert, 'ssiiissssi', $title, $author, $volumeNumber, $totalVolumes, $price, $format, $publisher, $summary, $cover, $added_by);

    try {
        if (mysqli_stmt_execute($stmt_insert)) {
            $sql_delete = "DELETE FROM livres_souhaites WHERE id = ?";
            $stmt_delete = mysqli_prepare($connect, $sql_delete);
            mysqli_stmt_bind_param($stmt_delete, 'i', $id);
            mysqli_stmt_execute($stmt_delete);
            mysqli_stmt_close($stmt_delete);

            header("Location: ../mes_envies.php");
            exit();
        } else {
            echo "Une erreur s'est produite lors de l'ajout du livre dans la bibliothèque.";
        }
    } catch (Exception $e) {
        echo "Erreur d'exécution de la requête : " . $e->getMessage();
        exit();
    }
}
?>
