<?php
    //Zona Waktu
    date_default_timezone_set('Asia/Jakarta');

    //Koneksi
    include "../../_Config/Connection.php";
    include "../../_Config/SettingGeneral.php";
    include "../../_Config/GlobalFunction.php";

    // Validasi Jenis data
    if(empty($_GET['data'])){
        echo "Kategori Data Tidak Boleh Kosong!";
    }

    // Buat variabel
    $kategori_data = $_GET['data'];

    $data_arry=[
        "Nota"   => "ExportNota.php",
        "Report" => "ExportReport.php",
        "Expertise" => "Expertise.php"
    ];

    if (array_key_exists($kategori_data, $data_arry)) { 
        include $data_arry[$kategori_data]; 
    } else { 
        include "_Page/Error/PageNotFound.php";
    }

?>