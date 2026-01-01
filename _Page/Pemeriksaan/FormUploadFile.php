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
                    <div class="alert alert-danger"><span>ID Pemeriksaan Tiidak Boleh Kosong!</span></div>
                </div>
            </div>
        ';
        exit;
    }

    //Buat variabel 'id_radiologi' dan sanitasi
    $id_radiologi = validateAndSanitizeInput($_POST['id_radiologi']);

    //Buka Detail 'radiologi'  Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT id_radiologi FROM radiologi WHERE id_radiologi = ?");
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
    $id_radiologi = $Data['id_radiologi'];
?>
<input type="hidden" name="id_radiologi" value="<?php echo $id_radiologi; ?>">

<div class="row mb-3">
    <div class="col-12 mb-2">
        <label for="upload_file">
            <small>Upload File</small>
        </label>
        <input type="file" name="upload_file" id="upload_file" class="form-control" required>
        <small class="text text-grayish">
            Tipe file (JPG, JPEG, PNG dan GIF), maksimal 2Mb
        </small>
    </div>
    <div class="col-12">
        <label for="file_description">
            <small>Deskripsi / Keterangan</small>
        </label>
        <textarea name="file_description" id="file_description" class="form-control"></textarea>
        <small class="text text-grayish">
            Tulis keterangan mengenai file tersebut.
        </small>
    </div>
</div>
