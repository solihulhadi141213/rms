<div class="card">
    <form action="javascript:void(0);" id="ProsesFilterSimrs2">
        
        <div class="card-header">
            <div class="row">
                <div class="col-md-3 mb-2">
                    <b class="card-title">SIMRS V.2</b>
                </div>
                <div class="col-md-9 mb-2 text-end">
                    <small>Mulai tampilkan data layanan dari SIMRS V.2 berikut ini</small>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-2 mb-2">
                    <label for="page">
                        <small>Page</small>
                    </label>
                    <input type="text" readonly name="page" id="page" class="form-control" value="1">
                </div>
                <div class="col-md-2 mb-2">
                    <label for="limit">
                        <small>Batas/Limit</small>
                    </label>
                    <select name="limit" id="limit" class="form-control">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="150">150</option>
                        <option value="200">200</option>
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <label for="short_by">
                        <small>Short By</small>
                    </label>
                    <select name="short_by" id="short_by" class="form-control">
                        <option value="id_rad">ID Radiologi</option>
                        <option value="id_pasien">ID pasien</option>
                        <option value="nama">Nama pasien</option>
                        <option value="waktu">Waktu</option>
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <label for="order_by">
                        <small>Order By</small>
                    </label>
                    <select name="order_by" id="order_by" class="form-control">
                        <option value="ASC">ASC</option>
                        <option value="DESC">DESC</option>
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <label for="keyword_by">
                        <small>Keyword By</small>
                    </label>
                    <select name="keyword_by" id="keyword_by" class="form-control">
                        <option value="id_rad">ID Radiologi</option>
                        <option value="id_pasien">ID pasien</option>
                        <option value="nama">Nama pasien</option>
                        <option value="waktu">Waktu</option>
                    </select>
                </div>
                 <div class="col-md-2 mb-2">
                    <label for="keyword">
                        <small>Keyword</small>
                    </label>
                   <input type="text" name="keyword" id="keyword" class="form-control">
                </div>
            </div>
        </div>
        <div class="card-footer">
           <div class="row">
                <div class="col-md-10"></div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-md btn-block btn-primary">
                        <i class="bi bi-filter"></i> Tampilkan
                    </button>
                </div>
           </div>
        </div>
    </form>
</div>
<div class="card">
    <form action="javascript:void(0);" id="ProsesMigrasiSimrs2">
        <div class="card-header">
            <div class="row">
                <div class="col-md-4 mb-2">
                    <b class="card-title">SIMRS V.2</b>
                </div>
                <div class="col-md-8 mb-2 text-end">
                    <small id="metadata"></small>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="tbale table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <td align="center">
                                <small><small><b>No</b></small></small>
                            </td>
                            <td>
                                <small><small><b>RM</b></small></small>
                            </td>
                            <td>
                                <small><small><b>REG</b></small></small>
                            </td>
                            <td>
                                <small><small><b>RAD</b></small></small>
                            </td>
                            <td>
                                <small><small><b>Nama</b></small></small>
                            </td>
                            <td>
                                <small><small><b>Tgl</b></small></small>
                            </td>
                            <td>
                                <small><small><b>Mod</b></small></small>
                            </td>
                            <td>
                                <small><small><b>Pemeriksaan</b></small></small>
                            </td>
                            <td>
                                <small><small><b>Pengirim</b></small></small>
                            </td>
                            <td>
                                <small><small><b>Penerima</b></small></small>
                            </td>
                            <td>
                                <small><small><b>Tujuan</b></small></small>
                            </td>
                            <td align="center">
                                <small><small><b>St</b></small></small>
                            </td>
                        </tr>
                    </thead>
                    <tbody id="TabelSimrsV2">
                        <tr>
                            <td colspan="12" class="text-center">NO DATA</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <div class="row">
                <div class="col-8 mb-3">
                    <button type="button" disabled class="btn btn-sm btn-floating btn-secondary" id="prev_button">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <button type="button" disabled class="btn btn-sm btn-outline-secondary" id="page_info">
                        0 / 0
                    </button>
                    <button type="button" disabled class="btn btn-sm btn-floating btn-secondary" id="next_button">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
                <div class="col-4 mb-3 text-end">
                    <button type="submit" disabled class="btn btn-md btn-primary" id="ButtonMulaiMigrasi2">
                        <i class="bi bi-download"></i> Migrasi
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>