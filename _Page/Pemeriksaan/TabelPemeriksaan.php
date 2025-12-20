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
                <td colspan="11" class="text-center">
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
            $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT id_radiologi FROM radiologi WHERE id_pasien like '%$keyword%' OR nama_pasien like '%$keyword%' OR asal_kiriman like '%$keyword%' OR alat_pemeriksa like '%$keyword%' OR radiografer like '%$keyword%' OR tujuan like '%$keyword%' OR tujuan like '%$pembayaran%' OR tujuan like '%$status_pemeriksaan%'"));
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
                <td colspan="11" class="text-center">
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
            $query = mysqli_query($Conn, "SELECT*FROM radiologi ORDER BY $OrderBy $ShortBy LIMIT $posisi, $batas");
        }else{
            $query = mysqli_query($Conn, "SELECT*FROM radiologi WHERE id_pasien like '%$keyword%' OR nama_pasien like '%$keyword%' OR asal_kiriman like '%$keyword%' OR alat_pemeriksa like '%$keyword%' OR radiografer like '%$keyword%' OR tujuan like '%$keyword%' OR tujuan like '%$pembayaran%' OR tujuan like '%$status_pemeriksaan%' ORDER BY $OrderBy $ShortBy LIMIT $posisi, $batas");
        }
    }else{
        if(empty($keyword)){
            $query = mysqli_query($Conn, "SELECT*FROM radiologi ORDER BY $OrderBy $ShortBy LIMIT $posisi, $batas");
        }else{
            $query = mysqli_query($Conn, "SELECT*FROM radiologi WHERE $keyword_by like '%$keyword%' ORDER BY $OrderBy $ShortBy LIMIT $posisi, $batas");
        }
    }
    while ($data = mysqli_fetch_array($query)) {
        $id_radiologi       = $data['id_radiologi'];
        $id_access          = $data['id_access'];
        $id_pasien          = $data['id_pasien'];
        $id_kunjungan       = $data['id_kunjungan'];
        $accession_number   = $data['accession_number'];
        $nama_pasien        = $data['nama_pasien'];
        $asal_kiriman       = $data['asal_kiriman'];
        $alat_pemeriksa     = $data['alat_pemeriksa'];
        $radiografer        = $data['radiografer'];
        $tujuan             = $data['tujuan'];
        $pembayaran         = $data['pembayaran'];
        $datetime_diminta   = $data['datetime_diminta'];
        $status_pemeriksaan = $data['status_pemeriksaan'];
        $datetime_diminta_format = date('d/m/Y H:i', strtotime($datetime_diminta));
       
        echo '
            <tr>
                <td><small>'.$no.'</small></td>
                <td>
                    <a href="javascript:void(0);" class="modal_detail" data-id="'.$id_access .'">
                        <small class="underscore_doted">'.$id_pasien.'</small>
                    </a>
                </td>
                <td><small>'.$nama_pasien.'</small></td>
                <td><small>'.$datetime_diminta_format.'</small></td>
                <td><small>'.$tujuan.'</small></td>
                <td><small>'.$pembayaran.'</small></td>
                <td><small>'.$asal_kiriman.'</small></td>
                <td><small>'.$alat_pemeriksa.'</small></td>
                <td><small>'.$radiografer.'</small></td>
                <td><small>'.$status_pemeriksaan.'</small></td>
                <td>
                    <button type="button" class="btn btn-sm btn-outline-dark btn-floating"  data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="">
                        <li class="dropdown-header text-start">
                            <h6>Option</h6>
                        </li>
                        <li>
                            <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#ModalDetailAkses" data-id="'.$id_radiologi .'">
                                <i class="bi bi-info-circle"></i> Detail
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#ModalEditAkses" data-id="'.$id_radiologi .'">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#ModalHapusAkses" data-id="'.$id_radiologi .'">
                                <i class="bi bi-x"></i> Hapus
                            </a>
                        </li>
                    </ul>
                </td>
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
    $('#page_info').html(''+curent_page+' / '+page_count+'');
    
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
</script>