<?php
session_start();
require_once 'db.php';

if (isset($_SESSION['user_id'])) {
    $stmt = $mysqli->prepare("UPDATE users SET remember_token = NULL, token_expiry = NULL WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
}

setcookie("remember_token", "", time() - 3600, "/"); // Delete the cookie
session_destroy();
header("Location: login.html");

?>
