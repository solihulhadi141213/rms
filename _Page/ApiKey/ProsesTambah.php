<?php
// =========================================
// SET HEADER RESPONSE JSON
// =========================================
header('Content-Type: application/json');

// =========================================
// INCLUDE KONEKSI DATABASE
// =========================================
include "../../_Config/Connection.php";

// =========================================
// VALIDASI REQUEST METHOD
// =========================================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Invalid request method'
    ]);
    exit;
}

// =========================================
// AMBIL & BERSIHKAN INPUT
// =========================================
$api_name         = trim($_POST['api_name'] ?? '');
$base_url_api     = trim($_POST['base_url_api'] ?? '');
$username         = trim($_POST['username'] ?? '');
$password_plain   = $_POST['password'] ?? '';
$duration_expired = intval($_POST['duration_expired'] ?? 0);
$satuan_duration  = $_POST['satuan_duration'] ?? '';
$created_at       = date('Y-m-d H:i:s');

// =========================================
// VALIDASI WAJIB ISI
// =========================================
if (
    empty($api_name) ||
    empty($base_url_api) ||
    empty($username) ||
    empty($password_plain) ||
    $duration_expired <= 0
) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Semua field wajib diisi'
    ]);
    exit;
}

// =========================================
// VALIDASI DUPLIKASI NAMA API
// =========================================
$cek = $Conn->prepare("SELECT id_api_account FROM api_account WHERE api_name = ?");
$cek->bind_param("s", $api_name);
$cek->execute();
$cek->store_result();

if ($cek->num_rows > 0) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Nama API sudah terdaftar'
    ]);
    exit;
}
$cek->close();

// =========================================
// KONVERSI DURATION KE MILISECOND
// =========================================
switch ($satuan_duration) {
    case 'menit':
        $duration_ms = $duration_expired * 60 * 1000;
        break;

    case 'jam':
        $duration_ms = $duration_expired * 60 * 60 * 1000;
        break;

    case 'hari':
        $duration_ms = $duration_expired * 24 * 60 * 60 * 1000;
        break;

    default:
        echo json_encode([
            'status'  => 'error',
            'message' => 'Satuan durasi tidak valid'
        ]);
        exit;
}

// =========================================
// HASH PASSWORD
// =========================================
$password_hash = password_hash($password_plain, PASSWORD_DEFAULT);

// =========================================
// SIMPAN DATA KE DATABASE
// =========================================
$stmt = $Conn->prepare("
    INSERT INTO api_account
    (api_name, base_url_api, username, password, duration_expired, created_at)
    VALUES (?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "ssssis",
    $api_name,
    $base_url_api,
    $username,
    $password_hash,
    $duration_ms,
    $created_at
);

if ($stmt->execute()) {

    echo json_encode([
        'status'  => 'success',
        'message' => 'API Key berhasil ditambahkan'
    ]);

} else {

    echo json_encode([
        'status'  => 'error',
        'message' => 'Gagal menyimpan data'
    ]);
}

$stmt->close();
$Conn->close();
