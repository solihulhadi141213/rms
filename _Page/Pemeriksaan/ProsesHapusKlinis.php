<?php
    // Koneksi Global Function dan Session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    // Validasi Sesi
    if (empty($SessionIdAccess)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Sesi Akses Sudah Berakhir. Silahkan Login Ulang!'
        ]);
        exit;
    }

    // Validasi input
    if (empty($_POST['id_radiologi'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'ID Radiologi tidak boleh kosong!'
        ]);
        exit;
    }

    if (empty($_POST['id_klinis'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'ID Klinis tidak boleh kosong!'
        ]);
        exit;
    }

    // Sanitasi
    $id_radiologi = validateAndSanitizeInput($_POST['id_radiologi']);
    $id_klinis    = validateAndSanitizeInput($_POST['id_klinis']);

    // Ambil data klinis lama
    $Qry = $Conn->prepare("SELECT klinis FROM radiologi WHERE id_radiologi = ?");
    $Qry->bind_param("i", $id_radiologi);
    $Qry->execute();
    $Res = $Qry->get_result();
    $Row = $Res->fetch_assoc();
    $Qry->close();

    if (!$Row) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Data radiologi tidak ditemukan!'
        ]);
        exit;
    }

    // Decode JSON
    $klinis_lama = [];
    if (!empty($Row['klinis'])) {
        $klinis_lama = json_decode($Row['klinis'], true);
        if (!is_array($klinis_lama)) {
            $klinis_lama = [];
        }
    }

    // Cari & hapus klinis
    $found = false;
    $klinis_baru = [];

    foreach ($klinis_lama as $item) {
        if (isset($item['id_klinis']) && $item['id_klinis'] === $id_klinis) {
            $found = true;
            continue; // lewati item yang dihapus
        }
        $klinis_baru[] = $item;
    }

    // Jika tidak ditemukan
    if (!$found) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Data klinis tidak ditemukan!'
        ]);
        exit;
    }

    // Encode ulang JSON
    $klinis_json = json_encode($klinis_baru, JSON_UNESCAPED_UNICODE);

    // Update database
    $Upd = $Conn->prepare("
        UPDATE radiologi 
        SET klinis = ?
        WHERE id_radiologi = ?
    ");
    $Upd->bind_param("si", $klinis_json, $id_radiologi);

    if (!$Upd->execute()) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal menghapus data klinis!'
        ]);
        exit;
    }
    $Upd->close();

    // Sukses
    echo json_encode([
        'status' => 'success',
        'message' => 'Data klinis berhasil dihapus'
    ]);
    exit;

?>
