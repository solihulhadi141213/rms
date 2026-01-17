<?php
    // ProsesTambah.php
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    // Fungsi money
    function moneyToNumber($value) {
        if ($value === null || $value === '') return 0;
        return (float) str_replace(['.', ','], ['', '.'], $value);
    }

    // Validasi Sesi
    if(empty($SessionIdAccess)){
        echo json_encode(['status' => 'error','message' => 'Sesi Akses Sudah Berakhir. Silahkan Login Ulang!']);
        exit;
    }

    // Validasi input 'id_radiologi'
    if(empty($_POST['id_radiologi'])){
        echo json_encode(['status' => 'error','message' => 'ID Radiologi tidak boleh kosong!']);
        exit;
    }

    // Validasi input 'id_master_klinis'
    if(empty($_POST['id_master_klinis'])){
        echo json_encode(['status' => 'error','message' => 'ID Master Klinis tidak boleh kosong!']);
        exit;
    }

    $id_radiologi     = validateAndSanitizeInput($_POST['id_radiologi']);
    $id_master_klinis = validateAndSanitizeInput($_POST['id_master_klinis']);

    // Buka Data master klinis
    $Qry = $Conn->prepare("SELECT * FROM master_klinis WHERE id_master_klinis = ?");
    $Qry->bind_param("i", $id_master_klinis);
    if (!$Qry->execute()) {
        $error=$Conn->error;
        echo json_encode(['status' => 'error','message' => 'Terjadi kesalahan pada saat membuka data dari database!<br>Keterangan : '.$error.'']);
        exit;
    }
    $Result = $Qry->get_result();
    $Data = $Result->fetch_assoc();
    $Qry->close();
    
    // Jika Data Tidak Ditemukan
    if(empty($Data['id_master_klinis'])){
        echo json_encode(['status' => 'error','message' => 'Referensi Klinis Tidak Ditemukan!']);
        exit;
    }

    $nama_klinis     = $Data['nama_klinis'];
    $snomed_code     = $Data['snomed_code'];
    $snomed_display  = $Data['snomed_display'];
    $kategori        = $Data['kategori'];
    $aktif           = $Data['aktif'];
    $datetime_create = $Data['datetime_create'];
    $datetime_update = $Data['datetime_update'];

    // Buka Klinis Dari Radiologi
    // Ambil data klinis lama dari radiologi
    $Qry = $Conn->prepare("SELECT klinis FROM radiologi WHERE id_radiologi = ?");
    $Qry->bind_param("i", $id_radiologi);
    $Qry->execute();
    $Res = $Qry->get_result();
    $Row = $Res->fetch_assoc();
    $Qry->close();

    // Decode JSON lama
    $klinis_lama = [];
    if (!empty($Row['klinis'])) {
        $klinis_lama = json_decode($Row['klinis'], true);
        if (!is_array($klinis_lama)) {
            $klinis_lama = [];
        }
    }

    // 🔒 CEK DUPLIKASI id_master_klinis
    foreach ($klinis_lama as $item) {
        if (
            isset($item['id_master_klinis']) &&
            (int)$item['id_master_klinis'] === (int)$id_master_klinis
        ) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Klinis ini sudah ditambahkan sebelumnya!'
            ]);
            exit;
        }
    }

    // Data klinis baru
    $klinis_baru = [
        'kategori'          => $kategori,
        'id_klinis'         => generateUUIDv4(), // dari GlobalFunction
        'nama_klinis'       => $nama_klinis,
        'snomed_code'       => $snomed_code,
        'snomed_display'    => $snomed_display,
        'id_master_klinis'  => (int)$id_master_klinis
    ];

    // Gabungkan
    $klinis_lama[] = $klinis_baru;

    // Encode kembali ke JSON
    $klinis_json = json_encode($klinis_lama, JSON_UNESCAPED_UNICODE);

    // Update ke tabel radiologi
    $Upd = $Conn->prepare("UPDATE radiologi SET klinis = ? WHERE id_radiologi = ?");
    $Upd->bind_param("si", $klinis_json, $id_radiologi);

    if (!$Upd->execute()) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal menyimpan data klinis ke radiologi!'
        ]);
        exit;
    }
    $Upd->close();

    // Sukses
    echo json_encode([
        'status' => 'success',
        'message' => 'Data klinis berhasil ditambahkan'
    ]);
    exit;
?>