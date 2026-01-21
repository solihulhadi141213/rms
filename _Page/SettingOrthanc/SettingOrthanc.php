<?php
    //Cek Aksesibilitas ke halaman ini
    $IjinAksesSaya=IjinAksesSaya($Conn,$SessionIdAccess,'aIPwzLJCmLLXfZLF1wSSzAFCowGVBqwDlbg1');
    if($IjinAksesSaya!=="Ada"){
        include "_Page/Error/NoAccess.php";
    }else{
?>
    <div class="pagetitle">
        <h1>
            <a href="">
                <i class="bi bi-plug"></i> Koneksi Orthanc Server</a>
            </a>
        </h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                <li class="breadcrumb-item active">Koneksi Orthanc Server</li>
            </ol>
        </nav>
    </div>
    <section class="section dashboard">
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <small>
                        Berikut ini adalah halaman untuk melakukan pengaturan koneksi dengan Orthanc Server. 
                        Baca dokumentasi lengkap pada URL Postman <a href="https://rsuelsyifa.postman.co/workspace/Orthanc-DICOM-Server~6efa9070-2767-4da8-a1df-284148592853/collection/12795177-8565c3c0-042d-443e-808a-8fd83b953d15?action=share&source=copy-link&creator=12795177">Berikut Ini</a>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </small>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-12 text-end">
                                <button type="button" class="btn btn-md btn-primary btn-floating modal_tambah" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Tambah Pengaturan Koneksi Orthanc">
                                    <i class="bi bi-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <td class="text-center"><b><small>No</small></b></td>
                                        <td class="text-left"><b><small>Nama Koneksi</small></b></td>
                                        <td class="text-left"><b><small>Base URL</small></b></td>
                                        <td class="text-left"><b><small>Username</small></b></td>
                                        <td class="text-left"><b><small>Password</small></b></td>
                                        <td class="text-center"><b><small>Status</small></b></td>
                                        <td class="text-center"><b><small>Opsi</small></b></td>
                                    </tr>
                                </thead>
                                <tbody id="tabel_koneksi">
                                    <tr>
                                        <td class="text-center" colspan="7">
                                            <small>Tidak ada data yang ditampilkan</small>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-12">
                                <small>
                                    Keterangan : <span class="text text-muted">Silahkan lakukan uji coba koneksi untuk memastikan pengaturan sudah benar</span>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php } ?>