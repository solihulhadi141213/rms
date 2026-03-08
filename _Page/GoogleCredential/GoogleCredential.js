//Fungsi Menampilkan Data
function ShowData() {
    var ProsesFilter = $('#ProsesFilter').serialize();
    var $tabel       = $('#TableGoogleCredential');
    if ($tabel.length < 1) {
        $tabel = $('#TabelGoogleCredential');
    }

    // Tambahkan efek visual loading (opacity menurun)
    $tabel.css({
        'opacity': '0.5',
        'pointer-events': 'none',
        'transition': 'opacity 0.3s ease'
    });
    $('#page_info').html('Total Data : 0');
    $.ajax({
        type   : 'POST',
        url    : '_Page/GoogleCredential/TabelGoogleCredential.php',
        data   : ProsesFilter,
        success: function(data) {
            // Ganti isi tabel tanpa mengganti elemen induk
            $tabel.html(data);

            // Kembalikan efek normal
            $tabel.css({
                'opacity': '1',
                'pointer-events': 'auto'
            });

            // Re-inisialisasi tooltip setelah data dimuat
            $('[data-bs-toggle="tooltip"]').tooltip();
        },
        error: function() {
            $tabel.html('<div class="alert alert-danger text-center mb-0"><small>Gagal memuat, silahkan coba lagi!</small></div>');
            $tabel.css({
                'opacity': '1',
                'pointer-events': 'auto'
            });
        }
    });
}


