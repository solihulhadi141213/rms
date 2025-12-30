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
    !isset($_POST['id_questionnaire']) ||trim($_POST['id_questionnaire']) === '') {
    echo '
        <div class="alert alert-danger text-center">
            <small>ID Questionnaire tidak boleh kosong.</small>
        </div>
    ';
    exit;
}

$id_questionnaire = validateAndSanitizeInput($_POST['id_questionnaire']);

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
$url = $base_url . "/fhir-r4/v1/Questionnaire/" . urlencode($id_questionnaire);

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
            <small>Gagal mengambil data Questionnaire (HTTP ' . $http_code . ').</small>
          </div>';
    exit;
}

/* ============================================================
 * PARSE RESPONSE JSON
 * ============================================================ */
$data = json_decode($response, true);

if (
    !$data || !is_array($data) || isset($data['issue'])) {
    echo '
        <div class="alert alert-danger">
            <small>Data Observation tidak valid atau tidak ditemukan.</small>
        </div>
    ';
    exit;
}
/**
 * ============================================================
 * TAMPILAN DETAIL QUESTIONNAIRE - SATUSEHAT
 * ============================================================
 */

function labelTypeQuestionnaire($type)
{
    $map = [
        'boolean' => 'Ya / Tidak',
        'string'  => 'Teks Pendek',
        'text'    => 'Teks Panjang',
        'choice'  => 'Pilihan',
        'integer' => 'Angka',
        'decimal' => 'Desimal',
        'date'    => 'Tanggal',
        'dateTime'=> 'Tanggal & Waktu'
    ];

    return $map[$type] ?? ucfirst($type);
}

?>

<!-- INFORMASI UMUM -->
<table class="table table-sm table-bordered mb-4">
    <tr>
        <th width="25%">ID Questionnaire</th>
        <td><?= htmlspecialchars($data['id']) ?></td>
    </tr>
    <tr>
        <th>Nama</th>
        <td><?= htmlspecialchars($data['name'] ?? '-') ?></td>
    </tr>
    <tr>
        <th>Judul</th>
        <td><?= htmlspecialchars($data['title'] ?? '-') ?></td>
    </tr>
    <tr>
        <th>Status</th>
        <td>
            <span class="badge bg-success">
                <?= htmlspecialchars($data['status']) ?>
            </span>
        </td>
    </tr>
    <tr>
        <th>Versi</th>
        <td><?= htmlspecialchars($data['meta']['versionId'] ?? '-') ?></td>
    </tr>
    <tr>
        <th>Terakhir Update</th>
        <td>
            <?= isset($data['meta']['lastUpdated'])
                ? date('d-m-Y H:i:s', strtotime($data['meta']['lastUpdated']))
                : '-' ?>
        </td>
    </tr>
</table>

<!-- DAFTAR PERTANYAAN -->
<h6 class="mb-3">
    <i class="bi bi-list-check"></i> Daftar Pertanyaan
</h6>

<?php if (!empty($data['item'])): ?>
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="table-light">
                <tr>
                    <th width="5%">#</th>
                    <th width="30%">Link ID</th>
                    <th>Pertanyaan</th>
                    <th width="20%">Tipe Jawaban</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['item'] as $index => $item): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td>
                            <code><?= htmlspecialchars($item['linkId']) ?></code>
                        </td>
                        <td><?= htmlspecialchars($item['text'] ?? '-') ?></td>
                        <td>
                            <span class="badge bg-info text-dark">
                                <?= labelTypeQuestionnaire($item['type'] ?? '-') ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="alert alert-warning text-center">
        <small>Belum ada pertanyaan pada Questionnaire ini.</small>
    </div>
<?php endif; ?>