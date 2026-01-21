<?php
    // koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    // Zona Waktu
    date_default_timezone_set("Asia/Jakarta");

    // =======================
    // VALIDASI SESSION
    // =======================
    if (empty($SessionIdAccess)) {
        echo '
            <div class="alert alert-danger text-center">
                <small>Sesi Akses Sudah Berakhir! Silahkan Login Ulang.</small>
            </div>
        ';
        exit;
    }

    // =======================
    // VALIDASI INPUT
    // =======================
    if (empty($_POST['id_connection_orthanc'])) {
        echo '
            <div class="alert alert-danger text-center">
                <small>Koneksi Satu Sehat Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }

    $id_connection_orthanc = validateAndSanitizeInput($_POST['id_connection_orthanc']);

    // =======================
    // AMBIL DATA KONEKSI
    // =======================
    $Qry = $Conn->prepare("SELECT * FROM connection_orthanc WHERE id_connection_orthanc = ?");
    $Qry->bind_param("i", $id_connection_orthanc);

    if (!$Qry->execute()) {
        echo '
            <div class="alert alert-danger">
                <small>Error DB: '.$Conn->error.'</small>
            </div>
        ';
        exit;
    }

    $Result = $Qry->get_result();
    $Data   = $Result->fetch_assoc();
    $Qry->close();

    if (!$Data) {
        echo '
            <div class="alert alert-danger">
                <small>Data koneksi Satu Sehat tidak ditemukan.</small>
            </div>
        ';
        exit;
    }

    // =======================
    // VARIABEL
    // =======================
    $url_connection_orthanc      = rtrim($Data['url_connection_orthanc'], '/');
    $username_connection_orthanc = $Data['username_connection_orthanc'];
    $password_connection_orthanc = $Data['password_connection_orthanc'];

    // =======================
    // MULAI CURL
    // =======================
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL            => $url_connection_orthanc . '/system',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => false,
        CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
        CURLOPT_USERPWD => ''.$username_connection_orthanc.':'.$password_connection_orthanc.'',
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT        => 10,

        // DEV ONLY
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false
    ]);
    $response     = curl_exec($curl);
    $curl_error   = curl_error($curl);
    $http_code    = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    curl_close($curl);

    // =======================
    // DEBUGGING CURL
    // =======================
    if ($response === false) {
        echo '
            <div class="alert alert-danger">
                <small>
                    <b>Gagal menghubungi API</b><br>
                    Error: '.$curl_error.'
                </small>
            </div>
        ';
        exit;
    }

    if ($http_code !== 200) {
        echo '
            <div class="alert alert-danger">
                <small>
                    <b>HTTP Error</b><br>
                    Status Code: '.$http_code.'
                </small>
            </div>
        ';
        exit;
    }

    // =======================
    // VALIDASI JSON
    // =======================
    $arry = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo '
            <div class="alert alert-danger">
                <small>Response API bukan JSON valid.</small>
            </div>
        ';
        exit;
    }

    // =======================
    // VALIDASI RESPONSE API
    // =======================
    if (!isset($arry['ApiVersion'])) {
        echo '
            <div class="alert alert-danger">
                <small>Format response API tidak dikenali.</small>
            </div>
        ';
        exit;
    }

    echo '<pre style="padding:15px;border-radius:6px;font-size:13px;overflow:auto;">';
    echo json_encode($arry, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    echo '</pre>';

    
