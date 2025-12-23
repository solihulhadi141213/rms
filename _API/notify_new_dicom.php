<?php
 /**
     * ======================================================
     * NOTIFY NEW DICOM
     * ======================================================
     * 1 Set Timezone UTC
     * 2 Prepare Header Response
     * 3 Include Connetion And Function
     * 4 Validation Method
     * 5 Catch Token And Validation
     * 6 Catch Body Data (application/json)
     * 7 Validasi 'accession_number'
     * 8 Insert Data Into Database (radiologi_expertise)
     * 9 Response JSON
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
    // 4. VALIDATION METHOD
    // ======================================================
    
    $allowedMethods = ['POST'];

    // Ambil method request
    $requestMethod = $_SERVER['REQUEST_METHOD'] ?? '';

    // Jika method tidak diizinkan
    if (!in_array($requestMethod, $allowedMethods)) {

        // Set HTTP Response Code: 405 Method Not Allowed
        http_response_code(405);

        // Response JSON
        echo json_encode([
            "status"  => "error",
            "message" => "Method tidak diizinkan",
            "allowed_method" => $allowedMethods,
            "received_method" => $requestMethod
        ]);

        exit;
    }

    // ======================================================
    // 5. CATCH AUTHORIZATION HEADER (BEARER TOKEN)
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
    // 6. CATCH BODY DATA (application/json)
    // ======================================================

    // Validasi Konten
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    if (stripos($contentType, 'application/json') === false) {
        http_response_code(415); // Unsupported Media Type
        echo json_encode([
            "status"  => "error",
            "message" => "Content-Type harus application/json"
        ]);
        exit;
    }

    // Ambil Raw Body
    $rawBody = file_get_contents('php://input');

    if (empty($rawBody)) {
        http_response_code(400);
        echo json_encode([
            "status"  => "error",
            "message" => "Body request kosong"
        ]);
        exit;
    }

    // Decode JSON
    $data = json_decode($rawBody, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode([
            "status"  => "error",
            "message" => "Format JSON tidak valid",
            "error"   => json_last_error_msg()
        ]);
        exit;
    }

    // TANGKAP DATA (AMAN DENGAN NULL COALESCING)
    $id                  = $data['id'] ?? null;
    $sop_instance_uid    = $data['sop_instance_uid'] ?? null;
    $study_instance_uid  = $data['study_instance_uid'] ?? null;
    $orthanc_study_id    = $data['orthanc_study_id'] ?? null;
    $accession_number    = $data['accession_number'] ?? null;

    $patient_name        = $data['patient_name'] ?? null;
    $patient_id          = $data['patient_id'] ?? null;
    $patient_birth_date  = $data['patient_birth_date'] ?? null;
    $patient_sex         = $data['patient_sex'] ?? null;

    $study_id            = $data['study_id'] ?? null;
    $study_date          = $data['study_date'] ?? null;
    $study_time          = $data['study_time'] ?? null;
    $modality            = $data['modality'] ?? null;
    $body_part_examined  = $data['body_part_examined'] ?? null;

    $institution_name    = $data['institution_name'] ?? null;
    $institution_address = $data['institution_address'] ?? null;
    $institution_id      = $data['institution_id'] ?? null;
    $doctor_id           = $data['doctor_id'] ?? null;

    $inserted_at         = $data['inserted_at'] ?? null;
    $is_reviewed         = $data['is_reviewed'] ?? false;
    $is_suspected        = $data['is_suspected'] ?? false;
    $is_approved         = $data['is_approved'] ?? false;

    // ======================================================
    // 7. VALIDASI ACCESSION NUMBER
    // ======================================================
    $stmt = $Conn->prepare("SELECT * FROM radiologi WHERE accession_number = ?");
    $stmt->bind_param("s", $accession_number);
    $stmt->execute();
    $DataRadiologi = $stmt->get_result()->fetch_assoc();
    if(empty($DataRadiologi['id_radiologi'])){
        http_response_code(400);
        echo json_encode([
            "status"  => "error",
            "message" => "Accession Number Tidak Valid (Tidak Ada Pada Database Server)"
        ]);
        exit;
    }
    $id_radiologi = $DataRadiologi['id_radiologi'];

    // ======================================================
    // 8. INSERT DATA INTO DATABASE (radiologi_dicom)
    // ======================================================

    // Encode ulang data menjadi JSON (AMAN untuk MySQL JSON)
    $json_dicom = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    if ($json_dicom === false) {
        http_response_code(500);
        echo json_encode([
            "status"  => "error",
            "message" => "Gagal meng-encode data DICOM ke JSON"
        ]);
        exit;
    }

    // ======================================================
    // INSERT DATA
    // ======================================================
    $stmt = $Conn->prepare("
        INSERT INTO radiologi_dicom 
        (id_radiologi, accession_number, data_dicom)
        VALUES (?, ?, ?)
    ");

    $stmt->bind_param(
        "iss",
        $id_radiologi,
        $accession_number,
        $json_dicom
    );

    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode([
            "status"  => "error",
            "message" => "Gagal menyimpan data DICOM",
            "error"   => $stmt->error
        ]);
        exit;
    }

    $id_radiologi_dicom = $stmt->insert_id;

    // ======================================================
    // 9. RESPONSE SUCCESS
    // ======================================================
    http_response_code(201); // Created
    echo json_encode([
        "status"  => "success",
        "message" => "Notifikasi DICOM berhasil disimpan",
        "data" => [
            "id_radiologi_dicom" => $id_radiologi_dicom,
            "id_radiologi"       => $id_radiologi,
            "accession_number"   => $accession_number,
            "study_instance_uid" => $study_instance_uid,
            "modality"           => $modality
        ]
    ]);
    exit;

?>