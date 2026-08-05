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
    $Qry = $Conn->prepare("SELECT id_radiologi, id_kunjungan FROM radiologi WHERE id_radiologi = ?");
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
    $id_kunjungan           = $Data['id_kunjungan'];

    // ===========================================
    // Membuka Data Kunjungan
    // ===========================================
    
    // Buka URL SIMRS
    $status_connection_simrs = 1;
    $url_connection_simrs = GetDetailData($Conn,'connection_simrs','status_connection_simrs',$status_connection_simrs,'url_connection_simrs');

    //Dapatkan Token SIMRS
    $token = GetSimrsToken($Conn);

    // Jika Token Tidak Valid Dan Gagal Dibuat
    if ($token === false) {
        echo '
            <div class="alert alert-danger">
                <small>Gagal mendapatkan token SIMRS!</small>
            </div>
        ';
        exit;
    }
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => ''.$url_connection_simrs.'/API/SIMRS/get_detail_kunjungan.php?id='.$id_kunjungan.'',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'token: '.$token.'',
            'X-API-Key: ••••••'
        ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    $data = json_decode($response, true);

    // Jika Response Tidak Valid
    if (empty($data['response']['code']) ||$data['response']['code'] != 200) {
        echo '
            <div class="row mb-3">
                <div class="col-12">
                    <div class="alert alert-danger">
                        <small>Gagal memuat data kunjungan<br> Pesan : '.$data['response']['message'].'</small>
                    </div>
                </div>
            </div>
        ';
        exit;
    }

    // Buka Metadata
    $metadata      = $data['metadata'] ?? [];
    $diagnosa_awal = $metadata['DiagAwal'] ?? '-';

    // Pastikan array pasien ada
    $pasien = $metadata['pasien'] ?? [];

    // Helper function untuk nilai yang mungkin kosong
    function getDisplayValue($value, $default = '-') {
        return (isset($value) && trim($value) !== '') ? $value : $default;
    }

    // Buat Variabel Penting
    $id_encounter            = getDisplayValue($metadata['id_encounter'] ?? null);
    $id_pasien               = getDisplayValue($pasien['id_pasien'] ?? null);
    $id_ihs                  = getDisplayValue($pasien['id_ihs'] ?? null);
    $nama                    = getDisplayValue($pasien['nama'] ?? null);
    $gender                  = getDisplayValue($pasien['gender'] ?? null);
    $tempat_lahir            = getDisplayValue($pasien['tempat_lahir'] ?? null);
    $tanggal_lahir_kunjungan = getDisplayValue($pasien['tanggal_lahir'] ?? null);
    $kontak                  = getDisplayValue($pasien['kontak'] ?? null);
    $kontak_darurat          = getDisplayValue($pasien['kontak_darurat'] ?? null);
    $nik                     = getDisplayValue($pasien['nik'] ?? null);
    $no_bpjs                 = getDisplayValue($pasien['no_bpjs'] ?? null);
    $propinsi                = getDisplayValue($pasien['propinsi'] ?? null);
    $kabupaten               = getDisplayValue($pasien['kabupaten'] ?? null);
    $kecamatan               = getDisplayValue($pasien['kecamatan'] ?? null);
    $desa                    = getDisplayValue($pasien['desa'] ?? null);
    $alamat                  = getDisplayValue($pasien['alamat'] ?? null);
    $perkawinan              = getDisplayValue($pasien['perkawinan'] ?? null);
    $dpjp                    = getDisplayValue($metadata['dokter'] ?? null);
    $penanggungjawab         = getDisplayValue($metadata['penanggungjawab'] ?? null);

?>
<input type="hidden" name="id_radiologi" value="<?php echo $id_radiologi; ?>">
<div class="row mb-3">
    <div class="col-12">
        <label for="tanggal_lahir_kunjungan">Tanggal Lahir</label>
        <input type="date" class="form-control" name="tanggal_lahir" value="<?php echo $tanggal_lahir_kunjungan; ?>">
    </div>
</div>