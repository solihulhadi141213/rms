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
            <div class="row mb-3">
                <div class="col-12 text-center">
                    <div class="alert alert-danger"><small>ID File Tiidak Valid!</small></div>
                </div>
            </div>
        ';
        exit;
    }

    //Buat Variabel
    $id_radiologi     = $Data['id_radiologi'];
    if(empty($Data['id_imaging_study'])){
        $id_imaging_study = '
            <a href="javascript:void(0);" class="text-danger">
                <i class="bi bi-plus-circle"></i> Kirim Imaging Study
            </a>
        ';
    }else{
        $id_imaging_study = $Data['id_imaging_study'];
        $id_imaging_study = '
            <a href="javascript:void(0);" class="text-danger">
                <i class="bi bi-arrow-up-right-square"></i> '.$id_imaging_study.'
            </a>
        ';
    }
    $accession_number = $Data['accession_number'];
    $filename         = $Data['filename'];
    $dicom_metadata   = $Data['dicom_metadata'];

    // Buka Informasi Pemeriksaan Radiologi
    $nama_pasien = GetDetailData($Conn, 'radiologi', 'id_radiologi', $id_radiologi, 'nama_pasien');

    // Buka DICOM metadata
    $dicom_metadata_arry = json_decode($dicom_metadata, true);
    $sop_uid             = $dicom_metadata_arry['sop-uid'];
    $Modality            = $dicom_metadata_arry['Modality'];
    $PatientID           = $dicom_metadata_arry['PatientID'];
    $study_uid           = $dicom_metadata_arry['study-uid'];
    $series_uid          = $dicom_metadata_arry['series-uid'];
    $PatientName         = $dicom_metadata_arry['PatientName'];
    $StudyDescription    = $dicom_metadata_arry['StudyDescription'];
   
?>
<div class="row mb-2">
    <div class="col-12"><small><b>A. Informasi File DICOM</b></small></div>
</div>
<div class="row mb-2">
    <div class="col-4"><small>Nama Pasien</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <small class="text text-grayish"><?php echo "$nama_pasien"; ?></small>
    </div>
</div>
<div class="row mb-2">
    <div class="col-4"><small><i>Accession Number</i></small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <small class="text text-grayish"><?php echo "$accession_number"; ?></small>
    </div>
</div>
<div class="row mb-2">
    <div class="col-4"><small><i>ID Imaging Study</i></small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <small class="text text-grayish"><?php echo "$id_imaging_study"; ?></small>
    </div>
</div>
<div class="row mb-2">
    <div class="col-4"><small><i>File Name</i></small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <small class="text text-grayish"><?php echo "$filename"; ?></small>
    </div>
</div>
<div class="row mb-2 mt-3">
    <div class="col-12"><small><b>B. DICOM Metadata</b></small></div>
</div>
<div class="row mb-2">
    <div class="col-4"><small>sop-uid</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <small class="text text-grayish"><?php echo "$sop_uid"; ?></small>
    </div>
</div>
<div class="row mb-2">
    <div class="col-4"><small>Modality</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <small class="text text-grayish"><?php echo "$Modality"; ?></small>
    </div>
</div>
<div class="row mb-2">
    <div class="col-4"><small>Patient ID</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <small class="text text-grayish"><?php echo "$PatientID"; ?></small>
    </div>
</div>
<div class="row mb-2">
    <div class="col-4"><small>study-uid</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <small class="text text-grayish"><?php echo "$study_uid"; ?></small>
    </div>
</div>
<div class="row mb-2">
    <div class="col-4"><small>series-uid</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <small class="text text-grayish"><?php echo "$series_uid"; ?></small>
    </div>
</div>
<div class="row mb-2">
    <div class="col-4"><small>Patient Name</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <small class="text text-grayish"><?php echo "$PatientName"; ?></small>
    </div>
</div>
<div class="row mb-2">
    <div class="col-4"><small>Study Description</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <small class="text text-grayish"><?php echo "$StudyDescription"; ?></small>
    </div>
</div>
<div class="row mb-2 mt-3">
    <div class="col-12">
        <button type="button" class="btn btn-primary btn-md btn-block modal_dicom_viewer" data-id="<?php echo $id_radiologi_dicom_conv; ?>">
            <i class="bi bi-arrow-right"></i> Buka File
        </button>
    </div>
</div>