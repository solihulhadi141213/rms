<?php
    include "../../_Config/Connection.php";
    if(empty($_POST['id_master_klinis'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>Silahkan Pilih Referensi Klinis Terlebih Dulu!</small>
            </div>
        ';
        exit;
    }
    $id_master_klinis = $_POST['id_master_klinis'];

    // Buka Data master klinis
    $Qry = $Conn->prepare("SELECT * FROM master_klinis WHERE id_master_klinis = ?");
    $Qry->bind_param("i", $id_master_klinis);
    if (!$Qry->execute()) {
        $error=$Conn->error;
        echo '
            <div class="alert alert-danger">
                <small>Terjadi kesalahan pada saat membuka data dari database!<br>Keterangan : '.$error.'</small>
            </div>
        ';
        exit;
    }
    $Result = $Qry->get_result();
    $Data = $Result->fetch_assoc();
    $Qry->close();
    
    // Jika Data Tidak Ditemukan
    if(empty($Data['id_master_klinis'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>Referensi Klinis Tidak Ditemukan!</small>
            </div>
        ';
        exit;
    }

    $nama_klinis     = $Data['nama_klinis'];
    $snomed_code     = $Data['snomed_code'];
    $snomed_display  = $Data['snomed_display'];
    $kategori        = $Data['kategori'];
    $aktif           = $Data['aktif'];
    $datetime_create = $Data['datetime_create'];
    $datetime_update = $Data['datetime_update'];

    // Tampilkan
    echo '
        <div class="row mb-2">
            <div class="col-4"><small>Kategori</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7"><small class="text text-grayish">'.$kategori.'</small></div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small>Klinis</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7"><small class="text text-grayish">'.$nama_klinis.'</small></div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small><i>Snomed Code</i></small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7"><small class="text text-grayish">'.$snomed_code.'</small></div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small><i>Snomed Display</i></small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7"><small class="text text-grayish">'.$snomed_display.'</small></div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small>Status</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7"><small class="text text-grayish">'.$aktif.'</small></div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small><i>Datetime Creat</i></small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7"><small class="text text-grayish">'.$datetime_create.'</small></div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small><i>Datetime Update</i></small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7"><small class="text text-grayish">'.$datetime_update.'</small></div>
        </div>
    ';
?>