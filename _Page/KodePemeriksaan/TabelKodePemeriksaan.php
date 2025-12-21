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
                <td colspan="7" class="text-center">
                    <small class="text-danger">Sesi Akses Sudah Berakhir! Silahkan Login Ulang!</small>
                </td>
            </tr>
            <script>
                $("#page_info").html("Page : 0 / 0");
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
        $OrderBy="id_master_pemeriksaan";
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
            $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT id_master_pemeriksaan FROM master_pemeriksaan "));
        }else{
            $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT id_master_pemeriksaan FROM master_pemeriksaan WHERE nama_pemeriksaan like '%$keyword%' OR modalitas like '%$keyword%' OR pemeriksaan_code like '%$keyword%' OR pemeriksaan_description like '%$keyword%' OR bodysite_code like '%$keyword%' OR bodysite_description like '%$keyword%'"));
        }
    }else{
        if(empty($keyword)){
            $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT id_master_pemeriksaan FROM master_pemeriksaan "));
        }else{
            $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT id_master_pemeriksaan FROM master_pemeriksaan WHERE $keyword_by like '%$keyword%'"));
        }
    }
    //Mengatur Halaman
    $JmlHalaman = ceil($jml_data/$batas); 
    if(empty($jml_data)){
        echo '
            <tr>
                <td colspan="7" class="text-center">
                    <small class="text-danger">Tidak Ada Data Yang Ditemukan!</small>
                </td>
            </tr>
            <script>
                $("#page_info").html("Page : 0 / 0");
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
            $query = mysqli_query($Conn, "SELECT*FROM master_pemeriksaan ORDER BY $OrderBy $ShortBy LIMIT $posisi, $batas");
        }else{
            $query = mysqli_query($Conn, "SELECT*FROM master_pemeriksaan WHERE nama_pemeriksaan like '%$keyword%' OR modalitas like '%$keyword%' OR pemeriksaan_code like '%$keyword%' OR pemeriksaan_description like '%$keyword%' OR bodysite_code like '%$keyword%' OR bodysite_description like '%$keyword%' ORDER BY $OrderBy $ShortBy LIMIT $posisi, $batas");
        }
    }else{
        if(empty($keyword)){
            $query = mysqli_query($Conn, "SELECT*FROM master_pemeriksaan ORDER BY $OrderBy $ShortBy LIMIT $posisi, $batas");
        }else{
            $query = mysqli_query($Conn, "SELECT*FROM master_pemeriksaan WHERE $keyword_by like '%$keyword%' ORDER BY $OrderBy $ShortBy LIMIT $posisi, $batas");
        }
    }
    while ($data = mysqli_fetch_array($query)) {
        $id_master_pemeriksaan   = $data['id_master_pemeriksaan'];
        $nama_pemeriksaan        = $data['nama_pemeriksaan'];
        $modalitas               = $data['modalitas'];
        $pemeriksaan_code        = $data['pemeriksaan_code'];
        $pemeriksaan_description = $data['pemeriksaan_description'];
        $bodysite_description    = $data['bodysite_description'];
       
        echo '
            <tr>
                <td><small>'.$no.'</small></td>
                <td>
                    <a href="javascript:void(0);" class="modal_detail" data-id="'.$id_master_pemeriksaan .'" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Lihat Detail Kode Pemeriksaan">
                        <small class="underscore_doted">'.$nama_pemeriksaan.'</small>
                    </a>
                </td>
                <td><small>'.$modalitas.'</small></td>
                <td><small><i>'.$pemeriksaan_code.'</i></small></td>
                <td><small><i>'.$pemeriksaan_description.'</i></small></td>
                <td><small>'.$bodysite_description.'</small></td>
                <td>
                    <button type="button" class="btn btn-sm btn-outline-dark btn-floating"  data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="">
                        <li class="dropdown-header text-start">
                            <h6>Option</h6>
                        </li>
                        <li>
                            <a class="dropdown-item modal_detail" href="javascript:void(0)" data-id="'.$id_master_pemeriksaan .'">
                                <i class="bi bi-info-circle"></i> Detail
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item modal_edit" href="javascript:void(0)" data-id="'.$id_master_pemeriksaan .'">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item modal_delete" href="javascript:void(0)" data-id="'.$id_master_pemeriksaan .'">
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
</script>