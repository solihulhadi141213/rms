<?php
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";
    require '../../vendor/autoload.php';

    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
    use PhpOffice\PhpSpreadsheet\Style\Alignment;
    use PhpOffice\PhpSpreadsheet\Style\Font;

    date_default_timezone_set("Asia/Jakarta");

    // ================= VALIDASI =================
    if (empty($SessionIdAccess)) {
        die('Sesi Akses Sudah Berakhir');
    }

    if (empty($_POST['periode']) || empty($_POST['tahun'])) {
        die('Periode dan Tahun wajib diisi');
    }

    $periode = $_POST['periode'];
    $tahun   = $_POST['tahun'];
    $bulan   = $_POST['bulan'] ?? '';

    if ($periode === 'Bulan' && empty($bulan)) {
        die('Bulan wajib diisi');
    }

    // ================= HELPER =================
    function formatDurasiExcel($menit){
        if ($menit <= 0) return '0 Min';
        if ($menit >= 1440) return floor($menit/1440).' Hari';
        if ($menit >= 60) return floor($menit/60).' Jam';
        return $menit.' Min';
    }

    function formatDurasiAvgExcel($total, $count){
        if ($count <= 0 || $total <= 0) {
            return '0 Min (Av: 0 Min)';
        }
        $avg = round($total/$count);
        return formatDurasiExcel($total).' (Av: '.formatDurasiExcel($avg).')';
    }

    $nama_bulan = [
        '01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April',
        '05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus',
        '09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'
    ];

    // ================= SPREADSHEET =================
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // ================= JUDUL =================
    $sheet->setCellValue('A1', 'LAPORAN DURASI PELAYANAN RADIOLOGI');
    $sheet->mergeCells('A1:H1');
    $sheet->getStyle('A1')->getFont()->setBold(true);
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    // Sub Judul
    $sheet->setCellValue('A2', $periode === 'Tahun'
        ? "Periode Tahun $tahun"
        : "Periode ".$nama_bulan[$bulan]." Tahun $tahun"
    );
    $sheet->mergeCells('A2:H2');
    $sheet->getStyle('A2')->getFont()->setBold(true);
    $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    // Header
    $sheet->setCellValue('A4','Tahun');
    $sheet->setCellValue('B4','Bulan');
    $sheet->setCellValue('C4','Tanggal');
    $sheet->setCellValue('D4','Permintaan');
    $sheet->setCellValue('E4','Diterima');
    $sheet->setCellValue('F4','Dikerjakan');
    $sheet->setCellValue('G4','Selesai');
    $sheet->setCellValue('H4','Total Durasi');
    
    // ================= HEADER STYLE =================
    $sheet->getStyle('A4:H4')->getFont()->setBold(true);
    $sheet->getStyle('A4:H4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('A4:H4')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    $row = 5;

    // ================= PERIODE TAHUN =================
    if ($periode === 'Tahun') {

        foreach ($nama_bulan as $kode_bulan => $label_bulan) {

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
                    YEAR(datetime_diminta)='$tahun'
                    AND MONTH(datetime_diminta)='$kode_bulan'
                    AND status_pemeriksaan<>'Batal'
            ";

            $q = mysqli_fetch_assoc(mysqli_query($Conn,$sql));

            $sheet->setCellValue("A$row",$tahun);
            $sheet->setCellValue("B$row",$label_bulan);
            $sheet->setCellValue("C$row","-");
            $sheet->setCellValue("D$row",$q['permintaan']);
            $sheet->setCellValue("E$row",formatDurasiAvgExcel($q['diterima'],$q['c_diterima']));
            $sheet->setCellValue("F$row",formatDurasiAvgExcel($q['dikerjakan'],$q['c_dikerjakan']));
            $sheet->setCellValue("G$row",formatDurasiAvgExcel($q['selesai'],$q['c_selesai']));
            $sheet->setCellValue("H$row",formatDurasiAvgExcel($q['total_durasi'],$q['permintaan']));

            $row++;
        }
    }

    // ================= PERIODE BULAN =================
    else {

        $jumlah_hari = cal_days_in_month(CAL_GREGORIAN,(int)$bulan,(int)$tahun);

        for ($i=1;$i<=$jumlah_hari;$i++) {

            $tanggal = sprintf('%04d-%02d-%02d',$tahun,$bulan,$i);

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
                WHERE DATE(datetime_diminta)='$tanggal'
                AND status_pemeriksaan<>'Batal'
            ";

            $q = mysqli_fetch_assoc(mysqli_query($Conn,$sql));

            $sheet->setCellValue("A$row",$tahun);
            $sheet->setCellValue("B$row",$nama_bulan[$bulan]);
            $sheet->setCellValue("C$row",$tanggal);
            $sheet->setCellValue("D$row",$q['permintaan']);
            $sheet->setCellValue("E$row",formatDurasiAvgExcel($q['diterima'],$q['c_diterima']));
            $sheet->setCellValue("F$row",formatDurasiAvgExcel($q['dikerjakan'],$q['c_dikerjakan']));
            $sheet->setCellValue("G$row",formatDurasiAvgExcel($q['selesai'],$q['c_selesai']));
            $sheet->setCellValue("H$row",formatDurasiAvgExcel($q['total_durasi'],$q['permintaan']));

            $row++;
        }
    }

    // ================= AUTO WIDTH =================
    foreach (range('A','H') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // ================= OUTPUT =================
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="Laporan_Durasi_Pelayanan.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
