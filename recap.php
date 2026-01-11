<?php
require_once 'auth_check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Recap Summary – Tunisair DDS</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="darkmode.css">
  <link rel = "icon" type = "image/png" href = "tunisairlogo.png">
</head>
<body>
<header>
  <div style="display:flex;align-items:center;">
    <img src="tunisairlogo.png" alt="Tunisair Logo" style="height:50px;">
    <h1 style="margin-left:20px;">📄 Recap Summary</h1>
  </div>
  <div>
    <span style="margin-right:15px;">👤 <?=htmlspecialchars($_SESSION['username']);?></span>
    <a href="index.php" style="margin-right:15px;color:#000;text-decoration:none;">🏠 Dashboard</a>
    <a href="logout.php" style="color:#000;text-decoration:none;">🚪 Logout</a>
  </div>
  <button onclick="toggleDarkMode()" class="dm" style="margin:0;float:right;">🌓 </button>
</header>

<div class="dashboard" style="margin-top:40px;">
  <table id="recapTable" class="styled-table">
    <thead>
      <tr>
        <th>✈️ Aircraft</th>
        <th>📍 Station</th>
        <th>🧮 Total Defects</th>
        <th>⚠️ Open Defects</th>
        <th>📅 Last Reported</th>
      </tr>
    </thead>
    <tbody></tbody>
  </table>
  <?php 
  if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'technician') {
    echo "<a href='export/export_excel.php' style='text-decoration:none;color:crimson; '>📥 Export to Excel</a>";
  }
  else{
    echo"";
  }
  ?>
</div>

<footer>
  <div>© 2025 Tunisair – DDS System</div>
</footer>

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
function loadRecap(){
  fetch('API/get_recap.php')
    .then(r => r.json())
    .then(data => {
      const body = document.querySelector('#recapTable tbody');
      body.innerHTML = '';
      data.forEach(r=>{
        body.innerHTML += `
          <tr>
            <td>${r.code_avion}</td>
            <td>${r.station ?? '—'}</td>
            <td>${r.total_defauts}</td>
            <td>${r.open_defauts}</td>
            <td>${r.date_maj ?? '—'}</td>
          </tr>`;
      });
    })
    .catch(e => console.error('Recap error', e));
}
loadRecap();
</script>
</body>
</html>
