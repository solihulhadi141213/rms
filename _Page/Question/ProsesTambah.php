<?php
    // ProsesTambah.php
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    // Validasi Sesi
    if(empty($SessionIdAccess)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Sesi Akses Sudah Berakhir. Silahkan Login Ulang!'
        ]);
        exit;
    }

    // Validasi input
    if(empty($_POST['question_group'])){
        echo json_encode([
            'status' => 'error',
            'message' => 'Nama Group Pertanyaan tidak boleh kosong!'
        ]);
        exit;
    }

    if(empty($_POST['question_text'])){
        echo json_encode([
            'status' => 'error',
            'message' => 'Text Pertanyaan tidak boleh kosong!'
        ]);
        exit;
    }

    if(empty($_POST['question_type'])){
        echo json_encode([
            'status' => 'error',
            'message' => 'Tipe Pertanyaan tidak boleh kosong!'
        ]);
        exit;
    }

    // Ambil data dari form
    $question_group = validateAndSanitizeInput($_POST['question_group']);
    $question_text = validateAndSanitizeInput($_POST['question_text']);
    $question_type = validateAndSanitizeInput($_POST['question_type']);

    // Variabel Tidak Wajib
    if(empty($_POST['id_questionnaire'])){
        $id_questionnaire = "";
        $satu_sehat       = 0;
    }else{
        $id_questionnaire = validateAndSanitizeInput($_POST['id_questionnaire']);
        $satu_sehat       = 1;
    }

    // Insert Ke Database
    $query = "INSERT INTO question (
        id_questionnaire,
        question_group,
        question_text,
        question_type,
        satu_sehat
    ) VALUES (?, ?, ?, ?, ?)";

    $stmt = $Conn->prepare($query);

    // Bind parameters
    $stmt->bind_param(
        "sssss",
        $id_questionnaire,
        $question_group,
        $question_text,
        $question_type,
        $satu_sehat
    );

    if($stmt->execute()){
        echo json_encode([
            'status' => 'success',
            'message' => 'Pertanyaan berhasil ditambahkan!'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal menyimpan data: ' . $stmt->error
        ]);
    }

    $stmt->close();
?>