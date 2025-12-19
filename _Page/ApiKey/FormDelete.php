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
                $durasi_tampil = round($duration_expired / 86400000) . ' Hari';
            } elseif ($duration_expired >= 3600000) {
                // >= 1 jam
                $durasi_tampil = round($duration_expired / 3600000) . ' Jam';
            } else {
                // < 1 jam
                $durasi_tampil = round($duration_expired / 60000) . ' Menit';
            }
            
            // Tampilkan Data Detail
            echo '
                <input type="hidden" name="id_api_account" value="'.$id_api_account.'">
                <div class="row mb-2">
                    <div class="col-4"><small>API Name</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-7">
                        <small class="text text-grayish text-long">'.$api_name.'</small>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-4"><small>Base URL</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-7">
                        <small class="text text-grayish text-long">'.$base_url_api.'</small>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-4"><small>Username</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-7">
                        <small class="text text-grayish text-long">'.$username.'</small>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-4"><small>Password</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-7">
                        <small class="text text-grayish text-long">********</small>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-4"><small>Creat At</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-7">
                        <small class="text text-grayish text-long">'.$created_at.'</small>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-4"><small>Expired Duration</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-7">
                        <small class="text text-grayish text-long">'.$durasi_tampil.'</small>
                    </div>
                </div>
                <div class="row mb-2 mt-3">
                    <div class="col-12 text-center">
                        <div class="alert alert-danger">
                            <small>
                                Menghapus Data Tersebut Akan Menyebabkan Aplikasi Yang Menggunakan Parameter Pada API Key Yang Bersangkutan Tidak ValidLagi.<br>
                                <b>Apakah Anda Yakin Ingin Tetap Menghapus Data Tersebut?</b>
                            </small>
                        </div>
                    </div>
                </div>
            ';

        }
    }
?>