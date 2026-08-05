<?php
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    date_default_timezone_set("Asia/Jakarta");

    /* ================= VALIDASI SESI ================= */
    if (empty($SessionIdAccess)) {
        echo '
            <div class="row mb-3">
                <div class="col-12 text-center">
                    <div class="alert alert-danger">
                        <small>Sesi Akses Sudah Berakhir! Silahkan Login Ulang.</small>
                    </div>
                </div>
            </div>
            <script>
                $("#button_export_rincian_durasi_pelayanan").prop("disabled", true);
            </script>
        ';
        exit;
    }

    /* ================= VALIDASI INPUT ================= */
    $periode = $_POST['periode'] ?? '';
    $tahun   = $_POST['tahun'] ?? '';
    $bulan   = $_POST['bulan'] ?? '';
    $tanggal = $_POST['tanggal'] ?? '';

    if (empty($periode)) {
        echo '
            <div class="alert alert-danger text-center">
                <small>Periode Data Belum Dipilih!</small>
            </div>
        ';
        exit;
    }

    if ($periode === 'Tahun' && empty($tahun) && empty($bulan)) {
        echo '
            <div class="alert alert-danger text-center">
                <small>Tahun dan Bulan Belum Dipilih!</small>
            </div>
        ';
        exit;
    }

    if ($periode === 'Bulan') {
        if(empty($tahun)){
            echo '
                <div class="alert alert-danger text-center">
                    <small>Untuk periode Bulan, Informasi Tahun tidak boleh kosong!</small>
                </div>
            ';
            exit;
        }
        if(empty($bulan)){
            echo '
                <div class="alert alert-danger text-center">
                    <small>Untuk periode Bulan, Informasi Bulan tidak boleh kosong!</small>
                </div>
            ';
            exit;
        }
        if(empty($tanggal)){
            echo '
                <div class="alert alert-danger text-center">
                    <small>Untuk periode Bulan, Informasi Tanggal tidak boleh kosong!</small>
                </div>
            ';
            exit;
        }
    }

    /* ================= FILTER KEYWORD ================= */
    if ($periode == "Tahun") {
        $keyword = $tahun . '-' . $bulan;
    } else {
        $keyword = $tanggal;
    }

    /* ================= QUERY DATA ================= */
    $query = mysqli_query(
        $Conn,
        "SELECT * FROM radiologi 
        WHERE datetime_diminta LIKE '%$keyword%' 
        ORDER BY id_radiologi ASC"
    );

    $jml_data = mysqli_num_rows($query);

    // if($periode=="Tahun"){
    //     $periode = "Bulan";
    // }
    // if($periode=="Bulan"){
    //     $periode = "Hari";
    // }
?>
<input type="hidden" name="periode" value="<?= $periode; ?>"> 
<input type="hidden" name="tahun" value="<?= $tahun; ?>"> 
<input type="hidden" name="bulan" value="<?= $bulan; ?>">
<input type="hidden" name="tanggal" value="<?= $tanggal; ?>">

