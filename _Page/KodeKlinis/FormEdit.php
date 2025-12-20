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
            $status1 = '';
            $status2 = 'selected';
        }else{
            $status1 = 'selected';
            $status2 = '';
        }
        //Tampilkan Data
        echo '
            <input type="hidden" name="id_master_klinis" value="'.$id_master_klinis.'" required>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="nama_klinis_edit">
                        <small>Nama Klinis</small>
                    </label>
                    <input type="text" class="form-control" name="nama_klinis" id="nama_klinis_edit" value="'.$nama_klinis.'" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="snomed_code_edit">
                        <small>Kode Klinis</small>
                    </label>
                    <input type="text" class="form-control" name="snomed_code" id="snomed_code_edit" value="'.$snomed_code.'" required>
                    <small class="text text-grayish">Kode Berdasarkan SNOMED-CT</small>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="snomed_display_edit">
                        <small>Display Klinis</small>
                    </label>
                    <input type="text" class="form-control" name="snomed_display" id="snomed_display_edit" value="'.$snomed_display.'" required>
                    <small class="text text-grayish">Deskripsi Berdasarkan SNOMED-CT</small>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="kategori_edit">
                        <small>Kategori</small>
                    </label>
                    <input type="text" class="form-control" name="kategori" id="kategori_edit" list="list_kategori_edit" value="'.$kategori.'" required>
                    <datalist id="list_kategori_edit" class="list_kategori"></datalist>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="aktif_edit">
                        <small>Status</small>
                    </label>
                    <select name="aktif" id="aktif_edit" class="form-control">
                        <option '.$status1.' value="1">Active</option>
                        <option '.$status2.' value="0">Inactive</option>
                    </select>
                </div>
            </div>
        ';
    }
?>