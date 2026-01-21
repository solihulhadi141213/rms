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
            <div class="row mb-3">
                <div class="col-12 text-center">
                    <div class="alert alert-danger"><small>Sesi Akses Sudah Berakhir! Silahkan Login Ulang.</small></div>
                </div>
            </div>
        ';
        exit;
    }

    //id_connection_orthanc wajib terisi
    if(empty($_POST['id_connection_orthanc'])){
        echo '
            <div class="row mb-3">
                <div class="col-12 text-center">
                    <div class="alert alert-danger"><small>ID Koneksi Boleh Kosong!</small></div>
                </div>
            </div>
        ';
        exit;
    }

    //Buat variabel 'id_connection_orthanc' dan sanitasi
    $id_connection_orthanc = validateAndSanitizeInput($_POST['id_connection_orthanc']);

    //Buka Detail Koneksi Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM connection_orthanc WHERE id_connection_orthanc = ?");
    $Qry->bind_param("i", $id_connection_orthanc);
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

    //Buat Variabel
    $id_connection_orthanc       = $Data['id_connection_orthanc'];
    $name_connection_orthanc     = $Data['name_connection_orthanc'];
    $url_connection_orthanc      = $Data['url_connection_orthanc'];
    $username_connection_orthanc = $Data['username_connection_orthanc'];
    $password_connection_orthanc = $Data['password_connection_orthanc'];
    $status_connection_orthanc   = $Data['status_connection_orthanc'];

    //Routing Status
    if(empty($status_connection_orthanc)){
        $label_status1 = 'selected';
        $label_status2 = '';
    }else{
        $label_status1 = '';
        $label_status2 = 'selected';
    }
    
    echo '
        <input type="hidden" name="id_connection_orthanc" value="'.$id_connection_orthanc.'">
        <div class="row mb-3">
            <div class="col-md-12">
                <label for="name_connection_orthanc_edit">
                    <small>Nama Koneksi</small>
                </label>
                <input type="text" class="form-control" name="name_connection_orthanc" id="name_connection_orthanc_edit" value="'.$name_connection_orthanc.'" required>
                <small>
                    <small class="text text-muted">
                        Example : Development, Staging, Production  dll.
                    </small>
                </small>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-12">
                <label for="url_connection_orthanc_edit">
                    <small>URL Orthanc</small>
                </label>
                <input type="url" class="form-control" name="url_connection_orthanc" id="url_connection_orthanc_edit" placeholder="https://" value="'.$url_connection_orthanc.'" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-12">
                <label for="username_connection_orthanc_edit">
                    <small>Username Orthanc</small>
                </label>
                <input type="text" class="form-control" name="username_connection_orthanc" id="username_connection_orthanc_edit" value="'.$username_connection_orthanc.'" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-12">
                <label for="password_connection_orthanc_edit">
                    <small>Password Orthanc</small>
                </label>
                <input type="text" class="form-control" name="password_connection_orthanc" id="password_connection_orthanc_edit" value="'.$password_connection_orthanc.'" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-12">
                <label for="status_connection_orthanc_edit">
                    <small>Status Connetion</small>
                </label>
                <select name="status_connection_orthanc" id="status_connection_orthanc_edit" class="form-control">
                    <option '.$label_status1.' value="0">Inactive</option>
                    <option '.$label_status2.' value="1">Active</option>
                </select>
            </div>
        </div>
    ';
?>