<?php
    //koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";
    
    //Zona Waktu
    date_default_timezone_set("Asia/Jakarta");

    //Session Akses
    if(empty($SessionIdAccess)){
        echo '
            <div class="alert alert-danger text-center">
                <small>Sesi Akses Sudah Berakhir! Silahkan Login Ulang.</small>
            </div>
        ';
        exit;
    }

    //id_google_credential wajib terisi
    if(empty($_POST['id_google_credential'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>ID Google Credential Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }

    //Buat variabel 'id_google_credential' dan sanitasi
    $id_google_credential = validateAndSanitizeInput($_POST['id_google_credential']);

    //Buka Detail Koneksi Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM google_credential WHERE id_google_credential = ?");
    $Qry->bind_param("i", $id_google_credential);
    if (!$Qry->execute()) {
        $error=$Conn->error;
        echo '
            <div class="alert alert-danger text-center">
                <small>Terjadi kesalahan pada saat membuka data dari database!<br>Keterangan : '.$error.'</small>
            </div>
        ';
    }else{
        $Result = $Qry->get_result();
        $Data = $Result->fetch_assoc();
        $Qry->close();

        // Buat Variabel
        $credential_env = $Data['credential_env'];
        $client_id      = $Data['client_id'];
        $client_secret  = $Data['client_secret'];
        $status         = $Data['status'];
        
        // Routing Status
        $Select1 = '';
        $Select2 = '';
        $Select3 = '';
        if($credential_env=="Development"){
            $Select1 = 'selected';
            $Select2 = '';
            $Select3 = '';
        }
        if($credential_env=="Staging"){
            $Select1 = '';
            $Select2 = 'selected';
            $Select3 = '';
        }
        if($credential_env=="Production"){
            $Select1 = '';
            $Select2 = '';
            $Select3 = 'selected';
        }
        //Tampilkan Data
        echo '
            <input type="hidden" name="id_google_credential" value="'.$id_google_credential.'">
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="credential_env_edit">Nama <i>Environment</i></label>
                    <select name="credential_env" id="credential_env_edit" class="form-control">
                        <option '.$Select1.' value="Development">Development</option>
                        <option '.$Select2.' value="Staging">Staging</option>
                        <option '.$Select3.' value="Production">Production</option>
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="client_id_edit"><i>Client ID</i></label>
                    <textarea name="client_id" id="client_id_edit" class="form-control">'.$client_id.'</textarea>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="client_secret_edit"><i>Client Secret</i></label>
                    <textarea name="client_secret" id="client_secret_edit" class="form-control">'.$client_secret.'</textarea>
                </div>
            </div>
        ';
    }
?>