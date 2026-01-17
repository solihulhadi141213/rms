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
                    <div class="alert alert-danger"><span>Sesi Akses Sudah Berakhir! Silahkan Login Ulang.</span></div>
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
                    <div class="alert alert-danger"><span>ID Pemeriksaan Tidak Boleh Kosong!</span></div>
                </div>
            </div>
        ';
        exit;
    }

    //id_klinis wajib terisi
    if(empty($_POST['id_klinis'])){
        echo '
            <div class="row mb-3">
                <div class="col-12 text-center">
                    <div class="alert alert-danger"><span>ID Klinis Tidak Boleh Kosong!</span></div>
                </div>
            </div>
        ';
        exit;
    }

    //Buat variabel 'id_radiologi' dan 'id_klinis'
    $id_radiologi = validateAndSanitizeInput($_POST['id_radiologi']);
    $id_klinis = validateAndSanitizeInput($_POST['id_klinis']);

    //Buka Detail 'radiologi'  Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT id_radiologi, klinis FROM radiologi WHERE id_radiologi = ?");
    $Qry->bind_param("i", $id_radiologi);
    if (!$Qry->execute()) {
        $error=$Conn->error;
        echo '
            <div class="alert alert-danger">
                <span>Terjadi kesalahan pada saat membuka data dari database!<br>Keterangan : '.$error.'</span>
            </div>
        ';
        exit;
    }
    $Result = $Qry->get_result();
    $Data = $Result->fetch_assoc();
    $Qry->close();

    // Validasi 'id_radiologi'
    if(empty($Data['id_radiologi'])){
        echo '
            <div class="row mb-3">
                <div class="col-12 text-center">
                    <div class="alert alert-danger"><span>ID Radiologi Tidak Valid!</span></div>
                </div>
            </div>
        ';
        exit;
    }

    //Buat Variabel
    $klinis      = $Data['klinis'];
    $klinis_arry = json_decode($klinis, true);

    foreach ($klinis_arry as $klinis_list){
        if($klinis_list['id_klinis']==$id_klinis){
            $nama_klinis     = $klinis_list['nama_klinis'];
            $snomed_code     = $klinis_list['snomed_code'];
            $snomed_display  = $klinis_list['snomed_display'];
            $kategori        = $klinis_list['kategori'];
        }
    }

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
    ';
?>
<input type="hidden" name="id_radiologi" value="<?php echo $id_radiologi; ?>">
<input type="hidden" name="id_klinis" value="<?php echo $id_klinis; ?>">
<div class="row mt-3">
    <div class="col-12">
        <div class="alert alert-warning">
            <small>Apakah anda yakin ingin menghapus data klinis tersebut?</small>
        </div>
    </div>
</div>
