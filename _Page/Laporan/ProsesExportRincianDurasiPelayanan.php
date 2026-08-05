<?php
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    require "../../vendor/autoload.php";

    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

    date_default_timezone_set("Asia/Jakarta");

    /* ======================================================
    AMBIL DAN VALIDASI INPUT (SESUAI DENGAN FORM)
    ====================================================== */
    $periode = isset($_POST['periode']) ? trim($_POST['periode']) : '';
    $tahun   = isset($_POST['tahun'])   ? trim($_POST['tahun'])   : '';
    $bulan   = isset($_POST['bulan'])   ? trim($_POST['bulan'])   : '';
    $tanggal = isset($_POST['tanggal']) ? trim($_POST['tanggal']) : '';

    // Validasi dasar
    if ($periode === '') {
        die('Periode data belum dipilih');
    }

    if (!in_array($periode, ['Tahun', 'Bulan'])) {
        die('Periode harus "Tahun" atau "Bulan"');
    }

    if ($periode === 'Tahun') {
        // Periode "Tahun" sebenarnya adalah filter bulan tertentu
        if ($tahun === '' || !preg_match('/^\d{4}$/', $tahun)) {
            die('Tahun harus diisi dan berupa 4 digit angka');
        }
        if ($bulan === '' || !is_numeric($bulan) || $bulan < 1 || $bulan > 12) {
            die('Bulan harus diisi dan berupa angka 1-12');
        }
        $judul_periode = "Periode Bulan $bulan Tahun $tahun";
        // Query menggunakan YEAR dan MONTH
        $sql = "
            SELECT 
                id_pasien,
                id_access,
                nama_pasien,
                tanggal_lahir,
                nama_dokter_pengirim,
                alat_pemeriksa,
                tujuan,
                pembayaran,
                datetime_diminta,
                datetime_dikerjakan,
                datetime_hasil,
                datetime_selesai
            FROM radiologi
            WHERE YEAR(datetime_diminta) = ? AND MONTH(datetime_diminta) = ?
            ORDER BY id_radiologi ASC
        ";
        $stmt = mysqli_prepare($Conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $tahun, $bulan);
    } else { // periode === 'Bulan'
        // Periode "Bulan" sebenarnya adalah filter tanggal tertentu
        if ($tahun === '' || !preg_match('/^\d{4}$/', $tahun)) {
            die('Tahun harus diisi dan berupa 4 digit angka');
        }
        if ($bulan === '' || !is_numeric($bulan) || $bulan < 1 || $bulan > 12) {
            die('Bulan harus diisi dan berupa angka 1-12');
        }
        if ($tanggal === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
            die('Tanggal harus diisi dengan format YYYY-MM-DD');
        }
        $judul_periode = "Periode Tanggal $tanggal, $bulan $tahun";
        // Query menggunakan DATE (hanya tanggal, tanpa jam)
        $sql = "
            SELECT 
                id_pasien,
                id_access,
                nama_pasien,
                tanggal_lahir,
                nama_dokter_pengirim,
                alat_pemeriksa,
                tujuan,
                pembayaran,
                datetime_diminta,
                datetime_dikerjakan,
                datetime_hasil,
                datetime_selesai
            FROM radiologi
            WHERE DATE(datetime_diminta) = ?
            ORDER BY id_radiologi ASC
        ";
        $stmt = mysqli_prepare($Conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $tanggal);
    }

    // Eksekusi query
    mysqli_stmt_execute($stmt);
    $query = mysqli_stmt_get_result($stmt);
    if (!$query) {
        die('Error query: ' . mysqli_error($Conn));
    }

    /* ======================================================
    FUNGSI HITUNG USIA (TIDAK BENTROK)
    ====================================================== */
    function hitungUsiaPasien($tanggal_lahir, $datetime_diminta) {
        if (empty($tanggal_lahir) || $tanggal_lahir == '0000-00-00' || empty($datetime_diminta)) {
            return '-';
        }
        $lahir = new DateTime($tanggal_lahir);
        $diminta = new DateTime($datetime_diminta);
        // Pastikan tanggal lahir tidak lebih besar dari tanggal hitung
        if ($lahir > $diminta) {
            return '-';
        }
        $diff = $lahir->diff($diminta);
        $tahun = $diff->y;
        $bulan = $diff->m;
        $hari  = $diff->d;
        
        if ($tahun > 0) {
            return $tahun . ' tahun';
        }
        if ($bulan > 0) {
            return $bulan . ' bulan';
        }
        // Jika kurang dari 1 bulan, tampilkan hari (minimal 1 hari)
        return ($hari < 1) ? '1 hari' : $hari . ' hari';
    }

    /* ======================================================
    INIT EXCEL
    ====================================================== */
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    /* ======================================================
    JUDUL (SESUAI DENGAN FORM)
    ====================================================== */
    $sheet->setCellValue('A1', 'LAPORAN RINCIAN DURASI PELAYANAN');
    $sheet->mergeCells('A1:N1');
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

    $sheet->setCellValue('A2', $judul_periode);
    $sheet->mergeCells('A2:N2');
    $sheet->getStyle('A2')->getFont()->setBold(true);
    $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');

    /* ======================================================
    HEADER TABEL (14 KOLOM, SAMA DENGAN FORM)
    ====================================================== */
    $header = [
        'No', 'Nama Pasien', 'RM', 'Usia', 'DPJP', 'Modality',
        'Kunjungan', 'Pembayaran', 'Diminta', 'Dikerjakan',
        'Hasil', 'Selesai', 'Durasi', 'Radiografer'
    ];

    $row = 4;
    $col = 'A';
    foreach ($header as $h) {
        $sheet->setCellValue($col . $row, $h);
        $sheet->getStyle($col . $row)->getFont()->setBold(true);
        $col++;
    }

    /* ======================================================
    DATA (PROSES SAMA PERSIS DENGAN FORM)
    ====================================================== */
    $row++;
    $no = 1;

    while ($d = mysqli_fetch_assoc($query)) {
        $id_access         = $d['id_access'];
        $tanggal_lahir     = $d['tanggal_lahir'];
        $datetime_diminta  = $d['datetime_diminta'];
        $datetime_selesai  = $d['datetime_selesai'];

        // Format tanggal (sama dengan form)
        $diminta    = empty($datetime_diminta) ? '-' : date('d/m/Y H:i', strtotime($datetime_diminta));
        $dikerjakan = empty($d['datetime_dikerjakan']) ? '-' : date('d/m/Y H:i', strtotime($d['datetime_dikerjakan']));
        $hasil      = empty($d['datetime_hasil']) ? '-' : date('d/m/Y H:i', strtotime($d['datetime_hasil']));
        $selesai    = empty($datetime_selesai) ? '-' : date('d/m/Y H:i', strtotime($datetime_selesai));

        // Hitung durasi (sama dengan form)
        if (!empty($datetime_diminta) && !empty($datetime_selesai)) {
            $start = strtotime($datetime_diminta);
            $end   = strtotime($datetime_selesai);
            $menit = floor(($end - $start) / 60);
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

        // Hitung usia (sama dengan form)
        $usia = hitungUsiaPasien($tanggal_lahir, $datetime_diminta);

        // Nama petugas
        $nama_petugas = GetDetailData($Conn, 'access', 'id_access', $id_access, 'access_name');

        // Tulis ke Excel (14 kolom, urutan sama dengan header)
        $sheet->fromArray([
            $no++,
            $d['nama_pasien'],
            $d['id_pasien'],
            $usia,
            $d['nama_dokter_pengirim'],
            $d['alat_pemeriksa'],
            $d['tujuan'],
            $d['pembayaran'],
            $diminta,
            $dikerjakan,
            $hasil,
            $selesai,
            $durasi,
            $nama_petugas
        ], null, 'A' . $row);

        $row++;
    }

    /* ======================================================
    AUTO WIDTH SEMUA KOLOM (A–N)
    ====================================================== */
    foreach (range('A', 'N') as $c) {
        $sheet->getColumnDimension($c)->setAutoSize(true);
    }

    /* ======================================================
    OUTPUT FILE
    ====================================================== */
    $filename = "Laporan_Rincian_Durasi_Pelayanan_"
                . ($periode === 'Tahun' ? $tahun . '_' . str_pad($bulan, 2, '0', STR_PAD_LEFT) : $tanggal)
                . ".xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
?>