<?php
require_once '../auth_check.php';
header('Content-Type: application/json');


require_once '../db.php';

/* --- collect filters --- */
$aircraft = $_GET['aircraft'] ?? '';
$status   = $_GET['status']   ?? '';  // open / closed / ''
$search   = trim($_GET['q'] ?? '');
$sort     = $_GET['sort']    ?? 'date_signalement';
$order    = strtolower($_GET['order'] ?? 'desc') === 'asc' ? 'ASC' : 'DESC';

/* --- build WHERE clause safely --- */
$where = [];
$params = [];
$types  = '';

if ($aircraft !== '') {            // filter by aircraft code
  $where[] = 'a.code_avion = ?';
  $params[] = $aircraft; $types .= 's';
}

if ($status === 'open') {
  $where[] = 'd.date_cloture IS NULL';
} elseif ($status === 'closed') {
  $where[] = 'd.date_cloture IS NOT NULL';
}

if ($search !== '') {              // search by DDS or text
  $where[] = '(d.numero_dds LIKE ? OR d.defect LIKE ?)';
  $params[] = '%' . $search . '%';
  $params[] = '%' . $search . '%';
  $types   .= 'ss';
}

$validSort = ['date_signalement','numero_dds'];
if (!in_array($sort,$validSort))   $sort='date_signalement';

/* --- final SQL --- */
$sql = "
  SELECT d.*, a.code_avion
  FROM defaut d
  JOIN avion a ON a.id_avion = d.id_avion
";
if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
$sql .= " ORDER BY $sort $order";

$stmt = $mysqli->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$res = $stmt->get_result();
$out = $res->fetch_all(MYSQLI_ASSOC);

echo json_encode($out);
