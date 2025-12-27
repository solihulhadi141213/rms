<div class="pagetitle">
    <h1>
        <a href="">
            <i class="bi bi-grid"></i> Dashboard
        </a>
    </h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>
    </nav>
</div>
<section class="section dashboard">
    <div class="row">
        <div class="col-md-12" id="notifikasi_proses">
            <!-- Kejadian Kegagalan Menampilkan Data Akan Ditampilkan Disini -->
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card" id="card_jam_menarik">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-12 col-md-3 mb-3 mb-md-0 text-center text-md-start" id="image_menarik">
                            <img src="assets/img/<?php echo $app_logo; ?>" width="150px" 
                                class="image_menarik img-fluid" alt="<?php echo $company_name; ?>">
                        </div>

                        <div class="col-12 col-md-9 text-center text-md-end">
                            <div id="title_menarik" class="display-4 fw-bold"><?php echo $company_name; ?></div>
                            <div id="tanggal_menarik" class="fs-5 mb-2">Hari, 01 Januari 1900</div>
                            <div id="jam_menarik" class="display-6 fw-bold">00:00:00</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3 col-12">
            <div class="card info-card sales-card">
                <div class="filter">
                    <a class="icon" href="javascript:void(0);">
                        <i class="bi bi-repeat"></i>
                    </a>
                    <a class="icon" href="javascript:void(0);" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                        <li class="dropdown-header text-start"><h6>Periode</h6></li>
                        <li><a href="javascript:void(0);" class="dropdown-item">Hari Ini</a></li>
                        <li><a href="javascript:void(0);" class="dropdown-item">Bulan Ini</a></li>
                        <li><a href="javascript:void(0);" class="dropdown-item">Tahun Ini</a></li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-bell"></i>
                        </div>
                        <div class="ps-3">
                            <b id="permintaan_pemeriksaan">00.000</b><br>
                            <small>Permintaan Pemeriksaan</small><br>
                            <small>
                                <small class="text text-grayish"><?php echo date('F Y'); ?></small>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-12">
            <div class="card info-card customers-card">
                <div class="filter">
                    <a class="icon" href="javascript:void(0);">
                        <i class="bi bi-repeat"></i>
                    </a>
                    <a class="icon" href="javascript:void(0);" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                        <li class="dropdown-header text-start"><h6>Periode</h6></li>
                        <li><a href="javascript:void(0);" class="dropdown-item">Hari Ini</a></li>
                        <li><a href="javascript:void(0);" class="dropdown-item">Bulan Ini</a></li>
                        <li><a href="javascript:void(0);" class="dropdown-item">Tahun Ini</a></li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-lightning-charge"></i>
                        </div>
                        <div class="ps-3">
                            <b id="sedang_dikerjakan">00.000</b><br>
                            <small>Sedang Dikerjakan</small><br>
                            <small>
                                <small class="text text-grayish"><?php echo date('F Y'); ?></small>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-12">
            <div class="card info-card yellow-card">
                <div class="filter">
                    <a class="icon" href="javascript:void(0);">
                        <i class="bi bi-repeat"></i>
                    </a>
                    <a class="icon" href="javascript:void(0);" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                        <li class="dropdown-header text-start"><h6>Periode</h6></li>
                        <li><a href="javascript:void(0);" class="dropdown-item">Hari Ini</a></li>
                        <li><a href="javascript:void(0);" class="dropdown-item">Bulan Ini</a></li>
                        <li><a href="javascript:void(0);" class="dropdown-item">Tahun Ini</a></li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-clock"></i>
                        </div>
                        <div class="ps-3">
                            <b id="menunggu_hasil">00.000</b><br>
                            <small>Menunggu Hasil</small><br>
                            <small>
                                <small class="text text-grayish"><?php echo date('F Y'); ?></small>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-12">
            <div class="card info-card revenue-card">
                <div class="filter">
                    <a class="icon" href="javascript:void(0);">
                        <i class="bi bi-repeat"></i>
                    </a>
                    <a class="icon" href="javascript:void(0);" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                        <li class="dropdown-header text-start"><h6>Periode</h6></li>
                        <li><a href="javascript:void(0);" class="dropdown-item">Hari Ini</a></li>
                        <li><a href="javascript:void(0);" class="dropdown-item">Bulan Ini</a></li>
                        <li><a href="javascript:void(0);" class="dropdown-item">Tahun Ini</a></li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-check"></i>
                        </div>
                        <div class="ps-3">
                            <b id="selesai">00.00</b><br>
                            <small>Selesai</small><br>
                            <small>
                                <small class="text text-grayish"><?php echo date('F Y'); ?></small>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 col-12">
            <div class="card info-card purple-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-clipboard2-pulse"></i>
                        </div>
                        <div class="ps-3">
                            <b id="service_request">00.000</b><br>
                            <small>Service Request</small><br>
                            <small>
                                <small class="text text-grayish"><?php echo date('F Y'); ?></small>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-12">
            <div class="card info-card purple-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-tools"></i>
                        </div>
                        <div class="ps-3">
                            <b id="procedure">00.000</b><br>
                            <small>Procedure</small><br>
                            <small>
                                <small class="text text-grayish"><?php echo date('F Y'); ?></small>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-12">
            <div class="card info-card purple-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-camera"></i>
                        </div>
                        <div class="ps-3">
                            <b id="imaging_study">00.000</b><br>
                            <small>Imaging Study</small><br>
                            <small>
                                <small class="text text-grayish"><?php echo date('F Y'); ?></small>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-12">
            <div class="card info-card purple-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-binoculars"></i>
                        </div>
                        <div class="ps-3">
                            <b id="observation">00.000</b><br>
                            <small>Observation</small><br>
                            <small>
                                <small class="text text-grayish"><?php echo date('F Y'); ?></small>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-12">
            <div class="card info-card purple-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-file-medical"></i>
                        </div>
                        <div class="ps-3">
                            <b id="diagnostic_report">00.000</b><br>
                            <small>Diagnostic Report</small><br>
                            <small>
                                <small class="text text-grayish"><?php echo date('F Y'); ?></small>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-12">
            <div class="card info-card purple-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-bounding-box"></i>
                        </div>
                        <div class="ps-3">
                            <b id="expertise">00.000</b><br>
                            <small>Expertise X-Ray</small><br>
                            <small>
                                <small class="text text-grayish"><?php echo date('F Y'); ?></small>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-12">
            <div class="card info-card purple-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-soundwave"></i>
                        </div>
                        <div class="ps-3">
                            <b id="expertise_usg">00.000</b><br>
                            <small>Expertise USG</small><br>
                            <small>
                                <small class="text text-grayish"><?php echo date('F Y'); ?></small>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-12">
            <div class="card info-card purple-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-file-earmark-binary"></i>
                        </div>
                        <div class="ps-3">
                            <b id="dicom_file">00.000</b><br>
                            <small>DICOM File</small><br>
                            <small>
                                <small class="text text-grayish"><?php echo date('F Y'); ?></small>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-lg-12">
            
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body" id="chart">
                           <!-- Menampilkan Grafik Disini -->
                        </div>
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <b class="card-title">
                                <i class="bi bi-send"></i> Dokter Pengirim</small>
                            </b>
                        </div>
                        <div class="card-body">
                            <div class="activity">
                                <div class="table table-responsive">
                                    <table class="table table-hover table-striped">
                                        <thead>
                                            <tr>
                                                <th><b>Dokter</b></th>
                                                <th class="text-end"><b>Pemeriksaan</b></th>
                                            </tr>
                                        </thead>
                                        <tbody id="tabel_dokter_pengirim">
                                            <tr>
                                                <td colspan="2" class="text-center">
                                                    <small class="text-danger">Belum Ada Data Yang Ditampilkan</small>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <b class="card-title">
                                <i class="bi bi-send"></i> Asal Kiriman</small>
                            </b>
                        </div>
                        <div class="card-body">
                            <div class="activity">
                                <div class="table table-responsive">
                                    <table class="table table-hover table-striped">
                                        <thead>
                                            <tr>
                                                <th><b>Poli/Ruangan</b></th>
                                                <th class="text-end"><b>Pemeriksaan</b></th>
                                            </tr>
                                        </thead>
                                        <tbody id="tabel_dokter_pengirim">
                                            <tr>
                                                <td colspan="2" class="text-center">
                                                    <small class="text-danger">Belum Ada Data Yang Ditampilkan</small>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <b class="card-title">
                                <i class="bi bi-send"></i> Jenis Pemeriksaan</small>
                            </b>
                        </div>
                        <div class="card-body">
                            <div class="activity">
                                <div class="table table-responsive">
                                    <table class="table table-hover table-striped">
                                        <thead>
                                            <tr>
                                                <th><b>Loinc</b></th>
                                                <th class="text-end"><b>Pemeriksaan</b></th>
                                            </tr>
                                        </thead>
                                        <tbody id="tabel_dokter_pengirim">
                                            <tr>
                                                <td colspan="2" class="text-center">
                                                    <small class="text-danger">Belum Ada Data Yang Ditampilkan</small>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
