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
    $base_price        = "" . number_format($base_price,0,',','.');
    $doctor_fee        = "" . number_format($doctor_fee,0,',','.');
    $radiographers_fee = "" . number_format($radiographers_fee,0,',','.');
    $facility_fee      = "" . number_format($facility_fee,0,',','.');
    $equipment_fee     = "" . number_format($equipment_fee,0,',','.');
    $total_price       = "" . number_format($total_price,0,',','.');
?>
<input type="hidden" name="id_master_service_prices" value="<?php echo $id_master_service_prices; ?>">
<div class="row mb-3">
    <div class="col-md-12">
        <label for="service_name_edit">
            <small>Nama Tarif</small>
        </label>
        <input type="text" class="form-control" name="service_name" id="service_name_edit" value="<?php echo $service_name; ?>" required>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-12">
        <label for="service_category_edit">
            <small>Kategori Tarif</small>
        </label>
        <input type="text" class="form-control" name="service_category" id="service_category_edit" list="service_category_list" value="<?php echo $service_category; ?>" required>
        <datalist id="service_category_list"></datalist>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-12">
        <label for="modality_edit">
            <small><i>Modality</i> (Alat)</small>
        </label>
        <select name="modality" id="modality_edit" class="form-control" required>
            <option <?php if($modality==""){echo "selected";} ?> value="">Pilih</option>
            <option <?php if($modality=="XR"){echo "selected";} ?> value="XR">X-Ray</option>
            <option <?php if($modality=="CT"){echo "selected";} ?> value="CT">CT-Scan</option>
            <option <?php if($modality=="US"){echo "selected";} ?> value="US">USG</option>
            <option <?php if($modality=="MR"){echo "selected";} ?> value="MR">MRI</option>
            <option <?php if($modality=="NM"){echo "selected";} ?> value="NM">Nuclear Medicine (Kedokteran nuklir)</option>
            <option <?php if($modality=="PT"){echo "selected";} ?> value="PT">PET Scan</option>
            <option <?php if($modality=="DX"){echo "selected";} ?> value="DX">Digital Radiography</option>
            <option <?php if($modality=="CR"){echo "selected";} ?> value="CR">Computed Radiography</option>
        </select>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-12">
        <label for="patient_class_edit">
            <small>Kelas Inap</small>
        </label>
        <input type="text" class="form-control" name="patient_class" id="patient_class_edit" value="<?php echo $patient_class; ?>">
        <small class="text text-grayish">Jika Tarif Tersebut Berkaitan Dengan Kelas Inap</small>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-12">
        <label for="insurance_type_edit">
            <small><i>Metode Pembayaran</i></small>
        </label>
        <select name="insurance_type" id="insurance_type_edit" class="form-control" required>
            <option <?php if($insurance_type==""){echo "selected";} ?> value="">Pilih</option>
            <option <?php if($insurance_type=="UMUM"){echo "selected";} ?> value="UMUM">UMUM</option>
            <option <?php if($insurance_type=="BPJS"){echo "selected";} ?> value="BPJS">BPJS</option>
            <option <?php if($insurance_type=="Umum dan BPJS"){echo "selected";} ?> value="Umum dan BPJS">Umum dan BPJS</option>
        </select>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-12">
        <label for="base_price_edit">
            <small>Tarif Dasar</small>
        </label>
        <input type="text" class="form-control form-money" name="base_price" id="base_price_edit" placeholder="Rp" value="<?php echo $base_price; ?>" required>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-12">
        <label for="doctor_fee_edit">
            <small>Jasa Dokter</small>
        </label>
        <input type="text" class="form-control form-money" name="doctor_fee" id="doctor_fee_edit" placeholder="Rp" value="<?php echo $doctor_fee; ?>">
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-12">
        <label for="radiographers_fee_edit">
            <small>Jasa Radiografer</small>
        </label>
        <input type="text" class="form-control form-money" name="radiographers_fee" id="radiographers_fee_edit" placeholder="Rp" value="<?php echo $radiographers_fee; ?>">
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-12">
        <label for="facility_fee_edit_edit">
            <small>Jasa RS</small>
        </label>
        <input type="text" class="form-control form-money" name="facility_fee" id="facility_fee_edit_edit" placeholder="Rp" value="<?php echo $facility_fee; ?>">
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-12">
        <label for="equipment_fee_edit">
            <small>Beban Alat & BHP</small>
        </label>
        <input type="text" class="form-control form-money" name="equipment_fee" id="equipment_fee_edit" placeholder="Rp" value="<?php echo $equipment_fee; ?>">
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-12">
        <label for="total_price_edit">
            <small>Total Harga</small>
        </label>
        <input type="text" class="form-control form-money" name="total_price" id="total_price_edit" placeholder="Rp" value="<?php echo $total_price; ?>" required>
    </div>
</div>