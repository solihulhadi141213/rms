<?php
    include "_Page/Logout/ModalLogout.php";
    if(!empty($_GET['Page'])){
        $Page=$_GET['Page'];
        
        // Daftar halaman dan modal yang terkait
        $modals = [
            "MyProfile"        => "_Page/MyProfile/ModalMyProfile.php",
            "AksesFitur"       => "_Page/AksesFitur/ModalAksesFitur.php",
            "AksesEntitas"     => "_Page/AksesEntitas/ModalAksesEntitas.php",
            "Akses"            => "_Page/Akses/ModalAkses.php",
            "SettingEmail"     => "_Page/SettingEmail/ModalSettingEmail.php",
            "SettingSimrs"     => "_Page/SettingSimrs/ModalSettingSimrs.php",
            "SettingSatuSehat" => "_Page/SettingSatuSehat/ModalSettingSatuSehat.php",
            "Aktivitas"        => "_Page/Aktivitas/ModalAktivitas.php",
            "Help"             => "_Page/Help/ModalHelp.php"
        ];

        // Cek apakah halaman memiliki modal terkait dan sertakan file modalnya
        if (!empty($_GET['Page']) && isset($modals[$_GET['Page']])) {
            include $modals[$_GET['Page']];
        }
    }
?>