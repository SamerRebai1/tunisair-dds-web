<?php
require_once 'auth_check.php';
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add New User</title>
  <link rel="stylesheet" href="darkmode.css">
  <link rel="stylesheet" href="style_defect.css">
  <link rel = "icon" type = "image/png" href = "tunisairlogo.png">
  <style>
     body.dark-mode a{
      color:white
    }
  </style>
</head>
<body>
  <header>
    <button onclick="toggleDarkMode()" class="dm" style="margin-top:50px;float:right;">🌓</button>
  </header>
<div class="form-container">
  <h2>➕ Add New User</h2>
  <form method="POST" action="API/create_user.php">
    <label>Username</label>
    <input type="text" name="username" required>

    <label>Password</label>
    <input type="password" name="password" required>

    <label>Full Name</label>
    <input type="text" name="full_name" required>

    <label>Email</label>
    <input type="email" name="email">

    <label>Phone</label>
    <input type="text" name="phone">

    <label>Role</label>
    <select name="role_" required>
      <option value="">-- Select Role --</option>
      <option value="admin">Admin</option>
      <option value="technician">Technician</option>
      <option value="viewer">Viewer</option>
    </select>

    <button type="submit">Create User</button>
    <br><a href="index.php" style="text-decoration:none;">⬅️ Return to Dashboard</a>
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
