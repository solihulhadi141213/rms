//Fungsi Menampilkan Data
function ShowTable() {

    var $container = $('#TabelDaftarPertanyaan');
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
        url     : '_Page/Question/TabelDaftarPertanyaan.php',
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
        url     : '_Page/Question/Listkategori.php',
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
            url 	    : '_Page/Question/FormFilter.php',
            data        : {KeywordBy: KeywordBy},
            success     : function(data){
                $('#FormFilter').html(data);
            }
        });
    });

    //Proses Filter/Pencarian
    $('#ProsesFilter').submit(function(){
        $('#page').val("1");
        ShowTable();
        $('#ModalFilter').modal('hide');
    });

    //Pagging
    $(document).on('click', '#next_button', function() {
        var page_now = parseInt($('#page').val(), 10); // Pastikan nilai diambil sebagai angka
        var next_page = page_now + 1;
        $('#page').val(next_page);
        ShowTable(0);
    });
    $(document).on('click', '#prev_button', function() {
        var page_now = parseInt($('#page').val(), 10); // Pastikan nilai diambil sebagai angka
        var next_page = page_now - 1;
        $('#page').val(next_page);
        ShowTable(0);
    });

    /*  
    ---------------------------------------------------
    TAMBAH Kode Pemeriksaan
    --------------------------------------------------- 
    */
    $(document).on('click', '.modal_tambah', function(){
        $('#ModalTambah').modal('show');

        // Tampilkan Form Dengan AJAX
        $.ajax({
            type    : 'POST',
            url     : '_Page/Question/FormTambah.php',
            success: function(data) {
                $('#FormTambah').html(data);
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
            url     : '_Page/Question/ProsesTambah.php',
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

                    // Toast Proses Berhasil
                    $('#put_message').html('<i class="bi bi-check-circle me-2"></i> ' + message);

                    // Tampilkan Toast
                    var toastEl = document.getElementById('toast_proses');
                    var toast   = new bootstrap.Toast(toastEl, {delay: 3000});
                    toast.show();

                    //reload tabel
                    ShowTable();
                }else{
                    $('#NotifikasiTambah').html('<div class="alert alert-danger"><small>'+message+'</small></div>');
                }
                
            }
        });
    });

    /*  
    ---------------------------------------------------
    DETAIL
    --------------------------------------------------- 
    */
    $(document).on('click', '.modal_detail', function(){
        //Menangkap 'id_master_pemeriksaan'
        var id_question = $(this).data('id');

        // Menampilkan modal
        $('#ModalDetail').modal('show');

        //Menampilkan Detail Dengan AJAX
        $('#FormDetail').html('Loading...');
        $.ajax({
            type    : 'POST',
            url     : '_Page/Question/FormDetail.php',
            data    : {id_question: id_question},
            success: function(data) {
                $('#FormDetail').html(data);
            }
        });
    });

    /*  
    ---------------------------------------------------
    EDIT Kode Pemeriksaan
    --------------------------------------------------- 
    */
    $(document).on('click', '.modal_edit', function () {

        //tangkap data 'id_master_pemeriksaan' dan buat variabel
        var id_master_pemeriksaan   = $(this).data('id');

        // Load 'ShowListKategori'
        ShowListKategori();

        //tampilkan modal
        $('#ModalEdit').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiEdit').html('');

        //Form Loading
        $('#FormEdit').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Question/FormEdit.php',
            data        : {id_master_pemeriksaan: id_master_pemeriksaan},
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
            url     : '_Page/Question/ProsesEdit.php',
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
                    ShowTable();

                    // Menampilkan Swal
                    Swal.fire(
                        'Success!',
                        'Edit Kode Pemeriksaan Berhasil!',
                        'success'
                    )
                }else{
                    $('#NotifikasiEdit').html('<div class="alert alert-danger"><small>'+message+'</small></div>');
                }
                
            }
        });
    });

    /*  
    ---------------------------------------------------
    HAPUS Kode Pemeriksaan
    --------------------------------------------------- 
    */
    $(document).on('click', '.modal_delete', function () {

        //tangkap data 'id_master_pemeriksaan' dan buat variabel
        var id_master_pemeriksaan   = $(this).data('id');

        //tampilkan modal
        $('#ModalDelete').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiDelete').html('');

        //Form Loading
        $('#FormDelete').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Question/FormDelete.php',
            data        : {id_master_pemeriksaan: id_master_pemeriksaan},
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
            url     : '_Page/Question/ProsesDelete.php',
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
                        'Hapus Kode Pemeriksaan Berhasil!',
                        'success'
                    )
                }else{
                    $('#NotifikasiDelete').html('<div class="alert alert-danger"><small>'+message+'</small></div>');
                }
                
            }
        });
    });

    /*  
    ---------------------------------------------------
    SATU SEHAT
    --------------------------------------------------- 
    */
    $(document).on('click', '.modal_kirim_satu_sehat', function(){
        //Menangkap 'id_master_pemeriksaan'
        var id_question = $(this).data('id');

        // Menampilkan modal
        $('#ModalKirimSatuSehat').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiKirimSatuSehat').html('');

        //Menampilkan Detail Dengan AJAX
        $('#FormKirimSatuSehat').html('Loading...');
        $.ajax({
            type    : 'POST',
            url     : '_Page/Question/FormKirimSatuSehat.php',
            data    : {id_question: id_question},
            success: function(data) {
                $('#FormKirimSatuSehat').html(data);
            }
        });
    });

    /* Ketika 'ProsesKirimSatuSehat' disubmit */
    $('#ProsesKirimSatuSehat').submit(function(){
       
        /* Menangkap data dari form  */
        var ProsesKirimSatuSehat=$('#ProsesKirimSatuSehat').serialize();

        /* Loading Notification */
        $('#NotifikasiKirimSatuSehat').html('loading..');

        /* Kirim data dengan AJAX  */
        $.ajax({
            type    : 'POST',
            url     : '_Page/Question/ProsesKirimSatuSehat.php',
            dataType: 'json',
            data    : ProsesKirimSatuSehat,
            success: function(response) {
                var status  = response.status;
                var message = response.message;

                // Apabila berhasil
                if(status=='success'){
                    //Bersihkan notifikasi
                    $('#NotifikasiKirimSatuSehat').html('');

                    //Tutup modal
                    $('#ModalKirimSatuSehat').modal('hide');

                    //reload tabel
                    ShowTable();

                    // Menampilkan Swal
                    Swal.fire(
                        'Success!',
                        'Pertanyaan Berhasil Dikirim Berhasil!',
                        'success'
                    )
                }else{
                     let html = `
                        <div class="alert alert-danger">
                            <small><b>Error:</b> ${message}</small>
                        </div>
                    `;

                    if(response.payload){
                        html += `
                            <div class="alert alert-warning mt-2">
                                <small><b>Payload yang dikirim:</b></small>
                                <pre style="font-size:11px; max-height:200px; overflow:auto;">
                ${JSON.stringify(response.payload, null, 2)}
                                </pre>
                            </div>
                        `;
                    }

                    $('#NotifikasiKirimSatuSehat').html(html);

                }
                
            }
        });
    });

    $(document).on('click', '.modal_detail_satu_sehat', function () {

        //tangkap data 'id_master_pemeriksaan' dan buat variabel
        var id_questionnaire   = $(this).data('id');

        //tampilkan modal
        $('#ModalDetailSatuSehat').modal('show');

        //Form Loading
        $('#FormDetailSatuSehat').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Question/FormDetailSatuSehat.php',
            data        : {id_questionnaire: id_questionnaire},
            success     : function(data){
                $('#FormDetailSatuSehat').html(data);
            }
        });
    });
});