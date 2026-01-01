<?php
    // Koneksi Database Dan Session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    // Validasi Session
    if(empty($SessionIdAccess)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Sesi Akses Sudah Berakhir. Silahkan Login Ulang!'
        ]);
        exit;
    }

    // Validasi input
    if(empty($_POST['id_question'])){
        echo json_encode([
            'status' => 'error',
            'message' => 'ID Pertanyaan tidak boleh kosong!'
        ]);
        exit;
    }

    if(empty($_POST['resourceType'])){
        echo json_encode([
            'status' => 'error',
            'message' => 'Resource Type tidak boleh kosong!'
        ]);
        exit;
    }

    if(empty($_POST['status'])){
        echo json_encode([
            'status' => 'error',
            'message' => 'Status tidak boleh kosong!'
        ]);
        exit;
    }

    if(empty($_POST['subjectType'])){
        echo json_encode([
            'status' => 'error',
            'message' => 'Subject Type tidak boleh kosong!'
        ]);
        exit;
    }

    if(empty($_POST['title'])){
        echo json_encode([
            'status' => 'error',
            'message' => 'Title tidak boleh kosong!'
        ]);
        exit;
    }

    if(empty($_POST['item_linkId'])){
        echo json_encode([
            'status' => 'error',
            'message' => 'Link ID tidak boleh kosong!'
        ]);
        exit;
    }

    if(empty($_POST['item_text'])){
        echo json_encode([
            'status' => 'error',
            'message' => 'Text Pertanyaan tidak boleh kosong!'
        ]);
        exit;
    }

    if(empty($_POST['item_type'])){
        echo json_encode([
            'status' => 'error',
            'message' => 'Tipe Pertanyaan tidak boleh kosong!'
        ]);
        exit;
    }

    // Buat Vaiabel dan Sanitasi
    $id_question  = validateAndSanitizeInput($_POST['id_question']);
    $resourceType = "Questionnaire";
    $status       = validateAndSanitizeInput($_POST['status']);
    $subjectType  = validateAndSanitizeInput($_POST['subjectType']);
    $title        = validateAndSanitizeInput($_POST['title']);
    $linkId       = validateAndSanitizeInput($_POST['item_linkId']);
    $text         = validateAndSanitizeInput($_POST['item_text']);
    $type         = validateAndSanitizeInput($_POST['item_type']);

    // Generate Token
    $tokenResult = generateTokenSatuSehat($Conn);

    if ($tokenResult['status'] !== 'success') {
        echo json_encode([
            'status'  => 'error',
            'message' => $tokenResult['message']
        ]);
        exit;
    }

    $token = $tokenResult['token'];

    //Buka Pengaturan Satu Sehat
    $status_active = 1;
    $stmt = $Conn->prepare("
        SELECT url_connection_satu_sehat 
        FROM connection_satu_sehat 
        WHERE status_connection_satu_sehat = ?
    ");
    $stmt->bind_param("i", $status_active);
    $stmt->execute();
    $result = $stmt->get_result();
    $config = $result->fetch_assoc();
    $stmt->close();

    if (!$config) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Koneksi SATUSEHAT tidak ditemukan.'
        ]);
        exit;
    }

    $url_api       = rtrim($config['url_connection_satu_sehat'], '/');
    $url_questionnaire = $url_api . '/fhir-r4/v1/Questionnaire';

    // Susun Payload
    $payload = [
        "resourceType" => "Questionnaire",   // HARUS literal
        "status"       => "active",           // draft | active | retired
        "name"         => "assessment-pra-radiologi",
        "title"        => $title,

        "item" => [
            [
                "linkId" => $linkId,          // string unik
                "text"   => $text,            // pertanyaan
                "type"   => $type             // boolean | string | choice | integer
            ]
        ]
    ];

    // Encode JSON Payload
    $payload_json = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Gagal membuat JSON payload.'
        ]);
        exit;
    }

    // Kirim Ke Satu Sehat
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => $url_questionnaire,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $payload_json,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
            'Accept: application/json'
        ],
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false
    ]);

    $response = curl_exec($curl);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($curl);
    curl_close($curl);

    // Handle Error
    if ($curl_error) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'cURL Error: ' . $curl_error
        ]);
        exit;
    }

    // Decode Response
    $result = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Response bukan JSON valid.',
            'response_raw' => substr($response, 0, 300)
        ]);
        exit;
    }

    // Validasi Response Satusehat
    if ($http_code !== 201) {
        $msg = 'Gagal mengirim Data ke SATUSEHAT';

        if (($result['resourceType'] ?? '') === 'OperationOutcome') {
            $msg = $result['issue'][0]['details']['text']
                ?? $result['issue'][0]['diagnostics']
                ?? $msg;
        }

        echo json_encode([
            'status'      => 'error',
            'message'     => $msg,
            'http_code'   => $http_code,
            'payload'     => json_decode($payload_json, true), // ⬅️ KUNCI
            'payload_raw' => $payload_json                      // ⬅️ opsional (string)
        ]);
        exit;
    }

    // Menangkap Nilai 'id_questionnaire'
    $id_questionnaire = $result['id'] ?? null;

    // Menangkap nilai linkId
    $linkId ="";
    if (!empty($result['item'])){
        foreach ($result['item'] as $item) {
            $linkId = htmlspecialchars($item['linkId']);
        }
    }
    
    // Simpan ID Ke Database
    $satu_sehat = 1;
    if ($id_questionnaire) {
        $upd = $Conn->prepare("UPDATE question SET id_questionnaire = ?, link_id = ?, satu_sehat = ? WHERE id_question = ?");
        $upd->bind_param("ssii", $id_questionnaire, $linkId, $satu_sehat, $id_question);
        $upd->execute();
        $upd->close();
    }

    // Tampilkan Response
    echo json_encode([
        'status'           => 'success',
        'message'          => 'Questionnaire berhasil dikirim ke SATUSEHAT',
        'id_questionnaire' => $id_questionnaire,
        'id_question'      => $id_question,
        'resource_url'     => $url_api . '/fhir-r4/v1/Questionnaire/' . $id_questionnaire
    ]);
    exit;

?>