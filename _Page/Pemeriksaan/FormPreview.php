<?php
    //koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";
    
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
                    <div class="alert alert-danger"><span>Sesi Akses Sudah Berakhir! Silahkan Login Ulang.</span></div>
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
                    <div class="alert alert-danger"><span>ID Pemeriksaan Tiidak Boleh Kosong!</span></div>
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
                <span>Terjadi kesalahan pada saat membuka data dari database!<br>Keterangan : '.$error.'</span>
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
    $id_service_request     = $Data['id_service_request'] ?? '<i class="text-danger">Informasi Tidak Tersedia</i>';
    $id_procedure           = $Data['id_procedure'] ?? '<i class="text-danger">Informasi Tidak Tersedia</i>';
    $id_imaging_study       = $Data['id_imaging_study'] ?? '<i class="text-danger">Informasi Tidak Tersedia</i>';
    $id_observation         = $Data['id_observation'] ?? '<i class="text-danger">Informasi Tidak Tersedia</i>';
    $id_diagnostic_report   = $Data['id_diagnostic_report'] ?? '<i class="text-danger">Informasi Tidak Tersedia</i>';
    $nama_pasien            = $Data['nama_pasien'];
    $priority               = $Data['priority'];
    $asal_kiriman           = $Data['asal_kiriman'];
    $alat_pemeriksa         = $Data['alat_pemeriksa'];
    $kode_dokter_pengirim   = !empty($Data['kode_dokter_pengirim']) ? $Data['kode_dokter_pengirim'] : "-";
    $ihs_dokter_pengirim    = !empty($Data['kode_dokter_pengirim']) ? $Data['ihs_dokter_pengirim'] : "-";
    $nama_dokter_pengirim   = !empty($Data['nama_dokter_pengirim']) ? $Data['nama_dokter_pengirim'] : "-";
    $kode_dokter_penerima   = !empty($Data['kode_dokter_penerima']) ? $Data['kode_dokter_penerima'] : "-";
    $ihs_dokter_penerima    = !empty($Data['ihs_dokter_penerima']) ? $Data['ihs_dokter_penerima'] : "-";
    $nama_dokter_penerima   = !empty($Data['nama_dokter_penerima']) ? $Data['nama_dokter_penerima'] : "-";
    $radiografer   = !empty($Data['radiografer']) ? $Data['radiografer'] : "-";
    $pesan                  = !empty($Data['pesan']) ? $Data['pesan'] : "-";
    $kesan                  = $Data['kesan'];
    $klinis                 = $Data['klinis'];
    $permintaan_pemeriksaan = $Data['permintaan_pemeriksaan'];
    $kv                     = !empty($Data['kv']) ? $Data['kv'] : "-";
    $ma                     = !empty($Data['ma']) ? $Data['ma'] : "-";
    $sec                    = !empty($Data['sec']) ? $Data['sec'] : "-";
    $tujuan                 = $Data['tujuan'];
    $pembayaran             = $Data['pembayaran'];
    $datetime_diminta       = !empty($Data['datetime_diminta']) ? $Data['datetime_diminta'] : "-";
    $datetime_dikerjakan    = !empty($Data['datetime_dikerjakan']) ? $Data['datetime_dikerjakan'] : "-";
    $datetime_hasil         = !empty($Data['datetime_hasil']) ? $Data['datetime_hasil'] : "-";
    $datetime_selesai       = !empty($Data['datetime_selesai']) ? $Data['datetime_selesai'] : "-";
    $status_pemeriksaan     = $Data['status_pemeriksaan'];
    $alasan_pembatalan      = $Data['alasan_pembatalan'];

    //Nama Radiografer
    if(empty($Data['radiografer'])){
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
                <span>Gagal mendapatkan token SIMRS!</span>
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
                        <span>Gagal memuat data kunjungan<br> Pesan : '.$data['response']['message'].'</span>
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
    $id_encounter    = getDisplayValue($metadata['id_encounter'] ?? null);
    $id_ihs          = getDisplayValue($pasien['id_ihs'] ?? null);
    $gender          = getDisplayValue($pasien['gender'] ?? null);
    $tempat_lahir    = getDisplayValue($pasien['tempat_lahir'] ?? null);
    $tanggal_lahir   = getDisplayValue($pasien['tanggal_lahir'] ?? null);
    $kontak          = getDisplayValue($pasien['kontak'] ?? null);
    $kontak_darurat  = getDisplayValue($pasien['kontak_darurat'] ?? null);
    $nik             = getDisplayValue($pasien['nik'] ?? null);
    $no_bpjs         = getDisplayValue($pasien['no_bpjs'] ?? null);
    $propinsi        = getDisplayValue($pasien['propinsi'] ?? null);
    $kabupaten       = getDisplayValue($pasien['kabupaten'] ?? null);
    $kecamatan       = getDisplayValue($pasien['kecamatan'] ?? null);
    $desa            = getDisplayValue($pasien['desa'] ?? null);
    $alamat          = getDisplayValue($pasien['alamat'] ?? null);
    $perkawinan      = getDisplayValue($pasien['perkawinan'] ?? null);
    $dpjp            = getDisplayValue($metadata['dokter'] ?? null);
    $penanggungjawab = getDisplayValue($metadata['penanggungjawab'] ?? null);

    //Menghitung Usia Dan Format Tanggal Lahir
    $usia                    = hitungUsia($tanggal_lahir);
    $tanggal_lahir_formatted = formatTanggalLahir($tanggal_lahir);
    
    // Untuk Direct Detail Radiology
    echo '
        <input type="hidden" name="id_radiologi" value="'.$id_radiologi.'">
    ';
?>
<div class="accordion accordion-flush" id="accordionFlushExample">
    <div class="accordion-item">
        <h2 class="accordion-header" id="flush-heading1">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse1" aria-expanded="true" aria-controls="flush-collapse1">
                <b>A. Permintaan Pemeriksaan Radiologi</b>
            </button>
        </h2>
        <div id="flush-collapse1" class="accordion-collapse collapse show" aria-labelledby="flush-heading1" data-bs-parent="#accordionFlushExample">
            <div class="accordion-body">
                <table class="table table-responsive table-sm table-bordered mt-3">
                    <tbody>
                        <tr>
                            <td><span>Nama Pasien</span></td>
                            <td class="text-grayish"><?php echo $nama_pasien; ?></td>
                        </tr>
                        <tr>
                            <td><span>Accession Number</span></td>
                            <td class="text-grayish"><?php echo $accession_number; ?></td>
                        </tr>
                        <tr>
                            <td><span>Tgl/Waktu Permintaan</span></td>
                            <td class="text-grayish"><?php echo $datetime_diminta; ?></td>
                        </tr>
                        <tr>
                            <td><span>Asal Permintaan</span></td>
                            <td class="text-grayish"><?php echo $asal_kiriman; ?></td>
                        </tr>
                        <tr>
                            <td><span>Prioritas</span></td>
                            <td class="text-grayish"><?php echo $priority_name; ?></td>
                        </tr>
                        <tr>
                            <td><span>Modalitas</span></td>
                            <td class="text-grayish">
                                <span class="text text-grayish" title="<?php echo $alat_pemeriksa; ?>">
                                    <?php echo "$nama_modalitas"; ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><span>Dokter Pengirim</span></td>
                            <td class="text-grayish"><?php echo "$nama_dokter_pengirim"; ?></td>
                        </tr>
                        <tr>
                            <td><span>Dokter Penerima</span></td>
                            <td class="text-grayish"><?php echo "$nama_dokter_penerima"; ?></td>
                        </tr>
                        <tr>
                            <td><span>Radiografer</span></td>
                            <td class="text-grayish"><?php echo "$radiografer"; ?></td>
                        </tr>
                        <tr>
                            <td><span>Pesan/Keterangan</span></td>
                            <td class="text-grayish"><?php echo "$pesan"; ?></td>
                        </tr>
                        <tr>
                            <td><span>Status</span></td>
                            <td class="text-grayish">
                                <span class="text text-grayish"><?php echo $label_status; ?></span>
                            </td>
                        </tr>
                        <?php
                            if(!empty($Data['alasan_pembatalan'])){
                                echo '
                                    <tr>
                                        <td><span>Alasan Pembatalan</span></td>
                                        <td class="text-grayish">
                                            <span class="text text-grayish">'.$alasan_pembatalan.'</span>
                                        </td>
                                    </tr>
                                ';
                            }
                        ?>
                    </tbody>
                </table>
                
            </div>
        </div>
    </div>
    <div class="accordion-item">
        <h2 class="accordion-header" id="flush-heading1_b">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse1_b" aria-expanded="true" aria-controls="flush-collapse1_b">
                <b>B. Informasi Pasien & Kunjungan</b>
            </button>
        </h2>
        <div id="flush-collapse1_b" class="accordion-collapse collapse" aria-labelledby="flush-heading1_b" data-bs-parent="#accordionFlushExample">
            <div class="accordion-body">
                <table class="table table-responsive table-sm table-bordered mt-3">
                    <tbody>
                        <tr>
                            <td colspan="2"><b>1. Identitas</b></td>
                        </tr>
                        <tr>
                            <td><span>No.RM</span></td>
                            <td class="text text-grayish"><?php echo $id_pasien; ?></td>
                        </tr>
                        <tr>
                            <td><i>IHS Patient</i></td>
                            <td class="text text-grayish"><?php echo $id_ihs; ?></td>
                        </tr>
                        <tr>
                            <td><span>Nama Pasien</span></td>
                            <td class="text-grayish"><?php echo $nama_pasien; ?></td>
                        </tr>
                        <tr>
                            <td><span>Jenis Kelamin</span></td>
                            <td class="text-grayish"><?php echo $gender; ?></td>
                        </tr>
                        <tr>
                            <td><span>TTL</span></td>
                            <td class="text-grayish"><?php echo "$tempat_lahir, $tanggal_lahir_formatted"; ?></td>
                        </tr>
                        <tr>
                            <td><span>Usia</span></td>
                            <td class="text-grayish"><?php echo "$usia"; ?></td>
                        </tr>
                        <tr>
                            <td><span>Status Pernikahan</span></td>
                            <td class="text-grayish"><?php echo "$perkawinan"; ?></td>
                        </tr>
                        <tr>
                            <td><span>NIK/KTP</span></td>
                            <td class="text-grayish"><?php echo "$nik"; ?></td>
                        </tr>
                        <tr>
                            <td><span>No.BPJS</span></td>
                            <td class="text-grayish"><?php echo "$no_bpjs"; ?></td>
                        </tr>
                        <tr>
                            <td colspan="2"><b>2. Alamat Tinggal</b></td>
                        </tr>
                        <tr>
                            <td><span>Provinsi</span></td>
                            <td class="text-grayish"><?php echo "$propinsi"; ?></td>
                        </tr>
                        <tr>
                            <td><span>Kab/Kota</span></td>
                            <td class="text-grayish"><?php echo "$kabupaten"; ?></td>
                        </tr>
                        <tr>
                            <td><span>Kecamatan</span></td>
                            <td class="text-grayish"><?php echo "$kecamatan"; ?></td>
                        </tr>
                        <tr>
                            <td><span>Desa</span></td>
                            <td class="text-grayish"><?php echo "$desa"; ?></td>
                        </tr>
                        <tr>
                            <td><span>Alamat</span></td>
                            <td class="text-grayish"><?php echo "$alamat"; ?></td>
                        </tr>
                        <tr>
                            <td colspan="2"><b>3. Informasi Kontak</b></td>
                        </tr>
                        <tr>
                            <td><span>Kontak Pribadi</span></td>
                            <td class="text-grayish"><?php echo "$kontak"; ?></td>
                        </tr>
                        <tr>
                            <td><span>Kontak Darurat</span></td>
                            <td class="text-grayish"><?php echo "$kontak_darurat"; ?></td>
                        </tr>
                        <tr>
                            <td><span>Penanggung Jawab</span></td>
                            <td class="text-grayish"><?php echo "$penanggungjawab"; ?></td>
                        </tr>
                        <tr>
                            <td colspan="2"><b>4. Informasi Kunjungan</b></td>
                        </tr>
                        <tr>
                            <td><i>ID. Encounter</i></td>
                            <td class="text-grayish"><?php echo $id_encounter; ?></td>
                        </tr>
                        <tr>
                            <td><span>Tujuan Kunjungan</span></td>
                            <td class="text-grayish"><?php echo $tujuan; ?></td>
                        </tr>
                        <tr>
                            <td><span>Metode Pembayaran</span></td>
                            <td class="text-grayish"><?php echo $pembayaran; ?></td>
                        </tr>
                        <tr>
                            <td><span>DPJP</span></td>
                            <td class="text-grayish"><?php echo $dpjp; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="accordion-item">
        <h2 class="accordion-header" id="flush-heading4">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse4" aria-expanded="false" aria-controls="flush-collapse4">
                <b>C. Dokter Pengirim & Penerima</b>
            </button>
        </h2>
        <div id="flush-collapse4" class="accordion-collapse collapse" aria-labelledby="flush-heading4" data-bs-parent="#accordionFlushExample">
            <div class="accordion-body">
                <table class="table table-responsive table-sm table-bordered mt-3">
                    <tbody>
                        <tr>
                            <td colspan="2"><b>1. Dokter Pengirim</b></td>
                        </tr>
                        <tr>
                            <td><span>ID Practitioner</span></td>
                            <td class="text-grayish"><?php echo "$ihs_dokter_pengirim"; ?></td>
                        </tr>
                        <tr>
                            <td><span>Kode Dokter</span></td>
                            <td class="text-grayish"><?php echo "$kode_dokter_pengirim"; ?></td>
                        </tr>
                        <tr>
                            <td><span>Nama Dokter</span></td>
                            <td class="text-grayish"><?php echo "$nama_dokter_pengirim"; ?></td>
                        </tr>
                        <tr>
                            <td colspan="2"><b>2. Dokter Penerima</b></td>
                        </tr>
                        <tr>
                            <td><span>ID Practitioner</span></td>
                            <td class="text-grayish"><?php echo "$ihs_dokter_penerima"; ?></td>
                        </tr>
                        <tr>
                            <td><span>Kode Dokter</span></td>
                            <td class="text-grayish"><?php echo "$kode_dokter_penerima"; ?></td>
                        </tr>
                        <tr>
                            <td><span>Nama Dokter</span></td>
                            <td class="text-grayish"><?php echo "$nama_dokter_penerima"; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="accordion-item">
        <h2 class="accordion-header" id="flush-heading5">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse5" aria-expanded="false" aria-controls="flush-collapse5">
                <b>D. Informasi Klinis</b>
            </button>
        </h2>
        <div id="flush-collapse5" class="accordion-collapse collapse" aria-labelledby="flush-heading5" data-bs-parent="#accordionFlushExample">
            <div class="accordion-body">
                <table class="table table-responsive table-sm table-bordered mt-3">
                    <tbody>
                        <?php
                            $no_klinis = 1;

                            /* Normalisasi JSON */
                            $klinis_ary = json_decode($klinis ?? '', true);

                            /* Jika bukan array / kosong → buat 1 data dummy */
                            if (!is_array($klinis_ary) || empty($klinis_ary)) {
                                $klinis_ary = [
                                    [
                                        'id_klinis'      => '-',
                                        'nama_klinis'    => '-',
                                        'snomed_code'    => '-',
                                        'snomed_display' => '-',
                                        'kategori'       => '-'
                                    ]
                                ];
                            }

                            foreach ($klinis_ary as $klinis_list) {
                                $id_klinis      = safe_text($klinis_list['id_klinis'] ?? null);
                                $nama_klinis    = safe_text($klinis_list['nama_klinis'] ?? null);
                                $snomed_code    = safe_text($klinis_list['snomed_code'] ?? null);
                                $snomed_display = safe_text($klinis_list['snomed_display'] ?? null);
                                $kategori       = safe_text($klinis_list['kategori'] ?? null);
                                
                                echo '
                                    <tr>
                                        <td colspan="3"><b>'.$no_klinis.'. '.$nama_klinis.'</b></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>ID Klinis</td>
                                        <td><span class="text text-grayish">'.$id_klinis.'</span></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>Nama Klinis</td>
                                        <td><span class="text text-grayish">'.$nama_klinis.'</span></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td><i>Code</i></td>
                                        <td><span class="text text-grayish">'.$snomed_code.'</span></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td><i>Display</i></td>
                                        <td><span class="text text-grayish">'.$snomed_display.'</span></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td><i>Category</i></td>
                                        <td><span class="text text-grayish">'.$kategori.'</span></td>
                                    </tr>
                                ';
                                $no_klinis++;
                            }
                        ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="accordion-item">
        <h2 class="accordion-header" id="flush-heading6">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse6" aria-expanded="false" aria-controls="flush-collapse6">
                <b>E. Permintaan Pemeriksaan</b>
            </button>
        </h2>
        <div id="flush-collapse6" class="accordion-collapse collapse" aria-labelledby="flush-heading6" data-bs-parent="#accordionFlushExample">
            <div class="accordion-body">
                <div class="table table-responsive mt-3">
                    <table class="table table-bordered table-sm">
                        <tbody>
                            <?php
                                if(!empty($permintaan_pemeriksaan)){
                                    $permintaan_pemeriksaan_arry = json_decode($permintaan_pemeriksaan, true);

                                    $no_pemeriksaan = 1;
                                    foreach ($permintaan_pemeriksaan_arry as $permintaan_pemeriksaan_list){
                                        echo '
                                            <tr>
                                                <td colspan="4">
                                                    <b>'.$no_pemeriksaan.'. '.$permintaan_pemeriksaan_list['nama_pemeriksaan'].'</b>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td colspan="3">
                                                    <i>a. Code System</i>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td>ID master</td>
                                                <td class="text text-grayish">'.$permintaan_pemeriksaan_list['id_master_pemeriksaan'].'</td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td>Nama Pemeriksaan</td>
                                                <td class="text text-grayish">'.$permintaan_pemeriksaan_list['nama_pemeriksaan'].'</td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td><i>Modality</i></td>
                                                <td class="text text-grayish">'.$permintaan_pemeriksaan_list['modalitas'].'</td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td><i>Code</i></td>
                                                <td class="text text-grayish">'.$permintaan_pemeriksaan_list['pemeriksaan_code'].'</td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td><i>Description</i></td>
                                                <td class="text text-grayish">'.$permintaan_pemeriksaan_list['pemeriksaan_description'].'</td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td><i>System</i></td>
                                                <td class="text text-grayish">'.$permintaan_pemeriksaan_list['pemeriksaan_sys'].'</td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td colspan="3">
                                                    <i>b. Body Site </i>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td><i>Code</i></td>
                                                <td class="text text-grayish">'.$permintaan_pemeriksaan_list['bodysite_code'].'</td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td><i>Description</i></td>
                                                <td class="text text-grayish">'.$permintaan_pemeriksaan_list['bodysite_description'].'</td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td><i>System</i></td>
                                                <td class="text text-grayish">'.$permintaan_pemeriksaan_list['bodysite_sys'].'</td>
                                            </tr>
                                        ';
                                        $no_pemeriksaan++;
                                    }
                                }
                                

                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
