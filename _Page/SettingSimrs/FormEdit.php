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

    //id_connection_simrs wajib terisi
    if(empty($_POST['id_connection_simrs'])){
        echo '
            <div class="row mb-3">
                <div class="col-12 text-center">
                    <div class="alert alert-danger"><small>Koneksi SIMRS Tiidak Boleh Kosong!</small></div>
                </div>
            </div>
        ';
        exit;
    }

    //Buat variabel 'id_connection_simrs' dan sanitasi
    $id_connection_simrs      = validateAndSanitizeInput($_POST['id_connection_simrs']);

    //Buka Detail Koneksi Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM connection_simrs WHERE id_connection_simrs = ?");
    $Qry->bind_param("i", $id_connection_simrs);
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
        $id_connection_simrs     = $Data['id_connection_simrs'];
        $name_connection_simrs   = $Data['name_connection_simrs'];
        $url_connection_simrs    = $Data['url_connection_simrs'];
        $client_id               = $Data['client_id'];
        $client_key              = $Data['client_key'];
        $status_connection_simrs = $Data['status_connection_simrs'];

        //Routing Status
        if(empty($status_connection_simrs)){
            $label_status1 = 'selected';
            $label_status2 = '';
        }else{
            $label_status1 = '';
            $label_status2 = 'selected';
        }

        // Tampilkan Form Edit
        if(empty($Data['id_connection_simrs'])){
            echo '
                <div class="alert alert-danger">
                    <small>Data Tidak Ditemukan</small>
                </div>
            '; 
        }else{
            echo '
                <input type="hidden" name="id_connection_simrs" value="'.$id_connection_simrs.'">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="name_connection_simrs_edit">
                            <small>Nama Koneksi</small>
                        </label>
                        <input type="text" class="form-control" name="name_connection_simrs" id="name_connection_simrs_edit" value="'.$name_connection_simrs.'" required>
                        <small>
                            <small class="text text-muted">
                                Example : Development, Staging, Production  dll.
                            </small>
                        </small>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="url_connection_simrs_edit">
                            <small>URL SIMRS</small>
                        </label>
                        <input type="url" class="form-control" name="url_connection_simrs" id="url_connection_simrs_edit" placeholder="https://" value="'.$url_connection_simrs.'" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="client_id_edit">
                            <small>Client ID</small>
                        </label>
                        <input type="text" class="form-control" name="client_id" id="client_id_edit" value="'.$client_id.'" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="client_key_edit">
                            <small>Client Key</small>
                        </label>
                        <input type="text" class="form-control" name="client_key" id="client_key_edit" value="'.$client_key.'" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="status_connection_simrs_edit">
                            <small>Client Key</small>
                        </label>
                        <select name="status_connection_simrs" id="status_connection_simrs_edit" class="form-control">
                            <option '.$label_status1.' value="0">Inactive</option>
                            <option '.$label_status2.' value="1">Active</option>
                        </select>
                    </div>
                </div>
            ';

        }
    }
?>