<?php
require_once '../auth_check.php';
require_once '../db.php';
require_once '../UTILS/log_action.php';


$user_id = $_SESSION['user_id'];

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    echo "Invalid limitation ID.";
    exit();
}
// Check ownership if technician
if ($_SESSION['role'] === 'technician') {
    $username = $_SESSION['username'];
    $check = $mysqli->prepare("
        SELECT l.id_limitation 
        FROM limitation l
        JOIN defaut d ON l.id_defaut = d.id_defaut
        WHERE l.id_limitation = ? AND d.technicien = ?
    ");
    $check->bind_param("is", $id, $username);
    $check->execute();
    $check->store_result();

    if ($check->num_rows === 0) {
        echo "🚫 You can only edit limitations related to your own defects.";
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and assign POST data
    $lim_fh = intval($_POST['lim_fh']);
    $lim_fc = intval($_POST['lim_fc']);
    $lim_day = intval($_POST['lim_day']);
    $reste_fh = intval($_POST['reste_fh']);
    $reste_fc = intval($_POST['reste_fc']);
    $reste_jours = intval($_POST['reste_jours']);
    $fh_jour = intval($_POST['fh_jour']);
    $fc_jour = intval($_POST['fc_jour']);
    $date_param = $mysqli->real_escape_string($_POST['date_param']);

    // Update limitation table
    $sql = "UPDATE limitation SET
        lim_fh = $lim_fh,
        lim_fc = $lim_fc,
        lim_day = $lim_day,
        reste_fh = $reste_fh,
        reste_fc = $reste_fc,
        reste_jours = $reste_jours,
        fh_jour = $fh_jour,
        fc_jour = $fc_jour,
        date_param = '$date_param'
      WHERE id_limitation = $id";

    if ($mysqli->query($sql)) {
      log_action($user_id, 'Edited limitation', 'limitation', $id, "Limitation fields updated");
        header('Location: ../limitations.php');
        exit();
    } else {
        $error = "Update failed: " . $mysqli->error;
    }
}

// Fetch limitation with defect + aircraft info
$stmt = $mysqli->prepare("
  SELECT l.*, d.numero_dds, a.code_avion
  FROM limitation l
  JOIN defaut d ON l.id_defaut = d.id_defaut
  JOIN avion a ON d.id_avion = a.id_avion
  WHERE l.id_limitation = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo "Limitation not found.";
    exit();
}

$row = $res->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Edit Limitation #<?= htmlspecialchars($id) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
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
  <button onclick="toggleDarkMode()" class="dm"style="margin:0;float:right;">🌓</button>
</header>
<div class="container mt-5">
  <div class="card shadow">
    <div class="card-header bg-danger text-white">
      <h4>Edit Limitation #<?= htmlspecialchars($id) ?></h4>
    </div>
    <div class="card-body">

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

      <form method="post" class="row g-3">

        <div class="col-md-6">
          <label class="form-label">Aircraft Code</label>
          <input type="text" class="form-control" value="<?= htmlspecialchars($row['code_avion']) ?>" readonly>
        </div>

        <div class="col-md-6">
          <label class="form-label">DDS Number</label>
          <input type="text" class="form-control" value="<?= htmlspecialchars($row['numero_dds']) ?>" readonly>
        </div>

        <div class="col-md-4">
          <label class="form-label">Lim FH</label>
          <input type="number" name="lim_fh" class="form-control" value="<?= htmlspecialchars($row['lim_fh']) ?>" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Lim FC</label>
          <input type="number" name="lim_fc" class="form-control" value="<?= htmlspecialchars($row['lim_fc']) ?>" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Lim Days</label>
          <input type="number" name="lim_day" class="form-control" value="<?= htmlspecialchars($row['lim_day']) ?>" required>
        </div>

        <div class="col-md-4">
          <label class="form-label">Reste FH</label>
          <input type="number" name="reste_fh" class="form-control" value="<?= htmlspecialchars($row['reste_fh']) ?>" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Reste FC</label>
          <input type="number" name="reste_fc" class="form-control" value="<?= htmlspecialchars($row['reste_fc']) ?>" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Reste Jours</label>
          <input type="number" name="reste_jours" class="form-control" value="<?= htmlspecialchars($row['reste_jours']) ?>" required>
        </div>

        <div class="col-md-6">
          <label class="form-label">FH / Day</label>
          <input type="number" step="0.01" name="fh_jour" class="form-control" value="<?= htmlspecialchars($row['fh_jour']) ?>" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">FC / Day</label>
          <input type="number" step="0.01" name="fc_jour" class="form-control" value="<?= htmlspecialchars($row['fc_jour']) ?>" required>
        </div>

        <div class="col-md-6">
          <label class="form-label">Date Param</label>
          <input type="date" name="date_param" class="form-control" value="<?= htmlspecialchars($row['date_param']) ?>" required>
        </div>

        <div class="col-12 mt-3">
          <button type="submit" class="btn btn-success">💾 Save Changes</button>
          <a href="../limitations.php" class="btn btn-secondary ms-2">Cancel</a>
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
