<?php
require_once '../auth_check.php';
require_once '../db.php';
require_once '../utils/log_action.php';



$role = $_SESSION['role'] ?? '';
$username = $_SESSION['username'] ?? '';
$user_id = $_SESSION['user_id'];
// Check role access
if ($role === 'viewer') {
    echo "🚫 Access denied: viewers cannot edit defects.";
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    echo "Invalid ID.";
    exit();
}

// Fetch the defect
$result = $mysqli->query("SELECT * FROM defaut WHERE id_defaut = $id");
if (!$result || $result->num_rows === 0 ) {
    echo "Defect not found.";
    exit();
}
$row = $result->fetch_assoc();

// If technician, ensure they are the one who reported it
if ($role === 'technician' && $row['technicien'] !== $username) {
    echo "🚫 Access denied: you can only edit your own reported defects.";
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Readonly fields are not updated
    $date_signalement = $mysqli->real_escape_string($_POST['date_signalement']);
    $defect = $mysqli->real_escape_string($_POST['defect']);
    $situation = $mysqli->real_escape_string($_POST['situation']);
    $zone_ = $mysqli->real_escape_string($_POST['zone_']);
    $flight_hours = floatval($_POST['flight_hours']);
    $flight_cycles = intval($_POST['flight_cycles']);
    $date_cloture = $mysqli->real_escape_string($_POST['date_cloture']);
    $technicien = $mysqli->real_escape_string($_POST['technicien']);
    $oe_reference = $mysqli->real_escape_string($_POST['oe_reference']);
    $work_order = $mysqli->real_escape_string($_POST['work_order']);
    $closure_work_order = $mysqli->real_escape_string($_POST['closure_work_order']);
    $part_number = $mysqli->real_escape_string($_POST['part_number']);
    $expiry_condition = $mysqli->real_escape_string($_POST['expiry_condition']);
    
    $query = "
        UPDATE defaut SET
            date_signalement = '$date_signalement',
            defect = '$defect',
            situation = '$situation',
            zone_ = '$zone_',
            flight_hours = $flight_hours,
            flight_cycles = $flight_cycles,
            date_cloture = " . ($date_cloture ? "'$date_cloture'" : "NULL") . ",
            technicien = '$technicien',
            oe_reference = '$oe_reference',
            work_order = '$work_order',
            closure_work_order = '$closure_work_order',
            part_number = '$part_number',
            expiry_condition = '$expiry_condition'
        WHERE id_defaut = $id
    ";

    if ($mysqli->query($query)) {
        header("Location: ../defects.php");
        exit();
    } else {
        echo "Update failed: " . $mysqli->error;
    }
}

$result = $mysqli->query("SELECT * FROM defaut WHERE id_defaut = $id");
if (!$result || $result->num_rows === 0 ) {
    echo "Defect not found.";
    exit();
}
$row = $result->fetch_assoc();
$id_avion = intval($row['id_avion']); 
$res = $mysqli->query("SELECT code_avion FROM avion WHERE id_avion = $id_avion");
if (!$res || $res->num_rows === 0) {
    $code_avion = 'Unknown';
} else {
    $row1 = $res->fetch_assoc();
    $code_avion = $row1['code_avion'];
}




log_action($_SESSION['user_id'], "edit", "defaut", $id);

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Defect</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../darkmode.css">
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
  body.dark-mode .card {
    background-color: #121212;
    color: #e0e0e0;
    border-radius: 10px;
    padding: 15px;
  }
  </style>
</head>
<body class="bg-light">
<header>
  <button onclick="toggleDarkMode()" class="dm" style="margin:0;float:right;">🌓</button>
</header>
<div class="container mt-5 mb-5">
  <div class="card shadow">
    <div class="card-header bg-danger text-white">
      <h4>Edit Defect #<?= htmlspecialchars($row['numero_dds'] ?? '') ?></h4>
    </div>
    <div class="card-body">
      <form method="post" class="row g-3">
        <div class="col-md-6">
          <label class="form-label">DDS Number</label>
          <input type="text" class="form-control" value="<?= htmlspecialchars($row['numero_dds'] ?? '') ?>" readonly>
        </div>
        <div class="col-md-6">
            <label class="form-label">Aircraft Code</label>
            <input type="text" class="form-control" name="code_avion_display" value="<?= htmlspecialchars($row1['code_avion'] ?? '') ?>" readonly>
        </div>


        <div class="col-md-4">
          <label class="form-label">Report Date</label>
          <input type="date" name="date_signalement" class="form-control" value="<?= htmlspecialchars($row['date_signalement'] ?? '') ?>">
        </div>

        <div class="col-12">
          <label class="form-label">Defect Description</label>
          <textarea name="defect" class="form-control" rows="3"><?= htmlspecialchars($row['defect'] ?? '') ?></textarea>
        </div>

        <div class="col-md-4">
          <label class="form-label">Situation</label>
          <input type="text" name="situation" class="form-control" value="<?= htmlspecialchars($row['situation'] ?? '') ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label">Zone</label>
          <input type="text" name="zone_" class="form-control" value="<?= htmlspecialchars($row['zone_'] ?? '') ?>">
        </div>
        <div class="col-md-2">
          <label class="form-label">Flight Hours</label>
          <input type="number" step="0.1" name="flight_hours" class="form-control" value="<?= htmlspecialchars($row['flight_hours'] ?? '') ?>">
        </div>
        <div class="col-md-2">
          <label class="form-label">Flight Cycles</label>
          <input type="number" name="flight_cycles" class="form-control" value="<?= htmlspecialchars($row['flight_cycles'] ?? '') ?>">
        </div>

        <div class="col-md-4">
          <label class="form-label">Closure Date</label>
          <input type="date" name="date_cloture" class="form-control" value="<?= htmlspecialchars($row['date_cloture'] ?? '') ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label">Technician</label>
          <input type="text" name="technicien" class="form-control" value="<?= htmlspecialchars($row['technicien'] ?? '') ?>" <?= ($role === 'technician') ? 'readonly' : '' ?>>

        </div>
        <div class="col-md-4">
          <label class="form-label">OE Reference</label>
          <input type="text" name="oe_reference" class="form-control" value="<?= htmlspecialchars($row['oe_reference'] ?? '') ?>">
        </div>

        <div class="col-md-6">
          <label class="form-label">Work Order</label>
          <input type="text" name="work_order" class="form-control" value="<?= htmlspecialchars($row['work_order'] ?? '') ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Closure WO</label>
          <input type="text" name="closure_work_order" class="form-control" value="<?= htmlspecialchars($row['closure_work_order'] ?? '') ?>">
        </div>

        <div class="col-md-6">
          <label class="form-label">Part Number</label>
          <input type="text" name="part_number" class="form-control" value="<?= htmlspecialchars($row['part_number'] ?? '') ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Expiry Condition</label>
          <input type="text" name="expiry_condition" class="form-control" value="<?= htmlspecialchars($row['expiry_condition'] ?? '') ?>">
        </div>

        <div class="col-12 mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-success" style="background-color: crimson; border-color: crimson;">💾 Save Changes</button>
            <a href="../defects.php" class="btn btn-secondary">Cancel</a>
            
        </div>

      </form>
    </div>
  </div>
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
};
</script>
</body>
</html>
