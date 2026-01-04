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
                    <div class="alert alert-danger"><small>ID Pemeriksaan Tidak Boleh Kosong!</small></div>
                </div>
            </div>
        ';
        exit;
    }

    if(empty($_POST['kolom'])){
        echo '
            <div class="row mb-3">
                <div class="col-12 text-center">
                    <div class="alert alert-danger"><small>Parameter Waktu Pelayanan Tidak Boleh Kosong!</small></div>
                </div>
            </div>
        ';
        exit;
    }

    //Buat variabel 'id_radiologi' dan sanitasi
    $id_radiologi = validateAndSanitizeInput($_POST['id_radiologi']);
    $kolom        = validateAndSanitizeInput($_POST['kolom']);

    //Buka Detail Koneksi Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT $kolom FROM radiologi WHERE id_radiologi = ?");
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
    $parameter_waktu  = $Data[$kolom];

    // Format Informasi Waktu
    if(!empty($Data[$kolom])){
        $tanggal_pelayanan = date('Y-m-d', strtotime($parameter_waktu));
        $jam_pelayanan     = date('H:i', strtotime($parameter_waktu));
    }else{
        $tanggal_pelayanan = "";
        $jam_pelayanan     = "";
    }
   

    // Routing Nama Parameter Waktu
    if($kolom=="datetime_diminta"){
        $nama_parameter = "Waktu Permintaan Dikirim";
    }else{
        if($kolom=="datetime_dikerjakan"){
            $nama_parameter = "Waktu Permintaan Diterima dan Dikerjakan";
        }else{
            if($kolom=="datetime_hasil"){
                $nama_parameter = "Dokter Mengisi Expertise Pemeriksaan Radiologi";
            }else{
                if($kolom=="datetime_selesai"){
                    $nama_parameter = "Hasil Pemeriksaan Diserahkan";
                }else{
                    $nama_parameter = "Keterangan Tidak Diketahui";
                }
            }
        }
    }
?>
<input type="hidden" name="id_radiologi" value="<?php echo $id_radiologi; ?>">
<input type="hidden" name="parameter_waktu" value="<?php echo $kolom; ?>">
<div class="row mb-3">
    <div class="col-md-4">
        <small>Nama Parameter</small>
    </div>
    <div class="col-md-8">
        <input type="text" readonly class="form-control" value="<?php echo $nama_parameter; ?>">
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-4">
        <label for="tanggal_pelayanan"><small>Tanggal Pelayanan</small></label>
    </div>
    <div class="col-md-8">
        <input type="date" class="form-control" name="tanggal_pelayanan" id="tanggal_pelayanan" value="<?php echo $tanggal_pelayanan; ?>">
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-4">
        <label for="jam_pelayanan"><small>Jam Pelayanan</small></label>
    </div>
    <div class="col-md-8">
        <input type="time" class="form-control" name="jam_pelayanan" id="jam_pelayanan" value="<?php echo $jam_pelayanan; ?>">
    </div>
</div>
