<?php
require_once '../auth_check.php';
require_once '../db.php';



$user_id = $_SESSION['user_id'];

$current_password = $_POST['current_password'] ?? '';
$new_password     = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Check if all fields are filled
if (!$current_password || !$new_password || !$confirm_password) {
    echo "❌ All fields are required.";
    exit();
}

// Check if new passwords match
if ($new_password !== $confirm_password) {
    echo "❌ New password and confirmation do not match.";
    exit();
}

// Get current hashed password from database
$stmt = $mysqli->prepare("SELECT password FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "❌ User not found.";
    exit();
}

$row = $result->fetch_assoc();
$hashedPassword = $row['password'];

// Verify current password
if (!password_verify($current_password, $hashedPassword)) {
    echo "❌ Incorrect current password.";
    exit();
}

// Hash new password
$new_hashed = password_hash($new_password, PASSWORD_DEFAULT);

// Update password in database
$update = $mysqli->prepare("UPDATE users SET password = ? WHERE id = ?");
$update->bind_param("si", $new_hashed, $user_id);

if ($update->execute()) {
    echo "<h3 style='color:green;'>✅ Password changed successfully!</h3>";
} else {
    echo "<h3 style='color:red;'>❌ Failed to change password: " . $update->error . "</h3>";
}

echo '<a href="../profile.php">⬅️ Back to Profile</a>';

$update->close();
$mysqli->close();
?>
