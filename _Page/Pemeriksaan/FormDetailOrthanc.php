<?php
    // Koneksi, Function dan Session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    date_default_timezone_set("Asia/Jakarta");

    // Validasi Session
    if (empty($SessionIdAccess)) {
        echo '
            <div class="alert alert-danger text-center">
                <small>Sesi akses sudah berakhir. Silakan login ulang.</small>
            </div>
        ';
        exit;
    }

    // Validasi Accession Number Tidak Boleh Kosong
    if (empty($_POST['accession_number'])) {
        echo '
            <div class="alert alert-danger text-center">
                <small>Accession Number tidak boleh kosong.</small>
            </div>
        ';
        exit;
    }

    $accession_number = validateAndSanitizeInput($_POST['accession_number']);

    // BUKA KONFIGURASI ORTHANC AKTIF
    $status_connection_orthanc = 1;
    $stmt = $Conn->prepare("SELECT * FROM connection_orthanc WHERE status_connection_orthanc = ?");
    $stmt->bind_param("i", $status_connection_orthanc);
    $stmt->execute();
    $result = $stmt->get_result();
    $config = $result->fetch_assoc();
    $stmt->close();

    if (!$config) {
        echo '
            <div class="alert alert-danger text-center">
                <small>Pengaturan koneksi Ortanc Tidak Ditemukan!</small>
            </div>
        ';
        exit;
    }

    $url_connection_orthanc      = rtrim($config['url_connection_orthanc'], '/');
    $url_connection_orthanc_full = "$url_connection_orthanc/worklists/$accession_number";
    $username_connection_orthanc = $config['username_connection_orthanc'];
    $password_connection_orthanc = $config['password_connection_orthanc'];

    // Mulai CULR Ortanc
    $curl_orthanc = curl_init();
    curl_setopt_array($curl_orthanc, [
        CURLOPT_URL => $url_connection_orthanc_full,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
        CURLOPT_USERPWD => ''.$username_connection_orthanc.':'.$password_connection_orthanc.'',
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json'
        ],
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false
    ]);

    $response_orthanc = curl_exec($curl_orthanc);
    $http_code_orthanc = curl_getinfo($curl_orthanc, CURLINFO_HTTP_CODE);
    $curl_orthanc_error = curl_error($curl_orthanc);
    curl_close($curl_orthanc);

    // Handdle Error Curl Ortanc
    if ($curl_orthanc_error) {
        echo '
            <div class="alert alert-danger text-center">
                <small>CURL Ortanc Error: '.$curl_orthanc_error.'</small>
            </div>
        ';
        exit;
    }

    // Decode Response Ortanc
    // Orthanc MWL biasanya tidak mengembalikan JSON
    if ($http_code_orthanc !== 200) {
        echo '
            <div class="alert alert-danger text-center">
                <small>Gagal Melakukan Request: Http Code '.$http_code_orthanc.' <br>Response: <br>'.$response_orthanc.'</small>
            </div>
        ';
        exit;
    }

    // Validasi Response Ortanc
    if ($http_code_orthanc !== 200) {
        $msg_orthanc = 'Gagal mengirim Request Ke Ortanc <br>Response : <code>'.$response_orthanc.'</code> <br>Payload : <code>'.$payload_json_orthanc.'</code>';

        echo '
            <div class="alert alert-danger text-center">
                <small>'.$msg_orthanc.'</small>
            </div>
        ';
        exit;
    }

    // Decode JSON
    $result      = json_decode($response_orthanc, true);
    $id_worklist = $result['ID'];
    $Tags        = $result['Tags'];
?>
<div class="row mb-2">
    <div class="col-4"><small>Accession Number</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <small class="text text-grayish"><?php echo $Tags['AccessionNumber']; ?></small>
    </div>
</div>
<div class="row mb-2">
    <div class="col-4"><small>Nama Faskes</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <small class="text text-grayish"><?php echo $Tags['IssuerOfPatientID']; ?></small>
    </div>
</div>
<div class="row mb-2">
    <div class="col-4"><small>Tanggal Lahir</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <small class="text text-grayish"><?php echo $Tags['PatientBirthDate']; ?></small>
    </div>
</div>
<div class="row mb-2">
    <div class="col-4"><small>No.RM</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <small class="text text-grayish"><?php echo $Tags['PatientID']; ?></small>
    </div>
</div>
<div class="row mb-2">
    <div class="col-4"><small>Nama Pasien</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <small class="text text-grayish"><?php echo $Tags['PatientName']; ?></small>
    </div>
</div>
<div class="row mb-2">
    <div class="col-4"><small>Gender / Jenis Kelamin</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <small class="text text-grayish"><?php echo $Tags['PatientSex']; ?></small>
    </div>
</div>
<div class="row mb-2">
    <div class="col-4"><small><i>Modality</i></small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <small class="text text-grayish"><?php echo $Tags['ScheduledProcedureStepSequence'][0]['Modality']; ?></small>
    </div>
</div>
<div class="row mb-2">
    <div class="col-4"><small>Dokter Pemeriksa</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <small class="text text-grayish"><pre><?php echo $Tags['ScheduledProcedureStepSequence'][0]['ScheduledPerformingPhysicianName']; ?></pre></small>
    </div>
</div>
<div class="row mb-2">
    <div class="col-4"><small>Prosedur Pemeriksaan</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <small class="text text-grayish"><?php echo $Tags['ScheduledProcedureStepSequence'][0]['ScheduledProcedureStepDescription']; ?></small>
    </div>
</div>
<div class="row mb-2">
    <div class="col-4"><small>Tanggal Pemeriksaan</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <small class="text text-grayish"><?php echo $Tags['ScheduledProcedureStepSequence'][0]['ScheduledProcedureStepStartDate']; ?></small>
    </div>
</div>
<div class="row mb-2">
    <div class="col-4"><small>Jam Pemeriksaan</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <small class="text text-grayish"><?php echo $Tags['ScheduledProcedureStepSequence'][0]['ScheduledProcedureStepStartTime']; ?></small>
    </div>
</div>
<div class="row mb-2">
    <div class="col-4"><small><i>Scheduled Station AE Title</i></small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <small class="text text-grayish"><?php echo $Tags['ScheduledProcedureStepSequence'][0]['ScheduledStationAETitle']; ?></small>
    </div>
</div>
<div class="row mb-2">
    <div class="col-4"><small><i>Specific Character Set</i></small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <small class="text text-grayish"><?php echo $Tags['SpecificCharacterSet']; ?></small>
    </div>
</div>
<div class="row mb-2">
    <div class="col-4"><small><i>Study Instance UID</i></small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <small class="text text-grayish"><?php echo $Tags['StudyInstanceUID']; ?></small>
    </div>
</div>
<!-- <div class="row mb-2">
    <div class="col-12"><small>Raw</small></div>
    <div class="col-12">
        <textarea name="raw" id="raw" class="form-control"><?php echo $response_orthanc; ?></textarea>
    </div>
</div> -->