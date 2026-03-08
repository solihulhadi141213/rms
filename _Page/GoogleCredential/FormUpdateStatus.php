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
        
        // Routing Status Baru
        if($status==1){
            $status_baru = 0;
        }else{
            $status_baru = 1;
        }

        // Routing Status
        if($status==0){
            $label_status = '<span class="badge bg-dark">Inactive</span>';
        }else{
            $label_status = '<span class="badge bg-success">Active</span>';
        }


        //Tampilkan Data
        echo '
            <input type="hidden" name="id_google_credential" value="'.$id_google_credential.'">
            <input type="hidden" name="status_baru" value="'.$status_baru.'">
        ';
        echo '
            <div class="row mb-2">
                <div class="col-4"><small>Nama Environment</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$credential_env.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small><i>Client ID</i></small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">
                        <code class="text text-grayish">'.$client_id.'</code>
                    </small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small><i>Client Secret</i></small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text-long">
                        <code class="text text-grayish">'.$client_secret.'</code>
                    </small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small><i>Status</i></small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$label_status.'</small>
                </div>
            </div>
        ';
        if($status==1){
            echo '
                <div class="row mb-3">
                    <div class="col-md-12 text-center">
                        <div class="alert alert-danger">Apakah Anda Yakin Akan Non aktifkan pengaturan google credential ini?</div>
                    </div>
                </div>
            ';
        }else{
            echo '
                <div class="row mb-3">
                    <div class="col-md-12">
                    <div class="alert alert-warning text-center">Apakah Anda Yakin Akan Mengaktifkan pengaturan google credential ini?</div>
                    </div>
                </div>
            ';
        }
    }
?>