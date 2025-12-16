<?php
    //Karena Ini Di running Dengan JS maka Panggil Ulang Koneksi
    include "../_Config/Connection.php";
    include "../_Config/GlobalFunction.php";
    include "../_Config/Session.php";
    
    //Menghitung Jumlah Pinjaman Yang Menunggak
    $JumlahNotifikasi = 0;
    
    
    //Apabila Tidak ada notifgikasi
    if(empty($JumlahNotifikasi)){
        echo '<li class="dropdown-header">';
        echo '  Tidak Ada Pemberitahuan';
        echo '</li>';
    }else{
        //Apabila Ada
        echo '<li class="dropdown-header">';
        echo '  Ada '.$JumlahNotifikasi.' Pemberitahuan Sistem';
        echo '</li>';
        if(!empty($student_gender_kosong)){
            echo '<li><hr class="dropdown-divider"></li>';
            echo '<li class="notification-item">';
            echo '  <i class="bi bi-exclamation-circle text-danger"></i>';
            echo '  <div>';
            echo '      <h4><a href="index.php?Page=Siswa">Daftar Siswa</a></h4>';
            echo '      <p>Ada '.$student_gender_kosong.' Data Siswa Yang Belum Memiliki Informasi Gender</p>';
            echo '  </div>';
            echo '</li>';
        }
        if(!empty($student_nis_kosong)){
            echo '<li><hr class="dropdown-divider"></li>';
            echo '<li class="notification-item">';
            echo '  <i class="bi bi-exclamation-circle text-danger"></i>';
            echo '  <div>';
            echo '      <h4><a href="index.php?Page=Siswa">Daftar Siswa</a></h4>';
            echo '      <p>Ada '.$student_nis_kosong.' Data Siswa Yang Belum Memiliki Informasi NIS</p>';
            echo '  </div>';
            echo '</li>';
        }
    }
?>