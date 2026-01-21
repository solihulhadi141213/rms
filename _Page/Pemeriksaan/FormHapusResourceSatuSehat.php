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
    //id_radiologi wajib terisi
    if(empty($_POST['id_radiologi'])){
        echo '
            <div class="row mb-3">
                <div class="col-12 text-center">
                    <div class="alert alert-danger"><small>ID Radiologi Tidak Boleh Kosong!</small></div>
                </div>
            </div>
        ';
        exit;
    }

    //resource wajib terisi
    if(empty($_POST['resource'])){
        echo '
            <div class="row mb-3">
                <div class="col-12 text-center">
                    <div class="alert alert-danger"><small>Nama Resource Tidak Boleh Kosong!</small></div>
                </div>
            </div>
        ';
        exit;
    }

    //id wajib terisi
    if(empty($_POST['id'])){
        echo '
            <div class="row mb-3">
                <div class="col-12 text-center">
                    <div class="alert alert-danger"><small>ID Resource Tidak Boleh Kosong!</small></div>
                </div>
            </div>
        ';
        exit;
    }
    $id_radiologi = $_POST['id_radiologi'];
    $resource     = $_POST['resource'];
    $id           = $_POST['id'];
    // Konfirmasi Hapus
    echo '
        <input type="hidden" name="id_radiologi" value="'.$id_radiologi.'">
        <input type="hidden" name="resource" value="'.$resource.'">
        <input type="hidden" name="id" value="'.$id.'">
        <div class="row mb-2">
            <div class="col-4"><small>Resource</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text text-grayish">'.$resource.'</small>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-4"><small>ID Resource</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text text-grayish">
                    <small>'.$id.'</small>
                </small>
            </div>
        </div>
        <div class="row mb-2 mt-2">
            <div class="col-12">
                <div class="alert alert-warning text-center">
                     <small>Apakah anda yakin akan menghapus <i>Resource</i> tersebut?</small>
                </div>
            </div>
        </div>
    ';
?>