<?php
    // ======================================================
    // KONEKSI DATABASE
    // ======================================================
    include "../../_Config/Connection.php";
    date_default_timezone_set("Asia/Jakarta");

    // ======================================================
    // VALIDASI INPUT
    // ======================================================
    if (!isset($_POST['id_master_signature'])) {
        echo json_encode([
            "status" => "error",
            "message" => "ID tidak ditemukan"
        ]);
        exit;
    }

    $id_master_signature = $_POST['id_master_signature'];
    $delete_at = date("Y-m-d H:i:s");

    // ======================================================
    // PROSES SOFT DELETE
    // ======================================================
    $stmt = $Conn->prepare("
        UPDATE master_signature 
        SET delete_at = ? 
        WHERE id_master_signature = ?
    ");

    $stmt->bind_param("si", $delete_at, $id_master_signature);

    if ($stmt->execute()) {
        echo json_encode([
            "status" => "success",
            "message" => "Data berhasil dihapus (Soft Delete)"
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Gagal menghapus data",
            "error" => $stmt->error
        ]);
    }

    $stmt->close();

?>