<?php
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    date_default_timezone_set("Asia/Jakarta");

    // Inisiaslisasi Judul Laporan
    $judul_laporan = "LAPORAN DURASI PELAYANAN";

    // ================= HELPER DURASI =================
    function formatDurasi($menit){
        if ($menit === null || $menit <= 0) {
            return '0 Min';
        }

        if ($menit >= 1440) {
            $hari = floor($menit / 1440);
            return $hari . ' Day';
        } elseif ($menit >= 60) {
            $jam = floor($menit / 60);
            return $jam . ' H';
        } else {
            return $menit . ' Min';
        }
    }

    function formatDurasiDenganAvg($totalMenit, $jumlah){
        if ($jumlah <= 0 || $totalMenit <= 0) {
            return '0 Min (Av: 0 Min)';
        }

        $avg = round($totalMenit / $jumlah);

        return formatDurasi($totalMenit).' (Av: '.formatDurasi($avg).')';
    }

    // Validasi Sesi Akses
     if(empty($SessionIdAccess)){
        echo '
            <tr>
                <td colspan="8" class="text-center text-danger">
                    <small>Sesi Akses Sudah Berakhir! Silahkan Login Ulang.</small>
                </td>
            </tr>
        ';
        exit;
    }


    
    // ================= VALIDASI INPUT =================
    if (empty($_POST['periode_durasi_pelayanan'])) {
        echo '
            <tr>
                <td colspan="8" class="text-center text-danger">
                    <small>Mode periode laporan belum dipilih</small>
                </td>
            </tr>
        ';
        exit;
    }

    $periode = $_POST['periode_durasi_pelayanan'];
    $tahun   = $_POST['tahun'] ?? '';
    $bulan   = $_POST['bulan'] ?? '';

    if ($periode === 'Tahun' && empty($tahun)) {
        echo '
            <tr>
                <td colspan="8" class="text-center text-danger">
                    <small>Tahun belum dipilih</small>
                </td>
            </tr>
        ';
        exit;
    }

    if ($periode === 'Bulan' && (empty($tahun) || empty($bulan))) {
        echo '
            <tr>
                <td colspan="8" class="text-center text-danger">
                    <small>Tahun / Bulan belum dipilih</small>
                </td>
            </tr>
        ';
        exit;
    }

    // ================= DATA MASTER =================
    $nama_bulan = [
        '01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April',
        '05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus',
        '09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'
    ];

    // ================= PERIODE TAHUN =================
    if ($periode === 'Tahun') {
        $judul_laporan = '
            <b>LAPORAN DURASI PELAYANAN RADIOLOGI</b><br>
            <small>Periode Tahun '.$tahun.'</small>
        ';
        foreach ($nama_bulan as $kode_bulan => $label_bulan) {

            $where = "
                YEAR(datetime_diminta) = '$tahun'
                AND MONTH(datetime_diminta) = '$kode_bulan'
                AND status_pemeriksaan <> 'Batal'
            ";

            $sql = "
               SELECT
                    COUNT(id_radiologi) AS permintaan,
                    SUM(TIMESTAMPDIFF(MINUTE, datetime_diminta, datetime_dikerjakan)) AS diterima,
                    SUM(TIMESTAMPDIFF(MINUTE, datetime_dikerjakan, datetime_hasil)) AS dikerjakan,
                    SUM(TIMESTAMPDIFF(MINUTE, datetime_hasil, datetime_selesai)) AS selesai,
                    SUM(TIMESTAMPDIFF(MINUTE, datetime_diminta, datetime_selesai)) AS total_durasi,

                    COUNT(datetime_dikerjakan) AS c_diterima,
                    COUNT(datetime_hasil) AS c_dikerjakan,
                    COUNT(datetime_selesai) AS c_selesai
                FROM radiologi
                WHERE $where
            ";

            $q = mysqli_fetch_assoc(mysqli_query($Conn, $sql));

            echo '
                <tr class="modal_rincian_durasi_pelayanan" data-periode="'.$periode.'" data-tahun="'.$tahun.'" data-bulan="'.$kode_bulan.'" data-tanggal="">
                    <td class="text-left">'.$tahun.'</td>
                    <td class="text-left">'.$label_bulan.'</td>
                    <td class="text-left">-</td>
                    <td class="text-center">'.($q['permintaan'] ?? 0).'</td>
                    <td class="text-center">'.formatDurasiDenganAvg($q['diterima'], $q['c_diterima']).'</td>
                    <td class="text-center">'.formatDurasiDenganAvg($q['dikerjakan'], $q['c_dikerjakan']).'</td>
                    <td class="text-center">'.formatDurasiDenganAvg($q['selesai'], $q['c_selesai']).'</td>
                    <td class="text-center">'.formatDurasiDenganAvg($q['total_durasi'], $q['permintaan']).'</td>
                </tr>
            ';
        }
    }

    // ================= PERIODE BULAN =================
    else {
        $judul_laporan = '
            <b>LAPORAN DURASI PELAYANAN RADIOLOGI</b><br>
            <small>Periode Bulan '.$bulan.' Tahun '.$tahun.'</small>
        ';

        $jumlah_hari = cal_days_in_month(CAL_GREGORIAN, (int)$bulan, (int)$tahun);

        for ($hari = 1; $hari <= $jumlah_hari; $hari++) {

            $tanggal = sprintf('%04d-%02d-%02d', $tahun, $bulan, $hari);

            // Mapping nama hari
            $dt = new DateTime($tanggal);
            $nama_hari = [
                'Sunday'    => 'Minggu',
                'Monday'    => 'Senin',
                'Tuesday'   => 'Selasa',
                'Wednesday' => 'Rabu',
                'Thursday'  => 'Kamis',
                'Friday'    => 'Jumat',
                'Saturday'  => 'Sabtu'
            ];
            $hari_indo = $nama_hari[$dt->format('l')];
            $tanggal_tampil = $hari_indo.', '.$dt->format('d').' '.$nama_bulan[$bulan].' '.$tahun;
            $tanggal_keyword = $tahun . '-' . $bulan . '-' . $dt->format('d');
            $sql = "
                SELECT
                    COUNT(id_radiologi) AS permintaan,
                    SUM(TIMESTAMPDIFF(MINUTE, datetime_diminta, datetime_dikerjakan)) AS diterima,
                    SUM(TIMESTAMPDIFF(MINUTE, datetime_dikerjakan, datetime_hasil)) AS dikerjakan,
                    SUM(TIMESTAMPDIFF(MINUTE, datetime_hasil, datetime_selesai)) AS selesai,
                    SUM(TIMESTAMPDIFF(MINUTE, datetime_diminta, datetime_selesai)) AS total_durasi,

                    COUNT(datetime_dikerjakan) AS c_diterima,
                    COUNT(datetime_hasil) AS c_dikerjakan,
                    COUNT(datetime_selesai) AS c_selesai
                FROM radiologi
                WHERE
                    DATE(datetime_diminta) = '$tanggal'
                    AND status_pemeriksaan <> 'Batal'
            ";

            $q = mysqli_fetch_assoc(mysqli_query($Conn, $sql));

            echo '
                <tr class="modal_rincian_durasi_pelayanan" data-periode="'.$periode.'" data-tahun="'.$tahun.'" data-bulan="'.$bulan.'" data-tanggal="'.$tanggal_keyword.'">
                    <td class="text-left">'.$tahun.'</td>
                    <td class="text-left">'.$nama_bulan[$bulan].'</td>
                    <td class="text-left">'.$tanggal_tampil.'</td>
                    <td class="text-center">'.($q['permintaan'] ?? 0).'</td>
                    <td class="text-center">'.formatDurasiDenganAvg($q['diterima'], $q['c_diterima']).'</td>
                    <td class="text-center">'.formatDurasiDenganAvg($q['dikerjakan'], $q['c_dikerjakan']).'</td>
                    <td class="text-center">'.formatDurasiDenganAvg($q['selesai'], $q['c_selesai']).'</td>
                    <td class="text-center">'.formatDurasiDenganAvg($q['total_durasi'], $q['permintaan']).'</td>
                </tr>
            ';
        }
    }

    echo '
        <script>
            $("#title_laporan_durasi_pelayanan").html(`'.$judul_laporan.'`);
        </script>
    ';
?>