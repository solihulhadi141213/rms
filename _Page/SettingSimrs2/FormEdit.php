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

    //id_connection_simrs_old wajib terisi
    if(empty($_POST['id_connection_simrs_old'])){
        echo '
            <div class="row mb-3">
                <div class="col-12 text-center">
                    <div class="alert alert-danger"><small>Koneksi SIMRS Tiidak Boleh Kosong!</small></div>
                </div>
            </div>
        ';
        exit;
    }

    //Buat variabel 'id_connection_simrs_old' dan sanitasi
    $id_connection_simrs_old      = validateAndSanitizeInput($_POST['id_connection_simrs_old']);

    //Buka Detail Koneksi Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM connection_simrs_old WHERE id_connection_simrs_old = ?");
    $Qry->bind_param("i", $id_connection_simrs_old);
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
        $id_connection_simrs_old = $Data['id_connection_simrs_old'];
        $name_connection         = $Data['name_connection'];
        $base_url                = $Data['base_url'];
        $username                = $Data['username'];
        $status_connection       = $Data['status_connection'];

        //Routing Status
        if(empty($status_connection)){
            $label_status1 = 'selected';
            $label_status2 = '';
        }else{
            $label_status1 = '';
            $label_status2 = 'selected';
        }

        // Tampilkan Form Edit
        if(empty($Data['id_connection_simrs_old'])){
            echo '
                <div class="alert alert-danger">
                    <small>Data Tidak Ditemukan</small>
                </div>
            '; 
        }else{
            echo '
                <input type="hidden" name="id_connection_simrs_old" value="'.$id_connection_simrs_old.'">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="name_connection_edit">
                            <small>Nama Koneksi</small>
                        </label>
                        <input type="text" class="form-control" name="name_connection" id="name_connection_edit" value="'.$name_connection.'" required>
                        <small>
                            <small class="text text-muted">
                                Example : Development, Staging, Production  dll.
                            </small>
                        </small>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="base_url_edit">
                            <small>URL SIMRS</small>
                        </label>
                        <input type="url" class="form-control" name="base_url" id="base_url_edit" placeholder="https://" value="'.$base_url.'" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="username_edit">
                            <small>Username</small>
                        </label>
                        <input type="text" class="form-control" name="username" id="username_edit" value="'.$username.'" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="password_edit">
                            <small>Password</small>
                        </label>
                        <input type="text" class="form-control" name="password" id="password_edit" value="'.$password.'" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="status_connection_edit">
                            <small>Status Connection</small>
                        </label>
                        <select name="status_connection" id="status_connection_edit" class="form-control">
                            <option '.$label_status1.' value="0">Inactive</option>
                            <option '.$label_status2.' value="1">Active</option>
                        </select>
                    </div>
                </div>
            ';

        }
    }
?>