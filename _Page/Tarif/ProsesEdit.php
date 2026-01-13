<?php
    // ProsesEdit.php
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    // Fungsi money
    function moneyToNumber($value) {
        if ($value === null || $value === '') return 0;
        return (float) str_replace(['.', ','], ['', '.'], $value);
    }

    // ======================================================
    // VALIDASI SESSION
    // ======================================================
    if (empty($SessionIdAccess)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Sesi Akses Sudah Berakhir. Silahkan Login Ulang!'
        ]);
        exit;
    }

    // ======================================================
    // VALIDASI INPUT WAJIB
    // ======================================================
    if (empty($_POST['id_master_service_prices'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'ID Tarif tidak boleh kosong!'
        ]);
        exit;
    }

    if (empty($_POST['service_name'])) {
        echo json_encode(['status'=>'error','message'=>'Nama Tarif tidak boleh kosong!']);
        exit;
    }

    if (empty($_POST['service_category'])) {
        echo json_encode(['status'=>'error','message'=>'Kategori Tarif tidak boleh kosong!']);
        exit;
    }

    if (empty($_POST['modality'])) {
        echo json_encode(['status'=>'error','message'=>'Kelompok modalitas tidak boleh kosong!']);
        exit;
    }

    if (empty($_POST['insurance_type'])) {
        echo json_encode(['status'=>'error','message'=>'Tipe asuransi tidak boleh kosong!']);
        exit;
    }

    if (empty($_POST['base_price'])) {
        echo json_encode(['status'=>'error','message'=>'Tarif dasar tidak boleh kosong!']);
        exit;
    }

    if (empty($_POST['total_price'])) {
        echo json_encode(['status'=>'error','message'=>'Total tarif tidak boleh kosong!']);
        exit;
    }

    // ======================================================
    // SANITASI & KONVERSI DATA
    // ======================================================
    $id_master_service_prices = validateAndSanitizeInput($_POST['id_master_service_prices']);
    $service_name     = validateAndSanitizeInput($_POST['service_name']);
    $service_category = validateAndSanitizeInput($_POST['service_category']);
    $modality         = validateAndSanitizeInput($_POST['modality']);
    $insurance_type   = validateAndSanitizeInput($_POST['insurance_type']);
    $patient_class    = validateAndSanitizeInput($_POST['patient_class'] ?? null);

    // Konversi uang
    $doctor_fee        = moneyToNumber($_POST['doctor_fee'] ?? 0);
    $radiographers_fee = moneyToNumber($_POST['radiographers_fee'] ?? 0);
    $facility_fee      = moneyToNumber($_POST['facility_fee'] ?? 0);
    $equipment_fee     = moneyToNumber($_POST['equipment_fee'] ?? 0);
    $base_price        = moneyToNumber($_POST['base_price']);
    $total_price       = moneyToNumber($_POST['total_price']);

    // ======================================================
    // TANGGAL
    // ======================================================
    $effective_date = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
    $expired_date   = (clone $effective_date)->modify('+1 year');

    $effective_date_db = $effective_date->format('Y-m-d H:i:s');
    $expired_date_db   = $expired_date->format('Y-m-d H:i:s');

    // ======================================================
    // STATUS
    // ======================================================
    $is_active = 1;

    // ======================================================
    // PROSES UPDATE
    // ======================================================
    $query = "
        UPDATE master_service_prices SET
            service_name = ?,
            service_category = ?,
            modality = ?,
            patient_class = ?,
            insurance_type = ?,
            base_price = ?,
            doctor_fee = ?,
            radiographers_fee = ?,
            facility_fee = ?,
            equipment_fee = ?,
            total_price = ?,
            is_active = ?,
            effective_date = ?,
            expired_date = ?
        WHERE id_master_service_prices = ?
    ";

    $stmt = $Conn->prepare($query);

    $stmt->bind_param(
        "ssssssssssssssi",
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
        $expired_date_db,
        $id_master_service_prices
    );

    if ($stmt->execute()) {

        if ($stmt->affected_rows > 0) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Data tarif berhasil diperbarui'
            ]);
        } else {
            echo json_encode([
                'status' => 'warning',
                'message' => 'Tidak ada perubahan data'
            ]);
        }

    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal memperbarui data: ' . $stmt->error
        ]);
    }

    $stmt->close();
    exit;
?>