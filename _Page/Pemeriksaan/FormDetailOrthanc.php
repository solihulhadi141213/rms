<?php
    // ===================== KONEKSI =====================
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    date_default_timezone_set("Asia/Jakarta");

    // ===================== VALIDASI =====================
    if (empty($SessionIdAccess)) {
        echo '<div class="alert alert-danger text-center"><small>Sesi habis</small></div>';
        exit;
    }

    if (empty($_POST['accession_number'])) {
        echo '<div class="alert alert-danger text-center"><small>ACN kosong</small></div>';
        exit;
    }

    $accession_number = validateAndSanitizeInput($_POST['accession_number']);

    // ===================== CONFIG =====================
    $stmt = $Conn->prepare("SELECT * FROM connection_orthanc WHERE status_connection_orthanc=1");
    $stmt->execute();
    $config = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$config) {
        echo '<div class="alert alert-danger text-center">Config Orthanc tidak ada</div>';
        exit;
    }

    $url  = rtrim($config['url_connection_orthanc'], '/');
    $user = $config['username_connection_orthanc'];
    $pass = $config['password_connection_orthanc'];

    // ===================== CURL =====================
    function curlOrthanc($url, $user, $pass, $method='GET', $payload=null) {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_USERPWD => "$user:$pass",
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ]);
        if ($payload) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        }
        $res = curl_exec($ch);
        $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return [$res, $http];
    }

    // ===================== 1. WORKLIST =====================
    list($resWL, $httpWL) = curlOrthanc("$url/worklists/$accession_number", $user, $pass);

    $isWorklist = false;
    $isStudy    = false;

    if ($httpWL == 200) {
        $data = json_decode($resWL, true);
        $Tags = $data['Tags'] ?? [];
        $isWorklist = true;
    } else {

        // ===================== 2. STUDY =====================
        $payload = [
            "Level" => "Study",
            "Query" => [
                "AccessionNumber" => $accession_number
            ]
        ];

        list($resFind, $httpFind) = curlOrthanc("$url/tools/find", $user, $pass, 'POST', $payload);

        if ($httpFind != 200) {
            echo '<div class="alert alert-danger text-center">Data tidak ditemukan</div>';
            exit;
        }

        $list = json_decode($resFind, true);

        if (empty($list[0])) {
            echo '<div class="alert alert-warning text-center">Tidak ada data</div>';
            exit;
        }

        $studyID = $list[0];

        list($resStudy, $httpStudy) = curlOrthanc("$url/studies/$studyID", $user, $pass);

        if ($httpStudy != 200) {
            echo '<div class="alert alert-danger text-center">Gagal ambil study</div>';
            exit;
        }

        $study = json_decode($resStudy, true);

        $Tags = $study['MainDicomTags'] ?? [];
        $isStudy = true;
    }

    // ===================== SAFE =====================
    function safe($arr, $key) {
        return $arr[$key] ?? '-';
    }
?>

<!-- ===================== STATUS ===================== -->
<div class="alert <?= $isWorklist ? 'alert-info' : 'alert-success' ?> text-center">
    <small>
        <?= $isWorklist ? 'MENUNGGU PEMERIKSAAN (WORKLIST)' : 'SELESAI (STUDY DICOM)' ?>
    </small>
</div>

<!-- ===================== DATA PASIEN ===================== -->
<div class="row mb-2">
    <div class="col-4">Accession Number</div>
    <div class="col-8"><?= safe($Tags,'AccessionNumber') ?></div>
</div>

<div class="row mb-2">
    <div class="col-4">Nama</div>
    <div class="col-8"><?= safe($Tags,'PatientName') ?></div>
</div>

<div class="row mb-2">
    <div class="col-4">No RM</div>
    <div class="col-8"><?= safe($Tags,'PatientID') ?></div>
</div>

<div class="row mb-2">
    <div class="col-4">Tgl Lahir</div>
    <div class="col-8"><?= safe($Tags,'PatientBirthDate') ?></div>
</div>

<div class="row mb-2">
    <div class="col-4">Gender</div>
    <div class="col-8"><?= safe($Tags,'PatientSex') ?></div>
</div>

<?php if ($isStudy): ?>

<hr>

<!-- ===================== METADATA DICOM ===================== -->
<h6>Metadata DICOM</h6>

<div class="row mb-2">
    <div class="col-4">Study Date</div>
    <div class="col-8"><?= safe($Tags,'StudyDate') ?></div>
</div>

<div class="row mb-2">
    <div class="col-4">Study Description</div>
    <div class="col-8"><?= safe($Tags,'StudyDescription') ?></div>
</div>

<div class="row mb-2">
    <div class="col-4">Modality</div>
    <div class="col-8"><?= safe($Tags,'Modality') ?></div>
</div>

<div class="row mb-2">
    <div class="col-4">Institution</div>
    <div class="col-8"><?= safe($Tags,'InstitutionName') ?></div>
</div>

<hr>

<!-- ===================== VIEWER ===================== -->
<h6>Viewer</h6>

<?php
// URL Web Viewer Orthanc (default)
$viewerUrl = "$url/app/explorer.html#study?uuid=$studyID";
?>

<a href="<?= $viewerUrl ?>" target="_blank" class="btn btn-primary btn-sm w-100">
    Lihat Gambar DICOM
</a>

<?php endif; ?>