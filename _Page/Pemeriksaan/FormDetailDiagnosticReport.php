<?php
/**
 * ============================================================
 * DETAIL DIAGNOSTIC REPORT RADIOLOGI - SATUSEHAT
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
if (
    !isset($_POST['id_diagnostic_report']) ||
    trim($_POST['id_diagnostic_report']) === ''
) {
    echo '<div class="alert alert-danger text-center">
            <small>ID Diagnostic Report tidak boleh kosong.</small>
          </div>';
    exit;
}

$id_diagnostic_report = validateAndSanitizeInput($_POST['id_diagnostic_report']);

/* ============================================================
 * TOKEN SATUSEHAT
 * ============================================================ */
$tokenResult = generateTokenSatuSehat($Conn);
if (
    empty($tokenResult) ||
    $tokenResult['status'] !== 'success' ||
    empty($tokenResult['token'])
) {
    echo '<div class="alert alert-danger text-center">
            <small>Gagal mendapatkan token SATUSEHAT.</small>
          </div>';
    exit;
}
$token = $tokenResult['token'];

/* ============================================================
 * KONFIGURASI KONEKSI SATUSEHAT
 * ============================================================ */
$status_connection = 1;
$stmt = $Conn->prepare("
    SELECT url_connection_satu_sehat
    FROM connection_satu_sehat
    WHERE status_connection_satu_sehat = ?
    LIMIT 1
");
$stmt->bind_param("i", $status_connection);
$stmt->execute();
$config = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (empty($config['url_connection_satu_sehat'])) {
    echo '<div class="alert alert-danger text-center">
            <small>Konfigurasi koneksi SATUSEHAT tidak ditemukan.</small>
          </div>';
    exit;
}

$base_url = rtrim($config['url_connection_satu_sehat'], '/');
$url = $base_url . "/fhir-r4/v1/DiagnosticReport/" . urlencode($id_diagnostic_report);

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

$response   = curl_exec($curl);
$http_code  = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$curl_error = curl_error($curl);
curl_close($curl);

if ($curl_error) {
    echo '<div class="alert alert-danger">
            <small>CURL Error: ' . htmlspecialchars($curl_error) . '</small>
          </div>';
    exit;
}

if ($http_code !== 200) {
    echo '<div class="alert alert-danger">
            <small>Gagal mengambil data Diagnostic Report (HTTP ' . $http_code . ').</small>
          </div>';
    exit;
}

/* ============================================================
 * PARSE RESPONSE JSON
 * ============================================================ */
$data = json_decode($response, true);

if (
    !$data ||
    !is_array($data) ||
    isset($data['issue'])
) {
    echo '<div class="alert alert-danger">
            <small>Data Diagnostic Report tidak valid atau tidak ditemukan.</small>
          </div>';
    exit;
}

/* ============================================================
 * AMBIL DATA DENGAN DEFAULT VALUE
 * ============================================================ */
$id_report        = $data['id'] ?? '-';
$status           = $data['status'] ?? '-';
$kategori          = $data['category'][0]['coding'][0]['display'] ?? '-';
$kode_pemeriksaan  = $data['code']['coding'][0]['code'] ?? '-';
$nama_pemeriksaan  = $data['code']['coding'][0]['display'] ?? '-';
$kesimpulan        = $data['conclusion'] ?? '-';

$kode_kesimpulan   = $data['conclusionCode'][0]['coding'][0]['code'] ?? '-';
$nama_kesimpulan   = $data['conclusionCode'][0]['coding'][0]['display'] ?? '-';

$dokter            = $data['performer'][0]['display'] ?? '-';
$faskes            = $data['performer'][1]['display'] ?? '-';

$id_observation    = $data['result'][0]['reference'] ?? '-';
$id_service_req    = $data['basedOn'][0]['reference'] ?? '-';
$id_imaging        = $data['imagingStudy'][0]['reference'] ?? '-';

$last_update       = $data['meta']['lastUpdated'] ?? '-';

/* ============================================================
 * TAMPILKAN DATA
 * ============================================================ */
?>
<div class="table-responsive">
    <table class="table table-bordered table-sm">
        <tr>
            <th width="35%">ID Diagnostic Report</th>
            <td><?= htmlspecialchars($id_report) ?></td>
        </tr>
        <tr>
            <th>Status</th>
            <td><?= htmlspecialchars($status) ?></td>
        </tr>
        <tr>
            <th>Kategori</th>
            <td><?= htmlspecialchars($kategori) ?></td>
        </tr>
        <tr>
            <th>Kode Pemeriksaan</th>
            <td><?= htmlspecialchars($kode_pemeriksaan) ?></td>
        </tr>
        <tr>
            <th>Nama Pemeriksaan</th>
            <td><?= htmlspecialchars($nama_pemeriksaan) ?></td>
        </tr>
        <tr>
            <th>Dokter Radiologi</th>
            <td><?= htmlspecialchars($dokter) ?></td>
        </tr>
        <tr>
            <th>Fasilitas Kesehatan</th>
            <td><?= htmlspecialchars($faskes) ?></td>
        </tr>
        <tr>
            <th>Kesimpulan</th>
            <td><?= nl2br(htmlspecialchars($kesimpulan)) ?></td>
        </tr>
        <tr>
            <th>Kode Kesimpulan (ICD-10)</th>
            <td><?= htmlspecialchars($kode_kesimpulan) ?> – <?= htmlspecialchars($nama_kesimpulan) ?></td>
        </tr>
        <tr>
            <th>Observation Terkait</th>
            <td><?= htmlspecialchars($id_observation) ?></td>
        </tr>
        <tr>
            <th>Service Request</th>
            <td><?= htmlspecialchars($id_service_req) ?></td>
        </tr>
        <tr>
            <th>Imaging Study</th>
            <td><?= htmlspecialchars($id_imaging) ?></td>
        </tr>
        <tr>
            <th>Terakhir Diperbarui</th>
            <td><?= htmlspecialchars($last_update) ?></td>
        </tr>
    </table>
</div>
