<?php
// =========================================
// SET HEADER RESPONSE JSON
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
if (empty($_POST['id_api_account']) || empty($_POST['password'])) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'ID API Key dan Password wajib diisi'
    ]);
    exit;
}

// =========================================
// SANITASI INPUT
// =========================================
$id_api_account = intval(validateAndSanitizeInput($_POST['id_api_account']));
$password_plain = trim($_POST['password']);

// =========================================
// VALIDASI PASSWORD
// =========================================
if (strlen($password_plain) < 6) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Password minimal 6 karakter'
    ]);
    exit;
}

// =========================================
// CEK DATA API KEY ADA ATAU TIDAK
// =========================================
$cek = $Conn->prepare("SELECT id_api_account FROM api_account WHERE id_api_account = ?");
$cek->bind_param("i", $id_api_account);
$cek->execute();
$cek->store_result();

if ($cek->num_rows == 0) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Data API Key tidak ditemukan'
    ]);
    exit;
}
$cek->close();

// =========================================
// HASH PASSWORD BARU
// =========================================
$password_hash = password_hash($password_plain, PASSWORD_DEFAULT);

// =========================================
// UPDATE PASSWORD
// =========================================
$stmt = $Conn->prepare("
    UPDATE api_account 
    SET password = ? 
    WHERE id_api_account = ?
");
$stmt->bind_param("si", $password_hash, $id_api_account);

// =========================================
// EKSEKUSI & RESPONSE
// =========================================
if ($stmt->execute()) {

    echo json_encode([
        'status'  => 'success',
        'message' => 'Password API Key berhasil diperbarui'
    ]);

} else {

    echo json_encode([
        'status'  => 'error',
        'message' => 'Gagal memperbarui password API Key'
    ]);
}

$stmt->close();
$Conn->close();
