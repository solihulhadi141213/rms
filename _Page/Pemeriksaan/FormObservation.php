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
    $id_observation           = $Data['id_observation'];
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
        $radiografer = "";
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
    $url_observation = $url_api . '/fhir-r4/v1/Observation';

    // ===========================================
    // PERMINTAAN PEMERIKSAAN
    // ===========================================
    
    //Buat Arry
    $pemeriksaan_arry = json_decode($permintaan_pemeriksaan, true);
    
    //Inisialisasi Variabel Pemeriksaan
    $id_master_pemeriksaan   = "";
    $nama_pemeriksaan        = "";
    $modalitas               = "";
    $pemeriksaan_code        = "";
    $pemeriksaan_description = "";
    $pemeriksaan_sys         = "";
    $bodysite_code           = "";
    $bodysite_description    = "";
    $bodysite_sys            = "";
    $report_code             = "";
    $report_description      = "";
    $report_sys              = "";

    // Looping
    foreach($pemeriksaan_arry as $pemeriksaan_list){
        $id_master_pemeriksaan   = $pemeriksaan_list['id_master_pemeriksaan'] ?? "";
        $nama_pemeriksaan        = $pemeriksaan_list['nama_pemeriksaan'] ?? "";
        $modalitas               = $pemeriksaan_list['modalitas'] ?? "";
        $pemeriksaan_code        = $pemeriksaan_list['pemeriksaan_code'] ?? "";
        $pemeriksaan_description = $pemeriksaan_list['pemeriksaan_description'] ?? "";
        $pemeriksaan_sys         = $pemeriksaan_list['pemeriksaan_sys'] ?? "";
        $bodysite_code           = $pemeriksaan_list['bodysite_code'] ?? "";
        $bodysite_description    = $pemeriksaan_list['bodysite_description'] ?? "";
        $bodysite_sys            = $pemeriksaan_list['bodysite_sys'] ?? "";
        $report_code             = $pemeriksaan_list['report_code'] ?? "";
        $report_description      = $pemeriksaan_list['report_description'] ?? "";
        $report_sys              = $pemeriksaan_list['report_sys'] ?? "";
    }

    // ==============================================================
    // id_observation, resourceType, status
    // ==============================================================
    echo '
        <input type="hidden" name="id_radiologi" value="'.$id_radiologi.'">
        <div class="row mb-2 mt-3">
            <div class="col-12">
                <h6 class="pb-2"><b>A. Informasi Umum <i>Observation</i></b></h6>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <label for="id_observation"><small>ID Observation</small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="text" name="id_observation" id="id_observation" class="form-control" value="'.$id_observation.'">
                <small class="text text-grayish">Jika terisi maka sistem akan melakukan uptdae berdasarkan ID Observation tersebut</small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <label for="resourceType"><small>Resource Type</small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="text" name="resourceType" id="resourceType" class="form-control" value="Observation" readonly>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <label for="status"><small>Status</small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <select name="status" id="status" class="form-control" required>
                    <option value="registered">Terdaftar</option>
                    <option value="preliminary">Sementara</option>
                    <option selected value="final">Final / Resmi</option>
                    <option value="amended">Refisi</option>
                    <option value="corrected">Dikoreksi</option>
                    <option value="cancelled">Dibatalkan</option>
                    <option value="entered-in-error">Salah Input</option>
                    <option value="unknown">Tidak Diketahui</option>
                </select>
            </div>
        </div>
    ';

    // ==============================================================
    // KATEGORI
    // ==============================================================
    echo '
        <div class="row mb-2 mt-3">
            <div class="col-12">
                <h6 class="pb-2"><b>B. Kategori</b></h6>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <label for="category_coding_system"><small><i>Category System</i></small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="text" name="category_coding_system" id="category_coding_system" class="form-control" value="http://terminology.hl7.org/CodeSystem/observation-category">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <label for="category_coding_code"><small><i>Category Code</i></small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="text" name="category_coding_code" id="category_coding_code" class="form-control" value="imaging">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <label for="category_coding_display"><small><i>Category Display</i></small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="text" name="category_coding_display" id="category_coding_display" class="form-control" value="imaging">
            </div>
        </div>
    ';
    
    // ==============================================================
    // REPORT (code - coding)
    // ==============================================================
    echo '
        <div class="row mb-2 mt-3">
            <div class="col-12">
                <h6 class="pb-2"><b>C. <i>Diagnostic Report (Code)</i></b></h6>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small>Nama Pemeriksaan</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7"><small class="text text-grayish">'.$nama_pemeriksaan.'</small></div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small>Kode Pemeriksaan</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7"><small class="text text-grayish">'.$pemeriksaan_code.' | <i>'.$pemeriksaan_description.'</i></small></div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small><i>Code System</i></small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7"><small class="text text-grayish">'.$pemeriksaan_sys.'</small></div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <label for="code_coding_code"><small><i>Code</i></small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="text" name="code_coding_code" id="code_coding_code" class="form-control" value="'.$report_sys.'">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <label for="code_coding_display"><small><i>Category Display</i></small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="text" name="code_coding_display" id="code_coding_display" class="form-control" value="'.$report_description.'">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <label for="code_coding_system"><small><i>System</i></small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="text" name="code_coding_system" id="code_coding_system" class="form-control" value="'.$report_sys.'">
            </div>
        </div>
    ';

    // ==============================================================
    // PASIEN
    // ==============================================================
    echo '
        <div class="row mb-2 mt-3">
            <div class="col-12">
                <h6 class="pb-2"><b>D. Informasi Pasien</b></h6>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <label for="subject_reference"><small>IHS Pasien</small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="text" name="subject_reference" id="subject_reference" class="form-control" value="'.$id_ihs.'">
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

    // ==============================================================
    // ENCOUNTER
    // ==============================================================
    echo '
        <div class="row mb-2 mt-3">
            <div class="col-12">
                <h6 class="pb-2"><b><i>E. Referensi Lain</i></b></h6>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <label for="encounter_reference"><small>Encounter</small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="text" name="encounter_reference" id="encounter_reference" class="form-control" value="'.$id_encounter.'">
            </div>
        </div>
    ';

    // ==============================================================
    // DOKTER PENERIMA
    // ==============================================================
    echo '
        <div class="row mb-2 mt-3">
            <div class="col-12">
                <h6 class="pb-2"><b>F. Dokter Penerima</b></h6>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <label for="performer_reference"><small>ID Practitioner</small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="text" name="performer_reference" id="performer_reference" class="form-control" value="'.$ihs_dokter_penerima.'">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <label for="performer_reference_name"><small>Nama Dokter</small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="text" name="performer_reference_name" id="performer_reference_name" class="form-control" value="'.$nama_dokter_penerima.'">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <label for="performer_reference_organization"><small>ID Organization</small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="text" name="performer_reference_organization" id="performer_reference_organization" class="form-control" value="'.$organization_id.'">
            </div>
        </div>
    ';

    // ==============================================================
    // TEMUAN (valueString)
    // ==============================================================
    // Temukan 'finding' pada tabel  'radiologi_expertise'
    $finding = GetDetailData($Conn, 'radiologi_expertise', 'id_radiologi', $id_radiologi, 'finding'); 

    // Jika Tidak Ada Maka Cari Di tabel 'radiologi_expertise_usg' 
    if(empty($finding)){
        $finding = GetDetailData($Conn, 'radiologi_expertise_usg', 'id_radiologi', $id_radiologi, 'finding'); 

        // Jika Tidak Ditemukan Maka Generate Sendiri
        if(empty($finding)){
            $finding = "";
        }
    }
    echo '
        <div class="row mb-2 mt-3">
            <div class="col-12">
                <h6 class="pb-2">
                    <b>E. Temuan (<i>Finding</i>)</b>
                </h6>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <label for="valueString"><small>Temuan Dari hasil Pencitraan</small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
               <div id="editor-valueString" style="height: 150px;">'.$finding.'</div>
                <input type="hidden" name="valueString" id="valueString">
            </div>
        </div>
    ';
?>
