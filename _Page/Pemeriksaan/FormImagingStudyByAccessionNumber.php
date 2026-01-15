<?php
    // Koneksi, Function dan Session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    // Validasi Session
    if (empty($SessionIdAccess)) {
        echo '
            <div class="alert alert-danger text-center">
                <small>Sesi telah berakhir! Silakan login ulang.</small>
            </div>
        ';
        exit;
    }

    // Validasi Input
    if(empty($_POST['accession_number'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>Accession Number Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }

    // Buat Variabel Dan Sanitasi
    $accession_number = validateAndSanitizeInput($_POST['accession_number']);

    // Buka Pengaturan PACS
    $status_connection_pacs = 1;
    $url_pacs = GetDetailData($Conn, ' connection_pacs', 'status_connection_pacs', $status_connection_pacs, 'url_pacs');

    // Buat Token Satu Sehat
    $tokenResult = generateTokenSatuSehat($Conn);
    if (empty($tokenResult) || $tokenResult['status'] !== 'success' || empty($tokenResult['token'])) {
        echo '
            <div class="alert alert-danger text-center">
                <small>Gagal mendapatkan token SATUSEHAT.</small>
            </div>
        ';
        exit;
    }

    $token = $tokenResult['token'];

    // Buka Pengaturan Satu Sehat
    $status_connection = 1;
    $stmt = $Conn->prepare("SELECT * FROM connection_satu_sehat WHERE status_connection_satu_sehat = ? LIMIT 1");
    $stmt->bind_param("i", $status_connection);
    $stmt->execute();
    $config = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (empty($config['url_connection_satu_sehat'])) {
        echo '
            <div class="alert alert-danger text-center">
                <small>Konfigurasi koneksi SATUSEHAT tidak ditemukan.</small>
            </div>
        ';
        exit;
    }
    $organization_id = $config['organization_id'];
    $base_url        = rtrim($config['url_connection_satu_sehat'], '/');
    $urlImagingStudy = "$base_url/fhir-r4/v1/ImagingStudy?identifier=http://sys-ids.kemkes.go.id/acsn/$organization_id|$accession_number";

    // Mulai Curl Imaging Study
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $urlImagingStudy,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json'
        ],
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false
    ]);

    $response   = curl_exec($curl);
    $http_code  = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($curl);
    curl_close($curl);

    if ($curl_error) {
        echo '<div class="alert alert-danger"><small>CURL Error: ' . htmlspecialchars($curl_error) . '</small></div>';
        exit;
    }

    if ($http_code !== 200) {
        echo '<div class="alert alert-danger"><small>Gagal mengambil data Imaging Study (HTTP ' . $http_code . ').</small></div>';exit;
    }

    $data = json_decode($response, true);

    if (!$data ||!is_array($data) ||isset($data['issue'])) {
        echo '<div class="alert alert-danger"><small>Data Imaging Study tidak valid atau tidak ditemukan.</small></div>';
        exit;
    }

    // Buat Variabelnya
    $data = json_decode($response, true);

    // Validasi JSON
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo '<div class="alert alert-danger"><small>Response JSON Tidak Valid.</small></div>';
        exit;
    }

    // Pastikan entry ada dan berbentuk array
    $entries      = $data['entry'] ?? [];
    $resourceType = $data['resourceType'] ?? "-";
    $total        = $data['total'] ?? "-";
    $type         = $data['type'] ?? "-";

    echo '
        <div class="row mb-2">
            <div class="col-12"><small><b><i>A. Title Information</i></b></small></div>
        </div>
        <div class="row">
            <div class="col-4"><small>Resource Type</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7"><small class="text text-grayish">'.$resourceType.'</small></div>
        </div>
        <div class="row">
            <div class="col-4"><small>Total Data</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7"><small class="text text-grayish">'.$total.' Row</small></div>
        </div>
        <div class="row">
            <div class="col-4"><small>Type</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7"><small class="text text-grayish">'.$type.'</small></div>
        </div>
    ';

    // Jika Data Tidak Ditemukan
    if (empty($entries)) {
        echo '
            <div class="row mb-2">
                <div class="col-12 text-center">
                    <div class="alert alert-danger"><small>Data Imaging Study tidak ditemukan.</small></div>
                </div>
            </div>
        ';
        exit;
    }

    // Jika Data Ditemukan
    $nomor = 1;
    foreach ($entries as $entry) {

        $resource = $entry['resource'] ?? [];

        // Ambil data utama
        $id            = htmlspecialchars($resource['id'] ?? '-');
        $description   = htmlspecialchars($resource['description'] ?? '-');
        $status        = htmlspecialchars($resource['status'] ?? '-');
        $started       = htmlspecialchars($resource['started'] ?? '-');
        $patientRef    = htmlspecialchars($resource['subject']['reference'] ?? '-');

        // Identifier (ACSN & DICOM UID)
        $acsn = '-';
        $dicomUidFull = '-';
        $dicomUid = '-';
        $dicomUidPrev = "-";
        if (!empty($resource['identifier']) && is_array($resource['identifier'])) {
            foreach ($resource['identifier'] as $identifier) {
                if (($identifier['system'] ?? '') === 'http://sys-ids.kemkes.go.id/acsn/'.$organization_id.'') {
                    $acsn = htmlspecialchars($identifier['value'] ?? '-');
                }
                if (($identifier['system'] ?? '') === 'urn:dicom:uid') {
                    $dicomUidFull = htmlspecialchars($identifier['value'] ?? '-');
                    $dicomUid = str_replace('urn:oid:', '', $dicomUidFull);
                    $dicomUidPrev = substr($dicomUid, 0, 10) . '..' . '..';
                }
            }
        }

        // Modality
        foreach ($resource['modality'] as $modality) {
            $modality_code = htmlspecialchars($modality['code'] ?? '-');
        }
        echo '
            <div class="row mb-2 mt-3">
                <div class="col-12"><small><b><i>'.$nomor.'. '.$description.'</i></b></small></div>
            </div>
        ';
        echo '
            <div class="row mb-2">
                <div class="col-4"><small><i>ID Imaging Study</i></small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7"><small class="text text-grayish">'.$id.'</small></div>
            </div>
             <div class="row mb-2">
                <div class="col-4"><small><i>Accession Number</i></small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7"><small class="text text-grayish">'.$acsn.'</small></div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small><i>Dicom UID</i></small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="'.$dicomUidFull.'">
                        <a href="'.$url_pacs.'/viewer?studyInstanceUID='.$dicomUid.'&modality='.$modality_code.'" target="_blank">
                            '.$dicomUidPrev.' <i class="bi bi-arrow-up-right-square"></i>
                        </a>
                    </small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Status</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7"><small class="text text-grayish">'.$status.'</small></div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Waktu Pemeriksaan</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7"><small class="text text-grayish">'.$started.'</small></div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>ID Pasien</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7"><small class="text text-grayish">'.$patientRef.'</small></div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Modalitas</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7"><small class="text text-grayish">'.$modality_code.'</small></div>
            </div>
        ';
        $nomor++;
    }



    // Debuging
    // $json = json_decode($response, true);
    // echo '<pre>';
    // echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    // echo '</pre>';

?>