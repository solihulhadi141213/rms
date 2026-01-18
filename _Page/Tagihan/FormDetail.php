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
                <span>Terjadi kesalahan pada saat membuka data dari database!<br>Keterangan : '.$error.'</span>
            </div>
        ';
        exit;
    }
    $Result = $Qry->get_result();
    $Data = $Result->fetch_assoc();
    $Qry->close();

    //Buat Variabel
    $id_pasien              = $Data['id_pasien'];
    $id_kunjungan           = $Data['id_kunjungan'];
    $accession_number       = $Data['accession_number'];
    $nama_pasien            = $Data['nama_pasien'];
    $priority               = $Data['priority'];
    $asal_kiriman           = $Data['asal_kiriman'];
    $alat_pemeriksa         = $Data['alat_pemeriksa'];
    $kode_dokter_pengirim   = !empty($Data['kode_dokter_pengirim']) ? $Data['kode_dokter_pengirim'] : "-";
    $ihs_dokter_pengirim    = !empty($Data['kode_dokter_pengirim']) ? $Data['ihs_dokter_pengirim'] : "-";
    $nama_dokter_pengirim   = !empty($Data['nama_dokter_pengirim']) ? $Data['nama_dokter_pengirim'] : "-";
    $kode_dokter_penerima   = !empty($Data['kode_dokter_penerima']) ? $Data['kode_dokter_penerima'] : "-";
    $ihs_dokter_penerima    = !empty($Data['ihs_dokter_penerima']) ? $Data['ihs_dokter_penerima'] : "-";
    $nama_dokter_penerima   = !empty($Data['nama_dokter_penerima']) ? $Data['nama_dokter_penerima'] : "-";
    $radiografer            = !empty($Data['radiografer']) ? $Data['radiografer'] : "-";
    $permintaan_pemeriksaan = $Data['permintaan_pemeriksaan'];
    $tujuan                 = $Data['tujuan'];
    $pembayaran             = $Data['pembayaran'];
    $datetime_diminta       = !empty($Data['datetime_diminta']) ? $Data['datetime_diminta'] : "-";

    // Definisikan Permintaan Pemeriksaan
    $nama_pemeriksaan ="-";

    if(!empty($permintaan_pemeriksaan)){
        $permintaan_pemeriksaan_arry = json_decode($permintaan_pemeriksaan, true);
        foreach ($permintaan_pemeriksaan_arry as $permintaan_pemeriksaan_list){
            $nama_pemeriksaan = $permintaan_pemeriksaan_list ['nama_pemeriksaan'];
        }
    }
    echo '
        <input type="hidden" name="data" value="Nota">
        <input type="hidden" name="an" value="'.$accession_number.'">
    ';
?>

<!-- Menampilkan Informasi Dalam 2 Kolom-->
 <div class="row">
    <div class="col-md-6">
        <div class="row mb-2 mt-3">
            <div class="col-12">
                <small><b>A. Informasi Pasien</b></small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small>No.RM</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text text-grayish"><?php echo $id_pasien; ?></small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small>ACN</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text text-grayish"><?php echo $accession_number; ?></small>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-4"><small>Nama Pasien</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text text-grayish"><?php echo $nama_pasien; ?></small>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="row mb-2 mt-3">
            <div class="col-12">
                <small><b>B. Informasi Kunjungan</b></small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small>Asal Kiriman</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text text-grayish"><?php echo $asal_kiriman; ?></small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small>Kunjungan</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text text-grayish"><?php echo $tujuan; ?></small>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-4"><small>Pembayaran</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text text-grayish"><?php echo $pembayaran; ?></small>
            </div>
        </div>
    </div>
 </div>
