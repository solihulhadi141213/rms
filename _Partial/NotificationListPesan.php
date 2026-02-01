<?php
    //Karena Ini Di running Dengan JS maka Panggil Ulang Koneksi
    include "../_Config/Connection.php";
    include "../_Config/GlobalFunction.php";
    include "../_Config/Session.php";
    
    $JumlahPermintaanPending = mysqli_num_rows(mysqli_query($Conn, "SELECT id_radiologi FROM radiologi WHERE status_pemeriksaan='Diminta'"));

    //Apabila Tidak ada notifgikasi
    if(empty($JumlahPermintaanPending)){
        echo '<li class="dropdown-header">';
        echo '  Tidak Ada Pemberitahuan Yang Tersedia';
        echo '</li>';
    }else{
        //Apabila Ada
        echo '<li class="dropdown-header">';
        echo '  Ada '.$JumlahPermintaanPending.' Permintaan Menunggu Moderasi';
        echo '</li>';
        echo '
            <li><hr class="dropdown-divider"></li>
            <li class="message-item text-center">
                <a href="index.php?Page=Pemeriksaan" class="text-center">
                    Lihat Halaman Pemeriksaan <i class="bi bi-chevron-right"></i>
                </a>
            </li>
        ';
    }
?>