<?php
    // ================= KONEKSI =================
    include "../../_Config/Connection.php";

    // Set header JSON
    header('Content-Type: application/json');

    // Zona Waktu
    date_default_timezone_set("Asia/Jakarta");

    // ================= DEFAULT RESPONSE =================
    $response = [
        "service_request"   => "0",
        "procedure"         => "0",
        "imaging_study"     => "0",
        "observation"       => "0",
        "diagnostic_report" => "0",
        "expertise"         => "0",
        "expertise_usg"     => "0",
        "dicom_file"        => "0"
    ];

    // ================= FORMAT ANGKA =================
    function formatNumber($number) {
        if ($number == 0) return "0";
        if ($number >= 1000000000) return round($number / 1000000000, 1) . 'B';
        if ($number >= 1000000)    return round($number / 1000000, 1) . 'M';
        if ($number >= 1000)       return round($number / 1000, 1) . 'K';
        return (string)$number;
    }

    // ================= HITUNG DATA =================

    // 1. service_request
    $q = mysqli_fetch_assoc(mysqli_query(
        $Conn,
        "SELECT COUNT(id_radiologi) AS total
        FROM radiologi
        WHERE id_service_request IS NOT NULL
        AND id_service_request != ''"
    ));
    $response['service_request'] = formatNumber($q['total']);

    // 2. procedure
    $q = mysqli_fetch_assoc(mysqli_query(
        $Conn,
        "SELECT COUNT(id_radiologi) AS total
        FROM radiologi
        WHERE id_procedure IS NOT NULL
        AND id_procedure != ''"
    ));
    $response['procedure'] = formatNumber($q['total']);

    // 3. imaging_study
    $q = mysqli_fetch_assoc(mysqli_query(
        $Conn,
        "SELECT COUNT(id_radiologi) AS total
        FROM radiologi
        WHERE id_imaging_study IS NOT NULL
        AND id_imaging_study != ''"
    ));
    $response['imaging_study'] = formatNumber($q['total']);

    // 4. observation
    $q = mysqli_fetch_assoc(mysqli_query(
        $Conn,
        "SELECT COUNT(id_radiologi) AS total
        FROM radiologi
        WHERE id_observation IS NOT NULL
        AND id_observation != ''"
    ));
    $response['observation'] = formatNumber($q['total']);

    // 5. diagnostic_report
    $q = mysqli_fetch_assoc(mysqli_query(
        $Conn,
        "SELECT COUNT(id_radiologi) AS total
        FROM radiologi
        WHERE id_diagnostic_report IS NOT NULL
        AND id_diagnostic_report != ''"
    ));
    $response['diagnostic_report'] = formatNumber($q['total']);

    // 6. expertise
    $q = mysqli_fetch_assoc(mysqli_query(
        $Conn,
        "SELECT COUNT(*) AS total FROM radiologi_expertise"
    ));
    $response['expertise'] = formatNumber($q['total']);

    // 7. expertise_usg
    $q = mysqli_fetch_assoc(mysqli_query(
        $Conn,
        "SELECT COUNT(*) AS total FROM radiologi_expertise_usg"
    ));
    $response['expertise_usg'] = formatNumber($q['total']);

    // 8. dicom_file
    $q = mysqli_fetch_assoc(mysqli_query(
        $Conn,
        "SELECT COUNT(*) AS total FROM radiologi_dicom"
    ));
    $response['dicom_file'] = formatNumber($q['total']);

    // ================= OUTPUT =================
    echo json_encode($response);
    exit;
?>