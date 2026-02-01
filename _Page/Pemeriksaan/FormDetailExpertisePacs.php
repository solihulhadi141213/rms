<?php
    //koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";
    include "../../_Config/SettingGeneral.php";

    // Helper function: fallback jika kosong
    function val($array, $key) {
        return (!empty($array[$key])) ? $array[$key] : '-';
    }
    
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

    //id_radiologi_expertise wajib terisi
    if(empty($_POST['id_radiologi_expertise'])){
        echo '
            <div class="row mb-3">
                <div class="col-12 text-center">
                    <div class="alert alert-danger"><small>ID Expertise Tiidak Boleh Kosong!</small></div>
                </div>
            </div>
        ';
        exit;
    }

    //modality wajib terisi
    if(empty($_POST['modality'])){
        echo '
            <div class="row mb-3">
                <div class="col-12 text-center">
                    <div class="alert alert-danger"><small>Kategori Modality Tiidak Boleh Kosong!</small></div>
                </div>
            </div>
        ';
        exit;
    }


    //Buat variabel 'id_radiologi_expertise' dan sanitasi
    $id_radiologi_expertise = validateAndSanitizeInput($_POST['id_radiologi_expertise']);
    $modality               = validateAndSanitizeInput($_POST['modality']);


    // Menampilkan Expertise XR
    if($modality =="XR"){
        $Qry = $Conn->prepare("SELECT * FROM radiologi_expertise WHERE id_radiologi_expertise = ?");
        $Qry->bind_param("i", $id_radiologi_expertise);
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
        if(empty($Data['id_radiologi_expertise'])){
            echo '
                <div class="row mb-3">
                    <div class="col-12 text-center">
                        <div class="alert alert-danger"><small>Data Expertise XR Tidak Ditemukan!</small></div>
                    </div>
                </div>
            ';
            exit;
        }
        // Ambil data dengan fallback strip (-)
        $accession_number                  = val($Data, 'accession_number');
        $description                       = val($Data, 'description');
        $timestamp                         = val($Data, 'timestamp');
        $finding                           = val($Data, 'finding');
        $study_number                      = val($Data, 'study_number');
        $viewer_link                       = val($Data, 'viewer_link');
        $study_instance_uid                = val($Data, 'study_instance_uid');
        $cardiac_silhouette                = val($Data, 'cardiac_silhouette');
        $aorta                             = val($Data, 'aorta');
        $mediastinum                       = val($Data, 'mediastinum');
        $lungs                             = val($Data, 'lungs');
        $trachea                           = val($Data, 'trachea');
        $diaphragm_and_costophrenic_angles = val($Data, 'diaphragm_and_costophrenic_angles');
        $visualized_structures             = val($Data, 'visualized_structures');
        $impression                        = val($Data, 'impression');
        $recommendation                    = val($Data, 'recommendation');
        $doctor_name                       = val($Data, 'doctor_name');
        // Tampilkan Data Tabel
        echo '
            <div class="row mb-2">
                <div class="col-5"><small><i>Accession Number</i></small></div>
                <div class="col-1"><small><i>:</i></small></div>
                <div class="col-6"><small class="text text-grayish text-long">'.$accession_number.'</small></div>
            </div>
            <div class="row mb-2">
                <div class="col-5"><small><i>Study Number</i></small></div>
                <div class="col-1"><small><i>:</i></small></div>
                <div class="col-6"><small class="text text-grayish text-long">'.$study_number.'</small></div>
            </div>
            <div class="row mb-2">
                <div class="col-5"><small><i>Timestamp</i></small></div>
                <div class="col-1"><small><i>:</i></small></div>
                <div class="col-6"><small class="text text-grayish text-long">'.$timestamp.'</small></div>
            </div>
            <div class="row mb-2">
                <div class="col-5"><small><i>Viewer Link</i></small></div>
                <div class="col-1"><small><i>:</i></small></div>
                <div class="col-6">
                    <a href="'.$viewer_link.'" target="_blank">
                        <small class="text-long">'.$viewer_link.'</small>
                    </a>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-5"><small><i>Study Instance UID</i></small></div>
                <div class="col-1"><small><i>:</i></small></div>
                <div class="col-6"><small class="text text-grayish text-long">'.$study_instance_uid.'</small></div>
            </div>
            <div class="row mb-2">
                <div class="col-5"><small>Nama Dokter</small></div>
                <div class="col-1"><small><i>:</i></small></div>
                <div class="col-6"><small class="text text-grayish text-long">'.$doctor_name.'</small></div>
            </div>
            <div class="row mb-2">
                <div class="col-5"><small><i>Description</i></small></div>
                <div class="col-1"><small><i>:</i></small></div>
                <div class="col-6"><small class="text text-grayish text-long">'.$description.'</small></div>
            </div>
            <div class="row mb-2">
                <div class="col-5"><small><i>Finding</i></small></div>
                <div class="col-1"><small><i>:</i></small></div>
                <div class="col-6"><small class="text text-grayish text-long">'.$finding.'</small></div>
            </div>
            <div class="row mb-2">
                <div class="col-5"><small><i>Impression</i></small></div>
                <div class="col-1"><small><i>:</i></small></div>
                <div class="col-6"><small class="text text-grayish text-long">'.$impression.'</small></div>
            </div>
            <div class="row mb-2">
                <div class="col-5"><small><i>Recommendation</i></small></div>
                <div class="col-1"><small><i>:</i></small></div>
                <div class="col-6"><small class="text text-grayish text-long">'.$recommendation.'</small></div>
            </div>
            <div class="row mb-2">
                <div class="col-5"><small><i>Cardiac Silhouette</i></small></div>
                <div class="col-1"><small><i>:</i></small></div>
                <div class="col-6"><small class="text text-grayish text-long">'.$cardiac_silhouette.'</small></div>
            </div>
            <div class="row mb-2">
                <div class="col-5"><small><i>Aorta</i></small></div>
                <div class="col-1"><small><i>:</i></small></div>
                <div class="col-6"><small class="text text-grayish text-long">'.$aorta.'</small></div>
            </div>
            <div class="row mb-2">
                <div class="col-5"><small><i>Mediastinum</i></small></div>
                <div class="col-1"><small><i>:</i></small></div>
                <div class="col-6"><small class="text text-grayish text-long">'.$mediastinum.'</small></div>
            </div>
            <div class="row mb-2">
                <div class="col-5"><small><i>Lungs</i></small></div>
                <div class="col-1"><small><i>:</i></small></div>
                <div class="col-6"><small class="text text-grayish text-long">'.$lungs.'</small></div>
            </div>
            <div class="row mb-2">
                <div class="col-5"><small><i>Trachea</i></small></div>
                <div class="col-1"><small><i>:</i></small></div>
                <div class="col-6"><small class="text text-grayish text-long">'.$trachea.'</small></div>
            </div>
            <div class="row mb-2">
                <div class="col-5"><small><i>Diaphragm & Costophrenic Angles</i></small></div>
                <div class="col-1"><small><i>:</i></small></div>
                <div class="col-6"><small class="text text-grayish text-long">'.$diaphragm_and_costophrenic_angles.'</small></div>
            </div>
            <div class="row mb-2">
                <div class="col-5"><small><i>Visualized structures</i></small></div>
                <div class="col-1"><small><i>:</i></small></div>
                <div class="col-6"><small class="text text-grayish text-long">'.$visualized_structures.'</small></div>
            </div>
        ';
    }else{
        $Qry = $Conn->prepare("SELECT * FROM  radiologi_expertise_usg WHERE id_radiologi_expertise_usg = ?");
        $Qry->bind_param("i", $id_radiologi_expertise);
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
        if(empty($Data['id_radiologi_expertise_usg'])){
            echo '
                <div class="row mb-3">
                    <div class="col-12 text-center">
                        <div class="alert alert-danger"><small>Data Expertise USG Tidak Ditemukan!</small></div>
                    </div>
                </div>
            ';
            exit;
        }
        
        // Ambil data dengan fallback strip (-)
        $accession_number          = val($Data, 'accession_number');
        $timestamp                 = val($Data, 'timestamp');
        $description               = val($Data, 'description');
        $finding                   = val($Data, 'finding');
        $recommendation            = val($Data, 'recommendation');
        $doctor_name               = val($Data, 'doctor_name');
        $viewer_link               = val($Data, 'viewer_link');
        $study_number              = val($Data, 'study_number');
        $study_instance_uid        = val($Data, 'study_instance_uid');
        $gestational_sac_size      = val($Data, 'gestational_sac_size');
        $crown_rump_length         = val($Data, 'crown_rump_length');
        $fetal_heart_rate          = val($Data, 'fetal_heart_rate');
        $biparietal_diameter       = val($Data, 'biparietal_diameter');
        $head_circumference        = val($Data, 'head_circumference');
        $abdominal_circumference   = val($Data, 'abdominal_circumference');
        $femur_length              = val($Data, 'femur_length');
        $single_deepest_pocket     = val($Data, 'single_deepest_pocket');
        $estimated_fetal_weight    = val($Data, 'estimated_fetal_weight');
        $fetal_position            = val($Data, 'fetal_position');
        $estimated_gestational_age = val($Data, 'estimated_gestational_age');
        $estimated_date_birth      = val($Data, 'estimated_date_birth');
        $fetal_presentation        = val($Data, 'fetal_presentation');

        // Tampilkan Data Tabel
        echo '
            <div class="row mb-2">
                <div class="col-5"><small><i>Accession Number</i></small></div>
                <div class="col-1"><small><i>:</i></small></div>
                <div class="col-6"><small class="text text-grayish text-long">'.$accession_number.'</small></div>
            </div>
            <div class="row mb-2">
                <div class="col-5"><small><i>Timestamp</i></small></div>
                <div class="col-1"><small><i>:</i></small></div>
                <div class="col-6"><small class="text text-grayish text-long">'.$timestamp.'</small></div>
            </div>
            <div class="row mb-2">
                <div class="col-5"><small><i>Viewer Link</i></small></div>
                <div class="col-1"><small><i>:</i></small></div>
                <div class="col-6">
                    <a href="'.$viewer_link.'" target="_blank">
                        <small class="text-long">'.$viewer_link.'</small>
                    </a>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-5"><small><i>Doctor Name</i></small></div>
                <div class="col-1"><small><i>:</i></small></div>
                <div class="col-6"><small class="text text-grayish text-long">'.$doctor_name.'</small></div>
            </div>
            <div class="row mb-2">
                <div class="col-5"><small><i>Study Instance Uid</i></small></div>
                <div class="col-1"><small><i>:</i></small></div>
                <div class="col-6"><small class="text text-grayish text-long">'.$study_instance_uid.'</small></div>
            </div>
            <div class="row mb-2">
                <div class="col-5"><small><i>Gestational Sac Size</i></small></div>
                <div class="col-1"><small><i>:</i></small></div>
                <div class="col-6"><small class="text text-grayish text-long">'.$gestational_sac_size.'</small></div>
            </div>
            <div class="row mb-2">
                <div class="col-5"><small><i>Crown Rump Length</i></small></div>
                <div class="col-1"><small><i>:</i></small></div>
                <div class="col-6"><small class="text text-grayish text-long">'.$crown_rump_length.'</small></div>
            </div>
            <div class="row mb-2">
                <div class="col-5"><small><i>Fetal Heart Rate</i></small></div>
                <div class="col-1"><small><i>:</i></small></div>
                <div class="col-6"><small class="text text-grayish text-long">'.$fetal_heart_rate.'</small></div>
            </div>
            <div class="row mb-2">
                <div class="col-5"><small><i>Biparietal Diameter</i></small></div>
                <div class="col-1"><small><i>:</i></small></div>
                <div class="col-6"><small class="text text-grayish text-long">'.$biparietal_diameter.'</small></div>
            </div>
            <div class="row mb-2">
                <div class="col-5"><small><i>Head Circumference</i></small></div>
                <div class="col-1"><small><i>:</i></small></div>
                <div class="col-6"><small class="text text-grayish text-long">'.$head_circumference.'</small></div>
            </div>
            <div class="row mb-2">
                <div class="col-5"><small><i>Abdominal Circumference</i></small></div>
                <div class="col-1"><small><i>:</i></small></div>
                <div class="col-6"><small class="text text-grayish text-long">'.$abdominal_circumference.'</small></div>
            </div>
            <div class="row mb-2">
                <div class="col-5"><small><i>Femur Length</i></small></div>
                <div class="col-1"><small><i>:</i></small></div>
                <div class="col-6"><small class="text text-grayish text-long">'.$femur_length.'</small></div>
            </div>
            <div class="row mb-2">
                <div class="col-5"><small><i>Single Deepest Pocket</i></small></div>
                <div class="col-1"><small><i>:</i></small></div>
                <div class="col-6"><small class="text text-grayish text-long">'.$single_deepest_pocket.'</small></div>
            </div>
            <div class="row mb-2">
                <div class="col-5"><small><i>Estimated Fetal Weight</i></small></div>
                <div class="col-1"><small><i>:</i></small></div>
                <div class="col-6"><small class="text text-grayish text-long">'.$estimated_fetal_weight.'</small></div>
            </div>
            <div class="row mb-2">
                <div class="col-5"><small><i>Fetal Position</i></small></div>
                <div class="col-1"><small><i>:</i></small></div>
                <div class="col-6"><small class="text text-grayish text-long">'.$fetal_position.'</small></div>
            </div>
            <div class="row mb-2">
                <div class="col-5"><small><i>Estimated Gestational Age</i></small></div>
                <div class="col-1"><small><i>:</i></small></div>
                <div class="col-6"><small class="text text-grayish text-long">'.$estimated_gestational_age.'</small></div>
            </div>
            <div class="row mb-2">
                <div class="col-5"><small><i>Estimated Date Birth</i></small></div>
                <div class="col-1"><small><i>:</i></small></div>
                <div class="col-6"><small class="text text-grayish text-long">'.$estimated_date_birth.'</small></div>
            </div>
            <div class="row mb-2">
                <div class="col-5"><small><i>Fetal Presentation</i></small></div>
                <div class="col-1"><small><i>:</i></small></div>
                <div class="col-6"><small class="text text-grayish text-long">'.$fetal_presentation.'</small></div>
            </div>
        ';
    }
    echo '
        <input type="hidden" name="data" value="Expertise">
        <input type="hidden" name="modality" value="'.$modality.'">
        <input type="hidden" name="id" value="'.$id_radiologi_expertise.'">
        <input type="hidden" name="acn" value="'.$accession_number.'">
    ';
?>
