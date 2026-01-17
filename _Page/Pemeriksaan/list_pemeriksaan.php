<?php
    include "../../_Config/Connection.php";
    header('Content-Type: application/json');

    $q     = $_GET['q'] ?? '';
    $page  = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 20;
    $offset = ($page - 1) * $limit;

    $data = [];

    $stmt = $Conn->prepare("
        SELECT SQL_CALC_FOUND_ROWS 
            id_master_pemeriksaan, nama_pemeriksaan, modalitas, pemeriksaan_code, pemeriksaan_description 
        FROM master_pemeriksaan 
        WHERE 
            nama_pemeriksaan LIKE CONCAT('%', ?, '%') 
            OR pemeriksaan_code LIKE CONCAT('%', ?, '%') 
            OR pemeriksaan_description LIKE CONCAT('%', ?, '%')
        ORDER BY nama_pemeriksaan ASC 
        LIMIT ?, ?
    ");

    $stmt->bind_param("sssii", $q, $q, $q, $offset, $limit);
    $stmt->execute();
    $res = $stmt->get_result();

    $results = [];
    while ($row = $res->fetch_assoc()) {

        // Buat Variabel Agar Mudah
        $id_master_pemeriksaan = $row['id_master_pemeriksaan'];
        $nama_pemeriksaan      = $row['nama_pemeriksaan'];
        $modalitas         = $row['modalitas'];
        $pemeriksaan_code      = $row['pemeriksaan_code'];
        $pemeriksaan_description      = $row['pemeriksaan_description'];
        $results[] = [
            'id'   => $id_master_pemeriksaan,
            'text' => "$modalitas - $nama_pemeriksaan ($pemeriksaan_code)"
        ];
    }

    // Hitung total data
    $totalRes = $Conn->query("SELECT FOUND_ROWS() AS total")->fetch_assoc();
    $total = (int)$totalRes['total'];

    echo json_encode([
        'results' => $results,
        'pagination' => [
            'more' => ($offset + $limit) < $total
        ]
    ]);
?>