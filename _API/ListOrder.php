<?php
    // SET TIME ZONE
    date_default_timezone_set('UTC');

    // HEADER RESPONSE (CORS + JSON)
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

    // VALIDASI METODE PENGIRIMAN DATA
    $allowedMethods = ['GET'];

    // Ambil method request
    $requestMethod = $_SERVER['REQUEST_METHOD'] ?? '';

    // Jika method tidak diizinkan
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

    // CONNECTION AND FUNCTION
    include "../_Config/Connection.php";
    include "../_Config/GlobalFunction.php";

    // CATCH AUTHORIZATION HEADER (BEARER TOKEN)
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
            "message" => "Authorization Bearer Token tidak ditemukan atau format tidak valid"
        ]);
        exit;
    }

    // Creat Variabel Token
    $token = $matches[1]; 

    // Validation Token
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
            "message" => "Token tidak valid atau sudah kedaluwarsa"
        ]);
        exit;
    }

    // ======================================================
    // MENANGKAP PARAMETER
    // ======================================================
    $limit = !empty($_GET['limit']) ? (int) $_GET['limit'] : 10;
    $page = !empty($_GET['page']) ? (int) $_GET['page'] : 1;
    $short_by = !empty($_GET['short_by']) ? $_GET['short_by'] : 'id_radiologi';
    $order_by = !empty($_GET['order_by']) ? $_GET['order_by'] : 'DESC';
    $keyword_by = !empty($_GET['keyword_by']) ? $_GET['keyword_by'] : '';
    $keyword = !empty($_GET['keyword']) ? $_GET['keyword'] : '';

    // ======================================================
    // MENENTUKAN allow_colom
    // ======================================================
    $allow_colom = [
        "id_radiologi",
        "id_pasien",
        "id_kunjungan",
        "accession_number",
        "nama_pasien",
        "priority",
        "asal_kiriman",
        "alat_pemeriksa",
        "kode_dokter_pengirim",
        "ihs_dokter_pengirim",
        "nama_dokter_pengirim",
        "kode_dokter_penerima",
        "ihs_dokter_penerima",
        "nama_dokter_penerima",
        "radiografer",
        "tujuan",
        "pembayaran",
        "datetime_diminta",
        "status_pemeriksaan"
    ];

    // ======================================================
    // VALIDASI PARAMETER
    // ======================================================
    if ($limit > 100) {
        http_response_code(400);
        echo json_encode([
            "status"  => "error",
            "message" => "Limit Melebihi Batas (Maksimal 100)"
        ]);
        exit;
    }

    if ($order_by !== 'ASC' && $order_by !== 'DESC') {
        http_response_code(400);
        echo json_encode([
            "status"  => "error",
            "message" => "Mode urutan data tidak valid (Hanya boleh ASC dan DESC)"
        ]);
        exit;
    }

    if (!in_array($short_by, $allow_colom)) {
        http_response_code(400);
        echo json_encode([
            "status"  => "error",
            "message" => "Kolom pengurutan data (short_by) tidak valid"
        ]);
        exit;
    }

    if (!empty($keyword_by) && !in_array($keyword_by, $allow_colom)) {
        http_response_code(400);
        echo json_encode([
            "status"  => "error",
            "message" => "Kolom pencarian tidak valid"
        ]);
        exit;
    }

    // ======================================================
    // HITUNG OFFSET PAGINATION
    // ======================================================
    $offset = ($page - 1) * $limit;

    // ======================================================
    // BANGUN KONDISI WHERE
    // ======================================================
    $where = "";
    $params = [];
    $types = "";

    if (!empty($keyword)) {

        // Jika keyword_by kosong → cari di semua allow_colom
        if (empty($keyword_by)) {

            $like_conditions = [];
            foreach ($allow_colom as $col) {
                $like_conditions[] = "$col LIKE ?";
                $params[] = "%" . $keyword . "%";
                $types .= "s";
            }

            $where = "WHERE (" . implode(" OR ", $like_conditions) . ")";

        } else {
            // Jika keyword_by spesifik
            $where = "WHERE $keyword_by LIKE ?";
            $params[] = "%" . $keyword . "%";
            $types .= "s";
        }
    }

    // ======================================================
    // HITUNG TOTAL DATA
    // ======================================================
    $count_sql = "SELECT COUNT(*) AS total FROM radiologi $where";
    $count_stmt = $Conn->prepare($count_sql);

    if (!empty($params)) {
        $count_stmt->bind_param($types, ...$params);
    }

    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    $total_data = $count_result->fetch_assoc()['total'];

    $total_pages = ceil($total_data / $limit);

    // ======================================================
    // AMBIL DATA
    // ======================================================
    $sql = "SELECT * FROM radiologi $where ORDER BY $short_by $order_by LIMIT ? OFFSET ?";
    $stmt = $Conn->prepare($sql);

    if (!empty($params)) {
        $params_with_limit = array_merge($params, [$limit, $offset]);
        $types_with_limit = $types . "ii";
        $stmt->bind_param($types_with_limit, ...$params_with_limit);
    } else {
        $stmt->bind_param("ii", $limit, $offset);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {

        // Decode JSON klinis jika ada
        if (!empty($row['klinis'])) {
            $decoded_klinis = json_decode($row['klinis'], true);
            $row['klinis'] = (json_last_error() === JSON_ERROR_NONE) ? $decoded_klinis : null;
        } else {
            $row['klinis'] = null;
        }

        // Decode JSON permintaan_pemeriksaan jika ada
        if (!empty($row['permintaan_pemeriksaan'])) {
            $decoded_perm = json_decode($row['permintaan_pemeriksaan'], true);
            $row['permintaan_pemeriksaan'] = (json_last_error() === JSON_ERROR_NONE) ? $decoded_perm : null;
        } else {
            $row['permintaan_pemeriksaan'] = null;
        }

        $data[] = $row;
    }

    // ======================================================
    // RESPONSE SUCCESS
    // ======================================================
    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "message" => "Data radiologi berhasil dimuat",
        "pagination" => [
            "total_data"   => (int) $total_data,
            "total_pages"  => (int) $total_pages,
            "current_page" => (int) $page,
            "limit"        => (int) $limit
        ],
        "filter" => [
            "short_by"   => $short_by,
            "order_by"   => $order_by,
            "keyword_by" => $keyword_by,
            "keyword"    => $keyword
        ],
        "data" => $data
    ]);
    exit;
?>
