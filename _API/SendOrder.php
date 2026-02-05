<?php
    // ======================================================
    // SET HEADER & TIMEZONE
    // ======================================================
    date_default_timezone_set("Asia/Jakarta");

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");

    // ======================================================
    // METHOD VALIDATION
    // ======================================================
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode([
            "status" => "error",
            "message" => "Method tidak diizinkan"
        ]);
        exit;
    }

    // ======================================================
    // CONFIG
    // ======================================================
    include "../_Config/Connection.php";
    include "../_Config/GlobalFunction.php";

    // ======================================================
    // AUTH BEARER TOKEN
    // ======================================================
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? '';

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
        "klinis", "permintaan_pemeriksaan", "tujuan", "pembayaran", "system_creator"
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
    // VALIDATE ALAT PEMERIKSA
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
    // GENERATE ACCESSION NUMBER
    // ======================================================
    $micro = microtime(true);
    $number = substr(str_replace('.', '', $micro), -6);
    $accession_number = "{$alat}-{$number}";

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
    $system_creator       = trim($data['system_creator']);
    $datetime_diminta     = date("Y-m-d H:i:s");

    $klinis_json     = json_encode($data['klinis'], JSON_UNESCAPED_UNICODE);
    $permintaan_json = json_encode($data['permintaan_pemeriksaan'], JSON_UNESCAPED_UNICODE);

    // ======================================================
    // INSERT DATABASE
    // ======================================================
    $sql = "
    INSERT INTO radiologi (
        id_pasien, id_kunjungan, accession_number,
        nama_pasien, priority, asal_kiriman, alat_pemeriksa,
        kode_dokter_pengirim, ihs_dokter_pengirim, nama_dokter_pengirim,
        pesan, klinis, permintaan_pemeriksaan,
        tujuan, pembayaran, datetime_diminta, status_pemeriksaan, orthanc, system_creator
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ";

    $stmt = $Conn->prepare($sql);

    $status_pemeriksaan = "Diminta";
    $orthanc = 0;

    $stmt->bind_param(
        "iisssssssssssssssis",
        $id_pasien,
        $id_kunjungan,
        $accession_number,
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
        $datetime_diminta,
        $status_pemeriksaan,
        $orthanc,
        $system_creator
    );

    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode([
            "status" => "error",
            "message" => "Gagal menyimpan order radiologi",
            "error" => $stmt->error
        ]);
        exit;
    }

    // ======================================================
    // SUCCESS RESPONSE
    // ======================================================
    http_response_code(201);
    echo json_encode([
        "status" => "success",
        "message" => "Order Radiologi Berhasil Dibuat",
        "data" => [
            "id_radiologi" => $stmt->insert_id,
            "accession_number" => $accession_number,
            "datetime_diminta" => $datetime_diminta
        ]
    ]);
    exit;
?>