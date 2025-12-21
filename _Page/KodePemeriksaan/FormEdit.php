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

    //id_master_pemeriksaan wajib terisi
    if(empty($_POST['id_master_pemeriksaan'])){
        echo '
            <div class="row mb-3">
                <div class="col-12 text-center">
                    <div class="alert alert-danger"><small>ID Kode Klinis Tiidak Boleh Kosong!</small></div>
                </div>
            </div>
        ';
        exit;
    }

    //Buat variabel 'id_master_pemeriksaan' dan sanitasi
    $id_master_pemeriksaan = validateAndSanitizeInput($_POST['id_master_pemeriksaan']);

    //Buka Detail Koneksi Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM master_pemeriksaan WHERE id_master_pemeriksaan = ?");
    $Qry->bind_param("i", $id_master_pemeriksaan);
    if (!$Qry->execute()) {
        $error=$Conn->error;
        echo '
            <div class="alert alert-danger">
                <small>Terjadi kesalahan pada saat membuka data dari database!<br>Keterangan : '.$error.'</small>
            </div>
        ';
    }else{
        $Result = $Qry->get_result();
        $Data = $Result->fetch_assoc();
        $Qry->close();

        // Buat Variabel
        $id_master_pemeriksaan   = $Data['id_master_pemeriksaan'];
        $nama_pemeriksaan        = $Data['nama_pemeriksaan'];
        $modalitas               = $Data['modalitas'];
        $pemeriksaan_code        = $Data['pemeriksaan_code'];
        $pemeriksaan_description = $Data['pemeriksaan_description'];
        $pemeriksaan_sys         = $Data['pemeriksaan_sys'];
        $bodysite_code           = $Data['bodysite_code'];
        $bodysite_description    = $Data['bodysite_description'];
        $bodysite_sys            = $Data['bodysite_sys'];

        // Nama Modalitas
        $modalitas_list = [
            'XR' => 'X-Ray',
            'CT' => 'CT-Scan',
            'US' => 'USG',
            'MR' => 'MRI',
            'NM' => 'Nuclear Medicine (Kedokteran Nuklir)',
            'PT' => 'PET Scan',
            'DX' => 'Digital Radiography',
            'CR' => 'Computed Radiography'
        ];

        // Bangun option
        $option_modalitas = '<option value="">Pilih</option>';
        foreach ($modalitas_list as $kode => $label) {
            $selected = ($kode === $modalitas) ? 'selected' : '';
            $option_modalitas .= '<option value="'.$kode.'" '.$selected.'>'.$label.'</option>';
        }

        //Tampilkan Data
        echo '
            <input type="hidden" name="id_master_pemeriksaan" value="'.$id_master_pemeriksaan.'">
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="nama_pemeriksaan_edit">
                        <small>Nama Pemeriksaan</small>
                    </label>
                    <input type="text" class="form-control" name="nama_pemeriksaan" id="nama_pemeriksaan_edit" value="'.$nama_pemeriksaan.'" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="modalitas">
                        <small>Modalitas/Alat</small>
                    </label>
                    <select name="modalitas" id="modalitas" class="form-control" required>
                        '.$option_modalitas.'
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="pemeriksaan_code_edit">
                        <small><i>LOINC Code</i></small>
                    </label>
                    <input type="text" class="form-control" name="pemeriksaan_code" id="pemeriksaan_code_edit" value="'.$pemeriksaan_code.'" required>
                    <small class="text text-grayish">Kode Pemeriksaan Berdasarkan LOINC</small>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="pemeriksaan_description_edit">
                        <small><i>LOINC Description</i></small>
                    </label>
                    <input type="text" class="form-control" name="pemeriksaan_description" id="pemeriksaan_description_edit" value="'.$pemeriksaan_description.'" required>
                    <small class="text text-grayish">Deskripsi Pemeriksaan Berdasarkan LOINC</small>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="pemeriksaan_sys_edit">
                        <small><i>URL Reference</i></small>
                    </label>
                    <input type="url" class="form-control" name="pemeriksaan_sys" id="pemeriksaan_sys_edit" value="'.$pemeriksaan_sys.'" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="bodysite_code_edit">
                        <small><i>Body Site Code</i></small>
                    </label>
                    <input type="text" class="form-control" name="bodysite_code" id="bodysite_code_edit" value="'.$bodysite_code.'" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="bodysite_description_edit">
                        <small><i>Body Site Description</i></small>
                    </label>
                    <input type="text" class="form-control" name="bodysite_description" id="bodysite_description_edit" value="'.$bodysite_description.'" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="bodysite_sys_edit">
                        <small><i>Body Site Reference</i></small>
                    </label>
                    <input type="url" class="form-control" name="bodysite_sys" id="bodysite_sys_edit" value="'.$bodysite_sys.'" required>
                </div>
            </div>
        ';
    }
?>