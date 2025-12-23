<?php
    /**
     * ======================================================
     * API LOGIN & GENERATE TOKEN
     * ======================================================
     * - Timezone UTC
     * - Secure Input Handling
     * - Proper HTTP Response Code
     * - Auto Cleanup Expired Token
     * ======================================================
     */

    // ======================================================
    // SET TIMEZONE
    // ======================================================
    date_default_timezone_set('UTC');

    // ======================================================
    // HEADER RESPONSE (CORS + JSON)
    // ======================================================
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

    // ======================================================
    // KONFIGURASI & KONEKSI
    // ======================================================
    include "../_Config/Connection.php";
    include "../_Config/GlobalFunction.php";

    // ======================================================
    // AMBIL DATA REQUEST
    // ======================================================
    $raw_data = file_get_contents("php://input");
    $data     = [];
    parse_str($raw_data, $data);

    // ======================================================
    // SANITASI INPUT (SECURITY HARDENING)
    // ======================================================
    $username = isset($data['username']) 
        ? trim(filter_var($data['username'], FILTER_SANITIZE_SPECIAL_CHARS)) 
        : '';

    $password = isset($data['password']) 
        ? trim($data['password']) 
        : '';

    // ======================================================
    // VALIDASI INPUT
    // ======================================================
    if ($username === '' || $password === '') {
        http_response_code(400); // Bad Request
        echo json_encode([
            "status"  => "error",
            "message" => "Username dan password wajib diisi"
        ]);
        exit;
    }

    // ======================================================
    // HAPUS TOKEN YANG SUDAH EXPIRED (AUTO CLEANUP)
    // ======================================================
    $deleteExpiredStmt = $Conn->prepare("
        DELETE FROM api_token 
        WHERE expired_at <= UTC_TIMESTAMP()
    ");
    $deleteExpiredStmt->execute();

    // ======================================================
    // AMBIL DATA API ACCOUNT
    // ======================================================
    $stmt = $Conn->prepare("
        SELECT id_api_account, api_name, base_url_api, password, duration_expired 
        FROM api_account 
        WHERE username = ?
        LIMIT 1
    ");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $data_api = $stmt->get_result()->fetch_assoc();

    // ======================================================
    // VALIDASI USERNAME & PASSWORD
    // ======================================================
    if (!$data_api || !password_verify($password, $data_api['password'])) {
        http_response_code(401); // Unauthorized
        echo json_encode([
            "status"  => "error",
            "message" => "Username atau password tidak valid"
        ]);
        exit;
    }

    // ======================================================
    // DATA ACCOUNT VALID
    // ======================================================
    $id_api_account   = (int) $data_api['id_api_account'];
    $duration_expired = (int) $data_api['duration_expired']; // millisecond

    // ======================================================
    // GENERATE TOKEN
    // ======================================================
    $token      = GenerateToken(36);
    $token_hash = password_hash($token, PASSWORD_DEFAULT);

    // ======================================================
    // HITUNG EXPIRED TOKEN (UTC + MILLISECOND)
    // ======================================================
    $now     = new DateTime('now', new DateTimeZone('UTC'));
    $seconds = $duration_expired / 1000;

    $expired_at = clone $now;
    $expired_at->add(new DateInterval('PT' . $seconds . 'S'));

    $expired_at_iso = $expired_at->format('Y-m-d\TH:i:s.') . sprintf('%03d', $expired_at->format('v')) . 'Z';

    // ======================================================
    // CREATED_AT (ISO 8601 + MILLISECOND)
    // ======================================================
    $microtime    = microtime(true);
    $milliseconds = sprintf('%03d', ($microtime - floor($microtime)) * 1000);
    $created_at   = gmdate('Y-m-d\TH:i:s', $microtime) . '.' . $milliseconds . 'Z';

    // ======================================================
    // FORMAT UNTUK DATABASE (DATETIME)
    // ======================================================
    $db_created_at = gmdate('Y-m-d H:i:s');
    $db_expired_at = $expired_at->format('Y-m-d H:i:s');

    // ======================================================
    // SIMPAN TOKEN KE DATABASE
    // ======================================================
    $insertTokenStmt = $Conn->prepare("
        INSERT INTO api_token 
        (id_api_account, token, created_at, expired_at) 
        VALUES (?, ?, ?, ?)
    ");
    $insertTokenStmt->bind_param(
        "isss",
        $id_api_account,
        $token_hash,
        $db_created_at,
        $db_expired_at
    );

    if (!$insertTokenStmt->execute()) {
        http_response_code(500); // Internal Server Error
        echo json_encode([
            "status"  => "error",
            "message" => "Gagal menyimpan token ke database"
        ]);
        exit;
    }

    // ======================================================
    // RESPONSE SUCCESS
    // ======================================================
    http_response_code(200);
    echo json_encode([
        "status"           => "success",
        "message"          => "Token berhasil dibuat",
        "token"            => $token,
        "created_at"       => $created_at,
        "token_expired_at" => $expired_at_iso
    ]);
    exit;
