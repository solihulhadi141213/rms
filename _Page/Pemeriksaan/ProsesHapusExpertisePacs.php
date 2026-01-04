<?php
    // koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";
    include "../../_Config/SettingGeneral.php";

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
    if (empty($_POST['id_radiologi_expertise'])) {
        $response['message'] = 'ID Expertise Tidak Boleh Kosong';
        echo json_encode($response);
        exit;
    }

    if (empty($_POST['modality'])) {
        $response['message'] = 'Modality Tidak Boleh Kosong';
        echo json_encode($response);
        exit;
    }

    $id_radiologi_expertise = validateAndSanitizeInput($_POST['id_radiologi_expertise']);
    $modality               = validateAndSanitizeInput($_POST['modality']);

    

    // =====================================================================
    // HAPUS DATA DATABASE
    // =====================================================================
    if($modality=="XR"){
        $Del = $Conn->prepare("DELETE FROM radiologi_expertise WHERE id_radiologi_expertise = ?");
    }else{
        $Del = $Conn->prepare("DELETE FROM radiologi_expertise_usg  WHERE id_radiologi_expertise_usg  = ?");
    }
    
    $Del->bind_param("i", $id_radiologi_expertise);

    if (!$Del->execute()) {
        $response['message'] = 'Gagal menghapus data dari database';
        echo json_encode($response);
        exit;
    }
    $Del->close();

    // =====================================================================
    // RESPONSE SUCCESS
    // =====================================================================
    $response = [
        'status'  => 'success',
        'message' => 'Data Expertise '.$modality.' berhasil dihapus'
    ];

    echo json_encode($response);
    exit;

?>