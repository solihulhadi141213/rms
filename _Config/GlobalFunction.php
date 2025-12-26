<?php
    //Generate UID dengan sparator
    function generateUUIDv4() {
        $data = openssl_random_pseudo_bytes(16);
        
        // Set versi 4
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        // Set variant RFC 4122
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
    //Special Captcha
    function GenerateCaptcha($length) {
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // Menghindari karakter ambigu
        $captcha = '';
        for ($i = 0; $i < $length; $i++) {
            $captcha .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $captcha;
    }
    
    //Membuat Token
    function GenerateToken($length) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        $charLength = strlen($characters);
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charLength - 1)];
        }
        return $randomString;
    }

    //Membuat Randome String
    function generateRandomString($length) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        $charLength = strlen($characters);
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charLength - 1)];
        }
        return $randomString;
    }

    //Membersihkan Variabel
    function validateAndSanitizeInput($input) {
        // Menghapus karakter yang tidak diinginkan
        $input = trim($input);
        $input = stripslashes($input);
        $input = htmlspecialchars($input);
        $input = addslashes($input);
        return $input;
    }

    //Data Detail
    function GetDetailData($Conn, $Tabel, $Param, $Value, $Colom) {
        // Validasi input yang diperlukan
        if (empty($Conn)) {
            return "No Database Connection";
        }
        if (empty($Tabel)) {
            return "No Table Selected";
        }
        if (empty($Param)) {
            return "No Parameter Selected";
        }
        if (empty($Value)) {
            return "No Value Provided";
        }
        if (empty($Colom)) {
            return "No Column Selected";
        }
    
        // Escape table name and column name untuk mencegah SQL Injection
        $Tabel = mysqli_real_escape_string($Conn, $Tabel);
        $Param = mysqli_real_escape_string($Conn, $Param);
        $Colom = mysqli_real_escape_string($Conn, $Colom);
    
        // Menggunakan prepared statement
        $Qry = $Conn->prepare("SELECT $Colom FROM $Tabel WHERE $Param = ?");
        if ($Qry === false) {
            return "Query Preparation Failed: " . $Conn->error;
        }
    
        // Bind parameter
        $Qry->bind_param("s", $Value);
    
        // Eksekusi query
        if (!$Qry->execute()) {
            return "Query Execution Failed: " . $Qry->error;
        }
    
        // Mengambil hasil
        $Result = $Qry->get_result();
        $Data = $Result->fetch_assoc();
    
        // Menutup statement
        $Qry->close();
    
        // Mengembalikan hasil
        if (empty($Data[$Colom])) {
            return "";
        } else {
            return $Data[$Colom];
        }
    }
    
    //Loging
    function addLog($Conn,$id_access,$log_datetime,$kategori_log,$deskripsi_log){
        $entry="INSERT INTO access_log (
            id_access,
            log_datetime,
            log_category,
            log_description
        ) VALUES (
            '$id_access',
            '$log_datetime',
            '$kategori_log',
            '$deskripsi_log'
        )";
        $Input=mysqli_query($Conn, $entry);
        if($Input){
            $Response="Success";
        }else{
            $Response="Input Log Gagal";
        }
        return $Response;
    }
    
    //Membuat Randome Number
    function generateRandomNumber($length) {
        $characters = '0123456789';
        $randomString = '';
        $charLength = strlen($characters);
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charLength - 1)];
        }
        return $randomString;
    }
    
    //Send Email
    function SendEmail($NamaTujuan,$EmailTujuan,$Subjek,$Pesan,$email_gateway,$password_gateway,$url_provider,$nama_pengirim,$port_gateway,$url_service) {
        if(empty($NamaTujuan)){
            $Response="Nama tujuan pesan tidak boleh kosong!";
        }else{
            if(empty($EmailTujuan)){
                $Response="Email tujuan pesan tidak boleh kosong!";
            }else{
                if(empty($Subjek)){
                    $Response="Subjek pesan tidak boleh kosong!";
                }else{
                    if(empty($Pesan)){
                        $Response="Isi Pesan Tidak Boleh Kosong!";
                    }else{
                        if(empty($email_gateway)){
                            $Response="Akun Email Gateway Tidak Boleh Kosong!";
                        }else{
                            if(empty($password_gateway)){
                                $Response="Password Tidak Boleh Kosong!";
                            }else{
                                if(empty($url_provider)){
                                    $Response="URL Provider Tidak Boleh Kosong!";
                                }else{
                                    if(empty($nama_pengirim)){
                                        $Response="Nama pengirim Tidak Boleh Kosong!";
                                    }else{
                                        if(empty($port_gateway)){
                                            $Response="Port Tidak Boleh Kosong!";
                                        }else{
                                            if(empty($url_service)){
                                                $Response="Url Service Tidak Boleh Kosong!";
                                            }else{
                                                //Kirim email
                                                $ch = curl_init();
                                                $headers = array(
                                                    'Content-Type: Application/JSON',          
                                                    'Accept: Application/JSON'     
                                                );
                                                $arr = array(
                                                    "subjek" => "$Subjek",
                                                    "email_asal" => "$email_gateway",
                                                    "password_email_asal" => "$password_gateway",
                                                    "url_provider" => "$url_provider",
                                                    "nama_pengirim" => "$nama_pengirim",
                                                    "email_tujuan" => "$EmailTujuan",
                                                    "nama_tujuan" => "$NamaTujuan",
                                                    "pesan" => "$Pesan",
                                                    "port" => "$port_gateway"
                                                );
                                                $json = json_encode($arr);
                                                curl_setopt($ch, CURLOPT_URL, "$url_service");
                                                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                                                curl_setopt($ch, CURLOPT_TIMEOUT, 3); 
                                                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                                                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                                                curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
                                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                                $content = curl_exec($ch);
                                                $err = curl_error($ch);
                                                curl_close($ch);
                                                $get =json_decode($content, true);
                                                $Response=$content;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $Response;
    }

    //Delete Data
    function DeleteData($Conn,$NamaDb,$NamaParam,$IdParam){
        $HapusData = mysqli_query($Conn, "DELETE FROM $NamaDb WHERE $NamaParam='$IdParam'") or die(mysqli_error($Conn));
        if($HapusData){
            $Response="Success";
        }else{
            $Response="Hapus Data Gagal";
        }
        return $Response;
    }

    function NamaHari($no){
        if($no==1){
            $Response="Senin";
        }else{
            if($no==2){
                $Response="Selasa";
            }else{
                if($no==3){
                    $Response="Rabu";
                }else{
                    if($no==4){
                        $Response="Kamis";
                    }else{
                        if($no==5){
                            $Response="Jumat";
                        }else{
                            if($no==6){
                                $Response="Sabtu";
                            }else{
                                if($no==7){
                                    $Response="Minggu";
                                }else{
                                    $Response="None";
                                }
                            }
                        }
                    }
                }
            }
        }
        return $Response;
    }
    function checkImageGifExists($jsonString,$type) {
        // Mengurai string JSON menjadi array PHP
        $data = json_decode($jsonString, true);
    
        // Pengecekan apakah $type ada dalam salah satu elemen array
        foreach ($data as $item) {
            if ($item['type'] === $type) {
                return true; // Jika ditemukan, kembalikan true
            }
        }
    
        return false; // Jika tidak ditemukan, kembalikan false
    }
    function MimeTiTipe($mim) {
        $Referensi = [
            ['name' => 'PDF', 'type' => 'application/pdf'],
            ['name' => 'XLS', 'type' => 'application/vnd.ms-excel'],
            ['name' => 'XLSX', 'type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
            ['name' => 'CSV1', 'type' => 'text/csv'],
            ['name' => 'CSV2', 'type' => 'application/csv'],
            ['name' => 'CSV3', 'type' => 'text/plain'],
            ['name' => 'DOC', 'type' => 'application/msword'],
            ['name' => 'DOCX', 'type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
            ['name' => 'JPEG', 'type' => 'image/jpeg'],
            ['name' => 'PNG', 'type' => 'image/png'],
            ['name' => 'GIF', 'type' => 'image/gif'],
        ];
        foreach ($Referensi as $item) {
            if ($item['type'] === $mim) {
                $matchedIds[] = $item['name'];
            }
        }
        $NamaFile=implode(', ', $matchedIds);
        return $NamaFile;
    }
    
    function validateUploadedFile($file,$size) {
        // Tipe file yang diperbolehkan
        $allowedMimeTypes = [
            'image/jpeg', // Untuk file .jpg dan .jpeg
            'image/png',  // Untuk file .png
            'image/gif',  // Untuk file .gif
        ];
    
        // Maksimal ukuran file (5MB, misalnya)
        $maxFileSize = $size * 1024 * 1024; // 5MB dalam byte
    
        // Periksa apakah file diunggah tanpa error
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return "Terjadi kesalahan saat mengunggah file.";
        }
    
        // Periksa ukuran file
        if ($file['size'] > $maxFileSize) {
            return "Ukuran file terlalu besar. Maksimal 5MB.";
        }
    
        // Dapatkan MIME type file
        $fileMimeType = mime_content_type($file['tmp_name']);
    
        // Validasi tipe MIME
        if (!in_array($fileMimeType, $allowedMimeTypes)) {
            return "Tipe file tidak valid. Hanya JPG, JPEG, PNG, dan GIF yang diperbolehkan.";
        }
    
        // Jika semua validasi lolos
        return true;
    }
    function validateDate($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        // Pastikan format sesuai dan tanggal valid (misalnya tidak ada 30 Februari)
        return $d && $d->format($format) === $date;
    }
    function IjinAksesSaya($Conn, $SessionIdAccess, $id_access_feature){
        // Siapkan query dengan placeholder
        $stmt = $Conn->prepare("SELECT id_access FROM access_permission WHERE id_access = ? AND id_access_feature = ?");
        
        // Bind parameter (sama-sama integer, jadi 'ii')
        $stmt->bind_param("is", $SessionIdAccess, $id_access_feature);
        
        // Eksekusi
        $stmt->execute();
        
        // Ambil hasil
        $result = $stmt->get_result();
        $DataParam = $result->fetch_assoc();
        
        // Tentukan respon
        if(empty($DataParam['id_access'])){
            $Response = "Tidak Ada";
        }else{
            $Response = "Ada";
        }
        
        // Tutup statement
        $stmt->close();
        
        return $Response;
    }
    function CekFiturEntitias($Conn, $id_access_group, $id_access_feature){
        
        // Query dengan prepared statement
        $stmt = $Conn->prepare("SELECT id_access_reference 
                                FROM access_reference 
                                WHERE id_access_group = ? AND id_access_feature = ? 
                                LIMIT 1");
        if($stmt){
            $stmt->bind_param("is", $id_access_group, $id_access_feature);
            $stmt->execute();
            $stmt->store_result();

            if($stmt->num_rows > 0){
                $Response = "Ada";
            }else{
                $Response = "Tidak Ada";
            }
            $stmt->close();
        }else{
            $Response = "Tidak Ada";
        }
        return $Response;
    }
    function CheckParameterOnJson($jsonString,$type,$parameter) {
        // Mengurai string JSON menjadi array PHP
        $data = json_decode($jsonString, true);
    
        // Pengecekan apakah $type ada dalam salah satu elemen array
        foreach ($data as $item) {
            if ($item[$parameter] === $type) {
                return true; // Jika ditemukan, kembalikan true
            }
        }
    
        return false; // Jika tidak ditemukan, kembalikan false
    }
    function ValidasiKutip($string) {
        // Pola untuk mendeteksi tanda kutip tunggal atau ganda
        $pola = "/['\"]/";
    
        // Menggunakan preg_match untuk mengecek apakah string mengandung tanda kutip
        if (preg_match($pola, $string)) {
            return false; // String mengandung tanda kutip
        } else {
            return true; // String tidak mengandung tanda kutip
        }
    }
    function ValidasiHanyaAngka($string) {
        // Pola untuk mendeteksi hanya angka
        $pola = "/^\d+$/";
    
        // Menggunakan preg_match untuk mengecek apakah string hanya mengandung angka
        return preg_match($pola, $string);
    }
    function formatRupiah($angka,$mata_uang,$zero_padding) {
        return ''.$mata_uang.' ' . number_format($angka, $zero_padding, ',', '.');
    }
    // Function to get value by UID
    function getValueByUid($data, $uid) {
        foreach ($data as $item) {
            if ($item['uid'] == $uid) {
                return $item['value'];
            }
        }
        return null;
    }
    // Function to get value by UID
    function SearchStringIntoArray($data, $keyword) {
        foreach ($data as $item) {
            if ($item== $keyword) {
                return true;
            }
        }
        return false;
    }
    function GetSometingByKeyword($DatArray,$keyword_by,$keyword,$value_parameter) {
        foreach ($DatArray as $item) {
            if ($item[$keyword_by] == $keyword) {
                return $item[$value_parameter];
            }
        }
        return null;
    }
    function ValidasiRekening($input) {
        // Cek apakah input hanya berisi angka
        if (!ctype_digit($input)) {
            return "Nomor Rekening harus berisi angka saja.";
        }
    
        // Cek apakah panjang input tidak lebih dari 20 karakter
        if (strlen($input) > 20) {
            return "Nomor Rekening tidak boleh lebih dari 20 karakter.";
        }
    
        return "Valid";
    }
    function ValidasiBank($input) {
        // Cek apakah input hanya berisi huruf dan spasi
        if (!preg_match('/^[a-zA-Z\s]*$/', $input)) {
            return "Nama Bank hanya boleh berisi huruf dan spasi.";
        }
    
        // Cek apakah panjang input tidak lebih dari 20 karakter
        if (strlen($input) > 20) {
            return "Nama Bank tidak boleh lebih dari 20 karakter.";
        }
    
        return "Valid";
    }
    function ValidasiKontak($input) {
        // Cek apakah input hanya berisi huruf dan spasi
        if (!preg_match('/^[0-9]*$/', $input)) {
            return "Kontak hanya boleh berisi huruf dan spasi.";
        }
    
        // Cek apakah panjang input tidak lebih dari 20 karakter
        if (strlen($input) > 20) {
            return "Kontak tidak boleh lebih dari 20 karakter.";
        }
    
        return "Valid";
    }
    function maskAccountNumber($accountNumber) {
        // Hitung panjang nomor rekening
        $length = strlen($accountNumber);
        
        // Ambil 4 digit terakhir
        $lastFourDigits = substr($accountNumber, -4);
        
        // Buat string bintang dengan panjang sesuai
        $maskedPart = str_repeat('*', $length - 4);
        
        // Gabungkan bagian yang di-mask dengan 4 digit terakhir
        return $maskedPart . $lastFourDigits;
    }
    function TokenValidation($input) {
        // Cek apakah input hanya berisi angka dan huruf
        if (!preg_match('/^[a-zA-Z0-9]*$/', $input)) {
            return "Input hanya boleh berisi huruf dan angka.";
        }
    
        // Cek apakah panjang input tidak lebih dari 20 karakter
        if (strlen($input) > 20) {
            return "Input tidak boleh lebih dari 20 karakter.";
        }
    
        return "Valid";
    }
    
    function hitung_usia($tanggal_lahir) {
        $birthDate = new DateTime($tanggal_lahir);
        $today = new DateTime("today");
        if ($birthDate > $today) {
            exit("Usia tidak valid");
        }
        $y = $today->diff($birthDate)->y;
        $m = $today->diff($birthDate)->m;
        $d = $today->diff($birthDate)->d;
    
        if ($y > 0) {
            return $y." tahun";
        } else if ($m > 0) {
            return $m." bulan";
        } else {
            return $d." hari";
        }
    }
    function ValidasiTanggal($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
    function calculateExpirationTimeFromDateTime($dateTime, $milliseconds) {
        // Membuat objek DateTime dari string input
        $date = new DateTime($dateTime);
    
        // Mengonversi milidetik ke detik dan mikrodetik
        $seconds = floor($milliseconds / 1000);
        $microseconds = ($milliseconds % 1000) * 1000;
    
        // Menambahkan detik dan mikrodetik ke objek DateTime
        $date->add(new DateInterval("PT{$seconds}S"));
        // Menambahkan mikrodetik menggunakan metode modify
        $date->modify("+{$microseconds} microseconds");
    
        // Mengembalikan waktu kedaluwarsa dalam format YYYY-mm-dd HH:ii:ss.uuu
        return $date->format('Y-m-d H:i:s');
    }
    function generateUuidV1() {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $length = 36;
        $uuid = '';
        for ($i = 0; $i < $length; $i++) {
            $uuid .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $uuid;
    }
    function getNamaBulan($angkaBulan) {
        // Array dengan nama-nama bulan
        $namaBulan = [
            '1' => 'Januari',
            '2' => 'Februari',
            '3' => 'Maret',
            '4' => 'April',
            '5' => 'Mei',
            '6' => 'Juni',
            '7' => 'Juli',
            '8' => 'Agustus',
            '9' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        ];
    
        // Mengembalikan nama bulan berdasarkan angka
        return $namaBulan[$angkaBulan] ?? 'Bulan tidak valid';
    }
    function getNamaBulanSingkat($angkaBulan) {
        // Array dengan nama-nama bulan
        $namaBulan = [
            '1' => 'Jan',
            '2' => 'Feb',
            '3' => 'Mar',
            '4' => 'Apr',
            '5' => 'Mei',
            '6' => 'Jun',
            '7' => 'Jul',
            '8' => 'Agu',
            '9' => 'Sept',
            '10' => 'Okt',
            '11' => 'Nov',
            '12' => 'Des'
        ];
    
        // Mengembalikan nama bulan berdasarkan angka
        return $namaBulan[$angkaBulan] ?? 'Bulan tidak valid';
    }
    function pembulatan_nilai($nilai){
        $nilai = (float) $nilai;
        $nilai = ($nilai == floor($nilai)) ? (int)$nilai : $nilai;
        return $nilai;
    }

    //Get Token
    function GetXToken($base_url,$USER_KEY,$SECRET_KEY) {

        //Inisialisasi CURL
        $curl = curl_init();

        //Creat Post Data
        $postData = json_encode([
            "USER_KEY"   => $USER_KEY,
            "SECRET_KEY" => $SECRET_KEY
        ]);

        // curl_setopt_array
        curl_setopt_array($curl, array(
            CURLOPT_URL            => rtrim($base_url, "/") . "/_API/get_token.php",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => $postData,
            CURLOPT_HTTPHEADER     => array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($postData)
            ),
            CURLOPT_SSL_VERIFYHOST => 0,  // ⚠️ Disable SSL check (testing only)
            CURLOPT_SSL_VERIFYPEER => 0   // ⚠️ Disable SSL check (testing only)
        ));
        
        //Variabel Response
        $response = curl_exec($curl);

        //tutup Curl
        curl_close($curl);

        return $response;
    }

    //CURL POST
    function CurlPost($postData,$url) {
       // Mulai CURL untuk Get Token
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => $postData,
            CURLOPT_HTTPHEADER     => array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($postData)
            ),
            CURLOPT_SSL_VERIFYHOST => 0,  // ⚠️ Disable SSL check (testing only)
            CURLOPT_SSL_VERIFYPEER => 0   // ⚠️ Disable SSL check (testing only)
        ));
        
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curlErrNo = curl_errno($curl);
        $curlErr   = curl_error($curl);
        curl_close($curl);
        if ($curlErrNo) {
            $response= htmlspecialchars($curlErr);
        }else{  
            return $response;
        }
    }

    /**
     * =====================================================
     * FUNCTION : GetSimrsToken
     * TUJUAN   : Mengambil token SIMRS aktif
     *            - Auto generate jika kosong / expired
     *            - Simpan token & expired ke database
     * PARAM    : $Conn (mysqli connection)
     * RETURN   : string token
     * =====================================================
     */
    function GetSimrsToken($Conn){
        date_default_timezone_set("Asia/Jakarta");

        // ===============================
        // AMBIL KONEKSI SIMRS AKTIF
        // ===============================
        $status = 1;

        $Qry = $Conn->prepare("
            SELECT 
                id_connection_simrs,
                url_connection_simrs,
                client_id,
                client_key,
                token,
                datetime_expired
            FROM connection_simrs
            WHERE status_connection_simrs = ?
            LIMIT 1
        ");
        $Qry->bind_param("i", $status);
        $Qry->execute();
        $Result = $Qry->get_result();

        if ($Result->num_rows == 0) {
            $Qry->close();
            return false;
        }

        $Data = $Result->fetch_assoc();
        $Qry->close();

        // ===============================
        // VARIABEL
        // ===============================
        $id_connection_simrs  = $Data['id_connection_simrs'];
        $url_connection_simrs = rtrim($Data['url_connection_simrs'], '/');
        $client_id            = $Data['client_id'];
        $client_key           = $Data['client_key'];
        $token                = $Data['token'];
        $datetime_expired     = $Data['datetime_expired'];

        $now = date('Y-m-d H:i:s');

        // ===============================
        // CEK TOKEN PERLU DIPERBARUI?
        // ===============================
        $needNewToken = false;

        if (empty($token)) {
            $needNewToken = true;
        }

        if (!empty($datetime_expired) && strtotime($datetime_expired) <= strtotime($now)) {
            $needNewToken = true;
        }

        // ===============================
        // REQUEST TOKEN BARU
        // ===============================
        if ($needNewToken === true) {

            $payload = json_encode([
                "client_id"  => $client_id,
                "client_key" => $client_key
            ]);

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $url_connection_simrs . "/API/SIMRS/get_token.php",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json'
                ],
                CURLOPT_TIMEOUT => 15
            ]);

            $response = curl_exec($curl);

            if ($response === false) {
                curl_close($curl);
                return false;
            }

            curl_close($curl);

            $res = json_decode($response, true);

            if (
                empty($res['response']['code']) ||
                $res['response']['code'] != 200 ||
                empty($res['metadata']['token'])
            ) {
                return false;
            }

            // ===============================
            // SIMPAN TOKEN BARU
            // ===============================
            $token            = $res['metadata']['token'];
            $datetime_expired = $res['metadata']['datetime_expired'];

            $Upd = $Conn->prepare("
                UPDATE connection_simrs 
                SET token = ?, datetime_expired = ?
                WHERE id_connection_simrs = ?
            ");
            $Upd->bind_param(
                "ssi",
                $token,
                $datetime_expired,
                $id_connection_simrs
            );
            $Upd->execute();
            $Upd->close();
        }

        // ===============================
        // TOKEN SIAP DIGUNAKAN
        // ===============================
        return $token;
    }

    function formatDateTimeStrict($datetime) {
        // Cek apakah null atau string kosong
        if (is_null($datetime) || trim($datetime) === '') {
            return '-';
        }
        
        // Cek format tanggal MySQL yang tidak valid
        $invalid_patterns = [
            '0000-00-00 00:00:00',
            '0000-00-00',
            '1970-01-01 00:00:00',
            '1970-01-01'
        ];
        
        if (in_array($datetime, $invalid_patterns)) {
            return '-';
        }
        
        // Cek apakah bisa di-parse oleh strtotime
        $timestamp = strtotime($datetime);
        if ($timestamp === false || $timestamp <= 0) {
            return '-';
        }
        
        // Format yang valid
        return date('d/m/Y H:i T', $timestamp);
    }

    // Fungsi Untuk Menghitung Usia Pasien
    function hitungUsia($tanggal_lahir, $default = '-') {
        // Jika tanggal lahir kosong, kembalikan default
        if (empty(trim($tanggal_lahir)) || $tanggal_lahir == '-') {
            return $default;
        }
        
        try {
            // Buat objek DateTime untuk tanggal lahir
            $tgl_lahir = new DateTime($tanggal_lahir);
            $sekarang = new DateTime();
            
            // Hitung selisih
            $selisih = $sekarang->diff($tgl_lahir);
            
            // Format usia berdasarkan kondisi
            if ($selisih->y >= 1) {
                // Jika 1 tahun atau lebih
                return $selisih->y . ' tahun';
            } elseif ($selisih->m >= 1) {
                // Jika 1 bulan atau lebih tapi kurang dari 1 tahun
                $bulan = $selisih->m;
                if ($selisih->d > 0) {
                    return $bulan . ' bulan ' . $selisih->d . ' hari';
                }
                return $bulan . ' bulan';
            } else {
                // Jika kurang dari 1 bulan
                return $selisih->d . ' hari';
            }
        } catch (Exception $e) {
            // Jika format tanggal tidak valid
            return $default;
        }
    }

    // Menampilkan Format tanggal lahir pasien
    function formatTanggalLahir($tanggal_lahir) {
        // Cek jika kosong atau null
        if (empty($tanggal_lahir) || $tanggal_lahir == '-' || trim($tanggal_lahir) == '') {
            return '-';
        }
        
        // Hapus spasi di awal dan akhir
        $tanggal_lahir = trim($tanggal_lahir);
        
        // Coba berbagai format tanggal
        $formats = [
            'Y-m-d',      // 2024-01-15
            'Y/m/d',      // 2024/01/15
            'd-m-Y',      // 15-01-2024
            'd/m/Y',      // 15/01/2024
            'Ymd',        // 20240115
            'd F Y',      // 15 January 2024
            'j F Y',      // 15 January 2024 (tanpa leading zero)
        ];
        
        foreach ($formats as $format) {
            $date = DateTime::createFromFormat($format, $tanggal_lahir);
            if ($date !== false) {
                return $date->format('d/m/Y');
            }
        }
        
        // Jika semua format gagal, coba dengan strtotime
        $timestamp = strtotime($tanggal_lahir);
        if ($timestamp !== false) {
            return date('d/m/Y', $timestamp);
        }
        
        // Jika tidak bisa di-parse sama sekali
        return '-';
    }

    // =========================================
    // Fungsi Untuk Generate Token Satu Sehat
    // =========================================
    function generateTokenSatuSehat($Conn) {

        // Validasi koneksi database
        if (!$Conn || $Conn->connect_error) {
            return [
                'status' => 'error',
                'message' => 'Koneksi database tidak valid!'
            ];
        }

        // Ambil koneksi SATUSEHAT yang aktif
        $Qry = $Conn->prepare("
            SELECT 
                id_connection_satu_sehat,
                name_connection_satu_sehat,
                url_connection_satu_sehat,
                client_key,
                secret_key,
                token,
                datetime_expired
            FROM connection_satu_sehat
            WHERE status_connection_satu_sehat = 1
            LIMIT 1
        ");

        if (!$Qry->execute()) {
            return [
                'status' => 'error',
                'message' => 'Error Database: ' . $Conn->error
            ];
        }

        $Result = $Qry->get_result();
        $Data   = $Result->fetch_assoc();
        $Qry->close();

        if (!$Data) {
            return [
                'status' => 'error',
                'message' => 'Tidak ada koneksi Satu Sehat yang aktif!'
            ];
        }

        // Ambil data
        $id_connection = $Data['id_connection_satu_sehat'];
        $url_api       = rtrim($Data['url_connection_satu_sehat'], '/');
        $client_key    = $Data['client_key'];
        $secret_key    = $Data['secret_key'];
        $token_db      = $Data['token'];
        $expired_db    = $Data['datetime_expired'];

        // =====================================================
        // 1️⃣ JIKA TOKEN MASIH ADA & BELUM EXPIRED → PAKAI
        // =====================================================
        if (!empty($token_db) && !empty($expired_db)) {
            if (strtotime($expired_db) > time()) {
                return [
                    'status'  => 'success',
                    'message' => 'Token masih valid (cache)',
                    'token'   => $token_db
                ];
            }
        }

        // =====================================================
        // 2️⃣ TOKEN KOSONG / EXPIRED → REQUEST TOKEN BARU
        // =====================================================
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL            => $url_api . '/oauth2/v1/accesstoken?grant_type=client_credentials',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => 'client_id='.$client_key.'&client_secret='.$secret_key.'',
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded'
            ],
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT        => 10,

            //Prod Only
            // CURLOPT_SSL_VERIFYPEER => true,
            // CURLOPT_SSL_VERIFYHOST => 2

            // DEV ONLY
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ]);

        $response   = curl_exec($ch);
        $http_code  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            return [
                'status' => 'error',
                'message' => 'Gagal menghubungi API Satu Sehat: ' . $curl_error
            ];
        }

        if ($http_code !== 200) {
            return [
                'status' => 'error',
                'message' => 'HTTP Error ' . $http_code . ' | ' . substr($response, 0, 200)
            ];
        }

        $result = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'status' => 'error',
                'message' => 'Response API bukan JSON valid: ' . json_last_error_msg()
            ];
        }

        if (empty($result['access_token'])) {
            return [
                'status' => 'error',
                'message' => 'Token tidak ditemukan pada response SATUSEHAT'
            ];
        }

        // =====================================================
        // 3️⃣ SIMPAN TOKEN BARU KE DATABASE
        // =====================================================
        $access_token = $result['access_token'];
        $expires_in   = isset($result['expires_in']) ? intval($result['expires_in']) : 3600;

        // Buffer 5 menit
        $buffer           = 300;
        $datetime_expired = date('Y-m-d H:i:s', time() + $expires_in - $buffer);

        $Update = $Conn->prepare("
            UPDATE connection_satu_sehat 
            SET token = ?, datetime_expired = ?
            WHERE id_connection_satu_sehat = ?
        ");

        $Update->bind_param("ssi", $access_token, $datetime_expired, $id_connection);

        if (!$Update->execute()) {
            return [
                'status' => 'error',
                'message' => 'Gagal menyimpan token: ' . $Conn->error
            ];
        }

        $Update->close();

        return [
            'status'  => 'success',
            'message' => 'Token baru berhasil dibuat',
            'token'   => $access_token
        ];
    }

    /**
     * ============================================================
     * GENERATE / REUSE TOKEN PACS
     * ============================================================
     * - Menggunakan token lama jika masih valid
     * - Login ulang jika token expired
     * - Simpan token & expired ke DB
     * ============================================================
     */
    function generateTokenPacs($Conn){
        date_default_timezone_set('Asia/Jakarta');

        /* ============================================================
        * 1. AMBIL KONFIGURASI PACS AKTIF
        * ============================================================ */
        $status = 1;
        $stmt = $Conn->prepare("
            SELECT 
                id_connection_pacs,
                url_connection_pacs,
                username_connection_pacs,
                password_connection_pacs,
                token,
                token_expired
            FROM connection_pacs
            WHERE status_connection_pacs = ?
            LIMIT 1
        ");
        $stmt->bind_param("i", $status);
        $stmt->execute();
        $config = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$config) {
            return [
                'status'  => 'error',
                'message' => 'Koneksi PACS aktif tidak ditemukan'
            ];
        }

        /* ============================================================
        * 2. JIKA TOKEN MASIH VALID → PAKAI LANGSUNG
        * ============================================================ */
        if (!empty($config['token']) && !empty($config['token_expired'])) {
            $now = new DateTime();
            $expired = new DateTime($config['token_expired']);

            if ($expired > $now) {
                return [
                    'status'           => 'success',
                    'message'          => 'Menggunakan token PACS yang masih valid',
                    'token'            => $config['token'],
                    'token_expired_at' => $config['token_expired']
                ];
            }
        }

        /* ============================================================
        * 3. LOGIN KE PACS (TOKEN EXPIRED / BELUM ADA)
        * ============================================================ */
        if (
            empty($config['url_connection_pacs']) ||
            empty($config['username_connection_pacs']) ||
            empty($config['password_connection_pacs'])
        ) {
            return [
                'status'  => 'error',
                'message' => 'Konfigurasi PACS tidak lengkap'
            ];
        }

        $login_url = rtrim($config['url_connection_pacs'], '/') . '/api/auth/login';

        $postFields = http_build_query([
            'username' => $config['username_connection_pacs'],
            'password' => $config['password_connection_pacs']
        ]);

        /* ============================================================
        * 4. CURL LOGIN
        * ============================================================ */
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => $login_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $postFields,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/x-www-form-urlencoded'
            ],
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => false, // aktifkan true jika SSL valid
            CURLOPT_SSL_VERIFYHOST => false
        ]);

        $response   = curl_exec($curl);
        $curl_error = curl_error($curl);
        $http_code  = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($curl_error) {
            return [
                'status'  => 'error',
                'message' => 'CURL Error: ' . $curl_error
            ];
        }

        if ($http_code !== 200) {
            return [
                'status'    => 'error',
                'message'   => 'Login PACS gagal',
                'http_code' => $http_code,
                'response'  => $response
            ];
        }

        /* ============================================================
        * 5. PARSE RESPONSE
        * ============================================================ */
        $result = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'status'  => 'error',
                'message' => 'Response PACS bukan JSON valid'
            ];
        }

        if (empty($result['token'])) {
            return [
                'status'  => 'error',
                'message' => 'Token tidak ditemukan pada response PACS'
            ];
        }

        /* ============================================================
        * 6. SIMPAN TOKEN & EXPIRED KE DATABASE
        * ============================================================ */
        $token        = $result['token'];
        $expired_at   = !empty($result['token_expired_at'])
                        ? date('Y-m-d H:i:s', strtotime($result['token_expired_at']))
                        : date('Y-m-d H:i:s', strtotime('+1 day')); // fallback

        $update = $Conn->prepare("
            UPDATE connection_pacs
            SET token = ?, token_expired = ?
            WHERE id_connection_pacs = ?
        ");
        $update->bind_param(
            "ssi",
            $token,
            $expired_at,
            $config['id_connection_pacs']
        );
        $update->execute();
        $update->close();

        /* ============================================================
        * 7. RETURN SUCCESS
        * ============================================================ */
        return [
            'status'           => 'success',
            'message'          => 'Token PACS berhasil diperbarui',
            'token'            => $token,
            'token_expired_at' => $expired_at,
            'user'             => $result['user'] ?? null
        ];
    }

    /**
     * Membuat inisial dari nama
     * @param string|null $nama
     * @return string
     */
    function getInisialNama($nama)
    {
        if (empty($nama)) {
            return '-';
        }

        // Bersihkan spasi berlebih
        $nama = trim(preg_replace('/\s+/', ' ', $nama));

        // Pisahkan nama
        $parts = explode(' ', $nama);

        if (count($parts) >= 2) {
            // Ambil inisial nama depan & belakang
            $inisial = mb_substr($parts[0], 0, 1) . mb_substr(end($parts), 0, 1);
        } else {
            // Hanya satu kata → ambil 2 huruf depan
            $inisial = mb_substr($parts[0], 0, 2);
        }

        return mb_strtoupper($inisial);
    }



?>