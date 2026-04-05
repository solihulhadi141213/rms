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
    $organization_id         = getPost('organization_id');
    $id_radiologi_dicom_conv = getPost('id_radiologi_dicom_conv');
    $id_radiologi            = getPost('id_radiologi');
    $ihs_pasien              = getPost('ihs_pasien');
    $nama_pasien             = getPost('nama_pasien');
    $accession_number        = getPost('accession_number');
    $ServiceRequest          = getPost('ServiceRequest');
    $modality_code           = getPost('modality_code');
    $modality_display        = getPost('modality_display');
    $SeriesInstanceUID       = getPost('SeriesInstanceUID');
    $StudyInstanceUID        = getPost('StudyInstanceUID');
    $SOPInstanceUID          = getPost('SOPInstanceUID');
    $SOPClassUID             = getPost('SOPClassUID');
    $started                 = getPost('started');
    $title                   = getPost('title');

    if (empty($organization_id)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'ID Organization tidak boleh kosong.'
        ]);
        exit;
    }
    if (empty($id_radiologi_dicom_conv)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'ID File Dicom tidak boleh kosong.'
        ]);
        exit;
    }

    if (empty($id_radiologi)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'ID Radiologi tidak boleh kosong.'
        ]);
        exit;
    }

    if (empty($ihs_pasien)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'IHS Pasien tidak boleh kosong.'
        ]);
        exit;
    }

    if (empty($nama_pasien)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Nama Pasien tidak boleh kosong.'
        ]);
        exit;
    }

    if (empty($accession_number)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Accession Number tidak boleh kosong.'
        ]);
        exit;
    }

    if (empty($ServiceRequest)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Service Request tidak boleh kosong.'
        ]);
        exit;
    }

    if (empty($modality_code)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Kode Modalitas tidak boleh kosong.'
        ]);
        exit;
    }

    if (empty($modality_display)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Nama Modalitas tidak boleh kosong.'
        ]);
        exit;
    }

    if (empty($SeriesInstanceUID)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Series Instance UID tidak boleh kosong.'
        ]);
        exit;
    }

    if (empty($StudyInstanceUID)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Study Instance UID tidak boleh kosong.'
        ]);
        exit;
    }

    if (empty($SOPInstanceUID)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'SOP Instance UID tidak boleh kosong.'
        ]);
        exit;
    }

    if (empty($SOPClassUID)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'SOP Class UID tidak boleh kosong.'
        ]);
        exit;
    }

    if (empty($started)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Informasi STARTED tidak boleh kosong.'
        ]);
        exit;
    }

    if (empty($title)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Informasi Title tidak boleh kosong.'
        ]);
        exit;
    }

    // Menyusun Payload
    $payload = [
        "resourceType" => "ImagingStudy",

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
                "value"  => $accession_number
            ],
            [
                "system" => "urn:dicom:uid",
                "value" => "urn:oid:$StudyInstanceUID",
            ]
        ],

        "status" => "available",

        "modality" => [
            [
                "system"  => "http://dicom.nema.org/resources/ontology/DCM",
                "code"    => $modality_code,
                "display" => $modality_display
            ]
        ],

        "subject" => [
            "reference" => "Patient/$ihs_pasien",
            "display"   => $nama_pasien
        ],

        "basedOn" => [
            [
                "reference" => "ServiceRequest/$ServiceRequest"
            ]
        ],

        "numberOfSeries"    => 1,
        "numberOfInstances" => 1,

        "series" => [
            [
                "uid" => $SeriesInstanceUID,
                "number" => 1,
                "modality" => [
                    "system" => "http://dicom.nema.org/resources/ontology/DCM",
                    "code"   => $modality_code
                ],
                "numberOfInstances" => 1,
                "started" => $started,
                "instance" => [
                    [
                        "uid" => $SOPInstanceUID,
                        "sopClass" => [
                            "system" => "urn:ietf:rfc:3986",
                            "code" => "urn:oid:$SOPClassUID"
                        ],
                        "number" => 1,
                        "title" => $title
                    ]
                ]
            ]
        ]
    ];


    // Ecode Payload ke JSON
    $payload_json = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Gagal membuat JSON payload.'
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

    $organization_id   = $config['organization_id'];
    $url_api           = rtrim($config['url_connection_satu_sehat'], '/');
    $url_imaging_study = $url_api . '/fhir-r4/v1/ImagingStudy';

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
    // SIMPAN ID IMAGING STUDY KE DATABASE 'radiologi_dicom_conv'
    // ======================================================
    $id_imaging_study = $result['id'] ?? null;

    if ($id_imaging_study) {
        $upd = $Conn->prepare("
            UPDATE radiologi_dicom_conv 
            SET id_imaging_study = ? 
            WHERE id_radiologi_dicom_conv = ?
        ");
        $upd->bind_param("si", $id_imaging_study, $id_radiologi_dicom_conv);
        $upd->execute();
        $upd->close();
    }

    // ======================================================
    // RESPONSE SUKSES
    // ======================================================
    echo json_encode([
        'status'                  => 'success',
        'message'                 => 'Imaging Study berhasil dikirim ke SATUSEHAT',
        'id_radiologi_dicom_conv' => $id_radiologi_dicom_conv
    ]);
    exit;

?>