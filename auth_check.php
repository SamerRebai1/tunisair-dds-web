<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db.php';

if (!isset($_SESSION['logged_in'])) {
    // Check if remember_token cookie exists
    if (isset($_COOKIE['remember_token'])) {
        $token = $_COOKIE['remember_token'];

        $stmt = $mysqli->prepare("SELECT id, username, role_ FROM users WHERE remember_token = ? AND token_expiry > NOW()");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            // Auto-login
            $_SESSION['logged_in'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role_'];
        } else {
            // Invalid or expired token
            header("Location: login.html");
            exit();
        }
    } else {
        // Not logged in
        header("Location: login.html");
        exit();
    }
}
?>
