<?php
include "../../_Config/Connection.php";
header('Content-Type: application/json');

$periode = $_POST['periode'] ?? 'Hari';
$keyword = $_POST['keyword'] ?? '';

$where = "";

if ($periode == 'Hari') {
    $where = "DATE(datetime_diminta) = '$keyword'";
} elseif ($periode == 'Bulan') {
    $where = "DATE_FORMAT(datetime_diminta,'%Y-%m') = '$keyword'";
} elseif ($periode == 'Tahun') {
    $where = "YEAR(datetime_diminta) = '$keyword'";
}

// ⚠️ Sesuaikan kolom tanggal di bawah ini
// GANTI `datetime_diminta` dengan kolom tanggal radiologi Anda
$query = "
    SELECT 
        alat_pemeriksa AS modality,
        COUNT(*) AS total
    FROM radiologi
    WHERE alat_pemeriksa IS NOT NULL
    " . ($where ? "AND $where" : "") . "
    GROUP BY alat_pemeriksa
    ORDER BY total DESC
";

$result = mysqli_query($Conn, $query);

$labels = [];
$series = [];

while ($row = mysqli_fetch_assoc($result)) {
    $labels[] = $row['modality'];
    $series[] = (int)$row['total'];
}

echo json_encode([
    'labels' => $labels,
    'series' => $series
]);
