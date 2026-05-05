<?php
// ===================== KONEKSI =====================
include "../../_Config/Connection.php";
include "../../_Config/GlobalFunction.php";
include "../../_Config/Session.php";

date_default_timezone_set("Asia/Jakarta");

// ===================== VALIDASI =====================
if (empty($SessionIdAccess)) {
    echo '<div class="alert alert-danger text-center">
            <small>Sesi habis</small>
          </div>';
    exit;
}

if (empty($_POST['accession_number'])) {
    echo '<div class="alert alert-danger text-center">
            <small>Accession Number kosong</small>
          </div>';
    exit;
}

$accession_number = validateAndSanitizeInput($_POST['accession_number']);

// ===================== CONFIG ORTHANC =====================
$stmt = $Conn->prepare("
    SELECT * 
    FROM connection_orthanc 
    WHERE status_connection_orthanc=1
");
$stmt->execute();

$config = $stmt->get_result()->fetch_assoc();

$stmt->close();

if (!$config) {
    echo '<div class="alert alert-danger text-center">
            Config Orthanc tidak ditemukan
          </div>';
    exit;
}

$url  = rtrim($config['url_connection_orthanc'], '/');
$user = $config['username_connection_orthanc'];
$pass = $config['password_connection_orthanc'];

// ===================== CURL FUNCTION =====================
function curlOrthanc($url, $user, $pass, $method='GET', $payload=null)
{
    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
        CURLOPT_USERPWD => "$user:$pass",
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json'
        ],
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false
    ]);

    if (!empty($payload)) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    }

    $response = curl_exec($ch);

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    return [$response, $httpCode];
}

// ===================== SAFE FUNCTION =====================
function safe($array, $key, $default='-')
{
    return $array[$key] ?? $default;
}

// ===================== FORMAT DICOM DATE =====================
function formatDicomDate($date)
{
    if (empty($date) || $date == '-') {
        return '-';
    }

    if (strlen($date) != 8) {
        return $date;
    }

    $year  = substr($date, 0, 4);
    $month = substr($date, 4, 2);
    $day   = substr($date, 6, 2);

    return "$day-$month-$year";
}

// ===================== FORMAT DICOM TIME =====================
function formatDicomTime($time)
{
    if (empty($time) || $time == '-') {
        return '-';
    }

    $time = substr($time, 0, 6);

    if (strlen($time) < 6) {
        return $time;
    }

    return substr($time,0,2).':'.
           substr($time,2,2).':'.
           substr($time,4,2);
}

// ============================================================
// STATUS
// ============================================================

$isStudy     = false;
$isWorklist  = false;

$study       = [];
$studyTags   = [];

$worklist    = [];
$worklistTags = [];

$studyID     = '';
$studyUID    = '';


// ============================================================
// 1. CARI STUDY DULU (PRIORITAS)
// ============================================================

$payload = [
    "Level" => "Study",
    "Query" => [
        "AccessionNumber" => $accession_number
    ]
];

list($resFind, $httpFind) = curlOrthanc(
    "$url/tools/find",
    $user,
    $pass,
    'POST',
    $payload
);

if ($httpFind == 200) {

    $findResult = json_decode($resFind, true);

    if (!empty($findResult[0])) {

        $studyID = $findResult[0];

        list($resStudy, $httpStudy) = curlOrthanc(
            "$url/studies/$studyID",
            $user,
            $pass
        );

        if ($httpStudy == 200) {

            $study = json_decode($resStudy, true);

            $studyTags = $study['MainDicomTags'] ?? [];

            $studyUID = safe($studyTags, 'StudyInstanceUID', '');

            $isStudy = true;
        }
    }
}

// ============================================================
// 2. CEK WORKLIST
// ============================================================

list($resWL, $httpWL) = curlOrthanc(
    "$url/worklists/$accession_number",
    $user,
    $pass
);

if ($httpWL == 200) {

    $worklist = json_decode($resWL, true);

    $worklistTags = $worklist['Tags'] ?? [];

    $isWorklist = true;
}

// ============================================================
// VALIDASI DATA
// ============================================================

if (!$isStudy && !$isWorklist) {

    echo '
    <div class="alert alert-warning text-center">
        Data tidak ditemukan
    </div>
    ';

    exit;
}

?>

<!-- ===================================================== -->
<!-- STATUS -->
<!-- ===================================================== -->

<?php if($isStudy): ?>

<div class="alert alert-success text-center mb-3">
    <small>
        STUDY DICOM SUDAH TERSEDIA
    </small>
</div>

<?php elseif($isWorklist): ?>

<div class="alert alert-info text-center mb-3">
    <small>
        MENUNGGU PEMERIKSAAN (WORKLIST)
    </small>
</div>

<?php endif; ?>

<!-- ===================================================== -->
<!-- DATA PASIEN -->
<!-- ===================================================== -->

<?php
/*
|--------------------------------------------------------------------------
| PRIORITAS TAG
|--------------------------------------------------------------------------
| Jika Study sudah ada gunakan data Study
| Jika belum gunakan Worklist
|--------------------------------------------------------------------------
*/

$Tags = $isStudy ? $studyTags : $worklistTags;
?>

