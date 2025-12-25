<?php
    // ======================================================
    // KONEKSI, SESSION, GLOBAL FUNCTION
    // ======================================================
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    date_default_timezone_set("Asia/Jakarta");

    // ======================================================
    // RESPONSE HEADER
    // ======================================================
    header('Content-Type: application/json');

    // ======================================================
    // VALIDASI SESSION
    // ======================================================
    if (empty($SessionIdAccess)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Sesi akses telah berakhir. Silakan login ulang.'
        ]);
        exit;
    }

    // ======================================================
    // FUNGSI AMAN AMBIL POST
    // ======================================================
    function getPost($key, $default = '')
    {
        if (!isset($_POST[$key])) {
            return $default;
        }

        $value = $_POST[$key];

        if (is_array($value)) {
            return array_map('trim', $value);
        }

        $value = trim($value);
        $value = str_replace(["\r", "\n"], ' ', $value);
        $value = preg_replace('/\s+/', ' ', $value);

        return $value;
    }

    // ======================================================
    // VALIDASI INPUT WAJIB
    // ======================================================
    $id_radiologi = getPost('id_radiologi');

    if (empty($id_radiologi)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'ID Radiologi tidak boleh kosong.'
        ]);
        exit;
    }

    // Field wajib FHIR Procedure
    $requiredFields = [
        'status',
        'category_system',
        'category_code',
        'category_display',
        'coding_system',
        'coding_code',
        'coding_display',
        'subject_reference',
        'encounter_reference',
        'basedOn_reference',
        'performer_actor_reference',
        'performer_actor_display',
        'performedDateTime_tanggal',
        'performedDateTime_jam'
    ];

    $missing = [];
    foreach ($requiredFields as $field) {
        if (empty(getPost($field))) {
            $missing[] = $field;
        }
    }

    if (!empty($missing)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Field wajib belum diisi: ' . implode(', ', $missing)
        ]);
        exit;
    }

    // ======================================================
    // FORMAT DATETIME
    // ======================================================
    $performedDateTime =
        getPost('performedDateTime_tanggal') . 'T' .
        getPost('performedDateTime_jam') . ':00+07:00';

    // ======================================================
    // NOTE (OPSIONAL)
    // ======================================================
    $note_text = getPost('note_text');
    if (empty($note_text)) {
        $note_text = 'Tidak ada catatan';
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
    // AMBIL KONFIGURASI SATUSEHAT AKTIF
    // ======================================================
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

    $url_api = rtrim($config['url_connection_satu_sehat'], '/');
    $url_procedure = $url_api . '/fhir-r4/v1/Procedure';

    // ======================================================
    // SUSUN PAYLOAD PROCEDURE (FHIR R4 VALID)
    // ======================================================
    $payload = [
        "resourceType" => "Procedure",
        "status"       => getPost('status'),

        "category" => [
            "coding" => [[
                "system"  => getPost('category_system'),
                "code"    => getPost('category_code'),
                "display" => getPost('category_display')
            ]]
        ],

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
            "reference" => "Encounter/" . getPost('encounter_reference')
        ],

        "performedDateTime" => $performedDateTime,

        "basedOn" => [[
            "reference" => "ServiceRequest/" . getPost('basedOn_reference')
        ]],

        "performer" => [[
            "actor" => [
                "reference" => "Practitioner/" . getPost('performer_actor_reference'),
                "display"   => getPost('performer_actor_display')
            ]
        ]]
    ];

    // ======================================================
    // OPTIONAL: reasonCode
    // ======================================================
    if (!empty($_POST['reasonCode_text'])) {
        foreach ($_POST['reasonCode_text'] as $i => $text) {
            $payload['reasonCode'][] = [
                "text" => trim($text),
                "coding" => [[
                    "system"  => $_POST['reasonCode_coding_system'][$i] ?? '',
                    "code"    => $_POST['reasonCode_coding_code'][$i] ?? '',
                    "display" => $_POST['reasonCode_coding_display'][$i] ?? ''
                ]]
            ];
        }
    }

    // ======================================================
    // ENCODE JSON
    // ======================================================
    $payload_json = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Gagal membuat JSON payload.'
        ]);
        exit;
    }

    // ======================================================
    // KIRIM KE SATUSEHAT
    // ======================================================
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => $url_procedure,
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

    // ======================================================
    // HANDLE ERROR CURL
    // ======================================================
    if ($curl_error) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'cURL Error: ' . $curl_error
        ]);
        exit;
    }

    // ======================================================
    // DECODE RESPONSE
    // ======================================================
    $result = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Response bukan JSON valid.',
            'response_raw' => substr($response, 0, 300)
        ]);
        exit;
    }

    // ======================================================
    // VALIDASI RESPONSE SATUSEHAT
    // ======================================================
    if ($http_code !== 201) {
        $msg = 'Gagal mengirim Procedure ke SATUSEHAT';

        if (($result['resourceType'] ?? '') === 'OperationOutcome') {
            $msg = $result['issue'][0]['details']['text']
                ?? $result['issue'][0]['diagnostics']
                ?? $msg;
        }

        echo json_encode([
            'status'  => 'error',
            'message' => $msg,
            'http_code' => $http_code
        ]);
        exit;
    }

    // ======================================================
    // SIMPAN ID PROCEDURE KE DATABASE
    // ======================================================
    $id_procedure = $result['id'] ?? null;

    if ($id_procedure) {
        $upd = $Conn->prepare("
            UPDATE radiologi 
            SET id_procedure = ? 
            WHERE id_radiologi = ?
        ");
        $upd->bind_param("si", $id_procedure, $id_radiologi);
        $upd->execute();
        $upd->close();
    }

    // ======================================================
    // RESPONSE SUKSES
    // ======================================================
    echo json_encode([
        'status'        => 'success',
        'message'       => 'Procedure berhasil dikirim ke SATUSEHAT',
        'id_procedure'  => $id_procedure,
        'id_radiologi'  => $id_radiologi,
        'resource_url'  => $url_api . '/fhir-r4/v1/Procedure/' . $id_procedure
    ]);
    exit;

?>