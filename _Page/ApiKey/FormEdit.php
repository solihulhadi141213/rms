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

    //id_api_account wajib terisi
    if(empty($_POST['id_api_account'])){
        echo '
            <div class="row mb-3">
                <div class="col-12 text-center">
                    <div class="alert alert-danger"><small>ID API Key Tiidak Boleh Kosong!</small></div>
                </div>
            </div>
        ';
        exit;
    }

    //Buat variabel 'id_api_account' dan sanitasi
    $id_api_account      = validateAndSanitizeInput($_POST['id_api_account']);

    //Buka Detail Koneksi Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM api_account WHERE id_api_account = ?");
    $Qry->bind_param("i", $id_api_account);
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
        
        //Jika Data Tidak Ditemukan
        if(empty($Data['id_api_account'])){
            echo '
                <div class="alert alert-danger">
                    <small>Data Tidak Ditemukan</small>
                </div>
            '; 
        }else{

            //Buat Variabel
            $id_api_account   = $Data['id_api_account'];
            $api_name         = $Data['api_name'];
            $base_url_api     = $Data['base_url_api'];
            $username         = $Data['username'];
            $password         = $Data['password'];
            $created_at       = $Data['created_at'];
            $duration_expired = $Data['duration_expired'];

            // ===============================
            // KONVERSI DURATION MILISECOND
            // ===============================
            if ($duration_expired >= 86400000) {
                // >= 1 hari
                $durasi_tampil = round($duration_expired / 86400000);
                $satuan_1      = '';
                $satuan_2      = '';
                $satuan_3      = 'selected';
            } elseif ($duration_expired >= 3600000) {
                // >= 1 jam
                $durasi_tampil = round($duration_expired / 3600000);
                $satuan_1      = '';
                $satuan_2      = 'selected';
                $satuan_3      = '';
            } else {
                // < 1 jam
                $durasi_tampil = round($duration_expired / 60000);
                $satuan_1      = 'selected';
                $satuan_2      = '';
                $satuan_3      = '';
            }
            
            // Tampilkan Data Detail
            echo '
                <input type="hidden" name="id_api_account" value="'.$id_api_account.'">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="api_name_edit">
                            <small>Nama API</small>
                        </label>
                        <input type="text" class="form-control" name="api_name" id="api_name_edit" value="'.$api_name.'" required>
                        <small>
                            <small class="text text-muted">
                                Example : Development, Staging, Production  dll.
                            </small>
                        </small>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="base_url_api_edit">
                            <small>Base URL</small>
                        </label>
                        <input type="url" class="form-control" name="base_url_api" id="base_url_api_edit" value="'.$base_url_api.'" placeholder="https://" required>
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
                        <label for="duration_expired_edit">
                            <small>Duration Expired</small>
                        </label>
                        <div class="input-group">
                            <input type="number" min="0" step="1" class="form-control" name="duration_expired" id="duration_expired_edit"  value="'.$durasi_tampil.'" required>
                            <select name="satuan_duration" id="satuan_duration_edit" class="form-control">
                                <option '.$satuan_1.' value="menit">Menit</option>
                                <option '.$satuan_2.' value="jam">Jam</option>
                                <option '.$satuan_3.' value="hari">Hari</option>
                            </select>
                        </div>
                    </div>
                </div>
            ';

        }
    }
?>