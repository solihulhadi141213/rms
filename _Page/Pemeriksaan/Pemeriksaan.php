<?php
    //Cek Aksesibilitas ke halaman ini
    $IjinAksesSaya=IjinAksesSaya($Conn,$SessionIdAccess,'DqA0kUSiUGYtR6msgXj0V7Lx2Sh9NkZW1NRD');
    if($IjinAksesSaya!=="Ada"){
        include "_Page/Error/NoAccess.php";
    }else{
?>
    <div class="pagetitle">
        <h1>
            <a href="">
                <i class="bi bi-plug"></i> Pemeriksaan Radiologi</a>
            </a>
        </h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                <li class="breadcrumb-item active">Pemeriksaan Radiologi</li>
            </ol>
        </nav>
    </div>
    <section class="section dashboard">
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <small>
                        Berikut ini adalah halaman untuk mengelola data pemeriksaan radiologi. 
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
                                <button type="button" class="btn btn-md btn-secondary btn-floating modal_filter" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Filter Data Pemeriksaan">
                                    <i class="bi bi-filter"></i>
                                </button>
                                 <button type="button" class="btn btn-md btn-primary btn-floating modal_pilih_kunjungan" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Tambah Permintaan Pemeriksaan Radiologi">
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
                                        <td valign="middle" class="text-center"><b><small>No</small></b></td>
                                        <td valign="middle" class="text-left"><b><small>RM</small></b></td>
                                        <td valign="middle" class="text-left"><b><small>Nama<br>Pasien</small></b></td>
                                        <td valign="middle" class="text-left"><b><small>Tgl/Jam</small></b></td>
                                        <td valign="middle" class="text-left"><b><small>Tujuan</small></b></td>
                                        <td valign="middle" class="text-left"><b><small>Pembayaran</small></b></td>
                                        <td valign="middle" class="text-left"><b><small>Asal<br>Kiriman</small></b></td>
                                        <td valign="middle" class="text-left"><b><small>Modality</small></b></td>
                                        <td valign="middle" class="text-left"><b><small>Radiografer</small></b></td>
                                        <td valign="middle" class="text-left"><b><small>Status</small></b></td>
                                        <td valign="middle" class="text-center"><b><small>Opsi</small></b></td>
                                    </tr>
                                </thead>
                                <tbody id="TabelPemeriksaan">
                                    <tr>
                                        <td class="text-center" colspan="11">
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
                                <small id="page_info">0 / 0</small>
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