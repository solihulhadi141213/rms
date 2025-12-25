<?php
    // ===============================
    // KONEKSI & SESSION
    // ===============================
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    date_default_timezone_set("Asia/Jakarta");

    // ===============================
    // CEK SESSION
    // ===============================
    if(empty($SessionIdAccess)){
        echo '
            <tr>
                <td colspan="10" class="text-center">
                    <small class="text-danger">Sesi Akses Sudah Berakhir! Silahkan Login Ulang!</small>
                </td>
            </tr>
            <script>
                $("#page_info_kunjungan").html("0 / 0");
                $("#prev_button_kunjungan").prop("disabled", true);
                $("#next_button_kunjungan").prop("disabled", true);
            </script>
        ';
        exit;
    }

    // ===============================
    // AMBIL URL SIMRS
    // ===============================
    $status_connection_simrs = 1;
    $url_connection_simrs = GetDetailData($Conn,'connection_simrs','status_connection_simrs',$status_connection_simrs,'url_connection_simrs');

    // ===============================
    // GET TOKEN SIMRS
    // ===============================
    $token = GetSimrsToken($Conn);

    if ($token === false) {
        echo '
            <tr>
                <td colspan="10" class="text-center">
                    <small class="text-danger">Gagal mendapatkan token SIMRS!</small>
                </td>
            </tr>
        ';
        exit;
    }

    // ===============================
    // PARAMETER FILTER
    // ===============================
    $keyword_by = $_POST['keyword_by'] ?? "";
    $keyword    = $_POST['keyword'] ?? "";
    $order_by   = $_POST['order_by'] ?? "id_kunjungan";
    $page       = $_POST['page'] ?? 1;
    $limit      = $_POST['limit'] ?? 10;
    $short_by   = $_POST['short_by'] ?? "DESC";

    // ===============================
    // REQUEST API KUNJUNGAN
    // ===============================
    $payload = json_encode([
        "page"       => (string)$page,
        "limit"      => (string)$limit,
        "short_by"   => $short_by,
        "order_by"   => $order_by,
        "keyword_by" => $keyword_by,
        "keyword"    => $keyword
    ]);

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url_connection_simrs . '/API/SIMRS/kunjungan.php',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => [
            'token: ' . $token,
            'Content-Type: application/json'
        ],
        CURLOPT_TIMEOUT => 15
    ]);

    $response = curl_exec($curl);
    curl_close($curl);

    // ===============================
    // DECODE RESPONSE
    // ===============================
    $data = json_decode($response, true);

    if (
        empty($data['response']['code']) ||
        $data['response']['code'] != 200
    ) {
        echo '
            <tr>
                <td colspan="10" class="text-center">
                    <small class="text-danger">Gagal memuat data kunjungan</small>
                </td>
            </tr>
        ';
        exit;
    }

    // ===============================
    // META DATA
    // ===============================
    $meta            = $data['metadata'];
    $list_kunjungan  = $meta['list_kunjungan'] ?? [];
    $total_data      = $meta['jumlah_total_data'] ?? 0;
    $total_page      = $meta['jumlah_halaman'] ?? 0;
    $current_page    = $meta['curent_page'] ?? 1;

    // ===============================
    // JIKA DATA KOSONG
    // ===============================
    if (empty($list_kunjungan)) {
        echo '
            <tr>
                <td colspan="10" class="text-center">
                    <small class="text-muted">Tidak ada data kunjungan</small>
                </td>
            </tr>
        ';
    }

    // ===============================
    // LOOP DATA KUNJUNGAN
    // ===============================
    foreach ($list_kunjungan as $row) {

        $alamat            = $row['alamat']['desa'].' - '.$row['alamat']['kecamatan'];
        $encounter         = $row['id_encounter'];
        $display_encounter = empty($encounter) ? '-' : substr($encounter, 0, 8) . '..';

        echo '
            <tr>
                <td class="text-center"><small>'.$row['no_urut'].'</small></td>
                <td><small>'.$row['id_pasien'].'</small></td>
                <td><small>'.$row['nama'].'</small></td>
                <td><small>'.date('d/m/Y H:i T', strtotime($row['tanggal'])).'</small></td>
                <td><small>'.$row['tujuan'].'</small></td>
                <td><small>'.$row['poliklinik'].' / '.$row['ruangan'].'</small></td>
                <td><small>'.$display_encounter.'</small></td>
                <td><small>'.$row['status'].'</small></td>
                <td>
                    <button type="button" class="btn btn-sm btn-secondary btn-floating tambah_permintaan" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Buat Permintaan Radiologi" data-id="'.$row['id_kunjungan'].'">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </td>
            </tr>
        ';
    }

    // ===============================
    // UPDATE PAGINATION
    // ===============================
    echo '
    <script>
        $("#page_info_kunjungan").html("'.$current_page.' / '.$total_page.'");
        $("#prev_button_kunjungan").prop("disabled",'.$current_page.' <= 1);
        $("#next_button_kunjungan").prop("disabled",'.$current_page.' >= '.$total_page.');
    </script>
    ';
?>
