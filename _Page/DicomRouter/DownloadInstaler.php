<?php
    /**
     * ============================================================
     * DOWNLOAD INSTALER DICOM ROUTER
     * ============================================================
     */

    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    date_default_timezone_set("Asia/Jakarta");

    /* ============================================================
    * VALIDASI SESSION
    * ============================================================ */
    if (empty($SessionIdAccess)) {
        echo '<div class="alert alert-danger text-center">
                <small>Sesi berakhir. Silakan login ulang.</small>
            </div>';
        exit;
    }

   
    /* ============================================================
    * TOKEN SATUSEHAT
    * ============================================================ */
    $tokenResult = generateTokenSatuSehat($Conn);
    if (
        empty($tokenResult) ||
        $tokenResult['status'] !== 'success' ||
        empty($tokenResult['token'])
    ) {
        echo '<div class="alert alert-danger text-center">
                <small>Gagal mendapatkan token SATUSEHAT.</small>
            </div>';
        exit;
    }

    $token = $tokenResult['token'];

    /* ============================================================
    * KONFIGURASI KONEKSI SATUSEHAT
    * ============================================================ */
    $status_connection = 1;
    $stmt = $Conn->prepare("
        SELECT url_connection_satu_sehat
        FROM connection_satu_sehat
        WHERE status_connection_satu_sehat = ?
        LIMIT 1
    ");
    $stmt->bind_param("i", $status_connection);
    $stmt->execute();
    $config = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (empty($config['url_connection_satu_sehat'])) {
        echo '<div class="alert alert-danger text-center">
                <small>Konfigurasi koneksi SATUSEHAT tidak ditemukan.</small>
            </div>';
        exit;
    }

    $base_url = rtrim($config['url_connection_satu_sehat'], '/');
    $url      = $base_url . "/dicom-router-installer";

    /* ============================================================
    * CURL REQUEST
    * ============================================================ */
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json'
        ],
        CURLOPT_TIMEOUT => 30,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false
    ]);

    $response = curl_exec($curl);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($curl);
    curl_close($curl);

    if ($curl_error) {
        echo '<div class="alert alert-danger">
                <small>CURL Error: ' . htmlspecialchars($curl_error) . '</small>
            </div>';
        exit;
    }

    if ($http_code !== 200) {
        echo '<div class="alert alert-danger">
                <small>Gagal mengambil data Observation (HTTP ' . $http_code . ').</small>
            </div>';
        exit;
    }

    /* ============================================================
    * PARSE RESPONSE JSON
    * ============================================================ */
    $data = json_decode($response, true);

    echo $response;
    
?>
