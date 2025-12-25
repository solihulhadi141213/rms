<?php
/**
 * ============================================================
 * DETAIL ORDER PACS
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
if (empty($_POST['accession_number'])) {
    echo '<div class="alert alert-danger text-center">
            <small>Accession Number tidak boleh kosong.</small>
          </div>';
    exit;
}

$accession_number = validateAndSanitizeInput($_POST['accession_number']);

/* ============================================================
 * TOKEN PACS
 * ============================================================ */
$tokenResult = generateTokenPacs($Conn);
if ($tokenResult['status'] !== 'success') {
    echo '<div class="alert alert-danger text-center">
            <small>Gagal mengakses PACS<br>Error: '.$tokenResult['message'].'</small>
          </div>';
    exit;
}
$tokenPacs = $tokenResult['token'];

/* ============================================================
 * KONFIGURASI PACS
 * ============================================================ */
$stmt = $Conn->prepare("
    SELECT url_connection_pacs 
    FROM connection_pacs 
    WHERE status_connection_pacs = 1 
    LIMIT 1
");
$stmt->execute();
$config = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$config) {
    echo '<div class="alert alert-danger text-center">
            <small>Konfigurasi PACS tidak ditemukan.</small>
          </div>';
    exit;
}

$url = rtrim($config['url_connection_pacs'], '/')
     . '/api/dicom/patient-worklist?accession_number='
     . urlencode($accession_number);

/* ============================================================
 * CURL REQUEST
 * ============================================================ */
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
    echo '<div class="alert alert-danger">
            <small>'.$error.'</small>
          </div>';
    exit;
}

/* ============================================================
 * DECODE JSON
 * ============================================================ */
$result = json_decode($response, true);

if (json_last_error() !== JSON_ERROR_NONE || empty($result['data'])) {
    echo '<div class="alert alert-warning text-center">
            <small>Data order PACS tidak ditemukan.</small>
          </div>';
    exit;
}

$order = $result['data'][0]; // detail order
?>

<table class="table table-sm table-bordered">
    <tr>
        <th width="30%">Accession Number</th>
        <td><?= htmlspecialchars($order['accession_number'] ?? '-') ?></td>
    </tr>
    <tr>
        <th>Nama Pasien</th>
        <td><?= htmlspecialchars($order['patient_name'] ?? '-') ?></td>
    </tr>
    <tr>
        <th>ID Pasien</th>
        <td><?= htmlspecialchars($order['patient_id'] ?? '-') ?></td>
    </tr>
    <tr>
        <th>Tanggal Lahir</th>
        <td><?= !empty($order['patient_birth_date']) 
                ? date('d-m-Y', strtotime($order['patient_birth_date'])) 
                : '-' ?></td>
    </tr>
    <tr>
        <th>Jenis Kelamin</th>
        <td><?= htmlspecialchars($order['patient_sex'] ?? '-') ?></td>
    </tr>
    <tr>
        <th>Modality</th>
        <td><span class="badge bg-info"><?= htmlspecialchars($order['modality'] ?? '-') ?></span></td>
    </tr>
    <tr>
        <th>Pemeriksaan</th>
        <td><?= htmlspecialchars($order['requested_procedure_name'] ?? '-') ?></td>
    </tr>
    <tr>
        <th>Kode Pemeriksaan</th>
        <td><?= htmlspecialchars($order['requested_procedure_code'] ?? '-') ?></td>
    </tr>
    <tr>
        <th>Dokter Pengirim</th>
        <td><?= htmlspecialchars($order['referring_doctor'] ?? '-') ?></td>
    </tr>
    <tr>
        <th>Dokter Penunjang</th>
        <td><?= htmlspecialchars($order['supporting_doctor'] ?? '-') ?></td>
    </tr>
    <tr>
        <th>Tanggal Registrasi</th>
        <td><?= htmlspecialchars($order['registration_date'] ?? '-') ?></td>
    </tr>
    <tr>
        <th>Status Order</th>
        <td>
            <span class="badge bg-success">
                <?= strtoupper($order['status'] ?? '-') ?>
            </span>
        </td>
    </tr>
    <tr>
        <th>Institusi</th>
        <td><?= htmlspecialchars($order['institution']['name'] ?? '-') ?></td>
    </tr>
</table>