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
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="nama_edit">
                        <small>Nama Lengkap</small>
                    </label>
                    <input type="text" class="form-control" name="nama" id="nama_edit" value="'.$nama.'" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="kode_edit">
                        <small>Kode Lokal</small>
                    </label>
                    <input type="text" class="form-control" name="kode" id="kode_edit" value="'.$kode.'">
                    <small class="text text-grayish">Kode Dari SIMRS (Jika Ada)</small>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="ihs_edit">
                        <small>IHS Satu Sehat</small>
                    </label>
                    <input type="text" class="form-control" name="ihs" id="ihs_edit" value="'.$ihs.'">
                    <small class="text text-grayish">ID Practitioner Dari Satu Sehat</small>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="kategori_edit">
                        <small>Kategori</small>
                    </label>
                    <input type="text" class="form-control" name="kategori" id="kategori_edit" list="list_kategori_edit" value="'.$kategori.'" required>
                    <datalist id="list_kategori_edit" class="list_kategori">
                       
                    </datalist>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="file_tanda_tangan_edit">
                        <small>File Tanda Tangan</small>
                    </label>
                    <input type="file" class="form-control" name="file_tanda_tangan" id="file_tanda_tangan_edit">
                    <small>
                        File Type PNG, JPG, GIF (Maksimal 1 Mb)
                    </small>
                </div>
            </div>
        ';
    }
?>