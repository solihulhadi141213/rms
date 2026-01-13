<?php
    //Validasi Data 'an' tidak boleh kosong
    if(empty($_GET['an'])){
       echo 'Accession Number Tidak Boleh Kosong';
       exit;
    }

    // Helper function untuk nilai yang mungkin kosong
    function getDisplayValue($value, $default = '-') {
        return (isset($value) && trim($value) !== '') ? $value : $default;
    }

    //Buat variabel
    $accession_number = validateAndSanitizeInput($_GET['an']);

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

    // Apabila Status Hasil Maka Selesaikan
    if($status_pemeriksaan=="Hasil"){
        // Update Status Pemeriksaan
        $datetime_hasil     = date('Y-m-d H:i:s');
        $status_pemeriksaan = "Selesai";

        // Update
        $update_radiologi   = $Conn->prepare("UPDATE radiologi SET datetime_hasil = ?, status_pemeriksaan = ? WHERE id_radiologi = ?");
        $update_radiologi->bind_param("ssi", $datetime_hasil, $status_pemeriksaan, $id_radiologi);
        $update_radiologi_executed = $update_radiologi->execute();
        $update_radiologi->close();
    }

    $text_url = "$app_base_url/_Page/Exporter/Exporter.php?data=Nota&an=$accession_number";
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
                    <b>NOTA TAGIHAN (INVOICE) PELAYANAN RADIOLOGI</b><br>
                    <small><?php echo "ACN: $accession_number"; ?></small>
                </td>
                <td align="right">
                    Tanggal / Jam : <?php echo $datetime_diminta ?><br>
                </td>
            </tr>
        </table>
        <table width="100%" class="">
            <tr>
                <td align="center" rowspan="10" width="20%">
                    <?php echo '<img src="../../qr.php?text='.$text_url.'" alt="QR Code">'; ?>
                </td>
            </tr>
             <tr>
                <td><i>Nama Pasien</i></td>
                <td>:</td>
                <td colspan="4"><?php echo "$nama_pasien"; ?></td>
            </tr>
            <tr>
                <td>No.RM</td>
                <td>:</td>
                <td><?php echo "$id_pasien"; ?></td>
                 <td><i>Modality</i></td>
                <td>:</td>
                <td><?php echo "$nama_modalitas"; ?></td>
            </tr>
            <tr>
                <td>Usia</td>
                <td>:</td>
                <td><?php echo "$usia"; ?></td>
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
            </tr>
            <tr>
                <td>Gender</td>
                <td>:</td>
                <td><?php echo "$gender"; ?></td>
                 <td>Pembayaran</td>
                <td>:</td>
                <td><?php echo "$pembayaran"; ?></td>
            </tr>
            <tr>
                <td>Dokter</td>
                <td>:</td>
                <td colspan="4"><?php echo "$nama_dokter_penerima"; ?></td>
            </tr>
            <tr>
                <td><i>Radiografer</i></td>
                <td>:</td>
                <td colspan="4"><?php echo "$radiografer"; ?></td>
            </tr>
        </table>
        <br>
        <table width="100%" class="expertise">
            <tr>
                <td align="center"><b>No</b></td>
                <td align="left"><b>Uraian</b></td>
                <td align="right"><b>Tarif/Harga</b></td>
                <td align="center"><b>QTY</b></td>
                <td align="right"><b>Jumlah</b></td>
            </tr>
            <?php
                // Apabila Data Tidak Ada
                // Cari Data Invoice
                $jumlah_invoice = mysqli_num_rows(mysqli_query($Conn, "SELECT id_radiologi FROM radiologi_invoice WHERE id_radiologi='$id_radiologi'"));
                if(empty($jumlah_invoice)){
                    echo '
                        <tr>
                            <td colspan="5" align="center">Tidak Ada Uraian Invoice</td>
                        </tr>
                    ';
                }else{
                    $total=0;
                    $no=1;
                    $query = mysqli_query($Conn, "SELECT * FROM radiologi_invoice WHERE id_radiologi='$id_radiologi'");
                    while ($data = mysqli_fetch_array($query)) {
                        $service_name = $data['service_name'];
                        $total_price  = $data['total_price'];
                        $quantity     = $data['quantity'];
                        $amount       = $data['amount'];

                        // Total
                        $total   = $total + $amount;
                        
                        // Format uang
                        $total_price = "Rp " . number_format($total_price,0,',','.');
                        $amount      = "Rp " . number_format($amount,0,',','.');
                        

                        // Menampilkan Baris Invoice
                        echo '
                            <tr>
                                <td align="center">'.$no.'</td>
                                <td align="left">'.$service_name.'</td>
                                <td align="right">'.$total_price.'</td>
                                <td align="center">'.$quantity.'</td>
                                <td align="right">'.$amount.'</td>
                            </tr>
                        ';
                        $no++;
                    }
                    $total = "Rp " . number_format($total,0,',','.');
                    echo '
                        <tr>
                            <td align="right" colspan="4">
                                <b>Jumlah Total</b>
                            </td>
                            <td align="right"><b>'.$total.'</b></td>
                        </tr>
                    ';
                }
            ?>
        </table>
    </body>
</html>