<?php
    /*
    ======================================================
    FILE : GoogleCallback.php
    FUNGSI : Menerima response login dari Google OAuth
    ======================================================
    */

    // Mulai session
    session_start();

    // Koneksi dan function aplikasi
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/SettingGeneral.php";

    // Zona waktu
    date_default_timezone_set("Asia/Jakarta");


    /*
    ======================================================
    CEK AUTHORIZATION CODE
    ======================================================
    */

    if(empty($_GET['code'])){
        die("Authorization code tidak ditemukan");
    }

    $code = $_GET['code'];


    /*
    ======================================================
    AMBIL GOOGLE CREDENTIAL YANG AKTIF
    ======================================================
    */

    $status = 1;

    $stmt = $Conn->prepare("
        SELECT * 
        FROM google_credential 
        WHERE status=?
    ");

    $stmt->bind_param("i",$status);
    $stmt->execute();

    $cred = $stmt->get_result()->fetch_assoc();

    if(!$cred){
        die("Credential Google tidak ditemukan");
    }

    $client_id     = $cred['client_id'];
    $client_secret = $cred['client_secret'];

    $redirect_uri  = $app_base_url . "/_Page/Login/GoogleCallback.php";


    /*
    ======================================================
    TUKAR AUTHORIZATION CODE MENJADI ACCESS TOKEN
    ======================================================
    */

    $token_url = "https://oauth2.googleapis.com/token";

    $post = [
        "code"          => $code,
        "client_id"     => $client_id,
        "client_secret" => $client_secret,
        "redirect_uri"  => $redirect_uri,
        "grant_type"    => "authorization_code"
    ];

    $options = [
        "http" => [
            "header"  => "Content-Type: application/x-www-form-urlencoded",
            "method"  => "POST",
            "content" => http_build_query($post)
        ]
    ];

    $context  = stream_context_create($options);
    $response = file_get_contents($token_url,false,$context);

    $token = json_decode($response,true);

    if(empty($token['access_token'])){
        die("Gagal mengambil access token dari Google");
    }

    $access_token = $token['access_token'];


    /*
    ======================================================
    AMBIL DATA USER GOOGLE
    ======================================================
    */

    $user_info = file_get_contents(
        "https://www.googleapis.com/oauth2/v2/userinfo?access_token=".$access_token
    );

    $user = json_decode($user_info,true);

    if(empty($user['email'])){
        die("Gagal mengambil data user Google");
    }

    $email = $user['email'];
    $nama  = $user['name'];


    /*
    ======================================================
    CEK USER DI DATABASE APLIKASI
    ======================================================
    */

    $stmt = $Conn->prepare("
        SELECT * 
        FROM access 
        WHERE access_email=?
    ");

    $stmt->bind_param("s",$email);
    $stmt->execute();

    $DataAkses = $stmt->get_result()->fetch_assoc();

    if(!$DataAkses){
        die("Email Google tidak terdaftar pada sistem.");
    }

    $id_access = $DataAkses['id_access'];


    /*
    ======================================================
    HAPUS TOKEN LOGIN LAMA
    ======================================================
    */

    $delete = $Conn->prepare("
        DELETE FROM access_login 
        WHERE id_access=?
    ");

    $delete->bind_param("i",$id_access);
    $delete->execute();


    /*
    ======================================================
    BUAT TOKEN LOGIN BARU
    ======================================================
    */

    $timestamp_now   = date("Y-m-d H:i:s");
    $expired_seconds = 60 * 60;

    $date_expired = date(
        "Y-m-d H:i:s",
        strtotime($timestamp_now) + $expired_seconds
    );

    $token_login = GenerateToken(36);

    $insert = $Conn->prepare("
        INSERT INTO access_login
        (
            id_access,
            token,
            datetime_creat,
            datetime_expired
        )
        VALUES (?,?,?,?)
    ");

    $insert->bind_param(
        "isss",
        $id_access,
        $token_login,
        $timestamp_now,
        $date_expired
    );

    if(!$insert->execute()){
        die("Gagal membuat sesi login");
    }


    /*
    ======================================================
    SIMPAN LOG LOGIN
    ======================================================
    */

    $kategori_log  = "Login";
    $deskripsi_log = "Login Google Berhasil";

    addLog(
        $Conn,
        $id_access,
        $timestamp_now,
        $kategori_log,
        $deskripsi_log
    );


    /*
    ======================================================
    BUAT SESSION LOGIN
    ======================================================
    */

    $_SESSION["id_access"]   = $id_access;
    $_SESSION["login_token"] = $token_login;

    $_SESSION["NotifikasiSwal"] ="Login Berhasil";


    /*
    ======================================================
    REDIRECT KE HALAMAN UTAMA
    ======================================================
    */

    header("Location: ../../index.php");
    exit;

?>