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

    //id_master_signature wajib terisi
    if(empty($_POST['id_master_signature'])){
        echo '
            <div class="row mb-3">
                <div class="col-12 text-center">
                    <div class="alert alert-danger"><small>ID Referensi Tiidak Boleh Kosong!</small></div>
                </div>
            </div>
        ';
        exit;
    }

    //Buat variabel 'id_master_signature' dan sanitasi
    $id_master_signature = validateAndSanitizeInput($_POST['id_master_signature']);

    //Buka Detail Koneksi Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM master_signature WHERE id_master_signature = ?");
    $Qry->bind_param("i", $id_master_signature);
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
        $kode = $Data['kode'];
        $ihs = $Data['ihs'];
        $nama = $Data['nama'];
        $kategori = $Data['kategori'];
        $base_64_ttd = $Data['base_64_ttd'];
        $delete_at = $Data['delete_at'];

        //Tampilkan Data
        echo '
            <input type="hidden" name="id_master_signature" value="'.$id_master_signature.'" required>
            <div class="row mb-2">
                <div class="col-4"><small>Nama Lengkap</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$nama.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Kode Lokal</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$kode.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>ID Practitioner</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$ihs.'</small>
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
                <div class="col-12 text-center">
                    <div class="alert alert-danger">
                        <small>
                            Menghapus referensi tanda tangan, akan menyebabkan tampilan referensi praktisi bersangkutan tidak akan terbaca.<br>
                            <b>Apakah Anda Yakin Ingin Tetap Menghapusnya?</b>
                        </small>
                    </div>
                </div>
            </div>
        ';
    }
?>