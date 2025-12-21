<?php
// ======================================================
// PROSES TAMBAH MASTER KODE PEMERIKSAAN
// ======================================================

header('Content-Type: application/json');
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");

include "../../_Config/Connection.php";
include "../../_Config/GlobalFunction.php";
include "../../_Config/Session.php";

// ======================================================
// VALIDASI SESSION
// ======================================================
if (empty($SessionIdAccess)) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Session login tidak valid'
    ]);
    exit;
}

// ======================================================
// AMBIL DATA POST (WAJIB)
// ======================================================
$nama_pemeriksaan = trim($_POST['nama_pemeriksaan'] ?? '');
$modalitas        = trim($_POST['modalitas'] ?? '');

// ======================================================
// AMBIL DATA POST (OPSIONAL)
// Jika kosong → NULL
// ======================================================
$pemeriksaan_code        = !empty($_POST['pemeriksaan_code'])        ? trim($_POST['pemeriksaan_code'])        : NULL;
$pemeriksaan_description = !empty($_POST['pemeriksaan_description']) ? trim($_POST['pemeriksaan_description']) : NULL;
$pemeriksaan_sys         = !empty($_POST['pemeriksaan_sys'])         ? trim($_POST['pemeriksaan_sys'])         : NULL;
$bodysite_code           = !empty($_POST['bodysite_code'])           ? trim($_POST['bodysite_code'])           : NULL;
$bodysiteite_description = !empty($_POST['bodysite_description'])    ? trim($_POST['bodysite_description'])    : NULL;
$bodysite_sys            = !empty($_POST['bodysite_sys'])            ? trim($_POST['bodysite_sys'])            : NULL;

// ======================================================
// VALIDASI FIELD WAJIB
// ======================================================
if (empty($nama_pemeriksaan) || empty($modalitas)) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Nama pemeriksaan dan modalitas wajib diisi'
    ]);
    exit;
}

// ======================================================
// VALIDASI MODALITAS (ENUM)
// ======================================================
$enum_modalitas = ['XR','CT','US','MR','NM','PT','DX','CR'];
if (!in_array($modalitas, $enum_modalitas)) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Modalitas tidak valid'
    ]);
    exit;
}

// ======================================================
// CEK DUPLIKASI (Nama + Modalitas)
// ======================================================
$cek = $Conn->prepare("
    SELECT id_master_pemeriksaan 
    FROM master_pemeriksaan
    WHERE nama_pemeriksaan = ?
      AND modalitas = ?
");
$cek->bind_param("ss", $nama_pemeriksaan, $modalitas);
$cek->execute();
$cek->store_result();

if ($cek->num_rows > 0) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Nama pemeriksaan dengan modalitas tersebut sudah ada'
    ]);
    exit;
}
$cek->close();

// ======================================================
// INSERT DATA
// ======================================================
$query = $Conn->prepare("
    INSERT INTO master_pemeriksaan (
        nama_pemeriksaan,
        modalitas,
        pemeriksaan_code,
        pemeriksaan_description,
        pemeriksaan_sys,
        bodysite_code,
        bodysite_description,
        bodysite_sys
    ) VALUES (?,?,?,?,?,?,?,?)
");

$query->bind_param(
    "ssssssss",
    $nama_pemeriksaan,
    $modalitas,
    $pemeriksaan_code,
    $pemeriksaan_description,
    $pemeriksaan_sys,
    $bodysite_code,
    $bodysiteite_description,
    $bodysite_sys
);

// ======================================================
// EKSEKUSI
// ======================================================
if ($query->execute()) {
    echo json_encode([
        'status'  => 'success',
        'message' => 'Data master pemeriksaan berhasil disimpan'
    ]);
} else {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Gagal menyimpan data'
    ]);
}

$query->close();
$Conn->close();
