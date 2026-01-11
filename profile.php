<?php
require_once 'auth_check.php';

require_once 'db.php';

$user_id = $_SESSION['user_id'];

$stmt = $mysqli->prepare("SELECT username, role_, full_name, email, phone FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Profile</title>
  <link rel="stylesheet" href="style_defect.css">
  <link rel = "icon" type = "image/png" href = "tunisairlogo.png">
  <link rel="stylesheet" href="darkmode.css">
  <style>
    body.dark-mode a{
      color:white
    }
    .profile-container {
      max-width: 520px;
      margin: 50px auto;
      background: #fff;
      padding: 25px 30px;
      border-radius: 10px;
      box-shadow: 0 0 12px rgba(0,0,0,0.15);
    }

    .btn {
      padding: 8px 16px;
      border: none;
      background-color: crimson;
      color: white;
      cursor: pointer;
      border-radius: 4px;
      margin-top: 15px;
    }

    .btn:hover {
      background-color: darkred;
    }

  .password-field {
    display: flex;
    align-items: center;
    position: relative;
  }

  .password-field input {
    width: 150px;
    padding-right: 40px; /* leave space for eye icon */
  }

  .password-field button {
    width:50px;
    position: relative;
    right: 15px;
    margin-top:10px;
    margin-bottom:12px;
    background: none;
    border: none;
    font-size: 18px;
    cursor: pointer;
    color: #444;
  }
    #strengthBar {
      height: 5px;
      margin-top: 4px;
      border-radius: 3px;
      background: #ddd;
      transition: width 0.3s;
    }

    #strengthText {
      font-size: 12px;
      color: #666;
      margin-bottom: 10px;
    }
  </style>
</head>
<body>
  <header>
    <button class="dm" onclick="toggleDarkMode()" style="float:right;">🌓 </button>
  </header>
  <div class="profile-container">
    <h2>👤 My Profile</h2>
    <form method="post" action="API/update_profile.php">
      <label>Username</label>
      <input type="text" value="<?= htmlspecialchars($user['username']) ?>" disabled>

      <label>Role</label>
      <input type="text" value="<?= htmlspecialchars($user['role_']) ?>" disabled>

      <label>Full Name</label>
      <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>">

      <label>Email</label>
      <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>">

      <label>Phone Number</label>
      <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>">

      <button type="submit" class="btn">💾 Save Profile</button>
    </form>

    <hr>

    <form method="post" action="API/update_password.php">
      <h3>🔐 Change Password</h3>

      <label>Current Password</label>
      <div class="password-field">
        <input type="password" id="current_password" name="current_password" required>
        <button type="button" onclick="toggleVisibility('current_password', this)">👁</button>
      </div>

      <label>New Password</label>
      <div class="password-field">
        <input type="password" id="new_password" name="new_password" required oninput="checkStrength()">
        <button type="button" onclick="toggleVisibility('new_password', this)">👁</button>
      </div>

      <div id="strengthBar"></div>
      <small id="strengthText"></small>

      <label>Confirm New Password</label>
      <div class="password-field">
        <input type="password" id="confirm_password" name="confirm_password" required>
        <button type="button" onclick="toggleVisibility('confirm_password', this)">👁</button>
      </div>

      <button type="submit" class="btn">Update Password</button>
    </form>

    <br><a href="index.php" style="text-decoration:none;">⬅️ Return to Dashboard</a>
  </div>

  <script>
    function toggleVisibility(fieldId, btn) {
      const field = document.getElementById(fieldId);
      if (field.type === "password") {
        field.type = "text";
        btn.textContent = "🙈";
      } else {
        field.type = "password";
        btn.textContent = "👁";
      }
    }

    function checkStrength() {
      const pwd = document.getElementById("new_password").value;
      const bar = document.getElementById("strengthBar");
      const text = document.getElementById("strengthText");

      let strength = 0;
      if (pwd.length >= 8) strength++;
      if (/[A-Z]/.test(pwd)) strength++;
      if (/[a-z]/.test(pwd)) strength++;
      if (/[0-9]/.test(pwd)) strength++;
      if (/[^A-Za-z0-9]/.test(pwd)) strength++;

      const strengths = ["Very Weak", "Weak", "Moderate", "Strong", "Very Strong"];
      const colors = ["#d9534f", "#f0ad4e", "#5bc0de", "#5cb85c", "#007bff"];
      bar.style.width = (strength / 5) * 100 + "%";
      bar.style.backgroundColor = colors[strength - 1] || "#ddd";
      text.textContent = strengths[strength - 1] || "";
    }
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
