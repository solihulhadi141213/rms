<?php
    // ======================================================
    // KONEKSI, SESSION, GLOBAL FUNCTION
    // ======================================================
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";
    include "../../_Config/SettingGeneral.php";

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
        'resourceType',
        'status',
        'category_coding_system',
        'category_coding_code',
        'category_coding_display',
        'code_coding_system',
        'code_coding_code',
        'code_coding_display',
        'subject_reference',
        'encounter_reference',
        'basedOn_reference',
        'imagingStudy_reference',
        'result_reference',
        'performer_reference',
        'performer_reference_name',
        'conclusionCode_coding_system',
        'conclusionCode_coding_code',
        'conclusion'
    ];
    // Untuk conclusionCode_coding_code yang ditangkap dari form memiliki format code|display

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
    $stmt = $Conn->prepare("SELECT url_connection_satu_sehat, organization_id FROM connection_satu_sehat WHERE status_connection_satu_sehat = ?
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

    $organization_id       = $config['organization_id'];
    $url_api               = rtrim($config['url_connection_satu_sehat'], '/');
    $url_diagnostic_report = $url_api . '/fhir-r4/v1/DiagnosticReport';

    // Validasi Status
    $allowedStatus = ['registered','preliminary','final','amended'];
    if (!in_array(getPost('status'), $allowedStatus)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Status Diagnostic Report tidak valid'
        ]);
        exit;
    }

    // ======================================================
    // SUSUN PAYLOAD DIAGNOSTIC REPORT (FHIR R4)
    // ======================================================

    // Pecah conclusionCode (format: code|display)
    list($conclusion_code, $conclusion_display) = array_pad(
        explode('|', getPost('conclusionCode_coding_code')),
        2,
        ''
    );

    // Pastikan basedOn format FHIR
    $basedOnRef = getPost('basedOn_reference');
    if (!preg_match('/^[A-Za-z]+\/.+$/', $basedOnRef)) {
        $basedOnRef = 'ServiceRequest/' . $basedOnRef;
    }

    // Pastikan imagingStudy format FHIR
    $imagingRef = getPost('imagingStudy_reference');
    if (!preg_match('/^[A-Za-z]+\/.+$/', $imagingRef)) {
        $imagingRef = 'ImagingStudy/' . $imagingRef;
    }

    // Pastikan subject format FHIR
    $subjectRef = getPost('subject_reference');
    if (!preg_match('/^[A-Za-z]+\/.+$/', $subjectRef)) {
        $subjectRef = 'Patient/' . $subjectRef;
    }

    // Pastikan Encounter format FHIR
    $EncounterRef = getPost('encounter_reference');
    if (!preg_match('/^[A-Za-z]+\/.+$/', $EncounterRef)) {
        $EncounterRef = 'Encounter/' . $EncounterRef;
    }

    // Pastikan performer_reference format FHIR
    $performer_reference = getPost('performer_reference');
    if (!preg_match('/^[A-Za-z]+\/.+$/', $performer_reference)) {
        $performer_reference = 'Practitioner/' . $performer_reference;
    }

    // Pastikan result_reference format FHIR
    $result_reference = getPost('result_reference');
    if (!preg_match('/^[A-Za-z]+\/.+$/', $result_reference)) {
        $result_reference = 'Observation/' . $result_reference;
    }

    $conclusion = strip_tags(getPost('conclusion'));
    $payload = [
        'resourceType' => 'DiagnosticReport',
        'status'       => getPost('status'),

        'category' => [[
            'coding' => [[
                'system'  => getPost('category_coding_system'),
                'code'    => getPost('category_coding_code'),
                'display' => getPost('category_coding_display')
            ]]
        ]],

        'code' => [
            'coding' => [[
                'system'  => getPost('code_coding_system'),
                'code'    => getPost('code_coding_code'),
                'display' => getPost('code_coding_display')
            ]]
        ],

        'subject' => [
            'reference' => $subjectRef
        ],

        'encounter' => [
            'reference' => $EncounterRef
        ],

        'performer' => [
            [
                'reference' => $performer_reference,
                'display'   => getPost('performer_reference_name'),
            ],
            [
                'reference' => 'Organization/' . $organization_id,
                'display'   => $company_name,
            ]
        ],

        'basedOn' => [[
            'reference' => $basedOnRef
        ]],

        'imagingStudy' => [[
            'reference' => $imagingRef
        ]],
        'result' => [[
            'reference' => $result_reference
        ]],

        'conclusionCode' => [[
            'coding' => [[
                'system'  => getPost('conclusionCode_coding_system'),
                'code'    => $conclusion_code,
                'display' => $conclusion_display
            ]]
        ]],

        'conclusion' => $conclusion
    ];


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
        CURLOPT_URL => $url_diagnostic_report,
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
        $msg = 'Gagal mengirim Diagnostic Report ke SATUSEHAT';

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
    // SIMPAN ID DIAGNOSTIC REPORT KE DATABASE
    // ======================================================
    $id_diagnostic_report = $result['id'] ?? null;

    if ($id_diagnostic_report) {
        $upd = $Conn->prepare("
            UPDATE radiologi 
            SET id_diagnostic_report = ? 
            WHERE id_radiologi = ?
        ");
        $upd->bind_param("si", $id_diagnostic_report, $id_radiologi);
        $upd->execute();
        $upd->close();
    }

    // ======================================================
    // RESPONSE SUKSES
    // ======================================================
    echo json_encode([
        'status'               => 'success',
        'message'              => 'Diagnostic Report berhasil dikirim ke SATUSEHAT',
        'id_diagnostic_report' => $id_diagnostic_report,
        'id_radiologi'         => $id_radiologi,
        'resource_url'         => $url_api . '/fhir-r4/v1/DiagnosticReport/' . $id_diagnostic_report
    ]);
    exit;

?>