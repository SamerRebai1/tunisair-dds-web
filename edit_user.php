<?php
require_once 'db.php';
require_once 'auth_check.php';
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    echo "🚫 Access denied.";
    exit();
}

$id = intval($_GET['id']);
$stmt = $mysqli->prepare("SELECT username, role_, full_name, email, phone FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    echo "❌ User not found.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit User</title>
  <link rel="stylesheet" href="style_defect.css">
  <link rel="stylesheet" href="darkmode.css">
  <link rel = "icon" type = "image/png" href = "tunisairlogo.png">
</head>
<body>
  <header>
    <button onclick="toggleDarkMode()" class="dm" style="margin-top:50px;float:right;">🌓 </button>
  </header>
  <div class="form-container">
    <h2>✏️ Edit User</h2>
    <form action="API/update_user.php" method="post">
      <input type="hidden" name="id" value="<?= $id ?>">

      <label>Full Name</label>
      <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" required>

      <label>Email</label>
      <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

      <label>Phone</label>
      <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>">

      <label>Role</label>
      <select name="role_" required>
        <option value="admin" <?= $user['role_'] === 'admin' ? 'selected' : '' ?>>Admin</option>
        <option value="technician" <?= $user['role_'] === 'technician' ? 'selected' : '' ?>>Technician</option>
        <option value="viewer" <?= $user['role_'] === 'viewer' ? 'selected' : '' ?>>Viewer</option>
      </select>

      <button type="submit">💾 Save Changes</button>
    </form>
  </div>
  <script>
    function toggleDarkMode() {
    document.body.classList.toggle("dark-mode");
    localStorage.setItem("darkMode", document.body.classList.contains("dark-mode"));
  }

  window.onload = function () {
    if (localStorage.getItem("darkMode") === "true") {
      document.body.classList.add("dark-mode");
    }
  };
  </script>
</body>
</html>
