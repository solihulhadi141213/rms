<?php
    // ProsesTambah.php
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    // Fungsi money
    function moneyToNumber($value) {
        if ($value === null || $value === '') return 0;
        return (float) str_replace(['.', ','], ['', '.'], $value);
    }

    // Validasi Sesi
    if(empty($SessionIdAccess)){
        echo json_encode(['status' => 'error','message' => 'Sesi Akses Sudah Berakhir. Silahkan Login Ulang!']);
        exit;
    }

    // Validasi input 'id_radiologi_invoice'
    if(empty($_POST['id_radiologi_invoice'])){
        echo json_encode(['status' => 'error','message' => 'ID Tagihan tidak boleh kosong!']);
        exit;
    }

    // Validasi input 'service_name'
    if(empty($_POST['service_name'])){
        echo json_encode(['status' => 'error','message' => 'Nama Tarif tidak boleh kosong!']);
        exit;
    }

    // Validasi input 'total_price'
    if(empty($_POST['total_price'])){
        echo json_encode(['status' => 'error','message' => 'Total Tarif tidak boleh kosong!']);
        exit;
    }

    // Validasi input 'quantity'
    if(empty($_POST['quantity'])){
        echo json_encode(['status' => 'error','message' => 'Quantity tidak boleh kosong!']);
        exit;
    }

    // Validasi input 'amount'
    if(empty($_POST['amount'])){
        echo json_encode(['status' => 'error','message' => 'Amount tidak boleh kosong!']);
        exit;
    }

    // Buat variabel
    $id_radiologi_invoice = validateAndSanitizeInput($_POST['id_radiologi_invoice']);
    $service_name         = validateAndSanitizeInput($_POST['service_name']);
    $total_price         = validateAndSanitizeInput($_POST['total_price']);
    $quantity             = validateAndSanitizeInput($_POST['quantity']);
    $amount               = validateAndSanitizeInput($_POST['amount']);
    
    // Ubah format uang menjadi nomor
    $total_price = moneyToNumber($total_price);
    $quantity    = moneyToNumber($quantity);
    $amount      = moneyToNumber($amount);

    $update_invoice = mysqli_query($Conn,"UPDATE radiologi_invoice SET 
        service_name='$service_name',
        total_price='$total_price',
        quantity='$quantity',
        amount='$amount'
    WHERE id_radiologi_invoice='$id_radiologi_invoice'") or die(mysqli_error($Conn)); 
    if($update_invoice){
        echo json_encode(['status' => 'success','message' => 'Update Invoice Berhasil!']);
        exit;
    }else{
        echo json_encode(['status' => 'error','message' => 'Update Invoice gagal!']);
        exit;
    }

?>