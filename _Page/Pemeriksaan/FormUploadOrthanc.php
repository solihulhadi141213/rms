<?php
    //koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";
    include "../../_Config/SettingGeneral.php";
    
    //Zona Waktu
    date_default_timezone_set("Asia/Jakarta");

    //Session Akses
    if(empty($SessionIdAccess)){
        echo '
            <div class="row mb-3">
                <div class="col-12 text-center">
                    <div class="alert alert-danger"><small>Sesi Akses Sudah Berakhir! Silahkan Login Ulang.</small></div>
                </div>
            </div>
        ';
        exit;
    }

    //id_radiologi_dicom_conv wajib terisi
    if(empty($_POST['id_radiologi_dicom_conv'])){
        echo '
            <div class="row mb-3">
                <div class="col-12 text-center">
                    <div class="alert alert-danger"><small>ID File Tidak Boleh Kosong!</small></div>
                </div>
            </div>
        ';
        exit;
    }

    //Buat variabel 'id_radiologi_dicom_conv' dan sanitasi
    $id_radiologi_dicom_conv = validateAndSanitizeInput($_POST['id_radiologi_dicom_conv']);

    //Buka Detail Koneksi Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM radiologi_dicom_conv WHERE id_radiologi_dicom_conv = ?");
    $Qry->bind_param("s", $id_radiologi_dicom_conv);
    if (!$Qry->execute()) {
        $error=$Conn->error;
        echo '
            <div class="alert alert-danger">
                <small>Terjadi kesalahan pada saat membuka data dari database!<br>Keterangan : '.$error.'</small>
            </div>
        ';
        exit;
    }
    $Result = $Qry->get_result();
    $Data = $Result->fetch_assoc();
    $Qry->close();

    // Jika Data Tidak Ditemukan
    if(empty($Data['id_radiologi_dicom_conv'])){
        echo '
            <div class="alert alert-danger">
                <small>Data Tidak Ditemukan</small>
            </div>
        ';
        exit;
    }

    //Buat Variabel
    $id_radiologi     = $Data['id_radiologi'];
    $accession_number = $Data['accession_number'];
    $filename         = $Data['filename'];
    $dicom_metadata   = $Data['dicom_metadata'];
    $orthanc_id       = $Data['orthanc_id'];
    $ParentStudy      = $Data['ParentStudy'];
    if(!empty($orthanc_id)){
        echo '
            <div class="alert alert-danger">
                <small>File Sudah Di Kirim Sebelumnya!</small>
            </div>
        ';
        exit;
    }

    // Buka pengaturan Orthanc
    $status_connection_orthanc = 1;
    $QryOrt = $Conn->prepare("SELECT * FROM connection_orthanc WHERE status_connection_orthanc = ?");
    $QryOrt->bind_param("i", $status_connection_orthanc);
    if (!$QryOrt->execute()) {
        $error=$Conn->error;
        echo '
            <div class="alert alert-danger">
                <small>Terjadi kesalahan pada saat membuka pengaturan Orthanc data dari database!<br>Keterangan : '.$Conn->error.'</small>
            </div>
        ';
        exit;
    }
    $ResultOrt = $QryOrt->get_result();
    $DataOrt = $ResultOrt->fetch_assoc();
    $QryOrt->close();

    // Jika Data Tidak Ditemukan
    if(empty($DataOrt['id_connection_orthanc'])){
        echo '
            <div class="alert alert-danger">
                <small>Pengaturan Orthanc Tidak Ditemukan</small>
            </div>
        ';
        exit;
    }
    //Buat Variabel
    $url_connection_orthanc = $DataOrt['url_connection_orthanc'];

    echo '
        <input type="hidden" name="id_radiologi_dicom_conv" value="'.$id_radiologi_dicom_conv.'">
    ';
?>
    <div class="row mb-2">
        <div class="col-4"><small>ID File</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-7">
            <small class="text text-dark"><?php echo "$id_radiologi_dicom_conv"; ?></small>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-4"><small>Path File</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-7">
            <small class="text text-dark"><?php echo "_DCM/$filename"; ?></small>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-4"><small><i>Path Orthanc</i></small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-7">
            <small class="text text-dark"><?php echo "$url_connection_orthanc"; ?></small>
        </div>
    </div>