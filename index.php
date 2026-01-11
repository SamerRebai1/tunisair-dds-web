<?php
require_once 'auth_check.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Tunisair DDS Dashboard</title>
  <link rel="stylesheet" href="style.css">
  <link rel = "icon" type = "image/png" href = "tunisairlogo.png">

</head>
<body>
<script src="func.js"></script>

<header>
  <div style="display: flex; align-items: center;">
    <img src="tunisairlogo.png" alt="Tunisair Logo">
    <h1>DDS Dashboard</h1>
  </div>
  <div>
    <span style="margin-right: 15px;">👤 <?php echo htmlspecialchars($_SESSION['username']); ?></span>
    <button class="toggle-dark" onclick="toggleDarkMode()">🌓</button>
    <a href="logout.php" style="text-decoration:none; margin-left: 15px; color: black;">Logout</a>
  </div>
</header>


<nav class="nav-section">
  <a href="#dashboard">🏠 Dashboard</a>
  <a href="#history">📚 History</a>
  <a href="#about">🛠️ About DDS</a>
  <a href="#contact">📞 Contact</a>
</nav>

<div id="dashboard" class="dashboard">
  <?php
if ($_SESSION['role'] === 'admin') {
    echo '<div class="card"><a href="manage_users.php">👥 Manage Users</a></div>';
    echo '<div class="card"><a href="add_user.php">➕ Add User</a></div>';
}
?>

  <div class="card">
    <a href="recap.php">📄 View Recap Summary</a>
  </div>
  <div class="card">
    <a href="add_defect_form.php">➕ Add New Defect</a>
  </div>
  <div class="card">
    <a href="defects.php">📋 View All Defects</a>
  </div>
  <div class="card">
    <div onclick="toggleDropdown()" style="cursor: pointer; text-decoration: none;
      color: #d71920;
      font-size: 18px;
      font-weight: bold;
      display: block;">
      ⏳ Manage Limitations ▼
    </div>
    <div class="dropdown-menu" id="limitationMenu" style="display: none; margin-top: 10px;">
      <a href="add_limitation_form.php">➕ Add Limitation</a><br>
      <a href="limitations.php">📋 View Limitations</a>
    </div>
  </div>

  <div class="card">
    <a href="charts.html">📊 View Reports</a>
  </div>
  <div class="card">
    <a href="audit.php">📝  Audit Logs</a>
  </div>
  <div class="card">
    <a href="profile.php">👤 My Profile</a>
  </div>
  
</div>


<section id="history" class="info-section">
  <h2>📚 History of Tunisair</h2>
  <p>
    Founded in 1948, Tunisair is Tunisia's national airline. With decades of service, the company has built a reputation 
    for reliability and safety, connecting Tunisia to Europe, Africa, and the Middle East.
  </p>
  <p>
    Today, Tunisair operates a modern fleet, serving millions of passengers annually and supporting national and international travel.
  </p>
</section>


<section id="about" class="info-section">
  <h2>🛠️ About the DDS System</h2>
  <p>
    DDS (Défaut Detection System) is a custom-built tool designed to track and manage technical defects on Tunisair aircraft.
    This platform helps engineers report, monitor, and resolve issues in an organized and efficient manner.
  </p>
  <p>
    This web-based dashboard simplifies defect entry, tracking, reporting, and compliance — ensuring safe and timely maintenance actions.
  </p>
</section>

<section id="contact" class="info-section">
  <h2>📞 Contact</h2>
  <p><strong>IT Support:</strong> support@tunisair.com</p>
  <p><strong>Engineering Department:</strong> maintenance@tunisair.com</p>
  <p><strong>Developed by:</strong> Rebai Samer – Internship Project 2025</p>
</section>

<footer>
  <div id="clock"></div>
  <div>© 2025 Tunisair – DDS System</div>
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
</script>
</body>
</html>
