<?php
    // ======================================================
    // KONEKSI & SESSION
    // ======================================================
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    // Zona Waktu
    date_default_timezone_set("Asia/Jakarta");

    // Response default
    $response = [
        'status'  => 'error',
        'message' => 'Terjadi kesalahan sistem'
    ];

    // ======================================================
    // VALIDASI SESSION
    // ======================================================
    if (empty($SessionIdAccess)) {
        $response['message'] = 'Sesi akses telah berakhir. Silakan login ulang.';
        echo json_encode($response);
        exit;
    }

    // ======================================================
    // VALIDASI INPUT
    // ======================================================
    if (empty($_POST['id_radiologi']) || empty($_POST['resource']) || empty($_POST['id'])) {
        $response['message'] = 'Parameter tidak lengkap.';
        echo json_encode($response);
        exit;
    }

    // Sanitasi
    $id_radiologi = validateAndSanitizeInput($_POST['id_radiologi']);
    $resource     = validateAndSanitizeInput($_POST['resource']);
    $id_resource  = validateAndSanitizeInput($_POST['id']);

    // ======================================================
    // GENERATE TOKEN SATU SEHAT
    // ======================================================
    $tokenResult = generateTokenSatuSehat($Conn);
    if ($tokenResult['status'] !== 'success') {
        echo json_encode([
            'status'  => 'error',
            'message' => $tokenResult['message']
        ]);
        exit;
    }

    $token = $tokenResult['token'];

    // ======================================================
    // AMBIL KONFIGURASI SATU SEHAT
    // ======================================================
    $stmt = $Conn->prepare("
        SELECT url_connection_satu_sehat 
        FROM connection_satu_sehat 
        WHERE status_connection_satu_sehat = 1
        LIMIT 1
    ");
    $stmt->execute();
    $config = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$config) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Koneksi SATUSEHAT tidak ditemukan.'
        ]);
        exit;
    }

    $url_api = rtrim($config['url_connection_satu_sehat'], '/');

    // ======================================================
    // MAPPING RESOURCE → STATUS FHIR → ENDPOINT
    // ======================================================
    $endpoint = '';
    $status_fhir = '';
    $nama_kolom_tabel = '';

    switch ($resource) {

        case 'service_request':
            $endpoint = 'ServiceRequest';
            $status_fhir = 'revoked';
            $nama_kolom_tabel = 'id_service_request';
            break;

        case 'procedure':
            $endpoint = 'Procedure';
            $status_fhir = 'stopped';
            $nama_kolom_tabel = 'id_procedure';
            break;

        case 'imaging_study':
            $endpoint = 'ImagingStudy';
            $status_fhir = 'cancelled';
            $nama_kolom_tabel = 'id_imaging_study';
            break;

        case 'observation':
            $endpoint = 'Observation';
            $status_fhir = 'cancelled';
            $nama_kolom_tabel = 'id_observation';
            break;

        case 'diagnostic_report':
            $endpoint = 'DiagnosticReport';
            $status_fhir = 'cancelled';
            $nama_kolom_tabel = 'id_diagnostic_report';
            break;

        default:
            echo json_encode([
                'status'  => 'error',
                'message' => 'Resource tidak dikenali.'
            ]);
            exit;
    }

    // ======================================================
    // URL & PAYLOAD PATCH
    // ======================================================
    $url = $url_api . '/fhir-r4/v1/' . $endpoint . '/' . $id_resource;

    $payload = [
        [
            "op"    => "replace",
            "path"  => "/status",
            "value" => $status_fhir
        ]
    ];

    $payload_json = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    // ======================================================
    // KIRIM PATCH KE SATU SEHAT
    // ======================================================
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => 'PATCH',
        CURLOPT_POSTFIELDS => $payload_json,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json-patch+json',
            'Accept: application/json'
        ],
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false
    ]);

    $response_api = curl_exec($curl);
    $http_code    = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $curl_error   = curl_error($curl);
    curl_close($curl);

    // ======================================================
    // HANDLE ERROR CURL
    // ======================================================
    if ($curl_error) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'cURL Error: ' . $curl_error
        ]);
        exit;
    }

    // ======================================================
    // VALIDASI RESPONSE SATU SEHAT
    // ======================================================
    if (!in_array($http_code, [200, 204])) {

        $msg = 'Gagal membatalkan resource SATU SEHAT';

        $decoded = json_decode($response_api, true);
        if (($decoded['resourceType'] ?? '') === 'OperationOutcome') {
            $msg = $decoded['issue'][0]['details']['text']
                ?? $decoded['issue'][0]['diagnostics']
                ?? $msg;
        }

        echo json_encode([
            'status'    => 'error',
            'message'   => $msg,
            'http_code' => $http_code
        ]);
        exit;
    }

    // ======================================================
    // UPDATE DATABASE LOKAL
    // ======================================================
    if (!empty($nama_kolom_tabel)) {
        $kosongkan = "";
        $upd = $Conn->prepare("
            UPDATE radiologi 
            SET $nama_kolom_tabel = ?
            WHERE id_radiologi = ?
        ");
        $upd->bind_param("si", $kosongkan, $id_radiologi);
        $upd->execute();
        $upd->close();
    }

    // ======================================================
    // RESPONSE SUKSES
    // ======================================================
    echo json_encode([
        'status'       => 'success',
        'message'      => 'Resource berhasil dibatalkan di SATU SEHAT',
        'resource'     => $endpoint,
        'status_fhir'  => $status_fhir,
        'resource_url' => $url,
        'id_radiologi' => $id_radiologi
    ]);
    exit;
?>