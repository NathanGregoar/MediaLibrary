<?php
require_once '../../utils/config.php';

$sql = "SELECT id, role_name FROM roles";
$result = $connect->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<div class="form-check">';
        echo '<input class="form-check-input" type="checkbox" name="roles[]" value="' . $row['id'] . '" id="role_' . $row['id'] . '">';
        echo '<label class="form-check-label" for="role_' . $row['id'] . '">' . htmlspecialchars($row['role_name']) . '</label>';
        echo '</div>';
    }
} else {
    echo '<p>Aucun rôle trouvé</p>';
}
?>
