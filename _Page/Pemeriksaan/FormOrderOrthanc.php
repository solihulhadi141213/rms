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

    if(empty($Data['id_radiologi'])){
        echo '
            <div class="alert alert-danger">
                <small>ID Radiologi Yang Anda Gunakan Tidak Valid</small>
            </div>
        ';
        exit;
    }

    //Buat Variabel
    $id_access              = $Data['id_access'] ?? null;
    $id_pasien              = $Data['id_pasien'] ?? null;
    $id_kunjungan           = $Data['id_kunjungan'] ?? null;
    $accession_number       = $Data['accession_number'];
    $id_service_request     = $Data['id_service_request'];
    $id_procedure           = $Data['id_procedure'];
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

    // Nama Modalitas
    $modalitas_list = [
        'XR' => 'X-Ray',
        'CT' => 'CT-Scan',
        'US' => 'USG',
        'MR' => 'MRI',
        'NM' => 'Nuclear Medicine (Kedokteran Nuklir)',
        'PT' => 'PET Scan',
        'DX' => 'Digital Radiography',
        'CR' => 'Computed Radiography'
    ];

    // Ambil nama modalitas
    $nama_modalitas = $modalitas_list[$alat_pemeriksa] ?? '-';

    //Format Tanggal
    $datetime_diminta     = formatDateTimeStrict($Data['datetime_diminta']);
    $datetime_dikerjakan  = formatDateTimeStrict($Data['datetime_dikerjakan']);
    $datetime_hasil       = formatDateTimeStrict($Data['datetime_hasil']);
    $datetime_selesai     = formatDateTimeStrict($Data['datetime_selesai']);

    //Format tanggal PACS
    $dt            = new DateTime($Data['datetime_diminta'], new DateTimeZone('Asia/Jakarta'));
    $datetime_pacs = $dt->format('Y-m-d H:i:s.uP');

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
    $id_encounter   = getDisplayValue($metadata['id_encounter'] ?? null);
    $id_ihs         = getDisplayValue($pasien['id_ihs'] ?? null);
    $nama           = getDisplayValue($pasien['nama'] ?? null);
    $gender         = getDisplayValue($pasien['gender'] ?? null);
    $tempat_lahir   = getDisplayValue($pasien['tempat_lahir'] ?? null);
    $tanggal_lahir  = getDisplayValue($pasien['tanggal_lahir'] ?? null);
    $kontak         = getDisplayValue($pasien['kontak'] ?? null);
    $kontak_darurat = getDisplayValue($pasien['kontak_darurat'] ?? null);
    $nik            = getDisplayValue($pasien['nik'] ?? null);
    $no_bpjs        = getDisplayValue($pasien['no_bpjs'] ?? null);
    $propinsi       = getDisplayValue($pasien['propinsi'] ?? null);
    $kabupaten      = getDisplayValue($pasien['kabupaten'] ?? null);
    $kecamatan      = getDisplayValue($pasien['kecamatan'] ?? null);
    $desa           = getDisplayValue($pasien['desa'] ?? null);
    $alamat         = getDisplayValue($pasien['alamat'] ?? null);
    $perkawinan     = getDisplayValue($pasien['perkawinan'] ?? null);

    //Menghitung Usia Dan Format Tanggal Lahir
    $usia                    = hitungUsia($tanggal_lahir);
    $tanggal_lahir_formatted = formatTanggalLahir($tanggal_lahir);
    $PatientBirthDate = date('Ymd',strtotime($tanggal_lahir));

    // =====================================================
    // Permintaan Pemeriksaan
    // =====================================================
    $pemeriksaan_arry = json_decode($permintaan_pemeriksaan, true);
    $nama_pemeriksaan        = "";
    $modalitas               = "";
    $pemeriksaan_code        = "";
    $pemeriksaan_description = "";
    $pemeriksaan_sys         = "";
    $bodysite_code           = "";
    $bodysite_description    = "";
    $bodysite_sys            = "";
    $modalitas_name          = "";
    foreach($pemeriksaan_arry as $pemeriksaan_list){
        $id_master_pemeriksaan   = $pemeriksaan_list['id_master_pemeriksaan'] ?? '-';
        $nama_pemeriksaan        = $pemeriksaan_list['nama_pemeriksaan'];
        $modalitas               = $pemeriksaan_list['modalitas'];
        $pemeriksaan_code        = $pemeriksaan_list['pemeriksaan_code'];
        $pemeriksaan_description = $pemeriksaan_list['pemeriksaan_description'];
        $pemeriksaan_sys         = $pemeriksaan_list['pemeriksaan_sys'];
        $bodysite_code           = $pemeriksaan_list['bodysite_code'];
        $bodysite_description    = $pemeriksaan_list['bodysite_description'];
        $bodysite_sys            = $pemeriksaan_list['bodysite_sys'];
        $modalitas_name          = $modalitas_list[$modalitas] ?? '-';
    }

    // ===========================================
    // Klinis Pasien
    // ===========================================
    $nama_klinis      = "";
    $snomed_code      = "";
    $snomed_display   = "";
    $klinis_arry = json_decode($klinis, true);
    foreach($klinis_arry as $klinis_list){
        $nama_klinis      = $klinis_list['nama_klinis'];
        $snomed_code      = $klinis_list['snomed_code'];
        $snomed_display   = $klinis_list['snomed_display'];
    }

    // Format Nama pasien
    $nama_pasien_format = formatPatientNameDICOM($nama_pasien);
