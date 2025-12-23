<?php
    /**
     * ============================================================
     * DETAIL SERVICE REQUEST RADIOLOGI - SATUSEHAT
     * ============================================================
     * Menampilkan detail ServiceRequest berdasarkan ID
     * Mengacu pada standar FHIR R4 (Radiology Workflow)
     * ============================================================
     */

    /* ============================================================
    * 1. KONEKSI, SESSION, & KONFIGURASI DASAR
    * ============================================================ */
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    // Zona Waktu Aplikasi
    date_default_timezone_set("Asia/Jakarta");

    /* ============================================================
    * 2. VALIDASI SESSION AKSES
    * ============================================================ */
    if (empty($SessionIdAccess)) {
        echo '<div class="alert alert-danger text-center"><small>Sesi berakhir. Silakan login ulang.</small></div>';
        exit;
    }

    /* ============================================================
    * 3. VALIDASI PARAMETER INPUT
    * ============================================================ */
    if (empty($_POST['id_service_request'])) {
        echo '<div class="alert alert-danger text-center"><small>ID ServiceRequest tidak boleh kosong.</small></div>';
        exit;
    }

    // Sanitasi input
    $id_service_request = validateAndSanitizeInput($_POST['id_service_request']);

    /* ============================================================
    * 4. GENERATE TOKEN SATUSEHAT
    * ============================================================ */
    $tokenResult = generateTokenSatuSehat($Conn);
    if ($tokenResult['status'] !== 'success') {
        echo '<div class="alert alert-danger text-center"><small>Gagal generate token: '.$tokenResult['message'].'</small></div>';
        exit;
    }
    $token = $tokenResult['token'];

    /* ============================================================
    * 5. AMBIL KONFIGURASI KONEKSI SATUSEHAT AKTIF
    * ============================================================ */
    $status_connection = 1;
    $stmt = $Conn->prepare("SELECT * FROM connection_satu_sehat WHERE status_connection_satu_sehat = ?");
    $stmt->bind_param("i", $status_connection);
    $stmt->execute();
    $config = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$config) {
        echo '<div class="alert alert-danger text-center"><small>Koneksi SATUSEHAT tidak ditemukan.</small></div>';
        exit;
    }

    // Endpoint ServiceRequest
    $base_url = rtrim($config['url_connection_satu_sehat'], '/');
    $url = $base_url . "/fhir-r4/v1/ServiceRequest/" . $id_service_request;

    /* ============================================================
    * 6. CURL REQUEST KE SATUSEHAT
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
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false
    ]);

    $response = curl_exec($curl);
    $curl_error = curl_error($curl);
    curl_close($curl);

    if ($curl_error) {
        echo '<div class="alert alert-danger"><small>CURL Error: '.$curl_error.'</small></div>';
        exit;
    }

    /* ============================================================
    * 7. PARSING RESPONSE JSON
    * ============================================================ */
    $data = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo '<div class="alert alert-danger"><small>Response bukan JSON valid.</small></div>';
        exit;
    }

    /* ============================================================
    * 8. EKSTRAKSI DATA UTAMA SERVICE REQUEST
    * ============================================================ */
    $id         = $data['id'] ?? '-';
    $status     = $data['status'] ?? '-';
    $intent     = $data['intent'] ?? '-';
    $priority   = $data['priority'] ?? '-';

    $encounter  = $data['encounter'] ?? [];
    $subject    = $data['subject'] ?? [];
    $requester  = $data['requester'] ?? [];
    $performer  = $data['performer'] ?? [];
    $reasonCode = $data['reasonCode'] ?? [];
    $code       = $data['code'] ?? [];

    // Format tanggal
    $occurrence = '-';
    if (!empty($data['occurrenceDateTime'])) {
        $dt = new DateTime($data['occurrenceDateTime']);
        $occurrence = $dt->format('d F Y H:i T');
    }

    /* ============================================================
    * 9. DICTIONARY (ENUM) UNTUK TAMPILAN USER
    * ============================================================ */
    $intentMap = [
        'order' => 'Perintah Pemeriksaan',
        'plan'  => 'Rencana',
        'proposal' => 'Usulan'
    ];

    $priorityMap = [
        'routine' => 'Biasa',
        'urgent'  => 'Segera',
        'stat'    => 'Gawat Darurat'
    ];

    /* ============================================================
    * 10. TAMPILAN DATA KE UI
    * ============================================================ */
    echo '<div class="mb-3"><b>A. Informasi Service Request</b></div>';

    function row($label, $value) {
        echo '
        <div class="row mb-2">
            <div class="col-4"><small>'.$label.'</small></div>
            <div class="col-1">:</div>
            <div class="col-7"><small>'.$value.'</small></div>
        </div>';
    }

    row('ID ServiceRequest', $id);
    row('Status', ucfirst($status));
    row('Intent', $intentMap[$intent] ?? $intent);
    row('Prioritas', $priorityMap[$priority] ?? $priority);
    row('Tanggal Permintaan', $occurrence);
    row('Encounter', $encounter['reference'] ?? '-');

    /* ============================================================
    * 11. INFORMASI PEMERIKSAAN (LOINC)
    * ============================================================ */
    echo '<hr><div class="mb-2"><b>B. Pemeriksaan Radiologi</b></div>';

    $examCode = $code['coding'][0]['code'] ?? '-';
    $examName = $code['coding'][0]['display'] ?? '-';
    $examText = $code['text'] ?? '-';

    row('Kode LOINC', $examCode);
    row('Nama Pemeriksaan', $examName);
    row('Keterangan Tambahan', $examText);

    /* ============================================================
    * 12. ALASAN KLINIS (SNOMED)
    * ============================================================ */
    echo '<hr><div class="mb-2"><b>C. Alasan Klinis</b></div>';

    if (!empty($reasonCode)) {
        foreach ($reasonCode as $r) {
            row('Diagnosa', $r['text'] ?? '-');
            row(
                'SNOMED',
                ($r['coding'][0]['code'] ?? '-') . ' - ' . ($r['coding'][0]['display'] ?? '-')
            );
        }
    } else {
        row('Alasan', '-');
    }

    /* ============================================================
    * 13. REQUESTER (DOKTER PEMINTA)
    * ============================================================ */
    echo '<hr><div class="mb-2"><b>D. Dokter Peminta</b></div>';
    row('Nama', $requester['display'] ?? '-');
    row('Reference', $requester['reference'] ?? '-');

    /* ============================================================
    * 14. PERFORMER (UNIT / DOKTER RADIOLOGI)
    * ============================================================ */
    echo '<hr><div class="mb-2"><b>E. Pelaksana</b></div>';

    if (!empty($performer)) {
        foreach ($performer as $p) {
            row('Nama', $p['display'] ?? '-');
            row('Reference', $p['reference'] ?? '-');
        }
    } else {
        row('Pelaksana', '-');
    }

?>
