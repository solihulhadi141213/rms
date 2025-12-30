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
                    <div class="alert alert-danger"><span>Sesi Akses Sudah Berakhir! Silahkan Login Ulang.</span></div>
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
                    <div class="alert alert-danger"><span>ID Pemeriksaan Tiidak Boleh Kosong!</span></div>
                </div>
            </div>
        ';
        exit;
    }

    //Buat variabel 'id_radiologi' dan sanitasi
    $id_radiologi = validateAndSanitizeInput($_POST['id_radiologi']);

    //Buka Detail 'radiologi'  Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT id_radiologi FROM radiologi WHERE id_radiologi = ?");
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

    // Validasi 'id_radiologi'
    if(empty($Data['id_radiologi'])){
        echo '
            <div class="row mb-3">
                <div class="col-12 text-center">
                    <div class="alert alert-danger"><span>ID Radiologi Tidak Valid!</span></div>
                </div>
            </div>
        ';
        exit;
    }

    //Buat Variabel
    $id_radiologi = $Data['id_radiologi'];
?>
<input type="hidden" name="id_radiologi" value="<?php echo $id_radiologi; ?>">
<div class="row mb-3 border-1 border-bottom">
    <div class="col-4 mb-3">
        <label for="id_master_service_prices">
            <small>Pilih Tarif</small>
        </label>
    </div>
    <div class="col-1 mb-3"><small>:</small></div>
    <div class="col-7 mb-3">
        <select name="kode_tarif" id="kode_tarif" class="form-control">
            <option value="">Pilih</option>
            <?php
                $query = mysqli_query($Conn, "SELECT * FROM master_service_prices WHERE is_active=1 ORDER BY service_category ASC");
                while ($data = mysqli_fetch_array($query)) {
                    $id_master_service_prices = $data['id_master_service_prices'];
                    $service_name             = $data['service_name'];
                    $service_category         = $data['service_category'];
                    $modality                 = $data['modality'];
                    $total_price              = $data['total_price'];
                    $is_active                = $data['is_active'];

                    //Tampilkan List Option
                    echo '
                        <option value="'.$id_master_service_prices.'">'.$modality.' - '.$service_name.'</option>
                    ';
                }
            ?>
        </select>
        <small class="text text-grayish">
            Jika tarif belum terdaftar, silahkan isi tarif secara manual.
        </small>
    </div>
</div>
<div class="row mb-3">
    <div class="col-4">
        <label for="service_name">
            <small>Nama Tarif</small>
        </label>
    </div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <input type="text" name="service_name" id="service_name" class="form-control" required>
    </div>
</div>
<div class="row mb-3">
    <div class="col-4">
        <label for="service_category">
            <small>Kategori Tarif</small>
        </label>
    </div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <input type="text" name="service_category" id="service_category" class="form-control" required>
    </div>
</div>
<div class="row mb-3">
    <div class="col-4">
        <label for="modality">
            <small>Modalitas</small>
        </label>
    </div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <input type="text" name="modality" id="modality" class="form-control" required>
    </div>
</div>
<div class="row mb-3">
    <div class="col-4">
        <label for="patient_class">
            <small>Kelas Inap</small>
        </label>
    </div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <input type="text" name="patient_class" id="patient_class" class="form-control">
        <small class="text text-grayish">Diisi apabila tarif berkaitan dengan kelas inap</small>
    </div>
</div>
<div class="row mb-3">
    <div class="col-4">
        <label for="insurance_type">
            <small>Asuransi/BPJS</small>
        </label>
    </div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <input type="text" name="insurance_type" id="insurance_type" list="list_insurance_type" class="form-control" required>
        <datalist id="list_insurance_type">
            <?php
                $query_asuransi = mysqli_query($Conn, "SELECT DISTINCT insurance_type FROM master_service_prices ORDER BY insurance_type ASC");
                while ($data_asuransi = mysqli_fetch_array($query_asuransi)) {
                    $insurance_type = $data_asuransi['insurance_type'];
                    //Tampilkan List Option
                    echo '<option value="'.$insurance_type.'">';
                }
            ?>
        </datalist>
    </div>
</div>
<div class="row mb-3">
    <div class="col-4">
        <label for="base_price">
            <small>Tarif Dasar</small>
        </label>
    </div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <input type="text" name="base_price" id="base_price" class="form-control form-money" required>
    </div>
</div>
<div class="row mb-3">
    <div class="col-4">
        <label for="doctor_fee">
            <small>Jasa Dokter</small>
        </label>
    </div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <input type="text" name="doctor_fee" id="doctor_fee" class="form-control form-money">
    </div>
</div>
<div class="row mb-3">
    <div class="col-4">
        <label for="radiographers_fee">
            <small>Jasa Radiografer</small>
        </label>
    </div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <input type="text" name="radiographers_fee" id="radiographers_fee" class="form-control form-money">
    </div>
</div>
<div class="row mb-3">
    <div class="col-4">
        <label for="facility_fee">
            <small>Jasa RS</small>
        </label>
    </div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <input type="text" name="facility_fee" id="facility_fee" class="form-control form-money">
    </div>
</div>
<div class="row mb-3 ">
    <div class="col-4">
        <label for="equipment_fee">
            <small>Beban Alat & BHP</small>
        </label>
    </div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <input type="text" name="equipment_fee" id="equipment_fee" class="form-control form-money">
    </div>
</div>
<div class="row mb-3 border-1 border-bottom">
    <div class="col-4 mb-3">
        <label for="total_price">
            <small>Total Harga</small>
        </label>
    </div>
    <div class="col-1 mb-3"><small>:</small></div>
    <div class="col-7 mb-3">
        <input type="text" name="total_price" id="total_price" class="form-control form-money" required>
    </div>
</div>
<div class="row mb-3">
    <div class="col-4">
        <label for="quantity">
            <small>Quantity</small>
        </label>
    </div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <input type="text" name="quantity" id="quantity" class="form-control form-money" value="1" required>
    </div>
</div>
<div class="row mb-3">
    <div class="col-4">
        <label for="amount">
            <small>Total Tagihan</small>
        </label>
    </div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <input type="text" name="amount" id="amount" class="form-control form-money" required>
    </div>
</div>