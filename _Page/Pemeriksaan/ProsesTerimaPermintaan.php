<?php
    // koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";
    include "../../_Config/SettingGeneral.php";

    // Zona Waktu
    date_default_timezone_set("Asia/Jakarta");

    // Response default
    $response = [
        'status'  => 'error',
        'message' => 'Terjadi kesalahan sistem'
    ];

    // =====================================================================
    // VALIDASI SESSION
    // =====================================================================
    if (empty($SessionIdAccess)) {
        $response['message'] = 'Sesi akses telah berakhir. Silakan login ulang.';
        echo json_encode($response);
        exit;
    }

    // =====================================================================
    // VALIDASI INPUT
    // =====================================================================
    $id_radiologi = (int) ($_POST['id_radiologi'] ?? 0);
    if ($id_radiologi <= 0) {
        $response['message'] = 'ID Radiologi tidak valid.';
        echo json_encode($response);
        exit;
    }
    $id_kunjungan          = validateAndSanitizeInput($_POST['id_kunjungan'] ?? '');
    $id_encounter          = validateAndSanitizeInput($_POST['id_encounter'] ?? '');
    $accession_number      = validateAndSanitizeInput($_POST['accession_number'] ?? '');
    $id_ihs                = validateAndSanitizeInput($_POST['id_ihs'] ?? '');
    $nama_pasien           = validateAndSanitizeInput($_POST['nama_pasien'] ?? '');
    $id_pasien             = validateAndSanitizeInput($_POST['id_pasien'] ?? '');
    $modalitas             = validateAndSanitizeInput($_POST['modalitas'] ?? '');
    $tanggal_lahir         = validateAndSanitizeInput($_POST['tanggal_lahir'] ?? '');
    $gender                = validateAndSanitizeInput($_POST['gender'] ?? '');
    $gender_code           = validateAndSanitizeInput($_POST['gender_code'] ?? '');
    $status                = validateAndSanitizeInput($_POST['status'] ?? '');
    $radiografer           = validateAndSanitizeInput($_POST['radiografer'] ?? '');
    $alasan_pembatalan     = validateAndSanitizeInput($_POST['alasan_pembatalan'] ?? '');
    $tanggal_dikerjakan    = validateAndSanitizeInput($_POST['tanggal_dikerjakan'] ?? '');
    $jam_dikerjakan        = validateAndSanitizeInput($_POST['jam_dikerjakan'] ?? '');
    $dokter_penerima       = validateAndSanitizeInput($_POST['dokter_penerima'] ?? '');
    $kirim_service_request = validateAndSanitizeInput($_POST['kirim_service_request'] ?? '');
    $kirim_procedure       = validateAndSanitizeInput($_POST['kirim_procedure'] ?? '');
    $kirim_ke_senalogi     = validateAndSanitizeInput($_POST['kirim_ke_senalogi'] ?? '');
    $kirim_orthanc         = validateAndSanitizeInput($_POST['kirim_orthanc'] ?? '');

    if (empty($id_radiologi)) {
        $response['message'] = 'ID Permintaan Pemeriksaan tidak boleh kosong.';
        echo json_encode($response);
        exit;
    }

    if (empty($status)) {
        $response['message'] = 'Status Permintaan Pemeriksaan tidak boleh kosong.';
        echo json_encode($response);
        exit;
    }

    if (empty($radiografer)) {
        $response['message'] = 'Nama Radiografer tidak boleh kosong.';
        echo json_encode($response);
        exit;
    }

    // =====================================================================
    // VALIDASI INPUT BERDASARKAN STATUS
    // =====================================================================

    if($status=="Batal"){
        if(empty($alasan_pembatalan)){
             $response['message'] = 'Alasan pembatalan tidak boleh kosong.';
            echo json_encode($response);
            exit;
        }
    }else{
        if(empty($tanggal_dikerjakan)||empty($jam_dikerjakan)){
             $response['message'] = 'Waktu pengerjaan tidak boleh kosong.';
            echo json_encode($response);
            exit;
        }

        if(empty($dokter_penerima)){
             $response['message'] = 'Dokter penerima permintaan tidak boleh kosong.';
            echo json_encode($response);
            exit;
        }
    }

    // =====================================================================
    // PEMBATALAN / PENOLAKAN PERMINTAAN
    // =====================================================================
    if($status=="Batal"){
        $stmt = $Conn->prepare("UPDATE radiologi SET
                radiografer        = ?,
                status_pemeriksaan = ?,
                alasan_pembatalan  = ?
            WHERE id_radiologi = ?
        ");

        $stmt->bind_param(
            "sssi",
            $radiografer,
            $status,
            $alasan_pembatalan,
            $id_radiologi
        );

        if (!$stmt) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Gagal menyiapkan query database'
            ]);
            exit;
        }

        if ($stmt->execute()) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Data Radiologi Berhasil Diperbaharui'
            ]);
            $stmt->close();
            exit;
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Terjadi Kesalahan Pada Saat Update Pembatalan Radiologi'
            ]);
            $stmt->close();
            exit;
        }
    }else{

        // Buka Beberapa Detail Permintaan
        $priority               = GetDetailData($Conn,'radiologi','id_radiologi',$id_radiologi,'priority');
        $klinis                 = GetDetailData($Conn,'radiologi','id_radiologi',$id_radiologi,'klinis');
        $permintaan_pemeriksaan = GetDetailData($Conn,'radiologi','id_radiologi',$id_radiologi,'permintaan_pemeriksaan');
        $pesan                  = GetDetailData($Conn,'radiologi','id_radiologi',$id_radiologi,'pesan');
        $ihs_dokter_pengirim    = GetDetailData($Conn,'radiologi','id_radiologi',$id_radiologi,'ihs_dokter_pengirim');
        $nama_dokter_pengirim   = GetDetailData($Conn,'radiologi','id_radiologi',$id_radiologi,'nama_dokter_pengirim');
        $kode_dokter_pengirim   = GetDetailData($Conn,'radiologi','id_radiologi',$id_radiologi,'kode_dokter_pengirim');
        
        // Buat Klinis Dalam Bentuk Arry
        $snomed_display ="";
        $klinis_array = json_decode($klinis, true);
        $reasonCode = [];
        foreach ($klinis_array as $klinis) {
            // Validasi minimal
            if (empty($klinis['snomed_code']) || empty($klinis['snomed_display'])) {
                continue; // skip data rusak
            }
            $snomed_display = $klinis['snomed_display'];
            $reasonCode[] = [
                "coding" => [[
                    "system"  => "http://snomed.info/sct",
                    "code"    => $klinis['snomed_code'],
                    "display" => $klinis['snomed_display']
                ]],
                "text" => $klinis['nama_klinis'] // human readable
            ];
        }
        // Buka pemeriksaan
        $modalitas               = "";
        $pemeriksaan_sys         = "";
        $pemeriksaan_code        = "";
        $pemeriksaan_description = "";
        $bodysite_sys            = "";
        $bodysite_code           = "";
        $bodysite_description    = "";

        $permintaan_pemeriksaan_arry = json_decode($permintaan_pemeriksaan, true);
        foreach($permintaan_pemeriksaan_arry as $permintaan_pemeriksaan_list){
            $modalitas               = $permintaan_pemeriksaan_list['modalitas'];
            $pemeriksaan_sys         = $permintaan_pemeriksaan_list['pemeriksaan_sys'];
            $pemeriksaan_code        = $permintaan_pemeriksaan_list['pemeriksaan_code'];
            $pemeriksaan_description = $permintaan_pemeriksaan_list['pemeriksaan_description'];
            $bodysite_sys            = $permintaan_pemeriksaan_list['bodysite_sys'];
            $bodysite_code           = $permintaan_pemeriksaan_list['bodysite_code'];
            $bodysite_description    = $permintaan_pemeriksaan_list['bodysite_description'];
            
        }

        // 1. Dapatkan Koneksi API SIMRS
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
        // 2. Call API get_dokter untuk mendapatkan detail dokter
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
        curl_close($curl2);
        $data_dokter = json_decode($response_dokter, true);
        
        // Inisialisasi Data dokter
        $kode_dokter = '';
        $ihs_dokter  = '';
        $nama_dokter = '';
        
        // Cari Dokter Yang Dipilih
        if(!empty($data_dokter['response']['code']) && $data_dokter['response']['code'] == 200){
            $list_dokter = $data_dokter['metadata']['list_dokter']
            ?? $data_dokter['response']['list_dokter']
            ?? [];
            foreach($list_dokter as $dokter){
                if($dokter['id_dokter'] == $dokter_penerima){
                    $kode_dokter = $dokter['kode'] ?? '';
                    $ihs_dokter = $dokter['id_ihs_practitioner'] ?? '';
                    $nama_dokter = $dokter['nama'] ?? '';
                    break;
                }
            }
        }

        if(empty($kode_dokter)){
            echo json_encode([
                'status' => 'error',
                'message' => 'ID Dokter Penerima Tidak Valid'
            ]);
            exit;
        }

        // 3. Membuat Keterangan 'datetime_dikerjakan'
        $dt = DateTime::createFromFormat('Y-m-d H:i', "$tanggal_dikerjakan $jam_dikerjakan");
        if (!$dt) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Format tanggal atau jam tidak valid'
            ]);
            exit;
        }
        $datetime_dikerjakan = $dt->format('Y-m-d H:i:s');

        // ISO 8601
        $timezone           = new DateTimeZone('Asia/Jakarta');
        $now                = new DateTime('now', $timezone);
        $occurrenceDateTime = $now->format('c');
        $authoredOn         = $now->format('c');

        // =========================================================================
        // KIRIM SERVICE REQUEST
        // =========================================================================
        $id_service_request = "";
        if(!empty($kirim_service_request)){

            // Token Satu Sehat
            $token_satu_sehat = generateTokenSatuSehat($Conn);
            if ($token_satu_sehat['status'] !== 'success') {
                echo json_encode([
                    'status'  => 'error',
                    'message' => $token_satu_sehat['message']
                ]);
                exit;
            }
            $token_satu_sehat = $token_satu_sehat['token'];

            // Buka Pengaturan Satu Sehat
            $status_connection_satu_sehat = 1;
            $organization_id              = GetDetailData($Conn,'connection_satu_sehat','status_connection_satu_sehat',$status_connection_satu_sehat,'organization_id');
            $url_connection_satu_sehat    = GetDetailData($Conn,'connection_satu_sehat','status_connection_satu_sehat',$status_connection_satu_sehat,'url_connection_satu_sehat');
            $url_api                      = rtrim($url_connection_satu_sehat, '/');
            $url_service_request          = $url_api . "/fhir-r4/v1/ServiceRequest";

            // Membuat Payload  Service Request
            $payload_service_request = [
                "resourceType" => "ServiceRequest",
                "status"       => "active",
                "intent"       => "order",
                "priority"     => $priority,
                
                "category" => [[
                    "coding" => [[
                        "system"  => "http://snomed.info/sct",
                        "code"    => "363679005",
                        "display" => "Imaging"
                    ]]
                ]],
                
                "code" => [
                    "coding" => [[
                        "system"  => $pemeriksaan_sys,
                        "code"    => $pemeriksaan_code,
                        "display" => $pemeriksaan_description
                    ]],
                    "text" => $pesan
                ],
                
                "subject" => [
                    "reference" => "Patient/$id_ihs"
                ],
                
                "encounter" => [
                    "reference" => "Encounter/$id_encounter",
                    "display" => "Permintaan Radiologi"
                ],
                
                "occurrenceDateTime" => $occurrenceDateTime,
                "authoredOn"         => $authoredOn,
                
                "requester" => [
                    "reference" => "Practitioner/$ihs_dokter_pengirim",
                    "display"   => $nama_dokter_pengirim
                ],
                
                "performer" => [[
                    "reference" => "Practitioner/$ihs_dokter",
                    "display"   => $nama_dokter
                ]],
                "identifier" => [[
                    "system" => "http://sys-ids.kemkes.go.id/acsn/$organization_id",
                    "value"  => $accession_number
                ]],
                "reasonCode" => $reasonCode,
                
            ];

            // Encode Payload Service Request
            $payload_service_request_json = json_encode($payload_service_request, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

            // Mulai Curl Service Request
            $curl_service_request = curl_init();
            curl_setopt_array($curl_service_request, [
                CURLOPT_URL => $url_service_request,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $payload_service_request_json, // LANGSUNG pakai JSON string, TANPA concat
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $token_satu_sehat,
                    'Content-Type: application/json',
                    'Accept: application/json',
                    'Content-Length: ' . strlen($payload_service_request_json)
                ],
                // DEV ONLY
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false
            ]);

            $response_service_request   = curl_exec($curl_service_request);
            $http_code_service_request  = curl_getinfo($curl_service_request, CURLINFO_HTTP_CODE);
            $curl_error_service_request = curl_error($curl_service_request);
            curl_close($curl_service_request);

            // Validasi Response CURL Untuk Service Request
            if ($curl_error_service_request) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Terjadi kesalahan pada saat mengirim service request. <br>CURL Error: ' . $curl_error_service_request,
                    'http_code' => $http_code_service_request
                ]);
                exit;
            }

            if ($response_service_request === false) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Terjadi kesalahan pada saat mengirim service request. <br> Empty response from server',
                    'http_code' => $http_code_service_request
                ]);
                exit;
            }

            // Decode Response Service Request
            $result_service_request = json_decode($response_service_request, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                // Jika bukan JSON, mungkin HTML error page
                $response_preview = substr($response_service_request, 0, 500);
                
                echo json_encode([
                    'status'  => 'error',
                    'message' => 'Invalid JSON response from server. Response preview: ' . $response_preview,
                    'http_code' => $http_code_service_request,
                    'response_raw' => $response_preview
                ]);
                exit;
            }

            // Jika Response Service Request Bermasalah
            if ($http_code_service_request !== 201) {
                
            // Cek jika response adalah OperationOutcome (error dari SatuSehat)
                if (isset($result_service_request['resourceType']) && $result_service_request['resourceType'] === 'OperationOutcome') {
                    $error_message = 'Error from SATUSEHAT API Service Request: ';
                    
                    if (isset($result_service_request['issue'][0]['details']['text'])) {
                        $error_message .= $result_service_request['issue'][0]['details']['text'];
                    } elseif (isset($result_service_request['issue'][0]['diagnostics'])) {
                        $error_message .= $result_service_request['issue'][0]['diagnostics'];
                    } else {
                        $error_message .= 'Unknown error';
                    }
                    
                    echo json_encode([
                        'status'  => 'error',
                        'message' => $error_message,
                        'http_code' => $http_code_service_request,
                        'detail'  => $result_service_request
                    ]);
                } else {
                    echo json_encode([
                        'status'  => 'error',
                        'message' => 'Failed to send ServiceRequest. HTTP Code: ' . $http_code_service_request,
                        'http_code' => $http_code_service_request,
                        'response' => $result_service_request
                    ]);
                }
                exit;
            }

            // Jika ID Service Request Tidak Ada
            if (empty($result_service_request['id'])) {
                echo json_encode([
                    'status'  => 'error',
                    'message' => 'Response does not contain ServiceRequest ID',
                    'http_code' => $http_code_service_request,
                    'response' => $result_service_request
                ]);
                exit;
            }

            // Jika ada
            $id_service_request = $result_service_request['id'];
        }

        // =========================================================================
        // KIRIM PROCEDURE
        // =========================================================================
        $id_procedure = "";
        if(!empty($kirim_procedure)){

            // Persyaratan Pengiriman Procedure Adalah harus Sudah Service Request
            if(empty($id_service_request)){
                echo json_encode([
                    'status'  => 'error',
                    'message' => "Pengiriman Procedure Harus Disertai Service Request"
                ]);
                exit;
            }

            // Menentukan URL Procedure 
            $url_procedure = $url_api . "/fhir-r4/v1/Procedure";

            // Membuat Payload Procedure
            $payload_procedure = [
                "resourceType" => "Procedure",
                "status"       => "completed",
                "category" => [
                    "coding" => [[
                        "system"  => "http://snomed.info/sct",
                        "code"    => "363679005",
                        "display" => "Imaging"
                    ]]
                ],
                "code" => [
                    "coding" => [[
                        "system"  => $pemeriksaan_sys,
                        "code"    => $pemeriksaan_code,
                        "display" => $pemeriksaan_description
                    ]],
                    "text" => $pesan
                ],
                "subject" => [
                    "reference" => "Patient/$id_ihs"
                ],

                "encounter" => [
                    "reference" => "Encounter/$id_encounter"
                ],

                "performedDateTime" => $occurrenceDateTime,

                "basedOn" => [[
                    "reference" => "ServiceRequest/$id_service_request"
                ]],
                "performer" => [[
                    "actor" => [
                        "reference" => "Practitioner/$ihs_dokter",
                        "display"   => $nama_dokter
                    ]
                ]]
            ];

            // Encode Payload Procedure
            $payload_procedure_json = json_encode($payload_procedure, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

            // Mulai Curl Service Request
            $curl_procedure = curl_init();
            curl_setopt_array($curl_procedure, [
                CURLOPT_URL => $url_procedure,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $payload_procedure_json, // LANGSUNG pakai JSON string, TANPA concat
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $token_satu_sehat,
                    'Content-Type: application/json',
                    'Accept: application/json',
                    'Content-Length: ' . strlen($payload_procedure_json)
                ],
                // DEV ONLY
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false
            ]);

            $response_procedure   = curl_exec($curl_procedure);
            $http_code_procedure  = curl_getinfo($curl_procedure, CURLINFO_HTTP_CODE);
            $curl_error_Procedure = curl_error($curl_procedure);
            curl_close($curl_procedure);

            // Validasi Response CURL Untuk Procedure
            if ($curl_error_Procedure) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Terjadi kesalahan pada saat mengirim service request. <br>CURL Error: ' . $curl_error_Procedure,
                    'http_code' => $http_code_procedure
                ]);
                exit;
            }

            if ($response_procedure === false) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Terjadi kesalahan pada saat mengirim Procedure. <br> Empty response from server',
                    'http_code' => $http_code_procedure
                ]);
                exit;
            }

            // Decode Response Procedure
            $result_procedure = json_decode($response_procedure, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                // Jika bukan JSON, mungkin HTML error page
                $response_preview = substr($response_procedure, 0, 500);
                
                echo json_encode([
                    'status'  => 'error',
                    'message' => 'Invalid JSON response from server. Response preview: ' . $response_preview,
                    'http_code' => $http_code_procedure,
                    'response_raw' => $response_preview
                ]);
                exit;
            }

            // Jika Response Procedure Bermasalah
            if ($http_code_procedure !== 201) {
                
            // Cek jika response adalah OperationOutcome (error dari SatuSehat)
                if (isset($result_procedure['resourceType']) && $result_procedure['resourceType'] === 'OperationOutcome') {
                    $error_message = 'Error from SATUSEHAT API Procedure: ';
                    
                    if (isset($result_procedure['issue'][0]['details']['text'])) {
                        $error_message .= $result_procedure['issue'][0]['details']['text'];
                    } elseif (isset($result_procedure['issue'][0]['diagnostics'])) {
                        $error_message .= $result_procedure['issue'][0]['diagnostics'];
                    } else {
                        $error_message .= 'Unknown error';
                    }
                    
                    echo json_encode([
                        'status'  => 'error',
                        'message' => $error_message,
                        'http_code' => $http_code_procedure,
                        'detail'  => $result_procedure
                    ]);
                } else {
                    echo json_encode([
                        'status'  => 'error',
                        'message' => 'Failed to send Procedure. HTTP Code: ' . $http_code_procedure,
                        'http_code' => $http_code_procedure,
                        'response' => $result_procedure
                    ]);
                }
                exit;
            }

            // Jika ID Procedure Tidak Ada
            if (empty($result_procedure['id'])) {
                echo json_encode([
                    'status'  => 'error',
                    'message' => 'Response does not contain Procedure ID',
                    'http_code' => $http_code_procedure,
                    'response' => $result_procedure
                ]);
                exit;
            }

            // Jika ada
            $id_procedure = $result_procedure['id'];
        }
        
        // =========================================================================
        // KIRIM PACS (SENALOGY)
        // =========================================================================
        $pacs = 0;
        if(!empty($kirim_ke_senalogi)){

            // Buat Token PACS
            $TokenPacsResult = generateTokenPacs($Conn);
            if ($TokenPacsResult['status'] !== 'success') {
                echo json_encode([
                    'status'  => 'error',
                    'message' => $TokenPacsResult['message']
                ]);
                exit;
            }
            $TokenPacs = $TokenPacsResult['token'];

            // Buka Pengaturan PACS
            $status_connection_pacs   = 1;
            $url_connection_pacs      = GetDetailData($Conn,'connection_pacs','status_connection_pacs',$status_connection_pacs,'url_connection_pacs');
            $url_connection_pacs      = rtrim($url_connection_pacs, '/');
            $url_connection_pacs_full = $url_connection_pacs . '/api/dicom/patient-worklist';

            // Susun Payload PACS
            $payload_pacs = [
                "PatientName"           => $nama_pasien,
                "PatientID"             => $id_pasien,
                "PatientBirthDate"      => $tanggal_lahir,
                "PatientSex"            => $gender,
                "ReferringDoctor"       => $nama_dokter_pengirim,
                "SupportingDoctor"      => $nama_dokter,
                "ReferringDoctorID"     => $kode_dokter_pengirim,
                "SupportingDoctorID"    => $kode_dokter,
                "RegistrationDate"      => $authoredOn,
                "RegistrationID"        => $id_kunjungan,
                "InstitutionBranchID"   => $company_code,
                "InstitutionBranchName" => $company_name,
                "IHSPatientNumber"      => $id_ihs,
                "IHSSupportingDoctor"   => $ihs_dokter,
                "EncounterUUID"         => $id_encounter,
                "ServiceRequestUUID"    => $id_service_request,
                "ScheduledProcedure" => [[
                    "ProcedureID"              => $id_procedure,
                    "AccessionNumber"          => $accession_number,
                    "RequestedProcedureName"   => $pemeriksaan_description,
                    "RequestedProcedureCode"   => $pemeriksaan_code,
                    "RequestedSystemProcedure" => $pemeriksaan_sys,
                    "Modality"                 => $modalitas,
                    "Clinical"                 => $snomed_display
                ]],
                "ScheduledProcedureStepSequence"    => "",
            ];

            // Encode JSON payload PACS
            $payload_json_pacs = json_encode($payload_pacs, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

            // Mulai CULR PACS
            $curl_pacs = curl_init();
            curl_setopt_array($curl_pacs, [
                CURLOPT_URL => $url_connection_pacs_full,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $payload_json_pacs,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $TokenPacs,
                    'Content-Type: application/json',
                    'Accept: application/json'
                ],
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false
            ]);

            $response_pacs = curl_exec($curl_pacs);
            $http_code_pacs = curl_getinfo($curl_pacs, CURLINFO_HTTP_CODE);
            $curl_pacs_error = curl_error($curl_pacs);
            curl_close($curl_pacs);

            // Handdle Error Curl PACS
            if ($curl_pacs_error) {
                echo json_encode([
                    'status'  => 'error',
                    'message' => 'CURL PACS Error: ' . $curl_pacs_error
                ]);
                exit;
            }

            // Decode Response PACS
            $result_pacs = json_decode($response_pacs, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                echo json_encode([
                    'status'  => 'error',
                    'message' => 'Response PACS bukan JSON valid.',
                    'response_raw' => substr($response_pacs, 0, 300)
                ]);
                exit;
            }

            // Validasi Response
            if ($http_code_pacs !== 201) {
                $msg_pacs = 'Gagal mengirim Order Ke PACS <br>Response : <code>'.$response_pacs.'</code> <br>Payload : <code>'.$payload_json_pacs.'</code>';

                echo json_encode([
                    'status'  => 'error',
                    'message' => $msg_pacs,
                    'http_code' => $http_code_pacs
                ]);
                exit;
            }

            $pacs = 1;

        }

        // =========================================================================
        // KIRIM WORKLIST KE ORTHANC (STABIL & ANTI DUPLIKAT)
        // =========================================================================

        $orthanc = 0;

        if (!empty($kirim_orthanc)) {

            // =========================================================================
            // KONFIGURASI ORTHANC
            // =========================================================================
            $status_connection_orthanc = 1;

            $base_url_orthanc = GetDetailData(
                $Conn,
                'connection_orthanc',
                'status_connection_orthanc',
                $status_connection_orthanc,
                'url_connection_orthanc'
            );

            $base_url_orthanc = rtrim($base_url_orthanc, '/');

            if (empty($base_url_orthanc)) {

                echo json_encode([
                    'status'  => 'error',
                    'message' => 'Koneksi Orthanc belum diatur'
                ]);

                exit;
            }

            $username_connection_orthanc = GetDetailData(
                $Conn,
                'connection_orthanc',
                'status_connection_orthanc',
                $status_connection_orthanc,
                'username_connection_orthanc'
            );

            $password_connection_orthanc = GetDetailData(
                $Conn,
                'connection_orthanc',
                'status_connection_orthanc',
                $status_connection_orthanc,
                'password_connection_orthanc'
            );

            // =========================================================================
            // VALIDASI ACCESSION NUMBER
            // =========================================================================

            if (empty($accession_number)) {

                echo json_encode([
                    'status'  => 'error',
                    'message' => 'Accession Number tidak boleh kosong'
                ]);

                exit;
            }

            // =========================================================================
            // FORMAT DICOM
            // =========================================================================

            $nama_pasien_dicom = formatPatientNameDICOM($nama_pasien);

            $dokter_pengirim_dicom = formatPatientNameDICOM(
                $nama_dokter_pengirim
            );

            $dokter_penerima_dicom = formatPatientNameDICOM(
                $nama_dokter
            );

            // =========================================================================
            // DATETIME STABIL (PENTING UNTUK ANTI DUPLIKAT)
            // =========================================================================
            /*
            |--------------------------------------------------------------------------
            | JANGAN gunakan:
            | date('His')
            |
            | karena akan membuat modality membaca worklist baru
            |--------------------------------------------------------------------------
            */

            $dt_worklist = new DateTime($datetime_dikerjakan);

            $ScheduledProcedureStepStartDate = $dt_worklist->format('Ymd');

            $ScheduledProcedureStepStartTime = $dt_worklist->format('His');

            // =========================================================================
            // STUDY INSTANCE UID STABIL
            // =========================================================================
            /*
            |--------------------------------------------------------------------------
            | Gunakan UID stabil berdasarkan accession
            |--------------------------------------------------------------------------
            */

            function generateStableStudyUID($accession)
            {
                $hash = preg_replace('/[^0-9]/', '', crc32($accession));

                return '1.2.826.0.1.3680043.2.1125.' . $hash;
            }

            $StudyInstanceUID = generateStableStudyUID(
                $accession_number
            );

            // =========================================================================
            // URL WORKLIST
            // =========================================================================

            $url_worklist = $base_url_orthanc .
                            '/worklists/' .
                            rawurlencode($accession_number);

            // =========================================================================
            // CEK WORKLIST EXISTING
            // =========================================================================

            $curl_check = curl_init();

            curl_setopt_array($curl_check, [
                CURLOPT_URL => $url_worklist,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
                CURLOPT_USERPWD => $username_connection_orthanc . ':' . $password_connection_orthanc,
                CURLOPT_TIMEOUT => 15,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false
            ]);

            $response_check = curl_exec($curl_check);

            $http_check = curl_getinfo(
                $curl_check,
                CURLINFO_HTTP_CODE
            );

            curl_close($curl_check);

            // =========================================================================
            // HAPUS WORKLIST LAMA
            // =========================================================================
            /*
            |--------------------------------------------------------------------------
            | Penting agar modality tidak membaca duplicate item
            |--------------------------------------------------------------------------
            */

            if ($http_check == 200) {

                $curl_delete = curl_init();

                curl_setopt_array($curl_delete, [
                    CURLOPT_URL => $url_worklist,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_CUSTOMREQUEST => 'DELETE',
                    CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
                    CURLOPT_USERPWD => $username_connection_orthanc . ':' . $password_connection_orthanc,
                    CURLOPT_TIMEOUT => 15,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false
                ]);

                $response_delete = curl_exec($curl_delete);

                $http_delete = curl_getinfo(
                    $curl_delete,
                    CURLINFO_HTTP_CODE
                );

                $curl_delete_error = curl_error($curl_delete);

                curl_close($curl_delete);

                if ($curl_delete_error) {

                    echo json_encode([
                        'status'  => 'error',
                        'message' => 'Gagal menghapus worklist lama: ' .
                                    $curl_delete_error
                    ]);

                    exit;
                }

                /*
                |--------------------------------------------------------------------------
                | Orthanc:
                | 200 = deleted
                | 404 = not found
                |--------------------------------------------------------------------------
                */

                if ($http_delete != 200 && $http_delete != 404) {

                    echo json_encode([
                        'status'  => 'error',
                        'message' => 'Gagal menghapus worklist lama',
                        'http_code' => $http_delete
                    ]);

                    exit;
                }
            }

            // =========================================================================
            // PAYLOAD WORKLIST ORTHANC
            // =========================================================================

            $payload_orthanc = [
                "Tags" => [

                    // ================================================================
                    // PATIENT
                    // ================================================================

                    "PatientName"       => $nama_pasien_dicom,
                    "PatientID"         => $id_pasien,
                    "PatientBirthDate"  => $tanggal_lahir,
                    "PatientSex"        => $gender_code,

                    "IssuerOfPatientID" => $company_name,

                    // ================================================================
                    // STUDY
                    // ================================================================

                    "AccessionNumber"     => $accession_number,
                    "StudyInstanceUID"    => $StudyInstanceUID,
                    "RequestedProcedureID"=> $accession_number,

                    "StudyDescription"    => $pemeriksaan_description,

                    "ReferringPhysicianName" =>
                        $dokter_pengirim_dicom,

                    // ================================================================
                    // REQUESTED PROCEDURE
                    // ================================================================

                    "RequestedProcedureDescription" =>
                        $pemeriksaan_description,

                    // ================================================================
                    // SPS
                    // ================================================================

                    "ScheduledProcedureStepSequence" => [[

                        "ScheduledStationAETitle" =>
                            "USG01",

                        "Modality" =>
                            $modalitas,

                        "ScheduledProcedureStepStartDate" =>
                            $ScheduledProcedureStepStartDate,

                        "ScheduledProcedureStepStartTime" =>
                            $ScheduledProcedureStepStartTime,

                        "ScheduledPerformingPhysicianName" =>
                            $dokter_penerima_dicom,

                        "ScheduledProcedureStepDescription" =>
                            $pemeriksaan_description
                    ]]
                ]
            ];

            // =========================================================================
            // ENCODE JSON
            // =========================================================================

            $payload_json_orthanc = json_encode(
                $payload_orthanc,
                JSON_UNESCAPED_SLASHES |
                JSON_UNESCAPED_UNICODE
            );

            if (!$payload_json_orthanc) {

                echo json_encode([
                    'status'  => 'error',
                    'message' => 'Gagal encode JSON worklist'
                ]);

                exit;
            }

            // =========================================================================
            // CURL PUT WORKLIST
            // =========================================================================

            $curl_orthanc = curl_init();

            curl_setopt_array($curl_orthanc, [

                CURLOPT_URL => $url_worklist,

                CURLOPT_RETURNTRANSFER => true,

                CURLOPT_CUSTOMREQUEST => 'PUT',

                CURLOPT_POSTFIELDS => $payload_json_orthanc,

                CURLOPT_HTTPAUTH => CURLAUTH_BASIC,

                CURLOPT_USERPWD =>
                    $username_connection_orthanc .
                    ':' .
                    $password_connection_orthanc,

                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Accept: application/json'
                ],

                CURLOPT_TIMEOUT => 30,

                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false
            ]);

            $response_orthanc = curl_exec($curl_orthanc);

            $http_code_orthanc = curl_getinfo(
                $curl_orthanc,
                CURLINFO_HTTP_CODE
            );

            $curl_orthanc_error = curl_error(
                $curl_orthanc
            );

            curl_close($curl_orthanc);

            // =========================================================================
            // HANDLE CURL ERROR
            // =========================================================================

            if ($curl_orthanc_error) {

                echo json_encode([
                    'status'  => 'error',
                    'message' => 'CURL Orthanc Error: ' .
                                $curl_orthanc_error
                ]);

                exit;
            }

            // =========================================================================
            // VALIDASI RESPONSE
            // =========================================================================

            /*
            |--------------------------------------------------------------------------
            | Orthanc MWL:
            | 200 = updated
            |--------------------------------------------------------------------------
            */

            if ($http_code_orthanc != 200) {

                echo json_encode([
                    'status'  => 'error',
                    'message' =>
                        'Gagal mengirim worklist ke Orthanc',

                    'http_code' =>
                        $http_code_orthanc,

                    'response_raw' =>
                        substr($response_orthanc, 0, 500),

                    'payload' =>
                        $payload_orthanc
                ]);

                exit;
            }

            // =========================================================================
            // SUCCESS
            // =========================================================================

            $orthanc = 1;
        }

        // ========================================================================
        // END STEP : UPDATE DATABASE RADIOLOGI
        // ========================================================================
        $stmt = $Conn->prepare("UPDATE radiologi SET
                id_access            = ?,
                id_service_request   = ?,
                id_procedure         = ?,
                pacs                 = ?,
                orthanc              = ?,
                kode_dokter_penerima = ?,
                ihs_dokter_penerima  = ?,
                nama_dokter_penerima = ?,
                radiografer          = ?,
                datetime_dikerjakan  = ?,
                status_pemeriksaan   = ?
            WHERE id_radiologi = ?
        ");

        $stmt->bind_param(
            "issiissssssi",
            $SessionIdAccess,
            $id_service_request,
            $id_procedure,
            $pacs,
            $orthanc,
            $kode_dokter,
            $ihs_dokter,
            $nama_dokter,
            $radiografer,
            $datetime_dikerjakan,
            $status,
            $id_radiologi
        );

        if (!$stmt) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Gagal menyiapkan query database'
            ]);
            exit;
        }

        if ($stmt->execute()) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Data Permintaan Radiologi Berhasil Diperbaharui'
            ]);
            $stmt->close();
            exit;
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Terjadi Kesalahan Pada Saat Update Penerimaan Permintaan Radiologi'
            ]);
            $stmt->close();
            exit;
        }
    }
?>