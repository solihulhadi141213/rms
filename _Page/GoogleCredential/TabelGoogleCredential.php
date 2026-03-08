<?php
    //koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";
    date_default_timezone_set("Asia/Jakarta");
    
    //Validasi Akses
    if(empty($SessionIdAccess)){
        echo '
            <tr>
                <td colspan="6" class="text-center">
                    <small class="text-danger">Sesi Akses Sudah Berakhir! Silahkan Login Ulang!</small>
                </td>
            </tr>
        ';
        exit;
    }
    $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT id_google_credential FROM google_credential"));
    if(empty($jml_data)){
        echo '
            <tr>
                <td colspan="6" class="text-center">
                    <small class="text-danger">Belum ada data pengaturan <i>Google Credential</i> Yang Disimpan</small>
                </td>
            </tr>
        ';
        exit;
    }
    $no = 1;
    $query = mysqli_query($Conn, "SELECT*FROM google_credential");
    while ($data = mysqli_fetch_array($query)) {
        $id_google_credential = $data['id_google_credential'];
        $credential_env       = $data['credential_env'];
        $client_id            = $data['client_id'];
        $client_secret        = $data['client_secret'];
        $status               = $data['status'];

        // Routing Status
        if(empty($data['status'])){
            $label_status = '
                <a href="javascript:void(0);" class="modal_update_status" data-id="'.$id_google_credential.'" data-status="1">
                    <span class="badge bg-dark">Inactive</span>
                </a>
            ';
        }else{
            $label_status = '
                <a href="javascript:void(0);" class="modal_update_status" data-id="'.$id_google_credential.'" data-status="0">
                    <span class="badge bg-success">Active</span>
                </a>
            ';
        }
        $client_secret = potong8Karakter($client_secret);
        echo '
            <tr>
                <td class="text-center"><small>'.$no.'</small></td>
                <td class="text-left"><small>'.$credential_env.'</small></td>
                <td class="text-left">
                    <small>
                        <code class="text text-grayish">'.$client_id.'</code>
                    </small>
                </td>
                <td class="text-left">
                    <a href="javascript:void(0);" class="modal_lihat_client_secret" data-id="'.$id_google_credential.'">
                        <small><i class="bi bi-eye"></i> '.$client_secret.'***</small>
                    </a>
                </td>
                <td class="text-center">'.$label_status.'</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-dark btn-floating"  data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="">
                        <li class="dropdown-header text-start">
                            <h6>Option</h6>
                        </li>
                        <li>
                            <a class="dropdown-item modal_lihat_client_secret" href="javascript:void(0)" data-id="'.$id_google_credential.'">
                                <i class="bi bi-eye"></i> Lihat Client Sceret
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item modal_edit" href="javascript:void(0)" data-id="'.$id_google_credential.'">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item modal_hapus" href="javascript:void(0)" data-id="'.$id_google_credential.'">
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
    var jml_data="<?php echo $jml_data; ?>";
    
    //Put Into Pagging Element
    $('#page_info').html('Total Data : '+jml_data+'');
    
</script>