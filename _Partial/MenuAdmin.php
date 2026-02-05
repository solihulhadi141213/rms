<aside id="sidebar" class="sidebar menu_background">
    <ul class="sidebar-nav" id="sidebar-nav">
        <li class="nav-item">
            <a class="nav-link <?php if($PageMenu==""){echo "";}else{echo "collapsed";} ?>" href="index.php">
                <i class="bi bi-grid"></i> <span>Dashboard</span>
            </a>
        </li>
        <li class="nav-heading border-1 border-top">
            <div class="mt-3">Fitur Dasar</div>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if($PageMenu=="SettingGeneral"||$PageMenu=="SettingEmail"||$PageMenu=="ApiKey"){echo "";}else{echo "collapsed";} ?>" data-bs-target="#components-nav" data-bs-toggle="collapse" href="javascript:void(0);">
                <i class="bi bi-gear"></i>
                    <span>Pengaturan</span><i class="bi bi-chevron-down ms-auto">
                </i>
            </a>
            <ul id="components-nav" class="nav-content collapse <?php if($PageMenu=="SettingGeneral"||$PageMenu=="SettingEmail"||$PageMenu=="ApiKey"){echo "show";} ?>" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="index.php?Page=SettingGeneral" class="<?php if($PageMenu=="SettingGeneral"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Pengaturan Umum</span>
                    </a>
                </li> 
                <li>
                    <a href="index.php?Page=SettingEmail" class="<?php if($PageMenu=="SettingEmail"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Email Gateway</span>
                    </a>
                </li> 
                <li>
                    <a href="index.php?Page=ApiKey" class="<?php if($PageMenu=="ApiKey"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>API Key</span>
                    </a>
                </li> 
            </ul>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if($PageMenu=="AksesFitur"||$PageMenu=="AksesEntitas"||$PageMenu=="Akses"){echo "";}else{echo "collapsed";} ?>" data-bs-target="#components2-nav" data-bs-toggle="collapse" href="javascript:void(0);">
                <i class="bi bi-key"></i>
                    <span>Aksesibilitas</span><i class="bi bi-chevron-down ms-auto">
                </i>
            </a>
            <ul id="components2-nav" class="nav-content collapse <?php if($PageMenu=="AksesFitur"||$PageMenu=="AksesEntitas"||$PageMenu=="Akses"){echo "show";} ?>" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="index.php?Page=AksesFitur" class="<?php if($PageMenu=="AksesFitur"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Fitur Aplikasi</span>
                    </a>
                </li> 
                <li>
                    <a href="index.php?Page=AksesEntitas" class="<?php if($PageMenu=="AksesEntitas"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Group/Entitas</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?Page=Akses" class="<?php if($PageMenu=="Akses"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Akses Pengguna</span>
                    </a>
                </li> 
            </ul>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if($PageMenu=="SettingSimrs2"||$PageMenu=="SettingSimrs"||$PageMenu=="SettingSatuSehat"||$PageMenu=="SettingPacs"||$PageMenu=="SettingOrthanc"){echo "";}else{echo "collapsed";} ?>" data-bs-target="#components3-nav" data-bs-toggle="collapse" href="javascript:void(0);">
                <i class="bx bx-plug"></i>
                    <span>Koneksi</span> <i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="components3-nav" class="nav-content collapse <?php if($PageMenu=="SettingSimrs2"||$PageMenu=="SettingSimrs"||$PageMenu=="SettingSatuSehat"||$PageMenu=="SettingPacs"||$PageMenu=="SettingOrthanc"){echo "show";} ?>" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="index.php?Page=SettingSimrs2" class="<?php if($PageMenu=="SettingSimrs2"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Konkesi SIMRS V.2</span>
                    </a>
                </li> 
                <li>
                    <a href="index.php?Page=SettingSimrs" class="<?php if($PageMenu=="SettingSimrs"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Konkesi SIMRS V.3</span>
                    </a>
                </li> 
                <li>
                    <a href="index.php?Page=SettingSatuSehat" class="<?php if($PageMenu=="SettingSatuSehat"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Koneksi Satu Sehat</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?Page=SettingPacs" class="<?php if($PageMenu=="SettingPacs"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Koneksi PACS</span>
                    </a>
                </li> 
                <li>
                    <a href="index.php?Page=SettingOrthanc" class="<?php if($PageMenu=="SettingOrthanc"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Koneksi Orthanc</span>
                    </a>
                </li> 
            </ul>
        </li>
        <li class="nav-heading border-1 border-top">
            <div class="mt-3">Master</div>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if($PageMenu=="KodeKlinis"||$PageMenu=="KodePemeriksaan"||$PageMenu=="Tarif"||$PageMenu=="Question"||$PageMenu=="TandaTangan"){echo "";}else{echo "collapsed";} ?>" data-bs-target="#components5-nav" data-bs-toggle="collapse" href="javascript:void(0);">
                <i class="bi bi-table"></i>
                    <span>Referensi</span> <i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="components5-nav" class="nav-content collapse <?php if($PageMenu=="KodeKlinis"||$PageMenu=="KodePemeriksaan"||$PageMenu=="Tarif"||$PageMenu=="Question"||$PageMenu=="TandaTangan"){echo "show";} ?>" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="index.php?Page=KodeKlinis" class="<?php if($PageMenu=="KodeKlinis"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Kode Klinis</span>
                    </a>
                </li> 
                <li>
                    <a href="index.php?Page=KodePemeriksaan" class="<?php if($PageMenu=="KodePemeriksaan"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Kode Pemeriksaan</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?Page=Tarif" class="<?php if($PageMenu=="Tarif"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Tarif Layanan</span>
                    </a>
                </li> 
                <li>
                    <a href="index.php?Page=Question" class="<?php if($PageMenu=="Question"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Daftar Pertanyaan</span>
                    </a>
                </li> 
                <li>
                    <a href="index.php?Page=TandaTangan" class="<?php if($PageMenu=="TandaTangan"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Tanda Tangan</span>
                    </a>
                </li> 
            </ul>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if($PageMenu=="Pemeriksaan"){echo "";}else{echo "collapsed";} ?>" href="index.php?Page=Pemeriksaan">
                <i class="bi bi-clipboard"></i> <span>Pemeriksaan Radiologi</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if($PageMenu=="Tagihan"){echo "";}else{echo "collapsed";} ?>" href="index.php?Page=Tagihan">
                <i class="bi bi-receipt-cutoff"></i> <span>Tagihan (Invoice)</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if($PageMenu=="Laporan"){echo "";}else{echo "collapsed";} ?>" href="index.php?Page=Laporan">
                <i class="bi bi-bar-chart"></i> <span>Laporan</span>
            </a>
        </li>
        <li class="nav-heading border-1 border-top">
            <div class="mt-3">Fitur Lainnya</div>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if($PageMenu!=="Migrasi"){echo "collapsed";} ?>" href="index.php?Page=Migrasi">
                <i class="bi bi-arrow-left-right"></i>
                <span>Migrasi</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if($PageMenu!=="Aktivitas"){echo "collapsed";} ?>" href="index.php?Page=Aktivitas">
                <i class="bi bi-circle"></i>
                <span>Log Aktivitas</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if($PageMenu!=="Help"){echo "collapsed";} ?>" href="index.php?Page=Help&Sub=HelpData">
                <i class="bi bi-question"></i>
                <span>Dokumentasi</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link collapsed" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#ModalLogout">
                <i class="bi bi-box-arrow-in-left"></i>
                <span>Keluar</span>
            </a>
        </li>
    </ul>
</aside> 