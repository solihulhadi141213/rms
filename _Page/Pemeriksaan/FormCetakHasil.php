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
    $radiografer            = $Data['radiografer'] ?? "-";
    $pesan                  = $Data['pesan'] ?? "-";
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

    if($status_pemeriksaan=="Hasil"||$status_pemeriksaan=="Selesai"){
        echo '
            <input type="hidden" name="data" value="Report">
            <input type="hidden" name="an" value="'.$accession_number.'">
            <div class="row mb-2">
                <div class="col-5"><small>ID Rad</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-6"><small>'.$id_radiologi.'</small></div>
            </div>
            <div class="row mb-2">
                <div class="col-5"><small>Accession Number</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-6"><small>'.$accession_number.'</small></div>
            </div>
            <div class="row mb-2">
                <div class="col-5"><small>Nama Pasien</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-6"><small>'.$nama_pasien.'</small></div>
            </div>
            <div class="row mb-2">
                <div class="col-5"><small>Tanggal</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-6"><small>'.$datetime_diminta.'</small></div>
            </div>
            <div class="row">
                <div class="col-12 text-center">
                    <div class="alert alert-info">
                        Apakah Anda Yakin Akan Melanjutkan Percetakan?
                    </div>
                </div>
            </div>
            <script>
                $("#button_cetak_hasil").prop("disabled", false);
            </script>
        ';
    }else{
        echo '
            <div class="row">
                <div class="col-12 text-center">
                    <div class="alert alert-danger">
                        <h1><i class="bi bi-exclamation-triangle"></i></h1>
                        Pemeriksaan Radiologi Belum Selesai! Anda Tidak Bisa Melakukan Percetakan Untuk Data Ini.<br>
                        <b>Silahkan selesaikan pelayanan pemeriksaan terlebih dulu.</b>
                    </div>
                </div>
            </div>
            <script>
                $("#button_cetak_hasil").prop("disabled", true);
            </script>
        ';
    }
?>
