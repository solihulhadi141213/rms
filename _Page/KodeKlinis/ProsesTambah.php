<?php
/**
 * -------------------------------------------------------
 * PROSES TAMBAH KODE KLINIS
 * Tabel : master_klinis
 * -------------------------------------------------------
 */

/* Header JSON */
header('Content-Type: application/json');

/* Koneksi Database */
require_once "../../_Config/Connection.php";
require_once "../../_Config/GlobalFunction.php";
require_once "../../_Config/Session.php";

/* Response default */
$response = [
    'status'  => 'error',
    'message' => 'Terjadi kesalahan sistem'
];

/* Validasi metode */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Metode tidak diizinkan';
    echo json_encode($response);
    exit;
}

/* Ambil & sanitasi input */
$nama_klinis     = trim($_POST['nama_klinis'] ?? '');
$snomed_code     = trim($_POST['snomed_code'] ?? '');
$snomed_display  = trim($_POST['snomed_display'] ?? '');
$kategori        = trim($_POST['kategori'] ?? '');
$aktif           = ($_POST['aktif'] ?? '0') == '1' ? 'Ya' : 'Tidak';

/* Validasi wajib */
if (
    empty($nama_klinis) ||
    empty($snomed_code) ||
    empty($snomed_display) ||
    empty($kategori)
) {
    $response['message'] = 'Semua field wajib diisi';
    echo json_encode($response);
    exit;
}

/* Validasi SNOMED (numerik) */
if (!ctype_digit($snomed_code)) {
    $response['message'] = 'Kode SNOMED harus berupa angka';
    echo json_encode($response);
    exit;
}

try {

    /* Cek duplikasi SNOMED */
    $cek = $Conn->prepare("
        SELECT id_master_klinis 
        FROM master_klinis 
        WHERE snomed_code = ?
        LIMIT 1
    ");
    $cek->bind_param("s", $snomed_code);
    $cek->execute();
    $cek->store_result();

    if ($cek->num_rows > 0) {
        $response['message'] = 'Kode SNOMED sudah terdaftar';
        echo json_encode($response);
        exit;
    }

    /* Insert data */
    $stmt = $Conn->prepare("
        INSERT INTO master_klinis
        (
            nama_klinis,
            snomed_code,
            snomed_display,
            kategori,
            aktif,
            datetime_create,
            datetime_update
        ) VALUES (
            ?, ?, ?, ?, ?, NOW(), NOW()
        )
    ");

    $stmt->bind_param(
        "sssss",
        $nama_klinis,
        $snomed_code,
        $snomed_display,
        $kategori,
        $aktif
    );

    if ($stmt->execute()) {
        $response['status']  = 'success';
        $response['message'] = 'Data klinis berhasil ditambahkan';
    } else {
        $response['message'] = 'Gagal menyimpan data';
    }

} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

/* Output JSON */
echo json_encode($response);
