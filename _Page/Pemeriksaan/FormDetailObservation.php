<?php
/**
 * ============================================================
 * DETAIL OBSERVATION RADIOLOGI - SATUSEHAT (SAFE VERSION)
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
    !isset($_POST['id_observation']) ||
    trim($_POST['id_observation']) === ''
) {
    echo '<div class="alert alert-danger text-center">
            <small>ID Observation tidak boleh kosong.</small>
          </div>';
    exit;
}

$id_observation = validateAndSanitizeInput($_POST['id_observation']);

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
$url = $base_url . "/fhir-r4/v1/Observation/" . urlencode($id_observation);

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
$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
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
            <small>Gagal mengambil data Observation (HTTP ' . $http_code . ').</small>
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
            <small>Data Observation tidak valid atau tidak ditemukan.</small>
          </div>';
    exit;
}

/* ============================================================
 * AMBIL DATA DENGAN DEFAULT VALUE
 * ============================================================ */
$observation_id   = $data['id'] ?? '-';
$status           = $data['status'] ?? '-';
$kategori         = $data['category'][0]['coding'][0]['display'] ?? '-';
$kode_pemeriksaan = $data['code']['coding'][0]['code'] ?? '-';
$nama_pemeriksaan = $data['code']['coding'][0]['display'] ?? '-';
$waktu_pemeriksaan= $data['effectiveDateTime'] ?? '-';
$hasil            = $data['valueString'] ?? '-';
$pasien           = $data['subject']['display'] ?? '-';
$dokter           = $data['performer'][0]['display'] ?? '-';
$faskes           = $data['performer'][1]['display'] ?? '-';

/* ============================================================
 * TAMPILKAN DATA
 * ============================================================ */
?>
<div class="table-responsive">
    <table class="table table-sm table-bordered">
        <tr>
            <th width="35%">ID Observation</th>
            <td><?= htmlspecialchars($observation_id) ?></td>
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
            <th>Waktu Pemeriksaan</th>
            <td><?= htmlspecialchars($waktu_pemeriksaan) ?></td>
        </tr>
        <tr>
            <th>Pasien</th>
            <td><?= htmlspecialchars($pasien) ?></td>
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
            <th>Hasil Observasi</th>
            <td><?= nl2br(htmlspecialchars($hasil)) ?></td>
        </tr>
    </table>
</div>
