<?php
require_once '../../utils/config.php';

if (isset($_POST['query'])) {
    $query = $_POST['query'];
    $sql = "SELECT username FROM users WHERE username LIKE ? LIMIT 10";
    $stmt = $connect->prepare($sql);
    $likeQuery = "%".$query."%";
    $stmt->bind_param("s", $likeQuery);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<a class="dropdown-item" href="#">' . htmlspecialchars($row['username']) . '</a>';
        }
    } else {
        echo '<p class="dropdown-item">Aucun utilisateur trouvé</p>';
    }

    $stmt->close();
}
?>
