<?php
    /**
     * ======================================================
     * List Dokter
     * ======================================================
     * 1 Set Timezone UTC
     * 2 Prepare Header Response
     * 3 Include Connetion And Function
     * 4 Catch Token And Validation
     * 5 Get TOKEN from Configuration
     * 6 Get Patient From SIMRS
     * 7 Response JSON
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
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

    // Validasi Method yang diizinkan
    $allowedMethods = ['GET'];

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
    // 5. Dapatkan Koneksi API SIMRS
    // ======================================================
    $status_connection_simrs = 1;
    $url_connection_simrs = GetDetailData($Conn,'connection_simrs','status_connection_simrs',$status_connection_simrs,'url_connection_simrs');
    $token = GetSimrsToken($Conn);

    if($token === false){
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal mendapatkan token SIMRS!'
        ]);
        exit;
    }
   
    // ======================================================
    // 6. Call API get_dokter untuk mendapatkan detail dokter
    // ======================================================
    $curl2 = curl_init();
    curl_setopt_array($curl2, array(
        CURLOPT_URL => ''.$url_connection_simrs.'/API/SIMRS/get_dokter.php',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'token: '.$token.'',
            'X-API-Key: ••••••'
        ),
    ));
    
    $response_dokter = curl_exec($curl2);
    if ($response_dokter === false) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal menghubungi API SIMRS'
        ]);
        exit;
    }

    echo $response_dokter;
?>