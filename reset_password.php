<?php
session_start();
if (!isset($_SESSION['reset_username'])) {
    echo "⛔ Unauthorized.";
    exit();
}
?>
<!DOCTYPE html>
<html>
<head><title>Reset Password</title><link rel = "icon" type = "image/png" href = "tunisairlogo.png"></head>
<body>
    <h2>🔑 Reset Password for <?= htmlspecialchars($_SESSION['reset_username']) ?></h2>
    <form method="post" action="update_reset_password.php">
        <label>New Password:</label><br>
        <input type="password" name="new_password" required><br><br>

        <label>Confirm Password:</label><br>
        <input type="password" name="confirm_password" required><br><br>

        <button type="submit">✅ Reset</button>
    </form>
    
</body>
</html>
