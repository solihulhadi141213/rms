<?php 
    $date_version=date('YmdHis');
    if(empty($_GET['Page'])){
        //Dafault Javascript Diarahkan Ke Dashboard
        echo '<script type="text/javascript" src="_Page/Dashboard/Dashboard.js?V='.$date_version.'"></script>';
    }else{
        $Page=$_GET['Page'];
        // Routing Javascript Berdasarkan Halaman
        $scripts = [
            "MyProfile"        => "_Page/MyProfile/MyProfile.js",
            "AksesFitur"       => "_Page/AksesFitur/AksesFitur.js",
            "AksesEntitas"     => "_Page/AksesEntitas/AksesEntitas.js",
            "Akses"            => "_Page/Akses/Akses.js",
            "SettingGeneral"   => "_Page/SettingGeneral/SettingGeneral.js",
            "SettingEmail"     => "_Page/SettingEmail/SettingEmail.js",
            "SettingSimrs"     => "_Page/SettingSimrs/SettingSimrs.js",
            "SettingSatuSehat" => "_Page/SettingSatuSehat/SettingSatuSehat.js",
            "SettingPacs"      => "_Page/SettingPacs/SettingPacs.js",
            "ApiKey"           => "_Page/ApiKey/ApiKey.js",
            "KodeKlinis"       => "_Page/KodeKlinis/KodeKlinis.js",
            "KodePemeriksaan"  => "_Page/KodePemeriksaan/KodePemeriksaan.js",
            "Pemeriksaan"      => "_Page/Pemeriksaan/Pemeriksaan.js",
            "Aktivitas"        => "_Page/Aktivitas/Aktivitas.js",
            "Help"             => "_Page/Help/Help.js"
        ];

        // Cek apakah halaman ada dalam daftar dan sertakan file JS yang sesuai
        if (!empty($_GET['Page']) && isset($scripts[$_GET['Page']])) {
            echo '<script type="text/javascript" src="' . $scripts[$_GET['Page']] . '?V='.$date_version.'"></script>';
        }
    }
    echo '<script type="text/javascript" src="_Partial/Universal.js?V='.$date_version.'"></script>';
?>