<?php
    // ProsesTambah.php
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    // Validasi Sesi
    if(empty($SessionIdAccess)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Sesi Akses Sudah Berakhir. Silahkan Login Ulang!'
        ]);
        exit;
    }

    // Validasi input
    if(empty($_POST['id_kunjungan'])){
        echo json_encode([
            'status' => 'error',
            'message' => 'ID Kunjungan tidak boleh kosong!'
        ]);
        exit;
    }

    if(empty($_POST['priority'])){
        echo json_encode([
            'status' => 'error',
            'message' => 'Perioritas permintaan tidak boleh kosong!'
        ]);
        exit;
    }

    // Ambil data dari form
    $id_kunjungan = validateAndSanitizeInput($_POST['id_kunjungan']);
    $id_access = $SessionIdAccess;

    // 1. Dapatkan Data Kunjungan dari API SIMRS
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

    // Call API get_detail_kunjungan
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
    $data_kunjungan = json_decode($response, true);

    if(empty($data_kunjungan['response']['code']) || $data_kunjungan['response']['code'] != 200){
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal mengambil data kunjungan: '.($data_kunjungan['response']['message'] ?? 'Unknown error')
        ]);
        exit;
    }

    $metadata = $data_kunjungan['metadata'];
    $id_pasien = $metadata['pasien']['id_pasien'] ?? '';
    $tujuan = $metadata['tujuan'] ?? '';
    $pembayaran = $metadata['pembayaran'] ?? '';

    // 2. Generate Accession Number
    $date = date('Ymd');
    $time = date('His');
    $random = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
    $accession_number = "RAD-{$date}-{$time}-{$random}";

    // 3. Ambil Data Dokter Pengirim
    if(!empty($_POST['dokter_pengirim'])){
        $id_dokter_pengirim = validateAndSanitizeInput($_POST['dokter_pengirim']);
        
        // Call API get_dokter untuk mendapatkan detail dokter
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
        curl_close($curl2);
        $data_dokter = json_decode($response_dokter, true);
        
        $kode_dokter_pengirim = '';
        $ihs_dokter_pengirim = '';
        $nama_dokter_pengirim = '';
        
        if(!empty($data_dokter['response']['code']) && $data_dokter['response']['code'] == 200){
            $list_dokter = $data_dokter['metadata']['list_dokter'] ?? [];
            foreach($list_dokter as $dokter){
                if($dokter['id_dokter'] == $id_dokter_pengirim){
                    $kode_dokter_pengirim = $dokter['kode'] ?? '';
                    $ihs_dokter_pengirim = $dokter['id_ihs_practitioner'] ?? '';
                    $nama_dokter_pengirim = $dokter['nama'] ?? '';
                    break;
                }
            }
        }
    } else {
        $id_dokter_pengirim = '';
        $kode_dokter_pengirim = '';
        $ihs_dokter_pengirim = '';
        $nama_dokter_pengirim = '';
    }

    // 4. Proses Data Klinis (JSON Format)
    $klinis_data = [];
    if(!empty($_POST['klinis'])){
        $klinis_input = is_array($_POST['klinis']) ? $_POST['klinis'] : explode(',', $_POST['klinis']);
        
        foreach($klinis_input as $nama_klinis){
            $nama_klinis = validateAndSanitizeInput($nama_klinis);
            
            // Cek apakah klinis sudah ada di master
            $query_klinis = "SELECT * FROM master_klinis WHERE nama_klinis = ? AND aktif = 'Ya'";
            $stmt_klinis = $Conn->prepare($query_klinis);
            $stmt_klinis->bind_param("s", $nama_klinis);
            $stmt_klinis->execute();
            $result_klinis = $stmt_klinis->get_result();
            
            // Generate UID Klinis
            $id_klinis = generateUUIDv4();
            if($result_klinis->num_rows > 0){
                $row_klinis = $result_klinis->fetch_assoc();
                $klinis_data[] = [
                    'id_klinis'        => $id_klinis,
                    'id_master_klinis' => $row_klinis['id_master_klinis'],
                    'nama_klinis'      => $row_klinis['nama_klinis'],
                    'snomed_code'      => $row_klinis['snomed_code'] ?? '',
                    'snomed_display'   => $row_klinis['snomed_display'] ?? '',
                    'kategori'         => $row_klinis['kategori'] ?? ''
                ];
            } else {
                // Jika tidak ada di master, simpan sebagai klinis custom
                $klinis_data[] = [
                    'id_klinis'        => $id_klinis,
                    'id_master_klinis' => null,
                    'nama_klinis'      => $nama_klinis,
                    'snomed_code'      => '',
                    'snomed_display'   => '',
                    'kategori'         => ''
                ];
            }
        }
    }

    // 5. Proses Data Permintaan Pemeriksaan (JSON Format)
    $pemeriksaan_data = [];
    if(!empty($_POST['permintaan_pemeriksaan'])){
        $pemeriksaan_input = is_array($_POST['permintaan_pemeriksaan']) ? $_POST['permintaan_pemeriksaan'] : explode(',', $_POST['permintaan_pemeriksaan']);
        
        // Ambil data alat pemeriksa
        $alat_pemeriksa = validateAndSanitizeInput($_POST['alat_pemeriksa'] ?? '');
        
        foreach($pemeriksaan_input as $nama_pemeriksaan){
            $nama_pemeriksaan = validateAndSanitizeInput($nama_pemeriksaan);
            
            // Cek apakah pemeriksaan sudah ada di master
            $query_pemeriksaan = "SELECT * FROM master_pemeriksaan WHERE nama_pemeriksaan = ?";
            $stmt_pemeriksaan = $Conn->prepare($query_pemeriksaan);
            $stmt_pemeriksaan->bind_param("s", $nama_pemeriksaan);
            $stmt_pemeriksaan->execute();
            $result_pemeriksaan = $stmt_pemeriksaan->get_result();
            
            if($result_pemeriksaan->num_rows > 0){
                $row_pemeriksaan = $result_pemeriksaan->fetch_assoc();
                $pemeriksaan_data[] = [
                    'id_master_pemeriksaan' => $row_pemeriksaan['id_master_pemeriksaan'],
                    'nama_pemeriksaan' => $row_pemeriksaan['nama_pemeriksaan'],
                    'modalitas' => $row_pemeriksaan['modalitas'] ?? $alat_pemeriksa,
                    'pemeriksaan_code' => $row_pemeriksaan['pemeriksaan_code'] ?? '',
                    'pemeriksaan_description' => $row_pemeriksaan['pemeriksaan_description'] ?? '',
                    'pemeriksaan_sys' => $row_pemeriksaan['pemeriksaan_sys'] ?? '',
                    'bodysite_code' => $row_pemeriksaan['bodysite_code'] ?? '',
                    'bodysite_description' => $row_pemeriksaan['bodysite_description'] ?? '',
                    'bodysite_sys' => $row_pemeriksaan['bodysite_sys'] ?? ''
                ];
            } else {
                // Jika tidak ada di master, simpan sebagai pemeriksaan custom
                $pemeriksaan_data[] = [
                    'id_master_pemeriksaan' => null,
                    'nama_pemeriksaan' => $nama_pemeriksaan,
                    'modalitas' => $alat_pemeriksa,
                    'pemeriksaan_code' => '',
                    'pemeriksaan_description' => '',
                    'pemeriksaan_sys' => '',
                    'bodysite_code' => '',
                    'bodysite_description' => '',
                    'bodysite_sys' => ''
                ];
            }
        }
    }

    // 6. Konversi ke JSON
    $klinis_json = !empty($klinis_data) ? json_encode($klinis_data, JSON_UNESCAPED_UNICODE) : null;
    $pemeriksaan_json = !empty($pemeriksaan_data) ? json_encode($pemeriksaan_data, JSON_UNESCAPED_UNICODE) : null;

    // 7. Data lainnya dari form
    $asal_kiriman   = validateAndSanitizeInput($_POST['asal_kiriman'] ?? '');
    $alat_pemeriksa = validateAndSanitizeInput($_POST['alat_pemeriksa'] ?? '');
    $priority = validateAndSanitizeInput($_POST['priority']);
    $pesan    = validateAndSanitizeInput($_POST['pesan'] ?? '');

    // 8. Tanggal dan waktu
    $tanggal_diminta  = validateAndSanitizeInput($_POST['tanggal_diminta'] ?? date('Y-m-d'));
    $jam_diminta      = validateAndSanitizeInput($_POST['jam_diminta'] ?? date('H:i'));
    $datetime_diminta = $tanggal_diminta . ' ' . $jam_diminta;

    // 9. Data yang dikosongkan
    $kode_dokter_penerima = '';
    $ihs_dokter_penerima = '';
    $nama_dokter_penerima = '';
    $radiografer = '';
    $kesan = '';
    $kv = '';
    $ma = '';
    $sec = '';
    $datetime_dikerjakan = NULL;
    $datetime_hasil = NULL;
    $datetime_selesai = NULL;
    $status_pemeriksaan = 'Diminta';

    // 10. Insert ke Database
    try {
        $query = "INSERT INTO radiologi (
            id_access,
            id_pasien,
            id_kunjungan,
            accession_number,
            nama_pasien,
            priority,
            asal_kiriman,
            alat_pemeriksa,
            kode_dokter_pengirim,
            ihs_dokter_pengirim,
            nama_dokter_pengirim,
            kode_dokter_penerima,
            ihs_dokter_penerima,
            nama_dokter_penerima,
            radiografer,
            pesan,
            kesan,
            klinis,
            permintaan_pemeriksaan,
            kv,
            ma,
            sec,
            tujuan,
            pembayaran,
            datetime_diminta,
            datetime_dikerjakan,
            datetime_hasil,
            datetime_selesai,
            status_pemeriksaan
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
        )";
        
        $stmt = $Conn->prepare($query);
        
        // Bind parameters
        $stmt->bind_param(
            "iiissssssssssssssssssssssssss",
            $id_access,
            $id_pasien,
            $id_kunjungan,
            $accession_number,
            $metadata['pasien']['nama'],
            $priority,
            $asal_kiriman,
            $alat_pemeriksa,
            $kode_dokter_pengirim,
            $ihs_dokter_pengirim,
            $nama_dokter_pengirim,
            $kode_dokter_penerima,
            $ihs_dokter_penerima,
            $nama_dokter_penerima,
            $radiografer,
            $pesan,
            $kesan,
            $klinis_json,
            $pemeriksaan_json,
            $kv,
            $ma,
            $sec,
            $tujuan,
            $pembayaran,
            $datetime_diminta,
            $datetime_dikerjakan,
            $datetime_hasil,
            $datetime_selesai,
            $status_pemeriksaan
        );
        
        if($stmt->execute()){
            echo json_encode([
                'status' => 'success',
                'message' => 'Permintaan pemeriksaan berhasil ditambahkan!'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Gagal menyimpan data: ' . $stmt->error
            ]);
        }
        
        $stmt->close();
        
    } catch(Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ]);
    }
?>