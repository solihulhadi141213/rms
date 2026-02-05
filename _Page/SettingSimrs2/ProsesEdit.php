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
    $id_connection_simrs_old = validateAndSanitizeInput($_POST['id_connection_simrs_old'] ?? '');
    $name_connection         = validateAndSanitizeInput($_POST['name_connection'] ?? '');
    $base_url                = validateAndSanitizeInput($_POST['base_url'] ?? '');
    $username                = validateAndSanitizeInput($_POST['username'] ?? '');
    $password                = validateAndSanitizeInput($_POST['password'] ?? '');
    $status_connection       = validateAndSanitizeInput($_POST['status_connection'] ?? '');

    // Validasi wajib
    if (
        empty($id_connection_simrs_old) ||
        empty($name_connection) ||
        empty($base_url) ||
        empty($username) ||
        empty($password)
    ) {
        $response['message'] = 'Semua field wajib diisi';
        echo json_encode($response);
        exit;
    }

    // Validasi panjang
    foreach ([
        $name_connection,
        $base_url,
        $username,
        $password
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
        if ($status_connection == '1') {
            $QryReset = $Conn->prepare("UPDATE connection_simrs_old SET status_connection = 0 WHERE id_connection_simrs_old != ?");
            if (!$QryReset) {
                throw new Exception($Conn->error);
            }

            $QryReset->bind_param("i", $id_connection_simrs_old);
            $QryReset->execute();
            $QryReset->close();
        }

        // UPDATE utama (❗ HAPUS datetime_update JIKA TIDAK ADA)
        $QryUpdate = $Conn->prepare("UPDATE connection_simrs_old SET name_connection = ?, base_url = ?, username = ?, password = ?, status_connection = ? WHERE id_connection_simrs_old = ?
        ");

        if (!$QryUpdate) {
            throw new Exception($Conn->error);
        }

        $QryUpdate->bind_param(
            "ssssii",
            $name_connection,
            $base_url,
            $username,
            $password,
            $status_connection,
            $id_connection_simrs_old
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
?>
