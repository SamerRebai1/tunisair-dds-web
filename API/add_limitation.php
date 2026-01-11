<?php
require_once '../auth_check.php';
require_once '../db.php'; // Ensure this points to your correct DB config
require_once '../UTILS/log_action.php';



$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Invalid request.");
}

// Helper function to check date format
function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

// Collect and sanitize input
$id_defaut     = intval($_POST['id_defaut']);
$lim_fh        = intval($_POST['lim_fh']);
$lim_fc        = intval($_POST['lim_fc']);
$lim_day       = intval($_POST['lim_day']);
$reste_fh      = intval($_POST['reste_fh']);
$reste_fc      = intval($_POST['reste_fc']);
$reste_jours   = intval($_POST['reste_jours']);
$fh_jour       = intval($_POST['fh_jour']);
$fc_jour       = intval($_POST['fc_jour']);
$date_param    = trim($_POST['date_param']);

// Validate date
if (!validateDate($date_param)) {
    die("Invalid date format. Use YYYY-MM-DD.");
}

if ($_SESSION['role'] === 'technician') {
    $username = $_SESSION['username'];
    $check = $mysqli->prepare("SELECT id_defaut FROM defaut WHERE id_defaut = ? AND technicien = ?");
    $check->bind_param("is", $id_defaut, $username);
    $check->execute();
    $check->store_result();

    if ($check->num_rows === 0) {
        die("❌ You can only add limitations to your own defects.");
    }
}

// Insert query
$stmt = $mysqli->prepare("INSERT INTO limitation
(id_defaut, lim_fh, lim_fc, lim_day, reste_fh, reste_fc, reste_jours, fh_jour, fc_jour, date_param)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

if (!$stmt) {
    die("Prepare failed: " . $mysqli->error);
}

$stmt->bind_param(
    "iiiiiiiiss",
    $id_defaut,
    $lim_fh,
    $lim_fc,
    $lim_day,
    $reste_fh,
    $reste_fc,
    $reste_jours,
    $fh_jour,
    $fc_jour,
    $date_param
);

// Execute
if ($stmt->execute()) {
    $new_id = $stmt->insert_id;
    log_action($user_id, 'Added limitation', 'limitation', $new_id, "Limitation added for defect #$id_defaut");
    echo "✅ Limitation added successfully.<br>";
    echo '<a href="../index.php">⬅️ Return to Dashboard</a>';
} else {
    echo "❌ Error: " . $stmt->error;
}

$stmt->close();
$mysqli->close();
?>
