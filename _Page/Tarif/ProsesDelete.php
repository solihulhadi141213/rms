<?php
    // koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    // Zona Waktu
    date_default_timezone_set("Asia/Jakarta");

    // Response default
    $response = [
        'status'  => 'error',
        'message' => 'Terjadi kesalahan sistem'
    ];

    // VALIDASI SESSION
    if (empty($SessionIdAccess)) {
        $response['message'] = 'Sesi akses telah berakhir. Silakan login ulang.';
        echo json_encode($response);
        exit;
    }

   // VALIDASI INPUT
    $id_master_service_prices = validateAndSanitizeInput($_POST['id_master_service_prices'] ?? '');

    if (empty($id_master_service_prices)) {
        $response['message'] = 'ID Tarif tidak boleh kosong.';
        echo json_encode($response);
        exit;
    }

    // PROSES DELETE
    $Conn->begin_transaction();

    try {

        $QryDelete = $Conn->prepare("DELETE FROM master_service_prices WHERE id_master_service_prices = ?
        ");
        if (!$QryDelete) {
            throw new Exception($Conn->error);
        }

        $QryDelete->bind_param("i", $id_master_service_prices);

        if (!$QryDelete->execute()) {
            throw new Exception('Gagal menghapus data.');
        }

        $QryDelete->close();
        $Conn->commit();

        $response['status']  = 'success';
        $response['message'] = 'Data berhasil dihapus.';

    } catch (Exception $e) {
        $Conn->rollback();
        $response['message'] = $e->getMessage();
    }

    // OUTPUT JSON
    echo json_encode($response);
?>
