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
        <button type="button" class="btn btn-md btn-floating btn-primary">
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
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-10">
                        <b class="card-title">C. Waktu Pelayanan</b>
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
                                <td>Permintaan Radiologi</td>
                                <td class="text text-grayish"><?php echo $datetime_diminta; ?></td>
                            </tr>
                            <tr>
                                <td>Permintaan Dikerjakan</td>
                                <td class="text text-grayish"><?php echo $datetime_dikerjakan; ?></td>
                            </tr>
                            <tr>
                                <td>Pembuatan Hasil</td>
                                <td class="text text-grayish"><?php echo $datetime_hasil; ?></td>
                            </tr>
                            <tr>
                                <td>Penyerahan Hasil</td>
                                <td class="text text-grayish"><?php echo $datetime_selesai; ?></td>
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
                                <td class="text-end"><b>Tarif</b></td>
                                <td class="text-center"><b>Opsi</b></td>
                            </tr>
                        </thead>
                        <tbody>
                           <?php
                                $jumlah_nota = mysqli_num_rows(mysqli_query($Conn, "SELECT id_radiologi_invoice FROM radiologi_invoice WHERE id_radiologi='$id_radiologi'"));
                                if(empty($jumlah_nota)){
                                    echo '
                                        <tr>
                                            <td colspan="4" class="text-center">
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
                                <td class="text text-grayish"><?php echo $accession_number; ?></td>
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

        <!-- G. DOKTER (PERFORMER) -->
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

    </div>
</div>

<script>
    $('[data-bs-toggle="tooltip"]').tooltip();
</script>