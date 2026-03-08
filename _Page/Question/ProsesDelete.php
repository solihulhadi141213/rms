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
$id_question = validateAndSanitizeInput($_POST['id_question'] ?? '');

if (empty($id_question)) {
    $response['message'] = 'ID Daftar Pertanyaan tidak valid.';
    echo json_encode($response);
    exit;
}

// =======================
// CEK DATA ADA ATAU TIDAK
// =======================
$QryCheck = $Conn->prepare("SELECT id_question FROM question WHERE id_question = ?");
if (!$QryCheck) {
    $response['message'] = $Conn->error;
    echo json_encode($response);
    exit;
}

$QryCheck->bind_param("i", $id_question);
$QryCheck->execute();
$Result = $QryCheck->get_result();
$Data   = $Result->fetch_assoc();
$QryCheck->close();

if (!$Data) {
    $response['message'] = 'Data Daftar Pertanyaan tidak ditemukan.';
    echo json_encode($response);
    exit;
}


// =======================
// PROSES DELETE
// =======================
$Conn->begin_transaction();

try {

    $QryDelete = $Conn->prepare("DELETE FROM question WHERE id_question = ?");
    if (!$QryDelete) {
        throw new Exception($Conn->error);
    }

    $QryDelete->bind_param("i", $id_question);

    if (!$QryDelete->execute()) {
        throw new Exception('Gagal menghapus data Daftar Pertanyaan.');
    }

    $QryDelete->close();
    $Conn->commit();

    $response['status']  = 'success';
    $response['message'] = 'Daftar Pertanyaan berhasil dihapus.';

} catch (Exception $e) {
    $Conn->rollback();
    $response['message'] = $e->getMessage();
}

// =======================
// OUTPUT JSON
// =======================
echo json_encode($response);