//Menampilkan Data Pertama Kali
$(document).ready(function() {

    //Menampilkan Data Pertama Kali
    ShowData();

    //Proses Tambah
    $('#ProsesTambah').submit(function(e){
        e.preventDefault();

        // Ambil Data Dari form
        var ProsesTambah = $(this).serialize();

        //Loading Notifikasi
        $('#NotifikasiTambah').html('<small class="text-muted">Menyimpan data...</small>');

        // Ajax Request
        $.ajax({
            type     : 'POST',
            url      : '_Page/GoogleCredential/ProsesTambah.php',
            dataType : 'json',
            data     : ProsesTambah,

            success: function(response){
                var status  = response.status;
                var message = response.message || 'Proses berhasil';

                if(status === 'success'){

                    // Bersihkan notifikasi
                    $('#NotifikasiTambah').html('');

                    // Tutup modal jika ada
                    $('#ModalTambah').modal('hide');

                    // Reset Form
                    $("#ProsesTambah")[0].reset();

                    // Reload Data
                    ShowData();

                    // Toast Proses Berhasil
                    $('#put_message').html('<i class="bi bi-check-circle me-2"></i> ' + message);

                    // Tampilkan Toast
                    var toastEl = document.getElementById('toast_proses');
                    var toast   = new bootstrap.Toast(toastEl, {delay: 3000});
                    toast.show();

                } else {
                    // Tampilkan Pesan Kesalahan
                    $('#NotifikasiTambah').html(
                        '<div class="alert alert-danger"><small>'+message+'</small></div>'
                    );
                }
            },

            error: function(xhr){
                console.log(xhr.responseText);

                $('#NotifikasiTambah').html(
                    '<div class="alert alert-danger"><small>Terjadi kesalahan sistem</small></div>'
                );
            }
        });
    });
    // ===============================================================================
    // DETAIL / LIHAT CLIENT Secret
    // ===============================================================================
    $(document).on('click', '.modal_lihat_client_secret', function () {

        //tangkap data 'id_referensi_metode_sample' dan buat variabel
        var id_google_credential   = $(this).data('id');

        //tampilkan modal
        $('#ModalLihatClientSecret').modal('show');

        //Form Loading
        $('#FormLihatClientSecret').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type      : 'POST',
            url       : '_Page/GoogleCredential/FormLihatClientSceret.php',
            data      : {id_google_credential: id_google_credential},
            success   : function(data){
                $('#FormLihatClientSecret').html(data);
            }
        });
    });

    // ===============================================================================
    // EDIT
    // ===============================================================================
    $(document).on('click', '.modal_edit', function () {

        //tangkap data 'id_google_credential' dan buat variabel
        var id_google_credential   = $(this).data('id');

        //tampilkan modal
        $('#ModalEdit').modal('show');

        //Form Loading
        $('#FormEdit').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type      : 'POST',
            url       : '_Page/GoogleCredential/FormEdit.php',
            data      : {id_google_credential: id_google_credential},
            success   : function(data){
                $('#FormEdit').html(data);
            }
        });
    });



    //Proses Edit
    $('#ProsesEdit').submit(function(e){
        e.preventDefault();

        // Ambil Data Dari form
        var ProsesEdit = $(this).serialize();

        //Loading Notifikasi
        $('#NotifikasiEdit').html('<small class="text-muted">Menyimpan data...</small>');

        // Ajax Request
        $.ajax({
            type     : 'POST',
            url      : '_Page/GoogleCredential/ProsesEdit.php',
            dataType : 'json',
            data     : ProsesEdit,

            success: function(response){

                var status  = response.status;
                var message = response.message;

                if(status === 'success'){

                    // Bersihkan notifikasi
                    $('#NotifikasiEdit').html('');

                    // Tutup modal jika ada
                    $('#ModalEdit').modal('hide');

                    // Reload detail pemeriksaan
                    ShowData();

                    // Toast Proses Berhasil
                    $('#put_message').html('<i class="bi bi-check-circle me-2"></i> ' + message);

                    // Tampilkan Toast
                    var toastEl = document.getElementById('toast_proses');
                    var toast   = new bootstrap.Toast(toastEl, {delay: 3000});
                    toast.show();

                } else {
                    // Tampilkan Pesan Kesalahan
                    $('#NotifikasiEdit').html(
                        '<div class="alert alert-danger"><small>'+message+'</small></div>'
                    );
                }
            },

            error: function(xhr){
                console.log(xhr.responseText);

                $('#NotifikasiEdit').html(
                    '<div class="alert alert-danger"><small>Terjadi kesalahan sistem</small></div>'
                );
            }
        });
    });

    // ===============================================================================
    // UPDATE STATUS
    // ===============================================================================
    $(document).on('click', '.modal_update_status', function () {

        //tangkap data 'id_google_credential' dan buat variabel
        var id_google_credential = $(this).data('id');
        var status               = $(this).data('status');

        //tampilkan modal
        $('#ModalUpdateStatus').modal('show');

        //Form Loading
        $('#FormUpdateStatus').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type      : 'POST',
            url       : '_Page/GoogleCredential/FormUpdateStatus.php',
            data      : {id_google_credential: id_google_credential, status: status},
            success   : function(data){
                $('#FormUpdateStatus').html(data);
            }
        });
    });



    //Proses Edit
    $('#ProsesUpdate').submit(function(e){
        e.preventDefault();

        // Ambil Data Dari form
        var ProsesUpdate = $(this).serialize();

        //Loading Notifikasi
        $('#NotifikasiUpdateStatus').html('<small class="text-muted">Menyimpan data...</small>');

        // Ajax Request
        $.ajax({
            type     : 'POST',
            url      : '_Page/GoogleCredential/ProsesUpdate.php',
            dataType : 'json',
            data     : ProsesUpdate,

            success: function(response){

                var status  = response.status;
                var message = response.message;

                if(status === 'success'){

                    // Bersihkan notifikasi
                    $('#NotifikasiUpdateStatus').html('');

                    // Tutup modal jika ada
                    $('#ModalUpdateStatus').modal('hide');

                    // Reload detail pemeriksaan
                    ShowData();

                    // Toast Proses Berhasil
                    $('#put_message').html('<i class="bi bi-check-circle me-2"></i> ' + message);

                    // Tampilkan Toast
                    var toastEl = document.getElementById('toast_proses');
                    var toast   = new bootstrap.Toast(toastEl, {delay: 3000});
                    toast.show();

                } else {
                    // Tampilkan Pesan Kesalahan
                    $('#NotifikasiUpdateStatus').html(
                        '<div class="alert alert-danger"><small>'+message+'</small></div>'
                    );
                }
            },

            error: function(xhr){
                console.log(xhr.responseText);

                $('#NotifikasiUpdateStatus').html(
                    '<div class="alert alert-danger"><small>Terjadi kesalahan sistem</small></div>'
                );
            }
        });
    });

    // ===============================================================================
    // HAPUS
    // ===============================================================================
    $(document).on('click', '.modal_hapus', function () {

        //tangkap data 'id_google_credential' dan buat variabel
        var id_google_credential   = $(this).data('id');

        //tampilkan modal
        $('#ModalHapus').modal('show');

        //Form Loading
        $('#FormHapus').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type      : 'POST',
            url       : '_Page/GoogleCredential/FormHapus.php',
            data      : {id_google_credential: id_google_credential},
            success   : function(data){
                $('#FormHapus').html(data);
            }
        });
    });

    //Proses Hapus
    $('#ProsesHapus').submit(function(e){
        e.preventDefault();

        // Ambil Data Dari form
        var ProsesHapus = $(this).serialize();

        //Loading Notifikasi
        $('#NotifikasiHapus').html('<small class="text-muted">Loading...</small>');

        // Ajax Request
        $.ajax({
            type     : 'POST',
            url      : '_Page/GoogleCredential/ProsesHapus.php',
            dataType : 'json',
            data     : ProsesHapus,

            success: function(response){

                var status  = response.status;
                var message = response.message;

                if(status === 'success'){

                    // Bersihkan notifikasi
                    $('#NotifikasiHapus').html('');

                    // Tutup modal jika ada
                    $('#ModalHapus').modal('hide');

                    // Reload detail pemeriksaan
                    ShowData();

                    // Toast Proses Berhasil
                    $('#put_message').html('<i class="bi bi-check-circle me-2"></i> ' + message);

                    // Tampilkan Toast
                    var toastEl = document.getElementById('toast_proses');
                    var toast   = new bootstrap.Toast(toastEl, {delay: 3000});
                    toast.show();

                } else {
                    // Tampilkan Pesan Kesalahan
                    $('#NotifikasiHapus').html(
                        '<div class="alert alert-danger"><small>'+message+'</small></div>'
                    );
                }
            },

            error: function(xhr){
                console.log(xhr.responseText);

                $('#NotifikasiHapus').html(
                    '<div class="alert alert-danger"><small>Terjadi kesalahan sistem</small></div>'
                );
            }
        });
    });



});
