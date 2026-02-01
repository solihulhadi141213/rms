<?php
    header('Content-Type: application/json');

    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    date_default_timezone_set("Asia/Jakarta");

    // ============================
    // VALIDASI SESSION
    // ============================
    if(empty($SessionIdAccess)){
        echo json_encode([
            "status" => "error",
            "message" => "Session habis, silakan login ulang"
        ]);
        exit;
    }

    // ============================
    // VALIDASI ID
    // ============================
    $id_master_signature = validateAndSanitizeInput($_POST['id_master_signature'] ?? '');

    if(empty($id_master_signature)){
        echo json_encode([
            "status" => "error",
            "message" => "ID Signature tidak valid"
        ]);
        exit;
    }

    // ============================
    // TANGKAP INPUT
    // ============================
    $nama     = validateAndSanitizeInput($_POST['nama'] ?? '');
    $kode     = validateAndSanitizeInput($_POST['kode'] ?? '');
    $ihs      = validateAndSanitizeInput($_POST['ihs'] ?? '');
    $kategori = validateAndSanitizeInput($_POST['kategori'] ?? '');

    // Validasi wajib
    if(empty($nama) || empty($kategori)){
        echo json_encode([
            "status" => "error",
            "message" => "Nama dan kategori wajib diisi"
        ]);
        exit;
    }

    // ============================
    // AMBIL DATA LAMA
    // ============================
    $old = $Conn->prepare("SELECT base_64_ttd FROM master_signature WHERE id_master_signature=?");
    $old->bind_param("i", $id_master_signature);
    $old->execute();
    $res = $old->get_result();
    $row = $res->fetch_assoc();
    $old_base64 = $row['base_64_ttd'] ?? null;
    $old->close();

    // ============================
    // CEK FILE BARU
    // ============================
    $base64_ttd = $old_base64; // default pakai lama

    if(isset($_FILES['file_tanda_tangan']) && $_FILES['file_tanda_tangan']['error'] == 0){

        $file = $_FILES['file_tanda_tangan'];

        $allowed_ext = ['png','jpg','jpeg','gif'];
        $max_size = 1024 * 1024; // 1MB

        $file_name = $file['name'];
        $file_size = $file['size'];
        $file_tmp  = $file['tmp_name'];

        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if(!in_array($ext, $allowed_ext)){
            echo json_encode([
                "status" => "error",
                "message" => "Format file tidak valid (PNG/JPG/GIF)"
            ]);
            exit;
        }

        if($file_size > $max_size){
            echo json_encode([
                "status" => "error",
                "message" => "Ukuran file maksimal 1MB"
            ]);
            exit;
        }

        // Convert ke Base64
        $file_content = file_get_contents($file_tmp);
        $mime_type = mime_content_type($file_tmp);
        $base64_ttd = "data:$mime_type;base64," . base64_encode($file_content);
    }

    // ============================
    // UPDATE DATABASE
    // ============================
    $stmt = $Conn->prepare("
        UPDATE master_signature 
        SET kode = ?, ihs = ?, nama = ?, kategori = ?, base_64_ttd = ?
        WHERE id_master_signature = ?
    ");

    if(!$stmt){
        echo json_encode([
            "status" => "error",
            "message" => "Prepare gagal: ".$Conn->error
        ]);
        exit;
    }

    $stmt->bind_param(
        "sssssi",
        $kode,
        $ihs,
        $nama,
        $kategori,
        $base64_ttd,
        $id_master_signature
    );

    if($stmt->execute()){
        echo json_encode([
            "status" => "success",
            "message" => "Data tanda tangan berhasil diperbarui"
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Gagal update: ".$stmt->error
        ]);
    }

    $stmt->close();
    exit;
?>