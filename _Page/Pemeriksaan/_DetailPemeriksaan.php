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
    $id_service_request     = $Data['id_service_request'] ?? "-";
    $id_procedure           = $Data['id_procedure'] ?? "-";
    $id_imaging_study       = $Data['id_imaging_study'] ?? "-";
    $id_observation         = $Data['id_observation'] ?? "-";
    $id_diagnostic_report   = $Data['id_diagnostic_report'] ?? "-";
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
    $id_encounter    = getDisplayValue($metadata['id_encounter'] ?? null);
    $id_pasien       = getDisplayValue($pasien['id_pasien'] ?? null);
    $id_ihs          = getDisplayValue($pasien['id_ihs'] ?? null);
    $nama            = getDisplayValue($pasien['nama'] ?? null);
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
?>
<div class="row mb-3">
    <div class="col-md-12 text-end">
        <button type="button" class="btn btn-md btn-floating btn-dark back_to_data">
            <i class="bi bi-chevron-left"></i>
        </button>
        <button type="button" class="btn btn-md btn-floating btn-outline-dark reload_detail">
            <i class="bi bi-repeat"></i>
        </button>
        <button type="button" class="btn btn-md btn-floating btn-primary modal_cetak_laporan2">
            <i class="bi bi-printer"></i>
        </button>
    </div>
