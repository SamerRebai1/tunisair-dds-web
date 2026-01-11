<?php
require '../vendor/autoload.php';
require_once '../db.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

// Create spreadsheet and set headers
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Recap Report');

$headers = ['Aircraft', 'Station', 'Total Defects','Open Defects', 'Update Date'];
$sheet->fromArray($headers, null, 'A1');

// Style for the header row
$headerStyle = [
    'font' => [
        'bold' => true,
        'color' => ['rgb' => 'FFFFFF']
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'D71920']
    ]
];
$sheet->getStyle('A1:E1')->applyFromArray($headerStyle);

// Style for the first column (Aircraft column)
$aircraftColumnStyle = [
    'font' => [
        'color' => ['rgb' => '1F4E78'],
        'bold' => true
    ]
];

// Fetch recap data
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
    $rowIndex = 2;
    while ($row = $result->fetch_assoc()) {
        $sheet->setCellValue("A{$rowIndex}", $row['code_avion']);
        $sheet->setCellValue("B{$rowIndex}", $row['station']);
        $sheet->setCellValue("C{$rowIndex}", $row['total_defauts']);
        $sheet->setCellValue("D{$rowIndex}", $row['open_defauts']);
        $sheet->setCellValue("E{$rowIndex}", $row['date_maj']);

        // Apply styling to the Aircraft column (A)
        $sheet->getStyle("A{$rowIndex}")->applyFromArray($aircraftColumnStyle);

        $rowIndex++;
    }
} else {
    die("❌ Failed to fetch recap data.");
}

// Adjust column widths
foreach (range('A', 'D') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Download as Excel file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="The_recap_report.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
