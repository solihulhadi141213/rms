<?php
    // koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    // Zona Waktu
    date_default_timezone_set("Asia/Jakarta");

    // Validasi Session Akses
    if (empty($SessionIdAccess)) {
        echo '
            <div class="alert alert-danger text-center">
                <small>Sesi Akses Sudah Berakhir! Silahkan Login Ulang.</small>
            </div>
        ';
        exit;
    }

    // VALIDASI INPUT
    if (empty($_POST['id_connection_simrs_old'])) {
        echo '
            <div class="alert alert-danger text-center">
                <small>Koneksi SIMRS Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }

    $id_connection_simrs_old = validateAndSanitizeInput($_POST['id_connection_simrs_old']);

    // AMBIL DATA KONEKSI
    $Qry = $Conn->prepare("SELECT * FROM connection_simrs_old WHERE id_connection_simrs_old = ?");
    $Qry->bind_param("i", $id_connection_simrs_old);

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

    // VARIABEL
    $base_url = rtrim($Data['base_url'], '/');
    $username = $Data['username'];
    $password = $Data['password'];

    // AUTH BASIC (HARUS SAMA DENGAN SERVER)
    $basicAuth = base64_encode($username . ':' . $password);

    // CURL REQUEST
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => $base_url . '/Auth/generate_token.php',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Basic ' . $basicAuth
        ]
    ]);

    $response   = curl_exec($curl);
    $curl_error = curl_error($curl);
    $http_code  = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    curl_close($curl);

    // ERROR CURL
    if ($response === false) {
        echo '
            <div class="alert alert-danger">
                <small><b>Gagal menghubungi API SIMRS</b><br>Error: '.$curl_error.'</small>
            </div>
        ';
        exit;
    }

    // HTTP ERROR
    if ($http_code !== 200) {
        echo '
            <div class="alert alert-danger">
                <small><b>HTTP Error</b><br>Status Code: '.$http_code.'<br>Response: '.$response.'</small>
            </div>
        ';
        exit;
    }

    // PARSE JSON
    $arry = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo '
            <div class="alert alert-danger">
                <small>Response API bukan JSON valid</small>
            </div>
        ';
        exit;
    }

    // VALIDASI STATUS
    if (!isset($arry['status'])) {
        echo '
            <div class="alert alert-danger">
                <small>Format response API tidak dikenali</small>
            </div>
        ';
        exit;
    }

    if ($arry['status'] !== 'success') {
        echo '
            <div class="alert alert-danger">
                <small>'.$arry['message'].'</small>
            </div>
        ';
        exit;
    }

    // OUTPUT DATA
    $token      = $arry['token'];
    $creat_at   = $arry['creat_at'];
    $expired_at = $arry['expired_at'];
    
    // Menyimpan Token dan memperbaharui tanggal
    $UpdateKoneksi = mysqli_query($Conn,"UPDATE connection_simrs_old SET 
        token='$token',
        creat_at='$creat_at',
        expired_at='$expired_at'
    WHERE id_connection_simrs_old='$id_connection_simrs_old'") or die(mysqli_error($Conn)); 
    if(!$UpdateKoneksi){
         echo '
            <div class="alert alert-danger">
                <small>Terjadi Kesalahan Pada Saat Melakukan Update Data</small>
            </div>
        ';
        exit;
    }else{
        echo '
            <div class="row mb-2">
                <div class="col-4"><small>Username</small></div>
                <div class="col-1">:</div>
                <div class="col-7"><small class="text-long">'.htmlspecialchars($arry['username']).'</small></div>
            </div>

            <div class="row mb-2">
                <div class="col-4"><small>ID Akses</small></div>
                <div class="col-1">:</div>
                <div class="col-7"><small class="text-long">'.$arry['id_akses'].'</small></div>
            </div>

            <div class="row mb-2">
                <div class="col-4"><small>Token</small></div>
                <div class="col-1">:</div>
                <div class="col-7"><small class="text-long">'.$arry['token'].'</small></div>
            </div>

            <div class="row mb-2">
                <div class="col-4"><small>Created At</small></div>
                <div class="col-1">:</div>
                <div class="col-7"><small class="text-long">'.$arry['creat_at'].'</small></div>
            </div>

            <div class="row mb-2">
                <div class="col-4"><small>Expired At</small></div>
                <div class="col-1">:</div>
                <div class="col-7"><small class="text-long">'.$arry['expired_at'].'</small></div>
            </div>
        ';
    }
?>