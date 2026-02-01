<?php
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
    $requestMethod = $_SERVER['REQUEST_METHOD'] ?? '';

    if (!in_array($requestMethod, $allowedMethods)) {
        http_response_code(405);
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

    $token = $matches[1];

    // Validation Token
    $stmt = $Conn->prepare("SELECT id_api_token, id_api_account, token FROM api_token WHERE expired_at > UTC_TIMESTAMP()");
    $stmt->execute();
    $result = $stmt->get_result();

    $token_valid    = false;
    $id_api_account = null;
    $id_api_token   = null;

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
    // 5. PARAMETER FILTER
    // ======================================================
    $keyword = !empty($_GET['keyword']) ? validateAndSanitizeInput($_GET['keyword']) : '';
    $limit   = !empty($_GET['limit']) ? (int) $_GET['limit'] : 10;
    $page    = !empty($_GET['page']) ? (int) $_GET['page'] : 1;

    if ($limit <= 0) $limit = 10;
    if ($page <= 0) $page = 1;

    $offset = ($page - 1) * $limit;

    // ======================================================
    // 6. HITUNG TOTAL DATA
    // ======================================================
    $searchSql = "";
    $searchParams = [];
    $paramTypes = "";

    if (!empty($keyword)) {
        $searchSql = " AND (
            nama_klinis LIKE ?
            OR snomed_code LIKE ?
            OR snomed_display LIKE ?
            OR kategori LIKE ?
        )";
        $kw = "%$keyword%";
        $searchParams = [$kw, $kw, $kw, $kw];
        $paramTypes = "ssss";
    }

    // Query Count
    $countSql = "SELECT COUNT(*) AS total FROM master_klinis WHERE aktif='Ya' $searchSql";
    $countStmt = $Conn->prepare($countSql);

    if (!empty($searchParams)) {
        $countStmt->bind_param($paramTypes, ...$searchParams);
    }

    $countStmt->execute();
    $countResult = $countStmt->get_result()->fetch_assoc();
    $totalData = $countResult['total'];
    $totalPages = ceil($totalData / $limit);

    // ======================================================
    // 7. QUERY DATA
    // ======================================================
    $dataSql = "
        SELECT 
            id_master_klinis,
            nama_klinis,
            snomed_code,
            snomed_display,
            kategori,
            aktif,
            datetime_create,
            datetime_update
        FROM master_klinis
        WHERE aktif='Ya' $searchSql
        ORDER BY nama_klinis ASC
        LIMIT ? OFFSET ?
    ";

    $dataStmt = $Conn->prepare($dataSql);

    if (!empty($searchParams)) {
        $bindParams = array_merge($searchParams, [$limit, $offset]);
        $bindTypes = $paramTypes . "ii";
        $dataStmt->bind_param($bindTypes, ...$bindParams);
    } else {
        $dataStmt->bind_param("ii", $limit, $offset);
    }

    $dataStmt->execute();
    $dataResult = $dataStmt->get_result();

    // ======================================================
    // 8. BUILD PAYLOAD DATA
    // ======================================================
    $payload_data = [];

    while ($row = $dataResult->fetch_assoc()) {
        $payload_data[] = [
            "id_master_klinis" => $row['id_master_klinis'],
            "nama_klinis"      => $row['nama_klinis'],
            "snomed_code"      => $row['snomed_code'],
            "snomed_display"   => $row['snomed_display'],
            "kategori"         => $row['kategori'],
            "aktif"            => $row['aktif'],
            "datetime_create"  => $row['datetime_create'],
            "datetime_update"  => $row['datetime_update']
        ];
    }

    // ======================================================
    // 9. RESPONSE JSON
    // ======================================================
    http_response_code(200);
    echo json_encode([
        "status"  => "success",
        "message" => "Data Berhasil Ditemukan",
        "meta" => [
            "keyword"      => $keyword,
            "page"         => $page,
            "limit"        => $limit,
            "total_data"   => $totalData,
            "total_pages"  => $totalPages
        ],
        "data" => $payload_data
    ]);
    exit;
?>