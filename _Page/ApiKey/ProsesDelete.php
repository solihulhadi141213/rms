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
// VALIDASI PARAMETER
// =========================================
if (empty($_POST['id_api_account'])) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'ID API Key tidak boleh kosong'
    ]);
    exit;
}

// =========================================
// SANITASI INPUT
// =========================================
$id_api_account = intval(validateAndSanitizeInput($_POST['id_api_account']));

// =========================================
// CEK DATA ADA ATAU TIDAK
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
// PROSES DELETE
// =========================================
$stmt = $Conn->prepare("DELETE FROM api_account WHERE id_api_account = ?");
$stmt->bind_param("i", $id_api_account);

// =========================================
// EKSEKUSI & RESPONSE
// =========================================
if ($stmt->execute()) {

    echo json_encode([
        'status'  => 'success',
        'message' => 'API Key berhasil dihapus'
    ]);

} else {

    echo json_encode([
        'status'  => 'error',
        'message' => 'Gagal menghapus API Key'
    ]);
}

$stmt->close();
$Conn->close();
