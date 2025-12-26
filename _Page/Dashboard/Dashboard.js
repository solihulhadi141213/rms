// Fungsi Untuk Menampilkan Grafik
function ShowGrafik() {
    $.getJSON("_Page/Dashboard/GrafikTransaksi.php", function (data) {
        const categories = data.map(item => item.x);
        const seriesData = data.map(item => parseInt(item.y));

        var options = {
            chart: {
                type: 'area',
                height: 400
            },
            series: [{
                name: 'Permintaan',
                data: seriesData
            }],
            xaxis: {
                categories: categories
            },
            yaxis: {
                labels: {
                    formatter: function (value) {
                        return Math.round(value);
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function (value) {
                        return Math.round(value) + ' Permintaan';
                    }
                }
            },
            dataLabels: {
                enabled: false
            },
            title: {
                text: 'Grafik Pelayanan Radiologi ' + new Date().getFullYear(),
                align: 'center'
            }
        };

        var chart = new ApexCharts(
            document.querySelector("#chart"),
            options
        );
        chart.render();
    });
}


// Fungsi untuk menampilkan jam digital
function tampilkanJam() {
    const waktu = new Date();
    let jam = waktu.getHours().toString().padStart(2, '0');
    let menit = waktu.getMinutes().toString().padStart(2, '0');
    let detik = waktu.getSeconds().toString().padStart(2, '0');

    $('#jam_menarik').text(`${jam}:${menit}:${detik}`);
}

// Fungsi untuk menampilkan tanggal
function tampilkanTanggal() {
    const waktu = new Date();
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    const tanggal = waktu.toLocaleDateString('id-ID', options);
    
    $('#tanggal_menarik').text(tanggal);
}

// Fungsi untuk menampilkan dashboard
function ShowDashboard() {
    $.ajax({
        type: 'POST',
        url: '_Page/Dashboard/CountDashboard.php',
        dataType: 'json',
        success: function(data) {
            $('#put_pengguna').hide().html(data.user).fadeIn('slow');
            $('#put_siswa_aktif').hide().html(data.siswa).fadeIn('slow');
            $('#put_periode_akademik').hide().html(data.periode).fadeIn('slow');
            $('#put_pembayaran').hide().html(data.pembayaran).fadeIn('slow');
        },
        error: function(xhr, status, error) {
            console.error("Gagal mengambil data dashboard:", error);
        }
    });
}

// Fungsi untuk Menampilkan Biaya Pendidikan
function ShowRiwayatTagihan() {
    $.ajax({
        type: 'POST',
        url: '_Page/Dashboard/TableTagihan.php',
        success: function(data) {
            $('#ShowRiwayatTagihan').hide().html(data).fadeIn('slow');
        },
        error: function(xhr, status, error) {
            console.error("Gagal mengambil data dashboard:", error);
        }
    });
}

// Fungsi untuk Menampilkan Pembayaran
function ShowRiwayatPembayaran() {
    $.ajax({
        type: 'POST',
        url: '_Page/Dashboard/TabelPembayaran.php',
        success: function(data) {
            $('#ShowRiwayatPembayaran').hide().html(data).fadeIn('slow');
        },
        error: function(xhr, status, error) {
            console.error("Gagal mengambil data dashboard:", error);
        }
    });
}

$(document).ready(function () {

    const backgrounds = [
        'assets/img/bg/0b91f2f4370cf26f23e44efe7136195c.jpg',
        'assets/img/bg/calendar.jpg',
        'assets/img/bg/bg3.jpg',
        'assets/img/bg/bg4.jpg'
    ];

    let index = 0;
    const card = $('#card_jam_menarik');

    // Set background awal
    card[0].style.setProperty(
        '--bg-image',
        `url(${backgrounds[index]})`
    );

    // Set via pseudo-element
    $('#card_jam_menarik::before');

    function gantiBackground() {
        index = (index + 1) % backgrounds.length;

        // Fade out
        card.css('--bg-opacity', 0);

        setTimeout(() => {
            card[0].style.setProperty(
                '--bg-image',
                `url(${backgrounds[index]})`
            );
            card.css('--bg-opacity', 1);
        }, 800);
    }

    // Inisialisasi
    card[0].style.setProperty(
        'background-image',
        `url(${backgrounds[index]})`
    );

    setInterval(gantiBackground, 7000); // 7 detik


    //Menampilkan Data Pertama Kali
    ShowGrafik();
    ShowDashboard();
    ShowRiwayatTagihan();
    ShowRiwayatPembayaran();

    ShowDashboard();
    // Update setiap 10 detik
    setInterval(ShowDashboard, 10000);
    
    //Jam Menarik
    tampilkanTanggal(); // Tampilkan tanggal saat halaman dimuat
    tampilkanJam();     // Tampilkan jam pertama kali
    setInterval(tampilkanJam, 1000); // Perbarui jam setiap detik
});