<?php
    //Karena Ini Di running Dengan JS maka Panggil Ulang Koneksi
    include "../_Config/Connection.php";
    include "../_Config/GlobalFunction.php";
    
    //Menghitung Permintaan Pemeriksaan Yang Belum Ditangani
    $JumlahNotifikasi=mysqli_num_rows(mysqli_query($Conn, "SELECT id_radiologi FROM radiologi WHERE status_pemeriksaan='Diminta'"));
    //Apabila ada notifgikasi
    if(!empty($JumlahNotifikasi)){
        echo '<i class="bi bi-chat-left-text text-light"></i>';
        echo '<span class="badge bg-danger badge-number">'.$JumlahNotifikasi.'</span>';
    }else{
        echo '<i class="bi bi-chat-left-text text-light"></i>';
    }
?>