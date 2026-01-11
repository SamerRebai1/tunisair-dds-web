<?php
require_once '../auth_check.php';
require_once '../db.php';
require_once '../UTILS/log_action.php';


$user_id = intval($_POST['id']);
$full_name = trim($_POST['full_name']);
$email = trim($_POST['email']);
$phone = trim($_POST['phone']);
$role = trim($_POST['role_']);

// Prevent changing your own role
if ($user_id === $_SESSION['user_id']) {
    echo "⚠️ You cannot change your own role.";
    exit();
}

$update = $mysqli->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, role_ = ? WHERE id = ?");
$update->bind_param("ssssi", $full_name, $email, $phone, $role, $user_id);

if ($update->execute()) {
    log_action($_SESSION['user_id'], 'update', 'users', $user_id, "Updated user: $full_name, role: $role");
    echo "✅ User updated successfully.";
} else {
    echo "❌ Update failed: " . $update->error;
}
$update->close();
?>
