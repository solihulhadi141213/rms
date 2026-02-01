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

    /* Validasi metode */
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $response['message'] = 'Metode tidak diizinkan';
        echo json_encode($response);
        exit;
    }

    // Validasi Akses
    if (empty($SessionIdAccess)) {
        $response['message'] = 'Sesi login telah berakhir';
        echo json_encode($response);
        exit;
    }

    // Validasi Input Data
    $nama     = validateAndSanitizeInput($_POST['nama'] ?? '');
    $kode     = validateAndSanitizeInput($_POST['kode'] ?? '');
    $ihs      = validateAndSanitizeInput($_POST['ihs'] ?? '');
    $kategori = validateAndSanitizeInput($_POST['kategori'] ?? '');

    // Validasi wajib
    if (empty($nama) || empty($kategori)) {
        echo json_encode([
            "status" => "error",
            "message" => "Nama dan Kategori wajib diisi"
        ]);
        exit;
    }

    // Validasi File
    if (!isset($_FILES['file_tanda_tangan']) || $_FILES['file_tanda_tangan']['error'] != 0) {
        echo json_encode([
            "status" => "error",
            "message" => "File tanda tangan wajib diunggah"
        ]);
        exit;
    }

    $file = $_FILES['file_tanda_tangan'];

    $allowed_ext = ['png', 'jpg', 'jpeg', 'gif'];
    $max_size    = 1024 * 1024; // 1 MB

    $file_name = $file['name'];
    $file_size = $file['size'];
    $file_tmp  = $file['tmp_name'];

    $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed_ext)) {
        echo json_encode([
            "status" => "error",
            "message" => "Format file tidak valid. Gunakan PNG, JPG, atau GIF"
        ]);
        exit;
    }

    if ($file_size > $max_size) {
        echo json_encode([
            "status" => "error",
            "message" => "Ukuran file terlalu besar. Maksimal 1MB"
        ]);
        exit;
    }

    // Konversi File Ke Base 64
    $file_content = file_get_contents($file_tmp);
    $base64_ttd   = base64_encode($file_content);

    // Optional prefix agar bisa dipakai langsung di <img>
    $mime_type = mime_content_type($file_tmp);
    $base64_ttd = "data:$mime_type;base64," . $base64_ttd;

    // Insert Ke datbase
    $stmt = $Conn->prepare("
        INSERT INTO master_signature
        (kode, ihs, nama, kategori, base_64_ttd)
        VALUES (?, ?, ?, ?, ?)
    ");

    if (!$stmt) {
        echo json_encode([
            "status" => "error",
            "message" => "Prepare gagal: " . $Conn->error
        ]);
        exit;
    }

    $stmt->bind_param(
        "sssss",
        $kode,
        $ihs,
        $nama,
        $kategori,
        $base64_ttd
    );

    if ($stmt->execute()) {
        echo json_encode([
            "status" => "success",
            "message" => "Tanda tangan berhasil disimpan"
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Gagal simpan data: " . $stmt->error
        ]);
    }

    $stmt->close();
    exit;
?>