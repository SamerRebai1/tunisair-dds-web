<?php
session_start();
require_once 'db.php';

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$remember = isset($_POST['remember']); 

$stmt = $mysqli->prepare("SELECT id, role_, password FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    if (password_verify($password, $user['password'])) {
        $_SESSION['logged_in'] = true;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $user['role_'];

        if ($remember) {
            // ✅ Generate a secure random token
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', time() + (30 * 24 * 60 * 60)); // 30 days

            // ✅ Save token to DB
            $update = $mysqli->prepare("UPDATE users SET remember_token = ?, token_expiry = ? WHERE id = ?");
            $update->bind_param("ssi", $token, $expiry, $user['id']);
            $update->execute();

            // ✅ Set the cookie
            setcookie(
                'remember_token',
                $token,
                time() + (30 * 24 * 60 * 60), // 30 days
                '/',                         // path
                '',                          // domain (use current)
                isset($_SERVER['HTTPS']),    // secure flag
                true                         // HttpOnly
            );
        }

        header("Location: index.php");
        exit();
    } else {
        echo "❌ Incorrect password.";
    }
} else {
    echo "❌ User not found.";
}
?>
