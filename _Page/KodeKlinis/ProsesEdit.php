<?php
/**
 * -------------------------------------------------------
 * PROSES EDIT KODE KLINIS
 * Tabel : master_klinis
 * -------------------------------------------------------
 */

header('Content-Type: application/json');

/* Koneksi & Session */
require_once "../../_Config/Connection.php";
require_once "../../_Config/GlobalFunction.php";
require_once "../../_Config/Session.php";

/* Response default */
$response = [
    'status'  => 'error',
    'message' => 'Terjadi kesalahan sistem'
];

/* Validasi session */
if (empty($SessionIdAccess)) {
    $response['message'] = 'Sesi login telah berakhir';
    echo json_encode($response);
    exit;
}

/* Validasi metode */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Metode tidak diizinkan';
    echo json_encode($response);
    exit;
}

/* Ambil & sanitasi input */
$id_master_klinis = intval($_POST['id_master_klinis'] ?? 0);
$nama_klinis     = trim($_POST['nama_klinis'] ?? '');
$snomed_code     = trim($_POST['snomed_code'] ?? '');
$snomed_display  = trim($_POST['snomed_display'] ?? '');
$kategori        = trim($_POST['kategori'] ?? '');
$aktif           = ($_POST['aktif'] ?? '0') == '1' ? 'Ya' : 'Tidak';

/* Validasi wajib */
if (
    $id_master_klinis <= 0 ||
    empty($nama_klinis) ||
    empty($snomed_code) ||
    empty($snomed_display) ||
    empty($kategori)
) {
    $response['message'] = 'Semua field wajib diisi';
    echo json_encode($response);
    exit;
}

/* Validasi SNOMED numeric */
if (!ctype_digit($snomed_code)) {
    $response['message'] = 'Kode SNOMED harus berupa angka';
    echo json_encode($response);
    exit;
}

try {

    /* Cek data exists */
    $cek = $Conn->prepare("
        SELECT id_master_klinis 
        FROM master_klinis 
        WHERE id_master_klinis = ?
        LIMIT 1
    ");
    $cek->bind_param("i", $id_master_klinis);
    $cek->execute();
    $cek->store_result();

    if ($cek->num_rows == 0) {
        $response['message'] = 'Data klinis tidak ditemukan';
        echo json_encode($response);
        exit;
    }

    /* Cek duplikasi SNOMED (kecuali dirinya sendiri) */
    $cekDuplikat = $Conn->prepare("
        SELECT id_master_klinis 
        FROM master_klinis 
        WHERE snomed_code = ?
        AND id_master_klinis != ?
        LIMIT 1
    ");
    $cekDuplikat->bind_param("si", $snomed_code, $id_master_klinis);
    $cekDuplikat->execute();
    $cekDuplikat->store_result();

    if ($cekDuplikat->num_rows > 0) {
        $response['message'] = 'Kode SNOMED sudah digunakan oleh data lain';
        echo json_encode($response);
        exit;
    }

    /* Update data */
    $stmt = $Conn->prepare("
        UPDATE master_klinis SET
            nama_klinis     = ?,
            snomed_code     = ?,
            snomed_display  = ?,
            kategori        = ?,
            aktif           = ?,
            datetime_update = NOW()
        WHERE id_master_klinis = ?
    ");

    $stmt->bind_param(
        "sssssi",
        $nama_klinis,
        $snomed_code,
        $snomed_display,
        $kategori,
        $aktif,
        $id_master_klinis
    );

    if ($stmt->execute()) {
        $response['status']  = 'success';
        $response['message'] = 'Data klinis berhasil diperbarui';
    } else {
        $response['message'] = 'Gagal memperbarui data';
    }

} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

/* Output JSON */
echo json_encode($response);
