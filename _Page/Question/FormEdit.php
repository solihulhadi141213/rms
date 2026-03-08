<?php
    //koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";
    
    //Zona Waktu
    date_default_timezone_set("Asia/Jakarta");

    //Session Akses
    if(empty($SessionIdAccess)){
        echo '
            <div class="row mb-3">
                <div class="col-12 text-center">
                    <div class="alert alert-danger"><small>Sesi Akses Sudah Berakhir! Silahkan Login Ulang.</small></div>
                </div>
            </div>
        ';
        exit;
    }

    //id_question wajib terisi
    if(empty($_POST['id_question'])){
        echo '
            <div class="row mb-3">
                <div class="col-12 text-center">
                    <div class="alert alert-danger"><small>ID Pertanyaan Tiidak Boleh Kosong!</small></div>
                </div>
            </div>
        ';
        exit;
    }

    //Buat variabel 'id_question' dan sanitasi
    $id_question = validateAndSanitizeInput($_POST['id_question']);

    //Buka Detail Koneksi Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM question WHERE id_question = ?");
    $Qry->bind_param("i", $id_question);
    if (!$Qry->execute()) {
        $error=$Conn->error;
        echo '
            <div class="alert alert-danger">
                <small>Terjadi kesalahan pada saat membuka data dari database!<br>Keterangan : '.$error.'</small>
            </div>
        ';
    }else{
        $Result = $Qry->get_result();
        $Data = $Result->fetch_assoc();
        $Qry->close();

        //Buat Variabel
        $id_question      = $Data['id_question'];
        $id_questionnaire = $Data['id_questionnaire'];
        $question_group   = $Data['question_group'];
        $question_text    = $Data['question_text'];
        $question_type    = $Data['question_type'];

        //Tampilkan Data
        echo '
            <input type="hidden" name="id_question" value="'.$id_question.'">
            <div class="row mb-3">
            <div class="col-md-12">
                <label for="question_group_edit">
                    <small>Group Pertanyaan</small>
                </label>
                <input type="text" class="form-control" name="question_group" id="question_group_edit" value="'.$question_group.'" list="list_question_group_edit" required>
                <datalist id="list_question_group_edit"></datalist>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-12">
                <label for="question_text_edit"><small>Pertanyaan</small></label>
                <input type="text" class="form-control" name="question_text" id="question_text_edit" value="'.$question_text.'" required>
                <small class="text text-grayish">Kalimat pertanyaan dalam bentuk text</small>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-12">
                <label for="id_questionnaire_edit"><small>ID Satu Sehat</small></label>
                <input type="text" class="form-control" name="id_questionnaire" id="id_questionnaire_edit" value="'.$id_questionnaire.'">
                <small class="text text-grayish">Jika pertanyaan sudah pernah di kirim ke satu sehat</small>
            </div>
        </div>
        ';
    }
?>