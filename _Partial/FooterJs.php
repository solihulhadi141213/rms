<!-- ======= Footer ======= -->
<!-- Vendor JS Files -->
<script src="node_modules/signature_pad/dist/signature_pad.umd.min.js"></script>
<script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/vendor/chart.js/chart.umd.js"></script>
<script src="assets/vendor/echarts/echarts.min.js"></script>
<script src="assets/vendor/quill/quill.js"></script>
<script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
<script src="assets/vendor/tinymce/tinymce.min.js"></script>
<script src="assets/vendor/php-email-form/validate.js"></script>
<script type="text/javascript" src="node_modules/jquery/dist/jquery.min.js"></script>
<script src="assets/jQuery-Mask-Plugin/dist/jquery.mask.min.js"></script>
<script src="node_modules\sweetalert2\dist\sweetalert2.all.min.js"></script>
<script type="text/javascript" src="assets/js/jquery.session.js"></script>

<script src="node_modules/html2canvas/dist/html2canvas.min.js"></script>
<script src="node_modules/jspdf/dist/jspdf.umd.min.js"></script>

<!-- Template Main JS File -->
<script src="assets/js/main.js"></script>
<script type="text/javascript">

    // Karakter Floating Button
    document.addEventListener("DOMContentLoaded", function () {
        const toggleBtn = document.querySelector(".option-toggle");
        const floatingOptions = document.querySelector(".floating-options");

        toggleBtn.addEventListener("click", function (e) {
            e.preventDefault();
            floatingOptions.classList.toggle("active");
        });
    });

    // Menampilkan dan Menyembunyikan
    function showFloatingOption() {
        const el = document.getElementById("floating_content");
        if (el) {
            el.style.display = "block";
        }
    }

    function hideFloatingOption() {
        const el = document.getElementById("floating_content");
        if (el) {
            el.style.display = "none";
        }
    }
</script>

<!-- Scan QR -->
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>

<!-- select2 -->
<script src="assets/select2/dist/js/select2.min.js"></script>
