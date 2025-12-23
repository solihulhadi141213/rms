<?php
    /**
     * ======================================================
     * SUBMIT EXPERTISE
     * ======================================================
     * 1 Set Timezone UTC
     * 2 Prepare Header Response
     * 3 Include Connetion And Function
     * 4 Catch Token And Validation
     * 5 Catch accession_number And Validation
     * 6 Catch Body Data (x-www-form-urlencoded)
     * 7 Insert Data Into Database (radiologi_expertise)
     * 8 Response JSON
     * ======================================================
     */

    // ======================================================
    // 1. SET TIMEZONE
    // ======================================================
    date_default_timezone_set('UTC');
    
    // ======================================================
    // 2. HEADER RESPONSE (CORS + JSON)
    // ======================================================
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

    // ======================================================
    // 3. CONNECTION AND FUNCTION
    // ======================================================
    include "../_Config/Connection.php";
    include "../_Config/GlobalFunction.php";

    // ======================================================
    // 4. CATCH AUTHORIZATION HEADER (BEARER TOKEN)
    // ======================================================
    $headers = getallheaders();

    $authHeader = '';
    if (isset($headers['Authorization'])) {
        $authHeader = $headers['Authorization'];
    } elseif (isset($headers['authorization'])) {
        $authHeader = $headers['authorization'];
    }

    // Validation Format Bearer
    if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        http_response_code(401);
        echo json_encode([
            "status"  => "error",
            "message" => "Authorization Bearer token tidak ditemukan"
        ]);
        exit;
    }

    // Creat Variabel Token
    $token = $matches[1]; 

    //Validation Token
    $stmt = $Conn->prepare("SELECT id_api_token, id_api_account, token FROM api_token WHERE expired_at > UTC_TIMESTAMP()");
    $stmt->execute();
    $result = $stmt->get_result();

    $token_valid      = false;
    $id_api_account   = null;
    $id_api_token     = null;

    while ($row = $result->fetch_assoc()) {
        if (password_verify($token, $row['token'])) {
            $token_valid    = true;
            $id_api_account = $row['id_api_account'];
            $id_api_token   = $row['id_api_token'];
            break;
        }
    }

    if (!$token_valid) {
        http_response_code(401);
        echo json_encode([
            "status"  => "error",
            "message" => "Token tidak valid atau sudah expired"
        ]);
        exit;
    }

    // ======================================================
    // 5. AMBIL accession_number DARI URL dan VALIDASI
    // ======================================================
    
    $requestUri = $_SERVER['REQUEST_URI'];
    $requestUri = strtok($requestUri, '?');
    $segments = explode('/', trim($requestUri, '/'));
    $accession_number = $segments[3] ?? null;
    if (empty($accession_number)) {
        http_response_code(404);
        echo json_encode([
            "status"  => "error",
            "message" => "Accession Number Tidak Boleh Kosong"
        ]);
        exit;
    }
    $accession_number = validateAndSanitizeInput($accession_number);

    // Validasi accession_number
    $stmt = $Conn->prepare("SELECT * FROM radiologi WHERE accession_number = ?");
    $stmt->bind_param("s", $accession_number);
    $stmt->execute();
    $DataRadiologi = $stmt->get_result()->fetch_assoc();
    if(empty($DataRadiologi['id_radiologi'])){
        http_response_code(400);
        echo json_encode([
            "status"  => "error",
            "message" => "accession_number tidak ditemukan"
        ]);
        exit;
    }
    $id_radiologi = $DataRadiologi['id_radiologi'];

    // ======================================================
    // 6. TANGKAP DATA BODY
    // ======================================================
    $rawBody = file_get_contents("php://input");
    $data    = [];
    parse_str($rawBody, $data);
    if (empty($data)) {
        http_response_code(400);
        echo json_encode([
            "status"  => "error",
            "message" => "Data request tidak ditemukan"
        ]);
        exit;
    }

    $description                       = $data['description'] ?? '';
    $timestamp                         = $data['timestamp'] ?? '';
    $finding                           = $data['finding'] ?? '';
    $study_number                      = $data['study_number'] ?? '';
    $attachments                       = $data['attachments'] ?? '';
    $viewer_link                       = $data['viewer_link'] ?? '';
    $study_instance_uid                = $data['study_instance_uid'] ?? '';
    $cardiac_silhouette                = $data['cardiac_silhouette'] ?? '';
    $aorta                             = $data['aorta'] ?? '';
    $mediastinum                       = $data['mediastinum']   ?? '';
    $lungs                             = $data['lungs'] ?? '';
    $trachea                           = $data['trachea'] ?? '';
    $diaphragm_and_costophrenic_angles = $data['diaphragm_and_costophrenic_angles'] ?? '';
    $visualized_structures             = $data['visualized_structures'] ?? '';
    $impression                        = $data['impression'] ?? '';
    $recommendation                    = $data['recommendation'] ?? '';
    $doctor_name                       = $data['doctor_name'] ?? '';

    // Sanitasi
    $description                       = validateAndSanitizeInput($description);
    $timestamp                         = validateAndSanitizeInput($timestamp);
    $finding                           = validateAndSanitizeInput($finding);
    $study_number                      = validateAndSanitizeInput($study_number);
    $attachments                       = validateAndSanitizeInput($attachments);
    $viewer_link                       = validateAndSanitizeInput($viewer_link);
    $study_instance_uid                = validateAndSanitizeInput($study_instance_uid);
    $cardiac_silhouette                = validateAndSanitizeInput($cardiac_silhouette);
    $aorta                             = validateAndSanitizeInput($aorta);
    $mediastinum                       = validateAndSanitizeInput($mediastinum);
    $lungs                             = validateAndSanitizeInput($lungs);
    $trachea                           = validateAndSanitizeInput($trachea);
    $diaphragm_and_costophrenic_angles = validateAndSanitizeInput($diaphragm_and_costophrenic_angles);
    $visualized_structures             = validateAndSanitizeInput($visualized_structures);
    $impression                        = validateAndSanitizeInput($impression);
    $recommendation                    = validateAndSanitizeInput($recommendation);
    $doctor_name                       = validateAndSanitizeInput($doctor_name);

    // ======================================================
    // 7. INSERT INTO DATABASE (radiologi_expertise)
    // ======================================================
    $stmt = $Conn->prepare("
        INSERT INTO radiologi_expertise (
            id_radiologi,accession_number, description, timestamp, finding, study_number,
            attachments, viewer_link, study_instance_uid,
            cardiac_silhouette, aorta, mediastinum, lungs, trachea,
            diaphragm_and_costophrenic_angles, visualized_structures,
            impression, recommendation, doctor_name
        ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
    ");

    $stmt->bind_param(
        "issssssssssssssssss",
        $id_radiologi,
        $accession_number,
        $description,
        $timestamp,
        $finding,
        $study_number,
        $attachments,
        $viewer_link,
        $study_instance_uid,
        $cardiac_silhouette,
        $aorta,
        $mediastinum,
        $lungs,
        $trachea,
        $diaphragm_and_costophrenic_angles,
        $visualized_structures,
        $impression,
        $recommendation,
        $doctor_name
    );

    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode([
            "status" => "error",
            "message" => "Gagal menyimpan data expertise"
        ]);
        exit;
    }
    // ======================================================
    // 8. RESPONSE JSON SUCCESS
    // ======================================================

    $data_expertise = [
        "accession_number"                  => $accession_number,
        "description"                       => $description,
        "timestamp"                         => $timestamp,
        "finding"                           => $finding,
        "study_number"                      => $study_number,
        "attachments"                       => $attachments,
        "viewer_link"                       => $viewer_link,
        "study_instance_uid"                => $study_instance_uid,
        "cardiac_silhouette"                => $cardiac_silhouette,
        "aorta"                             => $aorta,
        "mediastinum"                       => $mediastinum,
        "lungs"                             => $lungs,
        "trachea"                           => $trachea,
        "diaphragm_and_costophrenic_angles" => $diaphragm_and_costophrenic_angles,
        "visualized_structures"             => $visualized_structures,
        "impression"                        => $impression,
        "recommendation"                    => $recommendation,
        "doctor_name"                       => $doctor_name
    ];
    http_response_code(200); // Bad Request
    echo json_encode([
        "status"  => "success",
        "message" => "Data Berhasil Disimpan",
        "data" => $data_expertise
    ]);
    exit;
?>