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
        $id_questionnaire = $Data['id_questionnaire'] ?? "-";
        $question_group   = $Data['question_group'];
        $question_text    = $Data['question_text'];
        $question_type    = $Data['question_type'];

        $item_linkId=GenerateToken(32);

        //Tampilkan Data
        echo '
            <input type="hidden" class="form-control" name="id_question" value="'.$id_question.'">
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="resourceType">
                        <small><i>Resource Type</i></small>
                    </label>
                    <input type="text" class="form-control" name="resourceType" id="resourceType" value="Questionnaire" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="status">
                        <small>Status</small>
                    </label>
                    <input type="text" class="form-control" name="status" id="status" value="active" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="subjectType">
                        <small><i>Subject Type</i></small>
                    </label>
                    <input type="text" class="form-control" name="subjectType" id="subjectType" value="active" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="title">
                        <small><i>Title</i></small>
                    </label>
                    <input type="text" class="form-control" name="title" id="title" value="'.$question_group.'" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="item_linkId">
                        <small>Link ID</small>
                    </label>
                    <input type="text" class="form-control" name="item_linkId" id="item_linkId" value="'.$item_linkId.'" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="item_text"><small>Pertanyaan</small></label>
                    <input type="text" class="form-control" name="item_text" id="item_text" value="'.$question_text.'" required>
                </div>
            </div>
             <div class="row mb-3">
                <div class="col-md-12">
                    <label for="item_type"><small>Tipe Pertanyaan</small></label>
                    <input type="text" class="form-control" name="item_type" id="item_type" value="'.$question_type.'" required>
                </div>
            </div>
        ';
    }
?>