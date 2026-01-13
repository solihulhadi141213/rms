<?php
    //koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";
    
    //Zona Waktu
    date_default_timezone_set("Asia/Jakarta");

    //Session Akses
    if(empty($SessionIdAccess)){
        echo '
            <div class="row mb-3">
                <div class="col-12 text-center">
                    <div class="alert alert-danger"><small>Sesi Akses Sudah Berakhir! Silahkan Login Ulang.</small></div>
                </div>
            </div>
        ';
        exit;
    }

    //id_radiologi wajib terisi
    if(empty($_POST['id_radiologi'])){
        echo '
            <div class="row mb-3">
                <div class="col-12 text-center">
                    <div class="alert alert-danger"><small>ID Pemeriksaan Tiidak Boleh Kosong!</small></div>
                </div>
            </div>
        ';
        exit;
    }

    //Buat variabel 'id_radiologi' dan sanitasi
    $id_radiologi = validateAndSanitizeInput($_POST['id_radiologi']);

    //Buka Detail Koneksi Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM radiologi WHERE id_radiologi = ?");
    $Qry->bind_param("i", $id_radiologi);
    if (!$Qry->execute()) {
        $error=$Conn->error;
        echo '
            <div class="alert alert-danger">
                <small>Terjadi kesalahan pada saat membuka data dari database!<br>Keterangan : '.$error.'</small>
            </div>
        ';
        exit;
    }
    $Result = $Qry->get_result();
    $Data = $Result->fetch_assoc();
    $Qry->close();

    if(empty($Data['id_radiologi'])){
        echo '
            <div class="alert alert-danger">
                <small>Data Pemeriksaan Radiologi Tidak Ditemukan</small>
            </div>
        ';
        exit;
    }

    //Buat Variabel
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

    // Cari Data Invoice
    $jumlah_invoice = mysqli_num_rows(mysqli_query($Conn, "SELECT id_radiologi FROM radiologi_invoice WHERE id_radiologi='$id_radiologi'"));

    // Menampilkan Preview Pelayanan Radiologi
    echo '
        <input type="hidden" name="data" value="Nota">
        <input type="hidden" name="an" value="'.$accession_number.'">
        <div class="row mb-2">
            <div class="col-12">
                <small>
                    <b>A. Informasi Pendaftaran</b>
                </small>
            </div>
        </div>
         <div class="row mb-2">
            <div class="col-5"><small>No.RM</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-6"><small>'.$id_pasien.'</small></div>
        </div>
        <div class="row mb-2">
            <div class="col-5"><small>Accession Number</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-6"><small>'.$accession_number.'</small></div>
        </div>
        <div class="row mb-2">
            <div class="col-5"><small>Nama Pasien</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-6"><small>'.$nama_pasien.'</small></div>
        </div>
        <div class="row mb-2">
            <div class="col-5"><small>Tanggal Periksa</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-6"><small>'.date('d/m/Y H:i', strtotime($datetime_diminta)).'</small></div>
        </div>
        <div class="row mb-2">
            <div class="col-12">
                <small>
                    <b>B. Rincian Invoice</b>
                </small>
            </div>
        </div>
    ';
?>
<div class="row mb-2">
    <div class="col-12">
        <div class="table table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <td class="text-center"><b>No</b></td>
                        <td class="text-left"><b>Uraian</b></td>
                        <td class="text-end"><b>Tarif</b></td>
                        <td class="text-center"><b>QTY</b></td>
                        <td class="text-end"><b>Jumlah</b></td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        // Apabila Data Tidak Ada
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
                                    <td class="text-end" colspan="4">
                                        <b>Jumlah Total</b>
                                    </td>
                                    <td class="text-end"><b>'.$total.'</b></td>
                                </tr>
                            ';
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
