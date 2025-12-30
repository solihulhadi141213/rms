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
    $total_price              = round($Data['total_price']);
    $quantity                 = round($Data['quantity']);
    $amount                   = round($Data['amount']);

    echo '
        <input type="hidden" name="id_radiologi_invoice" value="'.$id_radiologi_invoice.'">
        <div class="row mb-3">
            <div class="col-4">
                <label for="service_name_edit">
                    <small>Nama Tarif</small>
                </label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="text" name="service_name" id="service_name_edit" class="form-control" value="'.$service_name.'" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-4">
                <label for="total_price_edit">
                    <small>Tarif/Harga</small>
                </label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="text" name="total_price" id="total_price_edit" class="form-control form-money" value="'.$total_price.'" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-4">
                <label for="quantity_edit">
                    <small>Quantity</small>
                </label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="text" name="quantity" id="quantity_edit" class="form-control form-money" value="'.$quantity.'" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-4">
                <label for="amount_edit">
                    <small>Total Tagihan</small>
                </label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="text" name="amount" id="amount_edit" class="form-control form-money" value="'.$amount.'" required>
            </div>
        </div>
    ';
    
?>