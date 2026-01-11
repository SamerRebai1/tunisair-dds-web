<?php
require_once '../auth_check.php';
require_once '../db.php';

// Defects per aircraft
$defectsByAircraft = [];
$res = $mysqli->query("SELECT a.code_avion, COUNT(*) AS total FROM defaut d JOIN avion a ON d.id_avion = a.id_avion GROUP BY a.code_avion");
while ($row = $res->fetch_assoc()) {
    $defectsByAircraft['labels'][] = $row['code_avion'];
    $defectsByAircraft['counts'][] = (int)$row['total'];
}

// Open vs Closed defects (closure date null = open)
$openClosed = ['open' => 0, 'closed' => 0];
$res2 = $mysqli->query("SELECT date_cloture FROM defaut");
while ($r = $res2->fetch_assoc()) {
    if ($r['date_cloture'] === null) {
        $openClosed['open']++;
    } else {
        $openClosed['closed']++;
    }
}
// Limitations per aircraft (via limitation → defaut → avion)
$limitationsByAircraft = [];
$res3 = $mysqli->query("
    SELECT a.code_avion, COUNT(*) AS total
    FROM limitation l
    JOIN defaut d ON l.id_defaut = d.id_defaut
    JOIN avion a ON d.id_avion = a.id_avion
    GROUP BY a.code_avion
");
while ($ro = $res3->fetch_assoc()) {
    $limitationsByAircraft['labels'][] = $ro['code_avion'];
    $limitationsByAircraft['counts'][] = (int)$ro['total'];
}


// Send everything as JSON
echo json_encode([
    'defects_by_aircraft' => $defectsByAircraft,
    'open_closed' => $openClosed,
    'limitations_by_aircraft' => $limitationsByAircraft
]);
