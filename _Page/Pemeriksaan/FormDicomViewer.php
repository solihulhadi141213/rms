<?php
    //koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";
    include "../../_Config/SettingGeneral.php";
    
    //Zona Waktu
    date_default_timezone_set("Asia/Jakarta");

    //Session Akses
    if(empty($SessionIdAccess)){
        echo '
            <div class="row mb-3">
                <div class="col-12 text-center">
                    <div class="alert alert-danger"><small>Sesi Akses Sudah Berakhir! Silahkan Login Ulang.</small></div>
                </div>
            </div>
        ';
        exit;
    }

    //id_radiologi_dicom_conv wajib terisi
    if(empty($_POST['id_radiologi_dicom_conv'])){
        echo '
            <div class="row mb-3">
                <div class="col-12 text-center">
                    <div class="alert alert-danger"><small>ID File Tidak Boleh Kosong!</small></div>
                </div>
            </div>
        ';
        exit;
    }

    //Buat variabel 'id_radiologi_dicom_conv' dan sanitasi
    $id_radiologi_dicom_conv = validateAndSanitizeInput($_POST['id_radiologi_dicom_conv']);

    //Buka Detail Koneksi Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM radiologi_dicom_conv WHERE id_radiologi_dicom_conv = ?");
    $Qry->bind_param("s", $id_radiologi_dicom_conv);
    if (!$Qry->execute()) {
        $error=$Conn->error;
        echo '
            <div class="alert alert-danger">
                <small>Terjadi kesalahan pada saat membuka data dari database!<br>Keterangan : '.$error.'</small>
            </div>
        ';
        exit;
    }
    $Result = $Qry->get_result();
    $Data = $Result->fetch_assoc();
    $Qry->close();

    // Jika Data Tidak Ditemukan
    if(empty($Data['id_radiologi_dicom_conv'])){
        echo '
            <div class="row mb-3">
                <div class="col-12 text-center">
                    <div class="alert alert-danger"><small>ID File Tiidak Valid!</small></div>
                </div>
            </div>
        ';
        exit;
    }

    //Buat Variabel
    $id_radiologi     = $Data['id_radiologi'];
    $accession_number = $Data['accession_number'];
    $filename         = $Data['filename'];
    $dicom_metadata   = $Data['dicom_metadata'];

    // Parse metadata untuk mendapatkan info pasien
    $metadata_array = json_decode($dicom_metadata, true);
    $patient_name = isset($metadata_array['PatientName']) ? $metadata_array['PatientName'] : 'Unknown';
    
    // Menentukan URL
    $url = "$app_base_url/DicomViewer.php?id=$id_radiologi_dicom_conv";
?>
<style>
    .dicom-viewer-wrapper {
        width: 100%;
        height: 100%;
        position: relative;
        overflow: hidden;
    }
    
    .dicom-viewer-iframe {
        width: 100%;
        height: 100%;
        border: none;
        background: #000;
        border-radius: 4px;
    }
    
    .dicom-loading {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.8);
        display: flex;
        justify-content: center;
        align-items: center;
        color: white;
        z-index: 10;
    }
    
    .dicom-info {
        position: absolute;
        top: 10px;
        left: 10px;
        background: rgba(0,0,0,0.7);
        color: white;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 12px;
        z-index: 5;
        max-width: 300px;
    }
</style>

<div class="dicom-viewer-wrapper">
    <div class="dicom-info">
        <small>
            <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($patient_name); ?><br>
            <i class="bi bi-file-earmark-medical"></i> <?php echo htmlspecialchars($filename); ?>
        </small>
    </div>
    
    <div class="dicom-loading" id="loadingOverlay">
        <div class="text-center">
            <div class="spinner-border text-primary mb-2" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p>Memuat DICOM Viewer...</p>
        </div>
    </div>
    
    <iframe
        id="dicomIframe"
        class="dicom-viewer-iframe"
        src="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8'); ?>"
        title="DICOM Viewer"
        allowfullscreen
        loading="lazy"
        onload="
            var overlay = document.getElementById('loadingOverlay');
            if (overlay) overlay.style.display = 'none';
        "
        onerror="
            var overlay = document.getElementById('loadingOverlay');
            if (overlay) {
                overlay.innerHTML =
                    '<div class=\'text-center text-danger\'>' +
                        '<i class=\'bi bi-exclamation-triangle fs-3\'></i>' +
                        '<p class=\'mt-2 mb-0\'>Gagal memuat viewer DICOM</p>' +
                    '</div>';
            }
        ">
    </iframe>
</div>

<script>
    // Fungsi untuk menyesuaikan tinggi iframe
    function adjustDicomIframeHeight() {
        const wrapper = document.querySelector('.dicom-viewer-wrapper');
        const iframe = document.getElementById('dicomIframe');
        
        if (wrapper && iframe) {
            // Hitung tinggi berdasarkan modal
            const modalContent = document.querySelector('#ModalDicomViewer .modal-content');
            const modalHeader = document.querySelector('#ModalDicomViewer .modal-header');
            const modalFooter = document.querySelector('#ModalDicomViewer .modal-footer');
            
            if (modalContent && modalHeader && modalFooter) {
                const headerHeight = modalHeader.offsetHeight;
                const footerHeight = modalFooter.offsetHeight;
                const contentHeight = modalContent.offsetHeight;
                const iframeHeight = contentHeight - headerHeight - footerHeight;
                
                wrapper.style.height = iframeHeight + 'px';
                iframe.style.height = iframeHeight + 'px';
            } else {
                // Fallback: gunakan window height
                const windowHeight = window.innerHeight;
                wrapper.style.height = (windowHeight - 200) + 'px';
                iframe.style.height = (windowHeight - 200) + 'px';
            }
        }
    }
    
    // Panggil fungsi saat halaman dimuat
    document.addEventListener('DOMContentLoaded', adjustDicomIframeHeight);
    
    // Panggil fungsi saat modal ditampilkan
    document.getElementById('ModalDicomViewer').addEventListener('shown.bs.modal', function() {
        setTimeout(adjustDicomIframeHeight, 100);
    });
    
    // Handle resize window
    window.addEventListener('resize', adjustDicomIframeHeight);
    
    // Fullscreen support
    document.getElementById('dicomIframe').addEventListener('load', function() {
        // Tambahkan event untuk komunikasi dengan iframe jika diperlukan
        const iframe = this;
        
        // Enable fullscreen pada iframe
        iframe.allow = "fullscreen";
    });
</script>