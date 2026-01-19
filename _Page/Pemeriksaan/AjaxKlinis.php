<?php
include "../../_Config/Connection.php";
header('Content-Type: application/json');

$q = $_GET['q'] ?? '';

try {
    $stmt = $Conn->prepare("
        SELECT 
            id_master_klinis,
            nama_klinis,
            snomed_code,
            kategori
        FROM master_klinis
        WHERE aktif = 'Ya'
          AND (
                nama_klinis  LIKE CONCAT('%', ?, '%')
             OR kategori     LIKE CONCAT('%', ?, '%')
             OR snomed_code  LIKE CONCAT('%', ?, '%')
          )
        ORDER BY nama_klinis ASC
        LIMIT 20
    ");

    $stmt->bind_param("sss", $q, $q, $q);
    $stmt->execute();
    $res = $stmt->get_result();

    $results = [];
    while ($row = $res->fetch_assoc()) {
        $results[] = [
            'id'   => $row['id_master_klinis'],
            'text' => "{$row['snomed_code']} | {$row['kategori']} - {$row['nama_klinis']}"
        ];
    }

    echo json_encode([
        'results' => $results,
        'pagination' => ['more' => false]
    ]);

} catch (Exception $e) {
    echo json_encode([
        'results' => [],
        'pagination' => ['more' => false]
    ]);
}
