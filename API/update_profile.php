<?php
require_once '../auth_check.php';
require_once '../db.php';



$user_id = $_SESSION['user_id'];

// Sanitize input
$full_name = trim(filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
$email     = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
$phone     = trim(filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS));

if (!$full_name || !$email) {
    echo "❌ Full name and email are required.";
    exit();
}

// Update query
$stmt = $mysqli->prepare("UPDATE users SET full_name = ?, email = ?, phone = ? WHERE id = ?");
$stmt->bind_param("sssi", $full_name, $email, $phone, $user_id);

if ($stmt->execute()) {
    echo "<h3 style='color:green;'>✅ Profile updated successfully!</h3>";
} else {
    echo "<h3 style='color:red;'>❌ Failed to update profile: " . $stmt->error . "</h3>";
}

echo '<a href="../profile.php">⬅️ Back to Profile</a>';

$stmt->close();
$mysqli->close();
?>
