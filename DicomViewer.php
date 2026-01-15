<?php
    include "_Config/Connection.php";
    include "_Config/GlobalFunction.php";
    include "_Config/SettingGeneral.php";

    // Function Tambahan
    function formatBytes($bytes, $precision = 2) {
        $units  = array('B', 'KB', 'MB', 'GB', 'TB');
        $bytes  = max($bytes, 0);
        $pow    = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow    = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    // ID wajib terisi
    if(empty($_GET['id'])){
        echo '<div class="alert alert-danger">ID File Tidak Boleh Kosong!</div>';
        exit;
    }

    // Buat Variabel dan Sanitasi
    $id_radiologi_dicom_conv = validateAndSanitizeInput($_GET['id']);
    
    // Query database untuk mendapatkan data file DICOM
    $query = "SELECT * FROM radiologi_dicom_conv WHERE id_radiologi_dicom_conv = ?";
    $stmt  = $Conn->prepare($query);
    $stmt->bind_param("s", $id_radiologi_dicom_conv);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows === 0){
        echo '<div class="alert alert-danger">ID File Tidak Valid Atau Tidak Ada Pada Database!</div>';
        exit;
    }
    
    $dicom_data   = $result->fetch_assoc();
    $file_dcm     = $dicom_data['filename'];
    $id_radiologi = $dicom_data['id_radiologi'];
    
    // Buka Informasi Pasien
    $id_pasien    = GetDetailData($Conn, 'radiologi', 'id_radiologi', $id_radiologi, 'id_pasien');
    $nama_pasien  = GetDetailData($Conn, 'radiologi', 'id_radiologi', $id_radiologi, 'nama_pasien');
    $asal_kiriman = GetDetailData($Conn, 'radiologi', 'id_radiologi', $id_radiologi, 'asal_kiriman');
    
    // Menentukan Path File
    $base_path = '_DCM/';
    $path = realpath($base_path . $file_dcm);
    
    // Validasi file exists
    if (!$path || !file_exists($path)) {
        echo '<div class="alert alert-danger">File DICOM tidak ditemukan di server.</div>';
        exit;
    }

    // Validasi file DICOM
    $isValidDicom = false;
    $handle = @fopen($path, 'rb');
    if ($handle) {
        fseek($handle, 128);
        $prefix = fread($handle, 4);
        fclose($handle);
        
        if ($prefix === 'DICM') {
            $isValidDicom = true;
        }
    }
    
    if (!$isValidDicom) {
        echo '<div class="alert alert-warning">File tidak valid format DICOM. Prefix tidak sesuai.</div>';
    }
    
    $file_size     = formatBytes(filesize($path));
    $last_modified = date("Y-m-d H:i:s", filemtime($path));
    
    // URL untuk file DICOM (relative path)
    $dicom_url = $base_path . rawurlencode($file_dcm);
