<?php
    // Koneksi Function, Session dan Setting
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";
    include "../../_Config/SettingGeneral.php";

    // Set Zona Waktu
    date_default_timezone_set("Asia/Jakarta");

    // Response Header
    header('Content-Type: application/json');

    // Validasi Session
    if (empty($SessionIdAccess)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Sesi akses telah berakhir. Silakan login ulang.'
        ]);
        exit;
    }

    // Validasi id_radiologi_file tidak boleh kosong
    if(empty($_POST['id_radiologi_file'])){
        echo json_encode([
            'status'  => 'error',
            'message' => 'ID File Tidak Boleh Kosong'
        ]);
        exit;
    }

    // Buat Variabel Dan Sanitasi
    $id_radiologi_file = validateAndSanitizeInput($_POST['id_radiologi_file']);

    // Buka Informasi File Dari Database
    $Qry = $Conn->prepare("SELECT * FROM radiologi_file WHERE id_radiologi_file = ?");
    $Qry->bind_param("s", $id_radiologi_file);
    if (!$Qry->execute()) {
        $error=$Conn->error;
        echo json_encode([
            'status'  => 'error',
            'message' => 'Terjadi kesalahan pada saat membuka data dari database!<br>Keterangan : '.$error.''
        ]);
        exit;
    }
    $Result = $Qry->get_result();
    $Data = $Result->fetch_assoc();
    $Qry->close();

    // Jika Data Tidak Ditemukan
    if(empty($Data['id_radiologi_file'])){
        echo json_encode([
            'status'  => 'error',
            'message' => 'ID File Tidak Falid Atau Tidak Terdaftar Pada Database'
        ]);
        exit;
    }

    // Buka Data Dari Database dan Buat Variabelnya
    $id_radiologi     = $Data['id_radiologi'];
    $folder_name      = $Data['folder_name'];
    $file_datetime    = $Data['file_datetime'];
    $file_description = $Data['file_description'];
    $file_type        = $Data['file_type'];
    $file_size        = $Data['file_size'];
    $file_name        = $Data['file_name'];

    // Buka Informasi Pasien
    $nama_pasien       = GetDetailData($Conn, 'radiologi', 'id_radiologi', $id_radiologi, 'nama_pasien');
    $nama_pasien_dicom = formatPatientNameDICOM($nama_pasien);
    $id_pasien         = GetDetailData($Conn, 'radiologi', 'id_radiologi', $id_radiologi, 'id_pasien');
    $accession_number  = GetDetailData($Conn, 'radiologi', 'id_radiologi', $id_radiologi, 'accession_number');

    // Menentukan Directory Penyimpanan File ($app_base_url sudah dinyatakan pada SettingGeneral.php)
    $dir_file = '../../_Storage/'.$folder_name.'/'.$file_name.''; 

    //Menentukan Directory Tujuan Dimana DICOM Akan Disimpan
    $dir_tujuan = '../../_DCM/'; 

    // Validasi Tipe File Yang Support
    $allowed_ext = ['jpg','jpeg','png','gif'];
    $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    if(!in_array($ext, $allowed_ext)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Format file tidak didukung untuk konversi DICOM'
        ]);
        exit;
    }

    // Tentukan Path DCMTK (img2dcm.exe)
    $dcmtk_path = 'C:\\dcmtk\\bin\\img2dcm.exe';

    // Menentukan Real Path File Asal dan tujuan
    $source_file = realpath($dir_file);
    if (!$source_file || !file_exists($source_file)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'File sumber tidak ditemukan'
        ]);
        exit;
    }
    if (!is_dir($dir_tujuan)) {
        mkdir($dir_tujuan, 0777, true);
    }
    $dir_tujuan_real = realpath($dir_tujuan);
    if ($dir_tujuan_real === false) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Direktori tujuan DICOM tidak valid'
        ]);
        exit;
    }
    $output_file = $dir_tujuan_real . DIRECTORY_SEPARATOR. pathinfo($file_name, PATHINFO_FILENAME) . '.dcm';

    // Invetarisir Metadata
    $patient_name = $nama_pasien_dicom;
    $patient_id   = $id_pasien;
    $modality     = 'OT';
    $study_desc   = $file_description ?: 'Radiologi Image';
    $study_desc   = preg_replace('/[^A-Z0-9 _\-]/i', '', $study_desc);

    // Menentukan UID
    $study_uid     = generateUID();
    $series_uid    = generateUID();
    $sop_uid       = generateUID();
    $sop_class_uid = '1.2.840.10008.5.1.4.1.1.7';
    $study_date    = date('Ymd');
    $study_time    = date('His');


    //Eksekusi img2dcm
   $cmd = "\"$dcmtk_path\" "
    . "-k \"(0020,000D)=$study_uid\" "
    . "-k \"(0020,000E)=$series_uid\" "
    . "-k \"(0008,0018)=$sop_uid\" "
    . "-k \"PatientName=$patient_name\" "
    . "-k \"PatientID=$patient_id\" "
    . "-k \"AccessionNumber=$accession_number\" "
    . "-k \"Modality=$modality\" "
    . "-k \"StudyDescription=$study_desc\" "
    . "-k \"StudyDate=$study_date\" "
    . "-k \"StudyTime=$study_time\" "
    . "\"$source_file\" \"$output_file\" 2>&1";

    exec($cmd, $output, $return_var);

    // Handle Hasil Eksekusi
    if($return_var !== 0){
        echo json_encode([
            'status' => 'error',
            'message' => 'Konversi DICOM gagal',
            'cmd' => $cmd,
            'return_var' => $return_var,
            'output' => $output
        ]);
        exit;
    }

    // Membuat JSON Metadata
    $dicom_metadata_payload = [
        "study-uid" => $study_uid,
        "series-uid" => $series_uid,
        "sop-uid" => $sop_uid,
        "PatientName" => $patient_name,
        "PatientID" => $patient_id,
        "Modality" => $modality,
        "StudyDescription" => $study_desc
    ];
    $dicom_metadata_json = json_encode($dicom_metadata_payload);

    // Menentukan Basename Dicom
    $file_dcm =basename($output_file);

    // id_imaging_study
    $id_imaging_study = "";
    // Jika Berhasil Simpan Ke Database 'radiologi_dicom_conv' 
    $query = "INSERT INTO radiologi_dicom_conv (
        id_radiologi_file,
        id_radiologi,
        id_imaging_study,
        accession_number,
        filename,
        dicom_metadata
    ) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $Conn->prepare($query);
    $stmt->bind_param(
        "sissss",
        $id_radiologi_file,
        $id_radiologi,
        $id_imaging_study,
        $accession_number,
        $file_dcm,
        $dicom_metadata_json
    );

    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success',
            'message' => 'File berhasil dikonversi ke DICOM'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal menyimpan data: ' . $stmt->error
        ]);
    }
    $stmt->close();
?>