?>
    <input type="hidden" name="id_radiologi" value="<?php echo "$id_radiologi"; ?>">
    <div class="row mb-2">
        <div class="col-12">
            <label for="IssuerOfPatientID"><small>Nama Faskes</small></label>
            <input type="text" class="form-control" name="IssuerOfPatientID" id="IssuerOfPatientID" value="<?php echo $company_name;?>">
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-12">
            <label for="AccessionNumber"><small><i>Accession Number</i></small></label>
            <input type="text" class="form-control" name="AccessionNumber" id="AccessionNumber" value="<?php echo $accession_number;?>">
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-12">
            <label for="PatientName"><small>Nama Pasien</small></label>
            <input type="text" class="form-control" name="PatientName" id="PatientName" value="<?php echo $nama_pasien_format;?>">
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-12">
            <label for="PatientID"><small>ID Pasien</small></label>
            <input type="text" class="form-control" name="PatientID" id="PatientID" value="<?php echo $id_pasien;?>">
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-12">
            <label for="PatientSex"><small>Gender / Jenis Kelamin</small></label>
            <input type="text" class="form-control" name="PatientSex" id="PatientSex" value="<?php echo $gender;?>">
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-12">
            <label for="PatientBirthDate"><small>Tanggal Lahir</small></label>
            <input type="text" class="form-control" name="PatientBirthDate" id="PatientBirthDate" value="<?php echo $PatientBirthDate; ?>">
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-12">
            <label for="Modality"><small><i>Modality</i></small></label>
            <input type="text" class="form-control" name="Modality" id="Modality" value="<?php echo $alat_pemeriksa;?>">
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-12">
            <label for="ScheduledStationAETitle"><small><i>Scheduled Station AE Title</i></small></label>
            <input type="text" class="form-control" name="ScheduledStationAETitle" id="ScheduledStationAETitle" value="USG01">
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-12">
            <label for="ScheduledProcedureStepStartDate"><small><i>Scheduled Procedure Step Start Date</i></small></label>
            <input type="text" class="form-control" name="ScheduledProcedureStepStartDate" id="ScheduledProcedureStepStartDate" value="<?php echo date('Ymd'); ?>">
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-12">
            <label for="ScheduledProcedureStepStartTime"><small><i>Scheduled Procedure Step Start Time</i></small></label>
            <input type="text" class="form-control" name="ScheduledProcedureStepStartTime" id="ScheduledProcedureStepStartTime" value="<?php echo date('His'); ?>">
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-12">
            <label for="ScheduledPerformingPhysicianName"><small><i>Dokter</i></small></label>
            <input type="text" class="form-control" name="ScheduledPerformingPhysicianName" id="ScheduledPerformingPhysicianName" value="<?php echo formatPatientNameDICOM($nama_dokter_penerima); ?>">
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-12">
            <label for="ScheduledProcedureStepDescription"><small><i>Pemeriksaan</i></small></label>
            <input type="text" class="form-control" name="ScheduledProcedureStepDescription" id="ScheduledProcedureStepDescription" value="<?php echo $nama_pemeriksaan; ?>">
        </div>
    </div>
    
    