//Fungsi Menampilkan Data Kunjungan
function ShowTable() {

    var $container = $('#TabelTandaTangan');
    var heightBefore = $container.height(); // simpan tinggi awal

    var ProsesFilter = $('#ProsesFilter').serialize();

    // Kunci tinggi agar layout tidak loncat
    $container
        .css({
            'min-height': heightBefore + 'px',
            'opacity': 0.5
        });

    $.ajax({
        type    : 'POST',
        url     : '_Page/TandaTangan/TabelTandaTangan.php',
        data    : ProsesFilter,
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

//Fungsi Menampilkan List Kategori
function ShowListKategori() {
    $.ajax({
        type    : 'POST',
        url     : '_Page/TandaTangan/Listkategori.php',
        success: function(data) {
            $('.list_kategori').html(data);
        }
    });
}

//Menampilkan Data Pertama Kali
$(document).ready(function() {
    ShowTable();

    /*  
    ---------------------------------------------------
    TAMBAH TANDA TANGAN
    --------------------------------------------------- 
    */
    $(document).on('click', '.modal_tambah', function(){
        $('#ModalTambah').modal('show');
        ShowListKategori();
    });

    /* Ketika 'ProsesTambah' disubmit */
    $('#ProsesTambah').submit(function(e){
        e.preventDefault();

        var formData = new FormData(this);

        $('#NotifikasiTambah').html('loading..');

        $.ajax({
            type    : 'POST',
            url     : '_Page/TandaTangan/ProsesTambah.php',
            dataType: 'json',
            data    : formData,
            processData: false,
            contentType: false,
            success: function(response) {
                var status  = response.status;
                var message = response.message;

                if(status=='success'){
                    $('#NotifikasiTambah').html('');
                    $('#ProsesTambah')[0].reset();
                    $('#ModalTambah').modal('hide');

                    Swal.fire('Success!', 'Tambah TANDA TANGAN Berhasil!', 'success');

                    ShowTable();
                } else {
                    $('#NotifikasiTambah').html('<div class="alert alert-danger"><small>'+message+'</small></div>');
                }
            }
        });
    });

    /*  
    ---------------------------------------------------
    DETAIL TANDA TANGAN
    --------------------------------------------------- 
    */
    $(document).on('click', '.modal_detail', function(){
        //Menangkap 'id_master_signature'
        var id_master_signature = $(this).data('id');

        // Menampilkan modal
        $('#ModalDetail').modal('show');

        //Menampilkan Detail Dengan AJAX
        $('#FormDetail').html('Loading...');
        $.ajax({
            type    : 'POST',
            url     : '_Page/TandaTangan/FormDetail.php',
            data    : {id_master_signature: id_master_signature},
            success: function(data) {
                $('#FormDetail').html(data);
            }
        });
    });

    /*  
    ---------------------------------------------------
    EDIT TANDA TANGAN
    --------------------------------------------------- 
    */
    $(document).on('click', '.modal_edit', function () {

        //tangkap data 'id_master_signature' dan buat variabel
        var id_master_signature   = $(this).data('id');

        //tampilkan modal
        $('#ModalEdit').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiEdit').html('');

        //Form Loading
        $('#FormEdit').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/TandaTangan/FormEdit.php',
            data        : {id_master_signature: id_master_signature},
            success     : function(data){
                $('#FormEdit').html(data);

                ShowListKategori();
            }
        });
    });
    
    /* Ketika 'ProsesEdit' disubmit */
    $('#ProsesEdit').submit(function(e){
        e.preventDefault();

        var formData = new FormData(this);

        $('#NotifikasiEdit').html('loading..');

        $.ajax({
            type    : 'POST',
            url     : '_Page/TandaTangan/ProsesEdit.php',
            dataType: 'json',
            data    : formData,
            processData: false,
            contentType: false,
            success: function(response) {
                var status  = response.status;
                var message = response.message;

                if(status=='success'){
                    $('#NotifikasiEdit').html('');
                    $('#ModalEdit').modal('hide');
                    ShowTable();

                    Swal.fire('Success!', 'Edit Tanda Tangan Berhasil!', 'success');
                } else {
                    $('#NotifikasiEdit').html('<div class="alert alert-danger"><small>'+message+'</small></div>');
                }
            }
        });
    });


    /*  
    ---------------------------------------------------
    HAPUS TANDA TANGAN
    --------------------------------------------------- 
    */
    $(document).on('click', '.modal_delete', function () {

        //tangkap data 'id_master_signature' dan buat variabel
        var id_master_signature   = $(this).data('id');

        //tampilkan modal
        $('#ModalDelete').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiDelete').html('');

        //Form Loading
        $('#FormDelete').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/TandaTangan/FormDelete.php',
            data        : {id_master_signature: id_master_signature},
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
            url     : '_Page/TandaTangan/ProsesDelete.php',
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
                    ShowTable();

                    // Menampilkan Swal
                    Swal.fire(
                        'Success!',
                        'Hapus TANDA TANGAN Berhasil!',
                        'success'
                    )
                }else{
                    $('#NotifikasiDelete').html('<div class="alert alert-danger"><small>'+message+'</small></div>');
                }
                
            }
        });
    });
});