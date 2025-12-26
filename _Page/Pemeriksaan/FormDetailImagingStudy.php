<?php
/**
 * ============================================================
 * DETAIL IMAGING STUDY RADIOLOGI - SATUSEHAT
 * ============================================================
 */

include "../../_Config/Connection.php";
include "../../_Config/GlobalFunction.php";
include "../../_Config/Session.php";

date_default_timezone_set("Asia/Jakarta");

/* ============================================================
 * VALIDASI SESSION
 * ============================================================ */
if (empty($SessionIdAccess)) {
    echo '<div class="alert alert-danger text-center">
            <small>Sesi berakhir. Silakan login ulang.</small>
          </div>';
    exit;
}

/* ============================================================
 * VALIDASI INPUT
 * ============================================================ */
if (empty($_POST['id_imaging_study'])) {
    echo '<div class="alert alert-danger text-center">
            <small>ID Imaging Study tidak boleh kosong.</small>
          </div>';
    exit;
}

$id_imaging_study = validateAndSanitizeInput($_POST['id_imaging_study']);

/* ============================================================
 * TOKEN SATUSEHAT
 * ============================================================ */
$tokenResult = generateTokenSatuSehat($Conn);
if ($tokenResult['status'] !== 'success') {
    echo '<div class="alert alert-danger text-center">
            <small>'.$tokenResult['message'].'</small>
          </div>';
    exit;
}
$token = $tokenResult['token'];

/* ============================================================
 * KONFIGURASI KONEKSI SATUSEHAT
 * ============================================================ */
$stmt = $Conn->prepare("
    SELECT url_connection_satu_sehat
    FROM connection_satu_sehat
    WHERE status_connection_satu_sehat = 1
");
$stmt->execute();
$config = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$config) {
    echo '<div class="alert alert-danger text-center">
            <small>Koneksi SATUSEHAT tidak ditemukan.</small>
          </div>';
    exit;
}

$url = rtrim($config['url_connection_satu_sehat'], '/') .
       "/fhir-r4/v1/ImagingStudy/" . $id_imaging_study;

/* ============================================================
 * CURL REQUEST
 * ============================================================ */
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json'
    ],
    CURLOPT_TIMEOUT => 30,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false
]);

$response = curl_exec($curl);
$error    = curl_error($curl);
curl_close($curl);

if ($error) {
    echo '<div class="alert alert-danger"><small>'.$error.'</small></div>';
    exit;
}

$data = json_decode($response, true);
if (!$data || isset($data['issue'])) {
    echo '<div class="alert alert-danger">
            <small>Data Imaging Study tidak ditemukan.</small>
          </div>';
    exit;
}

/* ============================================================
 * EKSTRAKSI DATA
 * ============================================================ */
$id                = $data['id'] ?? '-';
$status            = $data['status'] ?? '-';
$resourceType      = $data['resourceType'] ?? '-';
$numberOfSeries    = $data['numberOfSeries'] ?? 0;
$numberOfInstances = $data['numberOfInstances'] ?? 0;

$subjectDisplay = $data['subject']['display'] ?? '-';
$subjectRef     = $data['subject']['reference'] ?? '-';

$basedOnRef = $data['basedOn'][0]['reference'] ?? '-';

$identifierValue = $data['identifier'][0]['value'] ?? '-';
$identifierSystem = $data['identifier'][0]['system'] ?? '-';

$lastUpdated = $data['meta']['lastUpdated'] ?? '-';
$versionId   = $data['meta']['versionId'] ?? '-';
?>

<!-- ============================================================
     TAMPILAN DETAIL IMAGING STUDY
============================================================= -->
<table class="table table-bordered table-sm">
    <tr>
        <th width="30%">ImagingStudy ID</th>
        <td><?= htmlspecialchars($id) ?></td>
    </tr>
    <tr>
        <th>Status</th>
        <td><?= htmlspecialchars($status) ?></td>
    </tr>
    <tr>
        <th>Resource Type</th>
        <td><?= htmlspecialchars($resourceType) ?></td>
    </tr>
    <tr>
        <th>Accession Number</th>
        <td><?= htmlspecialchars($identifierValue) ?></td>
    </tr>
    <tr>
        <th>Accession System</th>
        <td><?= htmlspecialchars($identifierSystem) ?></td>
    </tr>
    <tr>
        <th>Patient</th>
        <td><?= htmlspecialchars($subjectDisplay) ?><br>
            <small class="text-muted"><?= htmlspecialchars($subjectRef) ?></small>
        </td>
    </tr>
    <tr>
        <th>Based On (ServiceRequest)</th>
        <td><?= htmlspecialchars($basedOnRef) ?></td>
    </tr>
    <tr>
        <th>Jumlah Series</th>
        <td><?= $numberOfSeries ?></td>
    </tr>
    <tr>
        <th>Jumlah Instance</th>
        <td><?= $numberOfInstances ?></td>
    </tr>
    <tr>
        <th>Last Updated</th>
        <td><?= htmlspecialchars($lastUpdated) ?></td>
    </tr>
    <tr>
        <th>Version ID</th>
        <td><?= htmlspecialchars($versionId) ?></td>
    </tr>
</table>

<h6 class="mt-4">Detail Series</h6>
<?php if (!empty($data['series'])): ?>
    <table class="table table-bordered table-sm">
        <thead class="table-light">
            <tr>
                <th>No</th>
                <th>UID</th>
                <th>Modality</th>
                <th>Deskripsi</th>
                <th>Jumlah Instance</th>
                <th>Started</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data['series'] as $i => $series): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($series['uid'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($series['modality']['code'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($series['description'] ?? '-') ?></td>
                    <td><?= $series['numberOfInstances'] ?? 0 ?></td>
                    <td><?= htmlspecialchars($series['started'] ?? '-') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <div class="alert alert-warning">
        Tidak ada data series.
    </div>
<?php endif; ?>
