<?php
    /* Header JSON */
    header('Content-Type: application/json');

    /* Koneksi Database */
    require_once "../../_Config/Connection.php";
    require_once "../../_Config/GlobalFunction.php";
    require_once "../../_Config/Session.php";

    /* Response default */
    $response = [
        'status'  => 'error',
        'message' => 'Terjadi kesalahan sistem'
    ];

    // Validasi Sesi Akses
    if (empty($SessionIdAccess)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Sesi Akses Sudah Berakhir! Silahkan Login Ulang!'
        ]);
        exit;
    }

    // Validasi Data Wajib (Mandatory)
    if (empty($_POST['id_google_credential'])) {
        echo json_encode(['status'  => 'error', 'message' => 'ID Credential Tidak Boleh Kosong!']);
        exit;
    }

    if (!isset($_POST['status_baru'])) {
        echo json_encode(['status'  => 'error', 'message' => 'Status Credential Tidak Boleh Kosong!']);
        exit;
    }

    // Buat Variabel
    $id_google_credential = validateAndSanitizeInput($_POST['id_google_credential']);
    $status_baru          = validateAndSanitizeInput($_POST['status_baru']);

    // Validasi Nilai Status
    if ($status_baru !== '0' && $status_baru !== '1') {
        echo json_encode(['status'  => 'error', 'message' => 'Status Credential Tidak Valid!']);
        exit;
    }

    // Validasi Data Tujuan Harus Ada
    $QryExist = $Conn->prepare("SELECT id_google_credential FROM google_credential WHERE id_google_credential = ?");
    $QryExist->bind_param("i", $id_google_credential);
    if (!$QryExist->execute()) {
        $error = $Conn->error;
        echo json_encode(['status'  => 'error', 'message' => 'Terjadi kesalahan pada saat membuka data dari database!<br>Keterangan : '.$error.'']);
        exit;
    }

    $ResultExist = $QryExist->get_result();
    $DataExist   = $ResultExist->fetch_assoc();
    $QryExist->close();

    if (empty($DataExist['id_google_credential'])) {
        echo json_encode(['status'  => 'error', 'message' => 'Data Google Credential tidak ditemukan']);
        exit;
    }

    $Conn->begin_transaction();

    try {
        // Jika status baru aktif (1), maka data lain harus nonaktif (0)
        if ((int)$status_baru === 1) {
            $QryReset = $Conn->prepare("UPDATE google_credential SET status = 0 WHERE id_google_credential != ?");
            if (!$QryReset) {
                throw new Exception($Conn->error);
            }
            $QryReset->bind_param("i", $id_google_credential);
            if (!$QryReset->execute()) {
                throw new Exception($Conn->error);
            }
            $QryReset->close();
        }

        // Update status data target
        $QryUpdate = $Conn->prepare("UPDATE google_credential SET status = ? WHERE id_google_credential = ?");
        if (!$QryUpdate) {
            throw new Exception($Conn->error);
        }
        $QryUpdate->bind_param("ii", $status_baru, $id_google_credential);
        if (!$QryUpdate->execute()) {
            throw new Exception($Conn->error);
        }
        $QryUpdate->close();

        $Conn->commit();

        echo json_encode([
            'status'  => 'success',
            'message' => 'Status Google Credential berhasil diperbarui'
        ]);
    } catch (Exception $e) {
        $Conn->rollback();
        echo json_encode([
            'status'  => 'error',
            'message' => 'Gagal memperbarui status!<br>Keterangan : '.$e->getMessage()
        ]);
    }

    $Conn->close();
?>
