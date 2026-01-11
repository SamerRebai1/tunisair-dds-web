<?php
require_once '../auth_check.php';
header("Content-Type: application/json");



require_once '../db.php';

$sql = "
SELECT 
  l.id_limitation,
  l.lim_fh,
  l.lim_fc,
  l.lim_day,
  l.reste_fh,
  l.reste_fc,
  l.reste_jours,
  l.fh_jour,
  l.fc_jour,
  l.date_param,
  d.numero_dds,
  a.code_avion
FROM limitation l
JOIN defaut d ON l.id_defaut = d.id_defaut
JOIN avion a ON d.id_avion = a.id_avion
ORDER BY l.id_limitation DESC
";

$result = $mysqli->query($sql);

if ($result) {
    $limitations = [];
    while ($row = $result->fetch_assoc()) {
        $limitations[] = $row;
    }
    echo json_encode($limitations);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Database query failed"]);
}
?>
