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
    $id_service_request     = $Data['id_service_request'];
    $id_procedure           = $Data['id_procedure'];
    $id_imaging_study       = $Data['id_imaging_study'];
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

    //Nama Radiografer
    if(empty($Data['radiografer'])){
        $radiografer = "-";
    }
    //Format Tanggal
    if (!empty($datetime_dikerjakan)) {
        $dt = new DateTime($datetime_dikerjakan, new DateTimeZone('Asia/Jakarta'));
        $datetime_iso = $dt->format('Y-m-d\TH:i:sP');
    } else {
        $datetime_iso = null;
    }

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

    // ======================================================
    // AMBIL KONFIGURASI SATUSEHAT AKTIF
    // ======================================================
    $status_active = 1;
    $stmt = $Conn->prepare("SELECT url_connection_satu_sehat, organization_id FROM connection_satu_sehat WHERE status_connection_satu_sehat = ?
    ");
    $stmt->bind_param("i", $status_active);
    $stmt->execute();
    $result = $stmt->get_result();
    $config = $result->fetch_assoc();
    $stmt->close();

    if (!$config) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Koneksi SATUSEHAT tidak ditemukan.'
        ]);
        exit;
    }

    $organization_id   = $config['organization_id'];
    $url_api           = rtrim($config['url_connection_satu_sehat'], '/');
    $url_imaging_study = $url_api . '/fhir-r4/v1/ImagingStudy';

    echo '
        <input type="hidden" name="id_radiologi" value="'.$id_radiologi.'">
        <div class="row mb-2 mt-3">
            <div class="col-12">
                <h6 class="border-bottom pb-2 text-primary">A. Informasi Umum Study</h6>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <label for="id_imaging_study"><small>ID Imaging Study</small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="text" name="id_imaging_study" id="id_imaging_study" class="form-control" value="'.$id_imaging_study.'">
                <small class="text text-grayish">Jika terisi maka sistem akan melakukan uptdae berdasarkan ID Imaging Study tersebut</small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <label for="resourceType"><small>Resource Type</small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="text" name="resourceType" id="resourceType" class="form-control" value="ImagingStudy" readonly>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <label for="status"><small>Status</small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <select name="status" id="status" class="form-control" required>
                    <option selected value="registered">Sudah didaftarkan</option>
                    <option value="available">Gambar tersedia</option>
                    <option value="cancelled">Dibatalkan</option>
                </select>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <label for="numberOfSeries"><small>Jumlah Seri Gambar</small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="text" name="numberOfSeries" id="numberOfSeries" class="form-control" value="1">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <label for="numberOfInstances"><small>Total Gambar</small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="text" name="numberOfInstances" id="numberOfInstances" class="form-control" value="1">
            </div>
        </div>
    ';
    echo '
        <div class="row mb-2 mt-3">
            <div class="col-12">
                <h6 class="border-bottom pb-2 text-primary">B. Accession Number</h6>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <label for="identifier_system"><small>Identifier System</small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="text" name="identifier_system" id="identifier_system" class="form-control" value="http://sys-ids.kemkes.go.id/radiology-accession-number">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <label for="identifier_value"><small>Identifier Value</small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="text" name="identifier_value" id="identifier_value" class="form-control" value="'.$accession_number.'">
            </div>
        </div>
    ';
    echo '
        <div class="row mb-2 mt-3">
            <div class="col-12">
                <h6 class="border-bottom pb-2 text-primary">C. Pasien</h6>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <label for="subject_reference"><small>IHS Pasien</small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="text" name="subject_reference" id="subject_reference" class="form-control" value="Patient/'.$id_ihs.'">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <label for="subject_display"><small>Nama Pasien</small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="text" name="subject_display" id="subject_display" class="form-control" value="'.$nama.'">
            </div>
        </div>
    ';

    echo '
        <div class="row mb-2 mt-3">
            <div class="col-12">
                <h6 class="border-bottom pb-2 text-primary">D. Modalitas</h6>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <label for="modality_system"><small><i>System</i></small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="text" name="modality_system" id="modality_system" class="form-control" value="http://dicom.nema.org/resources/ontology/DCM">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <label for="modality_code"><small><i>Code</i></small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="text" name="modality_code" id="modality_code" class="form-control" value="'.$alat_pemeriksa.'">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <label for="modality_display"><small><i>Display</i></small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="text" name="modality_display" id="modality_display" class="form-control" value="'.$nama_modalitas.'">
            </div>
        </div>
    ';
    // ===========================================
    // SERIES
    // ===========================================
    // Temukan Uid pada tabel  'radiologi_expertise'
    $series_uid = GetDetailData($Conn, 'radiologi_expertise', 'id_radiologi', $id_radiologi, 'study_instance_uid'); 

    // Jika Tidak Ada Maka Cari Di tabel 'radiologi_expertise_usg' 
    if(empty($series_uid)){
        $series_uid = GetDetailData($Conn, 'radiologi_expertise_usg', 'id_radiologi', $id_radiologi, 'study_instance_uid'); 

        // Jika Tidak Ditemukan Maka Generate Sendiri
        if(empty($series_uid)){
            $series_uid = '2.25.' . sprintf('%u', crc32(uniqid('', true)));
        }
    }
    
    echo '
        <div class="row mb-2 mt-3">
            <div class="col-12">
                <h6 class="border-bottom pb-2 text-primary">E. Series</h6>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <label for="series_uid"><small><i>UUID</i></small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="text" name="series_uid" id="series_uid" class="form-control" value="'.$series_uid.'">
            </div>
        </div>
    ';


    // ===========================================
    // PERMINTAAN PEMERIKSAAN
    // ===========================================
    
    //Buat Arry
    $pemeriksaan_arry = json_decode($permintaan_pemeriksaan, true);
    //Looping
    $pemeriksaan_code ="";
    foreach($pemeriksaan_arry as $pemeriksaan_list){
        $pemeriksaan_description = $pemeriksaan_list['pemeriksaan_description'];
    }
    echo '
        <div class="row mb-2">
            <div class="col-4">
                <label for="series_description"><small><i>Deskripsi</i></small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
            <input type="text" class="form-control" name="series_description" id="series_description" value="'.$pemeriksaan_description.'" required>
            </div>
        </div>
    ';

    echo '
        <div class="row mb-2 mt-3">
            <div class="col-12">
                <h6 class="border-bottom pb-2 text-primary">F. Tanggal/Jam Pemeriksaan</h6>
            </div>
        </div>
    ';
    echo '
        <div class="row mb-2">
            <div class="col-4">
                <label for="started_tanggal"><small>Tanggal</small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="date" class="form-control" name="started_tanggal" id="started_tanggal" value="'.date('Y-m-d', strtotime($Data['datetime_dikerjakan'])).'" required>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <label for="started_jam"><small>Jam</small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="time" class="form-control" name="started_jam" id="started_jam" value="'.date('H:i', strtotime($Data['datetime_dikerjakan'])).'" required>
            </div>
        </div>
    ';

    echo '
        <div class="row mb-2 mt-3">
            <div class="col-12">
                <h6 class="border-bottom pb-2 text-primary">G. Service Request</h6>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <label for="basedOn_reference"><small>ID Service Request</small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="text" class="form-control" name="basedOn_reference" id="basedOn_reference" value="'.$id_service_request.'" required>
            </div>
        </div>
    ';
     echo '
        <div class="row mb-2 mt-3">
            <div class="col-12">
                <h6 class="border-bottom pb-2 text-primary">H. Organization</h6>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <label for="organization_id"><small>ID Organization</small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="text" class="form-control" name="organization_id" id="organization_id" value="'.$organization_id.'" required>
            </div>
        </div>
    ';
?>
