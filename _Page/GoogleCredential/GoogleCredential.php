<?php
    //Cek Aksesibilitas ke halaman ini
    $IjinAksesSaya=IjinAksesSaya($Conn,$SessionIdAccess,'x2CsUoAj9w8kaochEhzurAgHUyDDVP1VlwuF');
    if($IjinAksesSaya!=="Ada"){
        include "_Page/Error/NoAccess.php";
    }else{
?>
    <div class="pagetitle">
        <h1>
            <a href="">
                <i class="bi bi-google"></i> Google Credential</a>
            </a>
        </h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                <li class="breadcrumb-item active">Google Credential</li>
            </ol>
        </nav>
    </div>
    <section class="section dashboard">
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <small>
                        Halaman ini digunakan untuk mengelola pengaturan <i>Google Credential</i>. 
                        API Service Google ini berfungsi untuk mempermudah user pada saat login menggunakan akun google miliknya.
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
                        <div class="col-8">
                            <b class="card-title"># List Credential</b>
                        </div>
                            <div class="col-md-4 text-end">
                                <button type="button" class="btn btn-md btn-primary btn-floating" data-bs-toggle="modal" data-bs-target="#ModalTambah">
                                    <i class="bi bi-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th class="text-center"><b>No</b></th>
                                        <th class="text-left"><b><i>Environment</i></b></th>
                                        <th class="text-left"><b><i>Client ID</i></b></th>
                                        <th class="text-left"><b><i>Client Secret</i></b></th>
                                        <th class="text-center"><b><i>Status</i></b></th>
                                        <th class="text-center"><b><i>Opsi</i></b></th>
                                    </tr>
                                </thead>
                                <tbody id="TabelGoogleCredential">
                                    <tr>
                                        <td colspan="6" class="text-center">
                                            <small class="text-danger">Belum ada data pengaturan <i>Google Credential</i> Yang Disimpan</small>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-12">
                                <small id="page_info">
                                    Total Data :
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php } ?>