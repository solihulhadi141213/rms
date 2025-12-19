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
            $label_status = '<span class="badge bg-danger"><i class="bi bi-x-circle"></i> Inactive</span>';
        }else{
            $label_status = '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Active</span>';
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
                <div class="row mb-2">
                    <div class="col-4"><small>URL PACS</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-7">
                        <div class="copy-wrapper">
                            <small class="text text-grayish text-long" id="url_simrs">
                                '.$url_connection_pacs.'
                            </small>
                            <i class="bi bi-clipboard copy-btn"
                            onclick="copyText(\'url_simrs\')"
                            title="Copy"></i>
                        </div>
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-4"><small>Nama Koneksi</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-7">
                        <small class="text text-grayish">'.$name_connection_pacs.'</small>
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-4"><small>Username</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-7">
                        <div class="copy-wrapper">
                            <small class="text text-grayish text-long" id="username_connection_pacs">
                                '.$username_connection_pacs.'
                            </small>
                            <i class="bi bi-clipboard copy-btn"
                            onclick="copyText(\'username_connection_pacs\')"
                            title="Copy"></i>
                        </div>
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-4"><small>Secret Key</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-7">
                        <div class="copy-wrapper">
                            <small class="text text-grayish text-long" id="password_connection_pacs">
                                '.$password_connection_pacs.'
                            </small>
                            <i class="bi bi-clipboard copy-btn"
                            onclick="copyText(\'password_connection_pacs\')"
                            title="Copy"></i>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-4"><small>Status Koneksi</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-7">
                        <small class="text text-grayish">'.$label_status.'</small>
                    </div>
                </div>
                <div class="row mb-2 mt-3">
                    <div class="col-12 text-center">
                        <div class="alert alert-danger">
                            <small>
                                Menghapus Pengaturan Koneksi PACS Akan Menyebabkan Aplikasi Tidak Bisa menggunakan parameter tersebut untuk terhubung dengan Server.<br>
                                <b>Apakah Anda Yakin Ingin Tetap Menghapus Pengaturan Tersebut?</b>
                            </small>
                        </div>
                    </div>
                </div>
            ';

        }
    }
?>