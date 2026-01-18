<?php
    //Cek Aksesibilitas ke halaman ini
    $IjinAksesSaya=IjinAksesSaya($Conn,$SessionIdAccess,'e87OD806mKlTs3Hu1KtBSPqCrZs797MsGeyU');
    if($IjinAksesSaya!=="Ada"){
        include "_Page/Error/NoAccess.php";
    }else{
?>
    <div class="pagetitle">
        <h1>
            <a href="">
                <i class="bi bi-receipt"></i> Tagihan (Invoice)</a>
            </a>
        </h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                <li class="breadcrumb-item active">Tagihan (Invoice)</li>
            </ol>
        </nav>
    </div>
    <section class="section dashboard">
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <small>
                        Berikut ini adalah halaman untuk mengelola data tagihan (Invoice) pasien atas pelayanan pemeriksaan Radiologi. 
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
                                <button type="button" class="btn btn-md btn-outline-dark btn-floating modal_export" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Download/Export Data Tagihan">
                                    <i class="bi bi-download"></i>
                                </button>
                                 <button type="button" class="btn btn-md btn-secondary btn-floating modal_filter" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Filter Data Taghan / Invoice">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th><b>No</b></th>
                                        <th><b>Nama Pasien</b></th>
                                        <th><b>RM</b></th>
                                        <th><b>Tanggal</b></th>
                                        <th><b>Tujuan</b></th>
                                        <th><b><i>Modality</i></b></th>
                                        <th><b>Metode</b></th>
                                        <th><b>Status</b></th>
                                        <th><b>Tagihan</b></th>
                                    </tr>
                                </thead>
                                <tbody id="TabelTagihan">
                                    <tr>
                                        <td class="text-center" colspan="10">
                                            <small>Tidak ada data yang ditampilkan</small>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-6">
                                <small id="page_info">Page : 0 / 0</small>
                            </div>
                            <div class="col-6 text-end">
                                <button type="button" class="btn btn-sm btn-outline-info btn-floating" id="prev_button">
                                    <i class="bi bi-chevron-left"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-info btn-floating" id="next_button">
                                    <i class="bi bi-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php } ?>