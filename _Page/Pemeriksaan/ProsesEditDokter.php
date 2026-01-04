<?php
    // koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

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
    if(empty($_POST['id_radiologi'])){
        $response['message'] = 'ID Radiologi Tidak Boleh Kosong!.';
        echo json_encode($response);
        exit;
    }

    if(empty($_POST['dokter_pengirim'])){
        $response['message'] = 'Dokter Pengirim Tidak Boleh Kosong!.';
        echo json_encode($response);
        exit;
    }

    if(empty($_POST['dokter_penerima'])){
        $response['message'] = 'Dokter Penerima Tidak Boleh Kosong!.';
        echo json_encode($response);
        exit;
    }

    // Buat Variabel
    $id_radiologi    = (int) ($_POST['id_radiologi'] ?? 0);
    $dokter_pengirim = validateAndSanitizeInput($_POST['dokter_pengirim'] ?? '');
    $dokter_penerima = validateAndSanitizeInput($_POST['dokter_penerima'] ?? '');

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
    $kode_dokter_pengirim = '';
    $ihs_dokter_pengirim  = '';
    $nama_dokter_pengirim = '';

    $kode_dokter_penerima = '';
    $ihs_dokter_penerima  = '';
    $nama_dokter_penerima = '';
    
    
    if(!empty($data_dokter['response']['code']) && $data_dokter['response']['code'] == 200){
        $list_dokter = $data_dokter['metadata']['list_dokter'] ?? $data_dokter['response']['list_dokter'] ?? [];

        // Cari Dokter Penerima Yang Dipilih
        foreach($list_dokter as $dokter_list1){
            if($dokter_list1['id_dokter'] == $dokter_pengirim){
                $kode_dokter_pengirim = $dokter_list1['kode'] ?? '';
                $ihs_dokter_pengirim  = $dokter_list1['id_ihs_practitioner'] ?? '';
                $nama_dokter_pengirim = $dokter_list1['nama'] ?? '';
                break;
            }
        }

        foreach($list_dokter as $dokter_list2){
            if($dokter_list2['id_dokter'] == $dokter_penerima){
                $kode_dokter_penerima = $dokter_list2['kode'] ?? '';
                $ihs_dokter_penerima  = $dokter_list2['id_ihs_practitioner'] ?? '';
                $nama_dokter_penerima = $dokter_list2['nama'] ?? '';
                break;
            }
        }
    }

    if(empty($kode_dokter_pengirim)){
        echo json_encode([
            'status' => 'error',
            'message' => 'ID Dokter Pengirim ('.$dokter_pengirim.') Tidak Valid'
        ]);
        exit;
    }
    if(empty($kode_dokter_penerima)){
        echo json_encode([
            'status' => 'error',
            'message' => 'ID Dokter Penerima ('.$dokter_penerima.') Tidak Valid'
        ]);
        exit;
    }


    // 4. Update Ke Database
    $stmt = $Conn->prepare("UPDATE radiologi SET
            kode_dokter_pengirim = ?,
            ihs_dokter_pengirim  = ?,
            nama_dokter_pengirim = ?,
            kode_dokter_penerima = ?,
            ihs_dokter_penerima  = ?,
            nama_dokter_penerima = ?
        WHERE id_radiologi = ?
    ");

    $stmt->bind_param(
        "ssssssi",
        $kode_dokter_pengirim,
        $ihs_dokter_pengirim,
        $nama_dokter_pengirim,
        $kode_dokter_penerima,
        $ihs_dokter_penerima,
        $nama_dokter_penerima,
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
            'message' => 'Data Dokter Pengirim dan Penerima Radiologi Berhasil Diperbaharui'
        ]);
        $stmt->close();
        exit;
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Terjadi Kesalahan Pada Saat Update Permintaan Radiologi'
        ]);
        $stmt->close();
        exit;
    }
?>