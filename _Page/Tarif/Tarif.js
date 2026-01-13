//Fungsi Menampilkan Data Kunjungan
function ShowTable() {

    var $container = $('#TabelTarif');
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
        url     : '_Page/Tarif/TabelTarif.php',
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
        url     : '_Page/Tarif/service_category_list.php',
        success: function(data) {
            $('#service_category_list').html(data);
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
    ShowTable();

    // MODAL FILTER
    $(document).on('click', '.modal_filter', function(){
        $('#ModalFilter').modal('show');
    });

    //Ketika keyword_by diubah
    $('#KeywordBy').change(function(){
        var KeywordBy =$('#KeywordBy').val();
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Tarif/FormFilter.php',
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

    // MODAL TAMBAH TARIF
    $(document).on('click', '.modal_tambah', function(){
        $('#ModalTambah').modal('show');
        ShowListKategori();
        initializeMoneyInputs();
    });

    // Listener perubahan input
    $(document).on('input', '#base_price, #doctor_fee, #radiographers_fee, #facility_fee, #equipment_fee, #quantity, #total_price', 
        function () {
            hitungTotalHarga();
        }
    );


    /* Ketika 'ProsesTambah' disubmit */
    $('#ProsesTambah').submit(function(){
       
        /* Menangkap data dari form  */
        var ProsesTambah=$('#ProsesTambah').serialize();

        /* Loading Notification */
        $('#NotifikasiTambah').html('loading..');

        /* Kirim data dengan AJAX  */
        $.ajax({
            type    : 'POST',
            url     : '_Page/Tarif/ProsesTambah.php',
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
                    $('#ProsesFilter')[0].reset();

                    //Tutup modal
                    $('#ModalTambah').modal('hide');

                    //Menampilkan Swal
                    Swal.fire(
                        'Success!',
                        'Tambah Tarif Pemeriksaan Berhasil!',
                        'success'
                    )

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
    DETAIL TARIF
    --------------------------------------------------- 
    */
    $(document).on('click', '.modal_detail', function(){
        //Menangkap 'id_master_service_prices'
        var id_master_service_prices = $(this).data('id');

        // Menampilkan modal
        $('#ModalDetail').modal('show');

        //Menampilkan Detail Dengan AJAX
        $('#FormDetail').html('Loading...');
        $.ajax({
            type    : 'POST',
            url     : '_Page/Tarif/FormDetail.php',
            data    : {id_master_service_prices: id_master_service_prices},
            success: function(data) {
                $('#FormDetail').html(data);
            }
        });
    });

    /*  
    ---------------------------------------------------
    EDIT TARIF
    --------------------------------------------------- 
    */
    $(document).on('click', '.modal_edit', function () {

        //tangkap data 'id_master_service_prices' dan buat variabel
        var id_master_service_prices   = $(this).data('id');

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
            url 	    : '_Page/Tarif/FormEdit.php',
            data        : {id_master_service_prices: id_master_service_prices},
            success     : function(data){
                $('#FormEdit').html(data);
                initializeMoneyInputs();
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
            url     : '_Page/Tarif/ProsesEdit.php',
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
                        'Edit Tarif Pemeriksaan Berhasil!',
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
    HAPUS Tarif Pemeriksaan
    --------------------------------------------------- 
    */
    $(document).on('click', '.modal_delete', function () {

        //tangkap data 'id_master_service_prices' dan buat variabel
        var id_master_service_prices   = $(this).data('id');

        //tampilkan modal
        $('#ModalDelete').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiDelete').html('');

        //Form Loading
        $('#FormDelete').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Tarif/FormDelete.php',
            data        : {id_master_service_prices: id_master_service_prices},
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
            url     : '_Page/Tarif/ProsesDelete.php',
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
                        'Hapus Tarif Pemeriksaan Berhasil!',
                        'success'
                    )
                }else{
                    $('#NotifikasiDelete').html('<div class="alert alert-danger"><small>'+message+'</small></div>');
                }
                
            }
        });
    });
});