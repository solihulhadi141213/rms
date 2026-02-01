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

// Validasi Method
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
// 4. CATCH AUTHORIZATION HEADER
// ======================================================
$headers = getallheaders();
$authHeader = '';

if (isset($headers['Authorization'])) {
    $authHeader = $headers['Authorization'];
} elseif (isset($headers['authorization'])) {
    $authHeader = $headers['authorization'];
}

// Validation Bearer Token
if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    http_response_code(401);
    echo json_encode([
        "status"  => "error",
        "message" => "Authorization Bearer token tidak ditemukan"
    ]);
    exit;
}

$token = $matches[1];

// Validate Token
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
        "status"  => "error",
        "message" => "Token tidak valid atau expired"
    ]);
    exit;
}

// ======================================================
// 5. PARAMETER FILTER
// ======================================================
$keyword  = !empty($_GET['keyword']) ? validateAndSanitizeInput($_GET['keyword']) : '';
$modality = !empty($_GET['modalitas']) ? validateAndSanitizeInput($_GET['modalitas']) : '';
$limit    = !empty($_GET['limit']) ? (int) $_GET['limit'] : 10;
$page     = !empty($_GET['page']) ? (int) $_GET['page'] : 1;

if ($limit <= 0) $limit = 10;
if ($page <= 0) $page = 1;

$offset = ($page - 1) * $limit;

// ======================================================
// 6. BUILD SEARCH FILTER
// ======================================================
$whereSql = " WHERE 1=1 ";
$params = [];
$types  = "";

// Filter Modalitas
if (!empty($modality)) {
    $whereSql .= " AND modalitas = ? ";
    $params[] = $modality;
    $types   .= "s";
}

// Filter Keyword
if (!empty($keyword)) {
    $whereSql .= " AND (
        nama_pemeriksaan LIKE ?
        OR pemeriksaan_code LIKE ?
        OR pemeriksaan_description LIKE ?
        OR bodysite_code LIKE ?
        OR report_code LIKE ?
    ) ";
    $kw = "%$keyword%";
    $params = array_merge($params, [$kw, $kw, $kw, $kw, $kw]);
    $types .= "sssss";
}

// ======================================================
// 7. COUNT TOTAL DATA
// ======================================================
$countSql = "SELECT COUNT(*) AS total FROM master_pemeriksaan $whereSql";
$countStmt = $Conn->prepare($countSql);

if (!empty($params)) {
    $countStmt->bind_param($types, ...$params);
}

$countStmt->execute();
$totalData = $countStmt->get_result()->fetch_assoc()['total'];
$totalPages = ceil($totalData / $limit);

// ======================================================
// 8. FETCH DATA
// ======================================================
$dataSql = "
    SELECT 
        id_master_pemeriksaan,
        nama_pemeriksaan,
        modalitas,
        pemeriksaan_code,
        pemeriksaan_description,
        pemeriksaan_sys,
        bodysite_code,
        bodysite_description,
        bodysite_sys,
        report_code,
        report_description,
        report_sys
    FROM master_pemeriksaan
    $whereSql
    ORDER BY nama_pemeriksaan ASC
    LIMIT ? OFFSET ?
";

$dataStmt = $Conn->prepare($dataSql);

$bindParams = $params;
$bindTypes  = $types . "ii";
$bindParams[] = $limit;
$bindParams[] = $offset;

$dataStmt->bind_param($bindTypes, ...$bindParams);
$dataStmt->execute();
$result = $dataStmt->get_result();

// ======================================================
// 9. BUILD PAYLOAD
// ======================================================
$payload_data = [];

while ($row = $result->fetch_assoc()) {
    $payload_data[] = [
        "id_master_pemeriksaan"     => $row['id_master_pemeriksaan'],
        "nama_pemeriksaan"          => $row['nama_pemeriksaan'],
        "modalitas"                 => $row['modalitas'],
        "pemeriksaan_code"          => $row['pemeriksaan_code'],
        "pemeriksaan_description"   => $row['pemeriksaan_description'],
        "pemeriksaan_sys"           => $row['pemeriksaan_sys'],
        "bodysite_code"             => $row['bodysite_code'],
        "bodysite_description"      => $row['bodysite_description'],
        "bodysite_sys"              => $row['bodysite_sys'],
        "report_code"               => $row['report_code'],
        "report_description"        => $row['report_description'],
        "report_sys"                => $row['report_sys']
    ];
}

// ======================================================
// 10. RESPONSE JSON
// ======================================================
http_response_code(200);
echo json_encode([
    "status"  => "success",
    "message" => "Data Master Pemeriksaan Berhasil Ditemukan",
    "meta" => [
        "keyword"     => $keyword,
        "modalitas"   => $modality,
        "page"        => $page,
        "limit"       => $limit,
        "total_data"  => $totalData,
        "total_pages" => $totalPages
    ],
    "data" => $payload_data
]);
exit;
