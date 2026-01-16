<?php
    //koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";
    include "../../_Config/SettingGeneral.php";
    
    //Zona Waktu
    date_default_timezone_set("Asia/Jakarta");

    // Fungsi Tambahan
    function safe_text($value) {
        $value = trim((string) $value);
        return $value === '' ? '-' : $value;
    }

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
    $Qry = $Conn->prepare("SELECT * FROM radiologi WHERE id_radiologi = ?");
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
    $id_access              = $Data['id_access'];
    $id_pasien              = $Data['id_pasien'];
    $id_kunjungan           = $Data['id_kunjungan'];
    $accession_number       = $Data['accession_number'];
    $id_service_request     = $Data['id_service_request'];
    $id_procedure           = $Data['id_procedure'];
    $id_imaging_study       = $Data['id_imaging_study'];
    $id_observation         = $Data['id_observation'];
    $id_diagnostic_report   = $Data['id_diagnostic_report'];
    $nama_pasien            = $Data['nama_pasien'];
    $priority               = $Data['priority'];
    $asal_kiriman           = $Data['asal_kiriman'];
    $alat_pemeriksa         = $Data['alat_pemeriksa'];
    $kode_dokter_pengirim   = $Data['kode_dokter_pengirim'];
    $ihs_dokter_pengirim    = $Data['ihs_dokter_pengirim'];
    $nama_dokter_pengirim   = $Data['nama_dokter_pengirim'];
    $kode_dokter_penerima   = $Data['kode_dokter_penerima'];
    $ihs_dokter_penerima    = $Data['ihs_dokter_penerima'];
    $nama_dokter_penerima   = $Data['nama_dokter_penerima'];
    $radiografer            = $Data['radiografer'];
    $pesan                  = $Data['pesan'];
    $kesan                  = $Data['kesan'];
    $klinis                 = $Data['klinis'];
    $permintaan_pemeriksaan = $Data['permintaan_pemeriksaan'];
    $kv                     = $Data['kv'];
    $ma                     = $Data['ma'];
    $sec                    = $Data['sec'];
    $tujuan                 = $Data['tujuan'];
    $pembayaran             = $Data['pembayaran'];
    $datetime_diminta       = $Data['datetime_diminta'];
    $datetime_dikerjakan    = $Data['datetime_dikerjakan'];
    $datetime_hasil         = $Data['datetime_hasil'];
    $datetime_selesai       = $Data['datetime_selesai'];
    $status_pemeriksaan     = $Data['status_pemeriksaan'];
    $alasan_pembatalan     = $Data['alasan_pembatalan'];
?>
<input type="hidden" name="id_radiologi" value="<?php echo $id_radiologi; ?>">
<div class="row mb-3">
    <div class="col-12">
        <label for="asal_kiriman_edit"><small>Asal Kiriman</small></label>
        <input type="text" name="asal_kiriman" id="asal_kiriman_edit" class="form-control" value="<?php echo $asal_kiriman; ?>">
    </div>
</div>
<div class="row mb-3">
    <div class="col-12">
        <label for="priority_edit"><small>Prioritisasi</small></label>
        <select name="priority" id="priority_edit" class="form-control">
            <option <?php if($priority=="routine"){echo "selected";} ?> value="routine">Biasa</option>
            <option <?php if($priority=="urgent"){echo "selected";} ?> value="urgent">Segera</option>
            <option <?php if($priority=="stat"){echo "selected";} ?> value="stat">Gawat</option>
        </select>
    </div>
</div>
<div class="row mb-3">
    <div class="col-12">
        <label for="alat_pemeriksa_edit"><small>Modality/Alat</small></label>
        <select name="alat_pemeriksa" <?php if($status_pemeriksaan=="Selesai"||$status_pemeriksaan=="Selesai"){echo "disabled";} ?> id="alat_pemeriksa_edit" class="form-control">
            <option <?php if($alat_pemeriksa==""){echo "selected";} ?> value="">Pilih</option>
            <option <?php if($alat_pemeriksa=="XR"){echo "selected";} ?> value="XR">X-Ray</option>
            <option <?php if($alat_pemeriksa=="US"){echo "selected";} ?> value="US">USG / Echo</option>
            <option <?php if($alat_pemeriksa=="CT"){echo "selected";} ?> value="CT">CT Scan</option>
            <option <?php if($alat_pemeriksa=="MR"){echo "selected";} ?> value="MR">MRI</option>
            <option <?php if($alat_pemeriksa=="NM"){echo "selected";} ?> value="NM">Nuclear Medicine (Kedokteran nuklir)</option>
            <option <?php if($alat_pemeriksa=="PT"){echo "selected";} ?> value="PT">PET Scan</option>
            <option <?php if($alat_pemeriksa=="DX"){echo "selected";} ?> value="DX">Digital Radiography</option>
            <option <?php if($alat_pemeriksa=="CR"){echo "selected";} ?> value="CR">Computed Radiography</option>
        </select>
    </div>
</div>
<div class="row mb-3">
    <div class="col-12">
        <label for="pesan_edit"><small>Pesan / Keterangan</small></label>
        <textarea class="form-control" name="pesan" id="pesan_edit"><?php echo "$pesan"; ?></textarea>
        <small class="text text-grayish">
            <small>Pesan atau keterangan yang perlu disertakan. Misalnya : Tolong menggunakan kontras</small>
        </small>
    </div>
</div>
<div class="row mb-3">
    <div class="col-12">
        <label for="status_pemeriksaan_edit"><small>Status Pemeriksaan</small></label>
        <select name="status_pemeriksaan" id="status_pemeriksaan_edit" class="form-control">
            <option <?php if($status_pemeriksaan==""){echo "selected";} ?> value="">Pilih</option>
            <option <?php if($status_pemeriksaan=="Diminta"){echo "selected";} ?> value="Diminta">Diminta</option>
            <option <?php if($status_pemeriksaan=="Dikerjakan"){echo "selected";} ?> value="Dikerjakan">Dikerjakan</option>
            <option <?php if($status_pemeriksaan=="Hasil"){echo "selected";} ?> value="Hasil">Hasil</option>
            <option <?php if($status_pemeriksaan=="Selesai"){echo "selected";} ?> value="Selesai">Selesai</option>
            <option <?php if($status_pemeriksaan=="Batal"){echo "selected";} ?> value="Batal">Batal</option>
        </select>
    </div>
</div>
<div class="row mb-3" id="form_alasan_pembatalan">
    <div class="col-12">
        <label for="alasan_pembatalan_edit"><small>Alasan Pembatalan</small></label>
        <textarea class="form-control" name="alasan_pembatalan" id="alasan_pembatalan_edit"><?php echo "$alasan_pembatalan"; ?></textarea>
    </div>
</div>
<div class="row mb-3">
    <div class="col-12">
        <label for="radiografer_edit"><small>Radiografer</small></label>
        <input type="text" name="radiografer" <?php if($status_pemeriksaan=="Selesai"||$status_pemeriksaan=="Selesai"){echo "disabled";} ?> id="radiografer_edit" class="form-control" value="<?php echo $radiografer; ?>">
    </div>
</div>