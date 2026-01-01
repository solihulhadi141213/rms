<?php
    // ======================================================
    // KONEKSI, SESSION, GLOBAL FUNCTION
    // ======================================================
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";
    include "../../_Config/SettingGeneral.php";

    date_default_timezone_set("Asia/Jakarta");

    // ======================================================
    // RESPONSE HEADER
    // ======================================================
    header('Content-Type: application/json');

    // ======================================================
    // VALIDASI SESSION
    // ======================================================
    if (empty($SessionIdAccess)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Sesi akses telah berakhir. Silakan login ulang.'
        ]);
        exit;
    }

    // ======================================================
    // FUNGSI AMAN AMBIL POST
    // ======================================================
    function getPost($key, $default = '')
    {
        if (!isset($_POST[$key])) {
            return $default;
        }

        $value = $_POST[$key];

        if (is_array($value)) {
            return array_map('trim', $value);
        }

        $value = trim($value);
        $value = str_replace(["\r", "\n"], ' ', $value);
        $value = preg_replace('/\s+/', ' ', $value);

        return $value;
    }

    // ======================================================
    // VALIDASI INPUT WAJIB
    // ======================================================
    $id_radiologi = getPost('id_radiologi');
    $id_question = getPost('id_question');

    if (empty($id_radiologi)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'ID Radiologi tidak boleh kosong.'
        ]);
        exit;
    }

    if (empty($id_question)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'ID Question (Pertanyaan) tidak boleh kosong.'
        ]);
        exit;
    }

    // Field wajib FHIR Procedure
    $requiredFields = [
        'questionnaire',
        'subject_reference',
        'subject_display',
        'encounter_reference',
        'authored',
        'author_reference',
        'item_linkId'
    ];
    // Untuk conclusionCode_coding_code yang ditangkap dari form memiliki format code|display

    $missing = [];
    foreach ($requiredFields as $field) {
        if (empty(getPost($field))) {
            $missing[] = $field;
        }
    }

    if (!empty($missing)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Field wajib belum diisi: ' . implode(', ', $missing)
        ]);
        exit;
    }

    // Memeriksa apakah sebelumnya sudah mempunyai data atau belum
    $QrySebelumnya = mysqli_query($Conn,"SELECT * FROM question_response WHERE id_radiologi='$id_radiologi' AND id_question='$id_question'")or die(mysqli_error($Conn));
    $DataSebelumnya = mysqli_fetch_array($QrySebelumnya);
    if(empty($DataSebelumnya['id_question_response'])){
        $id_question_response= "";
    }else{
        $id_question_response= $DataSebelumnya['id_question_response'];
    }
    

    // ======================================================
    // GENERATE TOKEN SATUSEHAT
    // ======================================================
    $tokenResult = generateTokenSatuSehat($Conn);

    if ($tokenResult['status'] !== 'success') {
        echo json_encode([
            'status'  => 'error',
            'message' => $tokenResult['message']
        ]);
        exit;
    }

    $token = $tokenResult['token'];

    // ======================================================
    // AMBIL KONFIGURASI SATUSEHAT AKTIF
    // ======================================================
    $status_active = 1;
    $stmt = $Conn->prepare("SELECT url_connection_satu_sehat, organization_id FROM connection_satu_sehat WHERE status_connection_satu_sehat = ?
    ");
    $stmt->bind_param("i", $status_active);
    $stmt->execute();
    $result = $stmt->get_result();
    $config = $result->fetch_assoc();
    $stmt->close();

    if (!$config) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Koneksi SATUSEHAT tidak ditemukan.'
        ]);
        exit;
    }

    $organization_id            = $config['organization_id'];
    $url_api                    = rtrim($config['url_connection_satu_sehat'], '/');
    $url_questionnaire_response = $url_api . '/fhir-r4/v1/QuestionnaireResponse';

    // ======================================================
    // SUSUN PAYLOAD QuestionnaireResponse (FHIR R4)
    // ======================================================

    // menyatakan ture or false
    $itemAnswer = getPost('item_answer');

    if ($itemAnswer === 'true' || $itemAnswer === '1') {
        $answer = true;
    } elseif ($itemAnswer === 'false' || $itemAnswer === '0') {
        $answer = false;
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Jawaban tidak valid (boolean)'
        ]);
        exit;
    }

    // Validasi Authored
    $authored = getPost('authored');

    if (!strtotime($authored)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Format authored tidak valid (ISO 8601)'
        ]);
        exit;
    }

    $payload = [
        'resourceType' => 'QuestionnaireResponse',
        'status'       => 'completed',
        'questionnaire'=> 'Questionnaire/' . getPost('questionnaire'),

        'subject' => [
            'reference' => 'Patient/' . getPost('subject_reference'),
            'display'   => getPost('subject_display')
        ],

        'encounter' => [
            'reference' => 'Encounter/' . getPost('encounter_reference')
        ],

        'authored' => $authored,

        'author' => [
            'reference' => 'Practitioner/' . getPost('author_reference')
        ],

        'source' => [
            'reference' => 'Patient/' . getPost('subject_reference')
        ],

        'item' => [
            [
                'linkId' => getPost('item_linkId'),
                'answer' => [
                    [
                        'valueBoolean' => $answer
                    ]
                ]
            ]
        ]
    ];
    // ======================================================
    // ENCODE JSON
    // ======================================================
    $payload_json = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Gagal membuat JSON payload.'
        ]);
        exit;
    }

    // ======================================================
    // KIRIM KE SATUSEHAT
    // ======================================================
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => $url_questionnaire_response,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $payload_json,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
            'Accept: application/json'
        ],
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false
    ]);

    $response = curl_exec($curl);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($curl);
    curl_close($curl);

    // ======================================================
    // HANDLE ERROR CURL
    // ======================================================
    if ($curl_error) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'cURL Error: ' . $curl_error
        ]);
        exit;
    }

    // ======================================================
    // DECODE RESPONSE
    // ======================================================
    $result = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Response bukan JSON valid.',
            'response_raw' => substr($response, 0, 300)
        ]);
        exit;
    }

    // ======================================================
    // VALIDASI RESPONSE SATUSEHAT
    // ======================================================
    if ($http_code !== 201) {
        $msg = 'Gagal mengirim QuestionnaireResponse ke SATUSEHAT';

        if (($result['resourceType'] ?? '') === 'OperationOutcome') {
            $msg = $result['issue'][0]['details']['text']
                ?? $result['issue'][0]['diagnostics']
                ?? $msg;
        }

        echo json_encode([
            'status'  => 'error',
            'message' => $msg,
            'http_code' => $http_code
        ]);
        exit;
    }

    // ======================================================
    // SIMPAN ID QuestionnaireResponse KE DATABASE
    // ======================================================
    $id_questionnaire_response = $result['id'] ?? null;

    if ($id_questionnaire_response) {
        if(empty($id_question_response)){

            //Jika ada id_question_response maka INSERT
            $query = "INSERT INTO question_response (
                id_question,
                id_radiologi,
                id_questionnaire_response,
                answer
            ) VALUES (?, ?, ?, ?)";

            $stmt = $Conn->prepare($query);

            $stmt->bind_param(
                "iiss",
                $id_question,
                $id_radiologi,
                $id_questionnaire_response,
                $answer
            );

            if ($stmt->execute()) {
                $status_proses ="success";

            } else {
                $status_proses = 'Gagal menyimpan data: ' . $stmt->error;
            }

            $stmt->close();
        }else{

            //Jika tidak ada id_question_response maka UPDATE
            $query = "UPDATE question_response SET answer = ? WHERE id_question_response = ?";
            $stmt = $Conn->prepare($query);
            $stmt->bind_param(
                "si",
                $answer,
                $id_question_response
            );

            if ($stmt->execute()) {
                $status_proses = "success";
            } else {
                $status_proses = "Gagal memperbarui data: " . $stmt->error;
            }

            $stmt->close();
        }
       
    }

    // ======================================================
    // RESPONSE
    // ======================================================
    if($status_proses!=="success"){
       echo json_encode([
            'status'  => 'error',
            'message' => $status_proses
        ]);
        exit;
    }else{
        echo json_encode([
            'status'         => 'success',
            'message'        => 'QuestionnaireResponse berhasil dikirim ke SATUSEHAT',
            'id_questionnaire_response' => $id_questionnaire_response,
            'id_radiologi'   => $id_radiologi,
            'resource_url'   => $url_api . '/fhir-r4/v1/QuestionnaireResponse/' . $id_questionnaire_response
        ]);
        exit;
    }
?>