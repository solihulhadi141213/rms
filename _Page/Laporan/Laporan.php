<?php
    //Cek Aksesibilitas ke halaman ini
    $IjinAksesSaya=IjinAksesSaya($Conn,$SessionIdAccess,'2HfUvMBIdyXT1omIjRflNGjTt8qjaJkJH6Np');
    if($IjinAksesSaya!=="Ada"){
        include "_Page/Error/NoAccess.php";
    }else{
?>
    <div class="pagetitle">
        <h1>
            <a href="">
                <i class="bi bi-bar-chart"></i> Laporan</a>
            </a>
        </h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                <li class="breadcrumb-item active">Laporan</li>
            </ol>
        </nav>
    </div>
    <section class="section dashboard">
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <small>
                        Berikut ini adalah halaman untuk menampilkan laporan pelayanan. 
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
                                <b>1. Durasi Pelayanan</b>
                            </div>
                            <div class="col-4 text-end">
                                <button type="button" class="btn btn-md btn-primary btn-floating modal_filter_durasi_pelayanan" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Tetapkan Periode Laporan">
                                    <i class="bi bi-filter"></i>
                                </button>
                                <button type="button" class="btn btn-md btn-outline-info btn-floating" id="modal_export_laporan_durasi_pelayanan">
                                    <i class="bi bi-printer"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-12 text-center" id="title_laporan_durasi_pelayanan">
                                <!-- Title Durasi Pelayanan Akan Muncul Disini -->
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="table table-responsive">
                                    <table class="table table-bordered table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <td valign="middle" rowspan="2" class="text-left"><b><small>Tahun</small></b></td>
                                                <td valign="middle" rowspan="2" class="text-left"><b><small>Bulan</small></b></td>
                                                <td valign="middle" rowspan="2" class="text-left"><b><small>Tanggal</small></b></td>
                                                <td valign="middle" rowspan="2" class="text-center"><b><small>Permintaan</small></b></td>
                                                <td valign="middle" colspan="3" class="text-center"><b><small>Waktu Pelayanan (Menit)</small></b></td>
                                                <td valign="middle" rowspan="2" class="text-center"><b><small>Total Durasi</small></b></td>
                                            </tr>
                                            <tr>
                                                <td valign="middle" class="text-center"><b><small>Diterima</small></b></td>
                                                <td valign="middle" class="text-center"><b><small>Dikerjakan</small></b></td>
                                                <td valign="middle" class="text-center"><b><small>Selesai</small></b></td>
                                            </tr>
                                        </thead>
                                        <tbody id="TabelLaporanDurasiPelayanan">
                                            <tr>
                                                <td class="text-center" colspan="8">
                                                    <small>Tidak ada data yang ditampilkan</small>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <small><b>Keterangan :</b> Laporan ini bersifat aktual, bisa berubah sewaktu-waktu.</small>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php } ?>