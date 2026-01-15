<?php
    // Koneksi, Function dan Session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    // Validasi Session
    if (empty($SessionIdAccess)) {
        echo '
            <div class="alert alert-danger text-center">
                <small>Sesi telah berakhir! Silakan login ulang.</small>
            </div>
        ';
        exit;
    }

    // Validasi Input
    if(empty($_POST['accession_number'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>Accession Number Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }

    // Buat Variabel Dan Sanitasi
    $accession_number = validateAndSanitizeInput($_POST['accession_number']);

    // Buka Pengaturan PACS
    $status_connection_pacs = 1;

    // Token PACS
    $tokenResult = generateTokenPacs($Conn);
    if ($tokenResult['status'] !== 'success') {
        echo '<div class="alert alert-danger text-center">
                <small>Gagal mengakses PACS<br>Error: '.$tokenResult['message'].'</small>
            </div>';
        exit;
    }
    $tokenPacs = $tokenResult['token'];

    // Konfigurasi PACS
    $stmt = $Conn->prepare(" SELECT url_connection_pacs, url_pacs FROM connection_pacs WHERE status_connection_pacs = 1 LIMIT 1");
    $stmt->execute();
    $config = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$config) {
        echo '<div class="alert alert-danger text-center">
                <small>Konfigurasi PACS tidak ditemukan.</small>
            </div>';
        exit;
    }
    $url_pacs = $config['url_pacs'];
    $url = rtrim($config['url_connection_pacs'], '/')
        . '/api/dicom/show-metadata?accession_number='
        . urlencode($accession_number);

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer '.$tokenPacs,
            'Accept: application/json'
        ],
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false
    ]);

    $response = curl_exec($curl);
    $error    = curl_error($curl);
    curl_close($curl);

    if ($error) {
        echo '<div class="alert alert-danger text-center">
                <small>'.$error.'</small>
            </div>';
        exit;
    }

    // Decode JSON
    $result = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE || empty($result['data'])) {
        echo '
            <div class="alert alert-warning text-center">
                <small>Data DICOM Metadata dari PACS tidak ditemukan.</small>
            </div>
        ';
        exit;
    }

    $data_dicom_metadata = $result['data'];

    // Shortcut aman untuk value
    $d = $data_dicom_metadata;

    // Helper value abu-abu
    function valGray($v, $default = '-') {
        if ($v === null || $v === '') {
            return '<span class="text-secondary">'.$default.'</span>';
        }
        return '<span class="text-secondary">'.htmlspecialchars($v).'</span>';
    }

    // Helper boolean berwarna
    function boolStatus($v, $type = 'normal') {
        if ($type === 'positive') {
            return $v
                ? '<span class="text-primary fw-semibold">Ya</span>'
                : '<span class="text-secondary">Tidak</span>';
        }

        if ($type === 'negative') {
            return $v
                ? '<span class="text-danger fw-semibold">Ya</span>'
                : '<span class="text-secondary">Tidak</span>';
        }

        return $v
            ? '<span class="text-primary">Ya</span>'
            : '<span class="text-secondary">Tidak</span>';
    }

    // Format tanggal abu-abu
    function dateGray($v) {
        if (!$v) {
            return '<span class="text-secondary">-</span>';
        }
        return '<span class="text-secondary">'.date('d-m-Y H:i', strtotime($v)).'</span>';
    }
    $study_instance_uid      = $d['study_instance_uid'];
    $study_instance_uid_prev = substr($study_instance_uid, 0, 10) . '..' . '..';
    echo '
        <div class="table-responsive">
            <table class="table table-bordered table-sm table-hover">
                <tbody>
                    <tr><td><small>ID</small></td><td><small>'.valGray($d['id']).'</small></td></tr>
                    <tr><td><small>SOP Instance UID</small></td><td><small>'.valGray($d['sop_instance_uid']).'</small></td></tr>
                    <tr>
                        <td><small>Study Instance UID</small></td>
                        <td>
                            <small data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="'.$study_instance_uid.'">
                                <a href="'.$url_pacs.'/viewer?studyInstanceUID='.$study_instance_uid.'&modality='.$d['modality'].'" target="_blank">
                                    '.$study_instance_uid_prev.' <i class="bi bi-arrow-up-right-square"></i>
                                </a>
                            </small>
                        </td>
                    </tr>
                    <tr><td><small>Orthanc Study ID</small></td><td><small>'.valGray($d['orthanc_study_id']).'</small></td></tr>
                    <tr><td><small>Accession Number</small></td><td><small>'.valGray($d['accession_number']).'</small></td></tr>
                    <tr><td><small>Patient Name</small></td><td><small>'.valGray($d['patient_name']).'</small></td></tr>
                    <tr><td><small>Patient ID</small></td><td><small>'.valGray($d['patient_id']).'</small></td></tr>
                    <tr><td><small>Patient Birth Date</small></td><td><small>'.dateGray($d['patient_birth_date']).'</small></td></tr>
                    <tr><td><small>Patient Sex</small></td><td><small>'.valGray($d['patient_sex']).'</small></td></tr>
                    <tr><td><small>Study ID</small></td><td><small>'.valGray($d['study_id']).'</small></td></tr>
                    <tr><td><small>Study Date</small></td><td><small>'.dateGray($d['study_date']).'</small></td></tr>
                    <tr><td><small>Study Time</small></td><td><small>'.valGray($d['study_time']).'</small></td></tr>
                    <tr><td><small>Modality</small></td><td><small>'.valGray($d['modality']).'</small></td></tr>
                    <tr><td><small>Body Part Examined</small></td><td><small>'.valGray($d['body_part_examined']).'</small></td></tr>
                    <tr><td><small>Institution Name</small></td><td><small>'.valGray($d['institution_name']).'</small></td></tr>
                    <tr><td><small>Institution Address</small></td><td><small>'.valGray($d['institution_address']).'</small></td></tr>
                    <tr><td><small>Institution ID</small></td><td><small>'.valGray($d['institution_id']).'</small></td></tr>
                    <tr><td><small>Doctor ID</small></td><td><small>'.valGray($d['doctor_id']).'</small></td></tr>
                    <tr><td><small>Inserted At</small></td><td><small>'.dateGray($d['inserted_at']).'</small></td></tr>
                    <tr><td><small>Reviewed?</small></td><td><small>'.boolStatus($d['is_reviewed'], 'positive').'</small></td></tr>
                    <tr><td><small>Suspected?</small></td><td><small>'.boolStatus($d['is_suspected'], 'negative').'</small></td></tr>
                    <tr><td><small>Approved?</small></td><td><small>'.boolStatus($d['is_approved'], 'positive').'</small></td></tr>
                    <tr><td><small>Done?</small></td><td><small>'.boolStatus($d['is_done'], 'positive').'</small></td></tr>
                    <tr><td><small>Rejected?</small></td><td><small>'.boolStatus($d['is_rejected'], 'negative').'</small></td></tr>
                    <tr><td><small>Rejection Reason</small></td><td><small>'.valGray($d['rejection_reason']).'</small></td></tr>
                    <tr><td><small>Status Updated At</small></td><td><small>'.dateGray($d['status_updated_at']).'</small></td></tr>
                    <tr><td><small>Completed At</small></td><td><small>'.dateGray($d['completed_at']).'</small></td></tr>
                </tbody>
            </table>
        </div>
    ';
?>

