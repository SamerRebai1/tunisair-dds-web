<?php
require_once 'auth_check.php';
require_once 'db.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    echo "🚫 Access denied.";
    exit();
}


$id = intval($_GET['id'] ?? 0);
if ($id <= 0 || $id == $_SESSION['user_id']) {
    echo "❌ Invalid operation.";
    exit();
}

// Optional: prevent deleting the last admin
$adminCheck = $mysqli->query("SELECT COUNT(*) AS total FROM users WHERE role_ = 'admin'");
$adminCount = $adminCheck->fetch_assoc()['total'];
$isTargetAdmin = $mysqli->query("SELECT role_ FROM users WHERE id = $id")->fetch_assoc()['role_'];

if ($isTargetAdmin === 'admin' && $adminCount <= 1) {
    echo "⚠️ Cannot delete the last admin.";
    exit();
}

$delete = $mysqli->prepare("DELETE FROM users WHERE id = ?");
$delete->bind_param("i", $id);
if ($delete->execute()) {
    header("Location: manage_users.php");
    exit();
} else {
    echo "❌ Error deleting user: " . $delete->error;
}
?>
