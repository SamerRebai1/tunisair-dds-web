<?php
require_once '../auth_check.php';
require_once '../db.php'; // Adjust path to your db.php
require_once '../UTILS/log_action.php';


$user_id = $_SESSION['user_id'];
// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("❌ Invalid request method.");
}

// Helper function to validate date format YYYY-MM-DD
function validateDate($date) {
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

// Sanitize inputs and validate required fields
$id_avion = filter_input(INPUT_POST, 'id_avion', FILTER_VALIDATE_INT);
$numero_dds = filter_input(INPUT_POST, 'numero_dds', FILTER_VALIDATE_INT);
$defect = trim(filter_input(INPUT_POST, 'defect', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
$date_signalement = $_POST['date_signalement'] ?? '';
$situation = trim(filter_input(INPUT_POST, 'situation', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
$zone_ = trim(filter_input(INPUT_POST, 'zone_', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
$flight_hours = filter_input(INPUT_POST, 'flight_hours', FILTER_VALIDATE_INT);
$flight_cycles = filter_input(INPUT_POST, 'flight_cycles', FILTER_VALIDATE_INT);
$date_cloture = $_POST['date_cloture'] ?? ''; // nullable
$technicien = trim(filter_input(INPUT_POST, 'technicien', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
$oe_reference = trim(filter_input(INPUT_POST, 'oe_reference', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
$work_order = trim(filter_input(INPUT_POST, 'work_order', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
$closure_work_order = trim(filter_input(INPUT_POST, 'closure_work_order', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
$part_number = trim(filter_input(INPUT_POST, 'part_number', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
$expiry_condition = trim(filter_input(INPUT_POST, 'expiry_condition', FILTER_SANITIZE_FULL_SPECIAL_CHARS));

// Check required fields
if (!$id_avion || !$numero_dds || !$defect || !$date_signalement) {
    die("❌ Required fields missing or invalid.");
}

// Validate date_signalement format
if (!validateDate($date_signalement)) {
    die("❌ Invalid date_signalement format. Please use YYYY-MM-DD.");
}

// Validate date_cloture or set to NULL if empty
if ($date_cloture) {
    if (!validateDate($date_cloture)) {
        die("❌ Invalid date_cloture format. Please use YYYY-MM-DD.");
    }
} else {
    $date_cloture = null;
}

// Set default values for optional integers if null
$flight_hours = $flight_hours ?? 0;
$flight_cycles = $flight_cycles ?? 0;


if ($_SESSION['role'] === 'technician') {
    $technicien = $_SESSION['username']; // force ownership
}

// Prepare insert SQL
$sql = "INSERT INTO defaut (
    id_avion, numero_dds, defect, date_signalement, situation, zone_, flight_hours,
    flight_cycles, date_cloture, technicien, oe_reference, work_order,
    closure_work_order, part_number, expiry_condition
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $mysqli->prepare($sql);
if (!$stmt) {
    die("❌ Prepare failed: " . $mysqli->error);
}

$types = "iissssii" . ($date_cloture ? "s" : "s") . "ssssss"; // Still treat as string

$stmt->bind_param(
    $types,
    $id_avion,
    $numero_dds,
    $defect,
    $date_signalement,
    $situation,
    $zone_,
    $flight_hours,
    $flight_cycles,
    $date_cloture,  // can be null
    $technicien,
    $oe_reference,
    $work_order,
    $closure_work_order,
    $part_number,
    $expiry_condition
);



if ($stmt->execute()) {
    $new_id=$stmt->insert_id;
    log_action($user_id, 'Added defect', 'defaut', $new_id, "Defect added with DDS #$numero_dds");
    echo "<h2 style='color:green;'>✅ Defect successfully added!</h2>";
    echo "<a href='../add_defect.html'>➕ Add Another</a> | <a href='../index.php'>🏠 Dashboard</a>";
} else {
    echo "<h2 style='color:red;'>❌ Error: " . htmlspecialchars($stmt->error) . "</h2>";
}



$stmt->close();
$mysqli->close();


?>
