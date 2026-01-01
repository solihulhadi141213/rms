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
    if(empty($Data['id_radiologi'])){
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

    // Buka Akses
    if(!empty($Data['id_access'])){
        $id_access        = $Data['id_access'];
        $officer = GetDetailData($Conn, 'access', 'id_access', $id_access, 'access_name');
    }else{
        $officer = "-";
    }
    $dir_file = ''.$app_base_url.'/_Storage/'.$folder_name.'/'.$file_name.'';
?>
<div class="table table-responsive">
    <table class="table table-bordered table-sm">
        <tbody>
            <tr>
                <td><i>File Name</i></td>
                <td class="text text-grayish"><?php echo "$file_name"; ?></td>
            </tr>
            <tr>
                <td><i>File Type</i></td>
                <td class="text text-grayish"><?php echo "$file_type"; ?></td>
            </tr>
            <tr>
                <td><i>File Size</i></td>
                <td class="text text-grayish"><?php echo "$file_size"; ?></td>
            </tr>
            <tr>
                <td><i>Upload At</i></td>
                <td class="text text-grayish"><?php echo "$file_datetime"; ?></td>
            </tr>
            <tr>
                <td><i>Description</i></td>
                <td class="text text-grayish"><?php echo "$file_description"; ?></td>
            </tr>
            <tr>
                <td><i>Officer</i></td>
                <td class="text text-grayish"><?php echo "$officer"; ?></td>
            </tr>
            <tr>
                <td colspan="2">
                    <img src="<?php echo $dir_file; ?>" alt="" width="100%">
                </td>
            </tr>
        </tbody>
    </table>
</div>