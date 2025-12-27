<?php
    // Header
    header('Content-Type: application/json');

    // Koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    date_default_timezone_set("Asia/Jakarta");
    
    // Tangkap keyword dari Select2
    $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : "";

    // 2. Deteksi jika keyword kosong
    if (empty($keyword)) {
        // Kembalikan array kosong jika tidak ada input
        echo json_encode([]);
        exit;
    }

    // Buka Pengaturan SIMRS
    $status_connection_simrs = 1;
    $url_connection_simrs    = GetDetailData($Conn,'connection_simrs','status_connection_simrs',$status_connection_simrs,'url_connection_simrs');

    // Dapatkan Token SIMRS
    $token = GetSimrsToken($Conn);

    // Build URL dengan query parameter sesuai dokumentasi Postman
    // Kita gunakan keyword_by=short_des agar mencari berdasarkan deskripsi
    $params = http_build_query([
        'limit'      => 100,
        'page'       => 1,
        'order_by'   => 'short_des',
        'short_by'   => 'ASC',
        'keyword_by' => '',
        'keyword'    => $keyword
    ]);

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url_connection_simrs.'/API/SIMRS/get_icd10.php?' . $params,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'token: ' . $token,
            'X-API-Key: ••••••' // Pastikan API Key ini benar
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        echo json_encode(["error" => "Curl Error: " . $err]);
        exit;
    }

    $dataArr = json_decode($response, true);

    // --- TRANSFORMASI DATA KE FORMAT SELECT2 ---
    $select2Data = [];

    if (isset($dataArr['metadata']['list']) && is_array($dataArr['metadata']['list'])) {
        foreach ($dataArr['metadata']['list'] as $item) {
            $select2Data[] = [
                'id'   => $item['kode'] . "|" . $item['short_des'],
                'text' => $item['kode'] . " - " . $item['short_des']
            ];
        }
    }

    // Kembalikan hasil dalam format JSON yang dipahami Select2
    echo json_encode($select2Data);
?>