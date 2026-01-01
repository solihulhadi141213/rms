<?php
    // koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    date_default_timezone_set("Asia/Jakarta");

    // Response default
    $response = [
        'status'  => 'error',
        'message' => 'Terjadi kesalahan sistem'
    ];

    // =========================================================
    // VALIDASI SESSION
    // =========================================================
    if (empty($SessionIdAccess)) {
        $response['message'] = 'Sesi akses telah berakhir. Silakan login ulang.';
        echo json_encode($response);
        exit;
    }

    // =========================================================
    // VALIDASI ID RADIOLOGI
    // =========================================================
    $id_radiologi = (int) ($_POST['id_radiologi'] ?? 0);
    if ($id_radiologi <= 0) {
        $response['message'] = 'ID Radiologi tidak valid.';
        echo json_encode($response);
        exit;
    }

    // =========================================================
    // VALIDASI FILE
    // =========================================================
    if (empty($_FILES['upload_file']['name'])) {
        $response['message'] = 'Tidak ada file yang diupload.';
        echo json_encode($response);
        exit;
    }

    // Ambil data file
    $original_file_name = $_FILES['upload_file']['name'];
    $file_size          = $_FILES['upload_file']['size'];
    $file_type          = $_FILES['upload_file']['type'];
    $file_tmp           = $_FILES['upload_file']['tmp_name'];

    // Ambil extension
    $ext = strtolower(pathinfo($original_file_name, PATHINFO_EXTENSION));

    // Validasi ekstensi
    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($ext, $allowed_ext)) {
        $response['message'] = 'Tipe file tidak diizinkan.';
        echo json_encode($response);
        exit;
    }

    // Validasi size (2 MB)
    if ($file_size > 2000000) {
        $response['message'] = 'Ukuran file terlalu besar (maksimal 2 MB).';
        echo json_encode($response);
        exit;
    }

    // =========================================================
    // VALIDASI DATA RADIOLOGI
    // =========================================================
    $Qry = $Conn->prepare("
        SELECT id_radiologi, id_kunjungan, id_pasien
        FROM radiologi
        WHERE id_radiologi = ?
    ");
    $Qry->bind_param("i", $id_radiologi);
    $Qry->execute();
    $Result = $Qry->get_result();
    $Data   = $Result->fetch_assoc();
    $Qry->close();

    if (empty($Data['id_radiologi'])) {
        $response['message'] = 'Data radiologi tidak ditemukan.';
        echo json_encode($response);
        exit;
    }

    $id_kunjungan = $Data['id_kunjungan'];
    $id_pasien    = $Data['id_pasien'];

    // =========================================================
    // DATA TAMBAHAN
    // =========================================================
    $file_description = validateAndSanitizeInput($_POST['file_description'] ?? '');
    $random_name      = generateRandomString(6);

    // Nama file final
    $file_name = "{$id_pasien}-{$id_kunjungan}-{$random_name}.{$ext}";

    // Folder bulanan
    $folder_name = date('Y-m');
    $base_path   = "../../_Storage/";
    $folder_path = $base_path . $folder_name . "/";

    // Buat folder jika belum ada
    if (!is_dir($folder_path)) {
        mkdir($folder_path, 0777, true);
    }

    // Path final
    $path = $folder_path . $file_name;

    // =========================================================
    // UPLOAD FILE
    // =========================================================
    if (!move_uploaded_file($file_tmp, $path)) {
        $response['message'] = 'Gagal menyimpan file.';
        echo json_encode($response);
        exit;
    }

    // =========================================================
    // SIMPAN KE DATABASE
    // =========================================================
    $id_radiologi_file = generateUUIDv4();
    $file_datetime     = date('Y-m-d H:i:s');

    $Insert = $Conn->prepare("
        INSERT INTO radiologi_file (
            id_radiologi_file,
            id_radiologi,
            id_access,
            folder_name,
            file_datetime,
            file_description,
            file_type,
            file_size,
            file_name
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $Insert->bind_param(
        "siissssds",
        $id_radiologi_file,
        $id_radiologi,
        $SessionIdAccess,
        $folder_name,
        $file_datetime,
        $file_description,
        $ext,
        $file_size,
        $file_name
    );

    if (!$Insert->execute()) {
        $response['message'] = 'Gagal menyimpan data ke database.';
        echo json_encode($response);
        exit;
    }

    $Insert->close();

    // =========================================================
    // RESPONSE SUKSES
    // =========================================================
    $response = [
        'status'  => 'success',
        'message' => 'File radiologi berhasil diupload.'
    ];

    echo json_encode($response);
    exit;
