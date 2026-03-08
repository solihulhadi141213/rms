<?php
    // ProsesEdit.php
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
    if(empty($_POST['id_question'])){
        echo json_encode([
            'status' => 'error',
            'message' => 'ID Pertanyaan tidak boleh kosong!'
        ]);
        exit;
    }

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

    // Ambil data dari form
    $id_question    = validateAndSanitizeInput($_POST['id_question']);
    $question_group = validateAndSanitizeInput($_POST['question_group']);
    $question_text  = validateAndSanitizeInput($_POST['question_text']);

    // Variabel Tidak Wajib
    if(empty($_POST['id_questionnaire'])){
        $id_questionnaire = "";
        $satu_sehat       = 0;
    }else{
        $id_questionnaire = validateAndSanitizeInput($_POST['id_questionnaire']);
        $satu_sehat       = 1;
    }

    // Update ke database
    $query = "UPDATE question SET
        id_questionnaire = ?,
        question_group = ?,
        question_text = ?,
        satu_sehat = ?
    WHERE id_question = ?";

    $stmt = $Conn->prepare($query);

    // Bind parameters
    $stmt->bind_param(
        "ssssi",
        $id_questionnaire,
        $question_group,
        $question_text,
        $satu_sehat,
        $id_question
    );

    if($stmt->execute()){
        echo json_encode([
            'status' => 'success',
            'message' => 'Pertanyaan berhasil diperbarui!'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal memperbarui data: ' . $stmt->error
        ]);
    }

    $stmt->close();
?>
