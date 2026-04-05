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

    //vALIDASI 'id_radiologi_dicom_conv'
    if(empty($_POST['id_radiologi_dicom_conv'])){
        echo json_encode([
            'status'  => 'error',
            'message' => 'ID File Tidak Boleh Kosong!.'
        ]);
        exit;
    }

    $id_radiologi_dicom_conv = validateAndSanitizeInput($_POST['id_radiologi_dicom_conv']);

    //Buka 'radiologi_dicom_conv' Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM radiologi_dicom_conv WHERE id_radiologi_dicom_conv = ?");
    $Qry->bind_param("s", $id_radiologi_dicom_conv);
    if (!$Qry->execute()) {
        $error=$Conn->error;
        echo json_encode([
            'status'  => 'error',
            'message' => 'Terjadi kesalahan pada saat membuka data dari database!<br>Keterangan : '.$error.''
        ]);
        exit;
    }
    $Result = $Qry->get_result();
    $Data = $Result->fetch_assoc();
    $Qry->close();

    // Jika Data Tidak Ditemukan
    if(empty($Data['id_radiologi_dicom_conv'])){
        echo json_encode([
            'status'  => 'error',
            'message' => 'Data Tidak Ditemukan!'
        ]);
        exit;
    }

    //Buat Variabel
    $filename         = $Data['filename'];
    $orthanc_id       = $Data['orthanc_id'];
    $ParentStudy      = $Data['ParentStudy'];
    if(!empty($orthanc_id)){
        echo json_encode([
            'status'  => 'error',
            'message' => 'File Sudah Di Kirim Sebelumnya!'
        ]);
        exit;
    }

    // Buka pengaturan Orthanc
    $status_connection_orthanc = 1;
    $QryOrt = $Conn->prepare("SELECT * FROM connection_orthanc WHERE status_connection_orthanc = ?");
    $QryOrt->bind_param("i", $status_connection_orthanc);
    if (!$QryOrt->execute()) {
        $error=$Conn->error;
        echo json_encode([
            'status'  => 'error',
            'message' => 'Terjadi kesalahan pada saat membuka pengaturan Orthanc data dari database!<br>Keterangan : '.$Conn->error.''
        ]);
        exit;
    }
    $ResultOrt = $QryOrt->get_result();
    $DataOrt = $ResultOrt->fetch_assoc();
    $QryOrt->close();

    // Jika Data Tidak Ditemukan
    if(empty($DataOrt['id_connection_orthanc'])){
        echo json_encode([
            'status'  => 'error',
            'message' => 'Pengaturan Orthanc Tidak Ditemukan!'
        ]);
        exit;
    }
    //Buat Variabel
    $username_connection_orthanc = $DataOrt['username_connection_orthanc'];
    $password_connection_orthanc = $DataOrt['password_connection_orthanc'];
    $url_connection_orthanc      = $DataOrt['url_connection_orthanc'];

    // Path File
    $path_file = "../../_DCM/$filename";

    // Validasi file ada
    if (!file_exists($path_file)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'File DICOM tidak ditemukan pada server!'
        ]);
        exit;
    }

    // Baca isi file DICOM
    $dicomContent = file_get_contents($path_file);
    if ($dicomContent === false) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Gagal membaca file DICOM!'
        ]);
        exit;
    }

    // Endpoint upload Orthanc
    $orthanc_endpoint = rtrim($url_connection_orthanc, '/') . '/instances';

    // Inisialisasi CURL
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $orthanc_endpoint);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $dicomContent);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/dicom',
        'Content-Length: ' . strlen($dicomContent)
    ]);

    // Authentication Orthanc
    curl_setopt($ch, CURLOPT_USERPWD, $username_connection_orthanc . ":" . $password_connection_orthanc);

    // Jika HTTPS self signed
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    // Execute request
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // Handle CURL error
    if (curl_errno($ch)) {
        $curl_error = curl_error($ch);
        curl_close($ch);

        echo json_encode([
            'status'  => 'error',
            'message' => 'Gagal koneksi ke Orthanc: ' . $curl_error
        ]);
        exit;
    }

    curl_close($ch);

    // Decode response
    $responseData = json_decode($response, true);

    // Validasi response
    if ($http_code != 200 || empty($responseData['ID'])) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Upload ke Orthanc gagal!',
            'response' => $response
        ]);
        exit;
    }

    // Ambil response Orthanc
    $orthanc_id  = $responseData['ID'] ?? '';
    $ParentStudy = $responseData['ParentStudy'] ?? '';
    $status_orthanc = $responseData['Status'] ?? '';

    // Update database
    $Update = $Conn->prepare("
        UPDATE radiologi_dicom_conv 
        SET orthanc_id = ?, ParentStudy = ?
        WHERE id_radiologi_dicom_conv = ?
    ");
    $Update->bind_param("sss", $orthanc_id, $ParentStudy, $id_radiologi_dicom_conv);

    if (!$Update->execute()) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Upload berhasil, tetapi gagal update database!',
            'orthanc_response' => $responseData
        ]);
        exit;
    }

    $Update->close();
    // Response Berhasil
    echo json_encode([
        'status'                  => 'success',
        'message'                 => 'File Dicom Berhasil Disimpan Ke Orthanc',
        'id_radiologi_dicom_conv' => $id_radiologi_dicom_conv
    ]);
    exit;
?>