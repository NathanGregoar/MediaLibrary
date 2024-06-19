<?php
require_once '../../utils/config.php';

if (isset($_POST['username'])) {
    $username = $_POST['username'];

    // Get user ID from username
    $selectSql = "SELECT id FROM users WHERE username = ?";
    $selectStmt = $connect->prepare($selectSql);
    $selectStmt->bind_param("s", $username);
    $selectStmt->execute();
    $result = $selectStmt->get_result();
    $user = $result->fetch_assoc();
    $userId = $user['id'];

    // Get current roles for the user
    $sql = "SELECT role_id FROM lien_users_roles WHERE user_id = ?";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $userRoles = [];
    while ($row = $result->fetch_assoc()) {
        $userRoles[] = $row['role_id'];
    }

    // Fetch all roles and check the roles assigned to the user
    $rolesSql = "SELECT id, role_name FROM roles";
    $rolesResult = $connect->query($rolesSql);

    if ($rolesResult->num_rows > 0) {
        while ($role = $rolesResult->fetch_assoc()) {
            $checked = in_array($role['id'], $userRoles) ? 'checked' : '';
            echo '<div class="form-check">';
            echo '<input class="form-check-input" type="checkbox" name="roles[]" value="' . $role['id'] . '" id="role_' . $role['id'] . '" ' . $checked . '>';
            echo '<label class="form-check-label" for="role_' . $role['id'] . '">' . htmlspecialchars($role['role_name']) . '</label>';
            echo '</div>';
        }
    } else {
        echo '<p>Aucun rôle trouvé</p>';
    }
}
?>
