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
        $error = $Conn->error;
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
            'message' => 'ID File Tidak Valid Atau Tidak Terdaftar Pada Database'
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

    // Menentukan Directory Penyimpanan File
    $dir_file = '../../_Storage/'.$folder_name.'/'.$file_name; 

    // Menentukan Directory Tujuan Dimana DICOM Akan Disimpan
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
    
    // Verifikasi DCMTK tersedia
    if (!file_exists($dcmtk_path)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'DCMTK tidak ditemukan di ' . $dcmtk_path
        ]);
        exit;
    }

    // Menentukan Real Path File Asal dan tujuan
    $source_file = realpath($dir_file);
    if (!$source_file || !file_exists($source_file)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'File sumber tidak ditemukan'
        ]);
        exit;
    }
    
    // Buat direktori tujuan jika belum ada
    if (!is_dir($dir_tujuan)) {
        if (!mkdir($dir_tujuan, 0777, true)) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Gagal membuat direktori tujuan'
            ]);
            exit;
        }
    }
    
    $dir_tujuan_real = realpath($dir_tujuan);
    if ($dir_tujuan_real === false) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Direktori tujuan DICOM tidak valid'
        ]);
        exit;
    }
    
    // Buat nama file DICOM unik
    $base_filename = pathinfo($file_name, PATHINFO_FILENAME);
    $output_file = $dir_tujuan_real . DIRECTORY_SEPARATOR . $base_filename . '_' . date('YmdHis') . '.dcm';

    // Ambil informasi gambar untuk metadata DICOM
    $image_info = @getimagesize($source_file);
    if (!$image_info) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Gagal membaca informasi gambar'
        ]);
        exit;
    }
    
    $width = $image_info[0];
    $height = $image_info[1];
    $image_type = $image_info[2];
    $channels = isset($image_info['channels']) ? $image_info['channels'] : 3;
    
    // Deteksi tipe gambar dan tentukan Photometric Interpretation
    $photometric_interpretation = 'RGB';
    $samples_per_pixel = 3;
    
    if ($ext == 'png') {
        // Coba deteksi apakah PNG grayscale
        $img = @imagecreatefrompng($source_file);
        if ($img) {
            // Cek 10 pixel random untuk menentukan apakah grayscale
            $is_gray = true;
            $checks = min(10, $width * $height);
            for ($i = 0; $i < $checks; $i++) {
                $x = rand(0, $width - 1);
                $y = rand(0, $height - 1);
                $rgb = imagecolorat($img, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
                if (abs($r - $g) > 2 || abs($g - $b) > 2) {
                    $is_gray = false;
                    break;
                }
            }
            imagedestroy($img);
            
            if ($is_gray) {
                $photometric_interpretation = 'MONOCHROME2';
                $samples_per_pixel = 1;
            }
        }
    } elseif (in_array($ext, ['jpg', 'jpeg'])) {
        // Untuk JPG, biasanya RGB kecuali kita tahu pasti grayscale
        // Kita bisa cek dengan exif jika ada
        if (function_exists('exif_read_data')) {
            $exif = @exif_read_data($source_file);
            if ($exif && isset($exif['ColorSpace'])) {
                if ($exif['ColorSpace'] == 1) { // 1 = sRGB, 65535 = Uncalibrated
                    $photometric_interpretation = 'RGB';
                    $samples_per_pixel = 3;
                }
            }
        }
    }

    // ==================== GENERATE METADATA DICOM YANG LENGKAP ====================
    
    // UID Generation
    $study_uid     = generateUID();
    $series_uid    = generateUID();
    $sop_uid       = generateUID();
    $sop_class_uid = '1.2.840.10008.5.1.4.1.1.7'; // Secondary Capture Image Storage
    
    // Timestamps
    $study_date    = date('Ymd');
    $study_time    = date('His');
    $series_date   = date('Ymd');
    $series_time   = date('His');
    $content_date  = date('Ymd');
    $content_time  = date('His');
    
    // Institution information
    $institution_name = 'RADIOLOGY_DEPARTMENT';
    
    // Study and Series information
    $study_id = 'STUDY_' . date('YmdHis');
    $series_number = '1';
    $instance_number = '1';
    
    // Pixel information
    $bits_allocated = 8;
    $bits_stored = 8;
    $high_bit = 7;
    $pixel_representation = 0; // 0 = unsigned
    $planar_configuration = 0; // 0 = pixel interleaved (RGBRGB...)
    $pixel_spacing = '1\\1'; // Default 1mm x 1mm
    
    // Window settings
    $window_center = 127;
    $window_width = 255;
    
    // Manufacturer information
    $manufacturer = 'DCMTK';
    $manufacturer_model_name = 'img2dcm';
    $software_versions = '3.6.7';
    
    // Patient information
    $patient_name = $nama_pasien_dicom;
    $patient_id = $id_pasien;
    $modality = 'OT'; // Other
    $study_description = $file_description ?: 'Radiologi Image';
    $study_description = preg_replace('/[^A-Z0-9 _\-]/i', ' ', $study_description);
    
    // ==================== OPTIMALISASI COMMAND img2dcm ====================
    
    // Gunakan metode 2-step untuk hasil yang lebih baik
    // Step 1: Buat DICOM dasar
    $temp_file = $dir_tujuan_real . DIRECTORY_SEPARATOR . 'temp_' . uniqid() . '.dcm';
    
    // Command sederhana untuk membuat DICOM dasar
    $cmd1 = "\"$dcmtk_path\" " .
           "--study-from \"$source_file\" " .
           "--series-from \"$source_file\" " .
           "--key \"PatientName=$patient_name\" " .
           "--key \"PatientID=$patient_id\" " .
           "--key \"StudyInstanceUID=$study_uid\" " .
           "--key \"SeriesInstanceUID=$series_uid\" " .
           "--key \"SOPInstanceUID=$sop_uid\" " .
           "--key \"AccessionNumber=$accession_number\" " .
           "\"$source_file\" \"$temp_file\" 2>&1";
    
    exec($cmd1, $output1, $return_var1);
    
    if ($return_var1 === 0 && file_exists($temp_file)) {
        // Step 2: Gunakan dcmodify untuk menambahkan metadata lengkap
        $dcmodify_path = 'C:\\dcmtk\\bin\\dcmodify.exe';
        
        if (file_exists($dcmodify_path)) {
            // Bangun command dcmodify dengan metadata lengkap
            $modify_cmd = "\"$dcmodify_path\" --no-backup ";
            
            // Tambahkan semua metadata penting
            $modify_cmd .= "--insert \"(0008,0016)=$sop_class_uid\" ";
            $modify_cmd .= "--insert \"(0008,0060)=$modality\" ";
            $modify_cmd .= "--insert \"(0008,1030)=" . addslashes($study_description) . "\" ";
            $modify_cmd .= "--insert \"(0008,0020)=$study_date\" ";
            $modify_cmd .= "--insert \"(0008,0030)=$study_time\" ";
            $modify_cmd .= "--insert \"(0008,0021)=$series_date\" ";
            $modify_cmd .= "--insert \"(0008,0031)=$series_time\" ";
            $modify_cmd .= "--insert \"(0008,0023)=$content_date\" ";
            $modify_cmd .= "--insert \"(0008,0033)=$content_time\" ";
            $modify_cmd .= "--insert \"(0020,0010)=$study_id\" ";
            $modify_cmd .= "--insert \"(0020,0011)=$series_number\" ";
            $modify_cmd .= "--insert \"(0020,0013)=$instance_number\" ";
            
            // Pixel data
            $modify_cmd .= "--insert \"(0028,0010)=$height\" "; // Rows
            $modify_cmd .= "--insert \"(0028,0011)=$width\" ";  // Columns
            $modify_cmd .= "--insert \"(0028,0100)=$bits_allocated\" "; // Bits Allocated
            $modify_cmd .= "--insert \"(0028,0101)=$bits_stored\" ";    // Bits Stored
            $modify_cmd .= "--insert \"(0028,0102)=$high_bit\" ";       // High Bit
            $modify_cmd .= "--insert \"(0028,0103)=$pixel_representation\" "; // Pixel Representation
            $modify_cmd .= "--insert \"(0028,0002)=$samples_per_pixel\" ";    // Samples Per Pixel
            $modify_cmd .= "--insert \"(0028,0004)=$photometric_interpretation\" "; // Photometric Interpretation
            $modify_cmd .= "--insert \"(0028,0006)=$planar_configuration\" "; // Planar Configuration
            
            // Window settings
            $modify_cmd .= "--insert \"(0028,1050)=$window_center\" "; // Window Center
            $modify_cmd .= "--insert \"(0028,1051)=$window_width\" ";  // Window Width
            
            // Pixel Spacing
            $modify_cmd .= "--insert \"(0028,0030)=$pixel_spacing\" ";
            
            // Manufacturer info
            $modify_cmd .= "--insert \"(0008,0070)=$manufacturer\" ";
            $modify_cmd .= "--insert \"(0008,1090)=$manufacturer_model_name\" ";
            $modify_cmd .= "--insert \"(0018,1020)=$software_versions\" ";
            
            // Institution info
            $modify_cmd .= "--insert \"(0008,0080)=$institution_name\" ";
            
            // Add specific tags for Secondary Capture
            $modify_cmd .= "--insert \"(0008,0008)=ORIGINAL\\PRIMARY\" "; // Image Type
            $modify_cmd .= "--insert \"(0018,0015)=ABDOMEN\" "; // Body Part Examined (default)
            
            // Set Transfer Syntax to Explicit VR Little Endian (required by many DICOM viewers)
            $modify_cmd .= "--insert \"(0002,0010)=1.2.840.10008.1.2.1\" ";
            
            $modify_cmd .= "\"$temp_file\" 2>&1";
            
            exec($modify_cmd, $modify_output, $modify_return);
            
            if ($modify_return === 0) {
                // File sukses dimodifikasi, pindahkan ke output file
                rename($temp_file, $output_file);
            } else {
                // dcmodify gagal, coba gunakan file temp asli
                copy($temp_file, $output_file);
                unlink($temp_file);
            }
        } else {
            // dcmodify tidak tersedia, gunakan file temp
            rename($temp_file, $output_file);
        }
    } else {
        // Metode 1 gagal, coba metode langsung
        $cmd = "\"$dcmtk_path\" " .
               "-k \"(0020,000D)=$study_uid\" " .
               "-k \"(0020,000E)=$series_uid\" " .
               "-k \"(0008,0018)=$sop_uid\" " .
               "-k \"(0008,0016)=$sop_class_uid\" " .
               "-k \"PatientName=$patient_name\" " .
               "-k \"PatientID=$patient_id\" " .
               "-k \"AccessionNumber=$accession_number\" " .
               "-k \"Modality=$modality\" " .
               "-k \"StudyDescription=$study_description\" " .
               "-k \"StudyDate=$study_date\" " .
               "-k \"StudyTime=$study_time\" " .
               "-k \"SeriesDate=$series_date\" " .
               "-k \"SeriesTime=$series_time\" " .
               "-k \"StudyID=$study_id\" " .
               "-k \"SeriesNumber=$series_number\" " .
               "-k \"InstanceNumber=$instance_number\" " .
               "-k \"Rows=$height\" " .
               "-k \"Columns=$width\" " .
               "-k \"BitsAllocated=$bits_allocated\" " .
               "-k \"BitsStored=$bits_stored\" " .
               "-k \"HighBit=$high_bit\" " .
               "-k \"PixelRepresentation=$pixel_representation\" " .
               "-k \"SamplesPerPixel=$samples_per_pixel\" " .
               "-k \"PhotometricInterpretation=$photometric_interpretation\" " .
               "-k \"WindowCenter=$window_center\" " .
               "-k \"WindowWidth=$window_width\" " .
               "-k \"Manufacturer=$manufacturer\" " .
               "\"$source_file\" \"$output_file\" 2>&1";
        
        exec($cmd, $output, $return_var);
        
        if ($return_var !== 0 || !file_exists($output_file)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Konversi DICOM gagal',
                'cmd' => $cmd,
                'return_var' => $return_var,
                'output' => $output
            ]);
            exit;
        }
    }

    // ==================== VALIDASI FILE DICOM ====================
    
    // Gunakan dcmdump untuk memverifikasi file DICOM
    $dcmtk_info_path = 'C:\\dcmtk\\bin\\dcmdump.exe';
    $is_valid = false;
    $dicom_info = [];
    
    if (file_exists($dcmtk_info_path) && file_exists($output_file)) {
        $verify_cmd = "\"$dcmtk_info_path\" \"$output_file\" 2>&1";
        exec($verify_cmd, $verify_output, $verify_return);
        
        if ($verify_return === 0) {
            $is_valid = true;
            
            // Ekstrak beberapa informasi penting dari output
            foreach ($verify_output as $line) {
                if (strpos($line, '(0010,0010)') !== false) {
                    $dicom_info['PatientName'] = trim(substr($line, strpos($line, '[') + 1, -1));
                } elseif (strpos($line, '(0008,0060)') !== false) {
                    $dicom_info['Modality'] = trim(substr($line, strpos($line, '[') + 1, -1));
                } elseif (strpos($line, '(0028,0010)') !== false) {
                    $dicom_info['Rows'] = trim(substr($line, strpos($line, '[') + 1, -1));
                } elseif (strpos($line, '(0028,0011)') !== false) {
                    $dicom_info['Columns'] = trim(substr($line, strpos($line, '[') + 1, -1));
                } elseif (strpos($line, '(0002,0010)') !== false) {
                    $dicom_info['TransferSyntax'] = trim(substr($line, strpos($line, '[') + 1, -1));
                }
            }
        }
    } else {
        // Validasi sederhana: cek apakah file ada dan ukurannya > 0
        if (file_exists($output_file) && filesize($output_file) > 100) {
            $is_valid = true;
        }
    }
    
    if (!$is_valid) {
        if (file_exists($output_file)) {
            unlink($output_file);
        }
        
        echo json_encode([
            'status' => 'error',
            'message' => 'File DICOM tidak valid atau rusak'
        ]);
        exit;
    }

    // ==================== BUAT METADATA LENGKAP UNTUK DATABASE ====================
    
    $dicom_metadata_payload = [
        "StudyInstanceUID"          => $study_uid,
        "SeriesInstanceUID"         => $series_uid,
        "SOPInstanceUID"            => $sop_uid,
        "SOPClassUID"               => $sop_class_uid,
        "PatientName"               => $patient_name,
        "PatientID"                 => $patient_id,
        "AccessionNumber"           => $accession_number,
        "Modality"                  => $modality,
        "StudyDescription"          => $study_description,
        "StudyDate"                 => $study_date,
        "StudyTime"                 => $study_time,
        "SeriesDate"                => $series_date,
        "SeriesTime"                => $series_time,
        "Rows"                      => $height,
        "Columns"                   => $width,
        "BitsAllocated"             => $bits_allocated,
        "BitsStored"                => $bits_stored,
        "HighBit"                   => $high_bit,
        "PixelRepresentation"       => $pixel_representation,
        "SamplesPerPixel"           => $samples_per_pixel,
        "PhotometricInterpretation" => $photometric_interpretation,
        "WindowCenter"              => $window_center,
        "WindowWidth"               => $window_width,
        "Manufacturer"              => $manufacturer,
        "ManufacturerModelName"     => $manufacturer_model_name,
        "SoftwareVersions"          => $software_versions,
        "InstitutionName"           => $institution_name,
        "ImageType"                 => "ORIGINAL\\PRIMARY",
        "ConversionSource"          => $file_name,
        "ConversionDate"            => date('Y-m-d H:i:s'),
        "FileSize"                  => filesize($output_file),
        "ValidationStatus"          => "VALID"
    ];
    
   // Encode JSON
    $dicom_metadata_json = json_encode($dicom_metadata_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    // Nama file DICOM
    $file_dcm = basename($output_file);

    // id_imaging_study
    $id_imaging_study = "";

    // Simpan ke database 'radiologi_dicom_conv'
    $query = "INSERT INTO radiologi_dicom_conv (
        id_radiologi_file,
        id_radiologi,
        id_imaging_study,
        accession_number,
        filename,
        dicom_metadata
    ) VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $Conn->prepare($query);
    if (!$stmt) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal menyiapkan query: ' . $Conn->error
        ]);
        exit;
    }
    
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
        $id_radiologi_dicom_conv = $stmt->insert_id;
        
        echo json_encode([
            'status' => 'success',
            'message' => 'File berhasil dikonversi ke DICOM',
            'file_dcm' => $file_dcm,
            'id_radiologi_dicom_conv' => $id_radiologi_dicom_conv,
            'view_url' => '_Page/Radiologi/DICOMViewer.php?id=' . $id_radiologi_dicom_conv,
            'validation' => $is_valid ? 'VALID' : 'INVALID',
            'file_size' => filesize($output_file) . ' bytes',
            'metadata_summary' => [
                'PatientName' => $patient_name,
                'Modality' => $modality,
                'ImageSize' => "{$width}x{$height}",
                'Photometric' => $photometric_interpretation
            ]
        ]);
    } else {
        // Hapus file DICOM jika gagal menyimpan ke database
        if (file_exists($output_file)) {
            unlink($output_file);
        }
        
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal menyimpan data ke database: ' . $stmt->error
        ]);
    }
    $stmt->close();
?>