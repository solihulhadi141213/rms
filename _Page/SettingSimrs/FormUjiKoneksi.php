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
    if (empty($_POST['id_connection_simrs'])) {
        echo '
            <div class="alert alert-danger text-center">
                <small>Koneksi SIMRS Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }

    $id_connection_simrs = validateAndSanitizeInput($_POST['id_connection_simrs']);

    // =======================
    // AMBIL DATA KONEKSI
    // =======================
    $Qry = $Conn->prepare("SELECT * FROM connection_simrs WHERE id_connection_simrs = ?");
    $Qry->bind_param("i", $id_connection_simrs);

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
                <small>Data koneksi SIMRS tidak ditemukan.</small>
            </div>
        ';
        exit;
    }

    // =======================
    // VARIABEL
    // =======================
    $url_connection_simrs = rtrim($Data['url_connection_simrs'], '/');
    $client_id            = $Data['client_id'];
    $client_key           = $Data['client_key'];

    // =======================
    // REQUEST TOKEN (cURL)
    // =======================
    $payload = json_encode([
        'client_id'  => $client_id,
        'client_key' => $client_key
    ]);

    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL            => $url_connection_simrs . '/API/SIMRS/get_token.php',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'X-API-Key: ••••••'
        ],
        CURLOPT_CONNECTTIMEOUT => 5,   // waktu koneksi (detik)
        CURLOPT_TIMEOUT        => 10,  // total timeout (detik)
        CURLOPT_FAILONERROR    => false
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
                    <b>Gagal menghubungi API SIMRS</b><br>
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
    if (!isset($arry['response']['code'])) {
        echo '
            <div class="alert alert-danger">
                <small>Format response API tidak dikenali.</small>
            </div>
        ';
        exit;
    }

    if ($arry['response']['code'] !== 200) {
        echo '
            <div class="alert alert-danger">
                <small>'.$arry['response']['message'].'</small>
            </div>
        ';
        exit;
    }

    // =======================
    // DATA TOKEN
    // =======================
    $meta = $arry['metadata'];

    echo '
        <div class="row mb-2"><div class="col-4"><small>API Name</small></div><div class="col-1">:</div><div class="col-7"><small class="text-long">'.$meta['api_name'].'</small></div></div>
        <div class="row mb-2"><div class="col-4"><small>Description</small></div><div class="col-1">:</div><div class="col-7"><small class="text-long">'.$meta['api_description'].'</small></div></div>
        <div class="row mb-2"><div class="col-4"><small>Expired Duration</small></div><div class="col-1">:</div><div class="col-7"><small>'.$meta['expired_duration'].'</small></div></div>
        <div class="row mb-2"><div class="col-4"><small>Created At</small></div><div class="col-1">:</div><div class="col-7"><small>'.$meta['datetime_creat'].'</small></div></div>
        <div class="row mb-2"><div class="col-4"><small>Expired At</small></div><div class="col-1">:</div><div class="col-7"><small>'.$meta['datetime_expired'].'</small></div></div>
        <div class="row mb-2"><div class="col-4"><small>Token</small></div><div class="col-1">:</div><div class="col-7"><small class="text-long">'.$meta['token'].'</small></div></div>
    ';
