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

    // =======================
    // VALIDASI SESSION
    // =======================
    if (empty($SessionIdAccess)) {
        $response['message'] = 'Sesi akses telah berakhir. Silakan login ulang.';
        echo json_encode($response);
        exit;
    }

    if (empty($_POST['id_radiologi'])) {
        $response['message'] = 'ID Radiologi tidak boleh kosong.';
        echo json_encode($response);
        exit;
    }

    // Membuat variabel dari data yang dikirim
    $id_radiologi  = validateAndSanitizeInput($_POST['id_radiologi'] ?? '');
    $isi_expertise = $_POST['isi_expertise'] ?? '';

    // Nilai title sebagai nama kolom pada database
    $title         = $_POST['title'] ?? '';

    // ==============================================
    // CEK DATA SUDAH ADA ATAU BELUM
    // ==============================================
    $QryCheck = $Conn->prepare("SELECT id_radiologi_local_exp, $title FROM radiologi_local_exp WHERE id_radiologi = ?");
    if (!$QryCheck) {
        $response['message'] = $Conn->error;
        echo json_encode($response);
        exit;
    }

    $QryCheck->bind_param("i", $id_radiologi);
    $QryCheck->execute();
    $Result = $QryCheck->get_result();
    $Data   = $Result->fetch_assoc();
    $QryCheck->close();

    if(empty($Data['id_radiologi_local_exp'])){
        
        // Jika Belum Ada Maka Insert
        $query = "INSERT INTO radiologi_local_exp (id_radiologi, $title) VALUES (?, ?)";
        $stmt = $Conn->prepare($query);
        // Bind parameters
        $stmt->bind_param(
            "is",
            $id_radiologi,
            $isi_expertise
        );
        
        if($stmt->execute()){

            // Jika Berhasil Cek Status Radiologi
            $status_pemeriksaan = GetDetailData($Conn, 'radiologi', 'id_radiologi', $id_radiologi, 'status_pemeriksaan');

            // Apabila Statusnya 'Dikerjakan' maka lakukan UPDATE
            if($status_pemeriksaan=="Dikerjakan"){
                
                // Update Status Pemeriksaan
                $datetime_hasil     = date('Y-m-d H:i:s');
                $status_pemeriksaan = "Hasil";
                $update_radiologi   = $Conn->prepare("UPDATE radiologi SET datetime_hasil = ?, status_pemeriksaan = ? WHERE id_radiologi = ?");
                $update_radiologi->bind_param("ssi", $datetime_hasil, $status_pemeriksaan, $id_radiologi);
                $update_radiologi_executed = $update_radiologi->execute();
                if (!$update_radiologi_executed) {
                    // Jika Terjadi kesalahan pada saat Update
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Database update failed: '.$Conn->error.''
                    ]);
                }

                $update_radiologi->close();

                // Jika Tidak Maka Tampilkan Notif Berhasil
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Expertise berhasil ditambahkan dan status permintaan berhasil diperbaharui!'
                ]);

            }else{
                // Jika Tidak Maka Tampilkan Notif Berhasil
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Expertise berhasil ditambahkan!'
                ]);
            }
        } else {

            // Jika Gagal Insert
            echo json_encode([
                'status' => 'error',
                'message' => 'Gagal menyimpan data: ' . $stmt->error
            ]);
        }
        $stmt->close();
    }else{

        // Jika Sudah Ada Maka Update
        $id_radiologi_local_exp = $Data['id_radiologi_local_exp'];

        // Proses Update
        $update = $Conn->prepare("UPDATE radiologi_local_exp SET $title = ? WHERE id_radiologi_local_exp = ?");
        $update->bind_param("si", $isi_expertise, $id_radiologi_local_exp);
        $update_executed = $update->execute();

        if (!$update_executed) {
            // Jika Terjadi kesalahan pada saat Update
            echo json_encode([
                'status' => 'error',
                'message' => 'Database update failed: '.$Conn->error.''
            ]);
        }
        $update->close();

        // Jika Berhasil Cek Status Radiologi
        $status_pemeriksaan = GetDetailData($Conn, 'radiologi', 'id_radiologi', $id_radiologi, 'status_pemeriksaan');

        // Apabila Statusnya 'Dikerjakan' maka lakukan UPDATE
        if($status_pemeriksaan=="Dikerjakan"){
            
            // Update Status Pemeriksaan
            $datetime_hasil     = date('Y-m-d H:i:s');
            $status_pemeriksaan = "Hasil";
            $update_radiologi   = $Conn->prepare("UPDATE radiologi SET datetime_hasil = ?, status_pemeriksaan = ? WHERE id_radiologi = ?");
            $update_radiologi->bind_param("ssi", $datetime_hasil, $status_pemeriksaan, $id_radiologi);
            $update_radiologi_executed = $update_radiologi->execute();
            if (!$update_radiologi_executed) {
                // Jika Terjadi kesalahan pada saat Update
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Database update failed: '.$Conn->error.''
                ]);
            }
            $update->close();

            // Jika Tidak Maka Tampilkan Notif Berhasil
            echo json_encode([
                'status' => 'success',
                'message' => 'Expertise berhasil ditambahkan dan status permintaan berhasil diperbaharui!'
            ]);

        }else{
            // Jika Tidak Maka Tampilkan Notif Berhasil
            echo json_encode([
                'status' => 'success',
                'message' => 'Expertise berhasil ditambahkan!'
            ]);
        }
    }

?>