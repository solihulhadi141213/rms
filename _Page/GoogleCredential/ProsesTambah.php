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
    $credential_env = validateAndSanitizeInput($_POST['credential_env']);
    $client_id      = validateAndSanitizeInput($_POST['client_id']);
    $client_secret  = validateAndSanitizeInput($_POST['client_secret']);

    // Validasi Duplikat Data
    $Qry = $Conn->prepare("SELECT * FROM google_credential WHERE credential_env = ? AND client_id = ? AND client_secret = ?");
    $Qry->bind_param("sss", $credential_env, $credential_env, $credential_env);
    if (!$Qry->execute()) {
        $error=$Conn->error;
        echo json_encode(['status'  => 'error','message' => 'Terjadi kesalahan pada saat membuka data dari database!<br>Keterangan : '.$error.'']);
        exit;
    }
    $Result = $Qry->get_result();
    $Data = $Result->fetch_assoc();
    $Qry->close();
    if(!empty($Data['id_google_credential'])){
        echo json_encode(['status'  => 'error','message' => 'Data Yang Anda Masukan Sudah Terdaftar']);
        exit;
    }
    $status = 0;

    // Simpan Data Ke Database
    $query = $Conn->prepare("
        INSERT INTO google_credential (
            credential_env,
            client_id,
            client_secret,
            status
        ) VALUES (?,?,?,?)
    ");

    $query->bind_param(
        "sssi",
        $credential_env,
        $client_id,
        $client_secret,
        $status
    );

    // ======================================================
    // EKSEKUSI
    // ======================================================
    if ($query->execute()) {
        echo json_encode([
            'status'  => 'success',
            'message' => 'Google Credential berhasil disimpan'
        ]);
    } else {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Gagal menyimpan data'
        ]);
    }

    $query->close();
    $Conn->close();
?>