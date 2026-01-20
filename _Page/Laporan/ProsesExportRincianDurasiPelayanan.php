<?php
    include "../../_Config/Connection.php";
    require "../../vendor/autoload.php";

    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

    date_default_timezone_set("Asia/Jakarta");

    /* ======================================================
    VALIDASI INPUT (INLINE – TANPA FUNCTION)
    ====================================================== */
    $periode = isset($_POST['periode']) ? trim($_POST['periode']) : '';
    $tahun   = isset($_POST['tahun']) ? trim($_POST['tahun']) : '';
    $bulan   = isset($_POST['bulan']) ? trim($_POST['bulan']) : '';
    $tanggal   = isset($_POST['tanggal']) ? trim($_POST['tanggal']) : '';

    if ($periode === '' || $tahun === '') {
        die('Parameter periode / tahun tidak lengkap');
    }

    if ($periode === 'Bulan' && $bulan === '') {
        die('Parameter bulan tidak boleh kosong');
    }

    /* ======================================================
    KEYWORD FILTER
    ====================================================== */
    if ($periode === 'Tahun') {
        $keyword = "$tahun-$bulan";
        $judul_periode = "Periode Tahun $tahun";
    } else {
        $keyword = $tanggal;
        $judul_periode = "Periode Bulan $bulan Tahun $tahun";
    }

    /* ======================================================
    QUERY DATA
    ====================================================== */
    $sql = "
        SELECT 
            id_pasien,
            nama_pasien,
            alat_pemeriksa,
            tujuan,
            pembayaran,
            datetime_diminta,
            datetime_dikerjakan,
            datetime_hasil,
            datetime_selesai
        FROM radiologi
        WHERE datetime_diminta LIKE '%$keyword%'
        ORDER BY id_radiologi ASC
    ";

    $query = mysqli_query($Conn, $sql);

    /* ======================================================
    INIT EXCEL
    ====================================================== */
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    /* ======================================================
    JUDUL
    ====================================================== */
    $sheet->setCellValue('A1', 'LAPORAN DURASI PELAYANAN RADIOLOGI');
    $sheet->mergeCells('A1:K1');
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

    $sheet->setCellValue('A2', $judul_periode);
    $sheet->mergeCells('A2:K2');
    $sheet->getStyle('A2')->getFont()->setBold(true);
    $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');

    /* ======================================================
    HEADER TABEL
    ====================================================== */
    $header = [
        'No', 'Nama Pasien', 'RM', 'Modality', 'Kunjungan',
        'Pembayaran', 'Diminta', 'Dikerjakan',
        'Hasil', 'Selesai', 'Durasi'
    ];

    $row = 4;
    $col = 'A';
    foreach ($header as $h) {
        $sheet->setCellValue($col.$row, $h);
        $sheet->getStyle($col.$row)->getFont()->setBold(true);
        $col++;
    }

    /* ======================================================
    DATA
    ====================================================== */
    $row++;
    $no = 1;

    while ($d = mysqli_fetch_assoc($query)) {

        // Format tanggal aman
        $diminta    = $d['datetime_diminta']    ? date('d/m/Y H:i', strtotime($d['datetime_diminta']))    : '-';
        $dikerjakan = $d['datetime_dikerjakan'] ? date('d/m/Y H:i', strtotime($d['datetime_dikerjakan'])) : '-';
        $hasil      = $d['datetime_hasil']      ? date('d/m/Y H:i', strtotime($d['datetime_hasil']))      : '-';
        $selesai    = $d['datetime_selesai']    ? date('d/m/Y H:i', strtotime($d['datetime_selesai']))    : '-';

        // Hitung durasi (MENIT → JAM → HARI)
        if ($d['datetime_diminta'] && $d['datetime_selesai']) {
            $menit = floor((strtotime($d['datetime_selesai']) - strtotime($d['datetime_diminta'])) / 60);

            if ($menit >= 1440) {
                $durasi = floor($menit / 1440) . ' Hari';
            } elseif ($menit >= 60) {
                $durasi = floor($menit / 60) . ' Jam';
            } else {
                $durasi = $menit . ' Menit';
            }
        } else {
            $durasi = '-';
        }

        $sheet->fromArray([
            $no++,
            $d['nama_pasien'],
            $d['id_pasien'],
            $d['alat_pemeriksa'],
            $d['tujuan'],
            $d['pembayaran'],
            $diminta,
            $dikerjakan,
            $hasil,
            $selesai,
            $durasi
        ], null, 'A'.$row);

        $row++;
    }

    /* ======================================================
    AUTO WIDTH KOLOM
    ====================================================== */
    foreach (range('A','K') as $c) {
        $sheet->getColumnDimension($c)->setAutoSize(true);
    }

    /* ======================================================
    OUTPUT
    ====================================================== */
    $filename = "Laporan_Durasi_Pelayanan_$keyword.xlsx";

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
?>