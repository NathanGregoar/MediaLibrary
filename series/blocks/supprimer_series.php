<?php
require_once '../../utils/auth.php';
require_once '../../utils/config.php';
include '../../utils/bootstrap.php';

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    $deleteId = $_POST['delete'];
    $loggedInUser = getLoggedInUser();
    $userId = $loggedInUser['id'];

    $_SESSION['previous_url'] = $_SERVER['HTTP_REFERER'];

    $deleteSql = "DELETE FROM series WHERE id = $deleteId AND added_by = $userId";
    if ($connect->query($deleteSql) === TRUE) {
        header("Location: " . $_SESSION['previous_url']);
        exit();
    } else {
        echo "Erreur lors de la suppression de la série : " . $connect->error;
    }

    $connect->close();
} else {
    header("Location: ../../accueil/index.php");
    exit();
}
?>
