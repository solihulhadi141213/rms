<?php
include "../../_Config/Connection.php";
header('Content-Type: application/json');

$q = $_GET['q'] ?? '';
$data = [];

try {
    $stmt = $Conn->prepare("SELECT id_master_klinis, nama_klinis FROM master_klinis WHERE aktif='Ya' AND nama_klinis LIKE CONCAT('%', ?, '%') ORDER BY nama_klinis ASC LIMIT 20");
    $stmt->bind_param("s", $q);
    $stmt->execute();
    $res = $stmt->get_result();

    $results = [];
    while ($row = $res->fetch_assoc()) {
        $results[] = [
            'id'   => $row['nama_klinis'],  // atau bisa $row['id_master_klinis'] jika ingin ID
            'text' => $row['nama_klinis']
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