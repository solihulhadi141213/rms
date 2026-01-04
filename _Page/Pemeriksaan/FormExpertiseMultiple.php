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

    //'id_radiologi' wajib terisi
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

    //Buat variabel 'id_radiologi' dan 'title'
    $id_radiologi = validateAndSanitizeInput($_POST['id_radiologi']);

    //Buka Detail Koneksi Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM radiologi_local_exp WHERE id_radiologi = ? ");
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

    // Jika Kosong
    if(empty($Data['id_radiologi_local_exp'])){
        $temuan ="";
        $kesan ="";
        $saran ="";
        $catatan ="";
    }else{
        $temuan = $Data['temuan'];
        $kesan = $Data['kesan'];
        $saran = $Data['saran'];
        $catatan = $Data['catatan'];
    }
?>
<input type="hidden" name="id_radiologi" value="<?php echo $id_radiologi; ?>">

<div class="row mb-3">
    <div class="col-12">
        <label for="expertise_temuan">Temuan</label>
        <input type="hidden" name="expertise_temuan" id="expertise_temuan">
        <div id="editor_expertise_temuan" style="height: 250px;">
            <?php echo $temuan; ?>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-12">
        <label for="expertise_kesan">Kesan</label>
        <input type="hidden" name="expertise_kesan" id="expertise_kesan">
        <div id="editor_expertise_kesan" style="height: 250px;">
            <?php echo $kesan; ?>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-12">
        <label for="expertise_saran">Saran</label>
        <input type="hidden" name="expertise_saran" id="expertise_saran">
        <div id="editor_expertise_saran" style="height: 250px;">
            <?php echo $saran; ?>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-12">
        <label for="expertise_catatan">Catatan / Keterangan Lainnya</label>
        <input type="hidden" name="expertise_catatan" id="expertise_catatan">
        <div id="editor_expertise_catatan" style="height: 250px;">
            <?php echo $catatan; ?>
        </div>
    </div>
</div>