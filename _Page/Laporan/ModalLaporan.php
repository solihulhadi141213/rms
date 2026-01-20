<div class="modal fade" id="ModalFilterDurasiPelayanan" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form action="javascript:void(0);" id="ProsesFilterDurasiPelayanan">
                <div class="modal-header">
                    <h5 class="modal-title text-dark"><i class="bi bi-funnel"></i> Periode Laporan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="periode_durasi_pelayanan">
                                <small>Periode Laporan</small>
                            </label>
                            <select name="periode_durasi_pelayanan" id="periode_durasi_pelayanan" class="form-control">
                                <option value="">Pilih</option>
                                <option value="Tahun">Tahun</option>
                                <option value="Bulan">Bulan</option>
                            </select>
                        </div>
                    </div>
                    <div id="form_lanjutan_durasi_pelayanan" class="mb-3"></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-rounded">
                        <i class="bi bi-check"></i> Tampilkan
                    </button>
                    <button type="button" class="btn btn-secondary btn-rounded" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Tutup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- MODAL FORM EXPORT LAPORAN DURASI PELAYANAN -->
<div class="modal fade" id="ModalExportLaporanDurasiPelayanan" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form action="_Page/Laporan/ProsesEksportLaporanDurasiPelayanan.php" method="POST" target="_blank">
                <div class="modal-header">
                    <h5 class="modal-title text-dark"><i class="bi bi-download"></i> Export Laporan Durasi Pelayanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12" id="FormExportLaporanDurasiPelayanan">
                            <!-- Form Rincian Durasi Akan Muncul Disini -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" disabled class="btn btn-primary btn-rounded" id="button_export_laporan_durasi_pelayanan">
                        <i class="bi bi-download"></i> Export
                    </button>
                    <button type="button" class="btn btn-secondary btn-rounded" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Tutup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
 
<div class="modal fade" id="ModalRincianDurasiPelayanan" tabindex="-1">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <form action="_Page/Laporan/ProsesExportRincianDurasiPelayanan.php" method="POST" target="_blank">
                <div class="modal-header nav_background">
                    <h5 class="modal-title text-light"><i class="bi bi-info-circle"></i> Rincian Laporan Durasi Pelayanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12" id="FormRincianDurasiPelayanan">
                            <!-- Form Rincian Durasi Akan Muncul Disini -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer nav_background">
                    <button type="submit" disabled class="btn btn-primary btn-rounded" id="button_export_rincian_durasi_pelayanan">
                        <i class="bi bi-download"></i> Export
                    </button>
                    <button type="button" class="btn btn-secondary btn-rounded" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Tutup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
