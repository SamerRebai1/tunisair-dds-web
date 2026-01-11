<?php
require_once '../auth_check.php';
require_once '../db.php';
require_once '../UTILS/log_action.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    exit("Unauthorized.");
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0 || $id === $_SESSION['user_id']) {
    echo "❌ Invalid delete action.";
    exit();
}

// Get deleted user info for logging
$user = $mysqli->prepare("SELECT username FROM users WHERE id = ?");
$user->bind_param("i", $id);
$user->execute();
$user->bind_result($username);
$user->fetch();
$user->close();

$delete = $mysqli->prepare("DELETE FROM users WHERE id = ?");
$delete->bind_param("i", $id);

if ($delete->execute()) {
    log_action($_SESSION['user_id'], 'delete', 'users', $id, "Deleted user: $username");
    header("Location: ../manage_users.php");
} else {
    echo "❌ Delete failed: " . $delete->error;
}
$delete->close();
?>
