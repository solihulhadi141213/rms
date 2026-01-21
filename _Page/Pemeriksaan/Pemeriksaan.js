// ======================================================
// Fungsi Menampilkan Data Pemeriksaan (Smooth Transition)
// ======================================================
function ShowTablePemeriksaan() {
    var ProsesFilter = $('#ProsesFilter').serialize();
    var $container  = $('#TabelPemeriksaan');

    // Simpan tinggi awal untuk mencegah loncat
    var currentHeight = $container.outerHeight();
    $container.css('min-height', currentHeight + 'px');

    $.ajax({
        type: 'POST',
        url: '_Page/Pemeriksaan/TabelPemeriksaan.php',
        data: ProsesFilter,
        beforeSend: function () {
            // Fade out halus
            $container.stop(true, true).animate({
                opacity: 0.3
            }, 150);
        },
        success: function (data) {
            // Ganti konten
            $container.html(data);

            // Re-init tooltip
            $('[data-bs-toggle="tooltip"]').tooltip();
        },
        complete: function () {
            // Fade in kembali
            $container.stop(true, true).animate({
                opacity: 1
            }, 200, function () {
                // Lepaskan tinggi setelah animasi selesai
                $container.css('min-height', '');
            });
        }
    });
}


//Fungsi Menampilkan Data Kunjungan
function ShowTableKunjungan() {

    var $container = $('#TabelKunjungan');
    var heightBefore = $container.height(); // simpan tinggi awal

    var ProsesFilterKunjungan = $('#ProsesFilterKunjungan').serialize();

    // Kunci tinggi agar layout tidak loncat
    $container
        .css({
            'min-height': heightBefore + 'px',
            'opacity': 0.5
        });

    $.ajax({
        type    : 'POST',
        url     : '_Page/Pemeriksaan/TabelKunjungan.php',
        data    : ProsesFilterKunjungan,
        success : function (data) {

            // Fade out ringan
            $container.fadeOut(150, function () {

                // Ganti isi tabel
                $container.html(data);

                // Fade in
                $container.fadeIn(200, function () {

                    // Lepas kunci tinggi setelah render
                    $container.css({
                        'min-height': '',
                        'opacity': 1
                    });

                    // Re-init tooltip
                    $('[data-bs-toggle="tooltip"]').tooltip();
                });
            });
        }
    });
}

/// Fungsi Menampilkan Detail Informasi Pemeriksaan
function ShowDetail(id_radiologi) {
    // Simpan tinggi konten sebelum loading
    var currentHeight = $('#FormDetail').outerHeight();
    
    // Set minimum height untuk menjaga posisi elemen lain
    $('#FormDetail').css({
        'min-height': currentHeight + 'px',
        'transition': 'all 0.3s ease'
    });
    
    // Buat loading overlay yang smooth
    var loadingHTML = `
        <div class="loading-container" style="
            position: relative;
            min-height: 150px;
            display: flex;
            align-items: center;
            justify-content: center;
        ">
            <div class="loading-content" style="
                text-align: center;
                padding: 30px;
            ">
                <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="text-muted mb-0">Memuat data pemeriksaan...</p>
            </div>
        </div>
    `;
    
    // Fade out konten lama dengan smooth transition
    $('#FormDetail').fadeOut(300, function() {
        $(this).html(loadingHTML).fadeIn(300);
    });
    
    // Tampilkan Form Dengan Ajax
    $.ajax({
        type: 'POST',
        url: '_Page/Pemeriksaan/FormDetail.php',
        data: { id_radiologi: id_radiologi },
        beforeSend: function() {
            // Optional: Tambahkan efek blur ringan
            $('#FormDetail').css('filter', 'blur(2px)');
        },
        success: function(data) {
            // Fade out loading dengan smooth
            $('#FormDetail').fadeOut(300, function() {
                // Hapus efek blur dan reset CSS
                $(this).css({
                    'filter': 'none',
                    'min-height': 'auto',
                    'transition': 'none'
                });
                
                // Set konten baru dan fade in
                $(this).html(data).fadeIn(300);

                // Re-init tooltip
                $('[data-bs-toggle="tooltip"]').tooltip();
            });
        },
        error: function(xhr, status, error) {
            // Handle error dengan animasi yang smooth
            $('#FormDetail').fadeOut(300, function() {
                $(this).css({
                    'filter': 'none',
                    'min-height': 'auto',
                    'transition': 'none'
                });
                
                var errorHTML = `
                    <div class="alert alert-danger m-0">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-exclamation-triangle-fill me-3 fs-4"></i>
                            <div>
                                <h5 class="alert-heading mb-2">Gagal Memuat Data</h5>
                                <p class="mb-0">Terjadi kesalahan saat memuat data. Silakan coba lagi.</p>
                                <small class="text-muted d-block mt-2">${error}</small>
                            </div>
                        </div>
                    </div>
                `;
                
                $(this).html(errorHTML).fadeIn(300);
            });
        }
    });
}
function ShowDetailPemeriksaan() {
    var ProsesDetail = $('#ProsesDetail').serialize();
    var targetElement = $('#RowDetailPermintaan');

    // Ambil tinggi aman
    var currentHeight = targetElement.outerHeight();
    if (currentHeight < 100) {
        currentHeight = 100;
    }

    $.ajax({
        type: 'POST',
        url: '_Page/Pemeriksaan/_DetailPemeriksaan.php',
        data: ProsesDetail,

        beforeSend: function () {
            targetElement
                .css('min-height', currentHeight + 'px')
                .html(
                    '<div class="loading-overlay" style="display:flex;align-items:center;justify-content:center;min-height:' + currentHeight + 'px;">' +
                        '<div class="loading-spinner" style="' +
                            'width:40px;height:40px;' +
                            'border:3px solid #f3f3f3;' +
                            'border-top:3px solid #3498db;' +
                            'border-radius:50%;' +
                            'animation:spin 1s linear infinite;">' +
                        '</div>' +
                    '</div>'
                );
        },

        success: function (data) {
            targetElement.fadeOut(150, function () {
                targetElement
                    .html(data)
                    .fadeIn(150)
                    .css('min-height', '');
            });

            // Init tooltip jika tersedia
            if (typeof bootstrap !== 'undefined') {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            }
        },

        error: function () {
            targetElement.html(
                '<div class="alert alert-danger text-center">Gagal memuat data</div>'
            ).css('min-height', '');
        }
    });
}


/* Tambahkan animasi spin */
$('<style>')
    .text('@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }')
    .appendTo('head');

/// Fungsi Menampilkan Detail Informasi Pemeriksaan
function ShowPreview(id_radiologi) {
    
    // Loading 'FormPreview'
    $('#FormPreview').html('Loading...');
    
    // Tampilkan Form Dengan Ajax
    $.ajax({
        type: 'POST',
        url : '_Page/Pemeriksaan/FormPreview.php',
        data: { id_radiologi: id_radiologi },
        success: function(data) {
            $('#FormPreview').html(data);
        }
    });
}

function parseMoney(value) {
    if (!value) return 0;
    return parseFloat(
        value
            .replace(/\./g, '')   // hapus ribuan
            .replace(',', '.')    // ganti koma ke titik
    ) || 0;
}

