<?php
    // KONEKSI, SESSION, GLOBAL FUNCTION
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    date_default_timezone_set("Asia/Jakarta");

    // RESPONSE HEADER
    header('Content-Type: application/json');

    // VALIDASI SESSION
    if (empty($SessionIdAccess)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Sesi akses telah berakhir. Silakan login ulang.'
        ]);
        exit;
    }

    // FUNGSI AMAN AMBIL POST
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

    // VALIDASI INPUT WAJIB
    $id_radiologi                      = getPost('id_radiologi');
    $IssuerOfPatientID                 = getPost('IssuerOfPatientID');
    $AccessionNumber                   = getPost('AccessionNumber');
    $PatientName                       = getPost('PatientName');
    $PatientID                         = getPost('PatientID');
    $PatientSex                        = getPost('PatientSex');
    $PatientBirthDate                  = getPost('PatientBirthDate');
    $Modality                          = getPost('Modality');
    $ScheduledStationAETitle           = getPost('ScheduledStationAETitle');
    $ScheduledProcedureStepStartDate   = getPost('ScheduledProcedureStepStartDate');
    $ScheduledProcedureStepStartTime   = getPost('ScheduledProcedureStepStartTime');
    $ScheduledPerformingPhysicianName  = getPost('ScheduledPerformingPhysicianName');
    $ScheduledProcedureStepDescription = getPost('ScheduledProcedureStepDescription');
    $ScheduledProcedureStepDescription = getPost('ScheduledProcedureStepDescription');

    if (empty($id_radiologi)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'ID Radiologi tidak boleh kosong.'
        ]);
        exit;
    }

    if (empty($IssuerOfPatientID)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Nama Faskes tidak boleh kosong.'
        ]);
        exit;
    }

    if (empty($AccessionNumber)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Accession Number tidak boleh kosong.'
        ]);
        exit;
    }

    if (empty($PatientName)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Nama pasien tidak boleh kosong.'
        ]);
        exit;
    }

    if (empty($PatientID)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'ID pasien tidak boleh kosong.'
        ]);
        exit;
    }
    
    // BUKA KONFIGURASI ORTHANC AKTIF
    $status_connection_orthanc = 1;
    $stmt = $Conn->prepare("SELECT * FROM connection_orthanc WHERE status_connection_orthanc = ?");
    $stmt->bind_param("i", $status_connection_orthanc);
    $stmt->execute();
    $result = $stmt->get_result();
    $config = $result->fetch_assoc();
    $stmt->close();

    if (!$config) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Koneksi Orthanc tidak ditemukan.'
        ]);
        exit;
    }

    $url_connection_orthanc      = rtrim($config['url_connection_orthanc'], '/');
    $url_connection_orthanc_full = "$url_connection_orthanc/worklists/$AccessionNumber";
    $username_connection_orthanc = $config['username_connection_orthanc'];
    $password_connection_orthanc = $config['password_connection_orthanc'];

    // SUSUN PAYLOAD ORTHANC
    $payload_orthanc = [
        "Tags" => [
            "IssuerOfPatientID"              => $IssuerOfPatientID,
            "AccessionNumber"                => $AccessionNumber,
            "PatientName"                    => $PatientName,
            "PatientID"                      => $PatientID,
            "PatientSex"                     => $PatientSex,
            "PatientBirthDate"               => $PatientBirthDate,
            "StudyDescription"               => $ScheduledProcedureStepDescription,
            "ReferringPhysicianName"         => $ScheduledPerformingPhysicianName,
            "ScheduledProcedureStepSequence" => [[
                "Modality"                          => $Modality,
                "ScheduledStationAETitle"           => $ScheduledStationAETitle,
                "ScheduledProcedureStepStartDate"   => $ScheduledProcedureStepStartDate,
                "ScheduledProcedureStepStartTime"   => $ScheduledProcedureStepStartTime,
                "ScheduledPerformingPhysicianName"  => $ScheduledPerformingPhysicianName,
                "ScheduledProcedureStepDescription" => $ScheduledProcedureStepDescription,
            ]]
        ]
    ];

    // ENCODE JSON
    $payload_orthanc_json = json_encode($payload_orthanc, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Gagal membuat JSON payload.'
        ]);
        exit;
    }

    // Mulai CULR Ortanc
    $curl_orthanc = curl_init();
    curl_setopt_array($curl_orthanc, [
        CURLOPT_URL => $url_connection_orthanc_full,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => 'PUT',
        CURLOPT_POSTFIELDS => $payload_orthanc_json,
        CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
        CURLOPT_USERPWD => ''.$username_connection_orthanc.':'.$password_connection_orthanc.'',
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json'
        ],
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false
    ]);

    $response_orthanc = curl_exec($curl_orthanc);
    $http_code_orthanc = curl_getinfo($curl_orthanc, CURLINFO_HTTP_CODE);
    $curl_orthanc_error = curl_error($curl_orthanc);
    curl_close($curl_orthanc);

    // Handdle Error Curl Ortanc
    if ($curl_orthanc_error) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'CURL Ortanc Error: ' . $curl_orthanc_error
        ]);
        exit;
    }

    // Decode Response Ortanc
    // Orthanc MWL biasanya tidak mengembalikan JSON
    if ($http_code_orthanc !== 200) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal mengirim Worklist ke Orthanc <br> Code: '.$http_code_orthanc.'',
            'http_code' => $http_code_orthanc,
            'response_raw' => substr($response_orthanc, 0, 300)
        ]);
        exit;
    }

    // Validasi Response Ortanc
    if ($http_code_orthanc !== 200) {
        $msg_orthanc = 'Gagal mengirim Order Ke Ortanc <br>Response : <code>'.$response_orthanc.'</code> <br>Payload : <code>'.$payload_json_orthanc.'</code>';

        echo json_encode([
            'status'  => 'error',
            'message' => $msg_orthanc,
            'http_code' => $http_code_orthanc
        ]);
        exit;
    }

    // UPDATE orthanc pada tabel radiologi menjadi 1
    $orthanc = 1;

    $upd = $Conn->prepare("UPDATE radiologi SET orthanc = ? WHERE id_radiologi = ?");
    $upd->bind_param("ii", $orthanc, $id_radiologi);
    $upd->execute();
    $upd->close();

    // ======================================================
    // RESPONSE SUKSES
    // ======================================================
    echo json_encode([
        'status'        => 'success',
        'message'       => 'Order Berhasil Dikirim Ke orthanc',
        'id_radiologi'  => $id_radiologi
    ]);
    exit;

?>