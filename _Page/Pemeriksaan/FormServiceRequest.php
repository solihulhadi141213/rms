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

    //klasifikasi prioritas
    $priority_list = [
        'routine' => 'Biasa',
        'urgent'  => 'Segera',
        'stat'    => 'Gawat'
    ];
    $priority_name = $priority_list[$priority] ?? '-';

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
    
    //Routing Status
    if($status_pemeriksaan=="Diminta"){
        $label_status = '<span class="badge bg-warning">Diminta</span>';
    }else{
        if($status_pemeriksaan=="Dikerjakan"){
            $label_status = '<span class="badge bg-info">Dikerjakan</span>';
        }else{
            if($status_pemeriksaan=="Hasil"){
                $label_status = '<span class="badge bg-primary">Hasil</span>';
            }else{
                if($status_pemeriksaan=="Selesai"){
                    $label_status = '<span class="badge bg-success">Selesai</span>';
                }else{
                    if($status_pemeriksaan=="Batal"){
                        $label_status = '<span class="badge bg-danger">Batal</span>';
                    }else{
                        $label_status = '<span class="badge bg-dark">None</span>';
                    }
                }
            }
        }
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
        <input type="hidden" name="id_radiologi" value="'.$id_radiologi.'">
        <div class="row mb-2 mt-3">
            <div class="col-12">
                <b><small>A. Status Permintaan</small></b>
            </div>
        </div>
    ';
    echo '
        <div class="row mb-2">
            <div class="col-4">
                <label for="id_service_request"><small>ID Service Request</small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="text" name="id_service_request" id="id_service_request" class="form-control" value="'.$id_service_request.'">
                <small class="text text-grayish">Jika terisi maka sistem akan melakukan uptdae berdasarkan ID service request tersebut</small>
            </div>
        </div>
    ';
    echo '
        <div class="row mb-2">
            <div class="col-4">
                <label for="intent"><small>Maksud & Tujuan</small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <select name="intent" id="intent" class="form-control" required>
                    <option value="order">Perintah resmi</option>
                    <option value="proposal">Usulan</option>
                    <option value="plan">Rencana</option>
                    <option value="directive">Instruksi kebijakan</option>
                    <option value="original-order">Order Awal (Jika ada perubahan)</option>
                    <option value="reflex-order">Otomatis (Dipicu hasil sebelumnya)</option>
                    <option value="filler-order">Order Pelaksana (Dari unit pelaksana)</option>
                    <option value="instance-order">Order instance</option>
                    <option value="option">Opsi (Alternatif tindakan)</option>
                </select>
            </div>
        </div>
    ';
    echo '
        <div class="row mb-2">
            <div class="col-4">
                <label for="status"><small>Status</small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <select name="status" id="status" class="form-control" required>
                    <option value="active">Permintaan Aktif</option>
                    <option value="completed">Sudah Dilayani / Selesai</option>
                    <option value="revoked">Dibatalkan</option>
                </select>
            </div>
        </div>
    ';
    echo '
        <div class="row mb-2 mt-3">
            <div class="col-12">
                <b><small>B. Kategori (<i>Category</i>)</small></b>
            </div>
        </div>
    ';
    echo '
        <div class="row mb-2">
            <div class="col-4">
                <label for="category_system"><small><i>System / Reference</i></small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="url" class="form-control" name="category_system" id="category_system" value="http://snomed.info/sct" required>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <label for="category_code"><small>Kode</small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
               <input type="text" class="form-control" name="category_code" id="category_code" value="363679005" required>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <label for="category_display"><small>Deskripsi</small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
               <input type="text" class="form-control" name="category_display" id="category_display" value="Imaging" required>
            </div>
        </div>
    ';
    echo '
        <div class="row mb-2 mt-3">
            <div class="col-12">
                <b><small>C. Permintaan Pemeriksaan</small></b>
            </div>
        </div>
    ';
    echo '
        <div class="row mb-2">
            <div class="col-4">
                <label for="coding_system"><small><i>System / Reference</i></small></label>
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
                <b><small>D. Body Site</small></b>
            </div>
        </div>
    ';
    echo '
        <div class="row mb-2">
            <div class="col-4">
                <label for="bodySite_coding_system"><small><i>System / Reference</i></small></label>
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
                <b><small>E. IHS Pasien & Encounter</small></b>
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
                <b><small>F. Dokter Pengirim</small></b>
            </div>
        </div>
    ';
    echo '
        <div class="row mb-2">
            <div class="col-4">
                <label for="requester_reference"><small>ID Practitioner</small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="text" class="form-control" name="requester_reference" id="requester_reference" value="'.$ihs_dokter_pengirim.'" required>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <label for="requester_display"><small>Nama Dokter</small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="text" class="form-control" name="requester_display" id="requester_display" value="'.$nama_dokter_pengirim.'" required>
            </div>
        </div>
    ';
    echo '
        <div class="row mb-2 mt-3">
            <div class="col-12">
                <b><small>G. Tanggal/Jam Permintaan</small></b>
            </div>
        </div>
    ';
    echo '
        <div class="row mb-2">
            <div class="col-4">
                <label for="authoredOn_tanggal"><small>Tanggal Permintaan</small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="date" class="form-control" name="authoredOn_tanggal" id="authoredOn_tanggal" value="'.date('Y-m-d', strtotime($Data['datetime_diminta'])).'" required>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <label for="authoredOn_jam"><small>Jam Permintaan</small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="text" class="form-control" name="authoredOn_jam" id="authoredOn_jam" value="'.date('H:i', strtotime($Data['datetime_diminta'])).'" required>
            </div>
        </div>
    ';
    echo '
        <div class="row mb-2 mt-3">
            <div class="col-12">
                <b><small>H. Prioritas</small></b>
            </div>
        </div>
    ';
    $select_priority_1 = ($priority == "routine") ? "selected" : "";
    $select_priority_2 = ($priority == "urgent") ? "selected" : "";
    $select_priority_3 = ($priority == "stat") ? "selected" : "";
    echo '
        <div class="row mb-2">
            <div class="col-4">
                <label for="priority_sr"><small>Prioritas Permintaan</small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <select name="priority" id="priority_sr" class="form-control">
                    <option '.$select_priority_1.' value="routine">Biasa</option>
                    <option '.$select_priority_2.' value="urgent">Segera</option>
                    <option '.$select_priority_3.' value="stat">Gawat</option>
                </select>
            </div>
        </div>
    ';
    echo '
        <div class="row mb-2 mt-3">
            <div class="col-12">
                <b><small>I. <i>Note</i></small></b>
            </div>
        </div>
    ';
    echo '
        <div class="row mb-2">
            <div class="col-4">
                <label for="note_text"><small>Pesan / Keterangan Lain</small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <textarea class="form-control" name="note_text" id="note_text">'.$pesan.'</textarea>
            </div>
        </div>
    ';
    // ===========================================
    // KELINIS PASIEN
    // ===========================================
    
    //Buat Arry
    $klinis_arry = json_decode($klinis, true);

    echo '
        <div class="row mb-2 mt-3">
            <div class="col-12">
                <b><small>I. Klinis</small></b>
            </div>
        </div>
    ';
    //Bentuk Tabel
    echo '<div class="row mb-3">';
    echo '  <div class="col-12">';
    echo '      <div class="table table-responsive border-1 border-top">';
    echo '          <table class="table table-borderless">';
    echo '              
                        <thead>
                            <tr>
                                <td><small><b>No</b></small></td>
                                <td><small><b>Nama Klinis</b></small></td>
                                <td><small><b><i>Code</i></b></small></td>
                                <td><small><b><i>Display</i></b></small></td>
                                <td><small><b><i>System</i></b></small></td>
                            </tr>
                        </thead>
    ';
    echo '              <tbody>';

    //Looping
    $no_klinis = 1;
    foreach($klinis_arry as $klinis_list){
        $id_master_klinis = $klinis_list['id_master_klinis'];
        $nama_klinis      = $klinis_list['nama_klinis'];
        $snomed_code      = $klinis_list['snomed_code'];
        $snomed_display   = $klinis_list['snomed_display'];
        $kategori         = $klinis_list['kategori'];
        echo '
            <tr>
                <td><small>'.$no_klinis.'</small></td>
                <td>
                    <input type="text" class="form-control" name="reasonCode_text[]" id="reasonCode_text'.$no_klinis.'" value="'.$nama_klinis.'">
                </td>
                <td>
                    <input type="text" class="form-control" name="reasonCode_coding_code[]" id="reasonCode_coding_code_'.$no_klinis.'" value="'.$snomed_code.'">
                </td>
                <td>
                    <input type="text" class="form-control" name="reasonCode_coding_display[]" id="reasonCode_coding_display_'.$no_klinis.'" value="'.$snomed_display.'">
                </td>
                <td>
                    <input type="text" class="form-control" name="reasonCode_coding_system[]" id="reasonCode_coding_system_'.$no_klinis.'" value="http://snomed.info/sct">
                </td>
            </tr>
        ';
    }

    echo '              </tbody>';
    echo '          </table>';
    echo '      </div>';
    echo '  </div>';
    echo '</div>';
    
?>