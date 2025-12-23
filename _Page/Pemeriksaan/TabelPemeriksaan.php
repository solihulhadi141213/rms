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
            $query = mysqli_query($Conn, "SELECT id_radiologi, id_pasien, id_kunjungan, id_service_request, nama_pasien, priority, asal_kiriman, alat_pemeriksa, radiografer, tujuan, pembayaran, datetime_diminta, status_pemeriksaan  FROM radiologi ORDER BY $OrderBy $ShortBy LIMIT $posisi, $batas");
        }else{
            $query = mysqli_query($Conn, "SELECT id_radiologi, id_pasien, id_kunjungan, id_service_request, nama_pasien, priority, asal_kiriman, alat_pemeriksa, radiografer, tujuan, pembayaran, datetime_diminta, status_pemeriksaan FROM radiologi WHERE id_pasien like '%$keyword%' OR nama_pasien like '%$keyword%' OR asal_kiriman like '%$keyword%' OR alat_pemeriksa like '%$keyword%' OR radiografer like '%$keyword%' OR tujuan like '%$keyword%' OR tujuan like '%$pembayaran%' OR tujuan like '%$status_pemeriksaan%' ORDER BY $OrderBy $ShortBy LIMIT $posisi, $batas");
        }
    }else{
        if(empty($keyword)){
            $query = mysqli_query($Conn, "SELECT id_radiologi, id_pasien, id_kunjungan, id_service_request, nama_pasien, priority, asal_kiriman, alat_pemeriksa, radiografer, tujuan, pembayaran, datetime_diminta, status_pemeriksaan FROM radiologi ORDER BY $OrderBy $ShortBy LIMIT $posisi, $batas");
        }else{ 
            $query = mysqli_query($Conn, "SELECT id_radiologi, id_pasien, id_kunjungan, id_service_request, nama_pasien, priority, asal_kiriman, alat_pemeriksa, radiografer, tujuan, pembayaran, datetime_diminta, status_pemeriksaan FROM radiologi WHERE $keyword_by like '%$keyword%' ORDER BY $OrderBy $ShortBy LIMIT $posisi, $batas");
        }
    }
    while ($data = mysqli_fetch_array($query)) {
        $id_radiologi       = $data['id_radiologi'];
        $id_pasien          = $data['id_pasien'];
        $id_kunjungan       = $data['id_kunjungan'];
        $id_service_request = $data['id_service_request'];
        $nama_pasien        = $data['nama_pasien'];
        $priority           = $data['priority'];
        $asal_kiriman       = $data['asal_kiriman'];
        $alat_pemeriksa     = $data['alat_pemeriksa'];
        $radiografer        = $data['radiografer'];
        $tujuan             = $data['tujuan'];
        $pembayaran         = $data['pembayaran'];
        $datetime_diminta   = $data['datetime_diminta'];
        $status_pemeriksaan = $data['status_pemeriksaan'];
        $tanggal            = date('d/m/Y', strtotime($datetime_diminta));
        $jam                = date('H:i', strtotime($datetime_diminta));

        if(empty($data['radiografer'])){
            $radiografer = "-";
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

        // Ambil nama modalitas
        $modalitas_nama = $nama_modalitas[$alat_pemeriksa] ?? '-';

        //Routing Status
        if($status_pemeriksaan=="Diminta"){
            $label_status = '<span class="badge bg-warning">Diminta</span>';
        }else{
            if($status_pemeriksaan=="Dikerjakan"){
                $label_status = '<span class="badge bg-info">Dikerjakan</span>';
            }else{
                if($status_pemeriksaan=="Hasil"){
                    $label_status = '<span class="badge bg-primary">Hasil</span>';
                }else{
                    if($status_pemeriksaan=="Selesai"){
                        $label_status = '<span class="badge bg-success">Selesai</span>';
                    }else{
                        if($status_pemeriksaan=="Batal"){
                            $label_status = '<span class="badge bg-danger">Batal</span>';
                        }else{
                            $label_status = '<span class="badge bg-dark">None</span>';
                        }
                    }
                }
            }
        }

        // Routing Service Request
        if(empty($id_service_request)){
            $sr = '<span class="text-danger"><i class="bi bi-x-circle"></i></span>';
        }else{
            $sr = '<span class="text-success"><i class="bi bi-check-circle"></i></span>';
        }

        //Routing Periority
        if($priority=="routine"){
            $priority_label='bg-success';
        }else{
            if($priority=="urgent"){
                $priority_label='bg-warning';
            }else{
                if($priority=="stat"){
                    $priority_label='bg-danger';
                }else{
                    $priority_label='bg-dark';
                }
            }
        }
        //klasifikasi prioritas
        $priority_list = [
            'routine' => 'Biasa',
            'urgent'  => 'Segera',
            'stat'    => 'Gawat'
        ];
        $priority_name = $priority_list[$priority] ?? '-';
       
        echo '
            <tr>
                <td><small>'.$no.'</small></td>
                <td><small><small>'.$id_pasien.'</small></small></td>
                <td><small><small>'.$id_kunjungan.'</small></small></td>
                <td>
                    <a href="javascript:void(0);" class="modal_detail" data-id="'.$id_radiologi .'" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Lihat Detail Pemeriksaan">
                        <small>
                            <small class="underscore_doted">'.$nama_pasien.'</small>
                        </small>
                    </a>
                </td>
                <td><small><small>'.$tanggal.'</small></small></td>
                <td><small><small>'.$jam.'</small></small></td>
                <td><small><small>'.$tujuan.'</small></small></td>
                <td><small><small>'.$pembayaran.'</small></small></td>
                <td><small><small>'.$asal_kiriman.'</small></small></td>
                <td><small><small>'.$modalitas_nama.'</small></small></td>
                <td><small><small>'.$radiografer.'</small></small></td>
                <td><small>'.$label_status.'</small></td>
                <td class="text-center"><small>'.$sr.'</small></td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-dark btn-floating"  data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-three-dots-vertical"></i>
                        <span class="position-absolute top-0 start-100 translate-middle p-2 '.$priority_label.' border border-light rounded-circle" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="'.$priority_name.'">
                            <span class="visually-hidden">New alerts</span>
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="">
                        <li class="dropdown-header text-start">
                            <h6>Option</h6>
                        </li>
                        <li>
                            <a class="dropdown-item modal_detail" href="javascript:void(0)" data-id="'.$id_radiologi .'">
                                <i class="bi bi-info-circle"></i> Detail
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item modal_edit" href="javascript:void(0)" data-id="'.$id_radiologi .'">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item modal_hapus" href="javascript:void(0)" data-id="'.$id_radiologi .'">
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