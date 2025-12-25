<?php
/**
 * ============================================================
 * DETAIL PROCEDURE RADIOLOGI - SATUSEHAT
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
    echo '<div class="alert alert-danger text-center"><small>Sesi berakhir. Silakan login ulang.</small></div>';
    exit;
}

/* ============================================================
 * VALIDASI INPUT
 * ============================================================ */
if (empty($_POST['id_procedure'])) {
    echo '<div class="alert alert-danger text-center"><small>ID Procedure tidak boleh kosong.</small></div>';
    exit;
}

$id_procedure = validateAndSanitizeInput($_POST['id_procedure']);

/* ============================================================
 * TOKEN SATUSEHAT
 * ============================================================ */
$tokenResult = generateTokenSatuSehat($Conn);
if ($tokenResult['status'] !== 'success') {
    echo '<div class="alert alert-danger text-center"><small>'.$tokenResult['message'].'</small></div>';
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
");
$stmt->bind_param("i", $status_connection);
$stmt->execute();
$config = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$config) {
    echo '<div class="alert alert-danger text-center"><small>Koneksi SATUSEHAT tidak ditemukan.</small></div>';
    exit;
}

$base_url = rtrim($config['url_connection_satu_sehat'], '/');
$url = $base_url . "/fhir-r4/v1/Procedure/" . $id_procedure;

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
$error = curl_error($curl);
curl_close($curl);

if ($error) {
    echo '<div class="alert alert-danger"><small>'.$error.'</small></div>';
    exit;
}

$data = json_decode($response, true);
if (!$data || isset($data['issue'])) {
    echo '<div class="alert alert-danger"><small>Data Procedure tidak ditemukan.</small></div>';
    exit;
}

/* ============================================================
 * EKSTRAKSI DATA PROCEDURE
 * ============================================================ */
$id         = $data['id'] ?? '-';
$status     = strtoupper($data['status'] ?? '-');
$subject    = $data['subject']['reference'] ?? '-';
$encounter  = $data['encounter']['reference'] ?? '-';

$code_text  = $data['code']['text'] ?? '-';
$code_value = $data['code']['coding'][0]['code'] ?? '-';

$performed  = $data['performedDateTime'] ?? '';
$performed  = $performed ? date('d-m-Y H:i', strtotime($performed)) : '-';

$doctor = '-';
if (!empty($data['performer'][0]['actor']['display'])) {
    $doctor = $data['performer'][0]['actor']['display'];
}

/* ============================================================
 * STATUS BADGE
 * ============================================================ */
$badge = 'secondary';
if ($status === 'COMPLETED') $badge = 'success';
elseif ($status === 'IN-PROGRESS') $badge = 'warning';
elseif ($status === 'STOPPED') $badge = 'danger';

?>

<!-- ============================================================
     TAMPILAN DETAIL PROCEDURE
============================================================ -->
<div class="row">
    <div class="col-12">
        <div class="table table-responsive">
            <table class="table table-sm table-bordered">
                <tr>
                    <th width="35%">ID Procedure</th>
                    <td><?= htmlspecialchars($id) ?></td>
                </tr>

                <tr>
                    <th>Status</th>
                    <td>
                        <span class="badge bg-<?= $badge ?>">
                            <?= htmlspecialchars($status) ?>
                        </span>
                    </td>
                </tr>

                <tr>
                    <th>Jenis Tindakan</th>
                    <td><?= htmlspecialchars($code_text) ?></td>
                </tr>

                <tr>
                    <th>Kode Tindakan</th>
                    <td><?= htmlspecialchars($code_value) ?></td>
                </tr>

                <tr>
                    <th>Pasien</th>
                    <td><?= htmlspecialchars($subject) ?></td>
                </tr>

                <tr>
                    <th>Encounter</th>
                    <td><?= htmlspecialchars($encounter) ?></td>
                </tr>

                <tr>
                    <th>Dokter / Pelaksana</th>
                    <td><?= htmlspecialchars($doctor) ?></td>
                </tr>

                <tr>
                    <th>Waktu Pelaksanaan</th>
                    <td><?= htmlspecialchars($performed) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
