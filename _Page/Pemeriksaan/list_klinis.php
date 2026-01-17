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
            id_master_klinis, nama_klinis, kategori, snomed_code 
        FROM master_klinis 
        WHERE aktif='Ya' 
        AND (
            nama_klinis LIKE CONCAT('%', ?, '%') 
            OR kategori LIKE CONCAT('%', ?, '%') 
            OR snomed_code LIKE CONCAT('%', ?, '%')
        )
        ORDER BY nama_klinis ASC 
        LIMIT ?, ?
    ");

    $stmt->bind_param("sssii", $q, $q, $q, $offset, $limit);
    $stmt->execute();
    $res = $stmt->get_result();

    $results = [];
    while ($row = $res->fetch_assoc()) {

        // Buat Variabel Agar Mudah
        $id_master_klinis = $row['id_master_klinis'];
        $nama_klinis      = $row['nama_klinis'];
        $kategori         = $row['kategori'];
        $snomed_code      = $row['snomed_code'];
        $results[] = [
            'id'   => $id_master_klinis,
            'text' => "$kategori - $nama_klinis ($snomed_code)"
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