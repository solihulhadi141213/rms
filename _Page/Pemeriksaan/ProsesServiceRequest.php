<?php
    // ======================================================
    // KONEKSI, SESSION, GLOBAL FUNCTION
    // ======================================================
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    date_default_timezone_set("Asia/Jakarta");

    // ======================================================
    // VALIDASI SESSION
    // ======================================================
    if (empty($SessionIdAccess)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Sesi Akses Sudah Berakhir. Silahkan Login Ulang!'
        ]);
        exit;
    }

    // ======================================================
    // GENERATE TOKEN SATUSEHAT
    // ======================================================
    $tokenResult = generateTokenSatuSehat($Conn);

    if ($tokenResult['status'] !== 'success') {
        echo json_encode([
            'status'  => 'error',
            'message' => $tokenResult['message']
        ]);
        exit;
    }

    $token = $tokenResult['token'];

    // ======================================================
    // BUKA KONFIGURASI KONEKSI SATUSEHAT AKTIF
    // ======================================================
    $status_connection_satu_sehat = 1;
    $Qry = $Conn->prepare("
        SELECT * FROM connection_satu_sehat 
        WHERE status_connection_satu_sehat = ?
    ");
    $Qry->bind_param("i", $status_connection_satu_sehat);
    $Qry->execute();
    $Result = $Qry->get_result();
    $Data   = $Result->fetch_assoc();
    $Qry->close();

    if (!$Data) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Tidak ada koneksi SATUSEHAT yang aktif'
        ]);
        exit;
    }

    $url_api             = rtrim($Data['url_connection_satu_sehat'], '/');
    $url_service_request = $url_api . "/fhir-r4/v1/ServiceRequest";

    // ======================================================
    // VALIDASI INPUT WAJIB
    // ======================================================
    $id_radiologi = $_POST['id_radiologi'] ?? null;
    if (empty($id_radiologi)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'ID Permintaan Radiologi Tidak Boleh Kosong!'
        ]);
        exit;
    }

    // ======================================================
    // FUNGSI UNTUK BERSIHKAN DAN VALIDASI INPUT
    // ======================================================
    function getPost($key, $default = '') {
        if (!isset($_POST[$key])) {
            return $default;
        }
        
        $value = $_POST[$key];
        
        // Jika array, proses setiap elemen
        if (is_array($value)) {
            return array_map(function($item) {
                return trim($item);
            }, $value);
        }
        
        // Jika string, bersihkan
        $value = trim($value);
        $value = str_replace(["\r", "\n"], ' ', $value); // Hapus newlines
        $value = preg_replace('/\s+/', ' ', $value);     // Normalize multiple spaces
        
        return $value;
    }

    // ======================================================
    // VALIDASI FIELD WAJIB
    // ======================================================
    $requiredFields = [
        'status',
        'intent',
        'priority',
        'category_system',
        'category_code',
        'category_display',
        'coding_system',
        'coding_code',
        'coding_display',
        'subject_reference',
        'encounter_reference',
        'requester_reference',
        'requester_display',
        'authoredOn_tanggal',
        'authoredOn_jam'
    ];

    $missingFields = [];
    foreach ($requiredFields as $field) {
        if (empty(getPost($field))) {
            $missingFields[] = $field;
        }
    }

    if (!empty($missingFields)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Field berikut wajib diisi: ' . implode(', ', $missingFields)
        ]);
        exit;
    }

    // ======================================================
    // NOTE (OPSIONAL)
    // ======================================================
    $note_text = getPost('note_text', 'None');
    if ($note_text === 'None' || empty(trim($note_text))) {
        $note_text = 'Tidak ada catatan';
    }

    // ======================================================
    // FORMAT TANGGAL DAN WAKTU
    // ======================================================
    $authoredOn_date = getPost('authoredOn_tanggal');
    $authoredOn_time = getPost('authoredOn_jam');
    $occurrenceDateTime = $authoredOn_date . "T" . $authoredOn_time . ":00+07:00";
    $authoredOn = $authoredOn_date . "T" . $authoredOn_time . ":00+07:00";

    // ======================================================
    // SUSUN PAYLOAD ServiceRequest
    // ======================================================
    $payload = [
        "resourceType" => "ServiceRequest",
        "status"       => getPost('status'),
        "intent"       => getPost('intent'),
        "priority"     => getPost('priority'),
        
        "category" => [[
            "coding" => [[
                "system"  => getPost('category_system'),
                "code"    => getPost('category_code'),
                "display" => getPost('category_display')
            ]]
        ]],
        
        "code" => [
            "coding" => [[
                "system"  => getPost('coding_system'),
                "code"    => getPost('coding_code'),
                "display" => getPost('coding_display')
            ]],
            "text" => $note_text
        ],
        
        "subject" => [
            "reference" => "Patient/" . getPost('subject_reference')
        ],
        
        "encounter" => [
            "reference" => "Encounter/" . getPost('encounter_reference'),
            "display" => "Permintaan Radiologi"
        ],
        
        "occurrenceDateTime" => $occurrenceDateTime,
        "authoredOn"         => $authoredOn,
        
        "requester" => [
            "reference" => "Practitioner/" . getPost('requester_reference'),
            "display"   => getPost('requester_display')
        ],
        
        "performer" => [[
            "reference" => "Practitioner/" . getPost('requester_reference'),
            "display"   => getPost('requester_display')
        ]],
        
    ];
    if(!empty($_POST['reasonCode_text'])){
        foreach($_POST['reasonCode_text'] as $i => $text){
            $payload['reasonCode'][] = [
            "text" => $text,
            "coding" => [[
                "system"  => $_POST['reasonCode_coding_system'][$i],
                "code"    => $_POST['reasonCode_coding_code'][$i],
                "display" => $_POST['reasonCode_coding_display'][$i]
            ]]
            ];
        }
        }

    // ======================================================
    // ENCODE PAYLOAD MENJADI JSON DENGAN FORMAT YANG TEPAT
    // ======================================================
    $payload_json = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    // Validasi JSON
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Error membuat JSON payload: ' . json_last_error_msg(),
            'json_error' => json_last_error_msg()
        ]);
        exit;
    }

    // ======================================================
    // DEBUG: TAMPILKAN PAYLOAD UNTUK VERIFIKASI
    // ======================================================
    /*
    echo "<pre>";
    echo "=== DEBUG PAYLOAD ===\n";
    echo "JSON Payload:\n";
    echo $payload_json;
    echo "\n\nJSON Validation: ";
    echo (json_last_error() === JSON_ERROR_NONE) ? "✓ Valid" : "✗ Invalid: " . json_last_error_msg();
    echo "\n\nPayload Array:\n";
    print_r($payload);
    echo "</pre>";
    exit;
    */

    // ======================================================
    // KIRIM KE SATUSEHAT DENGAN CURL YANG TEPAT
    // ======================================================
    $curl = curl_init();

    // OPTION 1: Menggunakan CURLOPT_POSTFIELDS dengan JSON string langsung
    curl_setopt_array($curl, [
        CURLOPT_URL => $url_service_request,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $payload_json, // LANGSUNG pakai JSON string, TANPA concat
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
            'Accept: application/json',
            'Content-Length: ' . strlen($payload_json)
        ],
        // DEV ONLY
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false
    ]);

    $response = curl_exec($curl);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($curl);
    curl_close($curl);

    // ======================================================
    // LOGGING UNTUK DEBUGGING
    // ======================================================
    $log_data = [
        'timestamp' => date('Y-m-d H:i:s'),
        'id_radiologi' => $id_radiologi,
        'url' => $url_service_request,
        'http_code' => $http_code,
        'payload' => $payload,
        'payload_json' => $payload_json,
        'response' => $response,
        'curl_error' => $curl_error,
        'token_preview' => substr($token, 0, 20) . '...'
    ];

    // Simpan log ke file
    $log_dir = "../../_Logs/";
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0777, true);
    }
    file_put_contents(
        $log_dir . "servicerequest_" . date('Ymd_His') . ".log",
        json_encode($log_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n",
        FILE_APPEND
    );

    // ======================================================
    // VALIDASI RESPONSE CURL
    // ======================================================
    if ($curl_error) {
        echo json_encode([
            'status' => 'error',
            'message' => 'cURL Error: ' . $curl_error,
            'http_code' => $http_code
        ]);
        exit;
    }

    if ($response === false) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Empty response from server',
            'http_code' => $http_code
        ]);
        exit;
    }

    // ======================================================
    // DECODE RESPONSE
    // ======================================================
    $result = json_decode($response, true);

    // Cek apakah response adalah JSON valid
    if (json_last_error() !== JSON_ERROR_NONE) {
        // Jika bukan JSON, mungkin HTML error page
        $response_preview = substr($response, 0, 500);
        
        echo json_encode([
            'status'  => 'error',
            'message' => 'Invalid JSON response from server. Response preview: ' . $response_preview,
            'http_code' => $http_code,
            'response_raw' => $response_preview
        ]);
        exit;
    }

    // ======================================================
    // VALIDASI RESPONSE SATUSEHAT
    // ======================================================
    if ($http_code !== 201) {
        // Cek jika response adalah OperationOutcome (error dari SatuSehat)
        if (isset($result['resourceType']) && $result['resourceType'] === 'OperationOutcome') {
            $error_message = 'Error from SATUSEHAT API: ';
            
            if (isset($result['issue'][0]['details']['text'])) {
                $error_message .= $result['issue'][0]['details']['text'];
            } elseif (isset($result['issue'][0]['diagnostics'])) {
                $error_message .= $result['issue'][0]['diagnostics'];
            } else {
                $error_message .= 'Unknown error';
            }
            
            echo json_encode([
                'status'  => 'error',
                'message' => $error_message,
                'http_code' => $http_code,
                'detail'  => $result
            ]);
        } else {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Failed to send ServiceRequest. HTTP Code: ' . $http_code,
                'http_code' => $http_code,
                'response' => $result
            ]);
        }
        exit;
    }

    // ======================================================
    // VALIDASI ID DALAM RESPONSE
    // ======================================================
    if (empty($result['id'])) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Response does not contain ServiceRequest ID',
            'http_code' => $http_code,
            'response' => $result
        ]);
        exit;
    }

    // ======================================================
    // SIMPAN ID ServiceRequest KE DATABASE
    // ======================================================
    $id_service_request = $result['id'];

    $update = $Conn->prepare("UPDATE radiologi SET id_service_request = ? WHERE id_radiologi = ?");
    $update->bind_param("si", $id_service_request, $id_radiologi);
    $update_executed = $update->execute();

    if (!$update_executed) {
        // Log database error tapi tetap return success untuk API
        error_log("Database update failed: " . $Conn->error);
    }

    $update->close();

    // ======================================================
    // RESPONSE SUKSES
    // ======================================================
    echo json_encode([
        'status'             => 'success',
        'message'            => 'ServiceRequest berhasil dikirim ke SATUSEHAT',
        'id_service_request' => $id_service_request,
        'id_radiologi'       => $id_radiologi,
        'http_code'          => $http_code,
        'resource_url'       => $url_api . "/fhir-r4/v1/ServiceRequest/" . $id_service_request
    ]);
    exit;
?>