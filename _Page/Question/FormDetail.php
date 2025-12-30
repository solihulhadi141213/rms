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

        //Tampilkan Data
        echo '
            <div class="row mb-3">
                <div class="col-4"><small>ID Questionnaire</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$id_questionnaire.'</small>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-4"><small>Group Pertanyaan</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$question_group.'</small>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-4"><small>Text Pertanyaan</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$question_text.'</small>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-4"><small>Tipe</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$question_type.'</small>
                </div>
            </div>
        ';
    }
?>