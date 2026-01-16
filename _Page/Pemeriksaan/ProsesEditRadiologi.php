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

    // VALIDASI SESSION
    if (empty($SessionIdAccess)) {
        $response['message'] = 'Sesi akses telah berakhir. Silakan login ulang.';
        echo json_encode($response);
        exit;
    }

    // =====================================================================
    // VALIDASI INPUT
    // =====================================================================
    if(empty($_POST['id_radiologi'])){
        $response['message'] = 'ID Radiologi Tidak Boleh Kosong!.';
        echo json_encode($response);
        exit;
    }

    if(empty($_POST['priority'])){
        $response['message'] = 'Kategori Prioritas Permintaan Tidak Boleh Kosong!.';
        echo json_encode($response);
        exit;
    }

    if(empty($_POST['status_pemeriksaan'])){
        $response['message'] = 'Status Pemeriksaan Tidak Boleh Kosong!.';
        echo json_encode($response);
        exit;
    }

    if(empty($_POST['asal_kiriman'])){
        $response['message'] = 'Informasi Asal Kiriman Tidak Boleh Kosong!.';
        echo json_encode($response);
        exit;
    }

    // Buat Variabel Dan Sanitasi
    $id_radiologi       = (int) ($_POST['id_radiologi'] ?? 0);
    $priority           = validateAndSanitizeInput($_POST['priority'] ?? '');
    $alat_pemeriksa     = validateAndSanitizeInput($_POST['alat_pemeriksa'] ?? '');
    $status_pemeriksaan = validateAndSanitizeInput($_POST['status_pemeriksaan'] ?? '');
    $radiografer        = validateAndSanitizeInput($_POST['radiografer'] ?? '');
    $asal_kiriman       = validateAndSanitizeInput($_POST['asal_kiriman'] ?? '');

    if(empty($_POST['alat_pemeriksa'])){
       $alat_pemeriksa = GetDetailData($Conn, 'radiologi', 'id_radiologi', $id_radiologi, 'alat_pemeriksa');
    }

    if(empty($_POST['radiografer'])){
       $radiografer = GetDetailData($Conn, 'radiologi', 'id_radiologi', $id_radiologi, 'radiografer');
    }

    // Variabel Tidak Wajib
    if(empty($_POST['pesan'])){
        $pesan = "";
    }else{
        $pesan = $_POST['pesan'];
    }

    if(empty($_POST['alasan_pembatalan'])){
        $alasan_pembatalan = "";
    }else{
        $alasan_pembatalan = $_POST['alasan_pembatalan'];
    }

    // Apabila $status_pemeriksaan == Batal maka $alasan_pembatalan wajib terisi
    if($status_pemeriksaan=="Batal" && empty($_POST['alasan_pembatalan'])){
        $response['message'] = 'Alasan Pembatalan Harus Diisi!.';
        echo json_encode($response);
        exit;
    }

    // Update Ke Database
    $stmt = $Conn->prepare("UPDATE radiologi SET
            priority           = ?,
            alat_pemeriksa     = ?,
            status_pemeriksaan = ?,
            radiografer        = ?,
            asal_kiriman       = ?,
            pesan              = ?,
            alasan_pembatalan  = ?
        WHERE id_radiologi = ?
    ");

    $stmt->bind_param(
        "sssssssi",
        $priority,
        $alat_pemeriksa,
        $status_pemeriksaan,
        $radiografer,
        $asal_kiriman,
        $pesan,
        $alasan_pembatalan,
        $id_radiologi
    );

    if (!$stmt) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal menyiapkan query database'
        ]);
        exit;
    }

    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Pemeriksaan Radiologi Berhasil Diperbaharui'
        ]);
        $stmt->close();
        exit;
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Terjadi Kesalahan Pada Saat Update Informasi Radiologi'
        ]);
        $stmt->close();
        exit;
    }
?>