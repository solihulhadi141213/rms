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

    //id_connection_pacs wajib terisi
    if(empty($_POST['id_connection_pacs'])){
        echo '
            <div class="row mb-3">
                <div class="col-12 text-center">
                    <div class="alert alert-danger"><small>Koneksi PACS Tiidak Boleh Kosong!</small></div>
                </div>
            </div>
        ';
        exit;
    }

    //Buat variabel 'id_connection_pacs' dan sanitasi
    $id_connection_pacs      = validateAndSanitizeInput($_POST['id_connection_pacs']);

    //Buka Detail Koneksi Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM connection_pacs WHERE id_connection_pacs = ?");
    $Qry->bind_param("i", $id_connection_pacs);
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
        $id_connection_pacs       = $Data['id_connection_pacs'];
        $name_connection_pacs     = $Data['name_connection_pacs'];
        $url_connection_pacs      = $Data['url_connection_pacs'];
        $username_connection_pacs = $Data['username_connection_pacs'];
        $password_connection_pacs = $Data['password_connection_pacs'];
        $status_connection_pacs   = $Data['status_connection_pacs'];

        //Routing Status
        if(empty($status_connection_pacs)){
            $label_status1 = 'selected';
            $label_status2 = '';
        }else{
             $label_status1 = '';
            $label_status2 = 'selected';
        }

        // Tampilkan Data Detail
        if(empty($Data['id_connection_pacs'])){
            echo '
                <div class="alert alert-danger">
                    <small>Data Tidak Ditemukan</small>
                </div>
            '; 
        }else{
            echo '
                <input type="hidden" name="id_connection_pacs" value="'.$id_connection_pacs.'">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="name_connection_pacs_edit">
                            <small>Nama Koneksi</small>
                        </label>
                        <input type="text" class="form-control" name="name_connection_pacs" id="name_connection_pacs_edit" value="'.$name_connection_pacs.'" required>
                        <small>
                            <small class="text text-muted">
                                Example : Development, Staging, Production  dll.
                            </small>
                        </small>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="url_connection_pacs_edit">
                            <small>URL SIMRS</small>
                        </label>
                        <input type="url" class="form-control" name="url_connection_pacs" id="url_connection_pacs_edit" placeholder="https://" value="'.$url_connection_pacs.'" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="username_connection_pacs_edit">
                            <small>Username</small>
                        </label>
                        <input type="text" class="form-control" name="username_connection_pacs" id="username_connection_pacs_edit" value="'.$username_connection_pacs.'" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="password_connection_pacs_edit">
                            <small>Password</small>
                        </label>
                        <input type="text" class="form-control" name="password_connection_pacs" id="password_connection_pacs_edit" value="'.$password_connection_pacs.'" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="status_connection_pacs_edit">
                            <small>Status Connection</small>
                        </label>
                        <select name="status_connection_pacs" id="status_connection_pacs_edit" class="form-control">
                            <option '.$label_status1.' value="0">Inactive</option>
                            <option '.$label_status2.' value="1">Active</option>
                        </select>
                    </div>
                </div>
            ';

        }
    }
?>