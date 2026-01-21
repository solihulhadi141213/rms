<?php
    // Koneksi dan Function
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    // Time Zone
    date_default_timezone_set('Asia/Jakarta');
    $now = date('Y-m-d H:i:s');

    // Validasi Session Akses
    if (empty($SessionIdAccess)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Sesi Akses Sudah Berakhir, Silahkan Login Ulang!'
        ]);
        exit;
    }

    // ==========================
    // AMBIL & SANITASI INPUT
    // ==========================
    $name_connection_orthanc  = isset($_POST['name_connection_orthanc']) 
        ? trim(htmlspecialchars($_POST['name_connection_orthanc'])) 
        : '';

    $url_connection_orthanc = isset($_POST['url_connection_orthanc']) 
        ? trim(htmlspecialchars($_POST['url_connection_orthanc'])) 
        : '';

    $username_connection_orthanc = isset($_POST['username_connection_orthanc']) 
        ? trim(htmlspecialchars($_POST['username_connection_orthanc'])) 
        : '';
    
    $password_connection_orthanc = isset($_POST['password_connection_orthanc']) 
        ? trim(htmlspecialchars($_POST['password_connection_orthanc'])) 
        : '';

    $status_connection_orthanc = isset($_POST['status_connection_orthanc']) 
        ? (int) $_POST['status_connection_orthanc'] 
        : 0;

    // ==========================
    // VALIDASI INPUT
    // ==========================

    // 1. Nama koneksi
    if ($name_connection_orthanc == '') {
        echo json_encode([
            'status' => 'error',
            'message' => 'Nama koneksi tidak boleh kosong!'
        ]);
        exit;
    }

    if (strlen($name_connection_orthanc) > 200) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Nama koneksi maksimal 200 karakter!'
        ]);
        exit;
    }

    // 2. URL 
    if ($url_connection_orthanc == '') {
        echo json_encode([
            'status' => 'error',
            'message' => 'URL tidak boleh kosong!'
        ]);
        exit;
    }

    if (!filter_var($url_connection_orthanc, FILTER_VALIDATE_URL)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Format URL tidak valid!'
        ]);
        exit;
    }

    // 3. Username dan Password
    if ($username_connection_orthanc == '' || $password_connection_orthanc == '') {
        echo json_encode([
            'status' => 'error',
            'message' => 'Username dan Password tidak boleh kosong!'
        ]);
        exit;
    }

    // Mulai transaksi untuk memastikan konsistensi data
    $Conn->begin_transaction();

    try {
        // ==========================
        // NONAKTIFKAN SEMUA KONEKSI LAIN JIKA STATUS = 1
        // ==========================
        if ($status_connection_orthanc == 1) {
            $sql_deactivate = "UPDATE connection_orthanc SET status_connection_orthanc = 0";
            $stmt_deactivate = $Conn->prepare($sql_deactivate);
            
            if (!$stmt_deactivate) {
                throw new Exception('Gagal menyiapkan query untuk menonaktifkan koneksi lain!');
            }
            
            if (!$stmt_deactivate->execute()) {
                throw new Exception('Gagal menonaktifkan koneksi lain!');
            }
            
            $stmt_deactivate->close();
        }
        
        // ==========================
        // SIMPAN DATA BARU KE DATABASE
        // ==========================
        $sql_insert = "
            INSERT INTO connection_orthanc 
            (
                name_connection_orthanc,
                url_connection_orthanc,
                username_connection_orthanc,
                password_connection_orthanc,
                status_connection_orthanc
            ) 
            VALUES (?, ?, ?, ?, ?)
        ";
        
        $stmt_insert = $Conn->prepare($sql_insert);
        
        if (!$stmt_insert) {
            throw new Exception('Gagal menyiapkan query untuk menyimpan data!');
        }
        
        $stmt_insert->bind_param(
            "ssssi",
            $name_connection_orthanc,
            $url_connection_orthanc,
            $username_connection_orthanc,
            $password_connection_orthanc,
            $status_connection_orthanc
        );
        
        if (!$stmt_insert->execute()) {
            throw new Exception('Gagal menyimpan data ke database!');
        }
        
        // Commit transaksi jika semua berhasil
        $Conn->commit();
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Koneksi berhasil disimpan.'
        ]);
        
        $stmt_insert->close();
        
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi error
        $Conn->rollback();
        
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
        exit;
    }
?>