?>
<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>DICOM Viewer - <?php echo htmlspecialchars($file_dcm); ?></title>
        
        <!-- Local CSS Libraries -->
        <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
        <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
        <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
        
        <!-- Cornerstone CSS -->
        <link rel="stylesheet" href="https://unpkg.com/cornerstone-core@2.6.0/dist/cornerstone.css">
        
        <!-- Custom CSS -->
        <link href="assets/css/dcm_viewer_style.css?v=<?php echo date('YmdHis'); ?>" rel="stylesheet">
        
        <style>
            /* Additional inline styles for viewer */
            #dicomViewer canvas {
                display: block;
                margin: 0 auto;
            }
            .cornerstone-canvas {
                cursor: crosshair !important;
            }
        </style>
    </head>
    <body>
        <div class="container-fluid main-container">
            <!-- Header -->
            <div class="header-bar">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">
                            <i class="bi bi-file-medical me-2"></i> DICOM Viewer
                        </h4>
                        <small class="text-light opacity-75">
                            <?php echo htmlspecialchars($file_dcm); ?>
                        </small>
                    </div>
                    <div>
                        <button class="btn btn-light btn-sm" onclick="downloadOriginalFile()">
                            <i class="bi bi-download me-1"></i> Download DICOM
                        </button>
                    </div>
                </div>
            </div>
            <input type="hidden" id="dicom_url" value="<?php echo $dicom_url; ?>">
            <div class="row">
                <!-- Kolom Kiri: Viewer -->
                <div class="col-lg-8">
                    <!-- Info Pasien -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <b class="card-title">
                                        <i class="bi bi-person-circle me-2"></i> Informasi Pasien
                                    </b>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-2">
                                        <div class="col-4"><small>No.RM</small></div>
                                        <div class="col-1"><small>:</small></div>
                                        <div class="col-7"><small><?php echo $id_pasien; ?></small></div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-4"><small>Nama Pasien</small></div>
                                        <div class="col-1"><small>:</small></div>
                                        <div class="col-7"><small><?php echo $nama_pasien; ?></small></div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-4"><small>Asal Kiriman</small></div>
                                        <div class="col-1"><small>:</small></div>
                                        <div class="col-7"><small><?php echo $asal_kiriman; ?></small></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-card">
                                <h6><i class="bi bi-info-circle me-2"></i> Informasi File</h6>
                                <table class="table table-sm mb-0">
                                    <tbody>
                                        <tr>
                                            <th>Ukuran File</th>
                                            <td><?php echo $file_size; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Terakhir Diubah</th>
                                            <td><?php echo $last_modified; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Status Validasi</th>
                                            <td>
                                                <?php if($isValidDicom): ?>
                                                    <span class="badge bg-success">Valid DICOM</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning">Format Tidak Dikenal</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- DICOM Viewer -->
                    <div class="viewer-wrapper mb-3">
                        <div class="loading-overlay" id="loadingOverlay">
                            <div class="text-center">
                                <div class="spinner-border text-primary mb-3" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p>Memuat gambar DICOM...</p>
                                <div class="progress mt-3" style="width: 200px; margin: 0 auto;">
                                    <div id="loadProgress" class="progress-bar" role="progressbar" style="width: 0%"></div>
                                </div>
                            </div>
                        </div>
                        <div id="dicomViewer"></div>
                        <div class="measurement-display" id="measurementDisplay" style="display: none;"></div>
                        <div class="pixel-info" id="pixelInfo" style="display: none;"></div>
                        <div class="image-info" id="imageInfo" style="display: none;"></div>
                    </div>
                </div>

                <!-- Kolom Kanan: Kontrol dan Metadata -->
                <div class="col-lg-4">
                    <!-- Panel Kontrol -->
                    <div class="control-panel mb-3">
                        <h6><i class="bi bi-sliders me-2"></i> Kontrol Gambar</h6>
                        
                        <!-- Tool Selection -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Alat Interaksi:</label>
                            <div class="btn-group d-flex mb-2" role="group">
                                <button type="button" class="btn btn-outline-primary btn-tool" id="toolWwwc" title="Window/Level">
                                    <i class="bi bi-brightness-alt-high"></i>
                                </button>
                                <button type="button" class="btn btn-outline-primary btn-tool" id="toolZoom" title="Zoom">
                                    <i class="bi bi-zoom-in"></i>
                                </button>
                                <button type="button" class="btn btn-outline-primary btn-tool" id="toolPan" title="Pan">
                                    <i class="bi bi-arrows-move"></i>
                                </button>
                                <button type="button" class="btn btn-outline-primary btn-tool" id="toolLength" title="Ukur Jarak">
                                    <i class="bi bi-rulers"></i>
                                </button>
                            </div>
                            
                            <div class="btn-group d-flex mb-2" role="group">
                                <button type="button" class="btn btn-outline-secondary btn-tool" id="toolAngle" title="Ukur Sudut">
                                    <i class="bi bi-angle-90"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-tool" id="toolRotate" title="Rotate">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-tool" id="toolFlipH" title="Flip Horizontal">
                                    <i class="bi bi-arrow-left-right"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-tool" id="toolFlipV" title="Flip Vertical">
                                    <i class="bi bi-arrow-up-down"></i>
                                </button>
                            </div>
                            
                            <div class="btn-group d-flex" role="group">
                                <button type="button" class="btn btn-outline-secondary btn-tool" id="toolInvert" title="Invert Warna">
                                    <i class="bi bi-contrast"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-tool" id="toolReset" title="Reset View">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-tool" id="toolClear" title="Hapus Pengukuran">
                                    <i class="bi bi-trash"></i>
                                </button>
                                <button type="button" class="btn btn-outline-success btn-tool" id="toolCapture" title="Capture & Print">
                                    <i class="bi bi-camera"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Window/Level Controls -->
                        <div class="slider-container">
                            <div class="slider-label">
                                <span><i class="bi bi-brightness-high me-1"></i> Window Width</span>
                                <span id="wwValue" class="badge bg-secondary">400</span>
                            </div>
                            <input type="range" class="form-range" id="windowWidth" min="1" max="4096" value="400" step="1">
                        </div>
                        
                        <div class="slider-container">
                            <div class="slider-label">
                                <span><i class="bi bi-circle-half me-1"></i> Window Center</span>
                                <span id="wcValue" class="badge bg-secondary">40</span>
                            </div>
                            <input type="range" class="form-range" id="windowCenter" min="-1024" max="3071" value="40" step="1">
                        </div>
                        
                        <!-- Zoom Controls -->
                        <div class="slider-container">
                            <div class="slider-label">
                                <span><i class="bi bi-zoom-in me-1"></i> Zoom Level</span>
                                <span id="zoomValue" class="badge bg-secondary">100%</span>
                            </div>
                            <input type="range" class="form-range" id="zoomSlider" min="10" max="500" value="100" step="1">
                        </div>
                        
                        <!-- Rotate Controls -->
                        <div class="slider-container">
                            <div class="slider-label">
                                <span><i class="bi bi-arrow-clockwise me-1"></i> Rotate</span>
                                <span id="rotateValue" class="badge bg-secondary">0°</span>
                            </div>
                            <input type="range" class="form-range" id="rotateSlider" min="0" max="360" value="0" step="1">
                        </div>
                        
                        <!-- Preset WW/WC -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Preset Window:</label>
                            <select class="form-select form-select-sm" id="wwPreset">
                                <option value="custom">Custom</option>
                                <option value="lung">Paru-paru (1500/-600)</option>
                                <option value="bone">Tulang (2000/500)</option>
                                <option value="brain">Otak (80/40)</option>
                                <option value="abdomen">Abdomen (400/50)</option>
                                <option value="mediastinum">Mediastinum (350/50)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Panel Metadata DICOM -->
                    <div class="control-panel">
                        <h6><i class="bi bi-table me-2"></i> Metadata DICOM</h6>
                        <div class="table-responsive">
                            <table class="table table-sm metadata-table">
                                <tbody id="metadataTable">
                                    <tr>
                                        <td colspan="2" class="text-center">
                                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            Memuat metadata...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Local JavaScript Libraries -->
        <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="node_modules/jquery/dist/jquery.min.js"></script>
        <script src="assets/jQuery-Mask-Plugin/dist/jquery.mask.min.js"></script>
        <script src="node_modules/sweetalert2/dist/sweetalert2.all.min.js"></script>
        <script type="text/javascript" src="assets/js/jquery.session.js"></script>
        
        <!-- Hammer.js untuk touch support -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/hammer.js/2.0.8/hammer.min.js"></script>
        
        <!-- Cornerstone DICOM Libraries -->
        <script src="https://unpkg.com/cornerstone-core"></script>
        <script src="https://unpkg.com/cornerstone-math"></script>
        <script src="https://unpkg.com/cornerstone-wado-image-loader"></script>
        <script src="https://unpkg.com/dicom-parser"></script>
        <script src="https://unpkg.com/cornerstone-tools"></script>

        <!-- html2canvas for capture -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
        
        <!-- Custom DICOM Viewer JavaScript -->
        <script src="_Partial/dicom_viewer.js"></script>
    </body>
</html>
<?php
    // Clean up
    if (isset($stmt)) {
        $stmt->close();
    }
?>