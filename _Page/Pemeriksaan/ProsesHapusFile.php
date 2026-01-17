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
    if (empty($_POST['id_radiologi_file'])) {
        $response['message'] = 'ID File Tidak Boleh Kosong';
        echo json_encode($response);
        exit;
    }

    $id_radiologi_file = validateAndSanitizeInput($_POST['id_radiologi_file']);

    // =====================================================================
    // AMBIL DATA FILE
    // =====================================================================
    $Qry = $Conn->prepare("
        SELECT 
            id_radiologi_file,
            folder_name,
            file_name
        FROM radiologi_file 
        WHERE id_radiologi_file = ?
    ");
    $Qry->bind_param("s", $id_radiologi_file);

    if (!$Qry->execute()) {
        $response['message'] = 'Gagal membuka data file';
        echo json_encode($response);
        exit;
    }

    $Result = $Qry->get_result();
    $Data   = $Result->fetch_assoc();
    $Qry->close();

    if (empty($Data['id_radiologi_file'])) {
        $response['message'] = 'ID File Tidak Valid';
        echo json_encode($response);
        exit;
    }

    // =====================================================================
    // HAPUS FILE FISIK
    // =====================================================================
    $folder_name = $Data['folder_name'];
    $file_name   = $Data['file_name'];

    $file_path = "../../_Storage/$folder_name/$file_name";

    // Jika file ada → hapus
    if (file_exists($file_path)) {
        if (!unlink($file_path)) {
            $response['message'] = 'Gagal menghapus file dari storage';
            echo json_encode($response);
            exit;
        }
    }

    // =====================================================================
    // HAPUS FILE FISIK DICOM jika ada
    // =====================================================================
    $filename_dicom_conv = GetDetailData($Conn, 'radiologi_dicom_conv ', 'id_radiologi_file', $id_radiologi_file, 'filename');
    if(!empty($filename_dicom_conv)){
        
    // Jika ada Maka Baca Path Nya
        $file_path_dicom = "../../_DCM/$filename_dicom_conv";
        // Jika file ada → hapus
        if (file_exists($file_path_dicom)) {
            if (!unlink($file_path_dicom)) {
                $response['message'] = 'Gagal menghapus file DICOM dari storage';
                echo json_encode($response);
                exit;
            }
        }
    }
    
    // =====================================================================
    // HAPUS DATA DATABASE
    // =====================================================================
    $Del = $Conn->prepare("DELETE FROM radiologi_file WHERE id_radiologi_file = ?");
    $Del->bind_param("s", $id_radiologi_file);

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
        'message' => 'File radiologi berhasil dihapus'
    ];

    echo json_encode($response);
    exit;

?>