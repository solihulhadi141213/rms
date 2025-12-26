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

//Menampilkan Data Pertama Kali
$(document).ready(function() {
    // Pertama Kali Sembunyikan 'TambahPermintaan'
    $('#TambahPermintaan').hide();

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
    $('#ProsesFilter').submit(function(){
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
    $('#ProsesFilterKunjungan').submit(function(){
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
                    tags              : true,
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

        // Kembalikan posisi layar ke atas
        $('html, body').scrollTop(0);
    });

    /* Ketika 'ProsesTambah' disubmit */
    $('#ProsesTambah').submit(function(){
       
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

                    // Kembalikan posisi layar ke atas
                    $('html, body').scrollTop(0);

                    //Tutup modal
                    $('#DataPemeriksaan').show();
                    $('#TambahPermintaan').hide();

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
    $('#ProsesTerimaPermintaan').submit(function(){
       
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

    /* Ketika 'ProsesEdit' disubmit */
    $('#ProsesEdit').submit(function(){
       
        /* Menangkap data dari form  */
        var ProsesEdit=$('#ProsesEdit').serialize();

        /* Loading Notification */
        $('#NotifikasiEdit').html('loading..');

        /* Kirim data dengan AJAX  */
        $.ajax({
            type    : 'POST',
            url     : '_Page/SettingSimrs/ProsesEdit.php',
            dataType: 'json',
            data    : ProsesEdit,
            success: function(response) {
                var status  = response.status;
                var message = response.message;

                // Apabila berhasil
                if(status=='success'){
                    //Bersihkan notifikasi
                    $('#NotifikasiEdit').html('');

                    //Tutup modal
                    $('#ModalEdit').modal('hide');

                    //reload tabel
                    ShowConnectionTable();

                    // Menampilkan Swal
                    Swal.fire(
                        'Success!',
                        'Edit Koneksi SIMRS Berhasil!',
                        'success'
                    )
                }else{
                    $('#NotifikasiEdit').html('<div class="alert alert-danger"><small>'+message+'</small></div>');
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

    $('#ProsesServiceRequest').submit(function(){
       
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
    $('#ProsesProcedure').submit(function(){
       
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

    $('#ProsesImagingStudy').submit(function(){
       
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

    $('#ProsesOrderPacs').submit(function(){
       
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

});