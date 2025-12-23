<?php
    /**
     * ======================================================
     * GET PATIENT
     * ======================================================
     * 1 Set Timezone UTC
     * 2 Prepare Header Response
     * 3 Include Connetion And Function
     * 4 Catch Token And Validation
     * 5 Catch patient_id And Validation
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
    header("Access-Control-Allow-Methods: POST");
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
    // 5. AMBIL patient_id DARI URL dan VALIDASI
    // ======================================================
    
    if (empty($_GET['patient_id'])) {
        http_response_code(404);
        echo json_encode([
            "status"  => "error",
            "message" => "ID Pasien Tidak Boleh Kosong"
        ]);
        exit;
    }
    $patient_id = validateAndSanitizeInput($_GET['patient_id']);

    // Validasi patient_id
    $stmt = $Conn->prepare("SELECT id_radiologi, nama_pasien, id_pasien, id_kunjungan FROM radiologi WHERE id_pasien = ?");
    $stmt->bind_param("s", $patient_id);
    $stmt->execute();
    $DataRadiologi = $stmt->get_result()->fetch_assoc();
    if(empty($DataRadiologi['id_radiologi'])){
        http_response_code(400);
        echo json_encode([
            "status"  => "error",
            "message" => "ID Pasien tidak ditemukan"
        ]);
        exit;
    }
    $id_radiologi = $DataRadiologi['id_radiologi'];
    $nama_pasien  = $DataRadiologi['nama_pasien'];
    $id_kunjungan = $DataRadiologi['id_kunjungan'];
    $id_pasien    = $DataRadiologi['id_pasien'];

    // ======================================================
    // 6. GET PATIENT FROM SIMRS
    // ======================================================

    // Buka url SIMRS dari pengaturan 'connection_simrs'
    $status_connection_simrs = 1;
    $url_connection_simrs    = GetDetailData($Conn,'connection_simrs','status_connection_simrs',$status_connection_simrs,'url_connection_simrs');

    //Dapatkan Token SIMRS
    $token = GetSimrsToken($Conn);

    // Jika Token Tidak Valid Dan Gagal Dibuat
    if ($token === false) {
        http_response_code(500);
        echo json_encode([
            "status"  => "error",
            "message" => "Terjadi kesalahan pada saat mencoba melakukan generate token untuk akses ke SIMRS"
        ]);
        exit;
    }

    // Mulai CURL ke SIMRS
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => ''.$url_connection_simrs.'/API/SIMRS/get_detail_kunjungan.php?id='.$id_kunjungan.'',
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
    $response = curl_exec($curl);
    curl_close($curl);
    $data = json_decode($response, true);

    // Jika Response Tidak Valid
    if (empty($data['response']['code']) ||$data['response']['code'] != 200) {
        http_response_code(500);
        echo json_encode([
            "status"  => "error",
            "message" => 'Gagal memuat data pasien<br> Pesan : '.$data['response']['message'].''
        ]);
        exit;
    }

    // Buka Metadata
    $metadata      = $data['metadata'] ?? [];
    $diagnosa_awal = $metadata['DiagAwal'] ?? '-';

    // Pastikan array pasien ada
    $pasien = $metadata['pasien'] ?? [];

    // Helper function untuk nilai yang mungkin kosong
    function getDisplayValue($value, $default = '-') {
        return (isset($value) && trim($value) !== '') ? $value : $default;
    }

    // Buat Variabel Penting
    $id_encounter   = getDisplayValue($metadata['id_encounter'] ?? null);
    $id_ihs         = getDisplayValue($pasien['id_ihs'] ?? null);
    $nama           = getDisplayValue($pasien['nama'] ?? null);
    $gender         = getDisplayValue($pasien['gender'] ?? null);
    $tempat_lahir   = getDisplayValue($pasien['tempat_lahir'] ?? null);
    $tanggal_lahir  = getDisplayValue($pasien['tanggal_lahir'] ?? null);
    $kontak         = getDisplayValue($pasien['kontak'] ?? null);
    $nik            = getDisplayValue($pasien['nik'] ?? null);
    $no_bpjs        = getDisplayValue($pasien['no_bpjs'] ?? null);
    $propinsi       = getDisplayValue($pasien['propinsi'] ?? null);
    $kabupaten      = getDisplayValue($pasien['kabupaten'] ?? null);
    $kecamatan      = getDisplayValue($pasien['kecamatan'] ?? null);
    $desa           = getDisplayValue($pasien['desa'] ?? null);
    $alamat         = getDisplayValue($pasien['alamat'] ?? null);
    $perkawinan     = getDisplayValue($pasien['perkawinan'] ?? null);

    //Routing Gender
    if($gender=="Perempuan"){
        $sex = "F";
    }else{
        $sex = "M";
    }

    // ======================================================
    // 7. RESPONSE JSON Yang Diharapkan
    // ======================================================

    $payload_data = [
        "id"            => $patient_id,
        "full_name"     => $nama_pasien,
        "date_of_birth" => $tanggal_lahir,
        "sex"           => $sex,
        "id_ihs"        => $id_ihs,
        "phone"         => $kontak,
        "nik"           => $nik,
        "province"      => $propinsi,
        "city"          => $kabupaten,
        "district"      => $kecamatan,
        "vilage"        => $desa,
    ];
    http_response_code(200);
    echo json_encode([
        "status"  => "success",
        "message" => 'Data Berhasil Ditemukan',
        "data" => $payload_data,
    ]);
    exit;
?>