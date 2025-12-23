<?php
    /**
     * ======================================================
     * SUBMIT EXPERTISE USG
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

    $description               = $data['description'] ?? '';
    $timestamp                 = $data['timestamp'] ?? '';
    $finding                   = $data['finding'] ?? '';
    $study_number              = $data['study_number'] ?? '';
    $imaging_study_uuid        = $data['imaging_study_uuid'] ?? '';
    $attachments               = $data['attachments'] ?? '';
    $viewer_link               = $data['viewer_link'] ?? '';
    $study_instance_uid        = $data['study_instance_uid'] ?? '';
    $recommendation            = $data['recommendation'] ?? '';
    $doctor_name               = $data['doctor_name'] ?? '';
    $gestational_sac_size      = $data['gestational_sac_size'] ?? '';
    $crown_rump_length         = $data['crown_rump_length'] ?? '';
    $fetal_heart_rate          = $data['fetal_heart_rate'] ?? '';
    $biparietal_diameter       = $data['biparietal_diameter'] ?? '';
    $head_circumference        = $data['head_circumference'] ?? '';
    $abdominal_circumference   = $data['abdominal_circumference'] ?? '';
    $femur_length              = $data['femur_length'] ?? '';
    $single_deepest_pocket     = $data['single_deepest_pocket'] ?? '';
    $estimated_fetal_weight    = $data['estimated_fetal_weight'] ?? '';
    $fetal_position            = $data['fetal_position'] ?? '';
    $estimated_gestational_age = $data['estimated_gestational_age'] ?? '';
    $estimated_date_birth      = $data['estimated_date_birth'] ?? '';
    $fetal_presentation        = $data['fetal_presentation'] ?? '';
   

    // Sanitasi
    $description               = validateAndSanitizeInput($data['description'] ?? '');
    $finding                   = validateAndSanitizeInput($data['finding'] ?? '');
    $study_number              = validateAndSanitizeInput($data['study_number'] ?? '');
    $imaging_study_uuid        = validateAndSanitizeInput($data['imaging_study_uuid'] ?? '');
    $attachments               = validateAndSanitizeInput($data['attachments'] ?? '');
    $viewer_link               = validateAndSanitizeInput($data['viewer_link'] ?? '');
    $study_instance_uid        = validateAndSanitizeInput($data['study_instance_uid'] ?? '');
    $recommendation            = validateAndSanitizeInput($data['recommendation'] ?? '');
    $doctor_name               = validateAndSanitizeInput($data['doctor_name'] ?? '');
    $fetal_position            = validateAndSanitizeInput($data['fetal_position'] ?? '');
    $fetal_presentation        = validateAndSanitizeInput($data['fetal_presentation'] ?? '');
    $gestational_sac_size      = validateAndSanitizeInput($data['gestational_sac_size'] ?? '');
    $crown_rump_length         = validateAndSanitizeInput($data['crown_rump_length'] ?? '');
    $fetal_heart_rate          = validateAndSanitizeInput($data['fetal_heart_rate'] ?? '');
    $biparietal_diameter       = validateAndSanitizeInput($data['biparietal_diameter'] ?? '');
    $head_circumference        = validateAndSanitizeInput($data['head_circumference'] ?? '');
    $abdominal_circumference   = validateAndSanitizeInput($data['abdominal_circumference'] ?? '');
    $femur_length              = validateAndSanitizeInput($data['femur_length'] ?? '');
    $single_deepest_pocket     = validateAndSanitizeInput($data['single_deepest_pocket'] ?? '');
    $estimated_fetal_weight    = validateAndSanitizeInput($data['estimated_fetal_weight'] ?? '');
    $estimated_gestational_age = validateAndSanitizeInput($data['estimated_gestational_age'] ?? '');
    $estimated_date_birth      = validateAndSanitizeInput($data['estimated_date_birth'] ?? '');

    // ======================================================
    // 7. INSERT INTO DATABASE (radiologi_expertise)
    // ======================================================
    $stmt = $Conn->prepare("INSERT INTO radiologi_expertise_usg 
        (
            id_radiologi,
            accession_number,
            description,
            timestamp,
            finding,
            study_number,
            imaging_study_uuid,
            attachments,
            viewer_link,
            study_instance_uid,
            gestational_sac_size,
            crown_rump_length,
            fetal_heart_rate,
            biparietal_diameter,
            head_circumference,
            abdominal_circumference,
            femur_length,
            single_deepest_pocket,
            estimated_fetal_weight,
            fetal_position,
            estimated_gestational_age,
            estimated_date_birth,
            fetal_presentation,
            recommendation,
            doctor_name
        ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
    ");

    $stmt->bind_param(
        "issssssssssssssssssssssss",
        $id_radiologi,
        $accession_number,
        $description,
        $timestamp,
        $finding,
        $study_number,
        $imaging_study_uuid,
        $attachments,
        $viewer_link,
        $study_instance_uid,
        $gestational_sac_size,
        $crown_rump_length,
        $fetal_heart_rate,
        $biparietal_diameter,
        $head_circumference,
        $abdominal_circumference,
        $femur_length,
        $single_deepest_pocket,
        $estimated_fetal_weight,
        $fetal_position,
        $estimated_gestational_age,
        $estimated_date_birth,
        $fetal_presentation,
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
        "accession_number"          => $accession_number,
        "description"               => $description,
        "timestamp"                 => $timestamp,
        "finding"                   => $finding,
        "study_number"              => $study_number,
        "imaging_study_uuid"        => $imaging_study_uuid,
        "attachments"               => $attachments,
        "viewer_link"               => $viewer_link,
        "study_instance_uid"        => $study_instance_uid,
        "gestational_sac_size"      => $gestational_sac_size,
        "crown_rump_length"         => $crown_rump_length,
        "fetal_heart_rate"          => $fetal_heart_rate,
        "biparietal_diameter"       => $biparietal_diameter,
        "head_circumference"        => $head_circumference,
        "abdominal_circumference"   => $abdominal_circumference,
        "femur_length"              => $femur_length,
        "single_deepest_pocket"     => $single_deepest_pocket,
        "estimated_fetal_weight"    => $estimated_fetal_weight,
        "fetal_position"            => $fetal_position,
        "estimated_gestational_age" => $estimated_gestational_age,
        "estimated_date_birth"      => $estimated_date_birth,
        "fetal_presentation"        => $fetal_presentation,
        "recommendation"            => $recommendation,
        "doctor_name"               => $doctor_name
    ];
    http_response_code(200); // Bad Request
    echo json_encode([
        "status"  => "success",
        "message" => "Data Berhasil Disimpan",
        "data" => $data_expertise
    ]);
    exit;
?>