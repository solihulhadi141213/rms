<?php
    // koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    // Zona Waktu
    date_default_timezone_set("Asia/Jakarta");

    // Response default
    $response = [
        'status'  => 'error',
        'message' => 'Terjadi kesalahan sistem'
    ];

    // VALIDASI SESSION
    if (empty($SessionIdAccess)) {
        $response['message'] = 'Sesi akses telah berakhir. Silakan login ulang.';
        echo json_encode($response);
        exit;
    }

    // Validasi Data Wajib
    if(empty($_POST['id_radiologi_expertise'])){
        $response['message'] = 'ID Expertise Radiologi Tidak Boleh Kosong!.';
        echo json_encode($response);
        exit;
    }
    if(empty($_POST['modality'])){
        $response['message'] = 'Modality Radiologi Tidak Boleh Kosong!.';
        echo json_encode($response);
        exit;
    }
    $id_radiologi_expertise = validateAndSanitizeInput($_POST['id_radiologi_expertise'] ?? '');
    $modality               = validateAndSanitizeInput($_POST['modality'] ?? '');

    if($modality!=="US"){

        // Update Expertise NON USG
        $doctor_name                       = validateAndSanitizeInput($_POST['doctor_name'] ?? '');
        $description                       = validateAndSanitizeInput($_POST['description'] ?? '');
        $finding                           = validateAndSanitizeInput($_POST['finding'] ?? '');
        $impression                        = validateAndSanitizeInput($_POST['impression'] ?? '');
        $recommendation                    = validateAndSanitizeInput($_POST['recommendation'] ?? '');
        $cardiac_silhouette                = validateAndSanitizeInput($_POST['cardiac_silhouette'] ?? '');
        $aorta                             = validateAndSanitizeInput($_POST['aorta'] ?? '');
        $mediastinum                       = validateAndSanitizeInput($_POST['mediastinum'] ?? '');
        $lungs                             = validateAndSanitizeInput($_POST['lungs'] ?? '');
        $trachea                           = validateAndSanitizeInput($_POST['trachea'] ?? '');
        $diaphragm_and_costophrenic_angles = validateAndSanitizeInput($_POST['diaphragm_and_costophrenic_angles'] ?? '');
        $visualized_structures             = validateAndSanitizeInput($_POST['visualized_structures'] ?? '');

        // Update Data
        $stmt = $Conn->prepare("UPDATE radiologi_expertise SET
            doctor_name = ?,
            description  = ?,
            finding = ?,
            impression = ?,
            recommendation  = ?,
            cardiac_silhouette = ?,
            aorta = ?,
            mediastinum = ?,
            lungs = ?,
            trachea = ?,
            diaphragm_and_costophrenic_angles = ?,
            visualized_structures = ?
            WHERE id_radiologi_expertise = ?
        ");

        $stmt->bind_param(
            "sssssssssssss",
            $doctor_name,
            $description,
            $finding,
            $impression,
            $recommendation,
            $cardiac_silhouette,
            $aorta,
            $mediastinum,
            $lungs,
            $trachea,
            $diaphragm_and_costophrenic_angles,
            $visualized_structures,
            $id_radiologi_expertise
        );

        if (!$stmt) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Gagal menyiapkan query database'
            ]);
            exit;
        }

        if ($stmt->execute()) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Expertise Berhasil Diperbaharui'
            ]);
            $stmt->close();
            exit;
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Terjadi Kesalahan Pada Saat Update Expertise'
            ]);
            $stmt->close();
            exit;
        }
    }else{
        // Tangkap Variabel
        $doctor_name               = validateAndSanitizeInput($_POST['doctor_name'] ?? '');
        $description               = validateAndSanitizeInput($_POST['description'] ?? '');
        $finding                   = validateAndSanitizeInput($_POST['finding'] ?? '');
        $recommendation            = validateAndSanitizeInput($_POST['recommendation'] ?? '');
        $gestational_sac_size      = validateAndSanitizeInput($_POST['gestational_sac_size'] ?? '');
        $crown_rump_length         = validateAndSanitizeInput($_POST['crown_rump_length'] ?? '');
        $fetal_heart_rate          = validateAndSanitizeInput($_POST['fetal_heart_rate'] ?? '');
        $biparietal_diameter       = validateAndSanitizeInput($_POST['biparietal_diameter'] ?? '');
        $head_circumference        = validateAndSanitizeInput($_POST['head_circumference'] ?? '');
        $abdominal_circumference   = validateAndSanitizeInput($_POST['abdominal_circumference'] ?? '');
        $femur_length              = validateAndSanitizeInput($_POST['femur_length'] ?? '');
        $single_deepest_pocket     = validateAndSanitizeInput($_POST['single_deepest_pocket'] ?? '');
        $estimated_fetal_weight    = validateAndSanitizeInput($_POST['estimated_fetal_weight'] ?? '');
        $fetal_position            = validateAndSanitizeInput($_POST['fetal_position'] ?? '');
        $estimated_gestational_age = validateAndSanitizeInput($_POST['estimated_gestational_age'] ?? '');
        $estimated_date_birth      = validateAndSanitizeInput($_POST['estimated_date_birth'] ?? '');
        $fetal_presentation        = validateAndSanitizeInput($_POST['fetal_presentation'] ?? '');

        // Update Data
        $stmt = $Conn->prepare("UPDATE radiologi_expertise_usg SET
            doctor_name = ?,
            description  = ?,
            finding = ?,
            recommendation  = ?,
            gestational_sac_size = ?,
            crown_rump_length = ?,
            fetal_heart_rate = ?,
            biparietal_diameter = ?,
            head_circumference = ?,
            abdominal_circumference = ?,
            femur_length = ?,
            single_deepest_pocket = ?,
            estimated_fetal_weight = ?,
            fetal_position = ?,
            estimated_gestational_age = ?,
            estimated_date_birth = ?,
            fetal_presentation = ?
            WHERE id_radiologi_expertise_usg = ?
        ");

        $stmt->bind_param(
            "sssssssssssssssssi",
            $doctor_name,
            $description,
            $finding,
            $recommendation,
            $gestational_sac_size,
            $crown_rump_length,
            $fetal_heart_rate,
            $biparietal_diameter,
            $head_circumference,
            $abdominal_circumference,
            $femur_length,
            $single_deepest_pocket,
            $estimated_fetal_weight,
            $fetal_position,
            $estimated_gestational_age,
            $estimated_date_birth,
            $fetal_presentation,
            $id_radiologi_expertise
        );

        if (!$stmt) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Gagal menyiapkan query database'
            ]);
            exit;
        }

        if ($stmt->execute()) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Expertise Berhasil Diperbaharui'
            ]);
            $stmt->close();
            exit;
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Terjadi Kesalahan Pada Saat Update Expertise'
            ]);
            $stmt->close();
            exit;
        }
    }
?>