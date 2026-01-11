<?php

require_once 'auth_check.php';

if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'technician') {
    echo "🚫 Access denied: viewers are not allowed here.";
    exit();
}

$role = $_SESSION['role'];
$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Add New Defect - Tunisair DDS</title>
  <link rel="stylesheet" href="style_defect.css">
  <link rel="stylesheet" href="darkmode.css">
  <link rel = "icon" type = "image/png" href = "tunisairlogo.png">
</head>
<body>
  <header>
    <button onclick="toggleDarkMode()" class="dm" style="margin-top:50px;float:right;">🌓</button>
  </header>
  <div class="form-container">
    <h2>➕ Add New Defect</h2>
    <form id="defectForm" action="API/add_defect.php" method="post">
      
      <label for="id_avion">Aircraft</label>
      <select id="id_avion" name="id_avion" required>
        <option value="">-- Select Aircraft --</option>
        <option value="1">IFM</option>
        <option value="2">IFN</option>
        <option value="3">IMA</option>
        <option value="4">IMB</option>
        <option value="5">IMR</option>
        <option value="6">IMX</option>
        <option value="7">IMY</option>
        <option value="8">IMZ</option>
      </select>
      <div class="error-message" id="error-id_avion"></div>

      <label for="numero_dds">DDS Number</label>
      <input type="number" id="numero_dds" name="numero_dds" required>
      <div class="error-message" id="error-numero_dds"></div>

      <label for="defect">Defect Description</label>
      <textarea id="defect" name="defect" rows="3" required></textarea>
      <div class="error-message" id="error-defect"></div>

      <label for="date_signalement">Date Reported</label>
      <input type="date" id="date_signalement" name="date_signalement" required>
      <div class="error-message" id="error-date_signalement"></div>

      <label for="situation">Situation</label>
      <input type="text" id="situation" name="situation" maxlength="20" placeholder="Open/Closed">
      <div class="error-message" id="error-situation"></div>

      <label for="zone_">Zone</label>
      <input type="text" id="zone_" name="zone_" maxlength="50">
      <div class="error-message" id="error-zone_"></div>

      <label for="flight_hours">Flight Hours</label>
      <input type="number" id="flight_hours" name="flight_hours" min="0">
      <div class="error-message" id="error-flight_hours"></div>

      <label for="flight_cycles">Flight Cycles</label>
      <input type="number" id="flight_cycles" name="flight_cycles" min="0">
      <div class="error-message" id="error-flight_cycles"></div>

      <label for="date_cloture">Closure Date</label>
      <input type="date" id="date_cloture" name="date_cloture">
      <div class="error-message" id="error-date_cloture"></div>

      <label for="technicien">Technician</label>
      <?php if ($role === 'technician'): ?>
      <input type="text" name="technicien" id="technician" value="<?= htmlspecialchars($username) ?>" readonly>
      <?php else: ?>
      <input type="text" name="technicien" id="technician">
      <?php endif; ?>
      <div class="error-message" id="error-technicien"></div>

      <label for="oe_reference">OE Reference</label>
      <input type="text" id="oe_reference" name="oe_reference" maxlength="50">
      <div class="error-message" id="error-oe_reference"></div>

      <label for="work_order">Work Order</label>
      <input type="text" id="work_order" name="work_order" maxlength="50">
      <div class="error-message" id="error-work_order"></div>

      <label for="closure_work_order">Closure Work Order</label>
      <input type="text" id="closure_work_order" name="closure_work_order" maxlength="50">
      <div class="error-message" id="error-closure_work_order"></div>

      <label for="part_number">Part Number</label>
      <input type="text" id="part_number" name="part_number" maxlength="50">
      <div class="error-message" id="error-part_number"></div>

      <label for="expiry_condition">Expiry Condition</label>
      <input type="text" id="expiry_condition" name="expiry_condition" maxlength="100">
      <div class="error-message" id="error-expiry_condition"></div>

      <button type="submit">➕ Add Defect</button>
    </form>
  </div>

  <script>
    const form = document.getElementById("defectForm");
    form.addEventListener("submit", function(e) {
      let ok = true;

      const validate = (id, condition, message) => {
        const field = document.getElementById(id);
        const errorDiv = document.getElementById("error-" + id);
        if (condition) {
          field.classList.add("invalid");
          errorDiv.textContent = message;
          ok = false;
        } else {
          field.classList.remove("invalid");
          errorDiv.textContent = "";
        }
      };

      validate("id_avion", !id_avion.value, "Select an aircraft");
      validate("numero_dds", numero_dds.value <= 0, "Enter a valid DDS number");
      validate("defect", !defect.value.trim(), "Defect description is required");
      validate("date_signalement", !date_signalement.value, "Date is required");

      if (!ok) e.preventDefault();
    });
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
