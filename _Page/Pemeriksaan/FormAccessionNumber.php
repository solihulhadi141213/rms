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
        echo "<p>Response JSON tidak valid</p>";
        exit;
    }

    // Pastikan entry ada dan berbentuk array
    $entries      = $data['entry'] ?? [];
    $resourceType = $data['resourceType'] ?? "-";
    $total        = $data['total'] ?? "-";
    $type         = $data['type'] ?? "-";

    if (empty($entries)) {
        echo "<p>Data ImagingStudy tidak ditemukan</p>";
        exit;
    }

    foreach ($entries as $entry) {

        $resource = $entry['resource'] ?? [];

        // Ambil data utama
        $id          = htmlspecialchars($resource['id'] ?? '-');
        $description = htmlspecialchars($resource['description'] ?? '-');
        $status      = htmlspecialchars($resource['status'] ?? '-');
        $started     = htmlspecialchars($resource['started'] ?? '-');
        $patientRef  = htmlspecialchars($resource['subject']['reference'] ?? '-');

        // Identifier (ACSN & DICOM UID)
        $acsn = '-';
        $dicomUid = '-';

        if (!empty($resource['identifier']) && is_array($resource['identifier'])) {
            foreach ($resource['identifier'] as $identifier) {
                if (($identifier['system'] ?? '') === 'http://sys-ids.kemkes.go.id/acsn/100026947') {
                    $acsn = htmlspecialchars($identifier['value'] ?? '-');
                }
                if (($identifier['system'] ?? '') === 'urn:dicom:uid') {
                    $dicomUid = htmlspecialchars($identifier['value'] ?? '-');
                }
            }
        }

        echo "<h3>Imaging Study</h3>";
        echo "<table border='1' cellpadding='6' cellspacing='0'>";
        echo "<tr><td>ID</td><td>{$id}</td></tr>";
        echo "<tr><td>Deskripsi</td><td>{$description}</td></tr>";
        echo "<tr><td>Status</td><td>{$status}</td></tr>";
        echo "<tr><td>Waktu Pemeriksaan</td><td>{$started}</td></tr>";
        echo "<tr><td>Pasien</td><td>{$patientRef}</td></tr>";
        echo "<tr><td>ACSN</td><td>{$acsn}</td></tr>";
        echo "<tr><td>DICOM UID</td><td>{$dicomUid}</td></tr>";
        echo "</table><br>";

        // Series & Instance
        $seriesList = $resource['series'] ?? [];

        if (!empty($seriesList)) {
            echo "<strong>Series</strong>";
            echo "<ul>";

            foreach ($seriesList as $series) {
                $seriesUid = htmlspecialchars($series['uid'] ?? '-');
                $seriesMod = htmlspecialchars($series['modality']['code'] ?? '-');
                $seriesNum = htmlspecialchars($series['number'] ?? '-');

                echo "<li>";
                echo "Series #{$seriesNum} | Modality: {$seriesMod} | UID: {$seriesUid}";

                // Instance
                $instances = $series['instance'] ?? [];
                if (!empty($instances)) {
                    echo "<ul>";
                    foreach ($instances as $instance) {
                        $instanceUid = htmlspecialchars($instance['uid'] ?? '-');
                        $sopClass   = htmlspecialchars($instance['sopClass']['code'] ?? '-');
                        $title      = htmlspecialchars($instance['title'] ?? '-');

                        echo "<li>";
                        echo "Instance UID: {$instanceUid}<br>";
                        echo "SOP Class: {$sopClass}<br>";
                        echo "Title: {$title}";
                        echo "</li>";
                    }
                    echo "</ul>";
                }

                echo "</li>";
            }

            echo "</ul>";
        }
    }



        // Debuging
        $json = json_decode($response, true);
        echo '<pre>';
        echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        echo '</pre>';

?>