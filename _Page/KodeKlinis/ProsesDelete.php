<?php
// koneksi dan session
include "../../_Config/Connection.php";
include "../../_Config/GlobalFunction.php";
include "../../_Config/Session.php";

// Zona Waktu
date_default_timezone_set("Asia/Jakarta");

// Response default
$response = [
    'status'  => 'error',
    'message' => 'Terjadi kesalahan sistem'
];

// =======================
// VALIDASI SESSION
// =======================
if (empty($SessionIdAccess)) {
    $response['message'] = 'Sesi akses telah berakhir. Silakan login ulang.';
    echo json_encode($response);
    exit;
}

// =======================
// VALIDASI INPUT
// =======================
$id_master_klinis = validateAndSanitizeInput($_POST['id_master_klinis'] ?? '');

if (empty($id_master_klinis)) {
    $response['message'] = 'ID Kode Klinis tidak valid.';
    echo json_encode($response);
    exit;
}

// =======================
// CEK DATA ADA ATAU TIDAK
// =======================
$QryCheck = $Conn->prepare("
    SELECT id_master_klinis, aktif 
    FROM master_klinis 
    WHERE id_master_klinis = ?
");
if (!$QryCheck) {
    $response['message'] = $Conn->error;
    echo json_encode($response);
    exit;
}

$QryCheck->bind_param("i", $id_master_klinis);
$QryCheck->execute();
$Result = $QryCheck->get_result();
$Data   = $Result->fetch_assoc();
$QryCheck->close();

if (!$Data) {
    $response['message'] = 'Data Master Klinis tidak ditemukan.';
    echo json_encode($response);
    exit;
}


// =======================
// PROSES DELETE
// =======================
$Conn->begin_transaction();

try {

    $QryDelete = $Conn->prepare("
        DELETE FROM master_klinis 
        WHERE id_master_klinis = ?
    ");
    if (!$QryDelete) {
        throw new Exception($Conn->error);
    }

    $QryDelete->bind_param("i", $id_master_klinis);

    if (!$QryDelete->execute()) {
        throw new Exception('Gagal menghapus data Master Klinis.');
    }

    $QryDelete->close();
    $Conn->commit();

    $response['status']  = 'success';
    $response['message'] = 'Master Klinis berhasil dihapus.';

} catch (Exception $e) {
    $Conn->rollback();
    $response['message'] = $e->getMessage();
}

// =======================
// OUTPUT JSON
// =======================
echo json_encode($response);
