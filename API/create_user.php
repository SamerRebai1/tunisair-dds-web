<?php
require_once '../auth_check.php';
require_once '../db.php';
require_once '../UTILS/log_action.php'; // Optional: for audit logging

if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    die("Access denied.");
}

$username   = trim($_POST['username'] ?? '');
$password   = $_POST['password'] ?? '';
$full_name  = trim($_POST['full_name'] ?? '');
$email      = trim($_POST['email'] ?? '');
$phone      = trim($_POST['phone'] ?? '');
$role_      = $_POST['role_'] ?? '';

if (!$username || !$password || !$full_name || !$role_) {
    die("❌ Missing required fields.");
}
if (strlen($password) < 6) {
    die("❌ Password must be at least 6 characters.");
}

// Check for duplicate username
$stmt = $mysqli->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    die("❌ Username already exists.");
}
$stmt->close();

// Hash password
$hashed = password_hash($password, PASSWORD_DEFAULT);

// Insert new user
$stmt = $mysqli->prepare("
    INSERT INTO users (username, password, role_, full_name, email, phone)
    VALUES (?, ?, ?, ?, ?, ?)
");
$stmt->bind_param("ssssss", $username, $hashed, $role_, $full_name, $email, $phone);

if ($stmt->execute()) {
    // Optional audit log
    log_action($_SESSION['user_id'], 'create', 'users', $stmt->insert_id, "Created user: $username");
    echo "✅ User created successfully. ";
    echo '<a href="../index.php">⬅️ Return to Dashboard</a>';
} else {
    echo "❌ Error: " . $stmt->error;
}
$stmt->close();
$mysqli->close();
?>
