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
        $nama_modalitas = [
            'XR' => 'X-Ray',
            'CT' => 'CT-Scan',
            'US' => 'USG',
            'MR' => 'MRI',
            'NM' => 'Nuclear Medicine (Kedokteran Nuklir)',
            'PT' => 'PET Scan',
            'DX' => 'Digital Radiography',
            'CR' => 'Computed Radiography'
        ];

        // Ambil nama modalitas
        $modalitas_nama = $nama_modalitas[$modalitas] ?? '-';

        //Tampilkan Data
        echo '
            <div class="row mb-2">
                <div class="col-4"><small>Nama Pemeriksaan</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$nama_pemeriksaan.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Modalitas</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$modalitas_nama.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small><i>Loinc Code</i></small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$pemeriksaan_code.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small><i>Description Code</i></small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$pemeriksaan_description.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small><i>Loinc Reference</i></small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$pemeriksaan_sys.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small><i>Body Site Code</i></small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$bodysite_code.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small><i>Body Site Description</i></small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$bodysite_description.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small><i>Body Site Reference</i></small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$bodysite_sys.'</small>
                </div>
            </div>
        ';
    }
?>