</div>
<div class="row">
    <!-- 
    ==============================================================
    KOLOM 1
    ==============================================================
    -->
    <div class="col-md-6">
        
        <!-- A. INFORMASI PASIEN -->
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-10">
                        <b class="card-title">A. Informasi Pasien</b>
                    </div>
                    <div class="col-2 text-end">
                        <button type="button" class="btn btn-sm btn-floating btn-secondary">
                            <i class="bi bi-arrow-left-right"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table table-responsive">
                    <table class="table table-bordered table-sm">
                        <tbody>
                            <tr>
                                <td colspan="2">
                                    <b>1. Informasi Umum</b>
                                </td>
                            </tr>
                            <tr>
                                <td>No.RM</td>
                                <td class="text text-grayish"><?php echo $id_pasien; ?></td>
                            </tr>
                            <tr>
                                <td>IHS Pasien</td>
                                <td class="text text-grayish"><?php echo $id_ihs; ?></td>
                            </tr>
                            <tr>
                                <td>NIK/KTP</td>
                                <td class="text text-grayish"><?php echo $nik; ?></td>
                            </tr>
                            <tr>
                                <td>No.BPJS</td>
                                <td class="text text-grayish"><?php echo $no_bpjs; ?></td>
                            </tr>
                            <tr>
                                <td>Nama Lengkap</td>
                                <td class="text text-grayish"><?php echo $nama; ?></td>
                            </tr>
                            <tr>
                                <td>Jenis Kelamin</td>
                                <td class="text text-grayish"><?php echo $gender; ?></td>
                            </tr>
                            <tr>
                                <td>Tempat Lahir</td>
                                <td class="text text-grayish"><?php echo $tempat_lahir; ?></td>
                            </tr>
                            <tr>
                                <td>Tanggal Lahir</td>
                                <td class="text text-grayish"><?php echo $tanggal_lahir_formatted; ?></td>
                            </tr>
                            <tr>
                                <td>Usia</td>
                                <td class="text text-grayish"><?php echo $usia; ?></td>
                            </tr>
                            <tr>
                                <td>Status Pernikahan</td>
                                <td class="text text-grayish"><?php echo $perkawinan; ?></td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <b>2. Alamat Tinggal</b>
                                </td>
                            </tr>
                            <tr>
                                <td>Provinsi</td>
                                <td class="text text-grayish"><?php echo $propinsi; ?></td>
                            </tr>
                            <tr>
                                <td>Kabupaten/Kota</td>
                                <td class="text text-grayish"><?php echo $kabupaten; ?></td>
                            </tr>
                            <tr>
                                <td>Kecamatan</td>
                                <td class="text text-grayish"><?php echo $kecamatan; ?></td>
                            </tr>
                            <tr>
                                <td>Kel/Desa</td>
                                <td class="text text-grayish"><?php echo $desa; ?></td>
                            </tr>
                            <tr>
                                <td>Alamat</td>
                                <td class="text text-grayish"><?php echo $alamat; ?></td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <b>4. Informasi Kontak</b>
                                </td>
                            </tr>
                            <tr>
                                <td>Kontak Pribadi</td>
                                <td class="text text-grayish"><?php echo $kontak; ?></td>
                            </tr>
                            <tr>
                                <td>Kontak Darurat</td>
                                <td class="text text-grayish"><?php echo $kontak_darurat; ?></td>
                            </tr>
                            <tr>
                                <td>Penanggung Jawab</td>
                                <td class="text text-grayish"><?php echo $penanggungjawab; ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <small>
                    <b>Keterangan : </b> Informasi ini hanya bisa diubah oleh Rekam Medis
                </small>
            </div>
        </div>

        <!-- B. INFORMASI KUNJUNGAN -->
        <div class="card">
            <div class="card-header">
                <b class="card-title">B. Informasi Kunjungan</b>
            </div>
            <div class="card-body">
                <div class="table table-responsive">
                    <table class="table table-bordered table-sm">
                        <tbody>
                            <tr>
                                <td>ID. Kunjungan</td>
                                <td class="text text-grayish"><?php echo $id_kunjungan; ?></td>
                            </tr>
                            <tr>
                                <td>ID. Encounter</td>
                                <td class="text text-grayish"><?php echo $id_encounter; ?></td>
                            </tr>
                            <tr>
                                <td>Tujuan Kunjungan</td>
                                <td class="text text-grayish"><?php echo $tujuan; ?></td>
                            </tr>
                            <tr>
                                <td>Metode Pembayaran</td>
                                <td class="text text-grayish"><?php echo $pembayaran; ?></td>
                            </tr>
                            <tr>
                                <td>DPJP</td>
                                <td class="text text-grayish"><?php echo $dpjp; ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- C. INFORMASI WAKTU PELAYANAN -->
         <?php
            $durasi_1 = hitungDurasi($datetime_diminta, $datetime_dikerjakan);
            $durasi_2 = hitungDurasi($datetime_diminta, $datetime_hasil);
            $durasi_3 = hitungDurasi($datetime_diminta, $datetime_selesai);

            // Menghitung Durasi Total
            if($datetime_diminta=="-"){
                $durasi_total = "-";
            }else{
                 if($datetime_dikerjakan=="-"){
                    $durasi_total = "0 m";
                }else{
                    if($datetime_hasil=="-"){
                        $durasi_total = hitungDurasi($datetime_diminta, $datetime_dikerjakan);
                    }else{
                        if($datetime_selesai=="-"){
                            $durasi_total = hitungDurasi($datetime_diminta, $datetime_hasil);
                        }else{
                            $durasi_total = hitungDurasi($datetime_diminta, $datetime_selesai);
                        }
                    }
                }
            }
         ?>
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-12">
                        <b class="card-title">C. Waktu Pelayanan</b>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <td class="text-center"><b>No</b></td>
                                <td><b>Keterangan</b></td>
                                <td><b>Tanggl/Jam</b></td>
                                <td class="text-center"><b>Durasi</b></td>
                                <td class="text-center"><b>Opsi</b></td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center">1</td>
                                <td><small>Permintaan Radiologi</small></td>
                                <td class="text-grayish"><small><?php echo $datetime_diminta; ?></small></td>
                                <td class="text-center text-grayish"><small>0 m</small></td>
                                <td class="text-center">
                                    <a href="javascript:void(0);" class="btn btn-sm btn-secondary btn-floating modal_ubah_waktu_pelayanan" data-kolom="datetime_diminta" data-id="<?php echo $id_radiologi; ?>">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center">2</td>
                                <td><small>Permintaan Dikerjakan</small></td>
                                <td class="text-grayish"><small><?php echo $datetime_dikerjakan; ?></small></td>
                                <td class="text-center text-grayish"><small><?php echo $durasi_1; ?></small></td>
                                <td class="text-center">
                                    <a href="javascript:void(0);" class="btn btn-sm btn-secondary btn-floating modal_ubah_waktu_pelayanan" data-kolom="datetime_dikerjakan" data-id="<?php echo $id_radiologi; ?>">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center">3</td>
                                <td><small>Pembuatan Hasil</small></td>
                                <td class="text-grayish"><small><?php echo $datetime_hasil; ?></small></td>
                                <td class="text-center text-grayish"><small><?php echo $durasi_2; ?></small></td>
                                <td class="text-center">
                                    <a href="javascript:void(0);" class="btn btn-sm btn-secondary btn-floating modal_ubah_waktu_pelayanan" data-kolom="datetime_hasil" data-id="<?php echo $id_radiologi; ?>">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center">3</td>
                                <td><small>Penyerahan Hasil</small></td>
                                <td class="text-grayish"><small><?php echo $datetime_selesai; ?></small></td>
                                <td class="text-center text-grayish"><small><?php echo $durasi_3; ?></small></td>
                                <td class="text-center">
                                    <a href="javascript:void(0);" class="btn btn-sm btn-secondary btn-floating modal_ubah_waktu_pelayanan" data-kolom="datetime_selesai" data-id="<?php echo $id_radiologi; ?>">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center"></td>
                                <td colspan="2">
                                    <b><small>Total Durasi Pelayanan</small></b>
                                </td>
                                <td class="text-center text-grayish"><small><?php echo $durasi_total; ?></small></td>
                                <td class="text-center"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- D. SATU SEHAT -->
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-12">
                        <b class="card-title">D. Satu Sehat</b>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <td class="text-center"><b>No</b></td>
                                <td><b><i>Resource</i></b></td>
                                <td><b><i>ID</i></b></td>
                                <td class="text-center"><b><i>Opsi</i></b></td>
                            </tr>
                        </thead>
                        <tbody>

                            <!-- Service Request -->
                            <tr>
                                <td class="text-center">1</td>
                                <td><i>Service Request</i></td>
                                <td class="text text-grayish">
                                    <small>
                                        <?php 
                                            if(empty($Data['id_service_request'])){
                                                echo '-';
                                            }else{
                                                echo '
                                                    <a href="javascript:void(0);" class="modal_detail_service_request" data-id="'.$id_service_request.'">
                                                        '.$id_service_request.'
                                                    </a>
                                                ';
                                            }
                                        ?>
                                    </small>
                                </td>
                                <td class="text-center">
                                    <?php 
                                        if(empty($Data['id_service_request'])){
                                            echo '
                                                <button type="button" class="btn btn-sm btn-floating btn-primary modal_service_request" data-id="'.$id_radiologi.'">
                                                    <i class="bi bi-plus"></i>
                                                </button>
                                            ';
                                        }else{
                                            echo '
                                                <button type="button" class="btn btn-sm btn-floating btn-secondary modal_edit_service_request" data-id="'.$id_service_request.'">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                            ';
                                        }
                                    ?>
                                </td>
                            </tr>

                            <!-- Procedure -->
                            <tr>
                                <td class="text-center">2</td>
                                <td><i>Procedure</i></td>
                                <td class="text text-grayish">
                                    <small>
                                        <?php 
                                            if(empty($Data['id_procedure'])){
                                                echo '-';
                                            }else{
                                                echo '
                                                    <a href="javascript:void(0);" class="modal_detail_procedure" data-id="'.$id_procedure.'">
                                                        '.$Data['id_procedure'].'
                                                    </a>
                                                ';
                                            }
                                        ?>
                                    </small>
                                </td>
                                <td class="text-center">
                                    <?php 
                                        if(empty($Data['id_procedure'])){
                                            echo '
                                                <button type="button" class="btn btn-sm btn-floating btn-primary modal_procedure" data-id="'.$id_radiologi.'">
                                                    <i class="bi bi-plus"></i>
                                                </button>
                                            ';
                                        }else{
                                            echo '
                                                <button type="button" class="btn btn-sm btn-floating btn-secondary modal_edit_procedure" data-id="'.$id_procedure.'">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                            ';
                                        }
                                    ?>
                                </td>
                            </tr>
                            
                            <!-- Imaging Study -->
                            <tr>
                                <td class="text-center">3</td>
                                <td><i>Imaging Study</i></td>
                                <td class="text text-grayish">
                                    <small>
                                        <?php 
                                            if(empty($Data['id_imaging_study'])){
                                                echo '-';
                                            }else{
                                                echo '
                                                    <a href="javascript:void(0);" class="modal_detail_imaging_study" data-id="'.$id_imaging_study.'">
                                                        '.$Data['id_imaging_study'].'
                                                    </a>
                                                ';
                                            }
                                        ?>
                                    </small>
                                </td>
                                <td class="text-center">
                                    <?php 
                                        if(empty($Data['id_imaging_study'])){
                                            echo '
                                                <button type="button" class="btn btn-sm btn-floating btn-primary modal_imaging_study" data-id="'.$id_radiologi.'">
                                                    <i class="bi bi-plus"></i>
                                                </button>
                                            ';
                                        }else{
                                            echo '
                                                <button type="button" class="btn btn-sm btn-floating btn-secondary modal_edit_imaging_study" data-id="'.$id_imaging_study.'">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                            ';
                                        }
                                    ?>
                                </td>
                            </tr>
                            
                            <!-- Observation -->
                            <tr>
                                <td class="text-center">4</td>
                                <td><i>Observation</i></td>
                                <td class="text text-grayish">
                                    <small>
                                        <?php 
                                            if(empty($Data['id_observation'])){
                                                echo '-';
                                            }else{
                                                echo '
                                                    <a href="javascript:void(0);" class="modal_detail_observation" data-id="'.$id_observation.'">
                                                        '.$Data['id_observation'].'
                                                    </a>
                                                ';
                                            }
                                        ?>
                                    </small>
                                </td>
                                <td class="text-center">
                                    <?php 
                                       if(empty($Data['id_observation'])){
                                                echo '
                                                    <button type="button" class="btn btn-sm btn-floating btn-primary modal_observation" data-id="'.$id_radiologi.'">
                                                        <i class="bi bi-plus"></i>
                                                    </button>
                                                ';
                                            }else{
                                                echo '
                                                    <button type="button" class="btn btn-sm btn-floating btn-secondary modal_edit_observation" data-id="'.$id_observation.'">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                ';
                                            }
                                    ?>
                                </td>
                            </tr>

                            <!-- Diagnostic Report -->
                            <tr>
                                <td class="text-center">4</td>
                                <td><i>Diagnostic Report</i></td>
                                <td class="text text-grayish">
                                    <small>
                                        <?php 
                                            if(empty($Data['id_diagnostic_report'])){
                                                echo '-';
                                            }else{
                                                echo '
                                                    <a href="javascript:void(0);" class="modal_detail_diagnostic_report" data-id="'.$id_diagnostic_report.'">
                                                        '.$Data['id_diagnostic_report'].'
                                                    </a>
                                                ';
                                            }
                                        ?>
                                    </small>
                                </td>
                                <td class="text-center">
                                    <?php 
                                       if(empty($Data['id_diagnostic_report'])){
                                            echo '
                                                <button type="button" class="btn btn-sm btn-floating btn-primary modal_diagnostic_report" data-id="'.$id_radiologi.'">
                                                    <i class="bi bi-plus"></i>
                                                </button>
                                            ';
                                        }else{
                                            echo '
                                               <button type="button" class="btn btn-sm btn-floating btn-secondary modal_edit_diagnostic_report" data-id="'.$id_diagnostic_report.'">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                            ';
                                        }
                                    ?>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- E. NOTA TAGIHAN -->
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-10"><b class="card-title">E. Nota Tagihan</b></div>
                    <div class="col-2 text-end">
                        <button type="button" class="btn btn-sm btn-floating btn-outline-dark modal_cetak_tagihan" data-id="<?php echo $id_radiologi; ?>">
                            <i class="bi bi-printer"></i>
                        </button>    
                        <button type="button" class="btn btn-sm btn-floating btn-primary modal_tambah_tagihan" data-id="<?php echo $id_radiologi; ?>">
                            <i class="bi bi-plus"></i>
                        </button>         
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <td class="text-center"><b>No</b></td>
                                <td><b>Uraian</b></td>
                                <td class="text-end"><b>Tarif</b></td>
                                <td class="text-center"><b>Qty</b></td>
                                <td class="text-end"><b>Jumlah</b></td>
                                <td class="text-center"><b>Opsi</b></td>
                            </tr>
                        </thead>
                        <tbody>
                           <?php
                                $jumlah_nota = mysqli_num_rows(mysqli_query($Conn, "SELECT id_radiologi_invoice FROM radiologi_invoice WHERE id_radiologi='$id_radiologi'"));
                                if(empty($jumlah_nota)){
                                    echo '
                                        <tr>
                                            <td colspan="6" class="text-center">
                                                <span class="text-center">Belum Ada Nota Tagihan</span>
                                            </td>
                                        </tr>
                                    ';
                                }else{
                                    $no_nota = 1;
                                    $total   = 0;
                                    $query_nota = mysqli_query($Conn, "SELECT * FROM radiologi_invoice WHERE id_radiologi='$id_radiologi'");
                                    while ($data_nota = mysqli_fetch_array($query_nota)) {
                                        $id_radiologi_invoice     = $data_nota['id_radiologi_invoice'];
                                        $id_master_service_prices = $data_nota['id_master_service_prices'];
                                        $service_name             = $data_nota['service_name'];
                                        $total_price              = $data_nota['total_price'];
                                        $quantity                 = $data_nota['quantity'];
                                        $amount                   = $data_nota['amount'];

                                        // Total
                                        $total   = $total + $amount;
                                        
                                        // Format uang
                                        $total_price = "Rp " . number_format($total_price,0,',','.');
                                        $amount      = "Rp " . number_format($amount,0,',','.');
                                        
                                        // menampilkan data
                                        echo '
                                            <tr>
                                                <td class="text-center">'.$no_nota.'</td>
                                                <td>'.$service_name.'</td>
                                                <td class="text-end">'.$total_price.'</td>
                                                <td class="text-center">'.$quantity.'</td>
                                                <td class="text-end">'.$amount.'</td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-secondary btn-floating"  data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="bi bi-three-dots-vertical"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="">
                                                        <li>
                                                            <a href="javascript:void(0)" class="dropdown-item modal_edit_nota" data-id="'.$id_radiologi_invoice .'">
                                                                <i class="bi bi-pencil"></i> Edit
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:void(0)" class="dropdown-item modal_hapus_nota"  data-id="'.$id_radiologi_invoice .'">
                                                                <i class="bi bi-x"></i> Hapus
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </td>
                                            </tr>
                                        ';
                                        $no_nota++;
                                    }
                                    $total = "Rp " . number_format($total,0,',','.');
                                    echo '
                                        <tr>
                                            <td class="text-end" colspan="4">
                                                <b>Jumlah Total</b>
                                            </td>
                                            <td class="text-end"><b>'.$total.'</b></td>
                                            <td class="text-end"></td>
                                        </tr>
                                    ';
                                }
                           ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- F. ASSESMENT PRA RADIOLOGI -->
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-12"><b class="card-title">F. Assesment Pra Radiologi</b></div>
                </div>
            </div>
            <div class="card-body">
                <div class="table table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <td class="text-center"><b>No</b></td>
                                <td><b>Pertanyaan</b></td>
                                <td><b>Tipe</b></td>
                                <td><b>Jawaban</b></td>
                                <td class="text-center"><b>Opsi</b></td>
                            </tr>
                        </thead>
                        <tbody>
                           <?php
                                $jumlah_pertanyaan = mysqli_num_rows(mysqli_query($Conn, "SELECT id_question FROM question"));
                                if(empty($jumlah_pertanyaan)){
                                    echo '
                                        <tr>
                                            <td colspan="5" class="text-center">
                                                <span class="text-center">Belum Ada Pertanyaan Assesment</span>
                                            </td>
                                        </tr>
                                    ';
                                }else{
                                    $no_pertanyaan = 1;
                                    $qry_pertanyaan = mysqli_query($Conn, "SELECT * FROM question ORDER BY question_text ASC");
                                    while ($data_pertanyaan = mysqli_fetch_array($qry_pertanyaan)) {
                                        $id_question      = $data_pertanyaan['id_question'];
                                        $question_group   = $data_pertanyaan['question_group'];
                                        $question_text    = $data_pertanyaan['question_text'];
                                        $question_type    = $data_pertanyaan['question_type'];

                                        // Membuka Jawaban
                                        $Qry_jawaban = $Conn->prepare("SELECT * FROM question_response WHERE id_question = ? AND id_radiologi = ?");
                                        $Qry_jawaban->bind_param("ii", $id_question, $id_radiologi);
                                        if (!$Qry_jawaban->execute()) {
                                            $jawaban =$Conn->error;
                                        }else{
                                            $ResultJawaban = $Qry_jawaban->get_result();
                                            $DataJawaban = $ResultJawaban->fetch_assoc();
                                            $Qry_jawaban->close();
                                            //Buat Variabel
                                            if(!empty($DataJawaban['id_question_response'])){
                                                $id_question_response      = $DataJawaban['id_question_response'] ?? "";
                                                $id_questionnaire_response = $DataJawaban['id_questionnaire_response'];
                                                if($DataJawaban['answer']=="1"){
                                                    $jawaban ="Ya";
                                                }else{
                                                     $jawaban ="Tidak";
                                                }
                                            }else{
                                                $id_question_response      = "";
                                                $id_questionnaire_response = "";
                                                $jawaban                   = "-";
                                            }
                                           
                                        }
                                        
                                        // menampilkan data
                                        echo '
                                            <tr>
                                                <td class="text-center"><small>'.$no_pertanyaan.'</small></td>
                                                <td><small>'.$question_text.'</small></td>
                                                <td><small>'.$question_type.'</small></td>
                                                <td class="text-center"><small>'.$jawaban.'</small></td>
                                            
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-secondary btn-floating ModalQuestionnaireResponse" data-id_question="'.$id_question .'" data-id_radiologi="'.$id_radiologi .'">
                                                        <i class="bi bi-send"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        ';
                                        $no_pertanyaan++;
                                    }
                                }
                           ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- 
    ==============================================================
    KOLOM 2 
    ==============================================================
    -->
    <div class="col-md-6">
        <!-- C. INFORMASI RADIOLOGI -->
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-10">
                        <b class="card-title">E. Informasi Radiologi</b>
                    </div>
                    <div class="col-2 text-end">
                        <button type="button" class="btn btn-sm btn-floating btn-secondary">
                            <i class="bi bi-pencil"></i>
                        </button>
                    </div>
                </div>
                
            </div>
            <div class="card-body">
                <div class="table table-responsive">
                    <table class="table table-bordered table-sm">
                        <tbody>
                            <tr>
                                <td>ID. Radiologi</td>
                                <td class="text text-grayish"><?php echo $id_radiologi; ?></td>
                            </tr>
                            <tr>
                                <td><i>Accession Number</i></td>
                                <td>
                                    <a href="javascript:void(0);" class="underscore_doted modal_detail_acn" data-id="<?php echo $accession_number; ?>">
                                        <?php echo $accession_number; ?>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>Tgl/Waktu Permintaan</td>
                                <td class="text text-grayish"><?php echo $datetime_diminta; ?></td>
                            </tr>
                            <tr>
                                <td>Asal Kiriman</td>
                                <td class="text text-grayish"><?php echo $asal_kiriman; ?></td>
                            </tr>
                            <tr>
                                <td>Prioritas</td>
                                <td class="text text-grayish"><?php echo $priority_name; ?></td>
                            </tr>
                            <tr>
                                <td>Modalitas</td>
                                <td class="text text-grayish"><?php echo $nama_modalitas; ?></td>
                            </tr>
                            <tr>
                                <td>Pesan</td>
                                <td class="text text-danger"><span>"<?php echo $pesan; ?>"</span></td>
                            </tr>
                            <tr>
                                <td>Status</td>
                                <td class="text text-grayish"><?php echo $label_status; ?></td>
                            </tr>
                            <tr>
                                <td>Radiografer</td>
                                <td class="text text-grayish"><?php echo $radiografer; ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- F. DOKTER (PERFORMER) -->
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-10">
                        <b class="card-title">F. Dokter <i>(Performer)</i></b>
                    </div>
                    <div class="col-2 text-end">
                        <button type="button" class="btn btn-sm btn-floating btn-secondary modal_edit_dokter" data-id="<?php echo $id_radiologi; ?>">
                            <i class="bi bi-pencil"></i>
                        </button>
                    </div>
                </div>
                
            </div>
            <div class="card-body">
                <div class="table table-responsive">
                    <table class="table table-bordered table-sm">
                        <tbody>
                            <tr>
                                <td colspan="2"><b>1. Dokter Pengirim</b></td>
                            </tr>
                            <tr>
                                <td><span>ID Practitioner</span></td>
                                <td class="text text-grayish"><?php echo "$ihs_dokter_pengirim"; ?></td>
                            </tr>
                            <tr>
                                <td><span>Kode Dokter</span></td>
                                <td class="text text-grayish"><?php echo "$kode_dokter_pengirim"; ?></td>
                            </tr>
                            <tr>
                                <td><span>Nama Dokter</span></td>
                                <td class="text text-grayish"><?php echo "$nama_dokter_pengirim"; ?></td>
                            </tr>
                            <tr>
                                <td colspan="2"><b>2. Dokter Penerima</b></td>
                            </tr>
                            <tr>
                                <td><span>ID Practitioner</span></td>
                                <td class="text text-grayish"><?php echo "$ihs_dokter_penerima"; ?></td>
                            </tr>
                            <tr>
                                <td><span>Kode Dokter</span></td>
                                <td class="text text-grayish"><?php echo "$kode_dokter_penerima"; ?></td>
                            </tr>
                            <tr>
                                <td><span>Nama Dokter</span></td>
                                <td class="text text-grayish"><?php echo "$nama_dokter_penerima"; ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- G. KLINIS -->
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-10">
                        <b class="card-title">G. Klinis</b>
                    </div>
                    <div class="col-2 text-end">
                        <button type="button" class="btn btn-sm btn-floating btn-secondary">
                            <i class="bi bi-pencil"></i>
                        </button>
                    </div>
                </div>
                
            </div>
            <div class="card-body">
                <div class="table table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <td class="text-center"><b><small>No</small></b></td>
                                <td><b><small>Klinis</small></b></td>
                                <td><b><small>Kategori</small></b></td>
                                <td><b><small><i>Code-Dispay</i></small></b></td>
                                <td class="text-center"><b><small>Opt</small></b></td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $no_klinis = 1;
                                if(empty($Data['klinis'])||$Data['klinis']==null||$Data['klinis']==NULL){
                                    echo '
                                        <tr>
                                            <td colspan="5" class="text-center">
                                                <small class="text-danger">Tidak Ada Data Yang Ditampilkan</small>
                                            </td>
                                        </tr>
                                    ';
                                }else{
                                    /* Normalisasi JSON */
                                    $klinis_ary = json_decode($Data['klinis'] ?? '', true);

                                    foreach ($klinis_ary as $klinis_list) {
                                        $id_klinis      = safe_text($klinis_list['id_klinis'] ?? null);
                                        $nama_klinis    = safe_text($klinis_list['nama_klinis'] ?? null);
                                        $snomed_code    = safe_text($klinis_list['snomed_code'] ?? null);
                                        $snomed_display = safe_text($klinis_list['snomed_display'] ?? null);
                                        $kategori       = safe_text($klinis_list['kategori'] ?? null);
                                        
                                        echo '
                                            <tr>
                                                <td class="text-center"><small>'.$no_klinis.'</small></td>
                                                <td><small>'.$nama_klinis.'</small></td>
                                                <td><small>'.$kategori.'</small></td>
                                                <td>
                                                    <small data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="http://snomed.info/sct">
                                                        '.$snomed_code.' - <i class="text text-grayish">'.$snomed_display.'</i>
                                                    </small>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-outline-dark btn-floating"  data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="bi bi-three-dots-vertical"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="">
                                                        <li>
                                                            <a class="dropdown-item modal_edit_klinis" href="javascript:void(0)" data-id="'.$id_klinis .'">
                                                                <i class="bi bi-pencil"></i> Edit
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item modal_hapus_klinis" href="javascript:void(0)" data-id="'.$id_klinis .'">
                                                                <i class="bi bi-x"></i> Hapus
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </td>
                                            </tr>
                                        ';
                                        $no_klinis++;
                                    }
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-10">
                        <b class="card-title">H. Permintaan Pemeriksaan</b>
                    </div>
                    <div class="col-2 text-end">
                        <button type="button" class="btn btn-sm btn-floating btn-secondary">
                            <i class="bi bi-pencil"></i>
                        </button>
                    </div>
                </div>
                
            </div>
            <div class="card-body">
                <div class="table table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <td class="text-center"><b><small>No</small></b></td>
                                <td><b><small>Pemeriksaan</small></b></td>
                                <td><b><small>Modality</small></b></td>
                                <td><b><small><i>Code-Dispay</i></small></b></td>
                                <td class="text-center"><b><small>Opt</small></b></td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $no_permintaan = 1;
                                if(empty($Data['permintaan_pemeriksaan'])||$Data['permintaan_pemeriksaan']==null||$Data['permintaan_pemeriksaan']==NULL){
                                    echo '
                                        <tr>
                                            <td colspan="5" class="text-center">
                                                <small class="text-danger">Tidak Ada Data Yang Ditampilkan</small>
                                            </td>
                                        </tr>
                                    ';
                                }else{
                                    /* Normalisasi JSON */
                                    $permintaan_pemeriksaan_arry = json_decode($Data['permintaan_pemeriksaan'] ?? '', true);

                                    foreach ($permintaan_pemeriksaan_arry as $permintaan_pemeriksaan_list) {
                                        $id_master_pemeriksaan = safe_text($permintaan_pemeriksaan_list['id_master_pemeriksaan'] ?? null);
                                        $nama_pemeriksaan = safe_text($permintaan_pemeriksaan_list['nama_pemeriksaan'] ?? null);
                                        $modalitas = safe_text($permintaan_pemeriksaan_list['modalitas'] ?? null);
                                        $pemeriksaan_code = safe_text($permintaan_pemeriksaan_list['pemeriksaan_code'] ?? null);
                                        $pemeriksaan_description = safe_text($permintaan_pemeriksaan_list['pemeriksaan_description'] ?? null);
                                        $pemeriksaan_sys = safe_text($permintaan_pemeriksaan_list['pemeriksaan_sys'] ?? null);
                                        
                                        echo '
                                            <tr>
                                                <td class="text-center"><small>'.$no_permintaan.'</small></td>
                                                <td><small>'.$nama_pemeriksaan.'</small></td>
                                                <td><small>'.$modalitas.'</small></td>
                                                <td>
                                                    <small data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="'.$pemeriksaan_sys.'">
                                                        '.$pemeriksaan_code.' - <i class="text text-grayish">'.$pemeriksaan_description.'</i>
                                                    </small>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-outline-dark btn-floating"  data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="bi bi-three-dots-vertical"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="">
                                                        <li>
                                                            <a class="dropdown-item modal_edit_klinis" href="javascript:void(0)" data-id="'.$id_master_pemeriksaan .'">
                                                                <i class="bi bi-pencil"></i> Edit
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item modal_hapus_klinis" href="javascript:void(0)" data-id="'.$id_master_pemeriksaan .'">
                                                                <i class="bi bi-x"></i> Hapus
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </td>
                                            </tr>
                                        ';
                                        $no_permintaan++;
                                    }
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-10">
                        <b class="card-title">I. Faktor Eksposur</b>
                    </div>
                    <div class="col-2 text-end">
                        <button type="button" class="btn btn-sm btn-floating btn-secondary modal_faktor_eksposi" data-id="<?php echo $id_radiologi; ?>">
                            <i class="bi bi-pencil"></i>
                        </button>
                    </div>
                </div>
                
            </div>
            <div class="card-body">
                <div class="table table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th><b>Parameter</b></th>
                                <th><b>Nilai</b></th>
                                <th><b>Satuan</b></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Tegangan Lisstrik</td>
                                <td><?php echo $kv; ?></td>
                                <td>kV</td>
                            </tr>
                            <tr>
                                <td>Arus Listrik</td>
                                <td><?php echo $ma; ?></td>
                                <td>mA</td>
                            </tr>
                            <tr>
                                <td>Lama Paparan</td>
                                <td><?php echo $sec; ?></td>
                                <td>sec</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Manajamen File manual -->
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-10">
                        <b class="card-title">J. File Manual (JPG, PNG, GIF)</b>
                    </div>
                    <div class="col-2 text-end">
                        <button type="button" class="btn btn-sm btn-floating btn-primary modal_upload_file" data-id="<?php echo $id_radiologi; ?>">
                            <i class="bi bi-upload"></i>
                        </button>
                    </div>
                </div>
                
            </div>
            <div class="card-body">
                <div class="table table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <td class="text-center"><b>No</b></td>
                                <td><b>File Name</b></td>
                                <td><b>Type</b></td>
                                <td><b>Size</b></td>
                                <td class="text-center"><b>Opsi</b></td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $jml_file = mysqli_num_rows(mysqli_query($Conn, "SELECT id_radiologi_file FROM radiologi_file WHERE id_radiologi='$id_radiologi'"));
                                if(empty($jml_file)){
                                    echo '
                                        <tr>
                                            <td colspan="5" class="text-center">Tidak Ada File Tersimpan</td>
                                        </tr>
                                    ';
                                }else{
                                    $no_file = 1;
                                    $query_file = mysqli_query($Conn, "SELECT * FROM radiologi_file WHERE id_radiologi='$id_radiologi'");
                                    while ($data_file = mysqli_fetch_array($query_file)) {
                                        $id_radiologi_file = $data_file['id_radiologi_file'];
                                        $file_name         = $data_file['file_name'];
                                        $file_type         = $data_file['file_type'];
                                        $file_size         = $data_file['file_size'];
                                        $folder_name       = $data_file['folder_name'];

                                        // Mengubah Satuan file_size
                                        $file_size_mb = round($file_size / 1024 / 1024, 2);
                                        $file_size    = "$file_size_mb Mb";

                                        // Menampilkan data
                                        $dir_file = ''.$app_base_url.'/_Storage/'.$folder_name.'/'.$file_name.'';

                                        // Apakah Sudah Punya File Dicom
                                        $id_radiologi_dicom_conv = GetDetailData($Conn, 'radiologi_dicom_conv', 'id_radiologi_file', $id_radiologi_file, 'id_radiologi_dicom_conv');
                                        $dicom_file_name = GetDetailData($Conn, 'radiologi_dicom_conv', 'id_radiologi_file', $id_radiologi_file, 'filename');

                                        // Jika belum Punya File DICOM
                                        if(empty($id_radiologi_dicom_conv)){
                                            $dicom_file_name_display = '';
                                            $tombol_lanjutan = '
                                                <li>
                                                    <a class="dropdown-item modal_konversi_dicom" href="javascript:void(0)" data-id="'.$id_radiologi_file .'">
                                                        <i class="bi bi-arrow-down"></i> DICOM
                                                    </a>
                                                </li>
                                            ';
                                        }else{
                                            // Jika Sudah Punya DICOM
                                            $dicom_file_name_display = '
                                                <br>
                                                <small class="text text-grayish">
                                                    <a href="javascript:void(0);" class="modal_detail_dicom" data-id="'.$id_radiologi_dicom_conv.'">
                                                        <small class="text text-grayish">
                                                            <i class="bi bi-arrow-return-right"></i> '.$dicom_file_name.'
                                                        </small>
                                                    </a>
                                                </small>
                                            ';
                                            $tombol_lanjutan = '';
                                        }
                                        echo '
                                            <tr>
                                                <td class="text-center"><small>'.$no_file.'</small></td>
                                                <td class="text-left">
                                                    <a href="javascript:void(0);" class="modal_detail_file" data-id="'.$id_radiologi_file.'">
                                                        <small>'.$file_name.'</small>
                                                    </a>
                                                    '.$dicom_file_name_display.'
                                                </td>
                                                <td class="text-left"><small>'.$file_type.'</small></td>
                                                <td class="text-left"><small>'.$file_size.'</small></td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-floating btn-outline-dark" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="bi bi-three-dots-vertical"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="">
                                                        <li class="dropdown-header text-start">
                                                            <h6>Option</h6>
                                                        </li>
                                                        '.$tombol_lanjutan.'
                                                        <li>
                                                            <a class="dropdown-item modal_hapus_file" href="javascript:void(0)" data-id="'.$id_radiologi_file .'">
                                                                <i class="bi bi-x"></i> Hapus
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </td>
                                            </tr>
                                        ';
                                        $no_file++;
                                    }
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Expertise Local -->
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-12">
                        <b class="card-title">J. Expertise</b>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="table table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <td class="text-center"><b>No</b></td>
                                <td><b><i>Title</i></b></td>
                                <td><b><i>Text</i></b></td>
                                <td class="text-center"><b>Opsi</b></td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $id_radiologi_local_exp = "None";
                                $temuan                 = "None";
                                $kesan                  = "None";
                                $saran                  = "None";
                                $catatan                = "None";

                                // Buka Kode Lokal
                                $QryLokal = $Conn->prepare("SELECT * FROM radiologi_local_exp WHERE id_radiologi = ?");
                                $QryLokal->bind_param("i", $id_radiologi);
                                if (!$QryLokal->execute()) {
                                    $error=$Conn->error;
                                    $id_radiologi_local_exp = $error;
                                    $temuan                 = $error;
                                    $kesan                  = $error;
                                    $saran                  = $error;
                                    $catatan                = $error;
                                }else{
                                    $ResultLokal = $QryLokal->get_result();
                                    $DataLokal = $ResultLokal->fetch_assoc();
                                    $QryLokal->close();

                                    if(empty($DataLokal['id_radiologi'])){
                                        $id_radiologi_local_exp = "-";
                                        $temuan                 = "-";
                                        $kesan                  = "-";
                                        $saran                  = "-";
                                        $catatan                = "-";

                                    }else{
                                        $temuan  = $DataLokal['temuan'];
                                        $kesan   = $DataLokal['kesan'];
                                        $saran   = $DataLokal['saran'];
                                        $catatan = $DataLokal['catatan'];

                                    }
                                   
                                }

                                echo '
                                    <tr>
                                        <td class="text-center"><small>1</small></td>
                                        <td class="text-left">Temua Klinis</td>
                                        <td class="text-left"><small>'.$temuan.'</small></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-floating btn-secondary modal_expertise" data-id_radiologi="'.$id_radiologi.'" data-title="temuan">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center"><small>2</small></td>
                                        <td class="text-left">Kesan</td>
                                        <td class="text-left"><small>'.$kesan.'</small></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-floating btn-secondary modal_expertise" data-id_radiologi="'.$id_radiologi.'" data-title="kesan">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center"><small>3</small></td>
                                        <td class="text-left">Saran</td>
                                        <td class="text-left"><small>'.$saran.'</small></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-floating btn-secondary modal_expertise" data-id_radiologi="'.$id_radiologi.'" data-title="saran">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center"><small>4</small></td>
                                        <td class="text-left">Catatan/Keterangan</td>
                                        <td class="text-left"><small>'.$catatan.'</small></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-floating btn-secondary modal_expertise" data-id_radiologi="'.$id_radiologi.'" data-title="catatan">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                        </td>
                                    </tr>
                                ';
                            ?>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Expertise PACS -->
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-12">
                        <b class="card-title">K. Expertise (PACS)</b>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="table table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <td class="text-center"><b>No</b></td>
                                <td><b><i>Datetime</i></b></td>
                                <td><b><i>Study Number</i></b></td>
                                <td><b><i>Descritption</i></b></td>
                                <td class="text-center"><b>Opsi</b></td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                // Menghitung Jumlah Data Expertise Berdasarkan Modality
                                if($alat_pemeriksa=="US"){
                                    $jml_expertise_pacs = mysqli_num_rows(mysqli_query($Conn, "SELECT id_radiologi_expertise_usg FROM radiologi_expertise_usg WHERE id_radiologi='$id_radiologi'"));
                                }else{
                                    $jml_expertise_pacs = mysqli_num_rows(mysqli_query($Conn, "SELECT id_radiologi_expertise FROM radiologi_expertise WHERE id_radiologi='$id_radiologi'"));
                                }
                                
                                // Jika Data Tidak Ditemukan
                                if(empty($jml_expertise_pacs)){
                                    echo '
                                        <tr>
                                            <td colspan="5" class="text-center">Belum Ada Expertise Untuk Permintaan Ini</td>
                                        </tr>
                                    ';
                                }else{
                                    $nomor_exp_pacs = 1;
                                    if($alat_pemeriksa=="US"){
                                        $query_exp_pacs = mysqli_query($Conn, "SELECT * FROM radiologi_expertise_usg WHERE id_radiologi='$id_radiologi' ORDER By id_radiologi_expertise_usg DESC");
                                    }else{
                                        $query_exp_pacs = mysqli_query($Conn, "SELECT * FROM radiologi_expertise WHERE id_radiologi='$id_radiologi' ORDER By id_radiologi_expertise DESC");
                                    }
                                    while ($data_exp_pacs = mysqli_fetch_array($query_exp_pacs)) {
                                        $timestamp             = $data_exp_pacs['timestamp'];
                                        $viewer_link           = $data_exp_pacs['viewer_link'];
                                        $study_number          = $data_exp_pacs['study_number'];
                                        $description_expertise = $data_exp_pacs['description'];
                                       
                                        // Routing Primary Key
                                        if($alat_pemeriksa=="US"){
                                            $id_radiologi_expertise = $data_exp_pacs['id_radiologi_expertise_usg'];
                                        }else{
                                            $id_radiologi_expertise = $data_exp_pacs['id_radiologi_expertise'];
                                        }

                                        // Format Waktu
                                        $datetime_expertise_pacs = date('d/m/Y H:i', strtotime($timestamp));
                                        echo '
                                            <tr>
                                                <td class="text-center"><small>'.$nomor_exp_pacs.'</small></td>
                                                <td><small>'.$datetime_expertise_pacs.'</small></td>
                                                <td>
                                                    <small>
                                                        <a href="javascript:void(0);" class="modal_detail_exp_pacs" data-id="'.$id_radiologi_expertise.'" data-modality="'.$alat_pemeriksa.'">
                                                            <small>'.$alat_pemeriksa.'-'.$study_number.'</small>
                                                        </a>
                                                    </small>
                                                </td>
                                                <td><small>'.$description_expertise.'</small></td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-floating btn-outline-danger modal_hapus_exp_pacs" data-id="'.$id_radiologi_expertise.'" data-modality="'.$alat_pemeriksa.'">
                                                        <i class="bi bi-x"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        ';
                                        $nomor_exp_pacs++;
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

<script>
    $('[data-bs-toggle="tooltip"]').tooltip();

    // Menampilkan floating button
    showFloatingOption();
</script>