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

    // VALIDASI INPUT
    if(empty($_POST['id_radiologi'])){
        $response['message'] = 'ID Radiologi Tidak Boleh Kosong!.';
        echo json_encode($response);
        exit;
    }

    if(empty($_POST['id_service_request'])){
        $id_service_request = "";
    }else{
        $id_service_request = $_POST['id_service_request'];
    }

    if(empty($_POST['id_procedure'])){
        $id_procedure = "";
    }else{
        $id_procedure = $_POST['id_procedure'];
    }

    if(empty($_POST['id_imaging_study'])){
        $id_imaging_study = "";
    }else{
        $id_imaging_study = $_POST['id_imaging_study'];
    }

    if(empty($_POST['id_observation'])){
        $id_observation = "";
    }else{
        $id_observation = $_POST['id_observation'];
    }

    if(empty($_POST['id_diagnostic_report'])){
        $id_diagnostic_report = "";
    }else{
        $id_diagnostic_report = $_POST['id_diagnostic_report'];
    }

    // Buat Variabel Dan Sanitasi
    $id_radiologi         = (int) ($_POST['id_radiologi'] ?? 0);
    $id_service_request   = validateAndSanitizeInput($_POST['id_service_request'] ?? '');
    $id_procedure         = validateAndSanitizeInput($_POST['id_procedure'] ?? '');
    $id_imaging_study     = validateAndSanitizeInput($_POST['id_imaging_study'] ?? '');
    $id_observation       = validateAndSanitizeInput($_POST['id_observation'] ?? '');
    $id_diagnostic_report = validateAndSanitizeInput($_POST['id_diagnostic_report'] ?? '');

    // Update Ke Database
    $stmt = $Conn->prepare("UPDATE radiologi SET
            id_service_request   = ?,
            id_procedure         = ?,
            id_imaging_study     = ?,
            id_observation       = ?,
            id_diagnostic_report = ?
        WHERE id_radiologi = ?
    ");

    $stmt->bind_param(
        "sssssi",
        $id_service_request,
        $id_procedure,
        $id_imaging_study,
        $id_observation,
        $id_diagnostic_report,
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