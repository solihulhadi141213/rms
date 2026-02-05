function ShowTableSimrs2() {

    var $container = $('#TabelSimrsV2');
    var heightBefore = $container.height(); // simpan tinggi awal

    var ProsesFilter = $('#ProsesFilterSimrs2').serialize();

    // Kunci tinggi agar layout tidak loncat
    $container
        .css({
            'min-height': heightBefore + 'px',
            'opacity': 0.5
        });

    $.ajax({
        type    : 'POST',
        url     : '_Page/Migrasi/TabelSimrsV2.php',
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

$(document).ready(function() {

    // Menampilkan SIMRS V2
    $('#ProsesFilterSimrs2').submit(function(){
        $('#page').val("1");
        ShowTableSimrs2();
    });

    // Pagging
    $(document).on('click', '#next_button', function() {
        var page_now = parseInt($('#page').val(), 10);
        var next_page = page_now + 1;
        $('#page').val(next_page);
        ShowTableSimrs2(0);
    });
    $(document).on('click', '#prev_button', function() {
        var page_now = parseInt($('#page').val(), 10);
        var next_page = page_now - 1;
        $('#page').val(next_page);
        ShowTableSimrs2(0);
    });

    // Ketika Submit Migrasi
    $('#ProsesMigrasiSimrs2').submit(function(e){
        e.preventDefault();

        // Tangkap Data
        var ProsesMigrasiSimrs2=$('#ProsesMigrasiSimrs2').serialize();

        // Tampilkan Modal
        $('#ModalMigrasi').modal('show');

        // Disabled tombol
        $("#TutupDanReload").prop("disabled", true);
        $("#ButtonMulaiMigrasi2").prop("disabled", true);

        // Ad Loading Elemnt
        $('#TabelHasilMigrasi2').html('<div class="row mb-2"><div class="col-12 text-center"><div class="alert alert-Info">LOAD DATA</div></div></div>');

        // Mulai Loading
        var $container = $('#TabelHasilMigrasi2');
        var heightBefore = $container.height(); // simpan tinggi awal

        // Kunci tinggi agar layout tidak loncat
        $container
            .css({
                'min-height': heightBefore + 'px',
                'opacity': 0.5
            });

        $.ajax({
            type    : 'POST',
            url     : '_Page/Migrasi/ProsesMigrasi2.php',
            data    : ProsesMigrasiSimrs2,
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

    });

    // tutup Dan Reload Modal
    $(document).on('click', '#TutupDanReload', function() {
        // Tutup Modal
        $('#ModalMigrasi').modal('hide');

        // Reload Data
       ShowTableSimrs2(0);
    });
});