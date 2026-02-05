<?php
    //Cek Aksesibilitas ke halaman ini
    $IjinAksesSaya=IjinAksesSaya($Conn,$SessionIdAccess,'VgkqxLVmQQM1qIPas6cBvgTLIlOxApnYPN4s');
    if($IjinAksesSaya!=="Ada"){
        include "_Page/Error/NoAccess.php";
    }else{
?>
    <div class="pagetitle">
        <h1>
            <a href="">
                <i class="bx bx-plug"></i> Koneksi SIMRS V.2</a>
            </a>
        </h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                <li class="breadcrumb-item active">Koneksi SIMRS V.2</li>
            </ol>
        </nav>
    </div>
    <section class="section dashboard">
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <small>
                        Berikut ini adalah halaman untuk melakukan pengaturan koneksi dengan SIMRS V.2. 
                        Baca dokumentasi lengkap pada URL Postman <a href="https://rsuelsyifa.postman.co/workspace/SIMRS-Old~ec3aa3f5-cd0b-4dd1-9c3c-c2e6bfded80b/collection/12795177-57a772fa-b882-4c8c-8612-a9795a146314?action=share&creator=12795177&active-environment=12795177-50ac0d19-d16b-4fdf-91c8-311d64ce2953">Berikut Ini</a>
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
                                <button type="button" class="btn btn-md btn-primary btn-floating modal_tambah" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Tambah Pengaturan Koneksi SIMRS">
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