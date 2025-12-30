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

    //id_radiologi_invoice wajib terisi
    if(empty($_POST['id_radiologi_invoice'])){
        echo '
            <div class="row mb-3">
                <div class="col-12 text-center">
                    <div class="alert alert-danger"><small>ID Invoice Tidak Boleh Kosong!</small></div>
                </div>
            </div>
        ';
        exit;
    }

    //Buat variabel 'id_radiologi_invoice' dan sanitasi
    $id_radiologi_invoice = validateAndSanitizeInput($_POST['id_radiologi_invoice']);

    //Buka Detail Radiologi Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM radiologi_invoice WHERE id_radiologi_invoice = ?");
    $Qry->bind_param("i", $id_radiologi_invoice);
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

    //Buat Variabel
    $service_name             = $Data['service_name'];
    $total_price              = $Data['total_price'];
    $quantity                 = $Data['quantity'];
    $amount                   = $Data['amount'];
    // Format uang
    $total_price = "Rp " . number_format($total_price,0,',','.');
    $amount      = "Rp " . number_format($amount,0,',','.');

    echo '
        <input type="hidden" name="id_radiologi_invoice" value="'.$id_radiologi_invoice.'">
        <div class="row mb-2">
            <div class="col-4"><small>Nama Tarif</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7"><small class="text text-grayish">'.$service_name.'</small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small>Harga / Tarif</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7"><small class="text text-grayish">'.$total_price.'</small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small>Quantity</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7"><small class="text text-grayish">'.$quantity.'</small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small>Jumlah / Total</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7"><small class="text text-grayish">'.$amount.'</small>
            </div>
        </div>
        <div class="row mb-2 mt-3">
            <div class="col-12 text-center">
                <div class="alert alert-danger">
                    <small>
                        <b>Apakah Anda Yakin Ingin Menghapus Data Tersebut?</b>
                    </small>
                </div>
            </div>
        </div>
    ';
    
?>