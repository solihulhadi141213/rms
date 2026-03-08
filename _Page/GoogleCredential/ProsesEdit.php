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
    if(empty($_POST['id_google_credential'])){
        echo json_encode(['status'  => 'error','message' => 'ID Credential Tidak Boleh Kosong!']);
        exit;
    }

    if(empty($_POST['credential_env'])){
        echo json_encode(['status'  => 'error','message' => 'Environment Credential Tidak Boleh Kosong!']);
        exit;
    }

    if(empty($_POST['client_id'])){
        echo json_encode(['status'  => 'error','message' => 'Client ID Tidak Boleh Kosong!']);
        exit;
    }

    if(empty($_POST['client_secret'])){
        echo json_encode(['status'  => 'error','message' => 'Client Secret Tidak Boleh Kosong!']);
        exit;
    }

    // Buat Variabel
    $id_google_credential = validateAndSanitizeInput($_POST['id_google_credential']);
    $credential_env       = validateAndSanitizeInput($_POST['credential_env']);
    $client_id            = validateAndSanitizeInput($_POST['client_id']);
    $client_secret        = validateAndSanitizeInput($_POST['client_secret']);

    // Validasi Data Tujuan Harus Ada
    $QryExist = $Conn->prepare("SELECT id_google_credential FROM google_credential WHERE id_google_credential = ?");
    $QryExist->bind_param("i", $id_google_credential);
    if (!$QryExist->execute()) {
        $error = $Conn->error;
        echo json_encode(['status'  => 'error','message' => 'Terjadi kesalahan pada saat membuka data dari database!<br>Keterangan : '.$error.'']);
        exit;
    }
    $ResultExist = $QryExist->get_result();
    $DataExist   = $ResultExist->fetch_assoc();
    $QryExist->close();

    if(empty($DataExist['id_google_credential'])){
        echo json_encode(['status'  => 'error','message' => 'Data Google Credential tidak ditemukan']);
        exit;
    }

    // Validasi Duplikat Data (kecuali data yang sedang diedit)
    $QryDuplicate = $Conn->prepare("SELECT id_google_credential FROM google_credential WHERE credential_env = ? AND client_id = ? AND client_secret = ? AND id_google_credential != ?");
    $QryDuplicate->bind_param("sssi", $credential_env, $client_id, $client_secret, $id_google_credential);
    if (!$QryDuplicate->execute()) {
        $error = $Conn->error;
        echo json_encode(['status'  => 'error','message' => 'Terjadi kesalahan pada saat membuka data dari database!<br>Keterangan : '.$error.'']);
        exit;
    }
    $ResultDuplicate = $QryDuplicate->get_result();
    $DataDuplicate   = $ResultDuplicate->fetch_assoc();
    $QryDuplicate->close();

    if(!empty($DataDuplicate['id_google_credential'])){
        echo json_encode(['status'  => 'error','message' => 'Data Yang Anda Masukan Sudah Terdaftar']);
        exit;
    }

    // Update Data Ke Database
    $query = $Conn->prepare("UPDATE google_credential SET credential_env = ?, client_id = ?, client_secret = ? WHERE id_google_credential = ?");
    $query->bind_param(
        "sssi",
        $credential_env,
        $client_id,
        $client_secret,
        $id_google_credential
    );

    if ($query->execute()) {
        echo json_encode([
            'status'  => 'success',
            'message' => 'Google Credential berhasil diperbarui'
        ]);
    } else {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Gagal memperbarui data'
        ]);
    }

    $query->close();
    $Conn->close();
?>
