<?php
    // SET HEADER & TIMEZONE
    date_default_timezone_set("Asia/Jakarta");

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: PUT");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");

    // VALIDASI METODE PENGIRIMAN DATA
    if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
        http_response_code(405);
        echo json_encode([
            "status" => "error",
            "message" => "Method tidak diizinkan"
        ]);
        exit;
    }

    // KONEKSI DAN FUNCTION
    include "../_Config/Connection.php";
    include "../_Config/GlobalFunction.php";

    // AUTH BEARER TOKEN
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';

    if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        http_response_code(401);
        echo json_encode([
            "status" => "error",
            "message" => "Token Bearer tidak ditemukan"
        ]);
        exit;
    }

    $token = $matches[1];

    // Validate Token
    $stmt = $Conn->prepare("SELECT token FROM api_token WHERE expired_at > UTC_TIMESTAMP()");
    $stmt->execute();
    $result = $stmt->get_result();

    $token_valid = false;
    while ($row = $result->fetch_assoc()) {
        if (password_verify($token, $row['token'])) {
            $token_valid = true;
            break;
        }
    }

    if (!$token_valid) {
        http_response_code(401);
        echo json_encode([
            "status" => "error",
            "message" => "Token tidak valid atau expired"
        ]);
        exit;
    }

    // ======================================================
    // VALIDASI PARAMETER id_radiologi
    // ======================================================
    if (empty($_GET['id_radiologi'])) {
        http_response_code(422);
        echo json_encode([
            "status" => "error",
            "message" => "id_radiologi tidak boleh kosong!"
        ]);
        exit;
    }

    $id_radiologi = (int) $_GET['id_radiologi'];

    // Cek apakah data radiologi ada
    $check = $Conn->prepare("SELECT id_radiologi, status_pemeriksaan FROM radiologi WHERE id_radiologi = ?");
    $check->bind_param("i", $id_radiologi);
    $check->execute();
    $check_result = $check->get_result();

    if ($check_result->num_rows == 0) {
        http_response_code(404);
        echo json_encode([
            "status" => "error",
            "message" => "Data radiologi tidak ditemukan"
        ]);
        exit;
    }
    $data_lama = $check_result->fetch_assoc();

    if ($data_lama['status_pemeriksaan'] !== 'Diminta') {
        echo json_encode([
            "status" => 'error',
            "message" => "Perubahan pada order pemeriksaan radiologi tidak bisa dilakukan karena order sudah dikerjakan"
        ]);
        exit;
    }

    // ======================================================
    // GET JSON BODY
    // ======================================================
    $rawInput = file_get_contents("php://input");
    $data = json_decode($rawInput, true);

    if (!$data) {
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "message" => "Payload JSON tidak valid"
        ]);
        exit;
    }

    // ======================================================
    // REQUIRED FIELDS
    // ======================================================
    $required = [
        "id_pasien", "id_kunjungan", "nama_pasien", "priority",
        "asal_kiriman", "alat_pemeriksa", "nama_dokter_pengirim",
        "klinis", "permintaan_pemeriksaan", "tujuan", "pembayaran"
    ];

    foreach ($required as $field) {
        if (!isset($data[$field]) || $data[$field] === "") {
            http_response_code(422);
            echo json_encode([
                "status" => "error",
                "message" => "Field wajib tidak boleh kosong",
                "field" => $field
            ]);
            exit;
        }
    }

    // ======================================================
    // VALIDATE ALAT PEMERIKSA (Modality)
    // ======================================================
    $allowedModality = [
        'XR' => 'X-Ray',
        'CT' => 'CT-Scan',
        'US' => 'USG',
        'MR' => 'MRI',
        'NM' => 'Nuclear Medicine',
        'PT' => 'PET Scan',
        'DX' => 'Digital Radiography',
        'CR' => 'Computed Radiography'
    ];

    $alat = strtoupper(trim($data['alat_pemeriksa']));

    if (!array_key_exists($alat, $allowedModality)) {
        http_response_code(422);
        echo json_encode([
            "status" => "error",
            "message" => "Kode alat_pemeriksa tidak valid",
            "allowed" => array_keys($allowedModality)
        ]);
        exit;
    }

    // ======================================================
    // VALIDATE TUJUAN
    // ======================================================
    $tujuan = $data['tujuan'];
    if (!in_array($tujuan, ['Rajal', 'Ranap'])) {
        http_response_code(422);
        echo json_encode([
            "status" => "error",
            "message" => "Tujuan hanya boleh Rajal atau Ranap"
        ]);
        exit;
    }

    // ======================================================
    // VALIDATE PRIORITY
    // ======================================================
    if (!in_array($data['priority'], ['routine', 'urgent', 'stat'])) {
        http_response_code(422);
        echo json_encode([
            "status" => "error",
            "message" => "Priority hanya boleh routine, urgent, stat"
        ]);
        exit;
    }

    // ======================================================
    // VALIDATE JSON klinis & permintaan_pemeriksaan
    // ======================================================
    if (!is_array($data['klinis'])) {
        http_response_code(422);
        echo json_encode([
            "status" => "error",
            "message" => "Format klinis harus JSON Array"
        ]);
        exit;
    }

    if (!is_array($data['permintaan_pemeriksaan'])) {
        http_response_code(422);
        echo json_encode([
            "status" => "error",
            "message" => "Format permintaan_pemeriksaan harus JSON Array"
        ]);
        exit;
    }

    // ======================================================
    // PREPARE DATA
    // ======================================================
    $id_pasien            = (int) $data['id_pasien'];
    $id_kunjungan         = (int) $data['id_kunjungan'];
    $nama_pasien          = trim($data['nama_pasien']);
    $priority             = $data['priority'];
    $asal_kiriman         = trim($data['asal_kiriman']);
    $alat_pemeriksa       = $alat;
    $kode_dokter_pengirim = $data['kode_dokter_pengirim'] ?? null;
    $ihs_dokter_pengirim  = $data['ihs_dokter_pengirim'] ?? null;
    $nama_dokter_pengirim = trim($data['nama_dokter_pengirim']);
    $pesan                = $data['pesan'] ?? null;
    $tujuan               = $tujuan;
    $pembayaran           = trim($data['pembayaran']);

    // Encode JSON untuk klinis dan permintaan_pemeriksaan
    $klinis_json     = json_encode($data['klinis'], JSON_UNESCAPED_UNICODE);
    $permintaan_json = json_encode($data['permintaan_pemeriksaan'], JSON_UNESCAPED_UNICODE);

    // ======================================================
    // UPDATE KE DATABASE
    // ======================================================
    $sql = "
        UPDATE radiologi SET
            id_pasien = ?,
            id_kunjungan = ?,
            nama_pasien = ?,
            priority = ?,
            asal_kiriman = ?,
            alat_pemeriksa = ?,
            kode_dokter_pengirim = ?,
            ihs_dokter_pengirim = ?,
            nama_dokter_pengirim = ?,
            pesan = ?,
            klinis = ?,
            permintaan_pemeriksaan = ?,
            tujuan = ?,
            pembayaran = ?
        WHERE id_radiologi = ?
    ";

    $stmt = $Conn->prepare($sql);

    if (!$stmt) {
        http_response_code(500);
        echo json_encode([
            "status" => "error",
            "message" => "Gagal mempersiapkan query update"
        ]);
        exit;
    }

    $stmt->bind_param(
        "iissssssssssssi",
        $id_pasien,
        $id_kunjungan,
        $nama_pasien,
        $priority,
        $asal_kiriman,
        $alat_pemeriksa,
        $kode_dokter_pengirim,
        $ihs_dokter_pengirim,
        $nama_dokter_pengirim,
        $pesan,
        $klinis_json,
        $permintaan_json,
        $tujuan,
        $pembayaran,
        $id_radiologi
    );

    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode([
            "status" => "error",
            "message" => "Gagal memperbaharui data radiologi"
        ]);
        exit;
    }

    // ======================================================
    // SUCCESS RESPONSE
    // ======================================================
    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "message" => "Order Radiologi Berhasil Diperbaharui"
    ]);
    exit;
?>
