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
    if($modality =="XR"){
        $Qry = $Conn->prepare("SELECT * FROM  radiologi_expertise WHERE id_radiologi_expertise = ?");
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
        
        // Tampilkan Data Tabel
        echo '
            <div class="table table-responsive">
                <table class="table table-bordered table-sm">
                    <tbody>
                        <tr>
                            <td><i>Accession Number</i></td>
                            <td class="text text-grayish">'.$Data['accession_number'].'</td>
                        </tr>
                        <tr>
                            <td><i>Description</i></td>
                            <td class="text text-grayish">'.$Data['description'].'</td>
                        </tr>
                        <tr>
                            <td><i>Timestamp</i></td>
                            <td class="text text-grayish">'.$Data['timestamp'].'</td>
                        </tr>
                        <tr>
                            <td><i>Finding</i></td>
                            <td class="text text-grayish">'.$Data['finding'].'</td>
                        </tr>
                        <tr>
                            <td><i>Study Number</i></td>
                            <td class="text text-grayish">'.$Data['study_number'].'</td>
                        </tr>
                        <tr>
                            <td><i>Viewer Link</i></td>
                            <td class="text text-grayish">'.$Data['viewer_link'].'</td>
                        </tr>
                        <tr>
                            <td><i>Stdy Instance UID</i></td>
                            <td class="text text-grayish">'.$Data['study_instance_uid'].'</td>
                        </tr>
                        <tr>
                            <td><i>Cardiac Silhouette</i></td>
                            <td class="text text-grayish">'.$Data['cardiac_silhouette'].'</td>
                        </tr>
                        <tr>
                            <td><i>Aorta</i></td>
                            <td class="text text-grayish">'.$Data['aorta'].'</td>
                        </tr>
                        <tr>
                            <td><i>Mediastinum</i></td>
                            <td class="text text-grayish">'.$Data['mediastinum'].'</td>
                        </tr>
                        <tr>
                            <td><i>Lungs</i></td>
                            <td class="text text-grayish">'.$Data['lungs'].'</td>
                        </tr>
                        <tr>
                            <td><i>Trachea</i></td>
                            <td class="text text-grayish">'.$Data['trachea'].'</td>
                        </tr>
                        <tr>
                            <td><i>Diaphragm & costophrenic angles</i></td>
                            <td class="text text-grayish">'.$Data['diaphragm_and_costophrenic_angles'].'</td>
                        </tr>
                        <tr>
                            <td><i>Visualized structures</i></td>
                            <td class="text text-grayish">'.$Data['visualized_structures'].'</td>
                        </tr>
                        <tr>
                            <td><i>Impression</i></td>
                            <td class="text text-grayish">'.$Data['impression'].'</td>
                        </tr>
                        <tr>
                            <td><i>Recommendation</i></td>
                            <td class="text text-grayish">'.$Data['recommendation'].'</td>
                        </tr>
                        <tr>
                            <td><i>Nama Dokter</i></td>
                            <td class="text text-grayish">'.$Data['doctor_name'].'</td>
                        </tr>
                    </tbody>
                </table>
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
        
        // Tampilkan Data Tabel
        echo '
            <div class="table table-responsive">
                <table class="table table-bordered table-sm">
                    <tbody>
                        <tr>
                            <td><i>Accession Number</i></td>
                            <td class="text text-grayish">'.$Data['accession_number'].'</td>
                        </tr>
                        <tr>
                            <td><i>Description</i></td>
                            <td class="text text-grayish">'.$Data['description'].'</td>
                        </tr>
                        <tr>
                            <td><i>Timestamp</i></td>
                            <td class="text text-grayish">'.$Data['timestamp'].'</td>
                        </tr>
                        <tr>
                            <td><i>Finding</i></td>
                            <td class="text text-grayish">'.$Data['finding'].'</td>
                        </tr>
                        <tr>
                            <td><i>Study Number</i></td>
                            <td class="text text-grayish">'.$Data['study_number'].'</td>
                        </tr>
                        <tr>
                            <td><i>Imaging Study UUID</i></td>
                            <td class="text text-grayish">
                                <code class="text text-grayish">'.$Data['imaging_study_uuid'].'</code>
                            </td>
                        </tr>
                        <tr>
                            <td><i>Viewer Link</i></td>
                            <td class="text text-grayish">
                                <a href="'.$Data['viewer_link'].'" target="_blank">Lihat Link</a>
                            </td>
                        </tr>
                        <tr>
                            <td><i>Stdy Instance UID</i></td>
                            <td class="text text-grayish">'.$Data['study_instance_uid'].'</td>
                        </tr>
                        <tr>
                            <td><i>Sestational Sac Size</i></td>
                            <td class="text text-grayish">'.$Data['gestational_sac_size'].'</td>
                        </tr>
                        <tr>
                            <td><i>Crown Rump Length</i></td>
                            <td class="text text-grayish">'.$Data['crown_rump_length'].'</td>
                        </tr>
                        <tr>
                            <td><i>Fetal Heart Rate</i></td>
                            <td class="text text-grayish">'.$Data['fetal_heart_rate'].'</td>
                        </tr>
                        <tr>
                            <td><i>Biparietal Diameter</i></td>
                            <td class="text text-grayish">'.$Data['biparietal_diameter'].'</td>
                        </tr>
                        <tr>
                            <td><i>Head Circumference</i></td>
                            <td class="text text-grayish">'.$Data['head_circumference'].'</td>
                        </tr>
                        <tr>
                            <td><i>Abdominal Circumference</i></td>
                            <td class="text text-grayish">'.$Data['abdominal_circumference'].'</td>
                        </tr>
                        <tr>
                            <td><i>Femur Length</i></td>
                            <td class="text text-grayish">'.$Data['femur_length'].'</td>
                        </tr>
                        <tr>
                            <td><i>Single Deepest Pocket</i></td>
                            <td class="text text-grayish">'.$Data['single_deepest_pocket'].'</td>
                        </tr>
                        <tr>
                            <td><i>Estimated Fetal Weight</i></td>
                            <td class="text text-grayish">'.$Data['estimated_fetal_weight'].'</td>
                        </tr>
                        <tr>
                            <td><i>Fetal Position</i></td>
                            <td class="text text-grayish">'.$Data['fetal_position'].'</td>
                        </tr>
                        <tr>
                            <td><i>Estimated Gestational Age</i></td>
                            <td class="text text-grayish">'.$Data['estimated_gestational_age'].'</td>
                        </tr>
                        <tr>
                            <td><i>Estimated Date Birth</i></td>
                            <td class="text text-grayish">'.$Data['estimated_date_birth'].'</td>
                        </tr>
                        <tr>
                            <td><i>Fetal Presentation</i></td>
                            <td class="text text-grayish">'.$Data['fetal_presentation'].'</td>
                        </tr>
                        <tr>
                            <td><i>Recommendation</i></td>
                            <td class="text text-grayish">'.$Data['recommendation'].'</td>
                        </tr>
                        <tr>
                            <td><i>Nama Dokter</i></td>
                            <td class="text text-grayish">'.$Data['doctor_name'].'</td>
                        </tr>
                        
                    </tbody>
                </table>
            </div>
        ';
    }
?>