<div class="row mb-2 mt-3">
    <div class="col-12">
        <small><b>C. Informasi Radiologi</b></small>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="row mb-2">
            <div class="col-4"><small><i>Modality</i></small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text text-grayish"><?php echo $alat_pemeriksa; ?></small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small>Permintaan Pemeriksaan</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text text-grayish"><?php echo $nama_pemeriksaan; ?></small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small>Dokter - Pengirim</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text text-grayish"><?php echo $nama_dokter_pengirim; ?></small>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="row mb-2">
            <div class="col-4"><small>Dokter - Penerima</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text text-grayish"><?php echo $nama_dokter_penerima; ?></small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small>Tanggal / Jam</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text text-grayish"><?php echo $datetime_diminta; ?></small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small>Petugas / Radiografer</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text text-grayish"><?php echo $radiografer; ?></small>
            </div>
        </div>
    </div>
</div>
<div class="row mt-3 mb-2">
    <div class="col-10">
        <small><b>D. Rincian Tagihan</b></small>
    </div>
    <div class="col-2 text-end">
        <button type="button" class="btn btn-md btn-floating btn-primary modal_tambah_tagihan" data-id="<?php echo "$id_radiologi"; ?>">
            <i class="bi bi-plus"></i>
        </button>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="table table-responsive">
            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <td class="text-center"><b>No</b></td>
                        <td><b>Uraian / Keterangan</b></td>
                        <td class="text-end"><b>Tarif</b></td>
                        <td class="text-center"><b>Qty</b></td>
                        <td class="text-end"><b>Jumlah</b></td>
                        <td class="text-center"><b>Opsi</b></td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $total   = 0;
                        $jumlah_nota = mysqli_num_rows(mysqli_query($Conn, "SELECT id_radiologi_invoice FROM radiologi_invoice WHERE id_radiologi='$id_radiologi'"));
                        if(empty($jumlah_nota)){
                            echo '
                                <tr>
                                    <td colspan="6" class="text-center">
                                        <span class="text-center">Belum Ada Uraian Tagihan</span>
                                    </td>
                                </tr>
                            ';
                        }else{
                            $no_nota = 1;
                            $query_nota = mysqli_query($Conn, "SELECT * FROM radiologi_invoice WHERE id_radiologi='$id_radiologi'");
                            while ($data_nota = mysqli_fetch_array($query_nota)) {
                                $id_radiologi_invoice     = $data_nota['id_radiologi_invoice'];
                                $id_master_service_prices = $data_nota['id_master_service_prices'];
                                $service_name             = $data_nota['service_name'];
                                $total_price              = $data_nota['total_price'];
                                $quantity                 = $data_nota['quantity'];
                                $amount                   = $data_nota['amount'];

                                // Total
                                $total   = $total + $amount;
                                
                                // Format uang
                                $total_price = "Rp " . number_format($total_price,0,',','.');
                                $amount      = "Rp " . number_format($amount,0,',','.');
                                
                                // menampilkan data
                                echo '
                                    <tr>
                                        <td class="text-center">'.$no_nota.'</td>
                                        <td>'.$service_name.'</td>
                                        <td class="text-end">'.$total_price.'</td>
                                        <td class="text-center">'.$quantity.'</td>
                                        <td class="text-end">'.$amount.'</td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-secondary btn-floating"  data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="">
                                                <li>
                                                    <a href="javascript:void(0)" class="dropdown-item modal_edit_nota" data-id="'.$id_radiologi_invoice .'">
                                                        <i class="bi bi-pencil"></i> Edit
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)" class="dropdown-item modal_hapus_nota"  data-id="'.$id_radiologi_invoice .'">
                                                        <i class="bi bi-x"></i> Hapus
                                                    </a>
                                                </li>
                                            </ul>
                                        </td>
                                    </tr>
                                ';
                                $no_nota++;
                            }
                        }
                         $total = "Rp " . number_format($total,0,',','.');
                        echo '
                            <tr>
                                <td class="" colspan="4">
                                    <b>JUMLAH TOTAL</b>
                                </td>
                                <td class="text-end"><b>'.$total.'</b></td>
                                <td class="text-end"></td>
                            </tr>
                        ';
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

