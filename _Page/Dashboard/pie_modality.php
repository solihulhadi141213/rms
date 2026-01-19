<?php
include "../../_Config/Connection.php";
header('Content-Type: application/json');

$periode = $_POST['periode'] ?? 'Tahun';
$keyword = $_POST['keyword'] ?? '';

// Fallback keyword otomatis
if (empty($keyword)) {
    if ($periode === 'Tahun') {
        $keyword = date('Y');
    } elseif ($periode === 'Bulan') {
        $keyword = date('Y-m');
    } elseif ($periode === 'Hari') {
        $keyword = date('Y-m-d');
    }
}

$where = "";

// Filter berdasarkan periode
if ($periode === 'Hari') {
    $where = "DATE(datetime_diminta) = '$keyword'";
} elseif ($periode === 'Bulan') {
    $where = "DATE_FORMAT(datetime_diminta,'%Y-%m') = '$keyword'";
} elseif ($periode === 'Tahun') {
    $where = "YEAR(datetime_diminta) = $keyword";
}

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

// Mapping nama modalitas
$nama_modalitas = [
    'XR' => 'X-Ray',
    'CT' => 'CT-Scan',
    'US' => 'USG',
    'MR' => 'MRI',
    'NM' => 'Nuclear Medicine (Kedokteran Nuklir)',
    'PT' => 'PET Scan',
    'DX' => 'Digital Radiography',
    'CR' => 'Computed Radiography'
];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {

        $kode = $row['modality']; // 🔑 WAJIB ADA

        $labels[] = $nama_modalitas[$kode] ?? $kode; // fallback aman
        $series[] = (int)$row['total'];
    }
}

echo json_encode([
    'labels' => $labels,
    'series' => $series
]);
