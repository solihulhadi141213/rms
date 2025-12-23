//Fungsi Menampilkan Data Pemeriksaan
function ShowTablePemeriksaan() {
    var ProsesFilter = $('#ProsesFilter').serialize();
    $.ajax({
        type    : 'POST',
        url     : '_Page/Pemeriksaan/TabelPemeriksaan.php',
        data    : ProsesFilter,
        success: function(data) {
            $('#TabelPemeriksaan').html(data);
            
            // 🔁 Re-inisialisasi tooltip setelah data dimuat
            $('[data-bs-toggle="tooltip"]').tooltip();
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
    ---------------------------------------------------
    TAMBAH PERMINTAAN
    --------------------------------------------------- 
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
        ShowTableKunjungan(0);
    });

    /*
    --------------------------------------------------------------------------------
    TAMBAH PERMINTAAN PEMERIKSAAN
    --------------------------------------------------------------------------------
    */
    $(document).on('click', '.modal_tambah_permintaan', function(){

        //Tangkap 'id_kunjungan'
        var id_kunjungan = $(this).data('id');

        //Tampilkan Modal
        $('#ModalTambahPermintaan').modal('show');

        //Kosongkan 'NotifikasiTambahPermintaan'
        $('#NotifikasiTambahPermintaan').html('');

        //Loading Form
        $('#FormTambahPermintaan').html('Loading...');

        //Buka Forrm Dengan AJAX
        $.ajax({
            type    : 'POST',
            url     : '_Page/Pemeriksaan/FormTambahPermintaan.php',
            data    : {id_kunjungan: id_kunjungan},
            success: function(data) {
                $('#FormTambahPermintaan').html(data);
                
                // ===== INIT SELECT2 DOKTER =====
                $('#dokter_pengirim').select2({
                    theme: 'bootstrap-5',          // opsional (jika pakai Bootstrap 5)
                    placeholder: 'Cari dokter...',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('#ModalTambahPermintaan') // WAJIB
                });

                // ===== INIT SELECT2 MASTER KLINIS =====
                $('#klinis').select2({
                    theme: 'bootstrap-5',
                    placeholder: 'Ketik & pilih klinis',
                    tags: true,
                    width: '100%',
                    minimumInputLength: 2,
                    dropdownParent: $('#ModalTambahPermintaan'), // Penting untuk modal
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
                            params.page = params.page || 1;
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

                // ===== INIT SELECT2 ALAT PEMERIKSA =====
                $('#alat_pemeriksa').select2({
                    theme: 'bootstrap-5',
                    placeholder: 'Pilih Modality/Alat',
                    width: '100%',
                    dropdownParent: $('#ModalTambahPermintaan')
                });
                
                // ===== INIT SELECT2 MASTER PEMERIKSAAN =====
                function initPemeriksaanSelect() {
                    var alat_pemeriksa = $('#alat_pemeriksa').val();
                    var isEnabled = !!alat_pemeriksa;

                    // Reset & enable/disable
                    $('#permintaan_pemeriksaan')
                        .prop('disabled', !isEnabled)
                        .empty();

                    var placeholder = isEnabled
                        ? 'Pilih pemeriksaan'
                        : 'Pilih alat pemeriksa terlebih dahulu';

                    // Destroy Select2 lama
                    if ($('#permintaan_pemeriksaan').hasClass('select2-hidden-accessible')) {
                        $('#permintaan_pemeriksaan').select2('destroy');
                    }

                    var select2Options = {
                        theme: 'bootstrap-5',
                        width: '100%',
                        placeholder: placeholder,
                        dropdownParent: $('#ModalTambahPermintaan'),

                        // 🔒 HANYA 1 & TANPA TEXT BEBAS
                        multiple: false,
                        closeOnSelect: true,
                        allowClear: true,
                        minimumResultsForSearch: 0 // tetap tampil search box
                    };

                    if (isEnabled) {
                        select2Options.ajax = {
                            url: '_Page/Pemeriksaan/AjaxPemeriksaan.php',
                            dataType: 'json',
                            delay: 200,

                            data: function (params) {
                                return {
                                    q: params.term || '',   // kosong = load awal
                                    alat: alat_pemeriksa,
                                    page: params.page || 1,
                                    limit: 10               // 🔹 10 data terbaru
                                };
                            },

                            processResults: function (data, params) {
                                params.page = params.page || 1;
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

                    $('#permintaan_pemeriksaan').select2(select2Options);

                    // 🔹 AUTO LOAD 10 DATA TERBARU SAAT ALAT DIPILIH
                    if (isEnabled) {
                        $('#permintaan_pemeriksaan').select2('open');
                    }
                }

                // Init pertama
                initPemeriksaanSelect();

                // Saat alat pemeriksa berubah
                $('#alat_pemeriksa').on('change', function () {
                    $('#permintaan_pemeriksaan').val(null).trigger('change');
                    initPemeriksaanSelect();
                });


            }
        });

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

                    //Tutup modal
                    $('#ModalTambahPermintaan').modal('hide');
                    $('#ModalKunjungan').modal('hide');

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

    /* MODAL EDIT */
    $(document).on('click', '.modal_edit', function () {

        //tangkap data 'id_connection_simrs' dan buat variabel
        var id_connection_simrs   = $(this).data('id');

        //tampilkan modal
        $('#ModalEdit').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiEdit').html('');

        //Form Loading
        $('#FormEdit').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/SettingSimrs/FormEdit.php',
            data        : {id_connection_simrs: id_connection_simrs},
            success     : function(data){
                $('#FormEdit').html(data);
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