<div class="row mb-3">
    <div class="col-12 text-center">
        <b>LAPORAN RINCIAN DURASI PELAYANAN</b><br>
        <small>
            <?php
                if($periode=="Tahun"){
                    echo 'Periode Bulan '.$bulan.' '.$tahun.'';
                }
                if($periode=="Bulan"){
                    echo 'Periode Tanggal  '.$tanggal.', '.$bulan.' '.$tahun.'';
                }
            ?>
        </small>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="table table-responsive">
            <table class="table table-bordered table-hover table-striped">
                <thead>
                    <tr>
                        <td class="text-center"><b>No</b></td>
                        <td class="text-left"><b>Nama Pasien</b></td>
                        <td class="text-left"><b>RM</b></td>
                        <td class="text-left"><b>Usia</b></td>
                        <td class="text-left"><b>DPJP</b></td>
                        <td class="text-left"><b>Modality</b></td>
                        <td class="text-left"><b>Kunjungan</b></td>
                        <td class="text-left"><b>Pembayaran</b></td>
                        <td class="text-left"><b>Diminta</b></td>
                        <td class="text-left"><b>Dikerjakan</b></td>
                        <td class="text-left"><b>Hasil</b></td>
                        <td class="text-left"><b>Selesai</b></td>
                        <td class="text-center"><b>Durasi</b></td>
                        <td class="text-center"><b>Radiografer</b></td>
                    </tr>
                </thead>
                <tbody>
                <?php
                if (empty($jml_data)) {
                    echo '
                        <tr>
                            <td colspan="14" class="text-center">
                                <small>Tidak Ada Data Yang Ditemukan</small>
                            </td>
                        </tr>
                    ';
                } else {
                    $no = 1;
                    while ($data = mysqli_fetch_array($query)) {

                        $id_access            = $data['id_access'];
                        $nama_dokter_pengirim = $data['nama_dokter_pengirim'];
                        $datetime_diminta     = $data['datetime_diminta'];
                        $tanggal_lahir        = $data['tanggal_lahir'];
                        $usia                 = "-";

                        // Menghitung Usia
                        if (!empty($tanggal_lahir)) {

                            $tglLahir = new DateTime($tanggal_lahir);
                            $tglHitung = new DateTime($datetime_diminta);

                            // Pastikan tanggal lahir tidak lebih besar dari tanggal perhitungan
                            if ($tglLahir <= $tglHitung) {

                                $diff = $tglLahir->diff($tglHitung);

                                if ($diff->y >= 1) {
                                    // >= 1 Tahun
                                    $usia = $diff->y . " Tahun";

                                } elseif ($diff->m >= 1) {
                                    // < 1 Tahun tetapi >= 1 Bulan
                                    $usia = $diff->m . " Bulan";

                                } else {
                                    // < 1 Bulan
                                    $usia = $diff->d . " Hari";
                                }
                            }
                        }

                        /* ===== FORMAT TANGGAL ===== */
                        $diminta    = empty($data['datetime_diminta']) ? '-' : date('d/m/Y H:i', strtotime($data['datetime_diminta']));
                        $dikerjakan = empty($data['datetime_dikerjakan']) ? '-' : date('d/m/Y H:i', strtotime($data['datetime_dikerjakan']));
                        $hasil      = empty($data['datetime_hasil']) ? '-' : date('d/m/Y H:i', strtotime($data['datetime_hasil']));
                        $selesai    = empty($data['datetime_selesai']) ? '-' : date('d/m/Y H:i', strtotime($data['datetime_selesai']));

                        /* ===== HITUNG DURASI ===== */
                        if (!empty($data['datetime_diminta']) && !empty($data['datetime_selesai'])) {
                            $start  = strtotime($data['datetime_diminta']);
                            $end    = strtotime($data['datetime_selesai']);
                            $menit  = floor(($end - $start) / 60);

                            if ($menit >= 1440) {
                                $durasi = floor($menit / 1440) . ' hari';
                            } elseif ($menit >= 60) {
                                $durasi = floor($menit / 60) . ' jam';
                            } else {
                                $durasi = $menit . ' menit';
                            }
                        } else {
                            $durasi = '-';
                        }

                        $nama_petugas = GetDetailData($Conn, 'access', 'id_access', $id_access, 'access_name');

                        echo '
                            <tr>
                                <td class="text-center"><small>'.$no.'</small></td>
                                <td class="text-left"><small>'.$data['nama_pasien'].'</small></td>
                                <td class="text-left"><small>'.$data['id_pasien'].'</small></td>
                                <td class="text-left"><small>'.$usia.'</small></td>
                                <td class="text-left"><small>'.$nama_dokter_pengirim.'</small></td>
                                <td class="text-left"><small>'.$data['alat_pemeriksa'].'</small></td>
                                <td class="text-left"><small>'.$data['tujuan'].'</small></td>
                                <td class="text-left"><small>'.$data['pembayaran'].'</small></td>
                                <td class="text-left"><small>'.$diminta.'</small></td>
                                <td class="text-left"><small>'.$dikerjakan.'</small></td>
                                <td class="text-left"><small>'.$hasil.'</small></td>
                                <td class="text-left"><small>'.$selesai.'</small></td>
                                <td class="text-center"><small>'.$durasi.'</small></td>
                                <td class="text-center"><small>'.$nama_petugas.'</small></td>
                            </tr>
                        ';
                        $no++;
                    }
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $("#button_export_rincian_durasi_pelayanan").prop("disabled", false);
</script>
