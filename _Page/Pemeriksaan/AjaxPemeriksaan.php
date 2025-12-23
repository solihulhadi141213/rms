<?php
include "../../_Config/Connection.php";
header('Content-Type: application/json');

$q = $_GET['q'] ?? '';
$alat = $_GET['alat'] ?? ''; // Parameter alat_pemeriksa

try {
    // Base query
    $sql = "
        SELECT id_master_pemeriksaan, nama_pemeriksaan, modalitas 
        FROM master_pemeriksaan
        WHERE nama_pemeriksaan LIKE CONCAT('%', ?, '%')
    ";
    
    // Tambahkan filter berdasarkan alat jika ada
    $params = [$q];
    $param_types = "s";
    
    if (!empty($alat)) {
        $sql .= " AND modalitas = ?";
        $params[] = $alat;
        $param_types .= "s";
    }
    
    // Tambahkan ORDER BY dan LIMIT
    $sql .= " ORDER BY nama_pemeriksaan ASC LIMIT 20";
    
    // Prepare statement
    $stmt = $Conn->prepare($sql);
    
    // Bind parameters
    if ($param_types === "s") {
        $stmt->bind_param("s", $params[0]);
    } elseif ($param_types === "ss") {
        $stmt->bind_param("ss", $params[0], $params[1]);
    }
    
    $stmt->execute();
    $res = $stmt->get_result();

    $results = [];
    while ($row = $res->fetch_assoc()) {
        // Format display text dengan info modalitas
        $display_text = $row['nama_pemeriksaan'];
        if (!empty($row['modalitas'])) {
            $modalitas_mapping = [
                'XR' => 'X-Ray',
                'US' => 'USG / Echo',
                'CT' => 'CT Scan',
                'MR' => 'MRI',
                'NM' => 'Nuclear Medicine',
                'PT' => 'PET Scan',
                'DX' => 'Digital Radiography',
                'CR' => 'Computed Radiography'
            ];
            $modalitas_text = $modalitas_mapping[$row['modalitas']] ?? $row['modalitas'];
            $display_text .= " (" . $modalitas_text . ")";
        }
        
        $results[] = [
            'id'   => $row['nama_pemeriksaan'],  // atau $row['id_master_pemeriksaan']
            'text' => $display_text,
            'modalitas' => $row['modalitas'] // Tambahkan modalitas jika diperlukan
        ];
    }
    
    // Format respons untuk Select2
    echo json_encode([
        'results' => $results,
        'pagination' => [
            'more' => false
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'results' => [],
        'pagination' => [
            'more' => false
        ]
    ]);
}
?>