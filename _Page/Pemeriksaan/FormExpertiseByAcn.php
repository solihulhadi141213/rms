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
        echo '
            <div class="alert alert-danger text-center">
                <small>Gagal mengakses PACS<br>Error: '.$tokenResult['message'].'</small>
            </div>
        ';
        exit;
    }
    $tokenPacs = $tokenResult['token'];

    // Konfigurasi PACS
    $stmt = $Conn->prepare(" SELECT url_connection_pacs, url_pacs FROM connection_pacs WHERE status_connection_pacs = 1 LIMIT 1");
    $stmt->execute();
    $config = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$config) {
        echo '
            <div class="alert alert-danger text-center">
                <small>Konfigurasi PACS tidak ditemukan.</small>
            </div>
        ';
        exit;
    }
    $url_pacs = $config['url_pacs'];
    $url = rtrim($config['url_connection_pacs'], '/'). '/api/dicom/expertise-result/'. urlencode($accession_number);

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
                <small>Expertise dari PACS tidak ditemukan.</small>
            </div>
        ';
        exit;
    }

    $expertise_data = $result['data'];

    echo '
        <div class="table-responsive">
            <table class="table table-bordered table-sm table-hover">
                <tbody>
                    <tr>
                        <td><small>Accession Number</small></td>
                        <td><small class="text-grayish">'.$expertise_data['accession_number'].'</small></td>
                    </tr>
                    <tr>
                        <td><small>Patient ID</small></td>
                        <td><small class="text-grayish">'.$expertise_data['patient_id'].'</small></td>
                    </tr>
                    <tr>
                        <td><small>Patient Name</small></td>
                        <td><small class="text-grayish">'.$expertise_data['patient_name'].'</small></td>
                    </tr>
                    <tr>
                        <td><small>Patient Birth Date</small></td>
                        <td><small class="text-grayish">'.$expertise_data['patient_birth_date'].'</small></td>
                    </tr>
                    <tr>
                        <td><small>Patient Sex</small></td>
                        <td><small class="text-grayish">'.$expertise_data['patient_sex'].'</small></td>
                    </tr>
                    <tr>
                        <td><small>Study ID</small></td>
                        <td><small class="text-grayish">'.$expertise_data['study_id'].'</small></td>
                    </tr>
                    <tr>
                        <td><small>Study Date</small></td>
                        <td><small class="text-grayish">'.$expertise_data['study_date'].'</small></td>
                    </tr>
                    <tr>
                        <td><small>Study Time</small></td>
                        <td><small class="text-grayish">'.$expertise_data['study_time'].'</small></td>
                    </tr>
                    <tr>
                        <td><small>Modality</small></td>
                        <td><small class="text-grayish">'.$expertise_data['modality'].'</small></td>
                    </tr>
                    <tr>
                        <td><small>Body Part Examined</small></td>
                        <td><small class="text-grayish">'.$expertise_data['body_part_examined'].'</small></td>
                    </tr>
                    <tr>
                        <td><small>Gestational Sac Size</small></td>
                        <td><small class="text-grayish">'.$expertise_data['gestational_sac_size'].'</small></td>
                    </tr>
                    <tr>
                        <td><small>Crown Rump Length</small></td>
                        <td><small class="text-grayish">'.$expertise_data['crown_rump_length'].'</small></td>
                    </tr>
                    <tr>
                        <td><small>Fetal Heart Rate</small></td>
                        <td><small class="text-grayish">'.$expertise_data['fetal_heart_rate'].'</small></td>
                    </tr>
                    <tr>
                        <td><small>Biparietal Diameter</small></td>
                        <td><small class="text-grayish">'.$expertise_data['biparietal_diameter'].'</small></td>
                    </tr>
                    <tr>
                        <td><small>Head Circumference</small></td>
                        <td><small class="text-grayish">'.$expertise_data['head_circumference'].'</small></td>
                    </tr>
                    <tr>
                        <td><small>Abdominal Circumference</small></td>
                        <td><small class="text-grayish">'.$expertise_data['abdominal_circumference'].'</small></td>
                    </tr>
                    <tr>
                        <td><small>Femur Length</small></td>
                        <td><small class="text-grayish">'.$expertise_data['femur_length'].'</small></td>
                    </tr>
                    <tr>
                        <td><small>Single Deepest Pocket</small></td>
                        <td><small class="text-grayish">'.$expertise_data['single_deepest_pocket'].'</small></td>
                    </tr>
                    <tr>
                        <td><small>Estimated Fetal Weight</small></td>
                        <td><small class="text-grayish">'.$expertise_data['estimated_fetal_weight'].'</small></td>
                    </tr>
                    <tr>
                        <td><small>Fetal Position</small></td>
                        <td><small class="text-grayish">'.$expertise_data['fetal_position'].'</small></td>
                    </tr>
                    <tr>
                        <td><small>Estimated Gestational Age</small></td>
                        <td><small class="text-grayish">'.$expertise_data['estimated_gestational_age'].'</small></td>
                    </tr>
                    <tr>
                        <td><small>Estimated Date Birth</small></td>
                        <td><small class="text-grayish">'.$expertise_data['estimated_date_birth'].'</small></td>
                    </tr>
                    <tr>
                        <td><small>Fetal Presentation</small></td>
                        <td><small class="text-grayish">'.$expertise_data['fetal_presentation'].'</small></td>
                    </tr>
                    <tr>
                        <td><small>Clinical</small></td>
                        <td><small class="text-grayish">'.$expertise_data['clinical'].'</small></td>
                    </tr>
                    <tr>
                        <td><small>Interpretation</small></td>
                        <td><small class="text-grayish">'.$expertise_data['interpretation'].'</small></td>
                    </tr>
                    <tr>
                        <td><small>Conclusion</small></td>
                        <td><small class="text-grayish">'.$expertise_data['conclusion'].'</small></td>
                    </tr>
                    <tr>
                        <td><small>Submitted At</small></td>
                        <td><small class="text-grayish">'.$expertise_data['submitted_at'].'</small></td>
                    </tr>
                </tbody>
            </table>
        </div>
    ';
?>

