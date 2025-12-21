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

// Ambil & sanitasi ID
$id_master_pemeriksaan = validateAndSanitizeInput($_POST['id_master_pemeriksaan'] ?? '');

// Validasi ID
if (empty($id_master_pemeriksaan)) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'ID Kode Pemeriksaan tidak boleh kosong.'
    ]);
    exit;
}

try {

    /* -------------------------------------------------
       Cek apakah data masih ada
    ------------------------------------------------- */
    $Cek = $Conn->prepare("
        SELECT id_master_pemeriksaan 
        FROM master_pemeriksaan 
        WHERE id_master_pemeriksaan = ?
    ");
    $Cek->bind_param("i", $id_master_pemeriksaan);
    $Cek->execute();
    $Cek->store_result();

    if ($Cek->num_rows == 0) {
        $Cek->close();
        echo json_encode([
            'status'  => 'error',
            'message' => 'Data tidak ditemukan atau sudah dihapus.'
        ]);
        exit;
    }
    $Cek->close();

    /* -------------------------------------------------
       Proses Hard Delete
    ------------------------------------------------- */
    $Qry = $Conn->prepare("
        DELETE FROM master_pemeriksaan 
        WHERE id_master_pemeriksaan = ?
    ");
    $Qry->bind_param("i", $id_master_pemeriksaan);

    if (!$Qry->execute()) {
        throw new Exception($Qry->error);
    }

    $Qry->close();

    echo json_encode([
        'status'  => 'success',
        'message' => 'Data pemeriksaan berhasil dihapus.'
    ]);

} catch (Exception $e) {

    echo json_encode([
        'status'  => 'error',
        'message' => 'Gagal menghapus data. ' . $e->getMessage()
    ]);
}
