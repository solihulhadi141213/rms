//Fungsi Menampilkan Data Kunjungan
function ShowTableDurasiPelayanan() {

    var $container = $('#TabelLaporanDurasiPelayanan');
    var heightBefore = $container.height(); // simpan tinggi awal

    var ProsesFilterDurasiPelayanan = $('#ProsesFilterDurasiPelayanan').serialize();

    // Kunci tinggi agar layout tidak loncat
    $container
        .css({
            'min-height': heightBefore + 'px',
            'opacity': 0.5
        });

    $.ajax({
        type    : 'POST',
        url     : '_Page/Laporan/TabelLaporanDurasiPelayanan.php',
        data    : ProsesFilterDurasiPelayanan,
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
    ShowTableDurasiPelayanan();

    /*  
    ---------------------------------------------------
    MODAL FILTER
    --------------------------------------------------- 
    */
    $(document).on('click', '.modal_filter_durasi_pelayanan', function(){
        $('#ModalFilterDurasiPelayanan').modal('show');
    });

    //Ketika keyword_by diubah
    $('#periode_durasi_pelayanan').change(function(){
        var periode_durasi_pelayanan =$('#periode_durasi_pelayanan').val();
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Laporan/FormLanjutanDurasiPelayanan.php',
            data        : {periode_durasi_pelayanan: periode_durasi_pelayanan},
            success     : function(data){
                $('#form_lanjutan_durasi_pelayanan').html(data);
            }
        });
    });

    //Proses Filter/Pencarian
    $('#ProsesFilterDurasiPelayanan').submit(function(){
        ShowTableDurasiPelayanan();
        $('#ModalFilterDurasiPelayanan').modal('hide');
    });

    //Ketika di click modal_export_laporan_durasi_pelayanan
    $(document).on('click', '#modal_export_laporan_durasi_pelayanan', function(){
        
        //Menangkap Data Dari Form Filter
       var ProsesFilterDurasiPelayanan = $('#ProsesFilterDurasiPelayanan').serialize();

        // Tampilkan Modal Export
        $('#ModalExportLaporanDurasiPelayanan').modal('show');

        // Loading Form
        $('#FormExportLaporanDurasiPelayanan').html('Loading..');

        // Tampilkan Form Dengan AJAX
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Laporan/FormExportLaporanDurasiPelayanan.php',
            data        : ProsesFilterDurasiPelayanan,
            success     : function(data){
                $('#FormExportLaporanDurasiPelayanan').html(data);
            }
        });
    });

    //Ketika di click modal_rincian_durasi_pelayanan
    $(document).on('click', '.modal_rincian_durasi_pelayanan', function(){

        //Menangkap 'id'
        var periode = $(this).data('periode');
        var tahun   = $(this).data('tahun');
        var bulan   = $(this).data('bulan');
        var tanggal   = $(this).data('tanggal');

        // Tampilkan Modal
        $('#ModalRincianDurasiPelayanan').modal('show');

        // Loading Form
        $('#FormRincianDurasiPelayanan').html('Loading..');

        // Tampilkan Form Dengan AJAX
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Laporan/FormRincianDurasiPelayanan.php',
            data        : {periode: periode, tahun: tahun, bulan: bulan, tanggal: tanggal},
            success     : function(data){
                $('#FormRincianDurasiPelayanan').html(data);
            }
        }); 
    });

    

    /*  
    ---------------------------------------------------
    DETAIL Kode Pemeriksaan
    --------------------------------------------------- 
    */
    $(document).on('click', '.modal_detail', function(){
        //Menangkap 'id_master_pemeriksaan'
        var id_master_pemeriksaan = $(this).data('id');

        // Menampilkan modal
        $('#ModalDetail').modal('show');

        //Menampilkan Detail Dengan AJAX
        $('#FormDetail').html('Loading...');
        $.ajax({
            type    : 'POST',
            url     : '_Page/KodePemeriksaan/FormDetail.php',
            data    : {id_master_pemeriksaan: id_master_pemeriksaan},
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
            url 	    : '_Page/KodePemeriksaan/FormEdit.php',
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
            url     : '_Page/KodePemeriksaan/ProsesEdit.php',
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
            url 	    : '_Page/KodePemeriksaan/FormDelete.php',
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
            url     : '_Page/KodePemeriksaan/ProsesDelete.php',
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
});