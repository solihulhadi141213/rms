<?php
    if(empty($_GET['Page'])){
        include "_Page/Dashboard/Dashboard.php";
    }else{
        $Page=$_GET['Page'];
        //Index Halaman
        $page_arry=[
            "MyProfile"        => "_Page/MyProfile/MyProfile.php",
            "AksesFitur"       => "_Page/AksesFitur/AksesFitur.php",
            "AksesEntitas"     => "_Page/AksesEntitas/AksesEntitas.php",
            "Akses"            => "_Page/Akses/Akses.php",
            "SettingGeneral"   => "_Page/SettingGeneral/SettingGeneral.php",
            "SettingEmail"     => "_Page/SettingEmail/SettingEmail.php",
            "SettingSimrs"     => "_Page/SettingSimrs/SettingSimrs.php",
            "SettingSatuSehat" => "_Page/SettingSatuSehat/SettingSatuSehat.php",
            "SettingPacs"      => "_Page/SettingPacs/SettingPacs.php",
            "SettingOrthanc"   => "_Page/SettingOrthanc/SettingOrthanc.php",
            "ApiKey"           => "_Page/ApiKey/ApiKey.php",
            "KodeKlinis"       => "_Page/KodeKlinis/KodeKlinis.php",
            "KodePemeriksaan"  => "_Page/KodePemeriksaan/KodePemeriksaan.php",
            "Tarif"            => "_Page/Tarif/Tarif.php",
            "Question"         => "_Page/Question/Question.php",
            "TandaTangan"      => "_Page/TandaTangan/TandaTangan.php",
            "Pemeriksaan"      => "_Page/Pemeriksaan/Pemeriksaan.php",
            "Tagihan"          => "_Page/Tagihan/Tagihan.php",
            "Laporan"          => "_Page/Laporan/Laporan.php",
            "DicomRouter"      => "_Page/DicomRouter/DicomRouter.php",
            "Aktivitas"        => "_Page/Aktivitas/Aktivitas.php",
            "Help"             => "_Page/Help/Help.php",
        ];

        //Tangkap 'Page'
        $Page = !empty($_GET['Page']) ? $_GET['Page'] : "";

        //Kondisi Pada masing-masing Page
        if (array_key_exists($Page, $page_arry)) { 
            include $page_arry[$Page]; 
        } else { 
            include "_Page/Error/PageNotFound.php";
        }
    }
?>