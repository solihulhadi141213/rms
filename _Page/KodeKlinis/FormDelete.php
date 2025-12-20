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

    //id_master_klinis wajib terisi
    if(empty($_POST['id_master_klinis'])){
        echo '
            <div class="row mb-3">
                <div class="col-12 text-center">
                    <div class="alert alert-danger"><small>ID Kode Klinis Tiidak Boleh Kosong!</small></div>
                </div>
            </div>
        ';
        exit;
    }

    //Buat variabel 'id_master_klinis' dan sanitasi
    $id_master_klinis = validateAndSanitizeInput($_POST['id_master_klinis']);

    //Buka Detail Koneksi Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM master_klinis WHERE id_master_klinis = ?");
    $Qry->bind_param("i", $id_master_klinis);
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
       $id_master_klinis = $Data['id_master_klinis'];
       $nama_klinis      = $Data['nama_klinis'];
       $snomed_code      = $Data['snomed_code'];
       $snomed_display   = $Data['snomed_display'];
       $kategori         = $Data['kategori'];
       $aktif            = $Data['aktif'];
       $datetime_create  = $Data['datetime_create'];
       $datetime_update  = $Data['datetime_update'];

        //Routing Status
        if($aktif=="Tidak"){
            $status = '<span class="badge bg-danger"><i class="bi bi-x"></i> Inactive</span>';
        }else{
            $status = '<span class="badge bg-success"><i class="bi bi-check"></i> Active</span>';
        }
        //Tampilkan Data
        echo '
            <input type="hidden" name="id_master_klinis" value="'.$id_master_klinis.'" required>
            <div class="row mb-2">
                <div class="col-4"><small>Nama Klinis</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$nama_klinis.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Snomed Code</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$snomed_code.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Snomed Display</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$snomed_display.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Kategori</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$kategori.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Datetime Creat</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$datetime_create.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Datetime Update</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$datetime_update.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Status</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$status.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-12 text-center">
                    <div class="alert alert-danger">
                        <small>
                            Menghapus Kode Klinis Akan Menyebabkan User Tidak Akan Bisa Memilih Opsi Klinis Tersebut.<br>
                            <b>Apakah Anda Yakin Ingin Tetap Menghapusnya?</b>
                        </small>
                    </div>
                </div>
            </div>
        ';
    }
?>