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
    if (empty($_POST['id_connection_pacs'])) {
        echo '
            <div class="alert alert-danger text-center">
                <small>Koneksi PACS Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }

    $id_connection_pacs = validateAndSanitizeInput($_POST['id_connection_pacs']);

    // =======================
    // AMBIL DATA KONEKSI
    // =======================
    $Qry = $Conn->prepare("SELECT * FROM connection_pacs WHERE id_connection_pacs = ?");
    $Qry->bind_param("i", $id_connection_pacs);

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
                <small>Data koneksi PACS tidak ditemukan.</small>
            </div>
        ';
        exit;
    }

    // =======================
    // VARIABEL
    // =======================
    $url_connection_pacs      = rtrim($Data['url_connection_pacs'], '/');
    $username_connection_pacs = $Data['username_connection_pacs'];
    $password_connection_pacs = $Data['password_connection_pacs'];

    // =======================
    // REQUEST TOKEN (cURL)
    // =======================
    $payload = json_encode([
        'username' => $username_connection_pacs,
        'password' => $password_connection_pacs
    ]);

    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL            => $url_connection_pacs . '/api/auth/login',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_POSTFIELDS     => 'username='. $username_connection_pacs.'&password='. $password_connection_pacs.'',
        CURLOPT_CONNECTTIMEOUT => 5,                                                      // waktu koneksi (detik)
        CURLOPT_TIMEOUT        => 10,                                                     // total timeout (detik)
        CURLOPT_FAILONERROR    => false,
        CURLOPT_SSL_VERIFYPEER => false, // aktifkan true jika SSL valid
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
                    <b>Gagal menghubungi API PACS</b><br>
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
    if (!isset($arry['message'])) {
        echo '
            <div class="alert alert-danger">
                <small>Format response API tidak dikenali.</small>
            </div>
        ';
        exit;
    }

    if ($arry['message'] !== 'Login successful') {
        echo '
            <div class="alert alert-danger">
                <small>'.$arry['message'].'</small>
            </div>
        ';
        exit;
    }

    // =======================
    // DATA TOKEN
    // =======================
    $user             = $arry['user'];
    $institution_info = $arry['user']['institution_info'];
    $token            = $arry['token'];
    $token_expired_at = $arry['token_expired_at'];

    // Tampilkan
    echo '
        <div class="row mb-2">
            <div class="col-12"><small><b>User Info</b></small></div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <small>User ID</small>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text-long">'.$user['id'].'</small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <small>Username</small>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text-long">'.$user['username'].'</small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <small>Email</small>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text-long">'.$user['email'].'</small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <small>Nama</small>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text-long">'.$user['name'].'</small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <small>Role Name</small>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text-long">'.$user['role_name'].'</small>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-4">
                <small>Doctor Info</small>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text-long">'.$user['doctor_info'].'</small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-12"><small><b>Institution Info</b></small></div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <small>ID Institution</small>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text-long">'.$institution_info['id'].'</small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <small>Institution Name</small>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text-long">'.$institution_info['name'].'</small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <small>Institution Code</small>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text-long">'.$institution_info['code'].'</small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <small>Institution Address</small>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text-long">'.$institution_info['address'].'</small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <small>Institution Phone</small>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text-long">'.$institution_info['phone'].'</small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <small>Institution Email</small>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text-long">'.$institution_info['email'].'</small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <small>Created AT</small>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text-long">'.$institution_info['created_at'].'</small>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-4">
                <small>Update AT</small>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text-long">'.$institution_info['updated_at'].'</small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-12"><small><b>Token Info</b></small></div>
        </div>
        <div class="row mb-4">
            <div class="col-4">
                <small>Token</small>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text-long">'.$token.'</small>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-4">
                <small>Expired AT</small>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text-long">'.$token_expired_at.'</small>
            </div>
        </div>
    ';
