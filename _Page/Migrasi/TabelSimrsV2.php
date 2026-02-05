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
            <tr>
                <td colspan="12" class="text-center">
                    <small class="text-danger">Sesi Akses Sudah Berakhir! Silahkan Login Ulang!</small>
                </td>
            </tr>
            <script>
                $("#ButtonMulaiMigrasi2").prop("disabled", true);
                $("#page_info").html("Page : 0 / 0");
                $("#prev_button").prop("disabled", true);
                $("#next_button").prop("disabled", true);
                $("#metadata").html("");
            </script>
        ';
        exit;
    }
    
    //Menangkap Data Filter
    if(!empty($_POST['keyword_by'])){
        $keyword_by=$_POST['keyword_by'];
    }else{
        $keyword_by="";
    }
    
    if(!empty($_POST['keyword'])){
        $keyword=$_POST['keyword'];
    }else{
        $keyword="";
    }
    
    if(!empty($_POST['limit'])){
        $limit=$_POST['limit'];
    }else{
        $limit="10";
    }
    
    if(!empty($_POST['short_by'])){
        $short_by=$_POST['short_by'];
    }else{
        $short_by="DESC";
    }
    //OrderBy
    if(!empty($_POST['order_by'])){
        $order_by=$_POST['order_by'];
    }else{
        $order_by="id_rad";
    }
    //Atur Page
    if(!empty($_POST['page'])){
        $page=$_POST['page'];
        $posisi = ( $page - 1 ) * $limit;
    }else{
        $page="1";
        $posisi = 0;
    }

    // Generate Token Koneksi Ke SIMRS V2
    $tokenResult = GenerateTokenSimrsV2($Conn);
    if ($tokenResult['status'] !== 'success') {
        echo '
            <tr>
                <td colspan="12" class="text-center">
                    <small class="text-danger">Gagal mengakses SIMRS V2<br>Error: '.$tokenResult['message'].'</small>
                </td>
            </tr>
            <script>
                $("#ButtonMulaiMigrasi2").prop("disabled", true);
                $("#page_info").html("Page : 0 / 0");
                $("#prev_button").prop("disabled", true);
                $("#next_button").prop("disabled", true);
                $("#metadata").html("");
            </script>
        ';
        exit;
    }
    $token    = $tokenResult['token'];
    $username = $tokenResult['username'];
    $base_url = $tokenResult['base_url'];
    
    // Mulai CURL
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => ''.$base_url.'/Radiologi/ListRadiologi.php',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array('limit' => $limit, 'page' => $page, 'short_by' => $short_by, 'order_by' => $order_by, 'keyword_by' => $keyword_by,'keyword' => $keyword),
        CURLOPT_HTTPHEADER => array(
            'username: '.$username.'',
            'token: '.$token.''
        ),
    ));

    $response   = curl_exec($curl);
    $curl_error = curl_error($curl);
    $http_code  = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    // Error CURL
    if ($curl_error) {
        echo '
            <tr>
                <td colspan="12" class="text-center">
                    <small class="text-danger">CURL Error: '.$curl_error.'</small>
                </td>
            </tr>
            <script>
                $("#ButtonMulaiMigrasi2").prop("disabled", true);
                $("#page_info").html("Page : 0 / 0");
                $("#prev_button").prop("disabled", true);
                $("#next_button").prop("disabled", true);
                $("#metadata").html("");
            </script>
        ';
        exit;
    }

    // Status Error (tidak 200)
    if ($http_code !== 200) {
        echo '
            <tr>
                <td colspan="12" class="text-center">
                    <small class="text-danger">CURL Error: '.$response.'</small>
                </td>
            </tr>
            <script>
                $("#ButtonMulaiMigrasi2").prop("disabled", true);
                $("#page_info").html("Page : 0 / 0");
                $("#prev_button").prop("disabled", true);
                $("#next_button").prop("disabled", true);
                $("#metadata").html("");
            </script>
        ';
        exit;
    }

    // Decode response
    $result = json_decode($response, true);
    
    // Menangkap meta
    $meta        = $result['meta'];
    $page        = $meta['page'];
    $total_data  = $meta['total_data'];
    $total_pages = $meta['total_pages'];
    $short_by    = $meta['short_by'];
    $order_by    = $meta['order_by'];
    $keyword_by  = $meta['keyword_by'];

    // Jika Data Tidak Ditemukan (Kosong)
    if(empty(count($result['data']))){
        echo '
            <tr>
                <td colspan="12" class="text-center">
                    <small class="text-danger">Tidak Ada Data yang Ditampilkan</small>
                </td>
            </tr>
            <script>
                $("#ButtonMulaiMigrasi2").prop("disabled", true);
                $("#page_info").html("Page : 0 / 0");
                $("#prev_button").prop("disabled", true);
                $("#next_button").prop("disabled", true);
                $("#metadata").html("");
            </script>
        ';
        exit;
    }

    $list_alat = [
        "expertisi BNO" => "XR",
        "expertisi thorax" => "XR",
        "expertisi thorax+BNO" => "XR",
        "expertisi thorax+BNO 3 posisi" => "XR",
        "expertisi thorax+Genue AP/Lat" => "XR",
        "ro" => "XR",
        "Roentgent" => "XR",
        "Roentgent0" => "XR",
        "Rongen" => "XR",
        "Rongent" => "XR",
        "Rontge" => "XR",
        "Rontgen" => "XR",
        "Rontgent" => "XR",
        "Rontgent6" => "XR",
        "Thorax" => "XR",
        "us" => "US",
        "USG" => "US",
         "expertisi Ct scan" => "CT",
    ];
    
    // Looping Data
    $no = 1 + $posisi;
    foreach($result['data'] as $row){

        // Mendefinisikan Alat Periksa
        $alat_pemeriksa = $row['alat_pemeriksa'];
        $modalitas_nama = $list_alat[$alat_pemeriksa] ?? 'XR';
        $waktu          = $row['waktu'] ?? '';
        $id_pasien      = $row['id_pasien'] ?? '';
        $id_kunjungan   = $row['id_kunjungan'] ?? '';
        $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT id_radiologi FROM radiologi WHERE id_pasien='$id_pasien' AND id_kunjungan='$id_kunjungan' AND datetime_diminta='$waktu' AND system_creator='SIMRS_V2'"));
        if(empty($jml_data)){
            $label_status = '<span class="badge bg-secondary"><i class="bi bi-x-circle"></i> None</span>';
        }else{
             
            $label_status = '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Done</span>';
        }
        echo '
            <tr>
                <td align="center">
                    <small>
                        <small class="text-dark">'.$no.'</small>
                    </small>
                </td>

                <td align="left">
                    <small>
                        <small class="text-dark"> '.$row['id_pasien'].'</small>
                    </small>
                </td>

                <td align="left">
                    <small>
                        <small class="text-dark">'.$row['id_kunjungan'].'</small>
                    </small>
                </td>

                <td align="left">
                    <small><small class="text-dark">'.$row['id_rad'].'</small></small>
                </td>

                <td align="left">
                    <small><small class="text-dark">'.$row['nama'].'</small></small>
                </td>

                <td align="left">
                    <small><small class="text-dark">'.$row['waktu'].'</small></small>
                </td>

                <td align="left">
                    <small><small class="text-dark">'.$modalitas_nama.'</small></small>
                </td>
                
                <td align="left">
                    <small><small class="text-dark">'.$row['permintaan_pemeriksaan'].'</small></small>
                </td>

                <td align="left">
                    <small><small class="text-dark">'.$row['dokter_pengirim'].'</small></small>
                </td>

                <td align="left">
                    <small><small class="text-dark">'.$row['dokter_penerima'].'</small></small>
                </td>

                <td align="left">
                    <small><small class="text-dark">'.$row['tujuan_kunjungan'].'</small></small>
                </td>

                <td align="center">
                    <small><small class="text-dark">'.$label_status.'</small></small>
                </td>
            </tr>
        ';
        $no++;
        echo '
            <input type="hidden" name="id_rad[]" value="'.$row['id_rad'].'">
            <input type="hidden" name="id_pasien[]" value="'.$row['id_pasien'].'">
            <input type="hidden" name="id_kunjungan[]" value="'.$row['id_kunjungan'].'">
            <input type="hidden" name="waktu[]" value="'.$row['waktu'].'">
            <input type="hidden" name="nama[]" value="'.$row['nama'].'">
            <input type="hidden" name="asal_kiriman[]" value="'.$row['asal_kiriman'].'">
            <input type="hidden" name="alat_pemeriksa[]" value="'.$modalitas_nama.'">
            <input type="hidden" name="permintaan_pemeriksaan[]" value="'.$row['permintaan_pemeriksaan'].'">
            <input type="hidden" name="dokter_pengirim[]" value="'.$row['dokter_pengirim'].'">
            <input type="hidden" name="dokter_penerima[]" value="'.$row['dokter_penerima'].'">
            <input type="hidden" name="radiografer[]" value="'.$row['radiografer'].'">
            <input type="hidden" name="kesan[]" value="'.$row['kesan'].'">
            <input type="hidden" name="klinis[]" value="'.$row['klinis'].'">
            <input type="hidden" name="jenis_pembayaran[]" value="'.$row['jenis_pembayaran'].'">
            <input type="hidden" name="tujuan_kunjungan[]" value="'.$row['tujuan_kunjungan'].'">
            <input type="hidden" name="selesai[]" value="'.$row['selesai'].'">
            <input type="hidden" name="kv[]" value="'.$row['kv'].'">
            <input type="hidden" name="ma[]" value="'.$row['ma'].'">
            <input type="hidden" name="sec[]" value="'.$row['sec'].'">
        ';
    }

    if($page==1){
        $prev_button = "true";
    }else{
        $prev_button = "false";
    }
    if($page>=$total_pages){
        $next_button = "true";
    }else{
        $next_button = "false";
    }
    echo '
        <script>
            $("#ButtonMulaiMigrasi2").prop("disabled", false);
            $("#page_info").html("Page : '.$page.' / '.$total_pages.'");
            $("#prev_button").prop("disabled", '.$prev_button.');
            $("#next_button").prop("disabled", '.$next_button.');
            $("#metadata").html("Total : '.$total_data.' | Short By : '.$short_by.' | Order By : '.$order_by.'");
        </script>
    ';
?>