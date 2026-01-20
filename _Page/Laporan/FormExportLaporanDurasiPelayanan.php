<?php
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    date_default_timezone_set("Asia/Jakarta");

    // Validasi Sesi Akses
    if(empty($SessionIdAccess)){
        echo '
            <div class="row mb-3">
                <div class="col-12 text-center">
                    <div class="alert alert-danger"><small>Sesi Akses Sudah Berakhir! Silahkan Login Ulang.</small></div>
                </div>
            </div>
            <script>$("#button_export_laporan_durasi_pelayanan").prop("disabled", true);</script>
        ';
        exit;
    }

    // Validasi Kelengkapan data
    if (empty($_POST['periode_durasi_pelayanan'])) {
         echo '
            <div class="row mb-3">
                <div class="col-12 text-center">
                    <div class="alert alert-danger"><small>Periode Data Belum Dipilih! Silahkan lakukan filter data laporan terlebih dulu.</small></div>
                </div>
            </div>
        ';
        exit;
    }

    $periode = $_POST['periode_durasi_pelayanan'];
    $tahun   = $_POST['tahun'] ?? '';
    $bulan   = $_POST['bulan'] ?? '';

    if ($periode === 'Tahun' && empty($tahun)) {
        echo '
            <div class="row mb-3">
                <div class="col-12 text-center">
                    <div class="alert alert-danger"><small>Tahun Belum Dipilih!</small></div>
                </div>
            </div>
            <script>$("#button_export_laporan_durasi_pelayanan").prop("disabled", true);</script>
        ';
        exit;
    }

    if ($periode === 'Bulan' && (empty($tahun) || empty($bulan))) {
        echo '
            <div class="row mb-3">
                <div class="col-12 text-center">
                    <div class="alert alert-danger"><small>Tahun / Bulan Belum Dipilih!!</small></div>
                </div>
            </div>
            <script>$("#button_export_laporan_durasi_pelayanan").prop("disabled", true);</script>
        ';
        exit;
    }

    // Routing Keyword
    if($periode=="Tahun"){
        $keyword = "$tahun";
    }else{
        $keyword = "$tahun-$bulan";
    }

    // Hitung Jumlah Data
    $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT id_radiologi FROM radiologi WHERE datetime_diminta LIKE '%$keyword%'"));

    echo '
        <input type="hidden" name="periode" value="'.$periode.'">
        <input type="hidden" name="tahun" value="'.$tahun.'">
        <input type="hidden" name="bulan" value="'.$bulan.'">
        <div class="row mb-2">
            <div class="col-5"><small>Periode</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-6"><small>'.$periode.'</small></div>
        </div>
        <div class="row mb-2">
            <div class="col-5"><small>Tahun</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-6"><small>'.$tahun.'</small></div>
        </div>
        <div class="row mb-2">
            <div class="col-5"><small>Bulan</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-6"><small>'.$bulan.'</small></div>
        </div>
        <div class="row mb-2">
            <div class="col-5"><small>Jumlah Data</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-6"><small>'.$jml_data.' Row</small></div>
        </div>
        <script>$("#button_export_laporan_durasi_pelayanan").prop("disabled", false);</script>
    ';
?>