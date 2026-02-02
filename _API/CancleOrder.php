<?php
    // SET TIME ZONE
    date_default_timezone_set('UTC');

    // HEADER RESPONSE (CORS + JSON)
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: DELETE");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

    // VALIDASI METODE PENGIRIMAN DATA
    $allowedMethods = ['DELETE'];

    // Ambil method request
    $requestMethod = $_SERVER['REQUEST_METHOD'] ?? '';

    // Jika method tidak diizinkan
    if (!in_array($requestMethod, $allowedMethods)) {

        // Set HTTP Response Code: 405 Method Not Allowed
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
    // JIKA MENANGKAP id_radiologi
    // ======================================================
    if (!empty($_GET['id_radiologi'])) {

        $id_radiologi = validateAndSanitizeInput($_GET['id_radiologi']);

        // Cek Status Permintaan Radiologi
        $status_pemeriksaan = GetDetailData($Conn, 'radiologi', 'id_radiologi', $id_radiologi, 'status_pemeriksaan');
        
        // Apabila tidak ditemukan
        if (empty($status_pemeriksaan)) {
            http_response_code(404);
            echo json_encode([
                "status"  => "error",
                "message" => "Data Radiologi tidak ditemukan"
            ]);
            exit;
        }

        // Apabila Status Pemeriksaan Sudah Diproses
        if ($status_pemeriksaan !== "Diminta") {
            http_response_code(403);
            echo json_encode([
                "status"  => "error",
                "message" => "Permintaan pemeriksaan sudah diproses petugas dan tidak dapat dibatalkan"
            ]);
            exit;
        }

        // Hapus Data
        $QryDelete = $Conn->prepare("DELETE FROM radiologi WHERE id_radiologi = ?");
        if (!$QryDelete) {
            http_response_code(500);
            echo json_encode([
                "status"  => "error",
                "message" => "Terjadi kesalahan saat mempersiapkan proses penghapusan data"
            ]);
            exit;
        }

        $QryDelete->bind_param("i", $id_radiologi);

        if (!$QryDelete->execute()) {
            http_response_code(500);
            echo json_encode([
                "status"  => "error",
                "message" => "Terjadi kesalahan saat menghapus data radiologi"
            ]);
            $QryDelete->close();
            exit;
        }

        $QryDelete->close();

        // Response Success
        http_response_code(200);
        echo json_encode([
            "status"  => "success",
            "message" => "Permintaan pemeriksaan radiologi berhasil dibatalkan"
        ]);
        exit;
    }

    // ======================================================
    // JIKA MENANGKAP accession_number
    // ======================================================
    if (!empty($_GET['accession_number'])) {

        $accession_number = validateAndSanitizeInput($_GET['accession_number']);

        // Cek Status Permintaan Radiologi
        $status_pemeriksaan = GetDetailData($Conn, 'radiologi', 'accession_number', $accession_number, 'status_pemeriksaan');

        // Apabila tidak ditemukan
        if (empty($status_pemeriksaan)) {
            http_response_code(404);
            echo json_encode([
                "status"  => "error",
                "message" => "Data Radiologi tidak ditemukan"
            ]);
            exit;
        }

        // Apabila Status Pemeriksaan Sudah Diproses
        if ($status_pemeriksaan !== "Diminta") {
            http_response_code(403);
            echo json_encode([
                "status"  => "error",
                "message" => "Permintaan pemeriksaan sudah diproses petugas dan tidak dapat dibatalkan"
            ]);
            exit;
        }

        // Hapus Data
        $QryDelete = $Conn->prepare("DELETE FROM radiologi WHERE accession_number = ?");
        if (!$QryDelete) {
            http_response_code(500);
            echo json_encode([
                "status"  => "error",
                "message" => "Terjadi kesalahan saat mempersiapkan proses penghapusan data"
            ]);
            exit;
        }

        $QryDelete->bind_param("s", $accession_number);

        if (!$QryDelete->execute()) {
            http_response_code(500);
            echo json_encode([
                "status"  => "error",
                "message" => "Terjadi kesalahan saat menghapus data radiologi"
            ]);
            $QryDelete->close();
            exit;
        }

        $QryDelete->close();

        // Response Success
        http_response_code(200);
        echo json_encode([
            "status"  => "success",
            "message" => "Permintaan pemeriksaan radiologi berhasil dibatalkan"
        ]);
        exit;
    }

    // ======================================================
    // JIKA PARAMETER UTAMA TIDAK DIKIRIM
    // ======================================================
    http_response_code(400);
    echo json_encode([
        "status"  => "error",
        "message" => "Parameter utama tidak ditemukan. Sertakan id_radiologi atau accession_number"
    ]);
    exit;
?>
