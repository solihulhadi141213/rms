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
$id_connection_pacs       = validateAndSanitizeInput($_POST['id_connection_pacs'] ?? '');
$name_connection_pacs     = validateAndSanitizeInput($_POST['name_connection_pacs'] ?? '');
$url_connection_pacs      = validateAndSanitizeInput($_POST['url_connection_pacs'] ?? '');
$username_connection_pacs = validateAndSanitizeInput($_POST['username_connection_pacs'] ?? '');
$password_connection_pacs = validateAndSanitizeInput($_POST['password_connection_pacs'] ?? '');
$status_connection_pacs   = validateAndSanitizeInput($_POST['status_connection_pacs'] ?? '');

// Validasi wajib
if (
    empty($id_connection_pacs) ||
    empty($name_connection_pacs) ||
    empty($url_connection_pacs) ||
    empty($username_connection_pacs) ||
    empty($password_connection_pacs)
) {
    $response['message'] = 'Semua field wajib diisi';
    echo json_encode($response);
    exit;
}

// Validasi panjang
foreach ([
    $name_connection_pacs,
    $url_connection_pacs,
    $username_connection_pacs,
    $password_connection_pacs
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
    if ($status_connection_pacs == '1') {
        $QryReset = $Conn->prepare("
            UPDATE connection_pacs 
            SET status_connection_pacs = 0
            WHERE id_connection_pacs != ?
        ");
        if (!$QryReset) {
            throw new Exception($Conn->error);
        }

        $QryReset->bind_param("i", $id_connection_pacs);
        $QryReset->execute();
        $QryReset->close();
    }

    // UPDATE utama (❗ HAPUS datetime_update JIKA TIDAK ADA)
    $QryUpdate = $Conn->prepare("
        UPDATE connection_pacs SET
            name_connection_pacs   = ?,
            url_connection_pacs    = ?,
            username_connection_pacs = ?,
            password_connection_pacs = ?,
            status_connection_pacs = ?
        WHERE id_connection_pacs = ?
    ");

    if (!$QryUpdate) {
        throw new Exception($Conn->error);
    }

    $QryUpdate->bind_param(
        "ssssii",
        $name_connection_pacs,
        $url_connection_pacs,
        $username_connection_pacs,
        $password_connection_pacs,
        $status_connection_pacs,
        $id_connection_pacs
    );

    $QryUpdate->execute();
    $QryUpdate->close();

    $Conn->commit();

    $response['status']  = 'success';
    $response['message'] = 'Edit koneksi PACS berhasil';

} catch (Exception $e) {
    $Conn->rollback();
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
