//Fungsi Menampilkan Data Kunjungan
function ShowTable() {

    var $container = $('#TabelTagihan');
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
        url     : '_Page/Tagihan/TabelTagihan.php',
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

// Fungsi Untuk Menampilkan Detail Invoice Dengan Lembut
function ShowDetailInvoice(id_radiologi) {
    $('#FormDetail').fadeOut(150, function () {
        $(this).html(`
            <div class="d-flex justify-content-center align-items-center" style="min-height:200px">
                <div class="spinner-border text-primary"></div>
            </div>
        `).fadeIn(150);

        $.post('_Page/Tagihan/FormDetail.php', { id_radiologi }, function (data) {
            $('#FormDetail').fadeOut(150, function () {
                $(this).html(data).fadeIn(150);
            });
        });
    });
}

// Fungsi Untuk Menampilkan Format Uang
function parseMoney(value) {
    if (!value) return 0;
    return parseFloat(
        value
            .replace(/\./g, '')   // hapus ribuan
            .replace(',', '.')    // ganti koma ke titik
    ) || 0;
}

// Fungsi Untuk Menghitung Biaya
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

// ------------------------------------------------------------
//MENAMPILKAN DATA PERTAMA KALI
// ------------------------------------------------------------
$(document).ready(function() {
    ShowTable();

    // Modal Filter
    $(document).on('click', '.modal_filter', function(){
        $('#ModalFilter').modal('show');
    });

    //Ketika keyword_by diubah
    $('#KeywordBy').change(function(){
        var KeywordBy =$('#KeywordBy').val();
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Tagihan/FormFilter.php',
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
    DETAIL INVOICE
    --------------------------------------------------- 
    */
    $(document).on('click', '.modal_detail', function(){
        //Menangkap 'id_radiologi'
        var id_radiologi = $(this).data('id');

        // Menampilkan modal
        $('#ModalDetail').modal('show');

        //Menampilkan Detail Dengan Menggunakan Function
        ShowDetailInvoice(id_radiologi);
    });

    /*  
    ---------------------------------------------------
    EXPORT TAGIHAN
    --------------------------------------------------- 
    */
    $(document).on('click', '.modal_export', function () {
       $('#ModalExport').modal('show');
    });

    /*  
    ---------------------------------------------------
    TAMBAH TAGIHAN
    --------------------------------------------------- 
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
                var id_radiologi  = response.id_radiologi;
                var message = response.message || 'Proses berhasil';

                if(status === 'success'){

                    // Bersihkan notifikasi
                    $('#NotifikasiTambahTagihan').html('');

                    // Tutup modal jika ada
                    $('#ModalTambahTagihan').modal('hide');

                    // Reload detail Invoice
                    ShowDetailInvoice(id_radiologi);

                    // Reload Tabel
                    ShowTable();

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

    /*  
    ---------------------------------------------------
    EDIT TAGIHAN
    --------------------------------------------------- 
    */

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

                var status       = response.status;
                var id_radiologi = response.id_radiologi;
                var message      = response.message || 'Proses berhasil';

                if(status === 'success'){

                    // Bersihkan notifikasi
                    $('#NotifikasiEditTagihan').html('');

                    // Tutup modal jika ada
                    $('#ModalEditTagihan').modal('hide');

                    // Reload detail Invoice
                    ShowDetailInvoice(id_radiologi);

                    // Reload Tabel
                    ShowTable();

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

    /*  
    ---------------------------------------------------
    HAPUS TAGIHAN
    --------------------------------------------------- 
    */

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

                var status       = response.status;
                var id_radiologi = response.id_radiologi;
                var message      = response.message || 'Proses berhasil';

                if(status === 'success'){

                    // Bersihkan notifikasi
                    $('#NotifikasiHapusNota').html('');

                    // Tutup modal jika ada
                    $('#ModalHapusNota').modal('hide');

                    // Reload detail Invoice
                    ShowDetailInvoice(id_radiologi);

                    // Reload Tabel
                    ShowTable();

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

});