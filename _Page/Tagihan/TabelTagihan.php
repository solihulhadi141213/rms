<?php
    //koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    //Zona Waktu
    date_default_timezone_set("Asia/Jakarta");


    $JmlHalaman = 0;
    $page       = 0;
    //Validasi Akses
    if(empty($SessionIdAccess)){
        echo '
            <tr>
                <td colspan="13" class="text-center">
                    <small class="text-danger">Sesi Akses Sudah Berakhir! Silahkan Login Ulang!</small>
                </td>
            </tr>
            <script>
                $("#page_info").html("0 / 0");
                $("#prev_button").prop("disabled", true);
                $("#next_button").prop("disabled", true);
            </script>
        ';
        exit;
    }
    //Keyword_by
    if(!empty($_POST['keyword_by'])){
        $keyword_by=$_POST['keyword_by'];
    }else{
        $keyword_by="";
    }
    //keyword
    if(!empty($_POST['keyword'])){
        $keyword=$_POST['keyword'];
    }else{
        $keyword="";
    }
    //batas
    if(!empty($_POST['batas'])){
        $batas=$_POST['batas'];
    }else{
        $batas="10";
    }
    //ShortBy
    if(!empty($_POST['ShortBy'])){
        $ShortBy=$_POST['ShortBy'];
    }else{
        $ShortBy="DESC";
    }
    //OrderBy
    if(!empty($_POST['OrderBy'])){
        $OrderBy=$_POST['OrderBy'];
    }else{
        $OrderBy="id_radiologi";
    }
    //Atur Page
    if(!empty($_POST['page'])){
        $page=$_POST['page'];
        $posisi = ( $page - 1 ) * $batas;
    }else{
        $page="1";
        $posisi = 0;
    }
    if(empty($keyword_by)){
        if(empty($keyword)){
            $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT id_radiologi FROM radiologi "));
        }else{
            $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT id_radiologi FROM radiologi WHERE id_pasien like '%$keyword%' OR nama_pasien like '%$keyword%'"));
        }
    }else{
        if(empty($keyword)){
            $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT id_radiologi FROM radiologi "));
        }else{
            $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT id_radiologi FROM radiologi WHERE $keyword_by like '%$keyword%'"));
        }
    }
    //Mengatur Halaman
    $JmlHalaman = ceil($jml_data/$batas); 
    if(empty($jml_data)){
        echo '
            <tr>
                <td colspan="13" class="text-center">
                    <small class="text-danger">Tidak Ada Data Yang Ditemukan!</small>
                </td>
            </tr>
            <script>
                $("#page_info").html("0 / 0");
                $("#prev_button").prop("disabled", true);
                $("#next_button").prop("disabled", true);
            </script>
        ';
        exit;
    }
    $no = 1+$posisi;
    //KONDISI PENGATURAN MASING FILTER
    if(empty($keyword_by)){
        if(empty($keyword)){
            $query = mysqli_query($Conn, "SELECT id_radiologi, id_pasien, nama_pasien, alat_pemeriksa, tujuan, pembayaran, datetime_diminta, status_pemeriksaan  FROM radiologi ORDER BY $OrderBy $ShortBy LIMIT $posisi, $batas");
        }else{
            $query = mysqli_query($Conn, "SELECT id_radiologi, id_pasien, nama_pasien, alat_pemeriksa, tujuan, pembayaran, datetime_diminta, status_pemeriksaan FROM radiologi id_pasien like '%$keyword%' OR nama_pasien like '%$keyword%' ORDER BY $OrderBy $ShortBy LIMIT $posisi, $batas");
        }
    }else{
        if(empty($keyword)){
            $query = mysqli_query($Conn, "SELECT id_radiologi, id_pasien, nama_pasien, alat_pemeriksa, tujuan, pembayaran, datetime_diminta, status_pemeriksaan FROM radiologi ORDER BY $OrderBy $ShortBy LIMIT $posisi, $batas");
        }else{ 
            $query = mysqli_query($Conn, "SELECT id_radiologi, id_pasien, nama_pasien, alat_pemeriksa, tujuan, pembayaran, datetime_diminta, status_pemeriksaan FROM radiologi WHERE $keyword_by like '%$keyword%' ORDER BY $OrderBy $ShortBy LIMIT $posisi, $batas");
        }
    }
    while ($data = mysqli_fetch_array($query)) {
        $id_radiologi         = $data['id_radiologi'];
        $id_pasien            = $data['id_pasien'];
        $nama_pasien          = $data['nama_pasien'];
        $alat_pemeriksa       = $data['alat_pemeriksa'];
        $tujuan               = $data['tujuan'];
        $pembayaran           = $data['pembayaran'];
        $datetime_diminta     = $data['datetime_diminta'];
        $status_pemeriksaan   = $data['status_pemeriksaan'];
        $tanggal              = date('d/m/y', strtotime($datetime_diminta));

        //Routing pembayaran
        if($pembayaran=="UMUM"){
            $pembayaran_code = "UMUM";
            $labal_pembyaran = "text-dark";
        }else{
            $pembayaran_code = "$pembayaran";
            $labal_pembyaran = "text-grayish";
        }

        // Nama Modalitas
        $nama_modalitas = [
            'XR' => 'X-Ray',
            'CT' => 'CT-Scan',
            'US' => 'USG',
            'MR' => 'MRI',
            'NM' => 'Nuclear Medicine (Kedokteran Nuklir)',
            'PT' => 'PET Scan',
            'DX' => 'Digital Radiography',
            'CR' => 'Computed Radiography'
        ];

        $warna_modalitas = [
            'XR' => 'primary',
            'CT' => 'secondary',
            'US' => 'warning',
            'MR' => 'info',
            'NM' => 'danger',
            'PT' => 'dark',
            'DX' => 'success',
            'CR' => 'dark'
        ];

        // Ambil nama modalitas
        $modalitas_nama = $nama_modalitas[$alat_pemeriksa] ?? '-';
        $modalitas_warna = $warna_modalitas[$alat_pemeriksa] ?? '-';

        // Routing Label Status
        $map_status = [
            'diminta'     => 'REQ',
            'dikerjakan'  => 'PRC',
            'hasil'       => 'RES',
            'selesai'     => 'DON',
            'batal'       => 'CAN'
        ];
        $badge_status = [
            'REQ'  => 'secondary',
            'PRC' => 'warning',
            'RES'  => 'info',
            'DON' => 'success',
            'CAN' => 'danger',
            'UNK'  => 'dark'
        ];

        $key_status   = strtolower(trim($status_pemeriksaan));
        $label_status = $map_status[$key_status] ?? 'UNK';
        $badge_class  = $badge_status[$label_status] ?? 'dark';
        
        // Labeling tujuan
        if($tujuan=="Rajal"){
            $labal_tujuan = 'text-success';
        }else{
            $labal_tujuan = 'text-warning';
        }

        // Menghitung Jumlah Tagihan
        $total   = 0;
        $query_nota = mysqli_query($Conn, "SELECT amount FROM radiologi_invoice WHERE id_radiologi='$id_radiologi'");
        while ($data_nota = mysqli_fetch_array($query_nota)) {
            $amount = $data_nota['amount'];
            $total  = $total + $amount;
        }
        $total_format = "Rp " . number_format($total,0,',','.');

        if(empty($total)){
            $total_format = '<span class="text text-grayish">'.$total_format.'</span>';
        }else{
            $total_format = '<span class="text text-dark">'.$total_format.'</span>';
        }
       
        echo '
            <tr class="modal_detail" data-id="'.$id_radiologi .'">
                <td><small>'.$no.'</small></td>
                <td><small>'.$nama_pasien.'</small></td>
                <td><small>'.$id_pasien.'</small></td>
                <td><small>'.$tanggal.'</small></td>
                <td class="text-left">
                    <small class="'.$labal_tujuan.'">
                        '.$tujuan.'
                    </small>
                </td>
                <td class="text-left">
                    <span class="badge badge-'.$modalitas_warna.'" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="'.$alat_pemeriksa.'">
                        '.$modalitas_nama.'
                    </span>
                </td>
                <td class="text-left">
                    <small class="text '.$labal_pembyaran.'" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="'.$pembayaran.'">
                        '.$pembayaran_code.'
                    </small>
                </td>
                <td class="text-left">
                    <span class="badge bg-'.$badge_class.'" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="'.$status_pemeriksaan.'">
                        '.$label_status.'
                    </span>
                </td>
                <td><small>'.$total_format.'</small></td>
            </tr>
        ';
        $no++;
    }
?>
<script>
    //Creat Javascript Variabel
    var page_count  = <?php echo $JmlHalaman; ?>;
    var curent_page = <?php echo $page; ?>;
    
    //Put Into Pagging Element
    $('#page_info').html('Page : '+curent_page+' / '+page_count+'');
    
    //Set Pagging Button
    if(curent_page==1){
        $('#prev_button').prop('disabled', true);
    }else{
        $('#prev_button').prop('disabled', false);
    }
    if(page_count<=curent_page){
        $('#next_button').prop('disabled', true);
    }else{
        $('#next_button').prop('disabled', false);
    }

    hideFloatingOption();
</script>