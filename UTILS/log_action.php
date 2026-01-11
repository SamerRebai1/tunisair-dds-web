<?php
require_once '../auth_check.php';
require_once '../db.php';

function log_action($user_id, $action, $table_name, $record_id, $details = null) {
    global $mysqli;
    $stmt = $mysqli->prepare("
        INSERT INTO audit_logs (user_id, action, table_name, record_id, details, timestamp)
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    $stmt->bind_param("issis", $user_id, $action, $table_name, $record_id, $details);
    $stmt->execute();
}
?>