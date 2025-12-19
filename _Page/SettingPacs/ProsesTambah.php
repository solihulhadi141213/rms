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
    $name_connection_pacs = isset($_POST['name_connection_pacs']) 
        ? trim(htmlspecialchars($_POST['name_connection_pacs'])) 
        : '';

    $url_connection_pacs = isset($_POST['url_connection_pacs']) 
        ? trim(htmlspecialchars($_POST['url_connection_pacs'])) 
        : '';

    $username_connection_pacs = isset($_POST['username_connection_pacs']) 
        ? trim(htmlspecialchars($_POST['username_connection_pacs'])) 
        : '';

    $password_connection_pacs             = isset($_POST['password_connection_pacs']) 
        ? trim(htmlspecialchars($_POST['password_connection_pacs'])) 
        : '';

    $status_connection_pacs = isset($_POST['status_connection_pacs']) 
        ? (int) $_POST['status_connection_pacs'] 
        : 0;

    // ==========================
    // VALIDASI INPUT
    // ==========================

    // 1. Nama koneksi
    if ($name_connection_pacs == '') {
        echo json_encode([
            'status' => 'error',
            'message' => 'Nama koneksi tidak boleh kosong!'
        ]);
        exit;
    }

    if (strlen($name_connection_pacs) > 200) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Nama koneksi maksimal 200 karakter!'
        ]);
        exit;
    }

    // 2. URL PACS
    if ($url_connection_pacs == '') {
        echo json_encode([
            'status' => 'error',
            'message' => 'URL PACS tidak boleh kosong!'
        ]);
        exit;
    }

    if (!filter_var($url_connection_pacs, FILTER_VALIDATE_URL)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Format URL PACS tidak valid!'
        ]);
        exit;
    }

    // 3. Client ID & Client Key
    if ($username_connection_pacs == '' || $password_connection_pacs == '') {
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
        if ($status_connection_pacs == 1) {
            $sql_deactivate = "UPDATE connection_pacs SET status_connection_pacs = 0";
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
            INSERT INTO connection_pacs 
            (
                name_connection_pacs ,
                url_connection_pacs ,
                username_connection_pacs,
                password_connection_pacs,
                status_connection_pacs 
            ) 
            VALUES (?, ?, ?, ?, ?)
        ";
        
        $stmt_insert = $Conn->prepare($sql_insert);
        
        if (!$stmt_insert) {
            throw new Exception('Gagal menyiapkan query untuk menyimpan data!');
        }
        
        $stmt_insert->bind_param(
            "ssssi",
            $name_connection_pacs ,
            $url_connection_pacs ,
            $username_connection_pacs,
            $password_connection_pacs,
            $status_connection_pacs 
        );
        
        if (!$stmt_insert->execute()) {
            throw new Exception('Gagal menyimpan data ke database!');
        }
        
        // Commit transaksi jika semua berhasil
        $Conn->commit();
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Koneksi PACS berhasil disimpan.'
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