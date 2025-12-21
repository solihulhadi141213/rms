<?php
// Response JSON
header('Content-Type: application/json');

// Koneksi & session
include "../../_Config/Connection.php";
include "../../_Config/GlobalFunction.php";
include "../../_Config/Session.php";

// Zona waktu
date_default_timezone_set("Asia/Jakarta");

// Validasi session
if (empty($SessionIdAccess)) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Sesi akses telah berakhir, silakan login ulang.'
    ]);
    exit;
}

// Daftar modalitas valid (ENUM guard)
$modalitas_valid = ['XR','CT','US','MR','NM','PT','DX','CR'];

// Ambil & sanitasi input
$id_master_pemeriksaan   = validateAndSanitizeInput($_POST['id_master_pemeriksaan'] ?? '');
$nama_pemeriksaan        = validateAndSanitizeInput($_POST['nama_pemeriksaan'] ?? '');
$modalitas               = validateAndSanitizeInput($_POST['modalitas'] ?? '');

$pemeriksaan_code        = validateAndSanitizeInput($_POST['pemeriksaan_code'] ?? '');
$pemeriksaan_description = validateAndSanitizeInput($_POST['pemeriksaan_description'] ?? '');
$pemeriksaan_sys         = validateAndSanitizeInput($_POST['pemeriksaan_sys'] ?? '');

$bodysite_code           = validateAndSanitizeInput($_POST['bodysite_code'] ?? '');
$bodysite_description    = validateAndSanitizeInput($_POST['bodysite_description'] ?? '');
$bodysite_sys            = validateAndSanitizeInput($_POST['bodysite_sys'] ?? '');

// Validasi wajib
if (empty($id_master_pemeriksaan) || empty($nama_pemeriksaan) || empty($modalitas)) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Nama pemeriksaan dan modalitas wajib diisi.'
    ]);
    exit;
}

// Validasi enum modalitas
if (!in_array($modalitas, $modalitas_valid)) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Modalitas tidak valid.'
    ]);
    exit;
}

try {
    // Prepared Statement UPDATE
    $Qry = $Conn->prepare("
        UPDATE master_pemeriksaan SET
            nama_pemeriksaan        = ?,
            modalitas               = ?,
            pemeriksaan_code        = ?,
            pemeriksaan_description = ?,
            pemeriksaan_sys         = ?,
            bodysite_code           = ?,
            bodysite_description    = ?,
            bodysite_sys            = ?
        WHERE id_master_pemeriksaan = ?
    ");

    $Qry->bind_param(
        "ssssssssi",
        $nama_pemeriksaan,
        $modalitas,
        $pemeriksaan_code,
        $pemeriksaan_description,
        $pemeriksaan_sys,
        $bodysite_code,
        $bodysite_description,
        $bodysite_sys,
        $id_master_pemeriksaan
    );

    if (!$Qry->execute()) {
        throw new Exception($Qry->error);
    }

    $Qry->close();

    echo json_encode([
        'status'  => 'success',
        'message' => 'Data pemeriksaan berhasil diperbarui.'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Gagal memperbarui data. ' . $e->getMessage()
    ]);
}
