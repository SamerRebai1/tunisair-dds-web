<?php
require_once '../auth_check.php';
require_once '../db.php';
require_once '../UTILS/log_action.php';



if ($_SESSION['role'] !== 'admin') {
    echo "🚫 Admin access only.";
    exit();
}

$user_id = $_SESSION['user_id'];
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    echo "❌ Invalid defect ID.";
    exit();
}

// Optionally fetch more info for logging
$stmtInfo = $mysqli->prepare("SELECT numero_dds FROM defaut WHERE id_defaut = ?");
$stmtInfo->bind_param("i", $id);
$stmtInfo->execute();
$res = $stmtInfo->get_result();
$defect = $res->fetch_assoc();
$dds = $defect['numero_dds'] ?? 'unknown';

$stmtInfo->close();

// Delete safely using prepared statement
$stmt = $mysqli->prepare("DELETE FROM defaut WHERE id_defaut = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    log_action($user_id, 'Deleted defect', 'defaut', $id, "Defect DDS #$dds deleted");
    header("Location: ../defects.php");
    exit();
} else {
    echo "❌ Error deleting defect: " . $stmt->error;
}

$stmt->close();
?>
