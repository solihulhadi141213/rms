<?php
    //koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";
    
    //Zona Waktu
    date_default_timezone_set("Asia/Jakarta");

    //Session Akses
    if(empty($SessionIdAccess)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Sesi Akses Sudah Berakhir. Silahkan Login Ulang!',
            'metadata' => []
        ]);
        exit;
    }

    //kode_tarif wajib terisi
    if(empty($_POST['kode_tarif'])){
        echo json_encode([
            'status' => 'success',
            'message' => 'Kode tarif Kosong',
            'metadata' => []
        ]);
        exit;
    }

    //Buat variabel 'kode_tarif' dan sanitasi
    $kode_tarif = validateAndSanitizeInput($_POST['kode_tarif']);

    // Buka Data
    $Qry = $Conn->prepare("SELECT * FROM master_service_prices WHERE id_master_service_prices = ?");
    $Qry->bind_param("i", $kode_tarif);
    if (!$Qry->execute()) {
        $error=$Conn->error;
        echo json_encode([
            'status' => 'success',
            'message' => 'Terjadi kesalahan pada saat membuka data dari database!<br>Keterangan : '.$error.'',
            'metadata' => []
        ]);
        exit;
    }
    $Result = $Qry->get_result();
    $Data = $Result->fetch_assoc();
    $Qry->close();
    $base_price        = round($Data['base_price']);
    $doctor_fee        = round($Data['doctor_fee']);
    $radiographers_fee = round($Data['radiographers_fee']);
    $facility_fee      = round($Data['facility_fee']);
    $equipment_fee     = round($Data['equipment_fee']);
    $total_price       = round($Data['total_price']);
    //Buat Variabel
    $metadata = [
        "service_name"      => $Data['service_name'],
        "service_category"  => $Data['service_category'],
        "modality"          => $Data['modality'],
        "patient_class"     => $Data['patient_class'],
        "insurance_type"    => $Data['insurance_type'],
        "base_price"        => $base_price,
        "doctor_fee"        => $doctor_fee,
        "radiographers_fee" => $radiographers_fee,
        "facility_fee"      => $facility_fee,
        "equipment_fee"     => $equipment_fee,
        "total_price"       => $total_price
    ];

    echo json_encode([
        'status' => 'success',
        'message' => 'Data berhasil ditampilkan',
        'metadata' => $metadata
    ]);
    exit;
?>