<?php
    // ============================================================================
    // KONEKSI, SESSION, GLOBAL FUNCTION
    // ============================================================================
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    date_default_timezone_set("Asia/Jakarta");

    // ============================================================================
    // RESPONSE HEADER
    // ============================================================================
    header('Content-Type: application/json');

    // ============================================================================
    // VALIDASI SESSION
    // ============================================================================
    if (empty($SessionIdAccess)) {

        echo json_encode([
            'status'  => 'error',
            'message' => 'Sesi akses telah berakhir. Silakan login ulang.'
        ]);

        exit;
    }

    // ============================================================================
    // FUNCTION AMAN AMBIL POST
    // ============================================================================
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

        $value = str_replace(
            ["\r", "\n"],
            ' ',
            $value
        );

        $value = preg_replace(
            '/\s+/',
            ' ',
            $value
        );

        return $value;
    }

    // ============================================================================
    // FORMAT DICOM PERSON NAME
    // ============================================================================
    function dicomPersonName($name)
    {
        $name = trim($name);

        $name = preg_replace('/\s+/', '^', $name);

        return strtoupper($name);
    }

    // ============================================================================
    // GENERATE STUDY UID STABIL
    // ============================================================================
    /*
    |--------------------------------------------------------------------------
    | UID STABIL BERDASARKAN ACCESSION
    |--------------------------------------------------------------------------
    | Jangan random agar modality tidak membaca sebagai study baru
    |--------------------------------------------------------------------------
    */

    function generateStableStudyUID($accession)
    {
        $hash = preg_replace(
            '/[^0-9]/',
            '',
            crc32($accession)
        );

        return '1.2.826.0.1.3680043.2.1125.' . $hash;
    }

    // ============================================================================
    // VALIDASI INPUT
    // ============================================================================
    $id_radiologi                      = (int) getPost('id_radiologi');

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

    // ============================================================================
    // VALIDASI FIELD WAJIB
    // ============================================================================
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

    if (empty($Modality)) {

        echo json_encode([
            'status'  => 'error',
            'message' => 'Modality tidak boleh kosong.'
        ]);

        exit;
    }

    // ============================================================================
    // VALIDASI FORMAT TANGGAL DICOM
    // ============================================================================
    if (!empty($PatientBirthDate)) {

        $PatientBirthDate = preg_replace(
            '/[^0-9]/',
            '',
            $PatientBirthDate
        );

        if (strlen($PatientBirthDate) != 8) {

            echo json_encode([
                'status'  => 'error',
                'message' => 'Format PatientBirthDate harus YYYYMMDD'
            ]);

            exit;
        }
    }

    // ============================================================================
    // VALIDASI FORMAT SPS DATE
    // ============================================================================
    if (!empty($ScheduledProcedureStepStartDate)) {

        $ScheduledProcedureStepStartDate = preg_replace(
            '/[^0-9]/',
            '',
            $ScheduledProcedureStepStartDate
        );

        if (strlen($ScheduledProcedureStepStartDate) != 8) {

            echo json_encode([
                'status'  => 'error',
                'message' => 'Format ScheduledProcedureStepStartDate harus YYYYMMDD'
            ]);

            exit;
        }
    }

    // ============================================================================
    // VALIDASI FORMAT SPS TIME
    // ============================================================================
    if (!empty($ScheduledProcedureStepStartTime)) {

        $ScheduledProcedureStepStartTime = preg_replace(
            '/[^0-9]/',
            '',
            $ScheduledProcedureStepStartTime
        );

        $ScheduledProcedureStepStartTime = substr(
            $ScheduledProcedureStepStartTime,
            0,
            6
        );
    }

    // ============================================================================
    // FORMAT NILAI DICOM
    // ============================================================================
    $PatientName = dicomPersonName(
        $PatientName
    );

    $ScheduledPerformingPhysicianName = dicomPersonName(
        $ScheduledPerformingPhysicianName
    );

    // ============================================================================
    // STUDY UID STABIL
    // ============================================================================
    $StudyInstanceUID = generateStableStudyUID(
        $AccessionNumber
    );

    // ============================================================================
    // BUKA KONFIGURASI ORTHANC
    // ============================================================================
    $status_connection_orthanc = 1;

    $stmt = $Conn->prepare("
        SELECT *
        FROM connection_orthanc
        WHERE status_connection_orthanc = ?
    ");

    $stmt->bind_param(
        "i",
        $status_connection_orthanc
    );

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

    // ============================================================================
    // URL ORTHANC
    // ============================================================================
    $url_connection_orthanc = rtrim(
        $config['url_connection_orthanc'],
        '/'
    );

    $url_worklist = $url_connection_orthanc .
                    '/worklists/' .
                    rawurlencode($AccessionNumber);

    $username_connection_orthanc =
        $config['username_connection_orthanc'];

    $password_connection_orthanc =
        $config['password_connection_orthanc'];

    // ============================================================================
    // CEK EXISTING WORKLIST
    // ============================================================================
    /*
    |--------------------------------------------------------------------------
    | Penting untuk menghindari duplicate pada modality
    |--------------------------------------------------------------------------
    */

    $curl_check = curl_init();

    curl_setopt_array($curl_check, [

        CURLOPT_URL => $url_worklist,

        CURLOPT_RETURNTRANSFER => true,

        CURLOPT_CUSTOMREQUEST => 'GET',

        CURLOPT_HTTPAUTH => CURLAUTH_BASIC,

        CURLOPT_USERPWD =>
            $username_connection_orthanc .
            ':' .
            $password_connection_orthanc,

        CURLOPT_TIMEOUT => 15,

        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false
    ]);

    $response_check = curl_exec($curl_check);

    $http_check = curl_getinfo(
        $curl_check,
        CURLINFO_HTTP_CODE
    );

    curl_close($curl_check);

    // ============================================================================
    // DELETE EXISTING WORKLIST
    // ============================================================================
    /*
    |--------------------------------------------------------------------------
    | Sangat penting agar modality tidak cache duplicate item
    |--------------------------------------------------------------------------
    */

    if ($http_check == 200) {

        $curl_delete = curl_init();

        curl_setopt_array($curl_delete, [

            CURLOPT_URL => $url_worklist,

            CURLOPT_RETURNTRANSFER => true,

            CURLOPT_CUSTOMREQUEST => 'DELETE',

            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,

            CURLOPT_USERPWD =>
                $username_connection_orthanc .
                ':' .
                $password_connection_orthanc,

            CURLOPT_TIMEOUT => 15,

            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ]);

        $response_delete = curl_exec($curl_delete);

        $http_delete = curl_getinfo(
            $curl_delete,
            CURLINFO_HTTP_CODE
        );

        $curl_delete_error = curl_error(
            $curl_delete
        );

        curl_close($curl_delete);

        if ($curl_delete_error) {

            echo json_encode([
                'status'  => 'error',
                'message' =>
                    'Gagal menghapus worklist lama: ' .
                    $curl_delete_error
            ]);

            exit;
        }

        /*
        |--------------------------------------------------------------------------
        | Orthanc:
        | 200 = deleted
        | 404 = not found
        |--------------------------------------------------------------------------
        */

        if ($http_delete != 200 && $http_delete != 404) {

            echo json_encode([
                'status'  => 'error',
                'message' => 'Gagal menghapus worklist lama',
                'http_code' => $http_delete
            ]);

            exit;
        }
    }

    // ============================================================================
    // PAYLOAD ORTHANC
    // ============================================================================
    $payload_orthanc = [

        "Tags" => [

            // ====================================================================
            // PATIENT
            // ====================================================================

            "IssuerOfPatientID" =>
                $IssuerOfPatientID,

            "PatientName" =>
                $PatientName,

            "PatientID" =>
                $PatientID,

            "PatientSex" =>
                $PatientSex,

            "PatientBirthDate" =>
                $PatientBirthDate,

            // ====================================================================
            // STUDY
            // ====================================================================

            "AccessionNumber" =>
                $AccessionNumber,

            "StudyInstanceUID" =>
                $StudyInstanceUID,

            "RequestedProcedureID" =>
                $AccessionNumber,

            "StudyDescription" =>
                $ScheduledProcedureStepDescription,

            "RequestedProcedureDescription" =>
                $ScheduledProcedureStepDescription,

            "ReferringPhysicianName" =>
                $ScheduledPerformingPhysicianName,

            // ====================================================================
            // SPS
            // ====================================================================

            "ScheduledProcedureStepSequence" => [[

                "Modality" =>
                    $Modality,

                "ScheduledStationAETitle" =>
                    $ScheduledStationAETitle,

                "ScheduledProcedureStepStartDate" =>
                    $ScheduledProcedureStepStartDate,

                "ScheduledProcedureStepStartTime" =>
                    $ScheduledProcedureStepStartTime,

                "ScheduledPerformingPhysicianName" =>
                    $ScheduledPerformingPhysicianName,

                "ScheduledProcedureStepDescription" =>
                    $ScheduledProcedureStepDescription
            ]]
        ]
    ];

    // ============================================================================
    // ENCODE JSON
    // ============================================================================
    $payload_orthanc_json = json_encode(
        $payload_orthanc,
        JSON_UNESCAPED_SLASHES |
        JSON_UNESCAPED_UNICODE
    );

    if (!$payload_orthanc_json) {

        echo json_encode([
            'status'  => 'error',
            'message' => 'Gagal membuat JSON payload'
        ]);

        exit;
    }

    // ============================================================================
    // CURL PUT WORKLIST
    // ============================================================================
    $curl_orthanc = curl_init();

    curl_setopt_array($curl_orthanc, [

        CURLOPT_URL => $url_worklist,

        CURLOPT_RETURNTRANSFER => true,

        CURLOPT_CUSTOMREQUEST => 'PUT',

        CURLOPT_POSTFIELDS => $payload_orthanc_json,

        CURLOPT_HTTPAUTH => CURLAUTH_BASIC,

        CURLOPT_USERPWD =>
            $username_connection_orthanc .
            ':' .
            $password_connection_orthanc,

        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json'
        ],

        CURLOPT_TIMEOUT => 30,

        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false
    ]);

    $response_orthanc = curl_exec(
        $curl_orthanc
    );

    $http_code_orthanc = curl_getinfo(
        $curl_orthanc,
        CURLINFO_HTTP_CODE
    );

    $curl_orthanc_error = curl_error(
        $curl_orthanc
    );

    curl_close($curl_orthanc);

    // ============================================================================
    // HANDLE CURL ERROR
    // ============================================================================
    if ($curl_orthanc_error) {

        echo json_encode([
            'status'  => 'error',
            'message' =>
                'CURL Orthanc Error: ' .
                $curl_orthanc_error
        ]);

        exit;
    }

    // ============================================================================
    // VALIDASI RESPONSE
    // ============================================================================
    /*
    |--------------------------------------------------------------------------
    | Orthanc MWL:
    | 200 = success
    |--------------------------------------------------------------------------
    */

    if ($http_code_orthanc != 200) {

        echo json_encode([
            'status'  => 'error',

            'message' =>
                'Gagal mengirim Worklist ke Orthanc',

            'http_code' =>
                $http_code_orthanc,

            'response_raw' =>
                substr($response_orthanc, 0, 500),

            'payload' =>
                $payload_orthanc
        ]);

        exit;
    }

    // ============================================================================
    // UPDATE DATABASE
    // ============================================================================
    $orthanc = 1;

    $upd = $Conn->prepare("
        UPDATE radiologi
        SET orthanc = ?
        WHERE id_radiologi = ?
    ");

    $upd->bind_param(
        "ii",
        $orthanc,
        $id_radiologi
    );

    $upd->execute();

    $upd->close();

    // ============================================================================
    // RESPONSE SUCCESS
    // ============================================================================
    echo json_encode([

        'status' => 'success',

        'message' =>
            'Order Worklist berhasil dikirim ke Orthanc',

        'id_radiologi' =>
            $id_radiologi,

        'accession_number' =>
            $AccessionNumber,

        'study_uid' =>
            $StudyInstanceUID
    ]);

    exit;
?>