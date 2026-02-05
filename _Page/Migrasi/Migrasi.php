<?php
    //Cek Aksesibilitas ke halaman ini
    $IjinAksesSaya=IjinAksesSaya($Conn,$SessionIdAccess,'yNsIOQ2UstRlw5ixEhZPyX7pxgpbMoIWHUD5');
    if($IjinAksesSaya!=="Ada"){
        include "_Page/Error/NoAccess.php";
    }else{

    // Menangkap Sub
    if(empty($_GET['Sub'])){
        $Sub = "";
    }else{
        $Sub = $_GET['Sub'];
    }

    // Routing warna
    if($Sub=="Simrs2"){
        $Simrs2 = "bg-primary text-light";
        $Simrs3 = "";
        $Simrs = "";
    }else{
        if($Sub=="Simrs3"){
            $Simrs2 = "";
            $Simrs3 = "bg-primary text-light";
            $Simrs = "";
        }else{
            $Simrs2 = "";
            $Simrs3 = "";
            $Simrs = "bg-secondary text-light";
        }
    }
?>
    <div class="pagetitle">
        <h1>
            <a href="index.php?Page=Migrasi">
                <i class="bi bi-arrow-left-right"></i> Migrasi</a>
            </a>
        </h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                <li class="breadcrumb-item active">Migrasi</li>
            </ol>
        </nav>
    </div>
    <section class="section dashboard">
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <small>
                        Berikut ini adalah halaman untuk melakukan migrasi data dari sistem lain ke Radix. Migrasi penting dilakukan untuk menjaga stabilitas data yang ada.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </small>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card <?php echo $Simrs; ?>">
                    <div class="card-body">
                        <b>Penjelasan Fitur</b>
                    </div>
                    <div class="card-footer">
                        <a href="index.php?Page=Migrasi" class="btn btn-sm btn-dark">
                            Penjelasan
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card <?php echo $Simrs2; ?>">
                    <div class="card-body">
                        <b>SIMRS V.2</b>
                    </div>
                    <div class="card-footer">
                        <a href="index.php?Page=Migrasi&Sub=Simrs2" class="btn btn-sm btn-dark">
                            Pliih Sistem
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card <?php echo $Simrs3; ?>">
                    <div class="card-body">
                        <b>SIMRS V.3</b>
                    </div>
                    <div class="card-footer">
                        <a href="index.php?Page=Migrasi&Sub=Simrs3" class="btn btn-sm btn-dark">
                            Pliih Sistem
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <?php
                    if($Sub=="Simrs2"){
                        include "_Page/Migrasi/Simrs2.php";
                    }else{
                        if($Sub=="Simrs3"){
                            include "_Page/Migrasi/Simrs3.php";
                        }else{
                            include "_Page/Migrasi/Pendahuluan.php";
                        }
                    }
                ?>
            </div>
        </div>
    </section>
<?php } ?>