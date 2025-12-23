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
$id_radiologi = validateAndSanitizeInput($_POST['id_radiologi'] ?? '');

if (empty($id_radiologi)) {
    $response['message'] = 'ID Permintaan Pemeriksaan tidak valid.';
    echo json_encode($response);
    exit;
}

// =======================
// CEK DATA ADA ATAU TIDAK
// =======================
$QryCheck = $Conn->prepare("SELECT id_radiologi, status_pemeriksaan FROM radiologi WHERE id_radiologi = ?");
if (!$QryCheck) {
    $response['message'] = $Conn->error;
    echo json_encode($response);
    exit;
}

$QryCheck->bind_param("i", $id_radiologi);
$QryCheck->execute();
$Result = $QryCheck->get_result();
$Data   = $Result->fetch_assoc();
$QryCheck->close();

if (!$Data) {
    $response['message'] = 'Data Permintaan tidak ditemukan.';
    echo json_encode($response);
    exit;
}

// =======================
// OPTIONAL (AMAN): CEGAH HAPUS JIKA AKTIF
// =======================
if ($Data['status_pemeriksaan'] == 'Selesai') {
    $response['message'] = 'Data Pemeriksaan Sudah Selesai! Anda tidak bisa menghapus data ini. Silahkan hubungi unit SIRS untuk memaksa sistem menghapus data tersebut.';
    echo json_encode($response);
    exit;
}

// =======================
// PROSES DELETE
// =======================
$Conn->begin_transaction();

try {

    $QryDelete = $Conn->prepare("DELETE FROM radiologi WHERE id_radiologi = ?");
    if (!$QryDelete) {
        throw new Exception($Conn->error);
    }

    $QryDelete->bind_param("i", $id_radiologi);

    if (!$QryDelete->execute()) {
        throw new Exception('Gagal menghapus data permintaan pemeriksaan.');
    }

    $QryDelete->close();
    $Conn->commit();

    $response['status']  = 'success';
    $response['message'] = 'Permintaan pemeriksaan berhasil dihapus.';

} catch (Exception $e) {
    $Conn->rollback();
    $response['message'] = $e->getMessage();
}

// =======================
// OUTPUT JSON
// =======================
echo json_encode($response);
