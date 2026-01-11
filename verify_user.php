<?php
session_start();
require_once 'db.php';

$username = trim($_POST['username'] ?? '');

if (!$username) {
    echo "❌ Username required.";
    exit();
}

$stmt = $mysqli->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 1) {
    $_SESSION['reset_username'] = $username;
    header("Location: reset_password.php");
    exit();
} else {
    echo "❌ User not found.";
}
?>
