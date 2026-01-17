<?php
    // ProsesTambah.php
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

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

    // Validasi input 'id_master_pemeriksaan'
    if(empty($_POST['id_master_pemeriksaan'])){
        echo json_encode(['status' => 'error','message' => 'ID Master Pemeriksaan tidak boleh kosong!']);
        exit;
    }

    $id_radiologi          = validateAndSanitizeInput($_POST['id_radiologi']);
    $id_master_pemeriksaan = validateAndSanitizeInput($_POST['id_master_pemeriksaan']);

    // Buka Data master Pemeriksaan
    $Qry = $Conn->prepare("SELECT * FROM master_pemeriksaan WHERE id_master_pemeriksaan = ?");
    $Qry->bind_param("i", $id_master_pemeriksaan);
    if (!$Qry->execute()) {
        $error=$Conn->error;
        echo json_encode(['status' => 'error','message' => 'Terjadi kesalahan pada saat membuka data dari database!<br>Keterangan : '.$error.'']);
        exit;
    }
    $Result = $Qry->get_result();
    $Data = $Result->fetch_assoc();
    $Qry->close();
    
    // Jika Data Tidak Ditemukan
    if(empty($Data['id_master_pemeriksaan'])){
        echo json_encode(['status' => 'error','message' => 'Referensi Pemeriksaan Tidak Ditemukan!']);
        exit;
    }

    $id_master_pemeriksaan   = $Data['id_master_pemeriksaan'];
    $nama_pemeriksaan        = $Data['nama_pemeriksaan'];
    $modalitas               = $Data['modalitas'];
    $pemeriksaan_code        = $Data['pemeriksaan_code'];
    $pemeriksaan_description = $Data['pemeriksaan_description'];
    $pemeriksaan_sys         = $Data['pemeriksaan_sys'];
    $bodysite_code           = $Data['bodysite_code'];
    $bodysite_description    = $Data['bodysite_description'];
    $bodysite_sys            = $Data['bodysite_sys'];
    $report_code             = $Data['report_code'];
    $report_description      = $Data['report_description'];
    $report_sys              = $Data['report_sys'];

    // Buat payload
    $payload = [[
        'id_master_pemeriksaan'   => $id_master_pemeriksaan,
        'modalitas'               => $modalitas,
        'nama_pemeriksaan'        => $nama_pemeriksaan,
        'pemeriksaan_code'        => $pemeriksaan_code,
        'pemeriksaan_description' => $pemeriksaan_description,
        'pemeriksaan_sys'         => $pemeriksaan_sys,
        'bodysite_code'           => $bodysite_code,
        'bodysite_description'    => $bodysite_description,
        'bodysite_sys'            => $bodysite_sys,
        'report_code'             => $report_code,
        'report_description'      => $report_description,
        'report_sys'              => $report_sys
    ]];

    // Encode ke JSON
    $payload_json = json_encode($payload, JSON_UNESCAPED_UNICODE);

    // Update ke tabel radiologi
    $Upd = $Conn->prepare("UPDATE radiologi SET permintaan_pemeriksaan = ? WHERE id_radiologi = ?");
    $Upd->bind_param("si", $payload_json, $id_radiologi);

    if (!$Upd->execute()) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal menyimpan data pemeriksaan ke radiologi!'
        ]);
        exit;
    }
    $Upd->close();

    // Sukses
    echo json_encode([
        'status' => 'success',
        'message' => 'Data pemeriksaan berhasil diperbaharui'
    ]);
    exit;
?>