<?php
require_once '../auth_check.php';
require_once '../db.php';
require_once '../UTILS/log_action.php';



if ($_SESSION['role'] !== 'admin') {
    echo "🚫 Admin access only.";
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    http_response_code(400);
    echo "❌ Invalid ID";
    exit();
}

$user_id = $_SESSION['user_id'];

// Optional: Fetch related defect DDS number for logging
$stmtInfo = $mysqli->prepare("
    SELECT d.numero_dds 
    FROM limitation l 
    JOIN defaut d ON l.id_defaut = d.id_defaut 
    WHERE l.id_limitation = ?
");
$stmtInfo->bind_param("i", $id);
$stmtInfo->execute();
$result = $stmtInfo->get_result();
$row = $result->fetch_assoc();
$dds = $row['numero_dds'] ?? 'unknown';
$stmtInfo->close();

// Delete the limitation
$stmt = $mysqli->prepare("DELETE FROM limitation WHERE id_limitation = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    log_action($user_id, 'Deleted limitation', 'limitation', $id, "Limitation of DDS #$dds deleted");
    header("Location: ../limitations.php");
    exit();
} else {
    http_response_code(500);
    echo "❌ Delete failed: " . $stmt->error;
}

$stmt->close();
?>
