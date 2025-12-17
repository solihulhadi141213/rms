<?php
include "../../_Config/Connection.php";
include "../../_Config/GlobalFunction.php";
include "../../_Config/Session.php";

date_default_timezone_set("Asia/Jakarta");

$response = [
    'status'  => 'error',
    'message' => 'Terjadi kesalahan sistem'
];

if (empty($SessionIdAccess)) {
    $response['message'] = 'Sesi berakhir';
    echo json_encode($response);
    exit;
}

// Ambil input
$id_connection_simrs     = validateAndSanitizeInput($_POST['id_connection_simrs'] ?? '');
$name_connection_simrs   = validateAndSanitizeInput($_POST['name_connection_simrs'] ?? '');
$url_connection_simrs    = validateAndSanitizeInput($_POST['url_connection_simrs'] ?? '');
$client_id               = validateAndSanitizeInput($_POST['client_id'] ?? '');
$client_key              = validateAndSanitizeInput($_POST['client_key'] ?? '');
$status_connection_simrs = validateAndSanitizeInput($_POST['status_connection_simrs'] ?? '');

// Validasi wajib
if (
    empty($id_connection_simrs) ||
    empty($name_connection_simrs) ||
    empty($url_connection_simrs) ||
    empty($client_id) ||
    empty($client_key)
) {
    $response['message'] = 'Semua field wajib diisi';
    echo json_encode($response);
    exit;
}

// Validasi panjang
foreach ([
    $name_connection_simrs,
    $url_connection_simrs,
    $client_id,
    $client_key
] as $val) {
    if (strlen($val) > 200) {
        $response['message'] = 'Panjang karakter maksimal 200';
        echo json_encode($response);
        exit;
    }
}

// Transaction
$Conn->begin_transaction();

try {

    // Jika aktif → nonaktifkan lainnya
    if ($status_connection_simrs == '1') {
        $QryReset = $Conn->prepare("
            UPDATE connection_simrs 
            SET status_connection_simrs = 0
            WHERE id_connection_simrs != ?
        ");
        if (!$QryReset) {
            throw new Exception($Conn->error);
        }

        $QryReset->bind_param("i", $id_connection_simrs);
        $QryReset->execute();
        $QryReset->close();
    }

    // UPDATE utama (❗ HAPUS datetime_update JIKA TIDAK ADA)
    $QryUpdate = $Conn->prepare("
        UPDATE connection_simrs SET
            name_connection_simrs   = ?,
            url_connection_simrs    = ?,
            client_id               = ?,
            client_key              = ?,
            status_connection_simrs = ?
        WHERE id_connection_simrs = ?
    ");

    if (!$QryUpdate) {
        throw new Exception($Conn->error);
    }

    $QryUpdate->bind_param(
        "ssssii",
        $name_connection_simrs,
        $url_connection_simrs,
        $client_id,
        $client_key,
        $status_connection_simrs,
        $id_connection_simrs
    );

    $QryUpdate->execute();
    $QryUpdate->close();

    $Conn->commit();

    $response['status']  = 'success';
    $response['message'] = 'Edit koneksi SIMRS berhasil';

} catch (Exception $e) {
    $Conn->rollback();
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
