//Fungsi Menampilkan Data
function ShowConnectionTable() {
    $.ajax({
        type    : 'POST',
        url     : '_Page/ApiKey/TabelApiKey.php',
        success: function(data) {
            $('#tabel_koneksi').html(data);
            
            // 🔁 Re-inisialisasi tooltip setelah data dimuat
            $('[data-bs-toggle="tooltip"]').tooltip();
        }
    });
}

// Untuk Menampilkan Toast
function copyText(elementId) {
    const el = document.getElementById(elementId);
    if (!el) return;

    const text = el.innerText.trim();

    navigator.clipboard.writeText(text).then(function () {
        const toastEl = document.getElementById('toastCopy');
        const toast = new bootstrap.Toast(toastEl, {
            delay: 2000
        });
        toast.show();
    });
}

//Menampilkan Data Pertama Kali
$(document).ready(function() {
    ShowConnectionTable();

    /*  
    ---------------------------------------------------
    TAMBAH KONEKSI
    --------------------------------------------------- 
    */

    /* Ketika 'modal_tambah' di click */
    $(document).on('click', '.modal_tambah', function(){
        $('#ModalTambah').modal('show');
    });

    // Event klik generate password
    $("#generate_password").on("click", function () {

        var panjang = 10;
        var karakter = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        var password = "";

        for (var i = 0; i < panjang; i++) {
            var randomIndex = Math.floor(Math.random() * karakter.length);
            password += karakter.charAt(randomIndex);
        }

        // Set hasil password ke input
        $("#password").val(password);

    });

    // Event delegation untuk generate password (edit)
    $(document).on("click", "#generate_password_edit", function () {

        var panjang   = 10;
        var karakter  = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        var password  = "";

        for (var i = 0; i < panjang; i++) {
            var randomIndex = Math.floor(Math.random() * karakter.length);
            password += karakter.charAt(randomIndex);
        }

        // Set hasil password ke input
        $("#password_edit").val(password);

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
            url     : '_Page/ApiKey/ProsesTambah.php',
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
                        'Tambah API Key Berhasil!',
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

        //tangkap data 'id_api_account' dan buat variabel
        var id_api_account   = $(this).data('id');

        //tampilkan modal
        $('#ModalDetail').modal('show');

        //Form Loading
        $('#FormDetail').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/ApiKey/FormDetail.php',
            data        : {id_api_account: id_api_account},
            success     : function(data){
                $('#FormDetail').html(data);
            }
        });
    });

    /* MODAL EDIT */
    $(document).on('click', '.modal_edit', function () {

        //tangkap data 'id_api_account' dan buat variabel
        var id_api_account   = $(this).data('id');

        //tampilkan modal
        $('#ModalEdit').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiEdit').html('');

        //Form Loading
        $('#FormEdit').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/ApiKey/FormEdit.php',
            data        : {id_api_account: id_api_account},
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
            url     : '_Page/ApiKey/ProsesEdit.php',
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
                        'Edit API Key Berhasil!',
                        'success'
                    )
                }else{
                    $('#NotifikasiEdit').html('<div class="alert alert-danger"><small>'+message+'</small></div>');
                }
                
            }
        });
    });

    /* MODAL EDIT PASSWORD */
    $(document).on('click', '.modal_edit_password', function () {

        //tangkap data 'id_api_account' dan buat variabel
        var id_api_account   = $(this).data('id');

        //tampilkan modal
        $('#ModalEditPassword').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiEditPassword').html('');

        //Form Loading
        $('#FormEditPassword').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/ApiKey/FormEditPassword.php',
            data        : {id_api_account: id_api_account},
            success     : function(data){
                $('#FormEditPassword').html(data);
            }
        });
    });

    /* Ketika 'ProsesEditPassword' disubmit */
    $('#ProsesEditPassword').submit(function(){
       
        /* Menangkap data dari form  */
        var ProsesEditPassword=$('#ProsesEditPassword').serialize();

        /* Loading Notification */
        $('#NotifikasiEditPassword').html('loading..');

        /* Kirim data dengan AJAX  */
        $.ajax({
            type    : 'POST',
            url     : '_Page/ApiKey/ProsesEditPassword.php',
            dataType: 'json',
            data    : ProsesEditPassword,
            success: function(response) {
                var status  = response.status;
                var message = response.message;

                // Apabila berhasil
                if(status=='success'){
                    //Bersihkan notifikasi
                    $('#NotifikasiEditPassword').html('');

                    //Tutup modal
                    $('#ModalEditPassword').modal('hide');

                    //reload tabel
                    ShowConnectionTable();

                    // Menampilkan Swal
                    Swal.fire(
                        'Success!',
                        'Edit Password API Key Berhasil!',
                        'success'
                    )
                }else{
                    $('#NotifikasiEditPassword').html('<div class="alert alert-danger"><small>'+message+'</small></div>');
                }
                
            }
        });
    });

    /* MODAL DELETE */
    $(document).on('click', '.modal_delete', function () {

        //tangkap data 'id_api_account' dan buat variabel
        var id_api_account   = $(this).data('id');

        //tampilkan modal
        $('#ModalDelete').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiDelete').html('');

        //Form Loading
        $('#FormDelete').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/ApiKey/FormDelete.php',
            data        : {id_api_account: id_api_account},
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
            url     : '_Page/ApiKey/ProsesDelete.php',
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
                        'Hapus API Key Berhasil!',
                        'success'
                    )
                }else{
                    $('#NotifikasiDelete').html('<div class="alert alert-danger"><small>'+message+'</small></div>');
                }
                
            }
        });
    });

});