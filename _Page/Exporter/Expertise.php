<?php
    //Validasi Data Wajib Ada
    if(empty($_GET['modality'])){
        echo 'Modality Tidak Boleh Kosong';
        exit;
    }

    if(empty($_GET['id'])){
        echo 'ID Expertise Tidak Boleh Kosong';
        exit;
    }

    if(empty($_GET['acn'])){
        echo 'Accession Number Tidak Boleh Kosong';
        exit;
    }

    // Helper function untuk nilai yang mungkin kosong
    function getDisplayValue($value, $default = '-') {
        return (isset($value) && trim($value) !== '') ? $value : $default;
    }

    //Buat variabel
    $modality         = validateAndSanitizeInput($_GET['modality']);
    $id_expertise     = validateAndSanitizeInput($_GET['id']);
    $accession_number = validateAndSanitizeInput($_GET['acn']);

    //Buka Detail Koneksi Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM radiologi WHERE accession_number = ?");
    $Qry->bind_param("s", $accession_number);
    if (!$Qry->execute()) {
        $error=$Conn->error;
        echo '
           Terjadi kesalahan pada saat membuka data dari database!<br>Keterangan : '.$error.'
        ';
        exit;
    }
    $Result = $Qry->get_result();
    $Data = $Result->fetch_assoc();
    $Qry->close();

    if (!$Data) {
        echo '<div class="alert alert-warning">Data radiologi tidak ditemukan</div>';
        exit;
    }

    //Buat Variabel
    $id_radiologi           = $Data['id_radiologi'];
    $id_access              = $Data['id_access'];
    $id_pasien              = $Data['id_pasien'];
    $id_kunjungan           = $Data['id_kunjungan'];
    $accession_number       = $Data['accession_number'];
    $nama_pasien            = $Data['nama_pasien'];
    $priority               = $Data['priority'];
    $asal_kiriman           = $Data['asal_kiriman'];
    $alat_pemeriksa         = $Data['alat_pemeriksa'];
    $kode_dokter_pengirim   = $Data['kode_dokter_pengirim'];
    $ihs_dokter_pengirim    = $Data['ihs_dokter_pengirim'];
    $nama_dokter_pengirim   = $Data['nama_dokter_pengirim'];
    $kode_dokter_penerima   = $Data['kode_dokter_penerima'];
    $ihs_dokter_penerima    = $Data['ihs_dokter_penerima'];
    $nama_dokter_penerima   = $Data['nama_dokter_penerima'];
    $radiografer            = $Data['radiografer'] ?? "-";
    $pesan                  = $Data['pesan'] ?? "-";
    $kesan                  = $Data['kesan'];
    $klinis                 = $Data['klinis'];
    $permintaan_pemeriksaan = $Data['permintaan_pemeriksaan'];
    $kv                     = $Data['kv'];
    $ma                     = $Data['ma'];
    $sec                    = $Data['sec'];
    $tujuan                 = $Data['tujuan'];
    $pembayaran             = $Data['pembayaran'];
    $datetime_diminta       = $Data['datetime_diminta'];
    $datetime_dikerjakan    = $Data['datetime_dikerjakan'];
    $datetime_hasil         = $Data['datetime_hasil'];
    $datetime_selesai       = $Data['datetime_selesai'];
    $status_pemeriksaan     = $Data['status_pemeriksaan'];

    //Nama Radiografer
    if (empty($Data['radiografer'])) {
        $radiografer = "-";
    }

    //klasifikasi prioritas
    $priority_list = [
        'routine' => 'Biasa',
        'urgent'  => 'Segera',
        'stat'    => 'Gawat'
    ];
    $priority_name = $priority_list[$priority] ?? '-';

    // Nama Modalitas
    $modalitas_list = [
        'XR' => 'X-Ray',
        'CT' => 'CT-Scan',
        'US' => 'USG',
        'MR' => 'MRI',
        'NM' => 'Nuclear Medicine (Kedokteran Nuklir)',
        'PT' => 'PET Scan',
        'DX' => 'Digital Radiography',
        'CR' => 'Computed Radiography'
    ];

    // Ambil nama modalitas
    $nama_modalitas = $modalitas_list[$alat_pemeriksa] ?? '-';
    

    //Format Tanggal
    $datetime_diminta     = formatDateTimeStrict($Data['datetime_diminta']);
    $datetime_dikerjakan  = formatDateTimeStrict($Data['datetime_dikerjakan']);
    $datetime_hasil       = formatDateTimeStrict($Data['datetime_hasil']);
    $datetime_selesai     = formatDateTimeStrict($Data['datetime_selesai']);

    // ===========================================
    // Membuka Data Kunjungan
    // ===========================================
    
    // Buka URL SIMRS
    $status_connection_simrs = 1;
    $url_connection_simrs = GetDetailData($Conn,'connection_simrs','status_connection_simrs',$status_connection_simrs,'url_connection_simrs');

    //Dapatkan Token SIMRS
    $token = GetSimrsToken($Conn);

    // Jika Token Tidak Valid Dan Gagal Dibuat
    if ($token === false) {
        echo '
            <div class="alert alert-danger">
                <small>Gagal mendapatkan token SIMRS!</small>
            </div>
        ';
        exit;
    }
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => ''.$url_connection_simrs.'/API/SIMRS/get_detail_kunjungan.php?id='.$id_kunjungan.'',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'token: '.$token.''
        ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    $data = json_decode($response, true);

    if ($response === false) {
        echo '<div class="alert alert-danger">Gagal koneksi ke SIMRS</div>';
        exit;
    }

    // Jika Response Tidak Valid
    if (empty($data['response']['code']) ||$data['response']['code'] != 200) {
        echo '
            <div class="row mb-3">
                <div class="col-12">
                    <div class="alert alert-danger">
                        <small>Gagal memuat data kunjungan<br> Pesan : '.$data['response']['message'].'</small>
                    </div>
                </div>
            </div>
        ';
        exit;
    }

    // Buka Metadata
    $metadata      = $data['metadata'] ?? [];
    $diagnosa_awal = $metadata['DiagAwal'] ?? '-';

    // Pastikan array pasien ada
    $pasien = $metadata['pasien'] ?? [];

    // Buat Variabel Penting
    $id_encounter   = getDisplayValue($metadata['id_encounter'] ?? null);
    $id_ihs         = getDisplayValue($pasien['id_ihs'] ?? null);
    $nama           = getDisplayValue($pasien['nama'] ?? null);
    $gender         = getDisplayValue($pasien['gender'] ?? null);
    $tempat_lahir   = getDisplayValue($pasien['tempat_lahir'] ?? null);
    $tanggal_lahir  = getDisplayValue($pasien['tanggal_lahir'] ?? null);
    $kontak         = getDisplayValue($pasien['kontak'] ?? null);
    $kontak_darurat = getDisplayValue($pasien['kontak_darurat'] ?? null);
    $nik            = getDisplayValue($pasien['nik'] ?? null);
    $no_bpjs        = getDisplayValue($pasien['no_bpjs'] ?? null);
    $propinsi       = getDisplayValue($pasien['propinsi'] ?? null);
    $kabupaten      = getDisplayValue($pasien['kabupaten'] ?? null);
    $kecamatan      = getDisplayValue($pasien['kecamatan'] ?? null);
    $desa           = getDisplayValue($pasien['desa'] ?? null);
    $alamat         = getDisplayValue($pasien['alamat'] ?? null);
    $perkawinan     = getDisplayValue($pasien['perkawinan'] ?? null);

    //Menghitung Usia Dan Format Tanggal Lahir
    $usia                    = hitungUsia($tanggal_lahir);
    $tanggal_lahir_formatted = formatTanggalLahir($tanggal_lahir);

    // Buka Data Expertise
    if($modality!=="US"){
        $Qry2 = $Conn->prepare("SELECT * FROM radiologi_expertise WHERE id_radiologi_expertise = ?");
    }else{
        $Qry2 = $Conn->prepare("SELECT * FROM radiologi_expertise_usg WHERE id_radiologi_expertise_usg = ?");
    }
    
    $Qry2->bind_param("s", $id_expertise);
    if (!$Qry2->execute()) {
        $error2=$Conn->error;
        echo '
           Terjadi kesalahan pada saat membuka data dari database!<br>Keterangan : '.$error2.'
        ';
        exit;
    }
    $Result2 = $Qry2->get_result();
    $Data2 = $Result2->fetch_assoc();
    $Qry2->close();

    if (!$Data2) {
        echo '<div class="alert alert-warning">Data Expertise tidak ditemukan</div>';
        exit;
    }

    //Buat Variabel
    $description = $Data2['description'];

    $text_url = $Data2['viewer_link'];
