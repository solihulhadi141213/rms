<?php
    // koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    // Zona Waktu
    date_default_timezone_set("Asia/Jakarta");

    $konten_preview = "";

    // Session
    if (empty($SessionIdAccess)) {
        echo '<div class="alert alert-danger text-center">Sesi Akses Sudah Berakhir! Silahkan Login Ulang.</div>';
        exit;
    }

    // Validasi id_radiologi
    if (empty($_POST['id_radiologi'])) {
        echo '<div class="alert alert-danger text-center">ID Pemeriksaan Tidak Boleh Kosong!</div>';
        exit;
    }

    $id_radiologi = validateAndSanitizeInput($_POST['id_radiologi']);

    // Query
    $Qry = $Conn->prepare("SELECT id_radiologi, id_service_request, id_procedure, id_imaging_study, id_observation, id_diagnostic_report FROM radiologi WHERE id_radiologi = ?");
    $Qry->bind_param("i", $id_radiologi);
    $Qry->execute();
    $Result = $Qry->get_result();
    $Data = $Result->fetch_assoc();
    $Qry->close();

    if (empty($Data['id_radiologi'])) {
        echo '<div class="alert alert-danger text-center">ID Radiologi Tidak Valid!</div>';
        exit;
    }
    $id_service_request   = $Data['id_service_request'];
    $id_procedure         = $Data['id_procedure'];
    $id_imaging_study     = $Data['id_imaging_study'];
    $id_observation       = $Data['id_observation'];
    $id_diagnostic_report = $Data['id_diagnostic_report'];
    // Tampilkan Form
    echo '
        <input type="hidden" name="id_radiologi" value="'.$id_radiologi.'">

        <div class="row mb-3">
            <label for="id_service_request_edit">
                <small><i>ID Service Request</i></small>
                <input type="text" name="id_service_request" id="id_service_request_edit" class="form-control" value="'.$id_service_request.'">
            </label>
        </div>
        <div class="row mb-3">
            <label for="id_procedure_edit">
                <small><i>ID Procedure</i></small>
                <input type="text" name="id_procedure" id="id_procedure_edit" class="form-control" value="'.$id_procedure.'">
            </label>
        </div>
        <div class="row mb-3">
            <label for="id_imaging_study_edit">
                <small><i>ID Imaging Study</i></small>
                <input type="text" name="id_imaging_study" id="id_imaging_study_edit" class="form-control" value="'.$id_imaging_study.'">
            </label>
        </div>
        <div class="row mb-3">
            <label for="id_observation_edit">
                <small><i>ID Observation</i></small>
                <input type="text" name="id_observation" id="id_observation_edit" class="form-control" value="'.$id_observation.'">
            </label>
        </div>
        <div class="row mb-3">
            <label for="id_diagnostic_report_edit">
                <small><i>ID Diagnostic Report</i></small>
                <input type="text" name="id_diagnostic_report" id="id_diagnostic_report_edit" class="form-control" value="'.$id_diagnostic_report.'">
            </label>
        </div>
    ';
?>