function hitungTotalHarga() {
    let base_price        = parseMoney($('#base_price').val());
    let doctor_fee        = parseMoney($('#doctor_fee').val());
    let radiographers_fee = parseMoney($('#radiographers_fee').val());
    let facility_fee      = parseMoney($('#facility_fee').val());
    let equipment_fee     = parseMoney($('#equipment_fee').val());
    let quantity          = parseMoney($('#quantity').val());

    let total = base_price
              + doctor_fee
              + radiographers_fee
              + facility_fee
              + equipment_fee;

    $('#total_price').val(formatMoney(total));

    let amount = total*quantity;
     $('#amount').val(formatMoney(amount));
}
function hitungTotalHargaEdit() {
    let total_price_edit = parseMoney($('#total_price_edit').val());
    let quantity_edit    = parseMoney($('#quantity_edit').val());
    let total            = total_price_edit*quantity_edit;
    $('#amount_edit').val(formatMoney(total));
}
//Menampilkan Data Pertama Kali
$(document).ready(function() {
    // Pertama Kali Sembunyikan 'TambahPermintaan' dan'RowDetailPermintaan'
    $('#TambahPermintaan').hide();
    $('#RowDetailPermintaan').hide();

    // Tampilkan Data
    ShowTablePemeriksaan();

    /*  
    ---------------------------------------------------
    MODAL FILTER
    --------------------------------------------------- 
    */
    $(document).on('click', '.modal_filter', function(){
        $('#ModalFilter').modal('show');
    });

    //Ketika keyword_by diubah
    $('#KeywordBy').change(function(){
        var KeywordBy =$('#KeywordBy').val();
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Pemeriksaan/FormFilter.php',
            data        : {KeywordBy: KeywordBy},
            success     : function(data){
                $('#FormFilter').html(data);
            }
        });
    });

    //Proses Filter/Pencarian
    $('#ProsesFilter').submit(function(e){
        e.preventDefault();
        $('#page').val("1");
        ShowTablePemeriksaan();
        $('#ModalFilter').modal('hide');
    });

    //Pagging
    $(document).on('click', '#next_button', function() {
        var page_now = parseInt($('#page').val(), 10); // Pastikan nilai diambil sebagai angka
        var next_page = page_now + 1;
        $('#page').val(next_page);
        ShowTablePemeriksaan(0);
    });
    $(document).on('click', '#prev_button', function() {
        var page_now = parseInt($('#page').val(), 10); // Pastikan nilai diambil sebagai angka
        var next_page = page_now - 1;
        $('#page').val(next_page);
        ShowTablePemeriksaan(0);
    });

    /*
    |--------------------------------------------------------------------------
    | RESET FILTER
    |--------------------------------------------------------------------------
    */
    $(document).on('click', '.reload_data_pemeriksaan', function() {
        // Reset Filter
        $('#ProsesFilter')[0].reset();

        // Tampilkan Ulang Data
        ShowTablePemeriksaan();
    });

    /*
    |--------------------------------------------------------------------------
    | TAMBAH PERMINTAAN PEMERIKSAAN RADIOLOGI
    |--------------------------------------------------------------------------
    */

    // Klik tombol buka modal
    $(document).on('click', '.modal_pilih_kunjungan', function () {
        $('#ModalKunjungan').modal('show');
    });

    // Saat modal benar-benar tampil
    $('#ModalKunjungan').on('shown.bs.modal', function () {
        $('#keyword_kunjungan').focus().select();
        ShowTableKunjungan();
    });

    //Pagging kunjungan
    $(document).on('click', '#next_button_kunjungan', function() {
        var page_now = parseInt($('#page_kunjungan').val(), 10); // Pastikan nilai diambil sebagai angka
        var next_page = page_now + 1;
        $('#page_kunjungan').val(next_page);
        ShowTableKunjungan(0);
    });
    $(document).on('click', '#prev_button_kunjungan', function() {
        var page_now = parseInt($('#page_kunjungan').val(), 10); // Pastikan nilai diambil sebagai angka
        var next_page = page_now - 1;
        $('#page_kunjungan').val(next_page);
        ShowTableKunjungan(0);
    });

    // Submit Pencarian
    $('#ProsesFilterKunjungan').submit(function(e){

        e.preventDefault();
        // Reset Halaman
        $('#page_kunjungan').val(1);

        // Tampilkan Data
        ShowTableKunjungan(0);
    });

    //Menampilkan Form Tambah Permintaan
    $(document).on('click', '.tambah_permintaan', function () {

        var id_kunjungan = $(this).data('id');

        // Reset UI
        $('#NotifikasiTambahPermintaan').html('');
        $('#FormTambahPermintaan').html('Loading...');

        // Tutup Modal 'ModalKunjungan'
        $('#ModalKunjungan').modal('hide');

        // Sembunyikan 'DataPemeriksaan'
        $('#DataPemeriksaan').hide();

        // Sembunyikan 'RowDetailPermintaan'
        $('#RowDetailPermintaan').hide();

        // Tampilkan 'TambahPermintaan'
        $('#TambahPermintaan').show();

        // Load form via AJAX (TIDAK bergantung event modal)
        $.ajax({
            type: 'POST',
            url: '_Page/Pemeriksaan/FormTambahPermintaan.php',
            data: { id_kunjungan: id_kunjungan },
            success: function (data) {

                $('#FormTambahPermintaan').html(data);

                /* ===============================
                | SELECT2 DOKTER
                =============================== */
                $('#dokter_pengirim').select2({
                    theme         : 'bootstrap-5',
                    placeholder   : 'Cari dokter...',
                    allowClear    : true,
                    width         : '100%',
                    dropdownParent: $('#FormTambahPermintaan')
                });

                /* ===============================
                | SELECT2 KLINIS
                =============================== */
                $('#klinis').select2({
                    theme             : 'bootstrap-5',
                    placeholder       : 'Ketik & pilih klinis',
                    tags              : false,
                    width             : '100%',
                    minimumInputLength: 2,
                    dropdownParent    : $('#FormTambahPermintaan'),
                    ajax: {
                        url: '_Page/Pemeriksaan/AjaxKlinis.php',
                        dataType: 'json',
                        delay: 300,
                        data: function (params) {
                            return {
                                q: params.term,
                                page: params.page || 1
                            };
                        },
                        processResults: function (data, params) {
                            return {
                                results: data.results,
                                pagination: {
                                    more: data.pagination?.more || false
                                }
                            };
                        },
                        cache: true
                    }
                });

                /* ===============================
                | SELECT2 PERMINTAAN PEMERIKSAAN
                =============================== */
                function initPemeriksaan() {

                    var alat = $('#alat_pemeriksa').val();
                    var enabled = alat && alat !== '';

                    // Destroy jika sudah ada
                    if ($('#permintaan_pemeriksaan').hasClass('select2-hidden-accessible')) {
                        $('#permintaan_pemeriksaan').select2('destroy');
                    }

                    $('#permintaan_pemeriksaan')
                        .prop('disabled', !enabled)
                        .empty();

                    var opt = {
                        theme: 'bootstrap-5',
                        width: '100%',
                        placeholder: enabled
                            ? 'Pilih pemeriksaan'
                            : 'Pilih alat pemeriksa terlebih dahulu',
                        allowClear: true,
                        dropdownParent: $('#FormTambahPermintaan'),
                        minimumResultsForSearch: 0
                    };

                    if (enabled) {
                        opt.ajax = {
                            url: '_Page/Pemeriksaan/AjaxPemeriksaan.php',
                            dataType: 'json',
                            delay: 250,
                            data: function (params) {
                                return {
                                    q: params.term || '',
                                    alat: alat,
                                    page: params.page || 1,
                                    limit: 10
                                };
                            },
                            processResults: function (data) {
                                return {
                                    results: data.results,
                                    pagination: {
                                        more: data.pagination?.more || false
                                    }
                                };
                            },
                            cache: true
                        };
                    }

                    $('#permintaan_pemeriksaan').select2(opt);
                }

                // Init awal (disabled)
                initPemeriksaan();

                // Saat alat berubah
                $('#alat_pemeriksa').on('change', function () {
                    $('#permintaan_pemeriksaan').val(null).trigger('change');
                    initPemeriksaan();
                });
            }
        });
    });

    // Ketika Klik 'back_to_data'
    $(document).on('click', '.back_to_data', function () {
        // Tampilkan 'DataPemeriksaan'
        $('#DataPemeriksaan').show();

        // Sembunyikan 'TambahPermintaan'
        $('#TambahPermintaan').hide();

        // Sembunyikan 'RowDetailPermintaan'
        $('#RowDetailPermintaan').hide();

        // Kembalikan posisi layar ke atas
        $('html, body').scrollTop(0);

        hideFloatingOption();
    });

    /* Ketika 'ProsesTambah' disubmit */
    $('#ProsesTambah').submit(function(e){

        e.preventDefault();
       
        /* Menangkap data dari form  */
        var ProsesTambah=$('#ProsesTambah').serialize();

        /* Loading Notification */
        $('#NotifikasiTambahPermintaan').html('loading..');

        /* Kirim data dengan AJAX  */
        $.ajax({
            type    : 'POST',
            url     : '_Page/Pemeriksaan/ProsesTambah.php',
            dataType: 'json',
            data    : ProsesTambah,
            success: function(response) {
                var status  = response.status;
                var message = response.message;

                // Apabila berhasil
                if(status=='success'){
                    //Bersihkan notifikasi
                    $('#NotifikasiTambahPermintaan').html('');

                    //reset form
                    $('#ProsesTambah')[0].reset();
                    $('#ProsesFilter')[0].reset();

                    // Kembalikan posisi layar ke atas
                    $('html, body').scrollTop(0);

                    //Tutup modal
                    $('#DataPemeriksaan').show();
                    $('#TambahPermintaan').hide();
                    $('#RowDetailPermintaan').hide();

                    //Menampilkan Swal
                    Swal.fire(
                        'Success!',
                        'Tambah Permintaan Pemeriksaan Berhasil!',
                        'success'
                    )

                    //reload tabel
                    ShowTablePemeriksaan();

                }else{
                    $('#NotifikasiTambahPermintaan').html('<div class="alert alert-danger"><small>'+message+'</small></div>');
                }
                
            }
        });
    });

    /*
    ===================================================================================
    PREVIEW PEMERIKSAAN RADIOLOGI
    ===================================================================================
    */
    $(document).on('click', '.modal_preview', function () {

        //tangkap data 'id_radiologi' dan buat variabel
        var id_radiologi   = $(this).data('id');

        //tampilkan modal
        $('#ModalPreview').modal('show');

        //Tampilkan detail dengan function
        ShowPreview(id_radiologi)
        
    });

    // Selengkapnya Mengarah Ke _DetailPemeriksaan
    $('#ProsesDetail').submit(function(e){

        e.preventDefault();

        // Load data
        ShowDetailPemeriksaan();
        
        // Tutup Modal
        $('#ModalPreview').modal('hide');

        // Tampilkan Element Yang Diperlukan
        $('#RowDetailPermintaan').show();

        // Sembunyikan Element Yang Tidak Perlu
        $('#DataPemeriksaan').hide();
        $('#TambahPermintaan').hide();
        
    });

    // Ketika Klik 'back_to_data'
    $(document).on('click', '.reload_detail', function () {
        ShowDetailPemeriksaan();
    });

    /*
    ===================================================================================
    DETAIL PEMERIKSAAN RADIOLOGI
    ===================================================================================
    */
    $(document).on('click', '.modal_detail', function () {

        //tangkap data 'id_radiologi' dan buat variabel
        var id_radiologi   = $(this).data('id');

        //tampilkan modal
        $('#ModalDetail').modal('show');

        //Tampilkan detail dengan function
        ShowDetail(id_radiologi);
        
    });

    /*
    |=========================================================================
    | TERIMA PERMINTAAN
    |=========================================================================
    */
    $(document).on('click', '.modal_terima_permintaan', function () {

        //tangkap data 'id_radiologi' dan 'status'
        var id_radiologi = $(this).data('id');
        var status       = $(this).data('status');

        //tampilkan modal
        $('#ModalTerimaPermintaan').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiTerimaPermintaan').html('');

        //Form Loading
        $('#FormTerimaPermintaan').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Pemeriksaan/FormTerimaPermintaan.php',
            data        : {id_radiologi: id_radiologi, status: status},
            success     : function(data){
                $('#FormTerimaPermintaan').html(data);

                if(status=="Terima"){
                    $('.terima_atau_tolak').html('Terima');

                    /* ===============================
                    | SELECT2 DOKTER
                    =============================== */
                    $('#dokter_penerima').select2({
                        theme         : 'bootstrap-5',
                        placeholder   : 'Cari dokter...',
                        allowClear    : true,
                        width         : '100%',
                        dropdownParent: $('#FormTerimaPermintaan')
                    });
                }else{
                    $('.terima_atau_tolak').html('Pembatalan');
                }
            }
        });
    });

    /* Ketika 'ProsesTerimaPermintaan' disubmit */
    $('#ProsesTerimaPermintaan').submit(function(e){
        e.preventDefault();

        /* Menangkap data dari form  */
        var ProsesTerimaPermintaan=$('#ProsesTerimaPermintaan').serialize();

        /* Loading Notification */
        $('#NotifikasiTerimaPermintaan').html('loading..');

        /* Kirim data dengan AJAX  */
        $.ajax({
            type    : 'POST',
            url     : '_Page/Pemeriksaan/ProsesTerimaPermintaan.php',
            dataType: 'json',
            data    : ProsesTerimaPermintaan,
            success: function(response) {
                var status  = response.status;
                var message = response.message;

                // Apabila berhasil
                if(status=='success'){
                    //Bersihkan notifikasi
                    $('#NotifikasiTerimaPermintaan').html('');

                    //reset form
                    $('#ProsesTerimaPermintaan')[0].reset();

                    //Tutup modal
                    $('#ModalTerimaPermintaan').modal('hide');

                    //Menampilkan Swal
                    Swal.fire(
                        'Success!',
                        'Update Permintaan Pemeriksaan Berhasil!',
                        'success'
                    )

                    //reload tabel
                    ShowTablePemeriksaan();

                }else{
                    $('#NotifikasiTerimaPermintaan').html('<div class="alert alert-danger"><small>'+message+'</small></div>');
                }
                
            }
        });
    });

    /*
    |=========================================================================
    | EDIT RADIOLOGI
    |=========================================================================
    */
    $(document).on('click', '.modal_edit_radiologi', function () {

        //tangkap data 'id_radiologi'
        var id_radiologi = $(this).data('id');

        //tampilkan modal
        $('#ModalEditRadiologi').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiEditRadiologi').html('');

        //Form Loading
        $('#FormEditRadiologi').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Pemeriksaan/FormEditRadiologi.php',
            data        : {id_radiologi: id_radiologi},
            success     : function(data){
                $('#FormEditRadiologi').html(data);

                // Tangkap status
                var status_pemeriksaan = $('#status_pemeriksaan_edit').val();

                // apabila status selesai maka hide alasan pembatalan
                if(status_pemeriksaan=="Selesai"){
                    $('#form_alasan_pembatalan').hide();
                }
            }
        });
    });

    $(document).on('change', '#status_pemeriksaan_edit', function () {

        //tangkap data 'id_radiologi'
        var status_pemeriksaan = $('#status_pemeriksaan_edit').val();

        // Routing Form Berdasarkan Status Pemeriksaan
        if(status_pemeriksaan=="Batal"){
            $('#form_alasan_pembatalan').show();
        }else{
            $('#form_alasan_pembatalan').hide();
        }

        if(status_pemeriksaan=="Selesai"){
            $("#alat_pemeriksa_edit").prop("disabled", true);
            $("#radiografer_edit").prop("disabled", true);
        }else{
           $("#alat_pemeriksa_edit").prop("disabled", false);
            $("#radiografer_edit").prop("disabled", false);
        }

    });

    /* Ketika 'ProsesEditRadiologi' disubmit */
    $('#ProsesEditRadiologi').submit(function(e){
        e.preventDefault();

        /* Menangkap data dari form  */
        var ProsesEditRadiologi=$('#ProsesEditRadiologi').serialize();

        /* Loading Notification */
        $('#NotifikasiEditRadiologi').html('loading..');

        /* Kirim data dengan AJAX  */
        $.ajax({
            type    : 'POST',
            url     : '_Page/Pemeriksaan/ProsesEditRadiologi.php',
            dataType: 'json',
            data    : ProsesEditRadiologi,
            success: function(response) {
                var status  = response.status;
                var message = response.message;

                // Apabila berhasil
                if(status=='success'){
                    //Bersihkan notifikasi
                    $('#NotifikasiEditRadiologi').html('');

                    //reset form
                    $('#ProsesEditRadiologi')[0].reset();

                    //Tutup modal
                    $('#ModalEditRadiologi').modal('hide');

                    //Menampilkan Swal
                    Swal.fire(
                        'Success!',
                        'Update Permintaan Pemeriksaan Berhasil!',
                        'success'
                    )

                    //reload tabel
                    ShowTablePemeriksaan();

                    // Reload data Detail
                    ShowDetailPemeriksaan();

                }else{
                    $('#NotifikasiEditRadiologi').html('<div class="alert alert-danger"><small>'+message+'</small></div>');
                }
                
            }
        });
    });


    /*
    ===================================================================================
    SERVICE REQUEST
    ===================================================================================
    */
    $(document).on('click', '.modal_service_request', function () {

        //tangkap data 'id_radiologi' dan buat variabel
        var id_radiologi   = $(this).data('id');

        //tampilkan modal
        $('#ModalServiceRequest').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiFormServiceRequest').html('');

        //Form Loading
        $('#FormServiceRequest').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Pemeriksaan/FormServiceRequest.php',
            data        : {id_radiologi: id_radiologi},
            success     : function(data){
                $('#FormServiceRequest').html(data);
            }
        });
    });

    $('#ProsesServiceRequest').submit(function(e){

        e.preventDefault();
       
        /* Menangkap data dari form  */
        var ProsesServiceRequest=$('#ProsesServiceRequest').serialize();

        /* Loading Notification */
        $('#NotifikasiFormServiceRequest').html('loading..');

        /* Kirim data dengan AJAX  */
        $.ajax({
            type    : 'POST',
            url     : '_Page/Pemeriksaan/ProsesServiceRequest.php',
            dataType: 'json',
            data    : ProsesServiceRequest,
            success: function(response) {
                var status       = response.status;
                var message      = response.message;
                var id_radiologi = response.id_radiologi;

                // Apabila berhasil
                if(status=='success'){
                    //Bersihkan notifikasi
                    $('#NotifikasiFormServiceRequest').html('');

                    //Tutup modal
                    $('#ModalServiceRequest').modal('hide');

                    //reload data detail
                    ShowDetail(id_radiologi);

                    // Reload Data Permintaan Pemeriksaan
                    ShowTablePemeriksaan();

                    // Menampilkan Swal
                    Swal.fire(
                        'Success!',
                        'Service Request Berhasil Dikirim!',
                        'success'
                    )
                }else{
                    $('#NotifikasiFormServiceRequest').html('<div class="alert alert-danger"><small>'+message+'</small></div>');
                }
                
            }
        });
    });

    $(document).on('click', '.modal_detail_service_request', function () {

        //tangkap data 'id_service_request' dan buat variabel
        var id_service_request   = $(this).data('id');

        //tampilkan modal
        $('#ModalDetailServiceRequest').modal('show');

        //Form Loading
        $('#FormDetailServiceRequest').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Pemeriksaan/FormDetailServiceRequest.php',
            data        : {id_service_request: id_service_request},
            success     : function(data){
                $('#FormDetailServiceRequest').html(data);
            }
        });
    });

    /*
    ===================================================================================
    PROCEDURE
    ===================================================================================
    */
    $(document).on('click', '.modal_procedure', function () {

        //tangkap data 'id_radiologi' dan buat variabel
        var id_radiologi   = $(this).data('id');

        //tampilkan modal
        $('#ModalProcedure').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiProcedure').html('');

        //Form Loading
        $('#FormProcedure').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Pemeriksaan/FormProcedure.php',
            data        : {id_radiologi: id_radiologi},
            success     : function(data){
                $('#FormProcedure').html(data);
            }
        });
    });
    $('#ProsesProcedure').submit(function(e){
       e.preventDefault();
        /* Menangkap data dari form  */
        var ProsesProcedure=$('#ProsesProcedure').serialize();

        /* Loading Notification */
        $('#NotifikasiProcedure').html('loading..');

        /* Kirim data dengan AJAX  */
        $.ajax({
            type    : 'POST',
            url     : '_Page/Pemeriksaan/ProsesProcedure.php',
            dataType: 'json',
            data    : ProsesProcedure,
            success: function(response) {
                var status       = response.status;
                var message      = response.message;
                var id_radiologi = response.id_radiologi;

                // Apabila berhasil
                if(status=='success'){
                    //Bersihkan notifikasi
                    $('#NotifikasiProcedure').html('');

                    //Tutup modal
                    $('#ModalProcedure').modal('hide');

                    //reload data detail
                    ShowDetail(id_radiologi);

                    // Reload Tabel Pemeriksaan
                    ShowTablePemeriksaan();

                    // Menampilkan Swal
                    Swal.fire(
                        'Success!',
                        'Resource Procedure Berhasil Dikirim Ke Satu Sehat!',
                        'success'
                    )
                }else{
                    $('#NotifikasiProcedure').html('<div class="alert alert-danger"><small>'+message+'</small></div>');
                }
                
            }
        });
    });

    $(document).on('click', '.modal_detail_procedure', function () {

        //tangkap data 'id_procedure' dan buat variabel
        var id_procedure   = $(this).data('id');

        //tampilkan modal
        $('#ModalDetailProcedure').modal('show');

        //Form Loading
        $('#FormDetailProcedure').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Pemeriksaan/FormDetailProcedure.php',
            data        : {id_procedure: id_procedure},
            success     : function(data){
                $('#FormDetailProcedure').html(data);
            }
        });
    });

    /*
    ===================================================================================
    IMAGING STUDY
    ===================================================================================
    */
    $(document).on('click', '.modal_imaging_study', function () {

        //tangkap data 'id_radiologi' dan buat variabel
        var id_radiologi   = $(this).data('id');

        //tampilkan modal
        $('#ModalImagingStudy').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiImagingStudy').html('');

        //Form Loading
        $('#FormImagingStudy').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Pemeriksaan/FormImagingStudy.php',
            data        : {id_radiologi: id_radiologi},
            success     : function(data){
                $('#FormImagingStudy').html(data);
            }
        });
    });

    $('#ProsesImagingStudy').submit(function(e){
       e.preventDefault();
        /* Menangkap data dari form  */
        var ProsesImagingStudy=$('#ProsesImagingStudy').serialize();

        /* Loading Notification */
        $('#NotifikasiImagingStudy').html('loading..');

        /* Kirim data dengan AJAX  */
        $.ajax({
            type    : 'POST',
            url     : '_Page/Pemeriksaan/ProsesImagingStudy.php',
            dataType: 'json',
            data    : ProsesImagingStudy,
            success: function(response) {
                var status       = response.status;
                var message      = response.message;
                var id_radiologi = response.id_radiologi;

                // Apabila berhasil
                if(status=='success'){
                    //Bersihkan notifikasi
                    $('#NotifikasiImagingStudy').html('');

                    //Tutup modal
                    $('#ModalImagingStudy').modal('hide');

                    //reload data detail
                    ShowDetail(id_radiologi);

                    // Reload Tabel Pemeriksaan
                    ShowTablePemeriksaan();

                    // Menampilkan Swal
                    Swal.fire(
                        'Success!',
                        'Resource Imaging Study Berhasil Dikirim Ke Satu Sehat!',
                        'success'
                    )
                }else{
                    $('#NotifikasiImagingStudy').html('<div class="alert alert-danger"><small>'+message+'</small></div>');
                }
                
            }
        });
    });

    $(document).on('click', '.modal_detail_imaging_study', function () {

        //tangkap data 'id_imaging_study' dan buat variabel
        var id_imaging_study   = $(this).data('id');

        //tampilkan modal
        $('#ModalDetailImagingStudy').modal('show');

        //Form Loading
        $('#FormDetailImagingStudy').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Pemeriksaan/FormDetailImagingStudy.php',
            data        : {id_imaging_study: id_imaging_study},
            success     : function(data){
                $('#FormDetailImagingStudy').html(data);
            }
        });
    });

    /*
    ===================================================================================
    OBSERVATION
    ===================================================================================
    */

    $(document).on('click', '.modal_observation', function () {

        //tangkap data 'id_radiologi' dan buat variabel
        var id_radiologi   = $(this).data('id');

        //tampilkan modal
        $('#ModalObservation').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiObservation').html('');

        //Form Loading
        $('#FormObservation').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Pemeriksaan/FormObservation.php',
            data        : {id_radiologi: id_radiologi},
            success : function(data){
                $('#FormObservation').html(data);
                
                let quillObservation = null;
                if (!quillObservation) {
                    quillObservation = new Quill('#editor-valueString', {
                        theme: 'snow'
                    });

                    quillObservation.on('text-change', function () {
                        $('#valueString').val(
                            quillObservation.root.innerHTML
                        );
                    });
                } else {
                    // 🔄 RESET ISI EDITOR
                    quillObservation.setText('');
                    $('#valueString').val('');
                }
            }
        });
    });

    $('#ProsesObservation').submit(function(e){
        e.preventDefault(); // WAJIB agar tidak submit normal

        var ProsesObservation = $('#ProsesObservation').serialize();

        $.ajax({
            type    : 'POST',
            url     : '_Page/Pemeriksaan/ProsesObservation.php',
            dataType: 'json',
            data    : ProsesObservation,

            // 🔒 KUNCI TOMBOL SAAT REQUEST DIMULAI
            beforeSend: function(){
                $('#btnSubmitObservation').prop('disabled', true);
                $('#NotifikasiObservation').html('Mengirim data...');
            },

            // ✅ RESPONSE BERHASIL DITERIMA (HTTP 200)
            success: function(response){
                var status       = response.status;
                var message      = response.message;
                var id_radiologi = response.id_radiologi;

                if(status === 'success'){
                    $('#NotifikasiObservation').html('');
                    $('#ModalObservation').modal('hide');

                    ShowDetail(id_radiologi);
                    ShowTablePemeriksaan();

                    Swal.fire(
                        'Success!',
                        'Resource Observation Berhasil Dikirim Ke Satu Sehat!',
                        'success'
                    );
                }else{
                    $('#NotifikasiObservation').html(
                        '<div class="alert alert-danger"><small>'+message+'</small></div>'
                    );
                }
            },

            // ❌ ERROR TEKNIS (NETWORK / 500 / TIMEOUT)
            error: function(xhr){
                $('#NotifikasiObservation').html(
                    '<div class="alert alert-danger">' +
                    '<small>Koneksi ke server gagal</small>' +
                    '</div>'
                );
            },

            // 🔓 AKTIFKAN KEMBALI TOMBOL (SELALU DIEKSEKUSI)
            complete: function(){
                $('#btnSubmitObservation').prop('disabled', false);
            }
        });
    });

    $(document).on('click', '.modal_detail_observation', function () {

        //tangkap data 'id_observation' dan buat variabel
        var id_observation   = $(this).data('id');

        //tampilkan modal
        $('#ModalDetailObservation').modal('show');

        //Form Loading
        $('#FormDetailObservation').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Pemeriksaan/FormDetailObservation.php',
            data        : {id_observation: id_observation},
            success     : function(data){
                $('#FormDetailObservation').html(data);
            }
        });
    });

     /*
    ===================================================================================
    DIAGNOSTIC REPORT
    ===================================================================================
    */
    $(document).on('click', '.modal_diagnostic_report', function () {
        var id_radiologi = $(this).data('id');

        $('#ModalDiagnosticReport').modal('show');
        $('#NotifikasiDiagnosticReport').html('');
        $('#FormDiagnosticReport').html('Loading...');

        $.ajax({
            type    : 'POST',
            url     : '_Page/Pemeriksaan/FormDiagnosticReport.php',
            data    : {id_radiologi: id_radiologi},
            success : function(data){
                $('#FormDiagnosticReport').html(data);

                // 1. Inisialisasi Quill (seperti sebelumnya)
                let quill;

                if (!quill) {
                    quill = new Quill('#editor-conclusion', { theme: 'snow' });

                    quill.on('text-change', function() {
                        $('#conclusion').val(quill.root.innerHTML);
                    });
                }

                // 2. Inisialisasi Select2 untuk ICD-10
                $('#conclusionCode_coding_code').select2({
                    theme             : 'bootstrap-5',                 // Sesuaikan dengan tema yang kamu pakai
                    placeholder       : 'Cari Kode ICD-10...',
                    minimumInputLength: 3,                             // Minimal 3 huruf baru mencari
                    allowClear        : true,
                    dropdownParent    : $('#FormDiagnosticReport'),   // WAJIB agar tampil di modal
                    ajax: {
                        url     : '_Page/Pemeriksaan/SearchIcd10.php',
                        dataType: 'json',
                        delay   : 250,
                        data    : function (params) {
                            return {
                                keyword: params.term  // Kata kunci yang diketik
                            };
                        },
                        processResults: function (data) {
                            return {
                                results: data // Format data harus [{id: 'A00', text: 'Cholera'}]
                            };
                        },
                        cache: true
                    }
                });
            },
            error: function(xhr){
                $('#FormDiagnosticReport').html(
                    '<div class="alert alert-danger text-center">' +
                    '<small>Gagal memuat form Diagnostic Report</small>' +
                    '</div>'
                );
            }
        });
    });

    $('#ProsesDiagnosticReport').submit(function(e){
        e.preventDefault(); // WAJIB agar tidak submit normal

        var ProsesDiagnosticReport = $('#ProsesDiagnosticReport').serialize();

        $.ajax({
            type    : 'POST',
            url     : '_Page/Pemeriksaan/ProsesDiagnosticReport.php',
            dataType: 'json',
            data    : ProsesDiagnosticReport,

            // 🔒 KUNCI TOMBOL SAAT REQUEST DIMULAI
            beforeSend: function(){
                $('#btnSubmitDiagnosticReport').prop('disabled', true);
                $('#NotifikasiDiagnosticReport').html('Mengirim data...');
            },

            // ✅ RESPONSE BERHASIL DITERIMA (HTTP 200)
            success: function(response){
                var status       = response.status;
                var message      = response.message;
                var id_radiologi = response.id_radiologi;

                if(status === 'success'){
                    $('#NotifikasiDiagnosticReport').html('');
                    $('#ModalDiagnosticReport').modal('hide');

                    ShowDetail(id_radiologi);
                    ShowTablePemeriksaan();

                    Swal.fire(
                        'Success!',
                        'Resource Diagnostic Report Berhasil Dikirim Ke Satu Sehat!',
                        'success'
                    );
                }else{
                    $('#NotifikasiDiagnosticReport').html(
                        '<div class="alert alert-danger"><small>'+message+'</small></div>'
                    );
                }
            },

            // ❌ ERROR TEKNIS (NETWORK / 500 / TIMEOUT)
            error: function(xhr){
                $('#NotifikasiDiagnosticReport').html(
                    '<div class="alert alert-danger">' +
                    '<small>Koneksi ke server gagal</small>' +
                    '</div>'
                );
            },

            // 🔓 AKTIFKAN KEMBALI TOMBOL (SELALU DIEKSEKUSI)
            complete: function(){
                $('#btnSubmitDiagnosticReport').prop('disabled', false);
            }
        });
    });

    $(document).on('click', '.modal_detail_diagnostic_report', function () {

        //tangkap data 'id_diagnostic_report' dan buat variabel
        var id_diagnostic_report   = $(this).data('id');

        //tampilkan modal
        $('#ModalDetailDiagnosticReport').modal('show');

        //Form Loading
        $('#FormDetailDiagnosticReport').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Pemeriksaan/FormDetailDiagnosticReport.php',
            data        : {id_diagnostic_report: id_diagnostic_report},
            success     : function(data){
                $('#FormDetailDiagnosticReport').html(data);
            }
        });
    });


    /*
    ===================================================================================
    ORDER PACS
    ===================================================================================
    */
    $(document).on('click', '.modal_order_pacs', function () {

        //tangkap data 'id_radiologi' dan buat variabel
        var id_radiologi   = $(this).data('id');

        //tampilkan modal
        $('#ModalOrderPacs').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiOrderPacs').html('');

        //Form Loading
        $('#FormOrderPacs').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Pemeriksaan/FormOrderPacs.php',
            data        : {id_radiologi: id_radiologi},
            success     : function(data){
                $('#FormOrderPacs').html(data);
            }
        });
    });

    $('#ProsesOrderPacs').submit(function(e){
       e.preventDefault();
        /* Menangkap data dari form  */
        var ProsesOrderPacs=$('#ProsesOrderPacs').serialize();

        /* Loading Notification */
        $('#NotifikasiOrderPacs').html('loading..');

        /* Kirim data dengan AJAX  */
        $.ajax({
            type    : 'POST',
            url     : '_Page/Pemeriksaan/ProsesOrderPacs.php',
            dataType: 'json',
            data    : ProsesOrderPacs,
            success: function(response) {
                var status       = response.status;
                var message      = response.message;
                var id_radiologi = response.id_radiologi;

                // Apabila berhasil
                if(status=='success'){
                    //Bersihkan notifikasi
                    $('#NotifikasiOrderPacs').html('');

                    //Tutup modal
                    $('#ModalOrderPacs').modal('hide');

                    //reload data detail
                    ShowDetail(id_radiologi);

                    // Reload Tabel Pemeriksaan
                    ShowTablePemeriksaan();

                    // Menampilkan Swal
                    Swal.fire(
                        'Success!',
                        'Pengiriman Order Ke PACS Berhasil!',
                        'success'
                    )
                }else{
                    $('#NotifikasiOrderPacs').html('<div class="alert alert-danger"><small>'+message+'</small></div>');
                }
                
            }
        });
    });

    $(document).on('click', '.modal_detail_pacd', function () {

        //tangkap data 'accession_number' dan buat variabel
        var accession_number   = $(this).data('id');

        //tampilkan modal
        $('#ModalDetailPacs').modal('show');

        //Form Loading
        $('#FormDetailPacs').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Pemeriksaan/FormDetailPacs.php',
            data        : {accession_number: accession_number},
            success     : function(data){
                $('#FormDetailPacs').html(data);
            }
        });
    });

    /*
    ===================================================================================
    HAPUS PERMINTAAN PEMERIKSAAN
    ===================================================================================
    */
    $(document).on('click', '.modal_hapus', function () {

        //tangkap data 'id_radiologi' dan buat variabel
        var id_radiologi   = $(this).data('id');

        //tampilkan modal
        $('#ModalHapus').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiHapus').html('');

        //Form Loading
        $('#FormHapus').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Pemeriksaan/FormHapus.php',
            data        : {id_radiologi: id_radiologi},
            success     : function(data){
                $('#FormHapus').html(data);
            }
        });
    });

    /* Ketika 'ProsesHapus' disubmit */
    $('#ProsesHapus').submit(function(e){
        e.preventDefault(); // Mencegah form submit default
    
        /* Menangkap data dari form  */
        var ProsesHapus = $('#ProsesHapus').serialize();

        /* Loading Notification */
        $('#NotifikasiHapus').html('loading..');

        /* Kirim data dengan AJAX  */
        $.ajax({
            type    : 'POST',
            url     : '_Page/Pemeriksaan/ProsesHapus.php',
            dataType: 'json',
            data    : ProsesHapus,
            success: function(response) {
                console.log('Success Response:', response); // Debug
                
                var status  = response.status;
                var message = response.message || 'Tidak ada pesan';

                // Apabila berhasil
                if(status == 'success'){
                    // Bersihkan notifikasi
                    $('#NotifikasiHapus').html('');

                    // Tutup modal
                    $('#ModalHapus').modal('hide');

                    // reload tabel
                    ShowTablePemeriksaan();

                    // Menampilkan Swal
                    Swal.fire(
                        'Success!',
                        'Hapus Permintaan Pemeriksaan Berhasil!',
                        'success'
                    )
                } else {
                    $('#NotifikasiHapus').html('<div class="alert alert-danger"><small>'+message+'</small></div>');
                }
            },
            error: function(xhr, status, error) {
                console.log('AJAX Error:', error);
                console.log('Status:', status);
                console.log('Response:', xhr.responseText);
                
                var errorMessage = 'Terjadi kesalahan pada server';
                
                try {
                    var response = JSON.parse(xhr.responseText);
                    errorMessage = response.message || errorMessage;
                } catch (e) {
                    // Jika response bukan JSON
                    errorMessage = xhr.responseText || errorMessage;
                }
                
                $('#NotifikasiHapus').html('<div class="alert alert-danger"><small>'+errorMessage+'</small></div>');
            }
        });
    });

    /*
    ===================================================================================
    FAKTOR EKSPOSUR
    ===================================================================================
    */
    $(document).on('click', '.modal_faktor_eksposi', function () {

        //tangkap data 'id_radiologi' dan buat variabel
        var id_radiologi   = $(this).data('id');

        //tampilkan modal
        $('#ModalEksposur').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiEksposur').html('');

        //Form Loading
        $('#FormEksposur').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Pemeriksaan/FormEksposur.php',
            data        : {id_radiologi: id_radiologi},
            success     : function(data){
                $('#FormEksposur').html(data);
            }
        });
    });

    $('#ProsesEksposur').submit(function(e){
        e.preventDefault();

        // =============================
        // AMBIL DATA FORM
        // =============================
        var ProsesEksposur = $(this).serialize();

        $('#NotifikasiEksposur').html(
            '<small class="text-muted">Menyimpan data...</small>'
        );

        // =============================
        // AJAX REQUEST
        // =============================
        $.ajax({
            type     : 'POST',
            url      : '_Page/Pemeriksaan/ProsesEksposur.php',
            dataType : 'json',
            data     : ProsesEksposur,

            success: function(response){

                var status  = response.status;
                var message = response.message || 'Proses berhasil';

                if(status === 'success'){

                    // Bersihkan notifikasi
                    $('#NotifikasiEksposur').html('');

                    // Tutup modal jika ada
                    $('#ModalEksposur').modal('hide');

                    // Reload detail pemeriksaan
                    ShowDetailPemeriksaan();

                    // =============================
                    // SET PESAN TOAST
                    // =============================
                    $('#put_message').html(
                        '<i class="bi bi-check-circle me-2"></i> ' + message
                    );

                    // =============================
                    // TAMPILKAN TOAST
                    // =============================
                    var toastEl = document.getElementById('toast_proses');
                    var toast   = new bootstrap.Toast(toastEl, {
                        delay: 3000
                    });
                    toast.show();

                } else {

                    $('#NotifikasiEksposur').html(
                        '<div class="alert alert-danger"><small>'+message+'</small></div>'
                    );
                }
            },

            error: function(xhr){
                console.log(xhr.responseText);

                $('#NotifikasiEksposur').html(
                    '<div class="alert alert-danger"><small>Terjadi kesalahan sistem</small></div>'
                );
            }
        });

    });

    /*
    ===================================================================================
    EDIT RESOURCE SATU SEHAT
    ===================================================================================
    */
    $(document).on('click', '.modal_edit_resource_satu_sehat', function () {
    
        //tangkap data 'id_radiologi' dan buat variabel
        var id_radiologi = $(this).data('id');
        
        //tampilkan modal
        $('#ModalEditResourceSatuSehat').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiEditResourceSatuSehat').html('');
        
        //Form Loading dengan spinner
        $('#FormEditResourceSatuSehat').html(`
            <div class="d-flex justify-content-center align-items-center h-100">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        `);
        
        //Tampilkan Form Dengan Ajax
        $.ajax({
            type: 'POST',
            url: '_Page/Pemeriksaan/FormEditResourceSatuSehat.php',
            data: {id_radiologi: id_radiologi},
            success: function(data) {
                $('#FormEditResourceSatuSehat').html(data);
            }
        });
    });

    $('#ProsesEditResourceSatuSehat').submit(function(e){
        e.preventDefault();
        var ProsesEditResourceSatuSehat = $(this).serialize();

        // Loading Notifikasi
        $('#NotifikasiEditResourceSatuSehat').html('<small class="text-muted">Menyimpan data...</small>');

       // Simpan Data Dengan AJAX
        $.ajax({
            type     : 'POST',
            url      : '_Page/Pemeriksaan/ProsesEditResourceSatuSehat.php',
            dataType : 'json',
            data     : ProsesEditResourceSatuSehat,

            success: function(response){

                var status  = response.status;
                var message = response.message || 'Proses berhasil';

                if(status === 'success'){

                    // Bersihkan notifikasi
                    $('#NotifikasiEditResourceSatuSehat').html('');

                    // Tutup modal jika ada
                    $('#ModalEditResourceSatuSehat').modal('hide');

                    // Reload detail pemeriksaan
                    ShowDetailPemeriksaan();
                    ShowTablePemeriksaan();

                    // Tampilkan Toast
                    $('#put_message').html(
                        '<i class="bi bi-check-circle me-2"></i> ' + message
                    );
                    var toastEl = document.getElementById('toast_proses');
                    var toast   = new bootstrap.Toast(toastEl, {
                        delay: 3000
                    });
                    toast.show();

                } else {
                    $('#NotifikasiEditResourceSatuSehat').html(
                        '<div class="alert alert-danger"><small>'+message+'</small></div>'
                    );
                }
            },

            error: function(xhr){
                console.log(xhr.responseText);

                $('#NotifikasiEksposur').html(
                    '<div class="alert alert-danger"><small>Terjadi kesalahan sistem</small></div>'
                );
            }
        });

    });

     /*
    ===================================================================================
    HAPUS RESOURCE SATU SEHAT
    ===================================================================================
    */
    $(document).on('click', '.modal_hapus_satu_sehat', function () {
    
        //tangkap data 'id_radiologi' dan buat variabel
        var resource     = $(this).data('resource');
        var id           = $(this).data('id');
        var id_radiologi = $(this).data('id_rad');
        
        //tampilkan modal
        $('#ModalHapusResourceSatuSehat').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiHapusResourceSatuSehat').html('');
        
        //Form Loading dengan spinner
        $('#FormHapusResourceSatuSehat').html(`
            <div class="d-flex justify-content-center align-items-center h-100">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        `);
        
        //Tampilkan Form Dengan Ajax
        $.ajax({
            type: 'POST',
            url: '_Page/Pemeriksaan/FormHapusResourceSatuSehat.php',
            data: {resource: resource, id: id, id_radiologi: id_radiologi},
            success: function(data) {
                $('#FormHapusResourceSatuSehat').html(data);
            }
        });
    });

    $('#ProsesHapusResourceSatuSehat').submit(function(e){
        e.preventDefault();
        var ProsesHapusResourceSatuSehat = $(this).serialize();

        // Loading Notifikasi
        $('#NotifikasiHapusResourceSatuSehat').html('<small class="text-muted">Menyimpan data...</small>');

       // Simpan Data Dengan AJAX
        $.ajax({
            type     : 'POST',
            url      : '_Page/Pemeriksaan/ProsesHapusResourceSatuSehat.php',
            dataType : 'json',
            data     : ProsesHapusResourceSatuSehat,

            success: function(response){

                var status  = response.status;
                var message = response.message || 'Proses berhasil';

                if(status === 'success'){

                    // Bersihkan notifikasi
                    $('#NotifikasiHapusResourceSatuSehat').html('');

                    // Tutup modal jika ada
                    $('#ModalHapusResourceSatuSehat').modal('hide');

                    // Reload detail pemeriksaan
                    ShowDetailPemeriksaan();
                    ShowTablePemeriksaan();

                    // Tampilkan Toast
                    $('#put_message').html(
                        '<i class="bi bi-check-circle me-2"></i> ' + message
                    );
                    var toastEl = document.getElementById('toast_proses');
                    var toast   = new bootstrap.Toast(toastEl, {
                        delay: 3000
                    });
                    toast.show();

                } else {
                    $('#NotifikasiHapusResourceSatuSehat').html(
                        '<div class="alert alert-danger"><small>'+message+'</small></div>'
                    );
                }
            },

            error: function(xhr){
                console.log(xhr.responseText);

                $('#NotifikasiHapusResourceSatuSehat').html(
                    '<div class="alert alert-danger"><small>Terjadi kesalahan sistem</small></div>'
                );
            }
        });

    });

    /*
    ===================================================================================
    TAGIHAN (INVOICE)
    ===================================================================================
    */

    // Modal Tambah Tagihan 'modal_tambah_tagihan'
    $(document).on('click', '.modal_tambah_tagihan', function () {

        //tangkap data 'id_radiologi' dan buat variabel
        var id_radiologi   = $(this).data('id');

        //tampilkan modal
        $('#ModalTambahTagihan').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiTambahTagihan').html('');

        //Form Loading
        $('#FormTambahTagihan').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Pemeriksaan/FormTambahTagihan.php',
            data        : {id_radiologi: id_radiologi},
            success     : function(data){
                $('#FormTambahTagihan').html(data);
                initializeMoneyInputs();
            }
        });
    });

    // Listener perubahan input
    $(document).on('input', '#base_price, #doctor_fee, #radiographers_fee, #facility_fee, #equipment_fee, #quantity, #amount', 
        function () {
            hitungTotalHarga();
        }
    );

    // Ketika kode_tarif diubah
    $(document).on('change', '#kode_tarif', function(){
        var kode_tarif =$('#kode_tarif').val();
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Pemeriksaan/get_master_service_prices.php',
            data        : {kode_tarif: kode_tarif},
            dataType    : 'json',
            success     : function(response){
                // Validasi Response
                var status = response.status;
                if(status=="success"){
                    var service_name      = response.metadata.service_name;
                    var service_category  = response.metadata.service_category;
                    var modality          = response.metadata.modality;
                    var patient_class     = response.metadata.patient_class;
                    var insurance_type    = response.metadata.insurance_type;
                    var base_price        = response.metadata.base_price;
                    var doctor_fee        = response.metadata.doctor_fee;
                    var radiographers_fee = response.metadata.radiographers_fee;
                    var facility_fee      = response.metadata.facility_fee;
                    var equipment_fee     = response.metadata.equipment_fee;
                    var total_price       = response.metadata.total_price;

                    // Tempelkan Ke Form
                    $('#service_name').val(service_name);
                    $('#service_category').val(service_category);
                    $('#modality').val(modality);
                    $('#patient_class').val(patient_class);
                    $('#insurance_type').val(insurance_type);
                    $('#base_price').val(base_price);
                    $('#doctor_fee').val(doctor_fee);
                    $('#radiographers_fee').val(radiographers_fee);
                    $('#facility_fee').val(facility_fee);
                    $('#equipment_fee').val(equipment_fee);
                    $('#total_price').val(total_price);

                    hitungTotalHarga();
                    initializeMoneyInputs();
                }
                
            }
        });
    });

    // Proses Tambah Tagihan 
    $('#ProsesTambahTagihan').submit(function(e){
        e.preventDefault();
        
        // Ambil Data Dari form
        var ProsesTambahTagihan = $(this).serialize();

        //Loading Notifikasi
        $('#NotifikasiTambahTagihan').html('<small class="text-muted">Menyimpan data...</small>');

        // Ajax Request
        $.ajax({
            type     : 'POST',
            url      : '_Page/Pemeriksaan/ProsesTambahTagihan.php',
            dataType : 'json',
            data     : ProsesTambahTagihan,

            success: function(response){

                var status  = response.status;
                var message = response.message || 'Proses berhasil';

                if(status === 'success'){

                    // Bersihkan notifikasi
                    $('#NotifikasiTambahTagihan').html('');

                    // Tutup modal jika ada
                    $('#ModalTambahTagihan').modal('hide');

                    // Reload detail pemeriksaan
                    ShowDetailPemeriksaan();

                    // Toast Proses Berhasil
                    $('#put_message').html('<i class="bi bi-check-circle me-2"></i> ' + message);

                    // Tampilkan Toast
                    var toastEl = document.getElementById('toast_proses');
                    var toast   = new bootstrap.Toast(toastEl, {delay: 3000});
                    toast.show();

                } else {
                    // Tampilkan Pesan Kesalahan
                    $('#NotifikasiTambahTagihan').html(
                        '<div class="alert alert-danger"><small>'+message+'</small></div>'
                    );
                }
            },

            error: function(xhr){
                console.log(xhr.responseText);

                $('#NotifikasiTambahTagihan').html(
                    '<div class="alert alert-danger"><small>Terjadi kesalahan sistem</small></div>'
                );
            }
        });
    });

    // Modal Cetak Nota Tagihan 'modal_cetak_tagihan'
    $(document).on('click', '.modal_cetak_tagihan', function () {

        //tangkap data 'id_radiologi' dan buat variabel
        var id_radiologi   = $(this).data('id');

        //tampilkan modal
        $('#ModalCetakNota').modal('show');

        //Form Loading
        $('#FormCetakNota').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Pemeriksaan/FormCetakNota.php',
            data        : {id_radiologi: id_radiologi},
            success     : function(data){
                $('#FormCetakNota').html(data);
            }
        });
    });

    // Ketika Modal Edit Tagihan Muncul
    $(document).on('click', '.modal_edit_nota', function () {

        //tangkap data 'id_radiologi_invoice' dan buat variabel
        var id_radiologi_invoice   = $(this).data('id');

        //tampilkan modal
        $('#ModalEditTagihan').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiEditTagihan').html('');

        //Form Loading
        $('#FormEditTagihan').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Pemeriksaan/FormEditTagihan.php',
            data        : {id_radiologi_invoice: id_radiologi_invoice},
            success     : function(data){
                $('#FormEditTagihan').html(data);
                initializeMoneyInputs();
            }
        });
    });

    $(document).on('input', '#total_price_edit, #quantity_edit', 
        function () {
            hitungTotalHargaEdit();
        }
    );

    //Proses Edit Tahihan
    $('#ProsesEditTagihan').submit(function(e){
        e.preventDefault();
        
        // Ambil Data Dari form
        var ProsesEditTagihan = $(this).serialize();

        //Loading Notifikasi
        $('#NotifikasiEditTagihan').html('<small class="text-muted">Menyimpan data...</small>');

        // Ajax Request
        $.ajax({
            type     : 'POST',
            url      : '_Page/Pemeriksaan/ProsesEditTagihan.php',
            dataType : 'json',
            data     : ProsesEditTagihan,

            success: function(response){

                var status  = response.status;
                var message = response.message || 'Proses berhasil';

                if(status === 'success'){

                    // Bersihkan notifikasi
                    $('#NotifikasiEditTagihan').html('');

                    // Tutup modal jika ada
                    $('#ModalEditTagihan').modal('hide');

                    // Reload detail pemeriksaan
                    ShowDetailPemeriksaan();

                    // Toast Proses Berhasil
                    $('#put_message').html('<i class="bi bi-check-circle me-2"></i> ' + message);

                    // Tampilkan Toast
                    var toastEl = document.getElementById('toast_proses');
                    var toast   = new bootstrap.Toast(toastEl, {delay: 3000});
                    toast.show();

                } else {
                    // Tampilkan Pesan Kesalahan
                    $('#NotifikasiEditTagihan').html(
                        '<div class="alert alert-danger"><small>'+message+'</small></div>'
                    );
                }
            },

            error: function(xhr){
                console.log(xhr.responseText);

                $('#NotifikasiEditTagihan').html(
                    '<div class="alert alert-danger"><small>Terjadi kesalahan sistem</small></div>'
                );
            }
        });
    });

    $(document).on('click', '.modal_hapus_nota', function () {

        //tangkap data 'id_radiologi_invoice' dan buat variabel
        var id_radiologi_invoice   = $(this).data('id');

        //tampilkan modal
        $('#ModalHapusNota').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiTambahTagihan').html('');

        //Form Loading
        $('#FormHapusNota').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Pemeriksaan/FormHapusNota.php',
            data        : {id_radiologi_invoice: id_radiologi_invoice},
            success     : function(data){
                $('#FormHapusNota').html(data);
            }
        });
    });

    $('#ProsesHapusNota').submit(function(e){
        e.preventDefault();
        
        // Ambil Data Dari form
        var ProsesHapusNota = $(this).serialize();

        //Loading Notifikasi
        $('#NotifikasiHapusNota').html('<small class="text-muted">Menyimpan data...</small>');

        // Ajax Request
        $.ajax({
            type     : 'POST',
            url      : '_Page/Pemeriksaan/ProsesHapusNota.php',
            dataType : 'json',
            data     : ProsesHapusNota,

            success: function(response){

                var status  = response.status;
                var message = response.message || 'Proses berhasil';

                if(status === 'success'){

                    // Bersihkan notifikasi
                    $('#NotifikasiHapusNota').html('');

                    // Tutup modal jika ada
                    $('#ModalHapusNota').modal('hide');

                    // Reload detail pemeriksaan
                    ShowDetailPemeriksaan();

                    // Toast Proses Berhasil
                    $('#put_message').html('<i class="bi bi-check-circle me-2"></i> ' + message);

                    // Tampilkan Toast
                    var toastEl = document.getElementById('toast_proses');
                    var toast   = new bootstrap.Toast(toastEl, {delay: 3000});
                    toast.show();

                } else {
                    // Tampilkan Pesan Kesalahan
                    $('#NotifikasiHapusNota').html(
                        '<div class="alert alert-danger"><small>'+message+'</small></div>'
                    );
                }
            },

            error: function(xhr){
                console.log(xhr.responseText);

                $('#NotifikasiHapusNota').html(
                    '<div class="alert alert-danger"><small>Terjadi kesalahan sistem</small></div>'
                );
            }
        });
    });

    /*
    ===================================================================================
    ASSESMENT (QuestionnaireResponse)
    ===================================================================================
    */

    // Modal Tambah Tagihan 'modal_tambah_tagihan'
    $(document).on('click', '.ModalQuestionnaireResponse', function () {

        //tangkap data 'id_radiologi' dan 'id_question'
        var id_radiologi = $(this).data('id_radiologi');
        var id_question  = $(this).data('id_question');

        //tampilkan modal
        $('#ModalQuestionnaireResponse').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiQuestionnaireResponse').html('');

        //Form Loading
        $('#FormQuestionnaireResponse').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Pemeriksaan/FormQuestionnaireResponse.php',
            data        : {id_radiologi: id_radiologi, id_question: id_question},
            success     : function(data){
                $('#FormQuestionnaireResponse').html(data);
            }
        });
    });

    // Proses Kirim Jawaban
    $('#ProsesQuestionnaireResponse').submit(function(e){
        e.preventDefault();
        
        // Ambil Data Dari form
        var ProsesQuestionnaireResponse = $(this).serialize();

        //Loading Notifikasi
        $('#NotifikasiQuestionnaireResponse').html('<small class="text-muted">Menyimpan data...</small>');

        // Ajax Request
        $.ajax({
            type     : 'POST',
            url      : '_Page/Pemeriksaan/ProsesQuestionnaireResponse.php',
            dataType : 'json',
            data     : ProsesQuestionnaireResponse,

            success: function(response){

                var status  = response.status;
                var message = response.message || 'Proses berhasil';

                if(status === 'success'){

                    // Bersihkan notifikasi
                    $('#NotifikasiQuestionnaireResponse').html('');

                    // Tutup modal jika ada
                    $('#ModalQuestionnaireResponse').modal('hide');

                    // Reload detail pemeriksaan
                    ShowDetailPemeriksaan();

                    // Toast Proses Berhasil
                    $('#put_message').html('<i class="bi bi-check-circle me-2"></i> ' + message);

                    // Tampilkan Toast
                    var toastEl = document.getElementById('toast_proses');
                    var toast   = new bootstrap.Toast(toastEl, {delay: 3000});
                    toast.show();

                } else {
                    // Tampilkan Pesan Kesalahan
                    $('#NotifikasiQuestionnaireResponse').html(
                        '<div class="alert alert-danger"><small>'+message+'</small></div>'
                    );
                }
            },

            error: function(xhr){
                console.log(xhr.responseText);

                $('#NotifikasiQuestionnaireResponse').html(
                    '<div class="alert alert-danger"><small>Terjadi kesalahan sistem</small></div>'
                );
            }
        });
    });

    /*
    ===================================================================================
    FILE MANUAL
    ===================================================================================
    */
    // Modal Upload Radiologi 'modal_tambah_tagihan'
    $(document).on('click', '.modal_upload_file', function () {

        //tangkap data 'id_radiologi'
        var id_radiologi = $(this).data('id');
        
        //tampilkan modal
        $('#ModalUploadFile').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiUploadFile').html('');

        //Form Loading
        $('#FormUploadFile').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Pemeriksaan/FormUploadFile.php',
            data        : {id_radiologi: id_radiologi},
            success     : function(data){
                $('#FormUploadFile').html(data);
            }
        });
    });

    // Proses Upload File
    $('#ProsesUploadFile').submit(function(e){
        e.preventDefault();

        // Gunakan FormData
        var formData = new FormData(this);

        // Loading Notifikasi
        $('#NotifikasiUploadFile').html('<small class="text-muted">Menyimpan data...</small>');

        $.ajax({
            type        : 'POST',
            url         : '_Page/Pemeriksaan/ProsesUploadFile.php',
            data        : formData,
            dataType    : 'json',
            processData : false, // WAJIB
            contentType : false, // WAJIB
            cache       : false,

            success: function(response){

                var status  = response.status;
                var message = response.message || 'Proses berhasil';

                if(status === 'success'){

                    $('#NotifikasiUploadFile').html('');
                    $('#ModalUploadFile').modal('hide');

                    // Reload detail pemeriksaan
                    ShowDetailPemeriksaan();

                    // Toast
                    $('#put_message').html('<i class="bi bi-check-circle me-2"></i> ' + message);
                    var toastEl = document.getElementById('toast_proses');
                    var toast   = new bootstrap.Toast(toastEl, {delay: 3000});
                    toast.show();

                } else {
                    $('#NotifikasiUploadFile').html(
                        '<div class="alert alert-danger"><small>'+message+'</small></div>'
                    );
                }
            },

            error: function(xhr){
                console.log(xhr.responseText);
                $('#NotifikasiUploadFile').html(
                    '<div class="alert alert-danger"><small>Terjadi kesalahan sistem</small></div>'
                );
            }
        });
    });

    // Modal Detail File 'modal_detail_file'
    $(document).on('click', '.modal_detail_file', function () {

        //tangkap data 'id_radiologi_file'
        var id_radiologi_file = $(this).data('id');
        
        //tampilkan modal
        $('#ModalDetailFile').modal('show');

        //Form Loading
        $('#FormDetailFile').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Pemeriksaan/FormDetailFile.php',
            data        : {id_radiologi_file: id_radiologi_file},
            success     : function(data){
                $('#FormDetailFile').html(data);
            }
        });
    });

    // Modal Hapus File 'ModalHapusFile'
    $(document).on('click', '.modal_hapus_file', function () {

        //tangkap data 'id_radiologi_file'
        var id_radiologi_file = $(this).data('id');
        
        //tampilkan modal
        $('#ModalHapusFile').modal('show');

        //Form Loading
        $('#FormHapusFile').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Pemeriksaan/FormHapusFile.php',
            data        : {id_radiologi_file: id_radiologi_file},
            success     : function(data){
                $('#FormHapusFile').html(data);
            }
        });
    });

    // Proses Hapus File
    $('#ProsesHapusFile').submit(function(e){
        e.preventDefault();
        
        // Ambil Data Dari form
        var ProsesHapusFile = $(this).serialize();

        //Loading Notifikasi
        $('#NotifikasiHapusFile').html('<small class="text-muted">Menyimpan data...</small>');

        // Ajax Request
        $.ajax({
            type     : 'POST',
            url      : '_Page/Pemeriksaan/ProsesHapusFile.php',
            dataType : 'json',
            data     : ProsesHapusFile,

            success: function(response){

                var status  = response.status;
                var message = response.message || 'Proses berhasil';

                if(status === 'success'){

                    // Bersihkan notifikasi
                    $('#NotifikasiHapusFile').html('');

                    // Tutup modal jika ada
                    $('#ModalHapusFile').modal('hide');

                    // Reload detail pemeriksaan
                    ShowDetailPemeriksaan();

                    // Toast Proses Berhasil
                    $('#put_message').html('<i class="bi bi-check-circle me-2"></i> ' + message);

                    // Tampilkan Toast
                    var toastEl = document.getElementById('toast_proses');
                    var toast   = new bootstrap.Toast(toastEl, {delay: 3000});
                    toast.show();

                } else {
                    // Tampilkan Pesan Kesalahan
                    $('#NotifikasiHapusFile').html(
                        '<div class="alert alert-danger"><small>'+message+'</small></div>'
                    );
                }
            },

            error: function(xhr){
                console.log(xhr.responseText);

                $('#NotifikasiHapusFile').html(
                    '<div class="alert alert-danger"><small>Terjadi kesalahan sistem</small></div>'
                );
            }
        });
    });

    /*
    ===================================================================================
    EXPERTISE LOCAL
    ===================================================================================
    */
    // Modal Expertise
    $(document).on('click', '.modal_expertise_multiple', function () {

        //Tangkap ID
        var id_radiologi = $(this).data('id');

        //Kosongkan Notifikasi
        $('#NotifikasiExpertiseMultiple').html('');

        // Tampilkan Modal
        $('#ModalExpertiseMultiple').modal('show');

        // Tampilkan Form
        $('#FormExpertiseMultiple').html('Loading...');
        $.ajax({
            type  : 'POST',
            url   : '_Page/Pemeriksaan/FormExpertiseMultiple.php',
            data  : {id_radiologi: id_radiologi},
            success: function(data){
                $('#FormExpertiseMultiple').html(data);

                // =============================
                // INIT QUILL
                // =============================
                window.quillExpertiseTemuan = new Quill('#editor_expertise_temuan', {
                    theme: 'snow',
                    placeholder: 'Tulis temusn di sini...',
                    modules: {
                        toolbar: [
                            [{ header: [1, 2, 3, false] }],
                            ['bold', 'italic', 'underline'],
                            [{ list: 'ordered' }, { list: 'bullet' }],
                            [{ align: [] }],
                            ['clean']
                        ]
                    }
                });

                window.quillExpertiseKesan = new Quill('#editor_expertise_kesan', {
                    theme: 'snow',
                    placeholder: 'Tulis kesan di sini...',
                    modules: {
                        toolbar: [
                            [{ header: [1, 2, 3, false] }],
                            ['bold', 'italic', 'underline'],
                            [{ list: 'ordered' }, { list: 'bullet' }],
                            [{ align: [] }],
                            ['clean']
                        ]
                    }
                });

                window.quillExpertiseSaran = new Quill('#editor_expertise_saran', {
                    theme: 'snow',
                    placeholder: 'Tulis Saran di sini...',
                    modules: {
                        toolbar: [
                            [{ header: [1, 2, 3, false] }],
                            ['bold', 'italic', 'underline'],
                            [{ list: 'ordered' }, { list: 'bullet' }],
                            [{ align: [] }],
                            ['clean']
                        ]
                    }
                });

                window.quillExpertiseCatatan = new Quill('#editor_expertise_catatan', {
                    theme: 'snow',
                    placeholder: 'Tulis Catatan/Keterangan lain di sini...',
                    modules: {
                        toolbar: [
                            [{ header: [1, 2, 3, false] }],
                            ['bold', 'italic', 'underline'],
                            [{ list: 'ordered' }, { list: 'bullet' }],
                            [{ align: [] }],
                            ['clean']
                        ]
                    }
                });
            }
        });
    });

    $('#ProsesExpertiseMultiple').submit(function(e){
        e.preventDefault();

        // Ambil isi quill → masukkan ke hidden input
        if (window.quillExpertiseTemuan) {
            $('#expertise_temuan').val(window.quillExpertiseTemuan.root.innerHTML);
        }
        if (window.quillExpertiseKesan) {
            $('#expertise_kesan').val(window.quillExpertiseKesan.root.innerHTML);
        }
        if (window.quillExpertiseSaran) {
            $('#expertise_saran').val(window.quillExpertiseSaran.root.innerHTML);
        }
        if (window.quillExpertiseCatatan) {
            $('#expertise_catatan').val(window.quillExpertiseCatatan.root.innerHTML);
        }

        var formData = $(this).serialize();

        $('#NotifikasiExpertiseMultiple').html('<small class="text-muted">Menyimpan data...</small>');

        $.ajax({
            type     : 'POST',
            url      : '_Page/Pemeriksaan/ProsesExpertiseMultiple.php',
            dataType : 'json',
            data     : formData,
            success  : function(response){
                var status  = response.status;
                var message = response.message || 'Proses berhasil';

                if(status === 'success'){

                    // Bersihkan notifikasi
                    $('#NotifikasiExpertiseMultiple').html('');

                    // Tutup modal jika ada
                    $('#ModalExpertiseMultiple').modal('hide');

                    // Tampilkan Ulang Tabel
                    ShowTablePemeriksaan();

                    // Toast Proses Berhasil
                    $('#put_message').html('<i class="bi bi-check-circle me-2"></i> ' + message);

                    // Tampilkan Toast
                    var toastEl = document.getElementById('toast_proses');
                    var toast   = new bootstrap.Toast(toastEl, {delay: 3000});
                    toast.show();

                } else {
                    // Tampilkan Pesan Kesalahan
                    $('#NotifikasiExpertiseMultiple').html(
                        '<div class="alert alert-danger"><small>'+message+'</small></div>'
                    );
                }
            },
            error: function(xhr){
                console.log(xhr.responseText);

                $('#NotifikasiExpertiseMultiple').html(
                    '<div class="alert alert-danger"><small>Terjadi kesalahan sistem</small></div>'
                );
            }
        });
    });

    // Modal Pengisian Expertise
    $(document).on('click', '.modal_expertise', function () {

        var id_radiologi = $(this).data('id_radiologi');
        var title        = $(this).data('title');

        $('#ModalExpertise').modal('show');
        $('#FormExpertise').html('Loading...');

        $.ajax({
            type  : 'POST',
            url   : '_Page/Pemeriksaan/FormExpertise.php',
            data  : {id_radiologi: id_radiologi, title: title},
            success: function(data){
                $('#FormExpertise').html(data);

                // =============================
                // INIT QUILL
                // =============================
                window.quillExpertise = new Quill('#editor_expertise', {
                    theme: 'snow',
                    placeholder: 'Tulis '+title+' di sini...',
                    modules: {
                        toolbar: [
                            [{ header: [1, 2, 3, false] }],
                            ['bold', 'italic', 'underline'],
                            [{ list: 'ordered' }, { list: 'bullet' }],
                            [{ align: [] }],
                            ['clean']
                        ]
                    }
                });
            }
        });
    });

    $('#ProsesExpertise').submit(function(e){
        e.preventDefault();

        // Ambil isi quill → masukkan ke hidden input
        if (window.quillExpertise) {
            $('#isi_expertise').val(window.quillExpertise.root.innerHTML);
        }

        var formData = $(this).serialize();

        $('#NotifikasiExpertise').html('<small class="text-muted">Menyimpan data...</small>');

        $.ajax({
            type     : 'POST',
            url      : '_Page/Pemeriksaan/ProsesExpertise.php',
            dataType : 'json',
            data     : formData,
            success  : function(response){
                var status  = response.status;
                var message = response.message || 'Proses berhasil';

                if(status === 'success'){

                    // Bersihkan notifikasi
                    $('#NotifikasiExpertise').html('');

                    // Tutup modal jika ada
                    $('#ModalExpertise').modal('hide');

                    // Reload detail pemeriksaan
                    ShowDetailPemeriksaan();

                    // Tampilkan Ulang Tabel
                    ShowTablePemeriksaan();

                    // Toast Proses Berhasil
                    $('#put_message').html('<i class="bi bi-check-circle me-2"></i> ' + message);

                    // Tampilkan Toast
                    var toastEl = document.getElementById('toast_proses');
                    var toast   = new bootstrap.Toast(toastEl, {delay: 3000});
                    toast.show();

                } else {
                    // Tampilkan Pesan Kesalahan
                    $('#NotifikasiExpertise').html(
                        '<div class="alert alert-danger"><small>'+message+'</small></div>'
                    );
                }
            },
            error: function(xhr){
                console.log(xhr.responseText);

                $('#NotifikasiExpertise').html(
                    '<div class="alert alert-danger"><small>Terjadi kesalahan sistem</small></div>'
                );
            }
        });
    });

    /*
    ===================================================================================
    EXPERTISE PACS
    ===================================================================================
    */
    // Modal Expertise APCS
    $(document).on('click', '.modal_detail_exp_pacs', function () {

        var id_radiologi_expertise = $(this).data('id');
        var modality               = $(this).data('modality');

        $('#ModalExpertisePacs').modal('show');
        $('#FormDetailExpertisePacs').html('Loading...');

        $.ajax({
            type  : 'POST',
            url   : '_Page/Pemeriksaan/FormDetailExpertisePacs.php',
            data  : {id_radiologi_expertise: id_radiologi_expertise, modality: modality},
            success: function(data){
                $('#FormDetailExpertisePacs').html(data);
            }
        });
    });

    // Modal Hapus Expertise PACS
    $(document).on('click', '.modal_hapus_exp_pacs', function () {

        var id_radiologi_expertise = $(this).data('id');
        var modality               = $(this).data('modality');

        // Kosongkan Notifikasi
        $('#NotifikasiHapusExpertisePacs').html('');

        // Tampilkan Modal
        $('#ModalHapusExpertisePacs').modal('show');

        // Tampilkan Loading
        $('#FormHapusExpertisePacs').html('Loading...');

        $.ajax({
            type  : 'POST',
            url   : '_Page/Pemeriksaan/FormHapusExpertisePacs.php',
            data  : {id_radiologi_expertise: id_radiologi_expertise, modality: modality},
            success: function(data){
                $('#FormHapusExpertisePacs').html(data);
            }
        });
    });

    // Proses Hapus Data Expertise Dari PACS
    $('#ProsesHapusExpertisePacs').submit(function(e){
        e.preventDefault();
        
        // Ambil Data Dari form
        var ProsesHapusExpertisePacs = $(this).serialize();

        //Loading Notifikasi
        $('#NotifikasiHapusExpertisePacs').html('<small class="text-muted">Menyimpan data...</small>');

        // Ajax Request
        $.ajax({
            type     : 'POST',
            url      : '_Page/Pemeriksaan/ProsesHapusExpertisePacs.php',
            dataType : 'json',
            data     : ProsesHapusExpertisePacs,

            success: function(response){

                var status  = response.status;
                var message = response.message || 'Proses berhasil';

                if(status === 'success'){

                    // Bersihkan notifikasi
                    $('#NotifikasiHapusExpertisePacs').html('');

                    // Tutup modal jika ada
                    $('#ModalHapusExpertisePacs').modal('hide');

                    // Reload detail pemeriksaan
                    ShowDetailPemeriksaan();

                    // Toast Proses Berhasil
                    $('#put_message').html('<i class="bi bi-check-circle me-2"></i> ' + message);

                    // Tampilkan Toast
                    var toastEl = document.getElementById('toast_proses');
                    var toast   = new bootstrap.Toast(toastEl, {delay: 3000});
                    toast.show();

                } else {
                    // Tampilkan Pesan Kesalahan
                    $('#NotifikasiHapusExpertisePacs').html(
                        '<div class="alert alert-danger"><small>'+message+'</small></div>'
                    );
                }
            },

            error: function(xhr){
                console.log(xhr.responseText);

                $('#NotifikasiHapusExpertisePacs').html(
                    '<div class="alert alert-danger"><small>Terjadi kesalahan sistem</small></div>'
                );
            }
        });
    });

    /*
    ===================================================================================
    WAKTU PELAYANAN
    ===================================================================================
    */

    // Modal Ubah Waktu Pelayanan
    $(document).on('click', '.modal_ubah_waktu_pelayanan', function () {

        var kolom        = $(this).data('kolom');
        var id_radiologi = $(this).data('id');

        // Kosongkan Notifikasi
        $('#NotifikasiWaktuPelayanan').html('');

        // Tampilkan Modal
        $('#ModalWaktuPelayanan').modal('show');

        // Tampilkan Loading
        $('#FormWaktuPelayanan').html('Loading...');

        $.ajax({
            type  : 'POST',
            url   : '_Page/Pemeriksaan/FormWaktuPelayanan.php',
            data  : {kolom: kolom, id_radiologi: id_radiologi},
            success: function(data){
                $('#FormWaktuPelayanan').html(data);
            }
        });
    });

    // Proses Update Waktu Pelayanan
    $('#ProsesWaktuPelayanan').submit(function(e){
        e.preventDefault();
        var formData = $(this).serialize();
        $('#NotifikasiWaktuPelayanan').html('<small class="text-muted">Menyimpan data...</small>');

        $.ajax({
            type     : 'POST',
            url      : '_Page/Pemeriksaan/ProsesWaktuPelayanan.php',
            dataType : 'json',
            data     : formData,
            success  : function(response){
                var status  = response.status;
                var message = response.message || 'Proses berhasil';

                if(status === 'success'){

                    // Bersihkan notifikasi
                    $('#NotifikasiWaktuPelayanan').html('');

                    // Tutup modal jika ada
                    $('#ModalWaktuPelayanan').modal('hide');

                    // Reload detail pemeriksaan
                    ShowDetailPemeriksaan();

                    // Tampilkan Ulang Tabel
                    ShowTablePemeriksaan();

                    // Toast Proses Berhasil
                    $('#put_message').html('<i class="bi bi-check-circle me-2"></i> ' + message);

                    // Tampilkan Toast
                    var toastEl = document.getElementById('toast_proses');
                    var toast   = new bootstrap.Toast(toastEl, {delay: 3000});
                    toast.show();

                } else {
                    // Tampilkan Pesan Kesalahan
                    $('#NotifikasiWaktuPelayanan').html(
                        '<div class="alert alert-danger"><small>'+message+'</small></div>'
                    );
                }
            },
            error: function(xhr){
                console.log(xhr.responseText);

                $('#NotifikasiWaktuPelayanan').html(
                    '<div class="alert alert-danger"><small>Terjadi kesalahan sistem</small></div>'
                );
            }
        });
    });

    /*
    ===================================================================================
    CETAK HASIL
    ===================================================================================
    */
    $(document).on('click', '.modal_cetak_laporan', function () {

        var id_radiologi = $(this).data('id');

        // Tampilkan Modal
        $('#ModalCetakHasil').modal('show');

        // Tampilkan Loading
        $('#FormCetakHasil').html('Loading...');

        $.ajax({
            type  : 'POST',
            url   : '_Page/Pemeriksaan/FormCetakHasil.php',
            data  : {id_radiologi: id_radiologi},
            success: function(data){
                $('#FormCetakHasil').html(data);
            }
        });
    });

    $(document).on('click', '.modal_cetak_laporan2', function () {

        var ProsesDetail = $('#ProsesDetail').serialize();

        // Tampilkan Modal
        $('#ModalCetakHasil').modal('show');

        // Tampilkan Loading
        $('#FormCetakHasil').html('Loading...');

        $.ajax({
            type  : 'POST',
            url   : '_Page/Pemeriksaan/FormCetakHasil.php',
            data  : ProsesDetail,
            success: function(data){
                $('#FormCetakHasil').html(data);
            }
        });
    });

    /*
    ===================================================================================
    DOKTER PENGIRIM DAN PENERIMA
    ===================================================================================
    */

    // Modal Ubah Waktu Pelayanan
    $(document).on('click', '.modal_edit_dokter', function () {

        var id_radiologi = $(this).data('id');

        // Kosongkan Notifikasi
        $('#NotifikasiEditDokter').html('');

        // Tampilkan Modal
        $('#ModalEditDokter').modal('show');

        // Tampilkan Loading
        $('#FormEditDokter').html('Loading...');

        $.ajax({
            type  : 'POST',
            url   : '_Page/Pemeriksaan/FormEditDokter.php',
            data  : {id_radiologi: id_radiologi},
            success: function(data){
                $('#FormEditDokter').html(data);
            }
        });
    });

    // Proses Update Dokter
    $('#ProsesEditDokter').submit(function(e){
        e.preventDefault();
        var formData = $(this).serialize();
        $('#NotifikasiEditDokter').html('<small class="text-muted">Menyimpan data...</small>');

        $.ajax({
            type     : 'POST',
            url      : '_Page/Pemeriksaan/ProsesEditDokter.php',
            dataType : 'json',
            data     : formData,
            success  : function(response){
                var status  = response.status;
                var message = response.message || 'Proses berhasil';

                if(status === 'success'){

                    // Bersihkan notifikasi
                    $('#NotifikasiEditDokter').html('');

                    // Tutup modal jika ada
                    $('#ModalEditDokter').modal('hide');

                    // Reload detail pemeriksaan
                    ShowDetailPemeriksaan();

                    // Tampilkan Ulang Tabel
                    ShowTablePemeriksaan();

                    // Toast Proses Berhasil
                    $('#put_message').html('<i class="bi bi-check-circle me-2"></i> ' + message);

                    // Tampilkan Toast
                    var toastEl = document.getElementById('toast_proses');
                    var toast   = new bootstrap.Toast(toastEl, {delay: 3000});
                    toast.show();

                } else {
                    // Tampilkan Pesan Kesalahan
                    $('#NotifikasiEditDokter').html(
                        '<div class="alert alert-danger"><small>'+message+'</small></div>'
                    );
                }
            },
            error: function(xhr){
                console.log(xhr.responseText);

                $('#NotifikasiEditDokter').html(
                    '<div class="alert alert-danger"><small>Terjadi kesalahan sistem</small></div>'
                );
            }
        });
    });

    /*
    ===================================================================================
    KLINIS
    ===================================================================================
    */
    // Modal Tambah Klinis
    $(document).on('click', '.modal_tambah_klinis', function () {

        var id_radiologi = $(this).data('id');

        // Kosongkan Notifikasi
        $('#NotifikasiTambahKlinis').html('');

        // Kosongkan Preview Tambah Klinis
        $('#PreviewTambahKlinis').html('');

        // Tampilkan Modal
        $('#ModalTambahKlinis').modal('show');

        // Tampilkan Loading
        $('#FormTambahKlinis').html('Loading...');

        $.ajax({
            type  : 'POST',
            url   : '_Page/Pemeriksaan/FormTambahKlinis.php',
            data  : {id_radiologi: id_radiologi},
            success: function(data){
                $('#FormTambahKlinis').html(data);

                // Select 2 untuk list klinis
                $('#id_master_klinis_tambah').select2({
                    theme             : 'bootstrap-5',
                    placeholder       : 'Cari Referensi Klinis...',
                    minimumInputLength: 3,
                    allowClear        : true,
                    dropdownParent    : $('#ModalTambahKlinis'),
                    ajax              : {
                        url     : '_Page/Pemeriksaan/list_klinis.php',
                        dataType: 'json',
                        delay   : 250,
                        data    : function (params) {
                            return {
                                q   : params.term || '', page: params.page || 1
                            };
                        },
                        processResults: function (data, params) {
                            params.page = params.page || 1;

                            return {
                                results   : data.results,
                                pagination: {
                                    more: data.pagination.more
                                }
                            };
                        },
                        cache: true
                    }
                });
            }
        });
    });

    // Ketika id_master_klinis_tambah change, sistem akan menampilkan preview
    $(document).on('change', '#id_master_klinis_tambah', function () {

        //tangkap data 'accession_number' dan buat variabel
        var id_master_klinis   = $(this).val();

        //Form Loading
        $('#PreviewTambahKlinis').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Pemeriksaan/PreviewTambahKlinis.php',
            data        : {id_master_klinis: id_master_klinis},
            success     : function(data){
                $('#PreviewTambahKlinis').html(data);
            }
        });
    });

    //Proses Tambah Klinis
    $('#ProsesTambahKlinis').submit(function(e){
        e.preventDefault();
        var formData = $(this).serialize();
        $('#NotifikasiTambahKlinis').html('<small class="text-muted">Menyimpan data...</small>');

        $.ajax({
            type     : 'POST',
            url      : '_Page/Pemeriksaan/ProsesTambahKlinis.php',
            dataType : 'json',
            data     : formData,
            success  : function(response){
                var status  = response.status;
                var message = response.message || 'Proses berhasil';

                if(status === 'success'){

                    // Bersihkan notifikasi
                    $('#NotifikasiTambahKlinis').html('');

                    // Tutup modal jika ada
                    $('#ModalTambahKlinis').modal('hide');

                    // Reload detail pemeriksaan
                    ShowDetailPemeriksaan();

                    // Tampilkan Ulang Tabel
                    ShowTablePemeriksaan();

                    // Toast Proses Berhasil
                    $('#put_message').html('<i class="bi bi-check-circle me-2"></i> ' + message);

                    // Tampilkan Toast
                    var toastEl = document.getElementById('toast_proses');
                    var toast   = new bootstrap.Toast(toastEl, {delay: 3000});
                    toast.show();

                } else {
                    // Tampilkan Pesan Kesalahan
                    $('#NotifikasiTambahKlinis').html(
                        '<div class="alert alert-danger"><small>'+message+'</small></div>'
                    );
                }
            },
            error: function(xhr){
                console.log(xhr.responseText);

                $('#NotifikasiTambahKlinis').html(
                    '<div class="alert alert-danger"><small>Terjadi kesalahan sistem</small></div>'
                );
            }
        });
    });

    // Modal Hapus Klinis
    $(document).on('click', '.modal_hapus_klinis', function () {

        var id_klinis = $(this).data('id_klinis');
        var id_radiologi = $(this).data('id_radiologi');

        // Kosongkan Notifikasi
        $('#NotifikasiHapusKlinis').html('');

        // Tampilkan Modal
        $('#ModalHapusKlinis').modal('show');

        // Tampilkan Loading
        $('#FormHapusKlinis').html('Loading...');

        $.ajax({
            type  : 'POST',
            url   : '_Page/Pemeriksaan/FormHapusKlinis.php',
            data  : {id_radiologi: id_radiologi, id_klinis: id_klinis},
            success: function(data){
                $('#FormHapusKlinis').html(data);
            }
        });
    });

     //Proses Hapus Klinis
    $('#ProsesHapusKlinis').submit(function(e){
        e.preventDefault();
        var formData = $(this).serialize();
        $('#NotifikasiHapusKlinis').html('<small class="text-muted">Menyimpan data...</small>');

        $.ajax({
            type     : 'POST',
            url      : '_Page/Pemeriksaan/ProsesHapusKlinis.php',
            dataType : 'json',
            data     : formData,
            success  : function(response){
                var status  = response.status;
                var message = response.message || 'Proses berhasil';

                if(status === 'success'){

                    // Bersihkan notifikasi
                    $('#NotifikasiHapusKlinis').html('');

                    // Tutup modal jika ada
                    $('#ModalHapusKlinis').modal('hide');

                    // Reload detail pemeriksaan
                    ShowDetailPemeriksaan();

                    // Tampilkan Ulang Tabel
                    ShowTablePemeriksaan();

                    // Toast Proses Berhasil
                    $('#put_message').html('<i class="bi bi-check-circle me-2"></i> ' + message);

                    // Tampilkan Toast
                    var toastEl = document.getElementById('toast_proses');
                    var toast   = new bootstrap.Toast(toastEl, {delay: 3000});
                    toast.show();

                } else {
                    // Tampilkan Pesan Kesalahan
                    $('#NotifikasiHapusKlinis').html(
                        '<div class="alert alert-danger"><small>'+message+'</small></div>'
                    );
                }
            },
            error: function(xhr){
                console.log(xhr.responseText);

                $('#NotifikasiHapusKlinis').html(
                    '<div class="alert alert-danger"><small>Terjadi kesalahan sistem</small></div>'
                );
            }
        });
    });

    /*
    ===================================================================================
    EDIT PERMINTAAN PEMERIKSAAN
    ===================================================================================
    */
    $(document).on('click', '.modal_edit_permintaan_pemeriksaan', function () {

        //tangkap data 'accession_number' dan buat variabel
        var id_radiologi   = $(this).data('id');

        // Kosongkan Notifikasi
        $('#NotifikasiEditPermintaanPemeriksaan').html('');

        // Tampilkan Modal
        $('#ModalEditPermintaanPemeriksaan').modal('show');

        // Tampilkan Loading
        $('#FormEditPermintaanPemeriksaan').html('Loading...');

        $.ajax({
            type  : 'POST',
            url   : '_Page/Pemeriksaan/FormEditPermintaanPemeriksaan.php',
            data  : {id_radiologi: id_radiologi},
            success: function(data){
                $('#FormEditPermintaanPemeriksaan').html(data);

                // Select 2 untuk list Permintaan Pemeriksaan
                $('#id_master_pemeriksaan_ubah').select2({
                    theme             : 'bootstrap-5',
                    placeholder       : 'Cari Referensi Pemeriksaan...',
                    minimumInputLength: 3,
                    allowClear        : true,
                    dropdownParent    : $('#ModalEditPermintaanPemeriksaan'),
                    ajax              : {
                        url     : '_Page/Pemeriksaan/list_pemeriksaan.php',
                        dataType: 'json',
                        delay   : 250,
                        data    : function (params) {
                            return {
                                q   : params.term || '', page: params.page || 1
                            };
                        },
                        processResults: function (data, params) {
                            params.page = params.page || 1;

                            return {
                                results   : data.results,
                                pagination: {
                                    more: data.pagination.more
                                }
                            };
                        },
                        cache: true
                    }
                });
            }
        });
    });

    // Preview Permintaan Pemeriksaan
    $(document).on('change', '#id_master_pemeriksaan_ubah', function () {

        //tangkap data 'accession_number' dan buat variabel
        var id_master_pemeriksaan   = $(this).val();

        //Form Loading
        $('#preview_master_pemeriksaan').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Pemeriksaan/preview_master_pemeriksaan.php',
            data        : {id_master_pemeriksaan: id_master_pemeriksaan},
            success     : function(data){
                $('#preview_master_pemeriksaan').html(data);
            }
        });
    });

    // Proses Ubah Pemeriksaan
    $('#ProsesEditPermintaanPemeriksaan').submit(function(e){
        e.preventDefault();
        var formData = $(this).serialize();
        $('#NotifikasiEditPermintaanPemeriksaan').html('<small class="text-muted">Menyimpan data...</small>');

        $.ajax({
            type     : 'POST',
            url      : '_Page/Pemeriksaan/ProsesEditPermintaanPemeriksaan.php',
            dataType : 'json',
            data     : formData,
            success  : function(response){
                var status  = response.status;
                var message = response.message || 'Proses berhasil';

                if(status === 'success'){

                    // Bersihkan notifikasi
                    $('#NotifikasiEditPermintaanPemeriksaan').html('');

                    // Tutup modal jika ada
                    $('#ModalEditPermintaanPemeriksaan').modal('hide');

                    // Reload detail pemeriksaan
                    ShowDetailPemeriksaan();

                    // Tampilkan Ulang Tabel
                    ShowTablePemeriksaan();

                    // Toast Proses Berhasil
                    $('#put_message').html('<i class="bi bi-check-circle me-2"></i> ' + message);

                    // Tampilkan Toast
                    var toastEl = document.getElementById('toast_proses');
                    var toast   = new bootstrap.Toast(toastEl, {delay: 3000});
                    toast.show();

                } else {
                    // Tampilkan Pesan Kesalahan
                    $('#NotifikasiEditPermintaanPemeriksaan').html(
                        '<div class="alert alert-danger"><small>'+message+'</small></div>'
                    );
                }
            },
            error: function(xhr){
                console.log(xhr.responseText);

                $('#NotifikasiEditPermintaanPemeriksaan').html(
                    '<div class="alert alert-danger"><small>Terjadi kesalahan sistem</small></div>'
                );
            }
        });
    });

    /*
    ===================================================================================
    DETAIL ACCESSION NUMBER
    ===================================================================================
    */
    $(document).on('click', '.modal_detail_acn', function () {

        //tangkap data 'accession_number' dan buat variabel
        var accession_number   = $(this).data('id');

        //tampilkan modal
        $('#ModalAccessionNumber').modal('show');

        //Form Loading
        $('#FormAccessionNumber').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Pemeriksaan/FormAccessionNumber.php',
            data        : {accession_number: accession_number},
            success     : function(data){
                $('#FormAccessionNumber').html(data);
            }
        });
    });
    $(document).on('click', '.modal_imaging_study_by_acn', function () {

        //tangkap data 'accession_number' dan buat variabel
        var accession_number   = $(this).data('id');

        //tampilkan modal
        $('#ModalImagingStudyByAccessionNumber').modal('show');

        //Form Loading
        $('#FormImagingStudyByAccessionNumber').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Pemeriksaan/FormImagingStudyByAccessionNumber.php',
            data        : {accession_number: accession_number},
            success     : function(data){
                $('#FormImagingStudyByAccessionNumber').html(data);
                // Re-init tooltip
                $('[data-bs-toggle="tooltip"]').tooltip();
            }
        });
    });
    $(document).on('click', '.modal_dicom_metadata_by_acn', function () {

        //tangkap data 'accession_number' dan buat variabel
        var accession_number   = $(this).data('id');

        //tampilkan modal
        $('#ModalDicomMetadataByAcn').modal('show');

        //Form Loading
        $('#FormDicomMetadataByAcn').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Pemeriksaan/FormDicomMetadataByAcn.php',
            data        : {accession_number: accession_number},
            success     : function(data){
                $('#FormDicomMetadataByAcn').html(data);
                // Re-init tooltip
                $('[data-bs-toggle="tooltip"]').tooltip();
            }
        });
    });
    $(document).on('click', '.modal_expertise_by_acn', function () {

        //tangkap data 'accession_number' dan buat variabel
        var accession_number   = $(this).data('id');

        //tampilkan modal
        $('#ModalExpertiseByAcn').modal('show');

        //Form Loading
        $('#FormExpertiseByAcn').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Pemeriksaan/FormExpertiseByAcn.php',
            data        : {accession_number: accession_number},
            success     : function(data){
                $('#FormExpertiseByAcn').html(data);
                // Re-init tooltip
                $('[data-bs-toggle="tooltip"]').tooltip();
            }
        });
    });


    /*
    ===================================================================================
    KONVERSI DICOM
    ===================================================================================
    */
    $(document).on('click', '.modal_konversi_dicom', function () {

        //tangkap data 'id_radiologi_file' dan buat variabel
        var id_radiologi_file   = $(this).data('id');

        //tampilkan modal
        $('#ModalKonversiDicom').modal('show');

        //Form Loading
        $('#FormKonversiDicom').html('Loading...');

        // Kosongkan Notifikasi
        $('#NotifikasiKonversiDicom').html('');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Pemeriksaan/FormKonversiDicom.php',
            data        : {id_radiologi_file: id_radiologi_file},
            success     : function(data){
                $('#FormKonversiDicom').html(data);
            }
        });
    });

    // Proses Konversi
    $('#ProsesKonversiDicom').submit(function(e){
        e.preventDefault();
        var formData = $(this).serialize();
        $('#NotifikasiKonversiDicom').html('<small class="text-muted">Menyimpan data...</small>');

        $.ajax({
            type     : 'POST',
            url      : '_Page/Pemeriksaan/ProsesKonversiDicom.php',
            dataType : 'json',
            data     : formData,
            success  : function(response){
                var status  = response.status;
                var message = response.message || 'Proses berhasil';

                if(status === 'success'){

                    // Bersihkan notifikasi
                    $('#NotifikasiKonversiDicom').html('');

                    // Tutup modal jika ada
                    $('#ModalKonversiDicom').modal('hide');

                    // Reload detail pemeriksaan
                    ShowDetailPemeriksaan();

                    // Tampilkan Ulang Tabel
                    ShowTablePemeriksaan();

                    // Toast Proses Berhasil
                    $('#put_message').html('<i class="bi bi-check-circle me-2"></i> ' + message);

                    // Tampilkan Toast
                    var toastEl = document.getElementById('toast_proses');
                    var toast   = new bootstrap.Toast(toastEl, {delay: 3000});
                    toast.show();

                } else {
                    // Tampilkan Pesan Kesalahan
                    $('#NotifikasiKonversiDicom').html(
                        '<div class="alert alert-danger"><small>'+message+'</small></div>'
                    );
                }
            },
            error: function(xhr){
                console.log(xhr.responseText);

                $('#NotifikasiKonversiDicom').html(
                    '<div class="alert alert-danger"><small>Terjadi kesalahan sistem</small></div>'
                );
            }
        });
    });
    $(document).on('click', '.modal_detail_dicom', function () {

        //tangkap data 'id_radiologi_dicom_conv' dan buat variabel
        var id_radiologi_dicom_conv   = $(this).data('id');

        //tampilkan modal
        $('#ModalDetailDicom').modal('show');

        //Form Loading
        $('#FormDetailDicom').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Pemeriksaan/FormDetailDicom.php',
            data        : {id_radiologi_dicom_conv: id_radiologi_dicom_conv},
            success     : function(data){
                $('#FormDetailDicom').html(data);
            }
        });
    });

    $(document).on('click', '.modal_dicom_viewer', function () {
    
        //tangkap data 'id_radiologi_dicom_conv' dan buat variabel
        var id_radiologi_dicom_conv = $(this).data('id');
        
        //tampilkan modal
        $('#ModalDicomViewer').modal('show');
        
        //Form Loading dengan spinner
        $('#FormDicomViewer').html(`
            <div class="d-flex justify-content-center align-items-center h-100">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Memuat DICOM Viewer...</p>
                </div>
            </div>
        `);
        
        //Tampilkan Form Dengan Ajax
        $.ajax({
            type: 'POST',
            url: '_Page/Pemeriksaan/FormDicomViewer.php',
            data: {id_radiologi_dicom_conv: id_radiologi_dicom_conv},
            success: function(data) {
                $('#FormDicomViewer').html(data);
                
                // Set iframe height setelah konten dimuat
                setTimeout(function() {
                    adjustIframeHeight();
                }, 100);
            },
            error: function(xhr, status, error) {
                $('#FormDicomViewer').html(`
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle"></i> Gagal memuat DICOM Viewer: ${error}
                    </div>
                `);
            }
        });
    });

    // Fungsi untuk menyesuaikan tinggi iframe
    function adjustIframeHeight() {
        const iframe = $('#FormDicomViewer iframe');
        if (iframe.length) {
            const modalHeader = $('#ModalDicomViewer .modal-header').outerHeight() || 56;
            const modalFooter = $('#ModalDicomViewer .modal-footer').outerHeight() || 72;
            const windowHeight = $(window).height();
            const iframeHeight = windowHeight - modalHeader - modalFooter - 40; // 40px untuk padding/margin
            
            iframe.css('height', iframeHeight + 'px');
        }
    }

    // Handle resize window
    $(window).on('resize', function() {
        adjustIframeHeight();
    });

    // Handle modal shown event
    $('#ModalDicomViewer').on('shown.bs.modal', function() {
        adjustIframeHeight();
    });
});