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
    $id_radiologi = (int) ($_POST['id_radiologi'] ?? 0);
    if ($id_radiologi <= 0) {
        $response['message'] = 'ID Radiologi tidak valid.';
        echo json_encode($response);
        exit;
    }
    $status             = validateAndSanitizeInput($_POST['status'] ?? '');
    $radiografer        = validateAndSanitizeInput($_POST['radiografer'] ?? '');
    $alasan_pembatalan  = validateAndSanitizeInput($_POST['alasan_pembatalan'] ?? '');
    $tanggal_dikerjakan = validateAndSanitizeInput($_POST['tanggal_dikerjakan'] ?? '');
    $jam_dikerjakan     = validateAndSanitizeInput($_POST['jam_dikerjakan'] ?? '');
    $dokter_penerima    = validateAndSanitizeInput($_POST['dokter_penerima'] ?? '');

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
    // UPDATE BERDASARKAN STATUS
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

        // 4. Update Ke Database
        $stmt = $Conn->prepare("UPDATE radiologi SET
                id_access            = ?,
                kode_dokter_penerima = ?,
                ihs_dokter_penerima  = ?,
                nama_dokter_penerima = ?,
                radiografer          = ?,
                datetime_dikerjakan  = ?,
                status_pemeriksaan   = ?
            WHERE id_radiologi = ?
        ");

        $stmt->bind_param(
            "issssssi",
            $SessionIdAccess,
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