?>

<html>
    <head>
        <meta charset="UTF-8">
        <title>Radiologi - <?php echo $accession_number; ?></title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #ffffffff;
                margin: 10px;
                padding: 0;
            }
            table.custom-table {
                width: 100%;
                border-collapse: collapse;
                color: #000;
                background-color: #fff;
            }
            table.custom-table thead td {
                border: 1px solid #000;
                padding: 4px; 
                text-align: center;
                font-family: Arial, sans-serif;
            }
            table.custom-table tbody td {
                border: 1px solid #000;
                padding: 4px;
                font-family: Arial, sans-serif;
            }
            table.header_logo{
                border-bottom: 3px double #000;
                width: 100%;
            }
            .logo{
                padding-right : 15px;
                width: 70px;
            }
            table.nama_dokumen{
               border-bottom: 3px double #000;
            }
            table.nama_dokumen tr td{
                font-family: Arial, sans-serif;
                padding : 5px;
            }
            table.identitas{
                border-bottom: 3px double #000;
            }
            table.identitas tr td{
                font-family: Arial, sans-serif;
            }
            b{
                font-family: Arial, sans-serif !important;
            }
            .title_report{
                text-decoration: underline;
            }

            table.expertise{
                border-collapse: collapse;
                color: #212529;
            }

            table.expertise tr td{
                border: 1px solid #000;
                padding: 8px;
                vertical-align: middle;
            }
           

        </style>
    </head>
    <body>
        <table class="header_logo">
            <tr>
                <td rowspan="2" class="logo" valign="top"><img src="../../assets/img/<?php echo "$app_logo"; ?>" alt="Logo" width="70px"></td>
                <td valign="top">
                    <b><?php echo "$company_name"; ?></b>
                </td>
            </tr>
            <tr>
                <td valign="top">
                    <small><?php echo "$company_address"; ?></small><br>
                    <small>
                        Telepon : <?php echo "$company_contact"; ?><br>
                        Email : <?php echo "$company_email"; ?>
                    </small>
                </td>
            </tr>
        </table>
        <table width="100%" class="nama_dokumen">
            <tr>
                <td align="left">
                    <b>Lembar Hasil Pemeriksaan <?php echo $nama_modalitas ?></b><br>
                    <small><?php echo "ACN: $accession_number"; ?></small>
                </td>
                <td align="right">
                    Permintaan : <?php echo $datetime_diminta ?><br>
                    Hasil : <?php echo $datetime_hasil ?>
                </td>
            </tr>
        </table>
        <table width="100%" class="">
            <tr>
                <td>No.RM</td>
                <td>:</td>
                <td><?php echo "$id_pasien"; ?></td>
                <td></td>
                <td><i>Modality</i></td>
                <td>:</td>
                <td><?php echo "$nama_modalitas"; ?></td>
            </tr>
            <tr>
                <td>Nama Pasien</td>
                <td>:</td>
                <td><?php echo "$nama_pasien"; ?></td>
                <td></td>
                <td>Klinis Pasien</td>
                <td>:</td>
                <td>
                    <?php
                        if(!empty($klinis)){
                            $klinis_arry = json_decode($klinis, true);
                            if(!empty(count($klinis_arry))){
                                foreach($klinis_arry as $klinis_list){
                                    $nama_klinis = $klinis_list['nama_klinis'];
                                    echo "$nama_klinis,";
                                }
                            }
                        }
                    ?>
                </td>
            </tr>
            <tr>
                <td>Usia + Gender</td>
                <td>:</td>
                <td><?php echo "$usia ($gender)"; ?></td>
                <td></td>
                <td>Dokter Pengirim</td>
                <td>:</td>
                <td><?php echo "$nama_dokter_pengirim"; ?></td>
            </tr>
            <tr>
                <td>Pembayaran</td>
                <td>:</td>
                <td><?php echo "$pembayaran"; ?></td>
                <td></td>
                <td>Dokter Penerima</td>
                <td>:</td>
                <td><?php echo "$nama_dokter_penerima"; ?></td>
            </tr>
            <tr>
                <td>Asal Kiriman</td>
                <td>:</td>
                <td><?php echo "$asal_kiriman"; ?></td>
                <td></td>
                <td>Radiografer</td>
                <td>:</td>
                <td><?php echo "$radiografer"; ?></td>
            </tr>
            <tr>
                <td>Pemeriksaan</td>
                <td>:</td>
                <td>
                    <?php
                        if(!empty($permintaan_pemeriksaan)){
                            $permintaan_pemeriksaan_arry = json_decode($permintaan_pemeriksaan, true);
                            if(!empty(count($permintaan_pemeriksaan_arry))){
                                foreach($permintaan_pemeriksaan_arry as $pemeriksaan_list){
                                    $nama_pemeriksaan = $pemeriksaan_list['nama_pemeriksaan'];
                                    echo "$nama_pemeriksaan,";
                                }
                            }
                        }
                    ?>
                </td>
                <td></td>
                <?php
                    if($alat_pemeriksa=="XR"){
                        echo '
                            <td>Faktor Eksposur</td>
                            <td>:</td>
                            <td>
                                ['.$kv.' kV], 
                                ['.$ma.' mA], 
                                ['.$sec.' Sec]
                            </td>
                        ';
                    }
                ?>
            </tr>
        </table>
        <br>
        
        <?php
            // Normalisasi datetime ke ISO 8601
            $datetime_diminta = date('c', strtotime($datetime_diminta));
            $datetime_hasil   = date('c', strtotime($datetime_hasil));

            // Dokter Pengirim
            $raw_dokter_pengirim = [
                "role" => "dokter_pengirim",
                "datetime" => $datetime_diminta,
                "kode" => $kode_dokter_pengirim,
                "id_practitioner" => $ihs_dokter_pengirim,
                "nama" => $nama_dokter_pengirim
            ];

            // Dokter Penerima
            $raw_dokter_penerima = [
                "role" => "dokter_penerima",
                "datetime" => $datetime_hasil,
                "kode" => $kode_dokter_penerima,
                "id_practitioner" => $ihs_dokter_penerima,
                "nama" => $nama_dokter_penerima
            ];

            // Petugas Radiografer
            $raw_petugas = [
                "role" => "radiografer",
                "datetime" => $datetime_diminta,
                "accession_number" => $accession_number,
                "nama" => $radiografer
            ];

            // Encode JSON Aman
            $json_dokter_pengirim = json_encode($raw_dokter_pengirim, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            $json_dokter_penerima = json_encode($raw_dokter_penerima, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            $json_petugas         = json_encode($raw_petugas, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        ?>
        <table width="100%" class="expertise">
            <tr>
                <td align="center" rowspan="4" width="20%">
                    <?php echo '<img src="../../qr.php?text='.$text_url.'" alt="QR Code">'; ?><br>
                    <small>Scan Me</small>
                </td>
            </tr>
            <tr>
                <td width="40%" align="center"><b>Dokter Pemeriksa</b></td>
                <td width="40%" align="center"><b>Petugas / Radiografer</b></td>
            </tr>
            <tr>
                <td align="center"></td>
                <td align="center"></td>
            </tr>
            <tr>
                <td align="center">
                    <small>(<?php echo "$nama_dokter_penerima"; ?>)</small>
                </td>
                <td align="center">
                    <small>(<?php echo "$radiografer"; ?>)</small>
                </td>
            </tr>
        </table>
    </body>
</html>