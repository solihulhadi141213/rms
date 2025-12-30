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

    // =======================
    // VALIDASI SESSION
    // =======================
    if (empty($SessionIdAccess)) {
        $response['message'] = 'Sesi akses telah berakhir. Silakan login ulang.';
        echo json_encode($response);
        exit;
    }

    // =======================
    // VALIDASI INPUT
    // =======================
    $id_radiologi = validateAndSanitizeInput($_POST['id_radiologi'] ?? '');
    $kv           = validateAndSanitizeInput($_POST['kv'] ?? '');
    $ma           = validateAndSanitizeInput($_POST['ma'] ?? '');
    $sec          = validateAndSanitizeInput($_POST['sec'] ?? '');

    if (empty($id_radiologi)) {
        $response['message'] = 'ID Permintaan Pemeriksaan tidak valid.';
        echo json_encode($response);
        exit;
    }

    // =======================
    // CEK DATA ADA ATAU TIDAK
    // =======================
    $QryCheck = $Conn->prepare("SELECT id_radiologi, status_pemeriksaan FROM radiologi WHERE id_radiologi = ?");
    if (!$QryCheck) {
        $response['message'] = $Conn->error;
        echo json_encode($response);
        exit;
    }

    $QryCheck->bind_param("i", $id_radiologi);
    $QryCheck->execute();
    $Result = $QryCheck->get_result();
    $Data   = $Result->fetch_assoc();
    $QryCheck->close();

    if (!$Data) {
        $response['message'] = 'Data Permintaan tidak ditemukan.';
        echo json_encode($response);
        exit;
    }

    // ======================================================
    // PROSES UPDATE
    // ======================================================
    $update = $Conn->prepare("UPDATE radiologi SET kv = ?, ma = ?, sec = ? WHERE id_radiologi = ?");
    $update->bind_param("sssi", $kv, $ma, $sec, $id_radiologi);
    $update_executed = $update->execute();

    if (!$update_executed) {
        // Log database error tapi tetap return success untuk API
        error_log("Database update failed: " . $Conn->error);
    }

    $update->close();

    // ======================================================
    // RESPONSE SUKSES
    // ======================================================
    echo json_encode([
        'status'             => 'success',
        'message'            => 'Faktor Eksposur Berhasil Di Update',
        'id_radiologi'       => $id_radiologi
    ]);
    exit;

?>