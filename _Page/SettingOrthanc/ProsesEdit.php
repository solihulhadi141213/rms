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
    $id_connection_orthanc       = validateAndSanitizeInput($_POST['id_connection_orthanc'] ?? '');
    $name_connection_orthanc     = validateAndSanitizeInput($_POST['name_connection_orthanc'] ?? '');
    $url_connection_orthanc      = validateAndSanitizeInput($_POST['url_connection_orthanc'] ?? '');
    $username_connection_orthanc = validateAndSanitizeInput($_POST['username_connection_orthanc'] ?? '');
    $password_connection_orthanc = validateAndSanitizeInput($_POST['password_connection_orthanc'] ?? '');
    $status_connection_orthanc   = validateAndSanitizeInput($_POST['status_connection_orthanc'] ?? '');
   
    // Validasi wajib
    if (
        empty($id_connection_orthanc) ||
        empty($name_connection_orthanc) ||
        empty($url_connection_orthanc) ||
        empty($username_connection_orthanc) ||
        empty($password_connection_orthanc)
    ) {
        $response['message'] = 'Semua field wajib diisi';
        echo json_encode($response);
        exit;
    }

    // Validasi panjang
    foreach ([
        $name_connection_orthanc,
        $username_connection_orthanc,
        $password_connection_orthanc,
        $status_connection_orthanc
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
        if ($status_connection_orthanc == '1') {
            $QryReset = $Conn->prepare("
                UPDATE connection_orthanc 
                SET status_connection_orthanc = 0
                WHERE id_connection_orthanc != ?
            ");
            if (!$QryReset) {
                throw new Exception($Conn->error);
            }

            $QryReset->bind_param("i", $id_connection_orthanc);
            $QryReset->execute();
            $QryReset->close();
        }

        // UPDATE utama (❗ HAPUS datetime_update JIKA TIDAK ADA)
        $QryUpdate = $Conn->prepare("
            UPDATE connection_orthanc SET
                name_connection_orthanc = ?,
                url_connection_orthanc = ?,
                username_connection_orthanc = ?,
                password_connection_orthanc = ?,
                status_connection_orthanc = ?
            WHERE id_connection_orthanc = ?
        ");

        if (!$QryUpdate) {
            throw new Exception($Conn->error);
        }

        $QryUpdate->bind_param(
            "ssssii",
            $name_connection_orthanc,
            $url_connection_orthanc,
            $username_connection_orthanc,
            $password_connection_orthanc,
            $status_connection_orthanc,
            $id_connection_orthanc
        );

        $QryUpdate->execute();
        $QryUpdate->close();

        $Conn->commit();

        $response['status']  = 'success';
        $response['message'] = 'Edit koneksi berhasil';

    } catch (Exception $e) {
        $Conn->rollback();
        $response['message'] = $e->getMessage();
    }

    echo json_encode($response);

?>
