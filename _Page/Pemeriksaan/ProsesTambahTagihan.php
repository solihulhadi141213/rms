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
        echo json_encode(['status' => 'error', 'id_radiologi' => '', 'message' => 'Sesi Akses Sudah Berakhir. Silahkan Login Ulang!']);
        exit;
    }

    // Validasi input 'id_radiologi'
    if(empty($_POST['id_radiologi'])){
        echo json_encode(['status' => 'error','message' => 'ID Radiologi tidak boleh kosong!']);
        exit;
    }

    // Validasi input 'service_name'
    if(empty($_POST['service_name'])){
        echo json_encode(['status' => 'error', 'id_radiologi' => '', 'message' => 'Nama Tarif tidak boleh kosong!']);
        exit;
    }

    // Validasi input 'service_category'
    if(empty($_POST['service_category'])){
        echo json_encode(['status' => 'error', 'id_radiologi' => '', 'message' => 'Kategori Tarif tidak boleh kosong!']);
        exit;
    }

    // Validasi input 'modality'
    if(empty($_POST['modality'])){
        echo json_encode(['status' => 'error', 'id_radiologi' => '', 'message' => 'Informasi kelompok modalitas tidak boleh kosong tidak boleh kosong!']);
        exit;
    }

    // Validasi input 'insurance_type'
    if(empty($_POST['insurance_type'])){
        echo json_encode(['status' => 'error', 'id_radiologi' => '', 'message' => 'Kelompok tipe asuransi tidak boleh kosong!']);
        exit;
    }

    // Validasi input 'base_price'
    if(empty($_POST['base_price'])){
        echo json_encode(['status' => 'error', 'id_radiologi' => '', 'message' => 'Tarif dasar tidak boleh kosong!']);
        exit;
    }

    // Validasi input 'total_price'
    if(empty($_POST['total_price'])){
        echo json_encode(['status' => 'error', 'id_radiologi' => '', 'message' => 'Total Tarif tidak boleh kosong!']);
        exit;
    }

    // Validasi input 'quantity'
    if(empty($_POST['quantity'])){
        echo json_encode(['status' => 'error', 'id_radiologi' => '','message' => 'Quantity tidak boleh kosong!']);
        exit;
    }

    // Validasi input 'amount'
    if(empty($_POST['amount'])){
        echo json_encode(['status' => 'error', 'id_radiologi' => '', 'message' => 'Amount tidak boleh kosong!']);
        exit;
    }

    // Buat variabel
    $id_radiologi     = validateAndSanitizeInput($_POST['id_radiologi']);
    $service_name     = validateAndSanitizeInput($_POST['service_name']);
    $service_category = validateAndSanitizeInput($_POST['service_category']);
    $modality         = validateAndSanitizeInput($_POST['modality']);
    $insurance_type   = validateAndSanitizeInput($_POST['insurance_type']);
    $base_price       = validateAndSanitizeInput($_POST['base_price']);
    $total_price      = validateAndSanitizeInput($_POST['total_price']);
    $quantity         = validateAndSanitizeInput($_POST['quantity']);
    $amount           = validateAndSanitizeInput($_POST['amount']);

    // Buat variabel yang tidak wajib
    $id_master_service_prices = validateAndSanitizeInput($_POST['kode_tarif'] ?? null);
    $patient_class            = validateAndSanitizeInput($_POST['patient_class'] ?? null);
    $patient_class            = $_POST['patient_class'] ?? null;
    
    // Ubah format uang menjadi nomor
    $doctor_fee        = moneyToNumber($_POST['doctor_fee'] ?? 0);
    $radiographers_fee = moneyToNumber($_POST['radiographers_fee'] ?? 0);
    $facility_fee      = moneyToNumber($_POST['facility_fee'] ?? 0);
    $equipment_fee     = moneyToNumber($_POST['equipment_fee'] ?? 0);
    $base_price        = moneyToNumber($base_price);
    $total_price       = moneyToNumber($total_price);
    $quantity          = moneyToNumber($quantity);
    $amount            = moneyToNumber($amount);

    // Menyatakan 'effective_date'
    $effective_date = new DateTime('now', new DateTimeZone('Asia/Jakarta'));

    // Menghitung 'expired_date' (1 tahun setelah effective_date)
    $expired_date = (clone $effective_date)->modify('+1 year');

    // Jika perlu format string untuk simpan ke database
    $effective_date_db = $effective_date->format('Y-m-d H:i:s');
    $expired_date_db   = $expired_date->format('Y-m-d H:i:s');

    // Menyatakan status aktif
    $is_active = 1;

    // Jika 'kode_tarif' kosong maka insert ke database 'master_service_prices' 
    if(empty($_POST['kode_tarif'])){
        $query = "INSERT INTO master_service_prices (
            service_name,
            service_category,
            modality,
            patient_class,
            insurance_type,
            base_price,
            doctor_fee,
            radiographers_fee,
            facility_fee,
            equipment_fee,
            total_price,
            is_active,
            effective_date,
            expired_date
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $Conn->prepare($query);

        $stmt->bind_param(
            "ssssssssssssss",
            $service_name,
            $service_category,
            $modality,
            $patient_class,
            $insurance_type,
            $base_price,
            $doctor_fee,
            $radiographers_fee,
            $facility_fee,
            $equipment_fee,
            $total_price,
            $is_active,
            $effective_date_db,
            $expired_date_db
        );

        if ($stmt->execute()) {

            // ✅ Ambil ID hasil INSERT
            $id_master_service_prices = $stmt->insert_id;

        } else {

            echo json_encode([
                'status' => 'error',
                'id_radiologi' => '',
                'message' => 'Gagal menyimpan data: ' . $stmt->error
            ]);
            exit;
        }
    }

    // Selanjutnya Simpan Data Ke 'radiologi_invoice' 
    $query2 = "INSERT INTO radiologi_invoice (
        id_radiologi,
        id_master_service_prices,
        service_name,
        total_price,
        quantity,
        amount
    ) VALUES (?, ?, ?, ?, ?, ?)";

    $stmt2 = $Conn->prepare($query2);

    $stmt2->bind_param(
        "iissss",
        $id_radiologi,
        $id_master_service_prices,
        $service_name,
        $total_price,
        $quantity,
        $amount
    );

    if ($stmt2->execute()) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Invoice Berhasil Ditambahkan',
            'id_radiologi' => $id_radiologi
        ]);
        exit;
    } else {

        echo json_encode([
            'status' => 'error',
            'id_radiologi' => '',
            'message' => 'Gagal menyimpan data: ' . $stmt2->error
        ]);
        exit;
    }

    // Tutup Statment
    $stmt->close();
    $stmt2->close();
?>