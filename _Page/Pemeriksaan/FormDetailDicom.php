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
            <a href="javascript:void(0);" class="text-danger modal_send_imaging_study_by_dicom" data-id="'.$id_radiologi_dicom_conv.'">
                <i class="bi bi-plus-circle"></i> Kirim Imaging Study
            </a>
        ';
    }else{
        $id_imaging_study = $Data['id_imaging_study'];
        $id_imaging_study = '
            <a href="javascript:void(0);" class="text-success modal_detail_imaging_study_by_is" data-id="'.$id_imaging_study.'">
                <i class="bi bi-arrow-up-right-square"></i> '.$id_imaging_study.'
            </a>
        ';
    }
    $accession_number = $Data['accession_number'];
    $filename         = $Data['filename'];
    $dicom_metadata   = $Data['dicom_metadata'];
    $orthanc_id       = $Data['orthanc_id'];
    $ParentStudy      = $Data['ParentStudy'];

    // Buka Informasi Pemeriksaan Radiologi
    $nama_pasien = GetDetailData($Conn, 'radiologi', 'id_radiologi', $id_radiologi, 'nama_pasien');

    // Buka DICOM metadata
    $dicom_metadata_arry = json_decode($dicom_metadata, true);

    //Buat Variabel
    $Modality          = $dicom_metadata_arry['Modality'];
    $ConversionDate    = $dicom_metadata_arry['ConversionDate'];
    $SOPClassUID       = $dicom_metadata_arry['SOPClassUID'];
    $Modality          = $dicom_metadata_arry['Modality'];
    $PatientID         = $dicom_metadata_arry['PatientID'];
    $SeriesInstanceUID = $dicom_metadata_arry['SeriesInstanceUID'];
    $StudyInstanceUID  = $dicom_metadata_arry['StudyInstanceUID'];
    $PatientName       = $dicom_metadata_arry['PatientName'];
    $StudyDescription  = $dicom_metadata_arry['StudyDescription'];
    $ConversionDate    = $dicom_metadata_arry['ConversionDate'];
   
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
    <div class="col-4"><small>Patient ID</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <small class="text text-grayish"><?php echo "$PatientID"; ?></small>
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
    <div class="col-4"><small>Modality</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <small class="text text-grayish"><?php echo "$Modality"; ?></small>
    </div>
</div>
<div class="row mb-2">
    <div class="col-4"><small>SOP Class UID</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <small class="text text-grayish"><?php echo "$SOPClassUID"; ?></small>
    </div>
</div>
<div class="row mb-2">
    <div class="col-4"><small>Study Instance UID</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <small class="text text-grayish"><?php echo "$StudyInstanceUID"; ?></small>
    </div>
</div>
<div class="row mb-2">
    <div class="col-4"><small>Series Instance UID</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <small class="text text-grayish"><?php echo "$SeriesInstanceUID"; ?></small>
    </div>
</div>
<div class="row mb-2">
    <div class="col-4"><small>Conversion Date</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <small class="text text-grayish"><?php echo "$ConversionDate"; ?></small>
    </div>
</div>

<div class="row mb-2 mt-3">
    <div class="col-12">
        <button type="button" class="btn btn-primary btn-md btn-block modal_dicom_viewer" data-id="<?php echo $id_radiologi_dicom_conv; ?>">
            <i class="bi bi-arrow-right"></i> Buka File
        </button>
    </div>
</div>
<div class="row mb-2 mt-3">
    <div class="col-12">
        <?php
            // Jika Belum Ada pada Ortanct
            if(empty($orthanc_id)){
                echo '
                    <button type="button" class="btn btn-warning btn-md btn-block modal_upload_orthanc" data-id="'.$id_radiologi_dicom_conv.'">
                        <i class="bi bi-save"></i> Save To Orthanc
                    </button>
                ';
            }else{
                // Jika Sudah Ada
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
                }else{
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
                    }else{
                        //Buat Variabel 'url_connection_orthanc'
                        $username_connection_orthanc = $DataOrt['username_connection_orthanc'];
                        $password_connection_orthanc = $DataOrt['password_connection_orthanc'];
                        $url_connection_orthanc = $DataOrt['url_connection_orthanc'];

                        // Dapatkan 'StudyInstanceUID'
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url_connection_orthanc . "/studies/" . $ParentStudy);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_USERPWD, $username_connection_orthanc . ":" . $password_connection_orthanc);
                        $response = curl_exec($ch);
                        curl_close($ch);
                        $result = json_decode($response, true);
                        $StudyInstanceUID = $result['MainDicomTags']['StudyInstanceUID'];

                        // Buat Link
                        $viewer_url = "$url_connection_orthanc/ohif/viewer?StudyInstanceUIDs=$StudyInstanceUID";

                        // Tampilkan Tombol
                        echo '
                            <a href="'.$viewer_url.'" target="_blank" class="btn btn-secondary btn-md btn-block">
                                <i class="bi bi-save"></i> Open Orthanc Viewer
                            </a>
                        ';
                    }
                }
            }
        ?>
    </div>
</div>