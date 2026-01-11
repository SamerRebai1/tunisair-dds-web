<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['reset_username'])) {
    echo "⛔ Unauthorized.";
    exit();
}

$username = $_SESSION['reset_username'];
$new = $_POST['new_password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

if (!$new || !$confirm) {
    echo "❌ All fields required.";
    exit();
}

if ($new !== $confirm) {
    echo "❌ Passwords do not match.";
    exit();
}

$hashed = password_hash($new, PASSWORD_DEFAULT);

$stmt = $mysqli->prepare("UPDATE users SET password = ? WHERE username = ?");
$stmt->bind_param("ss", $hashed, $username);

if ($stmt->execute()) {
    unset($_SESSION['reset_username']);
    echo "<h3 style='color:green;'>✅ Password reset successfully!</h3>";
    echo '<a href="login.html">🔐 Login</a>';
} else {
    echo "❌ Error updating password.";
}
?>
