<?php
    //koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    //Zona Waktu
    date_default_timezone_set("Asia/Jakarta");

    //Validasi Akses
    if(empty($SessionIdAccess)){
        echo '
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-danger text-center">
                        Sesi Akses Sudah Berakhir! Silahkan Login Ulang!
                    </div>
                </div>
            </div>
            <script>
                $("#TutupDanReload").prop("disabled", true);
            </script>
        ';
        exit;
    }

    if(empty($_POST['id_rad'])){
        echo '
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-danger text-center">
                        Tidak ada data yang dikirmkan
                    </div>
                </div>
            </div>
            <script>
                $("#TutupDanReload").prop("disabled", true);
            </script>
        ';
        exit;
    }

    // Generate Token Koneksi Ke SIMRS V2
    $tokenResult = GenerateTokenSimrsV2($Conn);
    if ($tokenResult['status'] !== 'success') {
        echo '
             <div class="row">
                <div class="col-12">
                    <div class="alert alert-danger text-center">
                        Gagal mengakses SIMRS V2<br>Error: '.$tokenResult['message'].'
                    </div>
                </div>
            </div>
            <script>
                $("#TutupDanReload").prop("disabled", true);
            </script>
        ';
        exit;
    }
    $token    = $tokenResult['token'];
    $username = $tokenResult['username'];
    $base_url = $tokenResult['base_url'];

    // Mulai Menangkap Data
    $jumlah_data_yang_ditangkap = count($_POST['id_rad']);
    echo '
        <div class="row">
            <div class="col-12">
                <div class="alert alert-info text-center">
                    Jumlah Data '.$jumlah_data_yang_ditangkap.'
                </div>
            </div>
        </div>
    ';
    // Inisialisasi Nomor Baris
    $no = 1;
    $status = "success";
    // Looping
    for ($i = 0; $i < count($_POST['id_rad']); $i++) {

        $id_rad              = $_POST['id_rad'][$i] ?? "";
        $id_pasien              = $_POST['id_pasien'][$i] ?? "";
        $id_kunjungan           = $_POST['id_kunjungan'][$i] ?? "";
        $waktu                  = $_POST['waktu'][$i] ?? "";
        $selesai                = $_POST['selesai'][$i] ?? "";
        $nama                   = $_POST['nama'][$i] ?? "";
        $asal_kiriman           = $_POST['asal_kiriman'][$i] ?? "";
        $alat_pemeriksa         = $_POST['alat_pemeriksa'][$i] ?? "";
        $permintaan_pemeriksaan = $_POST['permintaan_pemeriksaan'][$i] ?? "";
        $dokter_pengirim        = $_POST['dokter_pengirim'][$i] ?? "";
        $dokter_penerima        = $_POST['dokter_penerima'][$i] ?? "";
        $radiografer            = $_POST['radiografer'][$i] ?? "";
        $kesan                  = $_POST['kesan'][$i] ?? "";
        $klinis                 = $_POST['klinis'][$i] ?? "";
        $jenis_pembayaran       = $_POST['jenis_pembayaran'][$i] ?? "";
        $tujuan_kunjungan       = $_POST['tujuan_kunjungan'][$i] ?? "";
        $kv                     = $_POST['kv'][$i] ?? "";
        $ma                     = $_POST['ma'][$i] ?? "";
        $sec                    = $_POST['sec'][$i] ?? "";

        // inisiasi Hasil
        $hasil_expertise        = "";
        $interpertasi_expertise = "";
        $keterangan_expertise   = "";
        $file_name              = "";

        // Cek Data Apakah Sudah Ada Atau Belum
        if (empty($id_pasien)) {
            $status = "ID pasien Tidak Boleh Kosong";
        }
        if (empty($id_kunjungan)) {
            $status = "ID Kunjungan Tidak Boleh Kosong";
        }
        if (empty($waktu)) {
            $status = "Waktu Permintaan Tidak Boleh Kosong";
        }
        if (empty($selesai)) {
            $selesai = $waktu;
        }
        if (empty($nama)) {
            $status = "Nama Pasien Tidak Boleh Kosong";
        }
        if (empty($asal_kiriman)) {
            $status = "Asal Kiriman Tidak Boleh Kosong";
        }
        if (empty($alat_pemeriksa)) {
            $status = "Alat Pemeriksa Tidak Boleh Kosong";
        }
        if (empty($permintaan_pemeriksaan)) {
            $status = "Permintaan Pemeriksaan Tidak Boleh Kosong";
        }
        if (empty($dokter_pengirim)) {
            $status = "Dokter Pengirim Tidak Boleh Kosong";
        }
        if (empty($dokter_penerima)) {
            $status = "Dokter Penerima Tidak Boleh Kosong";
        }
        if (empty($radiografer)) {
            $status = "Radiografer Tidak Boleh Kosong";
        }
        if (empty($kesan)) {
            $kesan = "";
        }
        if (empty($klinis)) {
            $klinis = null;
        }
        if (empty($jenis_pembayaran)) {
            $status = "Jenis Pembayaran Tidak Boleh Kosong";
        }
        if (empty($tujuan_kunjungan)) {
            $status = "Tujuan Kunjungan Tidak Boleh Kosong";
        }
        if (empty($kv)) {
            $kv = "";
        }
        if (empty($ma)) {
            $ma = "";
        }
        if (empty($sec)) {
            $sec = "";
        }

        $validasi_duplikat = mysqli_num_rows(mysqli_query($Conn, "SELECT id_radiologi FROM radiologi WHERE id_pasien='$id_pasien' AND id_kunjungan='$id_kunjungan' AND datetime_diminta='$waktu' AND system_creator='SIMRS_V2'"));
        if(empty($validasi_duplikat)){

            // generate accession_number
            $micro = microtime(true);
            $number = substr(str_replace('.', '', $micro), -6);
            $accession_number = "{$alat_pemeriksa}-{$number}";

            // priority
            $priority = "routine";

            //status_pemeriksaan
            $status_pemeriksaan = "Selesai";

            // system_creator
            $system_creator = "SIMRS_V2";

            // Klinis
            $klinis_arry = [
                [
                    "kategori"         => "",
                    "id_klinis"        => "",
                    "nama_klinis"      => "",
                    "snomed_code"      => "",
                    "snomed_display"   => "",
                    "id_master_klinis" => null
                ]
            ];
            $klinis_json = json_encode($klinis_arry,JSON_UNESCAPED_UNICODE);

            // Klinis
            $pemeriksaan_arry = [
                [
                    "modalitas"               => $alat_pemeriksa,
                    "bodysite_sys"            => "",
                    "bodysite_code"           => "",
                    "pemeriksaan_sys"         => "",
                    "nama_pemeriksaan"        => $permintaan_pemeriksaan,
                    "pemeriksaan_code"        => "",
                    "bodysite_description"    => "",
                    "id_master_pemeriksaan"   => "",
                    "id_master_pemeriksaan"   => "",
                    "pemeriksaan_description" => $permintaan_pemeriksaan
                ]
            ];
            $pemeriksaan_json = json_encode($pemeriksaan_arry,JSON_UNESCAPED_UNICODE);

            // Simpan Ke Database 'radiologi'
            $query = "INSERT INTO radiologi (
                id_pasien,
                id_kunjungan,
                accession_number,
                nama_pasien,
                priority,
                asal_kiriman,
                alat_pemeriksa,
                nama_dokter_pengirim,
                nama_dokter_penerima,
                radiografer,
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
                status_pemeriksaan,
                system_creator
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
            )";
            
            $stmt = $Conn->prepare($query);
            
            // Bind parameters
            $stmt->bind_param(
                "iissssssssssssssssssssss",
                $id_pasien,
                $id_kunjungan,
                $accession_number,
                $nama,
                $priority,
                $asal_kiriman,
                $alat_pemeriksa,
                $dokter_pengirim,
                $dokter_penerima,
                $radiografer,
                $kesan,
                $klinis_json,
                $pemeriksaan_json,
                $kv,
                $ma,
                $sec,
                $tujuan_kunjungan,
                $jenis_pembayaran,
                $waktu,
                $waktu,
                $waktu,
                $selesai,
                $status_pemeriksaan,
                $system_creator
            );
            
            if($stmt->execute()){
                $id_radiologi = $Conn->insert_id;
                $status = "success";
            } else {
                $error = $stmt->error;
                $status = "Terjadi kesalahan pada saaat menyimpan data radiologi <br> Error : $error";
            }
            $stmt->close();

            // Buka Data Detail Radiologi
            if(!empty( $id_radiologi)){
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => ''.$base_url.'/Radiologi/DetailRadiologi.php?id='.$id_rad.'',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_HTTPHEADER => array(
                        'username: solihulhadi1412',
                        'token: '.$token.''
                    ),
                ));
                $response   = curl_exec($curl);
                $curl_error = curl_error($curl);
                $http_code  = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                curl_close($curl);

                // Error CURL
                if ($curl_error) {
                    $status = $curl_error;
                }

                // Buat Result
                $result = json_decode($response, true);

                // Jika Response Bukan 200
                if ($http_code !== 200) {
                    $message = $result['message'];
                    $status = $message;
                }

                $detail = $result['detail'];
                foreach ($detail as $list_detail){
                    if($list_detail['kategori']=="Radiologi"){
                        $hasil_expertise        = $list_detail['hasil'];
                        $interpertasi_expertise = $list_detail['interpertasi'];
                        $keterangan_expertise   = $list_detail['keterangan'];

                        // Simpan Ke 'radiologi_local_exp' 
                        $query = "INSERT INTO radiologi_local_exp (
                            id_radiologi,
                            temuan,
                            kesan,
                            saran,
                            catatan
                        ) VALUES (?, ?, ?, ?, ?)";
                        $stmt = $Conn->prepare($query);
                        // Bind parameters
                        $stmt->bind_param(
                            "issss",
                            $id_radiologi,
                            $hasil_expertise,
                            $interpertasi_expertise,
                            $keterangan_expertise,
                            $keterangan_expertise
                        );
                        
                        if($stmt->execute()){
                            $status = "success";
                        } else {
                            $error = $stmt->error;
                            $status = "Terjadi kesalahan pada saaat menyimpan data expertise <br> Error : $error";
                        }
                        $stmt->close();
                    }
                    if($list_detail['kategori']=="Image Radiologi"){
                        $file_name = $list_detail['keterangan'];

                        // Simpan Ke ' radiologi_file' 
                        $folder_name      = "SIMRSV_2";
                        $file_size        = "69";
                        $file_type        = "jpeg";
                        $file_datetime    = date('Y-m-d H:i:s');
                        $file_description = "";
                        $id_radiologi_file = generateUUIDv4();
                        $query = "INSERT INTO radiologi_file (
                            id_radiologi_file,
                            id_radiologi,
                            folder_name,
                            file_datetime,
                            file_description,
                            file_type,
                            file_size,
                            file_name
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                        $stmt = $Conn->prepare($query);
                        // Bind parameters
                        $stmt->bind_param(
                            "sissssss",
                            $id_radiologi_file,
                            $id_radiologi,
                            $folder_name,
                            $file_datetime,
                            $file_description,
                            $file_type,
                            $file_size,
                            $file_name
                        );
                        
                        if($stmt->execute()){
                            $status = "success";
                        } else {
                            $error = $stmt->error;
                            $status = "Terjadi kesalahan pada saaat menyimpan data File <br> Error : $error";
                        }
                        $stmt->close();
                    }
                }
            }
        }else{
            $status = "Sudah Ada";
        }



        // Routing Status
        if($status!=="success"){
            $label_status = '<span class="text-danger">'.$status.'</span>';
        }else{
            $label_status = '<span class="text-success">'.$status.'</span>';
        }

        echo '
            <div class="row mb-3 border-1 border-bottom">
                <div class="col-12 mb-2">
                    <small><b>'.$no.'. '.$nama.'</b></small>
                </div>
                <div class="col-md-4 mb-2">
                    <div class="row">
                        <div class="col-5"><small>No.RM</small></div>
                        <div class="col-1"><small>:</small></div>
                        <div class="col-6"><small class="text text-grayish">'.$id_pasien.'</small></div>
                    </div>
                    <div class="row">
                        <div class="col-5"><small>REG</small></div>
                        <div class="col-1"><small>:</small></div>
                        <div class="col-6"><small class="text text-grayish">'.$id_kunjungan.'</small></div>
                    </div>
                    <div class="row">
                        <div class="col-5"><small>Start</small></div>
                        <div class="col-1"><small>:</small></div>
                        <div class="col-6"><small class="text text-grayish">'.$waktu.'</small></div>
                    </div>
                    <div class="row">
                        <div class="col-5"><small>End</small></div>
                        <div class="col-1"><small>:</small></div>
                        <div class="col-6"><small class="text text-grayish">'.$selesai.'</small></div>
                    </div>
                    <div class="row">
                        <div class="col-5"><small>Tujuan</small></div>
                        <div class="col-1"><small>:</small></div>
                        <div class="col-6"><small class="text text-grayish">'.$tujuan_kunjungan.'</small></div>
                    </div>
                    <div class="row">
                        <div class="col-5"><small>Pembayaran</small></div>
                        <div class="col-1"><small>:</small></div>
                        <div class="col-6"><small class="text text-grayish">'.$jenis_pembayaran.'</small></div>
                    </div>
                </div>
                <div class="col-md-4 mb-2">
                    <div class="row">
                        <div class="col-5"><small>Asal Kiriman</small></div>
                        <div class="col-1"><small>:</small></div>
                        <div class="col-6"><small class="text text-grayish">'.$asal_kiriman.'</small></div>
                    </div>
                    <div class="row">
                        <div class="col-5"><small>Modalitas</small></div>
                        <div class="col-1"><small>:</small></div>
                        <div class="col-6"><small class="text text-grayish">'.$alat_pemeriksa.'</small></div>
                    </div>
                    <div class="row">
                        <div class="col-5"><small>Pemeriksaan</small></div>
                        <div class="col-1"><small>:</small></div>
                        <div class="col-6"><small class="text text-grayish">'.$permintaan_pemeriksaan.'</small></div>
                    </div>
                    
                    <div class="row">
                        <div class="col-5"><small>Eksposure</small></div>
                        <div class="col-1"><small>:</small></div>
                        <div class="col-6"><small class="text text-grayish">'.$kv.' kV / '.$ma.' Ma / '.$sec.' Sec</small></div>
                    </div>
                    <div class="row">
                        <div class="col-5"><small>Klinis</small></div>
                        <div class="col-1"><small>:</small></div>
                        <div class="col-6"><small class="text text-grayish">'.$klinis.'</small></div>
                    </div>
                    <div class="row">
                        <div class="col-5"><small>Radiografer</small></div>
                        <div class="col-1"><small>:</small></div>
                        <div class="col-6"><small class="text text-grayish">'.$radiografer.'</small></div>
                    </div>
                </div>
                <div class="col-md-4 mb-2">
                    <div class="row">
                        <div class="col-5"><small>Dokter Pengirim</small></div>
                        <div class="col-1"><small>:</small></div>
                        <div class="col-6"><small class="text text-grayish">'.$dokter_pengirim.'</small></div>
                    </div>
                    <div class="row">
                        <div class="col-5"><small>Dokter Penerima</small></div>
                        <div class="col-1"><small>:</small></div>
                        <div class="col-6"><small class="text text-grayish">'.$dokter_penerima.'</small></div>
                    </div>
                    <div class="row">
                        <div class="col-5"><small>Kesan</small></div>
                        <div class="col-1"><small>:</small></div>
                        <div class="col-6"><small class="text text-grayish">'.$kesan.'</small></div>
                    </div>
                    <div class="row">
                        <div class="col-5"><small>Status</small></div>
                        <div class="col-1"><small>:</small></div>
                        <div class="col-6"><small class="text text-grayish">'.$label_status.'</small></div>
                    </div>
                </div>
            </div>
        ';
        

        

        $no++;
    }

    echo '
        <script>
            $("#TutupDanReload").prop("disabled", false);
            $("#ButtonMulaiMigrasi2").prop("disabled", false);
        </script>
    ';
?>