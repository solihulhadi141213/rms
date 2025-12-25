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
    if(empty($data['radiografer'])){
        $radiografer = "-";
    }
    //Format Tanggal
    $datetime_diminta     = formatDateTimeStrict($Data['datetime_diminta']);
    $datetime_dikerjakan  = formatDateTimeStrict($Data['datetime_dikerjakan']);
    $datetime_hasil       = formatDateTimeStrict($Data['datetime_hasil']);
    $datetime_selesai     = formatDateTimeStrict($Data['datetime_selesai']);

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

    echo '
        <input type="hidden" name="id_radiologi" value="'.$id_radiologi.'">
        <div class="row mb-2 mt-3">
            <div class="col-12">
                <h6 class="border-bottom pb-2 text-primary">A. Informasi Umum Procedure</h6>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <label for="id_procedure"><small>ID Procedure</small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="text" name="id_procedure" id="id_procedure" class="form-control" value="'.$id_procedure.'">
                <small class="text text-grayish">Jika terisi maka sistem akan melakukan uptdae berdasarkan ID Procedure tersebut</small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <label for="resourceType"><small>Resource Type</small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="text" name="resourceType" id="resourceType" class="form-control" value="Procedure" readonly>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <label for="category_system"><small>Referensi Procedure</small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="text" name="category_system" id="category_system" class="form-control" value="http://snomed.info/sct">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <label for="category_code"><small>Code Procedure</small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="text" name="category_code" id="category_code" class="form-control" value="363679005">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <label for="category_display"><small>Display Procedure</small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="text" name="category_display" id="category_display" class="form-control" value="Imaging">
            </div>
        </div>
    ';
    echo '
        <div class="row mb-2 mt-3">
            <div class="col-12">
                <h6 class="border-bottom pb-2 text-primary">B. Status Tindakan</h6>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <label for="status"><small>Status Procedure</small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <select name="status" id="status" class="form-control" required>
                    <option value="preparation">Persiapan</option>
                    <option value="in-progress">Sedang dikerjakan</option>
                    <option selected value="completed">Selesai</option>
                    <option value="not-done">Tidak dilakukan</option>
                </select>
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
    echo '
        <div class="row mb-2 mt-3">
            <div class="col-12">
                <h6 class="border-bottom pb-2 text-primary">C. Permintaan Pemeriksaan</h6>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <label for="coding_system"><small><i>Reference</i></small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
            <input type="text" class="form-control" name="coding_system" id="coding_system" value="'.$pemeriksaan_sys.'" required>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <label for="coding_code"><small><i>Code</i></small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="text" class="form-control" name="coding_code" id="coding_code" value="'.$pemeriksaan_code.'" required>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <label for="coding_display"><small><i>Display</i></small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="text" class="form-control" name="coding_display" id="coding_display" value="'.$pemeriksaan_description.'" required>
            </div>
        </div>
    ';
    echo '
        <div class="row mb-2 mt-3">
            <div class="col-12">
                <h6 class="border-bottom pb-2 text-primary">D. Body Site</h6>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <label for="bodySite_coding_system"><small><i>Referense</i></small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
            <input type="text" class="form-control" name="bodySite_coding_system" id="bodySite_coding_system" value="'.$bodysite_sys.'" required>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <label for="bodySite_coding_code"><small><i>Code</i></small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="text" class="form-control" name="bodySite_coding_code" id="bodySite_coding_code" value="'.$bodysite_code.'" required>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <label for="bodySite_coding_display"><small><i>Display</i></small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="text" class="form-control" name="bodySite_coding_display" id="bodySite_coding_display" value="'.$bodysite_description.'" required>
            </div>
        </div>
    ';
    echo '
        <div class="row mb-2 mt-3">
            <div class="col-12">
                <h6 class="border-bottom pb-2 text-primary">E. IHS Pasien & Encounter</h6>
            </div>
        </div>
    ';
    echo '
        <div class="row mb-2">
            <div class="col-4">
                <label for="subject_reference"><small>IHS Pasien</small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="text" class="form-control" name="subject_reference" id="subject_reference" value="'.$id_ihs.'" required>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <label for="encounter_reference"><small>ID Encounter</small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="text" class="form-control" name="encounter_reference" id="encounter_reference" value="'.$id_encounter.'" required>
            </div>
        </div>
    ';

    echo '
        <div class="row mb-2 mt-3">
            <div class="col-12">
                <h6 class="border-bottom pb-2 text-primary">F. Tanggal/Jam Pelaksanaan</h6>
            </div>
        </div>
    ';
    echo '
        <div class="row mb-2">
            <div class="col-4">
                <label for="performedDateTime_tanggal"><small>Tanggal</small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="date" class="form-control" name="performedDateTime_tanggal" id="performedDateTime_tanggal" value="'.date('Y-m-d', strtotime($Data['datetime_dikerjakan'])).'" required>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <label for="performedDateTime_jam"><small>Jam</small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="text" class="form-control" name="performedDateTime_jam" id="performedDateTime_jam" value="'.date('H:i', strtotime($Data['datetime_dikerjakan'])).'" required>
            </div>
        </div>
    ';

    echo '
        <div class="row mb-2 mt-3">
            <div class="col-12">
                <h6 class="border-bottom pb-2 text-primary">G. Dokter Radiologi</h6>
            </div>
        </div>
    ';
    echo '
        <div class="row mb-2">
            <div class="col-4">
                <label for="performer_actor_reference"><small>ID Practitioner</small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="text" class="form-control" name="performer_actor_reference" id="performer_actor_reference" value="'.$ihs_dokter_penerima.'" required>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <label for="performer_actor_display"><small>Nama Dokter</small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="text" class="form-control" name="performer_actor_display" id="performer_actor_display" value="'.$nama_dokter_penerima.'" required>
            </div>
        </div>
    ';

    echo '
        <div class="row mb-2 mt-3">
            <div class="col-12">
                <h6 class="border-bottom pb-2 text-primary">H. Service Request</h6>
            </div>
        </div>
    ';
    echo '
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
?>
