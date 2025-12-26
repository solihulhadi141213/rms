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
        'resourceType',
        'status',
        'numberOfSeries',
        'numberOfInstances',
        'identifier_system',
        'identifier_value',
        'subject_reference',
        'subject_display',
        'modality_system',
        'modality_code',
        'modality_display',
        'series_uid',
        'series_description',
        'started_tanggal',
        'started_jam',
        'basedOn_reference'
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
    $started =
        getPost('started_tanggal') . 'T' .
        getPost('started_jam') . ':00+07:00';


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

    $organization_id   = $config['organization_id'];
    $url_api           = rtrim($config['url_connection_satu_sehat'], '/');
    $url_imaging_study = $url_api . '/fhir-r4/v1/ImagingStudy';

    // Validasi Status
    $allowedStatus = ['registered','available','cancelled','entered-in-error','unknown'];
    if (!in_array(getPost('status'), $allowedStatus)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Status ImagingStudy tidak valid'
        ]);
        exit;
    }

    // Validasi UUID
    if (!preg_match('/^[0-9.]+$/', getPost('series_uid'))) {
        echo json_encode([
            'status' => 'error',
            'message' => 'UID Series tidak valid (harus numeric dot format)'
        ]);
        exit;
    }

    // ======================================================
    // SUSUN PAYLOAD IMAGING STUDY (FHIR R4 VALID)
    // ======================================================
    $basedOnRef = getPost('basedOn_reference');
    // pastikan format FHIR
    if (!preg_match('/^[A-Za-z]+\/[A-Za-z0-9\-\.]+$/', $basedOnRef)) {
        $basedOnRef = 'ServiceRequest/' . $basedOnRef;
    }
    $payload = [
        "resourceType" => "ImagingStudy",
        "status" => getPost('status'),

       "identifier" => [
            [
                "use" => "usual",
                "type" => [
                    "coding" => [
                        [
                            "system" => "http://terminology.hl7.org/CodeSystem/v2-0203",
                            "code"   => "ACSN"
                        ]
                    ]
                ],
                "system" => "http://sys-ids.kemkes.go.id/acsn/" . $organization_id,
                "value"  => getPost('identifier_value')
            ]
        ],

        "subject" => [
            "reference" => getPost('subject_reference'),
            "display"   => getPost('subject_display')
        ],

        "basedOn" => [
            [
                "reference" => $basedOnRef
            ]
        ],

        "numberOfSeries"    => (int) getPost('numberOfSeries'),
        "numberOfInstances" => (int) getPost('numberOfInstances'),

        "modality" => [
            [
                "system"  => getPost('modality_system'),
                "code"    => getPost('modality_code'),
                "display" => getPost('modality_display')
            ]
        ],

        "series" => [
            [
                "uid" => getPost('series_uid'),
                "number" => 1,

                "modality" => [
                    "system" => getPost('modality_system'),
                    "code"   => getPost('modality_code')
                ],

                "description" => getPost('series_description'),
                "numberOfInstances" => (int) getPost('numberOfInstances'),
                "started" => $started
            ]
        ]
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
        CURLOPT_URL => $url_imaging_study,
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
        $msg = 'Gagal mengirim Imaging Study ke SATUSEHAT';

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
    // SIMPAN ID IMAGING STUDY KE DATABASE
    // ======================================================
    $id_imaging_study = $result['id'] ?? null;

    if ($id_imaging_study) {
        $upd = $Conn->prepare("
            UPDATE radiologi 
            SET id_imaging_study = ? 
            WHERE id_radiologi = ?
        ");
        $upd->bind_param("si", $id_imaging_study, $id_radiologi);
        $upd->execute();
        $upd->close();
    }

    // ======================================================
    // RESPONSE SUKSES
    // ======================================================
    echo json_encode([
        'status'           => 'success',
        'message'          => 'Imaging Study berhasil dikirim ke SATUSEHAT',
        'id_imaging_study' => $id_imaging_study,
        'id_radiologi'     => $id_radiologi,
        'resource_url'     => $url_api . '/fhir-r4/v1/ImagingStudy/' . $id_imaging_study
    ]);
    exit;

?>