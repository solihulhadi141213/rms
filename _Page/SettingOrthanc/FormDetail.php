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
    }else{
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
            $label_status = '<span class="badge bg-danger"><i class="bi bi-x-circle"></i> Inactive</span>';
        }else{
            $label_status = '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Active</span>';
        }

        // Tampilkan Data Detail
        if(empty($Data['id_connection_orthanc'])){
            echo '
                <div class="alert alert-danger">
                    <small>Data Tidak Ditemukan</small>
                </div>
            '; 
        }else{
            echo '
                <div class="row mb-2">
                    <div class="col-4"><small>Nama Koneksi</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-7">
                        <small class="text text-grayish">'.$name_connection_orthanc.'</small>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-4"><small>Base URL</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-7">
                        <small class="text text-grayish">'.$url_connection_orthanc.'</small>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-4"><small>Username</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-7">
                        <small class="text text-grayish">'.$username_connection_orthanc.'</small>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-4"><small>Password</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-7">
                        <small class="text text-grayish">'.$password_connection_orthanc.'</small>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-4"><small>Status Koneksi</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-7">
                        <small class="text text-grayish">'.$label_status.'</small>
                    </div>
                </div>
            ';

        }
    }
?>