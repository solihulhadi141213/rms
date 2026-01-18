<!-- MODAL FILTER -->
<div class="modal fade" id="ModalFilter" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form action="javascript:void(0);" id="ProsesFilter">
                <input type="hidden" name="page" id="page" value="1">
                <div class="modal-header">
                    <h5 class="modal-title text-dark"><i class="bi bi-funnel"></i> Filter Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-4">
                            <label for="batas">
                                <small>Limit</small>
                            </label>
                        </div>
                        <div class="col-8">
                            <select name="batas" id="batas" class="form-control">
                                <option value="5">5</option>
                                <option selected value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                                <option value="250">250</option>
                                <option value="500">500</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4">
                            <label for="OrderBy">
                                <small>Dasar Urutan</small>
                            </label>
                        </div>
                        <div class="col-8">
                            <select name="OrderBy" id="OrderBy" class="form-control">
                                <option value="">Pilih</option>
                                <option value="nama_pasien">Nama Pasien</option>
                                <option value="id_pasien">No Rm</option>
                                <option value="datetime_diminta">Tanggal</option>
                                <option value="tujuan">Tujuan</option>
                                <option value="pembayaran">Pembayaran</option>
                                <option value="alat_pemeriksa">Modality</option>
                                <option value="status_pemeriksaan">Status</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4">
                            <label for="ShortBy">
                                <small>Tipe Urutan</small>
                            </label>
                        </div>
                        <div class="col-8">
                            <select name="ShortBy" id="ShortBy" class="form-control">
                                <option value="ASC">A To Z</option>
                                <option selected value="DESC">Z To A</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4">
                            <label for="KeywordBy">
                                <small>Dasar Pencarian</small>
                            </label>
                        </div>
                        <div class="col-8">
                            <select name="keyword_by" id="KeywordBy" class="form-control">
                                <option value="">Pilih</option>
                                <option value="nama_pasien">Nama Pasien</option>
                                <option value="id_pasien">No Rm</option>
                                <option value="datetime_diminta">Tanggal</option>
                                <option value="tujuan">Tujuan</option>
                                <option value="pembayaran">Pembayaran</option>
                                <option value="alat_pemeriksa">Modality</option>
                                <option value="status_pemeriksaan">Status</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4">
                            <label for="keyword">
                                <small>Kata Kunci</small>
                            </label>
                        </div>
                        <div class="col-8" id="FormFilter">
                            <input type="text" name="keyword" id="keyword" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-rounded">
                        <i class="bi bi-save"></i> Filter
                    </button>
                    <button type="button" class="btn btn-secondary btn-rounded" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Tutup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EXPORT -->
<div class="modal fade" id="ModalExport" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form action="_Page/Tagihan/ExportTagihanExcel.php" method="POST" target="_blank">
                <div class="modal-header">
                    <h5 class="modal-title text-dark"><i class="bi bi-download"></i> Export Tagihan (Invoice)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="periode_1">
                                <small>Periode Awal</small>
                            </label>
                            <input type="date" class="form-control" name="periode_1" id="periode_1">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="periode_2">
                                <small>Periode Akhir</small>
                            </label>
                            <input type="date" class="form-control" name="periode_2" id="periode_2">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-rounded">
                        <i class="bi bi-download"></i> Download
                    </button>
                    <button type="button" class="btn btn-secondary btn-rounded" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Tutup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL DETAIL -->
<div class="modal fade" id="ModalDetail" tabindex="-1">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <form action="_Page/Exporter/Exporter.php" method="GET" target="_blank">
                <div class="modal-header nav_background">
                    <h5 class="modal-title text-white"><i class="bi bi-info-circle"></i> Detail Tagihan (Invoice)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12" id="FormDetail">
                            <!-- Notifikasi Proses Akan Muncul Disini -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer nav_background">
                    <button type="submit" class="btn btn-primary btn-rounded">
                        <i class="bi bi-printer"></i> Cetak
                    </button>
                    <button type="button" class="btn btn-secondary btn-rounded" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Tutup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tambah Tagihan -->
 <div class="modal fade" id="ModalTambahTagihan" tabindex="-1">
    <div class="modal-dialog modal-lg border border-2 border-primary-subtle rounded-4 shadow-lg">
        <div class="modal-content">
            <form action="javascript:void(0);" id="ProsesTambahTagihan">
                <div class="modal-header">
                    <h5 class="modal-title text-dark"><i class="bi bi-plus"></i> Tambah Tagihan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12" id="FormTambahTagihan">
                            <!-- Form Akan Muncul Disini -->
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12" id="NotifikasiTambahTagihan">
                            <!-- Notifikasi Proses Akan Muncul Disini -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-rounded">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                    <button type="button" class="btn btn-secondary btn-rounded" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Tutup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Tagihan -->
<div class="modal fade" id="ModalEditTagihan" tabindex="-1">
    <div class="modal-dialog modal-md border border-2 border-primary-subtle rounded-4 shadow-lg">
        <div class="modal-content">
            <form action="javascript:void(0);" id="ProsesEditTagihan">
                <div class="modal-header">
                    <h5 class="modal-title text-dark"><i class="bi bi-pencil"></i> Edit Tagihan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12" id="FormEditTagihan">
                            <!-- Form Akan Muncul Disini -->
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12" id="NotifikasiEditTagihan">
                            <!-- Notifikasi Proses Akan Muncul Disini -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-rounded">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                    <button type="button" class="btn btn-secondary btn-rounded" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Tutup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Hapus Nota -->
<div class="modal fade" id="ModalHapusNota" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content border border-2 border-primary-subtle rounded-4 shadow-lg">
            <form action="javascript:void(0);" id="ProsesHapusNota" autocomplete="off">
                <div class="modal-header">
                    <h5 class="modal-title text-dark"><i class="bi bi-trash"></i> Hapus Invoice</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12" id="FormHapusNota">
                            <!-- Form Delete Akan Muncul Disini -->
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12" id="NotifikasiHapusNota">
                            <!-- Notifikasi Proses Delete Akan Muncul Disini -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-rounded">
                        <i class="bi bi-check"></i> Hapus
                    </button>
                    <button type="button" class="btn btn-secondary btn-rounded" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Tutup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>