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
    $Qry = $Conn->prepare("SELECT * FROM connection_simrs_old  WHERE id_connection_simrs_old = ?");
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
        $password                = $Data['password'];
        $token                   = $Data['token'];
        $creat_at                = $Data['creat_at'];
        $expired_at              = $Data['expired_at'];
        $status_connection       = $Data['status_connection'];

        //Routing Status
        if(empty($status_connection)){
            $label_status = '<span class="badge bg-danger"><i class="bi bi-x-circle"></i> Inactive</span>';
        }else{
            $label_status = '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Active</span>';
        }

        // Tampilkan Data Detail
        if(empty($Data['id_connection_simrs_old'])){
            echo '
                <div class="alert alert-danger">
                    <small>Data Tidak Ditemukan</small>
                </div>
            '; 
        }else{
            echo '
                <div class="row mb-2">
                    <div class="col-4"><small>URL SIMRS</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-7">
                         <small class="text text-grayish text-long"> '.$base_url.'</small>
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-4"><small>Nama Koneksi</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-7">
                        <small class="text text-grayish">'.$name_connection.'</small>
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-4"><small>Username</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-7">
                        <small class="text text-grayish text-long">
                            '.$username.'
                        </small>
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-4"><small>Password</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-7">
                        <small class="text text-grayish text-long">
                            '.$password.'
                        </small>
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