<?php
    //koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    //Zona Waktu
    date_default_timezone_set("Asia/Jakarta");

    //Session Akses
    if(empty($SessionIdAccess)){
        echo '
            <tr>
                <td colspan="8" class="text-center">
                    <small class="text-danger">Sesi Akses Sudah Berakhir! Silahkan Login Ulang!</small>
                </td>
            </tr>
        ';
        exit;
    }

    //Hitung Jumlah Data
    $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT id_api_account FROM api_account"));

    //Jika Tidak Ada Data Kelas
    if(empty($jml_data)){
        echo '
            <tr>
                <td colspan="6" class="text-center">
                    <small class="text-danger">Tidak ada data <b>koneksi API</b> yang ditampilkan</small>
                </td>
            </tr>
        ';
        exit;
    }

    //Tampilkan Data
    $no=1;
    $qry = mysqli_query($Conn, "SELECT * FROM api_account ORDER BY id_api_account DESC");
    while ($data = mysqli_fetch_array($qry)) {
        $id_api_account   = $data['id_api_account'];
        $api_name         = $data['api_name'];
        $base_url_api     = $data['base_url_api'];
        $username         = $data['username'];
        $created_at       = $data['created_at'];
        $duration_expired = $data['duration_expired'];

        // Potong hanya 10 karakter pertama lalu tambahkan ***
        $username_masked = substr($username, 0, 10) . '***';

        // ===============================
        // KONVERSI DURATION MILISECOND
        // ===============================
        if ($duration_expired >= 86400000) {
            // >= 1 hari
            $durasi_tampil = round($duration_expired / 86400000) . ' Hari';
        } elseif ($duration_expired >= 3600000) {
            // >= 1 jam
            $durasi_tampil = round($duration_expired / 3600000) . ' Jam';
        } else {
            // < 1 jam
            $durasi_tampil = round($duration_expired / 60000) . ' Menit';
        }

        //Tampilkan Data
        echo '
            <tr>
                <td class="text-center"><small>'.$no.'</small></td>
                <td>
                    <a href="javascript:void(0);" class="modal_detail" data-id="'.$id_api_account  .'" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Tambah Pengaturan Koneksi SIMRS">
                        <small class="text text-primary underscore_doted">
                            '.$api_name .'
                        </small>
                    </a>
                </td>
                <td>
                    <small>
                        <code class="text text-grayish">'.$base_url_api .'</code>
                    </small>
                </td>
                <td><small>'.$username_masked.'</small></td>
                <td><small>'.$durasi_tampil.'</small></td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-dark btn-floating"  data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow bg-body-secondary" style="">
                        <li class="dropdown-header text-center">
                            <h6>Option</h6>
                        </li>
                        <li><hr class="dropdown-divider border-1 border-bottom"></li>
                        <li>
                            <a class="dropdown-item modal_detail" href="javascript:void(0)" data-id="'.$id_api_account  .'">
                                <i class="bi bi-info-circle"></i> Detail
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item modal_edit" href="javascript:void(0)" data-id="'.$id_api_account  .'">
                                <i class="bi bi-pencil"></i> Edit API
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item modal_edit_password" href="javascript:void(0)" data-id="'.$id_api_account  .'">
                                <i class="bi bi-lock"></i> Edit password
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item modal_delete" href="javascript:void(0)" data-id="'.$id_api_account  .'">
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