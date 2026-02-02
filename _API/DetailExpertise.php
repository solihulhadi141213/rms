<?php
    // ======================================================
    // TIMEZONE & HEADER
    // ======================================================
    date_default_timezone_set('UTC');

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

    // ======================================================
    // VALIDASI METHOD
    // ======================================================
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        echo json_encode([
            "status" => "error",
            "message" => "Method tidak diizinkan",
            "allowed_method" => ["GET"],
            "received_method" => $_SERVER['REQUEST_METHOD']
        ]);
        exit;
    }

    // ======================================================
    // CONNECTION & FUNCTION
    // ======================================================
    include "../_Config/Connection.php";
    include "../_Config/GlobalFunction.php";

    // ======================================================
    // VALIDASI BEARER TOKEN
    // ======================================================
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';

    if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        http_response_code(401);
        echo json_encode([
            "status" => "error",
            "message" => "Authorization Bearer Token tidak valid"
        ]);
        exit;
    }

    $token = $matches[1];

    // Validasi Token
    $stmt = $Conn->prepare("SELECT id_api_token, id_api_account, token FROM api_token WHERE expired_at > UTC_TIMESTAMP()");
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
    // VALIDASI PARAMETER
    // ======================================================
    if (empty($_GET['id_radiologi']) && empty($_GET['accession_number'])) {
        http_response_code(422);
        echo json_encode([
            "status" => "error",
            "message" => "Gunakan id_radiologi atau accession_number"
        ]);
        exit;
    }

    // ======================================================
    // RESOLVE ID RADIOLOGI
    // ======================================================
    if (!empty($_GET['id_radiologi'])) {
        $id_radiologi = validateAndSanitizeInput($_GET['id_radiologi']);
    } else {
        $accession_number = validateAndSanitizeInput($_GET['accession_number']);

        $stmt = $Conn->prepare("SELECT id_radiologi FROM radiologi WHERE accession_number = ?");
        $stmt->bind_param("s", $accession_number);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();

        if (!$res) {
            http_response_code(404);
            echo json_encode([
                "status" => "error",
                "message" => "accession_number tidak ditemukan"
            ]);
            exit;
        }

        $id_radiologi = $res['id_radiologi'];
    }

    // ======================================================
    // CEK DATA RADIOLOGI UTAMA
    // ======================================================
    $stmt = $Conn->prepare("SELECT id_radiologi, accession_number FROM radiologi WHERE id_radiologi = ?");
    $stmt->bind_param("i", $id_radiologi);
    $stmt->execute();
    $radiologi = $stmt->get_result()->fetch_assoc();

    if (!$radiologi) {
        http_response_code(404);
        echo json_encode([
            "status" => "error",
            "message" => "Data radiologi tidak ditemukan"
        ]);
        exit;
    }

    // ======================================================
    // FUNCTION AMBIL LIST DATA
    // ======================================================
    function fetchList($Conn, $query, $param) {
        $stmt = $Conn->prepare($query);
        $stmt->bind_param("i", $param);
        $stmt->execute();
        $res = $stmt->get_result();

        $rows = [];
        while ($row = $res->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }

    // ======================================================
    // AMBIL DATA DARI 3 TABEL EXPERTISE
    // ======================================================
    $local_exp = fetchList($Conn, "
        SELECT id_radiologi_local_exp, temuan, kesan, saran, catatan
        FROM radiologi_local_exp
        WHERE id_radiologi = ?
    ", $id_radiologi);

    $expertise = fetchList($Conn, "
        SELECT id_radiologi_expertise, accession_number, description, timestamp, finding,
            study_number, attachments, viewer_link, study_instance_uid,
            cardiac_silhouette, aorta, mediastinum, lungs, trachea,
            diaphragm_and_costophrenic_angles, visualized_structures,
            impression, recommendation, doctor_name
        FROM radiologi_expertise
        WHERE id_radiologi = ?
    ", $id_radiologi);

    $expertise_usg = fetchList($Conn, "
        SELECT id_radiologi_expertise_usg, accession_number, description, timestamp, finding,
            study_number, imaging_study_uuid, attachments, viewer_link,
            study_instance_uid, recommendation, doctor_name,
            gestational_sac_size, crown_rump_length, fetal_heart_rate,
            biparietal_diameter, head_circumference, abdominal_circumference,
            femur_length, single_deepest_pocket, estimated_fetal_weight,
            fetal_position, estimated_gestational_age, estimated_date_birth,
            fetal_presentation
        FROM radiologi_expertise_usg
        WHERE id_radiologi = ?
    ", $id_radiologi);

    // ======================================================
    // RESPONSE SUCCESS
    // ======================================================
    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "id_radiologi" => $id_radiologi,
        "accession_number" => $radiologi['accession_number'],
        "data" => [
            "local_expertise" => $local_exp,
            "expertise_radiologi" => $expertise,
            "expertise_usg" => $expertise_usg
        ],
        "total" => [
            "local_expertise" => count($local_exp),
            "expertise_radiologi" => count($expertise),
            "expertise_usg" => count($expertise_usg)
        ]
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

    exit;

?>