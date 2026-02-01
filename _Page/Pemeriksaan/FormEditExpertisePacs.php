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
    if($modality !=="US"){
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
        
        // Tampilkan Form
        echo '
            <input type="hidden" name="id_radiologi_expertise" value="'.$Data['id_radiologi_expertise'].'">
            <input type="hidden" name="modality" value="'.$modality.'">
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="doctor_name"><small><i>Doctor Name</i></small></label>
                    <input type="text" name="doctor_name" class="form-control" value="'.$Data['doctor_name'].'">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="description"><small><i>Description</i></small></label>
                    <textarea name="description" id="description" class="form-control">'.$Data['description'].'</textarea>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="finding"><small><i>Finding</i></small></label>
                    <textarea name="finding" id="finding" class="form-control">'.$Data['finding'].'</textarea>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="impression"><small><i>Impression</i></small></label>
                    <textarea name="impression" id="impression" class="form-control">'.$Data['impression'].'</textarea>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="recommendation"><small><i>Recommendation</i></small></label>
                    <textarea name="recommendation" id="recommendation" class="form-control">'.$Data['recommendation'].'</textarea>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="cardiac_silhouette"><small><i>Cardiac Silhouette</i></small></label>
                    <textarea name="cardiac_silhouette" id="cardiac_silhouette" class="form-control">'.$Data['cardiac_silhouette'].'</textarea>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="aorta"><small><i>Aorta</i></small></label>
                    <textarea name="aorta" id="aorta" class="form-control">'.$Data['aorta'].'</textarea>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="mediastinum"><small><i>Mediastinum</i></small></label>
                    <textarea name="mediastinum" id="mediastinum" class="form-control">'.$Data['mediastinum'].'</textarea>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="lungs"><small><i>Lungs</i></small></label>
                    <textarea name="lungs" id="lungs" class="form-control">'.$Data['lungs'].'</textarea>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="trachea"><small><i>Trachea</i></small></label>
                    <textarea name="trachea" id="trachea" class="form-control">'.$Data['trachea'].'</textarea>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="diaphragm_and_costophrenic_angles"><small><i>Diaphragm & Costophrenic Angles</i></small></label>
                    <textarea name="diaphragm_and_costophrenic_angles" id="diaphragm_and_costophrenic_angles" class="form-control">'.$Data['diaphragm_and_costophrenic_angles'].'</textarea>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="visualized_structures"><small><i>Visualized Structures</i></small></label>
                    <textarea name="visualized_structures" id="visualized_structures" class="form-control">'.$Data['visualized_structures'].'</textarea>
                </div>
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
        
        // Tampilkan Form
        echo '
            <input type="hidden" name="id_radiologi_expertise" value="'.$Data['id_radiologi_expertise_usg'].'">
            <input type="hidden" name="modality" value="'.$modality.'">
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="doctor_name"><small><i>Doctor Name</i></small></label>
                    <input type="text" name="doctor_name" class="form-control" value="'.$Data['doctor_name'].'">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="description"><small><i>Description</i></small></label>
                    <textarea name="description" id="description" class="form-control">'.$Data['description'].'</textarea>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="finding"><small><i>Finding</i></small></label>
                    <textarea name="finding" id="finding" class="form-control">'.$Data['finding'].'</textarea>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="recommendation"><small><i>Recommendation</i></small></label>
                    <textarea name="recommendation" id="recommendation" class="form-control">'.$Data['recommendation'].'</textarea>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="gestational_sac_size"><small><i>Gestational Sac Size</i></small></label>
                    <textarea name="gestational_sac_size" id="gestational_sac_size" class="form-control">'.$Data['gestational_sac_size'].'</textarea>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="crown_rump_length"><small><i>Crown Rump Length</i></small></label>
                    <textarea name="crown_rump_length" id="crown_rump_length" class="form-control">'.$Data['crown_rump_length'].'</textarea>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="fetal_heart_rate"><small><i>Fetal Heart Rate</i></small></label>
                    <textarea name="fetal_heart_rate" id="fetal_heart_rate" class="form-control">'.$Data['fetal_heart_rate'].'</textarea>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="biparietal_diameter"><small><i>Biparietal Diameter</i></small></label>
                    <textarea name="biparietal_diameter" id="biparietal_diameter" class="form-control">'.$Data['biparietal_diameter'].'</textarea>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="head_circumference"><small><i>Head Circumference</i></small></label>
                    <textarea name="head_circumference" id="head_circumference" class="form-control">'.$Data['head_circumference'].'</textarea>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="abdominal_circumference"><small><i>Abdominal Circumference</i></small></label>
                    <textarea name="abdominal_circumference" id="abdominal_circumference" class="form-control">'.$Data['abdominal_circumference'].'</textarea>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="femur_length"><small><i>Femur Length</i></small></label>
                    <textarea name="femur_length" id="femur_length" class="form-control">'.$Data['femur_length'].'</textarea>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="single_deepest_pocket"><small><i>Single Deepest Pocket</i></small></label>
                    <textarea name="single_deepest_pocket" id="single_deepest_pocket" class="form-control">'.$Data['single_deepest_pocket'].'</textarea>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="estimated_fetal_weight"><small><i>Estimated Fetal Weight</i></small></label>
                    <textarea name="estimated_fetal_weight" id="estimated_fetal_weight" class="form-control">'.$Data['estimated_fetal_weight'].'</textarea>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="fetal_position"><small><i>Fetal Position</i></small></label>
                    <textarea name="fetal_position" id="fetal_position" class="form-control">'.$Data['fetal_position'].'</textarea>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="estimated_gestational_age"><small><i>Estimated Gestational Age</i></small></label>
                    <textarea name="estimated_gestational_age" id="estimated_gestational_age" class="form-control">'.$Data['estimated_gestational_age'].'</textarea>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="estimated_date_birth"><small><i>Estimated Date Birth</i></small></label>
                    <textarea name="estimated_date_birth" id="estimated_date_birth" class="form-control">'.$Data['estimated_date_birth'].'</textarea>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="fetal_presentation"><small><i>Fetal Presentation</i></small></label>
                    <textarea name="fetal_presentation" id="fetal_presentation" class="form-control">'.$Data['fetal_presentation'].'</textarea>
                </div>
            </div>
        ';
    }
?>
