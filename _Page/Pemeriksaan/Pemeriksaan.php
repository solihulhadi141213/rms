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
        <div class="row" id="DataPemeriksaan">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-12 text-end">
                                <button type="button" class="btn btn-md btn-outline-secondary btn-floating reload_data_pemeriksaan" title="Reset Filter Data">
                                    <i class="bi bi-repeat"></i>
                                </button>
                                <button type="button" class="btn btn-md btn-secondary btn-floating modal_filter" title="Filter Data Pemeriksaan">
                                    <i class="bi bi-search"></i>
                                </button>
                                 <button type="button" class="btn btn-md btn-primary btn-floating modal_pilih_kunjungan" title="Tambah Permintaan Pemeriksaan Radiologi">
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
                                        <td valign="middle" class="text-center"><b><small>NO</small></b></td>
                                        <td valign="middle" class="text-left"><b><small>NAMA PASIEN</small></b></td>
                                        <td valign="middle" class="text-left"><b><small>RM</small></b></td>
                                        <td valign="middle" class="text-left"><b><small>TGL</small></b></td>
                                        <td valign="middle" class="text-left"><b><small>JAM</small></b></td>
                                        <td valign="middle" class="text-center">
                                            <b data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Metode Pembayaran">
                                                <small class="text-primary">PAY</small>
                                            </b>
                                        </td>
                                         <td valign="middle" class="text-center">
                                            <b data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Asal Kiriman">
                                                <small class="text-primary">FROM</small>
                                            </b>
                                        </td>
                                        <td valign="middle" class="text-center">
                                            <b data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Modalitas (Alat/Pesawat)">
                                                <small class="text-primary">MOD</small>
                                            </b>
                                        </td>
                                        <td valign="middle" class="text-center">
                                            <b data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Radiografer / Petugas">
                                                <small class="text-primary">OFC</small>
                                            </b>
                                        </td>
                                        <td valign="middle" class="text-center">
                                            <b data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Resource Satu Sehat">
                                                <small class="text-primary">RSS</small>
                                            </b>
                                        </td>
                                        <td valign="middle" class="text-center">
                                            <b data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Order Ke PACS">
                                                <small class="text-primary">PA</small>
                                            </b>
                                        </td>
                                        <td valign="middle" class="text-center">
                                            <b data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Status Pemeriksaan">
                                                <small class="text-primary">STTS</small>
                                            </b>
                                        </td>
                                        <td valign="middle" class="text-center">
                                            <b data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Option">
                                                <small class="text-primary">OPS</small>
                                            </b>
                                        </td>
                                    </tr>
                                </thead>
                                <tbody id="TabelPemeriksaan">
                                    <tr>
                                        <td class="text-center" colspan="12">
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
        <div class="row" id="TambahPermintaan">
            <div class="col-lg-12">
                <form action="javascript:void(0);" id="ProsesTambah">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-8">
                                    <b class="card-title"><i class="bi bi-plus"></i> Tambah Permintaan</b>
                                </div>
                                <div class="col-4 text-end">
                                    <button type="button" class="btn btn-md btn-secondary btn-floating back_to_data" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Kembali Ke Data Pemeriksaan">
                                        <i class="bi bi-chevron-left"></i>
                                    </button>
                                    <button type="button" class="btn btn-md btn-primary btn-floating modal_pilih_kunjungan" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Cari & Pilih Kunjungan">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12" id="FormTambahPermintaan"></div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12" id="NotifikasiTambahPermintaan"></div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="button" class="btn btn-outline-secondary btn-rounded btn-lg back_to_data">
                                <i class="bi bi-chevron-left"></i> Kembali
                            </button>
                            <button type="submit" class="btn btn-primary btn-rounded btn-lg">
                                <i class="bi bi-save me-2"></i> Simpan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
<?php } ?>