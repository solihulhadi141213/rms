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
function ShowBasicDashboard() {
    $.ajax({
        type    : 'POST',
        url     : '_Page/Dashboard/CountDashboard.php',
        dataType: 'json',
        success: function(data) {
            $('#put_diminta').hide().html(data.Diminta).fadeIn('slow');
            $('#put_dikerjakan').hide().html(data.Dikerjakan).fadeIn('slow');
            $('#put_hasil').hide().html(data.Hasil).fadeIn('slow');
            $('#put_selesai').hide().html(data.Selesai).fadeIn('slow');
        },
        error: function(xhr, status, error) {
            console.error("Gagal mengambil data dashboard:", error);
        }
    });
}

// Fungsi untuk Menampilkan Biaya Pendidikan
function ReloadPermintaan(Periode,Keyword) {
    $.ajax({
        type: 'POST',
        url : '_Page/Dashboard/CountPermintaan.php',
        data: {Periode: Periode, Keyword: Keyword},
        success: function(data) {
            $('#put_diminta').hide().html(data.count).fadeIn('slow');
            $('#periode_permintaan').hide().html(data.periode_display).fadeIn('slow');
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

   // ===============================================
   // Background images array
    const backgrounds = [
        '../../assets/img/calendar/0b91f2f4370cf26f23e44efe7136195c.jpg',
        '../../assets/img/calendar/calendar.jpg',
        '../../assets/img/calendar/0b91f2f4370cf26f23e44efe7136195c.jpg',
        '../../assets/img/calendar/0b91f2f4370cf26f23e44efe7136195c.jpg'
    ];

    // Cache DOM elements
    const card = $('#card_jam_menarik');
    const timeElement = $('#jam_menarik');
    const dateElement = $('#tanggal_menarik');

    let currentBgIndex = 0;
    let isTransitioning = false;

    // Initialize first background
    card[0].style.setProperty('--bg-image', `url(${backgrounds[currentBgIndex]})`);
    card[0].style.setProperty('--bg-opacity', 1);

    // Function to change background with smooth transition
    function changeBackground() {
        if (isTransitioning) return;
        
        isTransitioning = true;
        
        // Calculate next index
        currentBgIndex = (currentBgIndex + 1) % backgrounds.length;
        
        // Fade out current background
        card[0].style.setProperty('--bg-opacity', 0);
        
        // Wait for fade out, then change image and fade in
        setTimeout(() => {
            card[0].style.setProperty('--bg-image', `url(${backgrounds[currentBgIndex]})`);
            
            // Force reflow for smooth transition
            card[0].offsetHeight;
            
            card[0].style.setProperty('--bg-opacity', 1);
            
            // Reset transition flag after animation completes
            setTimeout(() => {
                isTransitioning = false;
            }, 1500);
        }, 800);
    }

    // Function to update time and date
    function updateDateTime() {
        const now = new Date();
        
        // Format time (HH:MM:SS)
        const timeString = now.toLocaleTimeString('id-ID', {
            hour12: false,
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
        
        // Format date (Hari, DD MMMM YYYY)
        const dateOptions = {
            weekday: 'long',
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        };
        
        const dateString = now.toLocaleDateString('id-ID', dateOptions);
        
        // Update elements
        timeElement.text(timeString);
        dateElement.text(dateString);
    }

    // Initialize and update time every second
    updateDateTime();
    setInterval(updateDateTime, 1000);

    // Change background every 7 seconds
    setInterval(changeBackground, 7000);

    // Preload images for smoother transitions
    function preloadImages(urls) {
        urls.forEach(url => {
            const img = new Image();
            img.src = url;
        });
    }

    // Preload background images
    preloadImages(backgrounds);

    // Optional: Add click to manually change background
    card.on('click', function(e) {
        if (!$(e.target).closest('#image_menarik').length) {
            changeBackground();
        }
    });

    // =======================================================================


    //Menampilkan Data Pertama Kali
    ShowGrafik();
    ShowBasicDashboard();

    // Update setiap 10 detik
    setInterval(ShowDashboard, 10000);
    
    //Jam Menarik
    tampilkanTanggal(); // Tampilkan tanggal saat halaman dimuat
    tampilkanJam();     // Tampilkan jam pertama kali
    setInterval(tampilkanJam, 1000); // Perbarui jam setiap detik


    
});

// Ketika Reload Permintaan
$(document).on('click', '.reload_permintaan_pemeriksaan', function () {

    //tangkap data 'accession_number' dan buat variabel
    var periode   = $(this).data('periode');
    var keyword   = $(this).data('keyword');

    ReloadPermintaan(periode,keyword);
});