<h6 class="mb-3">
    Informasi Pasien
</h6>

<div class="row mb-2">
    <div class="col-4">Accession</div>
    <div class="col-8">
        <?= safe($Tags,'AccessionNumber') ?>
    </div>
</div>

<div class="row mb-2">
    <div class="col-4">Nama</div>
    <div class="col-8">
        <?= safe($Tags,'PatientName') ?>
    </div>
</div>

<div class="row mb-2">
    <div class="col-4">No RM</div>
    <div class="col-8">
        <?= safe($Tags,'PatientID') ?>
    </div>
</div>

<div class="row mb-2">
    <div class="col-4">Tanggal Lahir</div>
    <div class="col-8">
        <?= formatDicomDate(safe($Tags,'PatientBirthDate')) ?>
    </div>
</div>

<div class="row mb-2">
    <div class="col-4">Gender</div>
    <div class="col-8">
        <?= safe($Tags,'PatientSex') ?>
    </div>
</div>

<hr>

<!-- ===================================================== -->
<!-- WORKLIST -->
<!-- ===================================================== -->

<?php if($isWorklist): ?>

<h6 class="mb-3">
    Informasi Worklist
</h6>

<div class="row mb-2">
    <div class="col-4">Modality</div>
    <div class="col-8">
        <?= safe($worklistTags,'Modality') ?>
    </div>
</div>

<div class="row mb-2">
    <div class="col-4">Study Description</div>
    <div class="col-8">
        <?= safe($worklistTags,'StudyDescription') ?>
    </div>
</div>

<div class="row mb-2">
    <div class="col-4">Requested Procedure</div>
    <div class="col-8">
        <?= safe($worklistTags,'RequestedProcedureDescription') ?>
    </div>
</div>

<div class="row mb-2">
    <div class="col-4">Scheduled Date</div>
    <div class="col-8">
        <?= formatDicomDate(
            safe($worklistTags,'ScheduledProcedureStepStartDate')
        ) ?>
    </div>
</div>

<div class="row mb-2">
    <div class="col-4">Scheduled Time</div>
    <div class="col-8">
        <?= formatDicomTime(
            safe($worklistTags,'ScheduledProcedureStepStartTime')
        ) ?>
    </div>
</div>

<hr>

<?php endif; ?>

<!-- ===================================================== -->
<!-- STUDY DICOM -->
<!-- ===================================================== -->

<?php if($isStudy): ?>

<h6 class="mb-3">
    Informasi Study DICOM
</h6>

<div class="row mb-2">
    <div class="col-4">Study Date</div>
    <div class="col-8">
        <?= formatDicomDate(
            safe($studyTags,'StudyDate')
        ) ?>
    </div>
</div>

<div class="row mb-2">
    <div class="col-4">Study Time</div>
    <div class="col-8">
        <?= formatDicomTime(
            safe($studyTags,'StudyTime')
        ) ?>
    </div>
</div>

<div class="row mb-2">
    <div class="col-4">Description</div>
    <div class="col-8">
        <?= safe($studyTags,'StudyDescription') ?>
    </div>
</div>

<div class="row mb-2">
    <div class="col-4">Modality</div>
    <div class="col-8">
        <?= safe($studyTags,'ModalitiesInStudy') ?>
    </div>
</div>

<div class="row mb-2">
    <div class="col-4">Institution</div>
    <div class="col-8">
        <?= safe($studyTags,'InstitutionName') ?>
    </div>
</div>

<div class="row mb-2">
    <div class="col-4">Study UID</div>
    <div class="col-8" style="word-break:break-all;">
        <?= safe($studyTags,'StudyInstanceUID') ?>
    </div>
</div>

<?php
// ============================================================
// SERIES & INSTANCE
// ============================================================

$totalSeries   = count($study['Series'] ?? []);
$totalInstance = count($study['Instances'] ?? []);
?>

<div class="row mb-2">
    <div class="col-4">Series</div>
    <div class="col-8">
        <?= $totalSeries ?>
    </div>
</div>

<div class="row mb-2">
    <div class="col-4">Images</div>
    <div class="col-8">
        <?= $totalInstance ?>
    </div>
</div>

<hr>

<!-- ===================================================== -->
<!-- VIEWER -->
<!-- ===================================================== -->

<h6 class="mb-3">
    Viewer DICOM
</h6>

<?php
/*
|--------------------------------------------------------------------------
| STONE VIEWER
|--------------------------------------------------------------------------
| Gunakan StudyInstanceUID
|--------------------------------------------------------------------------
*/

$viewerStone = "$url/stone-webviewer/index.html?study=".$studyUID;

/*
|--------------------------------------------------------------------------
| ORTHANC EXPLORER
|--------------------------------------------------------------------------
*/

$viewerExplorer = "$url/app/explorer.html#study?uuid=".$studyID;
?>

<div class="d-grid gap-2">

    <a href="<?= $viewerStone ?>"
       target="_blank"
       class="btn btn-primary btn-sm">

        Buka Stone Web Viewer

    </a>

    <a href="<?= $viewerExplorer ?>"
       target="_blank"
       class="btn btn-secondary btn-sm">

        Buka Orthanc Explorer

    </a>

</div>

<?php endif; ?>