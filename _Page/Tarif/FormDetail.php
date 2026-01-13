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

    //id_master_service_prices wajib terisi
    if(empty($_POST['id_master_service_prices'])){
        echo '
            <div class="row mb-3">
                <div class="col-12 text-center">
                    <div class="alert alert-danger"><small>ID Tarif Tiidak Boleh Kosong!</small></div>
                </div>
            </div>
        ';
        exit;
    }

    //Buat variabel 'id_master_service_prices' dan sanitasi
    $id_master_service_prices = validateAndSanitizeInput($_POST['id_master_service_prices']);

    //Buka Detail Koneksi Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM master_service_prices WHERE id_master_service_prices = ?");
    $Qry->bind_param("i", $id_master_service_prices);
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
    $service_name      = $Data['service_name'];
    $service_category  = $Data['service_category'];
    $modality          = $Data['modality'];
    $patient_class     = $Data['patient_class'];
    $insurance_type    = $Data['insurance_type'];
    $base_price        = $Data['base_price'];
    $doctor_fee        = $Data['doctor_fee'];
    $radiographers_fee = $Data['radiographers_fee'];
    $facility_fee      = $Data['facility_fee'];
    $equipment_fee     = $Data['equipment_fee'];
    $total_price       = $Data['total_price'];
    $is_active         = $Data['is_active'];
    $effective_date    = $Data['effective_date'];
    $expired_date      = $Data['expired_date'];

    // Format Uang
    $base_price        = "Rp " . number_format($base_price,0,',','.');
    $doctor_fee        = "Rp " . number_format($doctor_fee,0,',','.');
    $radiographers_fee = "Rp " . number_format($radiographers_fee,0,',','.');
    $facility_fee      = "Rp " . number_format($facility_fee,0,',','.');
    $equipment_fee     = "Rp " . number_format($equipment_fee,0,',','.');
    $total_price       = "Rp " . number_format($total_price,0,',','.');
    
    echo '
        <div class="row mb-2">
            <div class="col-4"><small>Nama Tarif</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7"><small class="text text-grayish">'.$service_name.'</small></div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small>Kategori Tarif</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7"><small class="text text-grayish">'.$service_category.'</small></div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small><i>Modality</i> (Alat/Pesawat)</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7"><small class="text text-grayish">'.$modality.'</small></div>
        </div>
    ';

    if(!empty($patient_class)){
        echo '
            <div class="row mb-2">
                <div class="col-4"><small><i>Kelas Inap</i></small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7"><small class="text text-grayish">'.$patient_class.'</small></div>
            </div>
        ';
    }
    echo '
        <div class="row mb-2">
            <div class="col-4"><small><i>Metode Pembayaran</i></small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7"><small class="text text-grayish">'.$insurance_type.'</small></div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small><i>Tarif Dasar</i></small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7"><small class="text text-grayish">'.$base_price.'</small></div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small><i>Jasa Dokter</i></small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7"><small class="text text-grayish">'.$doctor_fee.'</small></div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small><i>Jasa Radiografer</i></small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7"><small class="text text-grayish">'.$radiographers_fee.'</small></div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small><i>Jasa RS (Faskes)</i></small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7"><small class="text text-grayish">'.$facility_fee.'</small></div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small><i>Perlengkapan & BHP</i></small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7"><small class="text text-grayish">'.$equipment_fee.'</small></div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small><i>Total Tarif</i></small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7"><small class="text text-grayish">'.$total_price.'</small></div>
        </div>
    ';
?>