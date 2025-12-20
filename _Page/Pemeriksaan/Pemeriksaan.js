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
    KETIKA 'modal_tambah_permintaan' DI KLIK DAN MENAMPILKAN 'ModalTambahPermintaan'
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
                
                // Select2
                // $('#dokter_pengirim').select2({
                //     placeholder: "Cari produk di sini...",
                //     allowClear: true 
                // });
            }
        });

    });

    /* Ketika 'ProsesTambah' disubmit */
    $('#ProsesTambah').submit(function(){
       
        /* Menangkap data dari form  */
        var ProsesTambah=$('#ProsesTambah').serialize();

        /* Loading Notification */
        $('#NotifikasiTambah').html('loading..');

        /* Kirim data dengan AJAX  */
        $.ajax({
            type    : 'POST',
            url     : '_Page/SettingSimrs/ProsesTambah.php',
            dataType: 'json',
            data    : ProsesTambah,
            success: function(response) {
                var status  = response.status;
                var message = response.message;

                // Apabila berhasil
                if(status=='success'){
                    //Bersihkan notifikasi
                    $('#NotifikasiTambah').html('');

                    //reset form
                    $('#ProsesTambah')[0].reset();

                    //Tutup modal
                    $('#ModalTambah').modal('hide');

                    //Menampilkan Swal
                    Swal.fire(
                        'Success!',
                        'Tambah Koneksi SIMRS Berhasil!',
                        'success'
                    )

                    //reload tabel
                    ShowConnectionTable();
                }else{
                    $('#NotifikasiTambah').html('<div class="alert alert-danger"><small>'+message+'</small></div>');
                }
                
            }
        });
    });

    /* MODAL DETAIL */
    $(document).on('click', '.modal_detail', function () {

        //tangkap data 'id_connection_simrs' dan buat variabel
        var id_connection_simrs   = $(this).data('id');

        //tampilkan modal
        $('#ModalDetail').modal('show');

        //Form Loading
        $('#FormDetail').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/SettingSimrs/FormDetail.php',
            data        : {id_connection_simrs: id_connection_simrs},
            success     : function(data){
                $('#FormDetail').html(data);
            }
        });
    });

    /* MODAL UJI KONEKSI */
    $(document).on('click', '.modal_uji_koneksi', function () {

        //tangkap data 'id_connection_simrs' dan buat variabel
        var id_connection_simrs   = $(this).data('id');

        //tampilkan modal
        $('#ModalUjiKoneksi').modal('show');

        //Form Loading
        $('#FormUjiKoneksi').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/SettingSimrs/FormUjiKoneksi.php',
            data        : {id_connection_simrs: id_connection_simrs},
            success     : function(data){
                $('#FormUjiKoneksi').html(data);
            }
        });
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

    /* MODAL DELETE */
    $(document).on('click', '.modal_delete', function () {

        //tangkap data 'id_connection_simrs' dan buat variabel
        var id_connection_simrs   = $(this).data('id');

        //tampilkan modal
        $('#ModalDelete').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiDelete').html('');

        //Form Loading
        $('#FormDelete').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/SettingSimrs/FormDelete.php',
            data        : {id_connection_simrs: id_connection_simrs},
            success     : function(data){
                $('#FormDelete').html(data);
            }
        });
    });

    /* Ketika 'ProsesDelete' disubmit */
    $('#ProsesDelete').submit(function(){
       
        /* Menangkap data dari form  */
        var ProsesDelete=$('#ProsesDelete').serialize();

        /* Loading Notification */
        $('#NotifikasiDelete').html('loading..');

        /* Kirim data dengan AJAX  */
        $.ajax({
            type    : 'POST',
            url     : '_Page/SettingSimrs/ProsesDelete.php',
            dataType: 'json',
            data    : ProsesDelete,
            success: function(response) {
                var status  = response.status;
                var message = response.message;

                // Apabila berhasil
                if(status=='success'){
                    //Bersihkan notifikasi
                    $('#NotifikasiDelete').html('');

                    //Tutup modal
                    $('#ModalDelete').modal('hide');

                    //reload tabel
                    ShowConnectionTable();

                    // Menampilkan Swal
                    Swal.fire(
                        'Success!',
                        'Hapus Koneksi SIMRS Berhasil!',
                        'success'
                    )
                }else{
                    $('#NotifikasiDelete').html('<div class="alert alert-danger"><small>'+message+'</small></div>');
                }
                
            }
        });
    });

});