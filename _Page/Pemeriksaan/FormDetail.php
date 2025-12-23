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
    // IDENTITAS PASIEN
    // ===========================================
    echo '
        <div class="row mb-2 border-1 border-bottom">
            <div class="col-12 mb-2">
                <small>
                    <b>A. Identitas Pasien</b>
                </small>
            </div>
        </div>
    ';

    echo '
        <div class="row mb-3 border-1 border-bottom">
            <div class="col-md-4">
                <div class="row mb-2">
                    <div class="col-5"><small>No.RM</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-6">
                        <small class="text text-grayish text-long">'.$id_pasien.'</small>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-5"><small>IHS Pasien</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-6">
                        <small class="text text-grayish text-long">'.$id_ihs.'</small>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-5"><small>NIK / KTP</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-6">
                        <small class="text text-grayish text-long">'.$nik.'</small>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-5"><small>No.BPJS</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-6">
                        <small class="text text-grayish text-long">'.$no_bpjs.'</small>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-5"><small>Nama Lengkap</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-6">
                        <small class="text text-grayish text-long">'.$nama.'</small>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-5"><small>Gender</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-6">
                        <small class="text text-grayish text-long">'.$gender.'</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="row mb-2">
                    <div class="col-5"><small>Tempat Lahir</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-6">
                        <small class="text text-grayish text-long">'.$kontak.'</small>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-5"><small>Tanggal Lahir</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-6">
                        <small class="text text-grayish text-long">'.$tanggal_lahir_formatted.'</small>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-5"><small>Usia Sekarang</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-6">
                        <small class="text text-grayish text-long">'.$usia.'</small>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-5"><small>Kontak Pasien</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-6">
                        <small class="text text-grayish text-long">'.$kontak.'</small>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-5"><small>Kontak Darurat</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-6">
                        <small class="text text-grayish text-long">'.$kontak_darurat.'</small>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-5"><small>Status Pernikahan</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-6">
                        <small class="text text-grayish text-long">'.$perkawinan.'</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="row mb-2">
                    <div class="col-5"><small>Provinsi</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-6">
                        <small class="text text-grayish text-long">'.$propinsi.'</small>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-5"><small>Kab / Kota</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-6">
                        <small class="text text-grayish text-long">'.$kabupaten.'</small>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-5"><small>Kecamatan</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-6">
                        <small class="text text-grayish text-long">'.$kecamatan.'</small>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-5"><small>Kelurahan / Desa</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-6">
                        <small class="text text-grayish text-long">'.$desa.'</small>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-5"><small>Alamat</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-6">
                        <small class="text text-grayish text-long">'.$alamat.'</small>
                    </div>
                </div>
            </div>
        </div>
    ';

    // ===========================================
    // INFORMASI UMUM PEMERIKSAAN
    // ===========================================
    echo '
        <div class="row mb-2 border-1 border-bottom">
            <div class="col-12 mb-2">
                <small>
                    <b>B. Informasi Umum Pemeriksaan</b>
                </small>
            </div>
        </div>
    ';
    echo '
        <div class="row mb-3 border-1 border-bottom">
            <div class="col-md-4">
                <div class="row mb-2">
                    <div class="col-5"><small>ID Encounter</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-6">
                        <small class="text text-grayish text-long"><small>'.$id_encounter.'</small></small>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-5"><small>Accession Number</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-6">
                        <small class="text text-grayish text-long"><small>'.$accession_number.'</small></small>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-5"><small>Kunjungan</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-6">
                        <small class="text text-grayish text-long">'.$tujuan.'</small>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-5"><small>Pembayaran</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-6">
                        <small class="text text-grayish text-long">'.$pembayaran.'</small>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-5"><small>Prioritisasi</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-6">
                        <small class="text text-grayish text-long">'.$priority_name.'</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="row mb-2">
                    <div class="col-5"><small>Tanggal Permintan</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-6">
                        <small class="text text-grayish text-long">'.$datetime_diminta.'</small>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-5"><small>Tanggal Dikerjakan</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-6">
                        <small class="text text-grayish text-long">'.$datetime_dikerjakan.'</small>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-5"><small>Keluar Hasil</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-6">
                        <small class="text text-grayish text-long">'.$datetime_hasil.'</small>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-5"><small>Selesai Diserahkan</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-6">
                        <small class="text text-grayish text-long">'.$datetime_selesai.'</small>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-5"><small>Pesan / Keterangan</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-6">
                        <small class="text text-grayish text-long">'.$pesan.'</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="row mb-2">
                    <div class="col-5"><small>Diagnosa Awal</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-6">
                        <small class="text text-grayish text-long">'.$diagnosa_awal.'</small>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-5"><small>Asal Kiriman</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-6">
                        <small class="text text-grayish text-long">'.$asal_kiriman.'</small>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-5"><small>Modalitas / Alat</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-6">
                        <small class="text text-grayish text-long">'.$nama_modalitas.'</small>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-5"><small>Radiografer</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-6">
                        <small class="text text-grayish text-long">'.$radiografer.'</small>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-5"><small>Status</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-6">
                        <small class="text text-grayish text-long">'.$label_status.'</small>
                    </div>
                </div>
            </div>
        </div>
    ';
    
    // ===========================================
    // KELINIS PASIEN
    // ===========================================
    
    //Buat Arry
    $klinis_arry = json_decode($klinis, true);

    echo '
        <div class="row mb-2 border-1 border-bottom">
            <div class="col-9 mb-2">
                <small>
                    <b>C. Klinis Pasien</b>
                </small>
            </div>
            <div class="col-3 text-end mb-2">
                <button type="button" class="btn btn-sm btn-floating btn-primary">
                    <i class="bi bi-plus"></i>
                </button>
            </div>
        </div>
    ';

    //Bentuk Tabel
    echo '<div class="row mb-3">';
    echo '  <div class="col-12">';
    echo '      <div class="table table-responsive">';
    echo '          <table class="table table-borderless">';
    echo '              
                        <thead>
                            <tr>
                                <td><small><b>Nama Klinis</b></small></td>
                                <td><small><b>Kategori</b></small></td>
                                <td><small><b><i>Code</i></b></small></td>
                                <td><small><b><i>Description</i></b></small></td>
                                <td class="text-center"><small><b><i>Opsi</i></b></small></td>
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
                            <td><small>'.$nama_klinis.'</small></td>
                            <td><small>'.$kategori.'</small></td>
                            <td>
                                <small class="underscore_doted" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="http://snomed.info/sct">
                                    <i>'.$snomed_code.'</i>
                                </small>
                            </td>
                            <td>
                                <small class="underscore_doted" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="http://snomed.info/sct">
                                    <i>'.$snomed_display.'</i>
                                </small>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-floating btn-outline-primary modal_edit_kesan" data-id="'.$id_radiologi.'" data-id="'.$id_radiologi.'">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                            </td>
                        </tr>
        ';
    }

    echo '              </tbody>';
    echo '          </table>';
    echo '      </div>';
    echo '  </div>';
    echo '</div>';
    
    // ===========================================
    // PERMINTAAN PEMERIKSAAN
    // ===========================================
    
    //Buat Arry
    $pemeriksaan_arry = json_decode($permintaan_pemeriksaan, true);

    echo '
        <div class="row mb-2 border-1 border-bottom">
            <div class="col-9 mb-2">
                <small>
                    <b>D. Permintaan Pemeriksaan</b>
                </small>
            </div>
            <div class="col-3 text-end mb-2">
                <button type="button" class="btn btn-sm btn-floating btn-outline-primary">
                    <i class="bi bi-pencil"></i>
                </button>
            </div>
        </div>
    ';

    //Looping
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
        echo '
            <div class="row mb-3 border-1 border-bottom">
                <div class="col-md-4">
                    <div class="row mb-2">
                        <div class="col-5"><small>Nama Pemeriksaan</small></div>
                        <div class="col-1"><small>:</small></div>
                        <div class="col-6">
                            <small class="text text-grayish text-long">'.$nama_pemeriksaan.'</small>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5"><small>Modalitas / Alat</small></div>
                        <div class="col-1"><small>:</small></div>
                        <div class="col-6">
                            <small class="text text-grayish text-long">'.$modalitas_name.'</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="row mb-2">
                        <div class="col-5"><small>Kode Pemeriksaan</small></div>
                        <div class="col-1"><small>:</small></div>
                        <div class="col-6">
                            <small class="text text-grayish text-long underscore_doted" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="'.$pemeriksaan_sys.'">
                                '.$pemeriksaan_code.'
                            </small>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5"><small>Deskripsi Pemeriksaan</small></div>
                        <div class="col-1"><small>:</small></div>
                        <div class="col-6">
                            <small class="text text-grayish text-long">'.$pemeriksaan_description.'</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="row mb-2">
                        <div class="col-5"><small>Kode <i>Body Site</i></small></div>
                        <div class="col-1"><small>:</small></div>
                        <div class="col-6">
                            <small class="text text-grayish text-long underscore_doted" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="'.$bodysite_sys.'">
                                '.$bodysite_code.'
                            </small>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5"><small>Deskripsi <i>Body Site</i></small></div>
                        <div class="col-1"><small>:</small></div>
                        <div class="col-6">
                            <small class="text text-grayish text-long">'.$bodysite_description.'</small>
                        </div>
                    </div>
                </div>
            </div>
        ';
    }
    // ===========================================
    // SERVICE REQUEST
    // ===========================================
    if(!empty($Data['id_service_request'])){
        $id_service_request       = $Data['id_service_request'];
        $label_id_service_request = '
            <a href="javascript:void(0);" class="modal_detail_service_request" data-id="'.$id_service_request.'">
                <small class="text text-primary underscore_doted">
                    <small>'.$id_service_request.'</small>
                </small>
            </a>
        ';
    }else{
        $id_service_request       = "";
        $label_id_service_request = '<small class="text text-danger"><small>Belum Ada</small></small>';
    }
    echo '
        <div class="row mb-2 border-1 border-bottom">
            <div class="col-9 mb-2">
                <small>
                    <b><i>E. Service Request</i></b>
                </small>
            </div>
            <div class="col-3 text-end mb-2">
                <button type="button" class="btn btn-sm btn-floating btn-primary modal_service_request" data-id="'.$id_radiologi.'">
                    <i class="bi bi-plus"></i>
                </button>
            </div>
        </div>
    ';
     echo '
        <div class="row mb-3 border-1 border-bottom">
            <div class="col-md-4">
                <div class="row mb-2">
                    <div class="col-5"><small>ID Service Request</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-6">'.$label_id_service_request.'</div>
                </div>
                </div>
            </div>
        </div>
    ';

    // ===========================================
    // PROCEDURE
    // ===========================================
    echo '
        <div class="row mb-2 border-1 border-bottom">
            <div class="col-9 mb-2">
                <small>
                    <b><i>F. Procedure</i></b>
                </small>
            </div>
            <div class="col-3 text-end mb-2">
                <button type="button" class="btn btn-sm btn-floating btn-primary">
                    <i class="bi bi-plus"></i>
                </button>
            </div>
        </div>
    ';
    
?>