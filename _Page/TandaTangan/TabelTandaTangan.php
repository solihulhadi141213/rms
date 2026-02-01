<?php
    //koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    $jml_data = 0;
    //Zona Waktu
    date_default_timezone_set("Asia/Jakarta");

    //Validasi Akses
    if(empty($SessionIdAccess)){
        echo '
            <tr>
                <td colspan="6" class="text-center">
                    <small class="text-danger">Sesi Akses Sudah Berakhir! Silahkan Login Ulang!</small>
                </td>
            </tr>
            <script>
                $("#page_info").html("Jumlah Data : '.$jml_data.'");
            </script>
        ';
        exit;
    }
    $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT id_master_signature FROM master_signature WHERE delete_at IS NULL"));
    
    // Jika Data Tidak Ada
    if(empty($jml_data)){
        echo '
            <tr>
                <td colspan="6" class="text-center">
                    <small class="text-danger">Tidak Ada Data Yang Ditemukan!</small>
                </td>
            </tr>
           <script>
                $("#page_info").html("Jumlah Data : '.$jml_data.'");
            </script>
        ';
        exit;
    }
    $no = 1;
    //KONDISI PENGATURAN MASING FILTER
    $query = mysqli_query($Conn, "SELECT id_master_signature, kode, ihs, nama, kategori FROM master_signature WHERE delete_at IS NULL ORDER BY id_master_signature DESC");
    while ($data = mysqli_fetch_array($query)) {
        $id_master_signature = $data['id_master_signature'];
        $kode                = (!empty($data['kode'])) ? $data['kode'] : '-';
        $ihs                 = (!empty($data['ihs'])) ? $data['ihs'] : '-';
        $nama                = $data['nama'];
        $kategori            = $data['kategori'];
       
        echo '
            <tr>
                <td><small>'.$no.'</small></td>
                <td>
                    <a href="javascript:void(0);" class="modal_detail" data-id="'.$id_master_signature .'" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Lihat Detail Tanda Tangan">
                        <small class="underscore_doted">'.$nama.'</small>
                    </a>
                </td>
                <td><small>'.$kategori.'</small></td>
                <td><small>'.$ihs.'</small></td>
                <td><small>'.$kode.'</small></td>
                <td>
                    <button type="button" class="btn btn-sm btn-outline-dark btn-floating"  data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="">
                        <li class="dropdown-header text-start">
                            <h6>Option</h6>
                        </li>
                        <li>
                            <a class="dropdown-item modal_detail" href="javascript:void(0)" data-id="'.$id_master_signature .'">
                                <i class="bi bi-info-circle"></i> Detail
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item modal_edit" href="javascript:void(0)" data-id="'.$id_master_signature .'">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item modal_delete" href="javascript:void(0)" data-id="'.$id_master_signature .'">
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
    var jml_data  = <?php echo $jml_data; ?>;
    
    $('#page_info').html('Jumlah Data : '+jml_data+'');
</script>