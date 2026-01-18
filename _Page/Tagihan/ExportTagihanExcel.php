<?php
    // ================= CONFIG =================
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    require '../../vendor/autoload.php';

    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
    use PhpOffice\PhpSpreadsheet\Style\Alignment;
    use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

    date_default_timezone_set("Asia/Jakarta");

    // ================= VALIDASI =================
    if (empty($SessionIdAccess)) {
        die("Sesi Akses Sudah Berakhir!");
    }

    if (empty($_POST['periode_1']) || empty($_POST['periode_2'])) {
        die("Periode Tidak Lengkap!");
    }

    $periode_1 = validateAndSanitizeInput($_POST['periode_1']);
    $periode_2 = validateAndSanitizeInput($_POST['periode_2']);

    if ($periode_1 >= $periode_2) {
        die("Periode Awal Tidak Boleh >= Periode Akhir");
    }

    // ================= QUERY DATA =================
    $query = mysqli_query($Conn, "
        SELECT id_radiologi, id_pasien, nama_pasien, alat_pemeriksa, tujuan,
            pembayaran, datetime_diminta, status_pemeriksaan
        FROM radiologi
        WHERE datetime_diminta BETWEEN '$periode_1' AND '$periode_2'
        ORDER BY id_radiologi DESC
    ");

    if (mysqli_num_rows($query) == 0) {
        die("Data Tagihan Tidak Ditemukan!");
    }

    // ================= SPREADSHEET =================
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Tagihan Radiologi');

    // ================= HEADER =================
    $header = [
        'No','Nama','RM','Tanggal','Jam',
        'Tujuan','Modality','Metode','Status','Tagihan'
    ];

    $col = 'A';
    foreach ($header as $title) {
        $sheet->setCellValue($col.'1', $title);
        $sheet->getStyle($col.'1')->getFont()->setBold(true);
        $sheet->getStyle($col.'1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $col++;
    }

    // ================= ISI DATA =================
    $row = 2;
    $no  = 1;

    $nama_modalitas = [
        'XR' => 'X-Ray',
        'CT' => 'CT-Scan',
        'US' => 'USG',
        'MR' => 'MRI',
        'NM' => 'Nuclear Medicine',
        'PT' => 'PET Scan',
        'DX' => 'Digital Radiography',
        'CR' => 'Computed Radiography'
    ];

    while ($data = mysqli_fetch_assoc($query)) {

        // Hitung total tagihan
        $total = 0;
        $q = mysqli_query($Conn, "SELECT amount FROM radiologi_invoice WHERE id_radiologi='{$data['id_radiologi']}'");
        while ($n = mysqli_fetch_assoc($q)) {
            $total += $n['amount'];
        }

        $sheet->setCellValue("A$row", $no);
        $sheet->setCellValue("B$row", $data['nama_pasien']);
        $sheet->setCellValue("C$row", $data['id_pasien']);
        $sheet->setCellValue("D$row", date('d/m/Y', strtotime($data['datetime_diminta'])));
        $sheet->setCellValue("E$row", date('H:i', strtotime($data['datetime_diminta'])));
        $sheet->setCellValue("F$row", $data['tujuan']);
        $sheet->setCellValue("G$row", $nama_modalitas[$data['alat_pemeriksa']] ?? '-');
        $sheet->setCellValue("H$row", $data['pembayaran']);
        $sheet->setCellValue("I$row", $data['status_pemeriksaan']);
        $sheet->setCellValue("J$row", $total);

        // Format rupiah
        $sheet->getStyle("J$row")
            ->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        $row++;
        $no++;
    }

    // ================= AUTO WIDTH =================
    foreach (range('A','J') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // ================= OUTPUT =================
    $filename = "Tagihan_Radiologi_{$periode_1}_sd_{$periode_2}.xlsx";

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
?>