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
                                <option value="id_pasien">No Rm</option>
                                <option value="id_kunjungan">ID Reg</option>
                                <option value="nama_pasien">Nama Pasien</option>
                                <option value="datetime_diminta">Tgl/Waktu Permintaan</option>
                                <option value="tujuan">Kunjungan</option>
                                <option value="pembayaran">Pembayaran</option>
                                <option value="asal_kiriman">Asal Kiriman</option>
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
                                <option value="id_pasien">No Rm</option>
                                <option value="id_kunjungan">ID Reg</option>
                                <option value="nama_pasien">Nama Pasien</option>
                                <option value="datetime_diminta">Tgl/Waktu Permintaan</option>
                                <option value="tujuan">Kunjungan</option>
                                <option value="pembayaran">Pembayaran</option>
                                <option value="asal_kiriman">Asal Kiriman</option>
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

<div class="modal fade" id="ModalKunjungan" tabindex="-1">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-dark"><i class="bi bi-search"></i> Pilih Kunjungan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="javascript:void(0);" id="ProsesFilterKunjungan" class="mb-2">
                    <input type="hidden" name="page" id="page_kunjungan" value="1">
                    <div class="row mb-2">
                        <div class="col-md-8"></div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="text" class="form-control" name="keyword" id="keyword_kunjungan" placeholder="No RM / Nama pasien">
                                <button type="submit" class="btn btn-md btn-primary">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="row mb-2 mt-3">
                    <div class="col-12">
                        <div class="table table-responsive border-top border-1">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <td><small><b>No</b></small></td>
                                        <td><small><b>No.RM</b></small></td>
                                        <td><small><b>Nama Pasien</b></small></td>
                                        <td><small><b>Tgl/Jam</b></small></td>
                                        <td><small><b>Tujuan</b></small></td>
                                        <td><small><b>Ruangan/Poli</b></small></td>
                                        <td><small><b>Status</b></small></td>
                                        <td><small><b>Opsi</b></small></td>
                                    </tr>
                                </thead>
                                <tbody id="TabelKunjungan">
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            <small>Tidak Ada Data Yang Ditampilkan</small>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <small id="page_info_kunjungan">0 / 0</small>
                    </div>
                    <div class="col-6 text-end">
                        <button type="button" class="btn btn-sm btn-outline-info btn-floating" id="prev_button_kunjungan">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-info btn-floating" id="next_button_kunjungan">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-rounded" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalTambahPermintaan" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-dark"><i class="bi bi-plus"></i> Tambah Permintaan Pemeriksaan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12" id="FormTambahPermintaan"></div>
                </div>
                <div class="row">
                    <div class="col-12" id="NotifikasiTambahPermintaan"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-rounded">
                    <i class="bi bi-save"></i> Simpan
                </button>
                <button type="button" class="btn btn-secondary btn-rounded" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>