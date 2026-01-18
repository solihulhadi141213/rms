<?php
// koneksi dan session
include "../../_Config/Connection.php";
include "../../_Config/GlobalFunction.php";
include "../../_Config/Session.php";

// Zona Waktu
date_default_timezone_set("Asia/Jakarta");

// Response default
$response = [
    'status'       => 'error',
    'id_radiologi' => '',
    'message'      => 'Terjadi kesalahan sistem'
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
$id_radiologi_invoice = validateAndSanitizeInput($_POST['id_radiologi_invoice'] ?? '');

if (empty($id_radiologi_invoice)) {
    $response['message'] = 'ID Invoice tidak valid.';
    echo json_encode($response);
    exit;
}

// =======================
// CEK DATA ADA ATAU TIDAK
// =======================
$QryCheck = $Conn->prepare("SELECT id_radiologi FROM radiologi_invoice WHERE id_radiologi_invoice = ?");
if (!$QryCheck) {
    $response['message'] = $Conn->error;
    echo json_encode($response);
    exit;
}

$QryCheck->bind_param("i", $id_radiologi_invoice);
$QryCheck->execute();
$Result = $QryCheck->get_result();
$Data   = $Result->fetch_assoc();
$QryCheck->close();

if (!$Data) {
    $response['message'] = 'Data Invoice tidak ditemukan.';
    echo json_encode($response);
    exit;
}
$id_radiologi = $Data['id_radiologi'];


// =======================
// PROSES DELETE
// =======================
$Conn->begin_transaction();

try {

    $QryDelete = $Conn->prepare("DELETE FROM radiologi_invoice WHERE id_radiologi_invoice = ?");
    if (!$QryDelete) {
        throw new Exception($Conn->error);
    }

    $QryDelete->bind_param("i", $id_radiologi_invoice);

    if (!$QryDelete->execute()) {
        throw new Exception('Gagal menghapus data Invoice.');
    }

    $QryDelete->close();
    $Conn->commit();

    $response['status']  = 'success';
    $response['id_radiologi']  = $id_radiologi;
    $response['message'] = 'Hapus Invoice berhasil.';

} catch (Exception $e) {
    $Conn->rollback();
    $response['message'] = $e->getMessage();
}

// =======================
// OUTPUT JSON
// =======================
echo json_encode($response);
