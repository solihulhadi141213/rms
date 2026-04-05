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

    //id_radiologi_dicom_conv wajib terisi
    if(empty($_POST['id_radiologi_dicom_conv'])){
        echo '
            <div class="row mb-3">
                <div class="col-12 text-center">
                    <div class="alert alert-danger"><small>ID File Tidak Boleh Kosong!</small></div>
                </div>
            </div>
        ';
        exit;
    }

    //Buat variabel 'id_radiologi_dicom_conv' dan sanitasi
    $id_radiologi_dicom_conv = validateAndSanitizeInput($_POST['id_radiologi_dicom_conv']);

    //Buka Detail Koneksi Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM radiologi_dicom_conv WHERE id_radiologi_dicom_conv = ?");
    $Qry->bind_param("s", $id_radiologi_dicom_conv);
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

    // Jika Data Tidak Ditemukan
    if(empty($Data['id_radiologi_dicom_conv'])){
        echo '
            <div class="row mb-3">
                <div class="col-12 text-center">
                    <div class="alert alert-danger"><small>ID File Tiidak Valid!</small></div>
                </div>
            </div>
        ';
        exit;
    }

    //Buat Variabel
    $id_radiologi     = $Data['id_radiologi'];
    $id_imaging_study = $Data['id_imaging_study'];
    $id_imaging_study = $Data['id_imaging_study'];
    $accession_number = $Data['accession_number'];
    $filename         = $Data['filename'];
    $dicom_metadata   = $Data['dicom_metadata'];

    // Buka DICOM metadata
    $dicom_metadata_arry = json_decode($dicom_metadata, true);

    //Buat Variabel
    $Modality          = $dicom_metadata_arry['Modality'];
    $ConversionDate    = $dicom_metadata_arry['ConversionDate'];
    $SOPClassUID       = $dicom_metadata_arry['SOPClassUID'];
    $Modality          = $dicom_metadata_arry['Modality'];
    $PatientID         = $dicom_metadata_arry['PatientID'];
    $SeriesInstanceUID = $dicom_metadata_arry['SeriesInstanceUID'];
    $StudyInstanceUID  = $dicom_metadata_arry['StudyInstanceUID'];
    $SOPInstanceUID    = $dicom_metadata_arry['SOPInstanceUID'];
    $PatientName       = $dicom_metadata_arry['PatientName'];
    $StudyDescription  = $dicom_metadata_arry['StudyDescription'];
    $ConversionDate    = $dicom_metadata_arry['ConversionDate'];
    $StudyDate         = $dicom_metadata_arry['StudyDate'];
    $StudyTime         = $dicom_metadata_arry['StudyTime'];
    $ImageType         = $dicom_metadata_arry['ImageType'];

    // Mengubah Format Waktu Started
    $DateTimeStarted = DateTime::createFromFormat(
        'YmdHis',
        $StudyDate . $StudyTime,
        new DateTimeZone('Asia/Jakarta')
    );

    $Started = $DateTimeStarted->format('Y-m-d\TH:i:sP');

    // Buka Informasi Pemeriksaan Radiologi
    $QryPemeriksaan = $Conn->prepare("SELECT * FROM radiologi WHERE id_radiologi = ?");
    $QryPemeriksaan->bind_param("i", $id_radiologi);
    if (!$QryPemeriksaan->execute()) {
        $error=$Conn->error;
        echo '
            <div class="alert alert-danger">
                <small>Terjadi kesalahan pada saat membuka data dari database!<br>Keterangan : '.$error.'</small>
            </div>
        ';
        exit;
    }
    $ResultPemeriksaan = $QryPemeriksaan->get_result();
    $DataPemeriksaan = $ResultPemeriksaan->fetch_assoc();
    $QryPemeriksaan->close();

    //Buat Variabel
    $id_access              = $DataPemeriksaan['id_access'];
    $id_pasien              = $DataPemeriksaan['id_pasien'];
    $id_kunjungan           = $DataPemeriksaan['id_kunjungan'];
    $accession_number       = $DataPemeriksaan['accession_number'];
    $id_service_request     = $DataPemeriksaan['id_service_request'];
    $id_procedure           = $DataPemeriksaan['id_procedure'];
    $id_imaging_study       = $DataPemeriksaan['id_imaging_study'];
    $nama_pasien            = $DataPemeriksaan['nama_pasien'];
    $priority               = $DataPemeriksaan['priority'];
    $asal_kiriman           = $DataPemeriksaan['asal_kiriman'];
    $alat_pemeriksa         = $DataPemeriksaan['alat_pemeriksa'];
    $kode_dokter_pengirim   = $DataPemeriksaan['kode_dokter_pengirim'];
    $ihs_dokter_pengirim    = $DataPemeriksaan['ihs_dokter_pengirim'];
    $nama_dokter_pengirim   = $DataPemeriksaan['nama_dokter_pengirim'];
    $kode_dokter_penerima   = $DataPemeriksaan['kode_dokter_penerima'];
    $ihs_dokter_penerima    = $DataPemeriksaan['ihs_dokter_penerima'];
    $nama_dokter_penerima   = $DataPemeriksaan['nama_dokter_penerima'];
    $radiografer            = $DataPemeriksaan['radiografer'] ?? "-";
    $pesan                  = $DataPemeriksaan['pesan'] ?? "-";
    $kesan                  = $DataPemeriksaan['kesan'];
    $klinis                 = $DataPemeriksaan['klinis'];
    $permintaan_pemeriksaan = $DataPemeriksaan['permintaan_pemeriksaan'];
    $kv                     = $DataPemeriksaan['kv'];
    $ma                     = $DataPemeriksaan['ma'];
    $sec                    = $DataPemeriksaan['sec'];
    $tujuan                 = $DataPemeriksaan['tujuan'];
    $pembayaran             = $DataPemeriksaan['pembayaran'];
    $datetime_diminta       = $DataPemeriksaan['datetime_diminta'];
    $datetime_dikerjakan    = $DataPemeriksaan['datetime_dikerjakan'];
    $datetime_hasil         = $DataPemeriksaan['datetime_hasil'];
    $datetime_selesai       = $DataPemeriksaan['datetime_selesai'];
    $status_pemeriksaan     = $DataPemeriksaan['status_pemeriksaan'];

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
    $id_ihs         = getDisplayValue($pasien['id_ihs'] ?? null);

    echo '
        <input type="hidden" name="id_radiologi_dicom_conv" class="form-control" value="'.$id_radiologi_dicom_conv.'">
        <div class="row mb-2">
            <div class="col-12">
                <label for="organization_id_dcm_conv"><small class="text-white">ID Organization</small></label>
                <input type="text" readonly name="organization_id" id="organization_id_dcm_conv" class="form-control" value="'.$organization_id.'">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-12">
                <label for="id_radiologi_dcm_conv"><small class="text-white">ID Radiologi</small></label>
                <input type="text" readonly name="id_radiologi" id="id_radiologi_dcm_conv" class="form-control" value="'.$id_radiologi.'">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-12">
                <label for="ihs_pasien_dcm_conv"><small class="text-white">IHS Pasien</small></label>
                <input type="text" readonly name="ihs_pasien" id="ihs_pasien_dcm_conv" class="form-control" value="'.$id_ihs.'">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-12">
                <label for="nama_pasien_dcm_conv"><small class="text-white">Nama Pasien</small></label>
                <input type="text" readonly name="nama_pasien" id="nama_pasien_dcm_conv" class="form-control" value="'.$nama_pasien.'">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-12">
                <label for="accession_number_dcm_conv"><small class="text-white">Accession Number</small></label>
                <input type="text" readonly name="accession_number" id="accession_number_dcm_conv" class="form-control" value="'.$accession_number.'">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-12">
                <label for="ServiceRequest_dcm_conv"><small class="text-white">Service Request</small></label>
                <input type="text" readonly name="ServiceRequest" id="ServiceRequest_dcm_conv" class="form-control" value="'.$id_service_request.'">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-12">
                <label for="modality_code_dcm_conv"><small class="text-white">Modality Code</small></label>
                <input type="text" readonly name="modality_code" id="modality_code_dcm_conv" class="form-control" value="'.$alat_pemeriksa.'">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-12">
                <label for="modality_display_dcm_conv"><small class="text-white">Modality Display</small></label>
                <input type="text" readonly name="modality_display" id="modality_display_dcm_conv" class="form-control" value="'.$nama_modalitas.'">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-12">
                <label for="SeriesInstanceUID_dcm_conv"><small class="text-white">Series Instance UID</small></label>
                <input type="text" readonly name="SeriesInstanceUID" id="SeriesInstanceUID_dcm_conv" class="form-control" value="'.$SeriesInstanceUID.'">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-12">
                <label for="StudyInstanceUID_dcm_conv"><small class="text-white">Study Instance UID</small></label>
                <input type="text" readonly name="StudyInstanceUID" id="StudyInstanceUID_dcm_conv" class="form-control" value="'.$StudyInstanceUID.'">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-12">
                <label for="SOPInstanceUID_dcm_conv"><small class="text-white">SOP Instance UID</small></label>
                <input type="text" readonly name="SOPInstanceUID" id="SOPInstanceUID_dcm_conv" class="form-control" value="'.$SOPInstanceUID.'">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-12">
                <label for="SOPClassUID_dcm_conv"><small class="text-white">SOP Class UID</small></label>
                <input type="text" readonly name="SOPClassUID" id="SOPClassUID_dcm_conv" class="form-control" value="'.$SOPClassUID.'">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-12">
                <label for="started_dcm_conv"><small class="text-white">Started</small></label>
                <input type="text" readonly name="started" id="started_dcm_conv" class="form-control" value="'.$Started.'">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-12">
                <label for="title_dcm_conv"><small class="text-white">Title</small></label>
                <input type="text" readonly name="title" id="title_dcm_conv" class="form-control" value="'.$ImageType.'">
            </div>
        </div>
    ';
?>