<?php
    //koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";
    include "../../_Config/SettingGeneral.php";
    
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

    //id_radiologi_file wajib terisi
    if(empty($_POST['id_radiologi_file'])){
        echo '
            <div class="row mb-3">
                <div class="col-12 text-center">
                    <div class="alert alert-danger"><small>ID File Tiidak Boleh Kosong!</small></div>
                </div>
            </div>
        ';
        exit;
    }

    //Buat variabel 'id_radiologi_file' dan sanitasi
    $id_radiologi_file = validateAndSanitizeInput($_POST['id_radiologi_file']);

    //Buka Detail Koneksi Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM radiologi_file WHERE id_radiologi_file = ?");
    $Qry->bind_param("s", $id_radiologi_file);
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
    if(empty($Data['id_radiologi_file'])){
        echo '
            <div class="row mb-3">
                <div class="col-12 text-center">
                    <div class="alert alert-danger"><small>ID File Tiidak Valid!</small></div>
                </div>
            </div>
        ';
        exit;
    }

    //Buat Variabel
    $id_radiologi     = $Data['id_radiologi'];
    $folder_name      = $Data['folder_name'];
    $file_datetime    = $Data['file_datetime'];
    $file_description = $Data['file_description'];
    $file_type        = $Data['file_type'];
    $file_size        = $Data['file_size'];
    $file_name        = $Data['file_name'];

    // Mengubah Satuan file_size
    $file_size_mb = round($file_size / 1024 / 1024, 2);
    $file_size    = "$file_size_mb Mb";

    // Buka Akses Dan Ambil Nama Petugas
    if(!empty($Data['id_access'])){
        $id_access        = $Data['id_access'];
        $officer = GetDetailData($Conn, 'access', 'id_access', $id_access, 'access_name');
    }else{
        $officer = "-";
    }
    $dir_file = ''.$app_base_url.'/_Storage/'.$folder_name.'/'.$file_name.'';
?>
    <input type="hidden" name="id_radiologi_file" value="<?php echo $id_radiologi_file; ?>">
    <div class="row mb-2">
        <div class="col-4"><small><i>File Name</i></small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-7">
            <small class="text text-grayish">
                <small><?php echo "$file_name"; ?></small>
            </small>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-4"><small><i>File Type</i></small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-7">
            <small class="text text-grayish">
                <small><?php echo "$file_type"; ?></small>
            </small>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-4"><small><i>File Size</i></small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-7">
            <small class="text text-grayish">
                <small><?php echo "$file_size"; ?></small>
            </small>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-4"><small><i>Upload At</i></small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-7">
            <small class="text text-grayish">
                <small><?php echo "$file_datetime"; ?></small>
            </small>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-4"><small><i>Upload At</i></small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-7">
            <small class="text text-grayish">
                <small><?php echo "$file_datetime"; ?></small>
            </small>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-4"><small><i>Officer</i></small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-7">
            <small class="text text-grayish">
                <small><?php echo "$officer"; ?></small>
            </small>
        </div>
    </div>