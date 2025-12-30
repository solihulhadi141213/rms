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
                    <div class="alert alert-danger"><small>ID Pemeriksaan Tiidak Boleh Kosong!</small></div>
                </div>
            </div>
        ';
        exit;
    }

    //Buat variabel 'id_radiologi' dan sanitasi
    $id_radiologi = validateAndSanitizeInput($_POST['id_radiologi']);

    //Buka Detail Koneksi Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT kv, ma, sec FROM radiologi WHERE id_radiologi = ?");
    $Qry->bind_param("i", $id_radiologi);
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

    //Buat Variabel
    $kv  = $Data['kv'];
    $ma  = $Data['ma'];
    $sec = $Data['sec'];

    echo '';
?>
<input type="hidden" name="id_radiologi" value="<?php echo $id_radiologi; ?>">
<div class="row mb-3">
    <div class="col-12">
        <label for="kv"><small>Tegangan Listrik</small></label>
        <div class="input-group">
            <input type="text" class="form-control" name="kv" id="kv" value="<?php echo $kv; ?>">
            <span class="input-group-text" id="basic-addon2">kV</span>
        </div>
    </div>
</div>
<div class="row mb-2">
    <div class="col-12">
        <label for="ma"><small>Arus Listrik</small></label>
        <div class="input-group">
            <input type="text" class="form-control" name="ma" id="ma" value="<?php echo $ma; ?>">
            <span class="input-group-text" id="basic-addon2">mA</span>
        </div>
    </div>
</div>
<div class="row mb-2">
    <div class="col-12">
        <label for="sec"><small>Lama Paparan</small></label>
        <div class="input-group">
            <input type="text" class="form-control" name="sec" id="sec" value="<?php echo $sec; ?>">
            <span class="input-group-text" id="basic-addon2">sec</span>
        </div>
    </div>
</div>