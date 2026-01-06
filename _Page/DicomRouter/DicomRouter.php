<?php
    //Cek Aksesibilitas ke halaman ini
    $IjinAksesSaya=IjinAksesSaya($Conn,$SessionIdAccess,'KX9gf0vhmDPh6ewWEZhJBkfzzJSP381lGM8e');
    if($IjinAksesSaya!=="Ada"){
        include "_Page/Error/NoAccess.php";
    }else{
?>
    <div class="pagetitle">
        <h1>
            <a href="">
                <i class="bi bi-plug"></i> DICOM Router</a>
            </a>
        </h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                <li class="breadcrumb-item active">DICOM Router</li>
            </ol>
        </nav>
    </div>
    <section class="section dashboard">
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <small>
                        Berikut ini adalah halaman untuk download aplikasi DICOM router dari Satu Sehat. 
                        Baca dokumentasi lengkap pada URL <a href="https://satusehat.kemkes.go.id/platform/docs/id/dicom-system/installer-dicom/#installer-dicom-router">Berikut Ini</a>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </small>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <b class="card-title">Windows Instaler</b>
                    </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-12">
                                <button type="button" class="btn btn-md btn-rounded btn-primary download_dicom_router">
                                    <i class="bi bi-download"></i> Download DICOM Router
                                </button>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-12 text-center" id="hasil_download_instaler">
                                No Content
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php } ?>