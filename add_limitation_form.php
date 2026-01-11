<?php
require_once 'auth_check.php';

if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'technician') {
    echo "🚫 Access denied: viewers are not allowed here.";
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Add Limitation - Tunisair DDS</title>
  <link rel="stylesheet" href="style_defect.css">
  <link rel="stylesheet" href="darkmode.css">
  <link rel = "icon" type = "image/png" href = "tunisairlogo.png">
</head>
<body>
<?php
require_once 'db.php';

$username = $_SESSION['username'];
$role = $_SESSION['role'];

if ($role === 'technician') {
    // Only show defects reported by this technician
    $stmt = $mysqli->prepare("SELECT d.id_defaut, d.numero_dds, a.code_avion, LEFT(d.defect, 40) AS short_def 
                              FROM defaut d 
                              JOIN avion a ON d.id_avion = a.id_avion 
                              WHERE d.technicien = ?
                              ORDER BY d.id_defaut DESC");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $defects = $stmt->get_result();
} else {
    // Admin sees all defects
    $defects = $mysqli->query("SELECT d.id_defaut, d.numero_dds, a.code_avion, LEFT(d.defect, 40) AS short_def
                               FROM defaut d 
                               JOIN avion a ON d.id_avion = a.id_avion 
                               ORDER BY d.id_defaut DESC");
}

?>
  <header>
    <button onclick="toggleDarkMode()" class="dm" style="margin-top:50px;float:right;">🌓</button>
  </header>
  <div class="form-container">
    <h2>⏳ Add Limitation</h2>
    <form id="limitationForm" action="API/add_limitation.php" method="post">

      <label for="id_defaut">Select Defect</label>
      <select name="id_defaut" id="id_defaut" required>
        <option value="">-- Select Defect --</option>
        <?php while ($d = $defects->fetch_assoc()): ?>
          <option value="<?= $d['id_defaut'] ?>">
            #<?= $d['id_defaut'] ?> | DDS <?= $d['numero_dds'] ?> | <?= $d['code_avion'] ?> | <?= htmlspecialchars($d['short_def']) ?>
          </option>
        <?php endwhile; ?>
      </select>
      <div class="error-message" id="error-id_defaut"></div>

      <label for="lim_fh">Limitation FH</label>
      <input type="number" id="lim_fh" name="lim_fh" min="0" required>
      <div class="error-message" id="error-lim_fh"></div>

      <label for="lim_fc">Limitation FC</label>
      <input type="number" id="lim_fc" name="lim_fc" min="0" required>
      <div class="error-message" id="error-lim_fc"></div>

      <label for="lim_day">Limitation Days</label>
      <input type="number" id="lim_day" name="lim_day" min="0" required>
      <div class="error-message" id="error-lim_day"></div>

      <label for="fh_jour">FH/day</label>
      <input type="number" id="fh_jour" name="fh_jour" min="0" required>
      <div class="error-message" id="error-fh_jour"></div>

      <label for="fc_jour">FC/day</label>
      <input type="number" id="fc_jour" name="fc_jour" min="0" required>
      <div class="error-message" id="error-fc_jour"></div>

      <label for="date_param">Parameter Date</label>
      <input type="date" id="date_param" name="date_param" required>
      <div class="error-message" id="error-date_param"></div>

      <label for="reste_fh">Remaining FH</label>
      <input type="number" id="reste_fh" name="reste_fh" readonly>

      <label for="reste_fc">Remaining FC</label>
      <input type="number" id="reste_fc" name="reste_fc" readonly>

      <label for="reste_jours">Remaining Days</label>
      <input type="number" id="reste_jours" name="reste_jours" readonly>

      <button type="submit">Add Limitation</button>
    </form>
  </div>

  <script>
    const form = document.getElementById("limitationForm");

    const fields = {
      lim_fh: document.getElementById("lim_fh"),
      lim_fc: document.getElementById("lim_fc"),
      lim_day: document.getElementById("lim_day"),
      fh_jour: document.getElementById("fh_jour"),
      fc_jour: document.getElementById("fc_jour"),
      reste_fh: document.getElementById("reste_fh"),
      reste_fc: document.getElementById("reste_fc"),
      reste_jours: document.getElementById("reste_jours")
    };

    function calculateReste() {
      const fh = parseFloat(fields.lim_fh.value) || 0;
      const fc = parseFloat(fields.lim_fc.value) || 0;
      const day = parseFloat(fields.lim_day.value) || 0;
      const fhj = parseFloat(fields.fh_jour.value) || 1;
      const fcj = parseFloat(fields.fc_jour.value) || 1;

      fields.reste_fh.value = Math.floor(fh / fhj);
      fields.reste_fc.value = Math.floor(fc / fcj);
      fields.reste_jours.value = Math.min(fields.reste_fh.value, fields.reste_fc.value, day);
    }

    Object.values(fields).forEach(field => {
      field.addEventListener("input", calculateReste);
    });

    form.addEventListener("submit", function(e) {
      let ok = true;
      const required = ["id_defaut", "lim_fh", "lim_fc", "lim_day", "fh_jour", "fc_jour", "date_param"];
      required.forEach(id => {
        const el = document.getElementById(id);
        const error = document.getElementById("error-" + id);
        if (!el.value || el.value <= 0) {
          el.classList.add("invalid");
          error.textContent = "This field is required and must be valid.";
          ok = false;
        } else {
          el.classList.remove("invalid");
          error.textContent = "";
        }
      });
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
