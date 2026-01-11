<?php
require_once 'auth_check.php';
require_once 'db.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    echo "🚫 Access denied.";
    exit();
}


$sql = "
    SELECT al.*, u.username 
    FROM audit_logs al
    JOIN users u ON al.user_id = u.id
    ORDER BY al.timestamp DESC
";
$result = $mysqli->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Audit Logs - DDS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="darkmode.css">
  <link rel = "icon" type = "image/png" href = "tunisairlogo.png">
  <style>
   body.dark-mode {
    background-color: #121212 !important;
    color: #e0e0e0;
  }

  body.dark-mode .container {
    background-color: #121212;
    color: #e0e0e0;
    border-radius: 10px;
    padding: 15px;
  }

  body.dark-mode .table {
    background-color: #121212;
    color: #ccc;
  }

  body.dark-mode .table thead {
    background-color: #121212;
    color: #f5f5f5;
  }



  </style>
</head>
<body class="bg-light" >
<header>
  <button onclick="toggleDarkMode()" class="dm" style="margin:0;float:right;">🌓</button>
</header>
<div class="container mt-4" >
  <h2 class="mb-4">📝 Audit Logs</h2>
  <a href="index.php" class="btn btn-secondary mb-3">← Back to Dashboard</a>

  <?php if ($result && $result->num_rows > 0): ?>
    <div class="table-responsive">
      <table class="table table-striped table-bordered align-middle">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>User</th>
            <th>Action</th>
            <th>Table</th>
            <th>Record ID</th>
            <th>Details</th>
            <th>Timestamp</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?= $row['id'] ?></td>
              <td><?= htmlspecialchars($row['username']) ?></td>
              <td><?= htmlspecialchars($row['action']) ?></td>
              <td><?= htmlspecialchars($row['table_name']) ?></td>
              <td><?= htmlspecialchars($row['record_id']) ?></td>
              <td>Seek tables for further details.</td>
              <td><?= htmlspecialchars($row['timestamp']) ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <p>No logs found.</p>
  <?php endif; ?>
</div>
<script>
function toggleDarkMode() {
  const body = document.body;
  if (body.classList.contains('dark-mode')) {
    body.classList.remove('dark-mode');
    body.classList.add('bg-light');
    localStorage.setItem('darkMode', 'disabled');
  } else {
    body.classList.add('dark-mode');
    body.classList.remove('bg-light');
    localStorage.setItem('darkMode', 'enabled');
  }
}

// On load, check preference
window.onload = function() {
  if (localStorage.getItem('darkMode') === 'true') {
    document.body.classList.add('dark-mode');
    document.body.classList.remove('bg-light');
  }
}
</script>
</body>
</html>
