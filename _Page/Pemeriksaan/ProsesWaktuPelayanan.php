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

    // =====================================================================
    // VALIDASI SESSION
    // =====================================================================
    if (empty($SessionIdAccess)) {
        $response['message'] = 'Sesi akses telah berakhir. Silakan login ulang.';
        echo json_encode($response);
        exit;
    }

    // =====================================================================
    // VALIDASI INPUT
    // =====================================================================
    if (empty($_POST['id_radiologi'])) {
        $response['message'] = 'ID Pemeriksaan Tidak Boleh Kosong';
        echo json_encode($response);
        exit;
    }

    if (empty($_POST['parameter_waktu'])) {
        $response['message'] = 'Informasi Parameter Waktu Tidak Boleh Kosong';
        echo json_encode($response);
        exit;
    }

    if (empty($_POST['tanggal_pelayanan'])) {
        $response['message'] = 'Informasi Tanggal Tidak Boleh Kosong';
        echo json_encode($response);
        exit;
    }

    if (empty($_POST['jam_pelayanan'])) {
        $response['message'] = 'Informasi Jam Layanan Tidak Boleh Kosong';
        echo json_encode($response);
        exit;
    }

    $id_radiologi      = validateAndSanitizeInput($_POST['id_radiologi']);
    $parameter_waktu   = validateAndSanitizeInput($_POST['parameter_waktu']);
    $tanggal_pelayanan = validateAndSanitizeInput($_POST['tanggal_pelayanan']);
    $jam_pelayanan     = validateAndSanitizeInput($_POST['jam_pelayanan']);
    $tanggal_pelayanan = "$tanggal_pelayanan $jam_pelayanan";

    // Buka Data Informasi Radiologi
    $Qry = $Conn->prepare("SELECT * FROM radiologi WHERE id_radiologi = ?");
    $Qry->bind_param("i", $id_radiologi);
    if (!$Qry->execute()) {
        $error=$Conn->error;
        $response['message'] = 'Terjadi kesalahan pada saat membuka data dari database!<br>Keterangan : '.$error.'';
        echo json_encode($response);
        exit;
    }
    $Result = $Qry->get_result();
    $Data = $Result->fetch_assoc();
    $Qry->close();

    //Buat Variabel
    $datetime_diminta    = $Data['datetime_diminta'];
    $datetime_dikerjakan = $Data['datetime_dikerjakan'];
    $datetime_hasil      = $Data['datetime_hasil'];
    $datetime_selesai    = $Data['datetime_selesai'];

    // Validasi Persyaratan Berdasarkan Data Lain
    if($parameter_waktu=="datetime_diminta"){
        if(!empty($datetime_dikerjakan)){
            if($tanggal_pelayanan>$datetime_dikerjakan){
                $response['message'] = 'Waktu permintaan tidak boleh mendahului pengerjaan';
                echo json_encode($response);
                exit;
            }
        }
       
    }
    if($parameter_waktu=="datetime_dikerjakan"){
        if($tanggal_pelayanan<$datetime_diminta){
            $response['message'] = 'Waktu pengerjaan tidak boleh lebih kecil dari permintaan';
            echo json_encode($response);
            exit;
        }
        if(!empty($datetime_hasil)){
            if($tanggal_pelayanan>$datetime_hasil){
                $response['message'] = 'Waktu pengerjaan tidak boleh melebihi pengisian hasil';
                echo json_encode($response);
                exit;
            }
        }
    }
    if($parameter_waktu=="datetime_hasil"){
        if($tanggal_pelayanan<$datetime_dikerjakan){
            $response['message'] = 'Waktu pengisian hasil tidak boleh lebih kecil dari permintaan diterima';
            echo json_encode($response);
            exit;
        }
        if(!empty($datetime_selesai)){
            if($tanggal_pelayanan>$datetime_selesai){
                $response['message'] = 'Waktu pengisian hasil tidak boleh melebihi waktu selesai';
                echo json_encode($response);
                exit;
            }
        }
    }

    if($parameter_waktu=="datetime_selesai"){
        if($tanggal_pelayanan<$datetime_hasil){
            $response['message'] = 'Waktu penyerahan hasil tidak boleh lebih kecil dari pembuatan hasil';
            echo json_encode($response);
            exit;
        }
    }

    $update_waktu = mysqli_query($Conn,"UPDATE radiologi SET $parameter_waktu='$tanggal_pelayanan' WHERE id_radiologi='$id_radiologi'") or die(mysqli_error($Conn)); 
    if($update_waktu){
        echo json_encode(['status' => 'success','message' => 'Update Waktu Pelayanan Berhasil!']);
        exit;
    }else{
        echo json_encode(['status' => 'error','message' => 'Update Waktu Pelayanan gagal!']);
        exit;
    }
?>