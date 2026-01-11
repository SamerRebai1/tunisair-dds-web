<?php
require_once 'auth_check.php';
require_once 'db.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    echo "🚫 Access denied.";
    exit();
}

$result = $mysqli->query("SELECT id, username, role_, full_name, email, phone FROM users ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Users</title>
  <link rel="stylesheet" href="style_defect.css">
  <link rel = "icon" type = "image/png" href = "tunisairlogo.png">
  <link rel="stylesheet" href="darkmode.css">
  <style>
     body.dark-mode a{
      color:white
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 30px;
    }
    th, td {
      padding: 10px;
      border-bottom: 1px solid #ccc;
    }
    th {
      background: #f2f2f2;
    }
    a.button {
      width:20px;  
      padding: 4px 10px;
      background: #007bff;
      color: white;
      text-decoration: none;
      border-radius: 4px;
      font-size: 14px;
    }
    a.button:hover {
      background: #0056b3;
    }
  </style>
</head>
<body>
  <header>
    <button onclick="toggleDarkMode()" class="dm" style="margin-top:50px;float:right;">🌓</button>
  </header>
  <div class="form-container">
    <h2>👥 Manage Users</h2>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Username</th>
          <th>Role</th>
          <th>Full Name</th>
          <th>Email</th>
          <th>Phone</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><?= $row['role_'] ?></td>
            <td><?= htmlspecialchars($row['full_name']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['phone']) ?></td>
            <td>
              <a href="edit_user.php?id=<?= $row['id'] ?>" class="button">Edit</a>
              <a href="delete_user.php?id=<?= $row['id'] ?>" class="button" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
    <br>
    <a href="index.php" style="text-decoration:none;">⬅️ Back to Dashboard</a>
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
