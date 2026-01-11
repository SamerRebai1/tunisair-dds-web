<?php
require_once '../auth_check.php';
header("Content-Type: application/json");



require_once '../db.php';

// Query to get total and open defects dynamically per aircraft
$sql = "
    SELECT 
        a.code_avion,
        a.station,
        COUNT(d.id_defaut) AS total_defauts,
        SUM(CASE WHEN d.date_cloture IS NULL THEN 1 ELSE 0 END) AS open_defauts,
        MAX(d.date_signalement) AS date_maj
    FROM avion a
    LEFT JOIN defaut d ON d.id_avion = a.id_avion
    GROUP BY a.id_avion, a.code_avion, a.station
    ORDER BY a.code_avion
";

$result = $mysqli->query($sql);

if ($result) {
    $recaps = [];
    while ($row = $result->fetch_assoc()) {
        $recaps[] = [
            "code_avion"     => $row["code_avion"],
            "station"        => $row["station"],
            "total_defauts"  => intval($row["total_defauts"]),
            "open_defauts"   => intval($row["open_defauts"]),
            "date_maj"       => $row["date_maj"] ?? "—"
        ];
    }
    echo json_encode($recaps);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Query failed: " . $mysqli->error]);
}
?>
