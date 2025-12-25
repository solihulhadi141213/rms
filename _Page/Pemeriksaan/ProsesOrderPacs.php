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
        'PatientID',
        'IHSPatientNumber',
        'PatientName',
        'PatientBirthDate',
        'PatientSex',
        'RegistrationDate',
        'RegistrationID',
        'EncounterUUID',
        'ServiceRequestUUID',
        'ReferringDoctorID',
        'IHSReferringDoctor',
        'ReferringDoctor',
        'SupportingDoctorID',
        'IHSSupportingDoctor',
        'SupportingDoctor',
        'InstitutionBranchID',
        'InstitutionBranchName',
        'ProcedureID',
        'RequestedProcedureName',
        'RequestedProcedureCode',
        'RequestedSystemProcedure',
        'Modality',
        'Clinical'
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
    // GENERATE TOKEN PACS
    // ======================================================
    $tokenResult = generateTokenPacs($Conn);

    if ($tokenResult['status'] !== 'success') {
        echo json_encode([
            'status'  => 'error',
            'message' => $tokenResult['message']
        ]);
        exit;
    }

    $tokenPacs = $tokenResult['token'];
    
    // ======================================================
    // AMBIL KONFIGURASI PACS AKTIF
    // ======================================================
    $status_connection_pacs = 1;
    $stmt = $Conn->prepare("SELECT * FROM connection_pacs WHERE status_connection_pacs = ?");
    $stmt->bind_param("i", $status_connection_pacs);
    $stmt->execute();
    $result = $stmt->get_result();
    $config = $result->fetch_assoc();
    $stmt->close();

    if (!$config) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Koneksi PACS tidak ditemukan.'
        ]);
        exit;
    }

    $url_connection_pacs      = rtrim($config['url_connection_pacs'], '/');
    $url_connection_pacs_full = $url_connection_pacs . '/api/dicom/patient-worklist';

    // ======================================================
    // SUSUN PAYLOAD PACS
    // ======================================================
    $payload = [
        "PatientName"           => getPost('PatientName'),
        "PatientID"             => getPost('PatientID'),
        "PatientBirthDate"      => getPost('PatientBirthDate'),
        "PatientSex"            => getPost('PatientSex'),
        "ReferringDoctor"       => getPost('ReferringDoctor'),
        "SupportingDoctor"      => getPost('SupportingDoctor'),
        "ReferringDoctorID"     => getPost('ReferringDoctorID'),
        "SupportingDoctorID"    => getPost('SupportingDoctorID'),
        "RegistrationDate"      => getPost('RegistrationDate'),
        "RegistrationID"        => getPost('RegistrationID'),
        "InstitutionBranchID"   => getPost('InstitutionBranchID'),
        "InstitutionBranchName" => getPost('InstitutionBranchName'),
        "IHSPatientNumber"      => getPost('IHSPatientNumber'),
        "IHSSupportingDoctor"   => getPost('IHSSupportingDoctor'),
        "EncounterUUID"         => getPost('EncounterUUID'),
        "ServiceRequestUUID"    => getPost('ServiceRequestUUID'),
        "ScheduledProcedure" => [[
            "ProcedureID"              => getPost('ProcedureID'),
            "AccessionNumber"          => getPost('AccessionNumber'),
            "RequestedProcedureName"   => getPost('RequestedProcedureName'),
            "RequestedProcedureCode"   => getPost('RequestedProcedureCode'),
            "RequestedSystemProcedure" => getPost('RequestedSystemProcedure'),
            "Modality"                 => getPost('Modality'),
            "Clinical"                 => getPost('Clinical')
        ]],
        "ScheduledProcedureStepSequence"    => "",
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
    // KIRIM KE PACS
    // ======================================================
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => $url_connection_pacs_full,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $payload_json,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $tokenPacs,
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
    // VALIDASI RESPONSE
    // ======================================================
    if ($http_code !== 201) {
        $msg = 'Gagal mengirim Order Ke PACS <br>Response : <code>'.$response.'</code> <br>Payload : <code>'.$payload_json.'</code>';

        echo json_encode([
            'status'  => 'error',
            'message' => $msg,
            'http_code' => $http_code
        ]);
        exit;
    }

    // ======================================================
    // UPDATE pacs pada tabel radiologi menjadi 1
    // ======================================================
    $pacs = 1;

    $upd = $Conn->prepare("UPDATE radiologi SET pacs = ? WHERE id_radiologi = ?");
    $upd->bind_param("ii", $pacs, $id_radiologi);
    $upd->execute();
    $upd->close();

    // ======================================================
    // RESPONSE SUKSES
    // ======================================================
    echo json_encode([
        'status'        => 'success',
        'message'       => 'Order Berhasil Dikirim Ke PACS',
        'id_radiologi'  => $id_radiologi
    ]);
    exit;

?>