<?php
    // Koneksi, Function dan Session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    // Zona Waktu
    date_default_timezone_set("Asia/Jakarta");

    // Validasi Session
    if (empty($SessionIdAccess)) {
        echo '<div class="alert alert-danger text-center">
                <small>Sesi berakhir. Silakan login ulang.</small>
            </div>';
        exit;
    }

    // Validasi Input
    if (empty($_POST['id_radiologi_expertise'])) {
        echo '<div class="alert alert-danger text-center">
                <small>ID Expertise tidak boleh kosong.</small>
            </div>';
        exit;
    }
    if (empty($_POST['modality'])) {
        echo '<div class="alert alert-danger text-center">
                <small>ID Expertise tidak boleh kosong.</small>
            </div>';
        exit;
    }

    // Buat Variabel Data
    $id_radiologi_expertise = validateAndSanitizeInput($_POST['id_radiologi_expertise']);
    $modality               = validateAndSanitizeInput($_POST['modality']);

    // Tampilkan Sedikit Detail Data

    // Menentukan Query Berdasarkan Modality
    if($modality=="XR"){
        $Qry = $Conn->prepare("SELECT * FROM  radiologi_expertise WHERE id_radiologi_expertise = ?");
    }else{
        $Qry = $Conn->prepare("SELECT * FROM radiologi_expertise_usg WHERE id_radiologi_expertise_usg = ?");
    }
    
    $Qry->bind_param("s", $id_radiologi_expertise);
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

    // Menentukan Primary Key
    if($modality=="XR"){
        $id_radiologi_expertise = $Data['id_radiologi_expertise'];
    }else{
        $id_radiologi_expertise = $Data['id_radiologi_expertise_usg'];
    }

    // Jika Data Tidak Ditemukan
    if(empty($id_radiologi_expertise)){
        echo '
            <div class="row mb-3">
                <div class="col-12 text-center">
                    <div class="alert alert-danger"><small>ID Expertise Tiidak Valid! (Tidak Ditemukan Pada Database)</small></div>
                </div>
            </div>
        ';
        exit;
    }

    // Form Konfirmasi
?>
<input type="hidden" name="id_radiologi_expertise" value="<?php echo "$id_radiologi_expertise"; ?>">
<input type="hidden" name="modality" value="<?php echo "$modality"; ?>">
<div class="row mb-3">
    <div class="col-5"><small>ID Expertise</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-6">
        <small class="text text-grayish"><?php echo "$id_radiologi_expertise"; ?></small>
    </div>
</div>
<div class="row mb-3">
    <div class="col-5"><small>Modality</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-6">
        <small class="text text-grayish"><?php echo "$modality"; ?></small>
    </div>
</div>
<div class="row mb-3">
    <div class="col-5"><small><i>Accession Number</i></small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-6">
        <small class="text text-grayish"><?php echo $Data['accession_number']; ?></small>
    </div>
</div>
<div class="row mb-3">
    <div class="col-5"><small><i>Description</i></small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-6">
        <small class="text text-grayish"><?php echo $Data['description']; ?></small>
    </div>
</div>
<div class="row mb-3">
    <div class="col-12">
        <div class="alert alert-danger">
            Apakah anda yakin akan menghapus data tersebut?
        </div>
    </div>
</div>