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

    // =====================================================================
    // VALIDASI SESSION
    // =====================================================================
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

    if(empty($_POST['tanggal_lahir'])){
        $response['message'] = 'Tanggal Lahir Tidak Boleh Kosong!.';
        echo json_encode($response);
        exit;
    }

    // Buat Variabel
    $id_radiologi    = (int) ($_POST['id_radiologi'] ?? 0);
    $tanggal_lahir = validateAndSanitizeInput($_POST['tanggal_lahir'] ?? '');

    // Update Ke Database
    $stmt = $Conn->prepare("UPDATE radiologi SET tanggal_lahir = ? WHERE id_radiologi = ? ");
    $stmt->bind_param(
        "si",
        $tanggal_lahir,
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
            'message' => 'Data Dokter Pengirim dan Penerima Radiologi Berhasil Diperbaharui'
        ]);
        $stmt->close();
        exit;
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Terjadi Kesalahan Pada Saat Update Radiologi'
        ]);
        $stmt->close();
        exit;
    }
?>