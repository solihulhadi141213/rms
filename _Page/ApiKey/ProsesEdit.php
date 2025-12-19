<?php
// =========================================
// SET HEADER RESPONSE
// =========================================
header('Content-Type: application/json');

// =========================================
// INCLUDE KONFIGURASI
// =========================================
include "../../_Config/Connection.php";
include "../../_Config/GlobalFunction.php";
include "../../_Config/Session.php";

// =========================================
// ZONA WAKTU
// =========================================
date_default_timezone_set("Asia/Jakarta");

// =========================================
// VALIDASI SESSION
// =========================================
if (empty($SessionIdAccess)) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Sesi akses sudah berakhir, silahkan login ulang'
    ]);
    exit;
}

// =========================================
// VALIDASI PARAMETER WAJIB
// =========================================
if (
    empty($_POST['id_api_account']) ||
    empty($_POST['api_name']) ||
    empty($_POST['base_url_api']) ||
    empty($_POST['username']) ||
    empty($_POST['duration_expired']) ||
    empty($_POST['satuan_duration'])
) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Data yang dikirim tidak lengkap'
    ]);
    exit;
}

// =========================================
// SANITASI INPUT
// =========================================
$id_api_account    = intval($_POST['id_api_account']);
$api_name          = validateAndSanitizeInput($_POST['api_name']);
$base_url_api      = validateAndSanitizeInput($_POST['base_url_api']);
$username          = validateAndSanitizeInput($_POST['username']);
$duration_input    = intval($_POST['duration_expired']);
$satuan_duration   = $_POST['satuan_duration'];

// =========================================
// VALIDASI NILAI DURATION
// =========================================
if ($duration_input <= 0) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Durasi expired tidak valid'
    ]);
    exit;
}

// =========================================
// VALIDASI DUPLIKASI NAMA API
// (KECUALI DATA YANG SEDANG DIEDIT)
// =========================================
$cek = $Conn->prepare("
    SELECT id_api_account 
    FROM api_account 
    WHERE api_name = ? 
    AND id_api_account != ?
");
$cek->bind_param("si", $api_name, $id_api_account);
$cek->execute();
$cek->store_result();

if ($cek->num_rows > 0) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Nama API sudah digunakan'
    ]);
    exit;
}
$cek->close();

// =========================================
// KONVERSI DURATION KE MILISECOND
// =========================================
switch ($satuan_duration) {
    case 'menit':
        $duration_ms = $duration_input * 60 * 1000;
        break;

    case 'jam':
        $duration_ms = $duration_input * 60 * 60 * 1000;
        break;

    case 'hari':
        $duration_ms = $duration_input * 24 * 60 * 60 * 1000;
        break;

    default:
        echo json_encode([
            'status'  => 'error',
            'message' => 'Satuan durasi tidak valid'
        ]);
        exit;
}

// =========================================
// UPDATE DATA
// =========================================
$stmt = $Conn->prepare("
    UPDATE api_account SET
        api_name = ?,
        base_url_api = ?,
        username = ?,
        duration_expired = ?
    WHERE id_api_account = ?
");

$stmt->bind_param(
    "sssii",
    $api_name,
    $base_url_api,
    $username,
    $duration_ms,
    $id_api_account
);

// =========================================
// EKSEKUSI & RESPONSE
// =========================================
if ($stmt->execute()) {

    echo json_encode([
        'status'  => 'success',
        'message' => 'Data API berhasil diperbarui'
    ]);

} else {

    echo json_encode([
        'status'  => 'error',
        'message' => 'Gagal memperbarui data'
    ]);
}

$stmt->close();
$Conn->close();
