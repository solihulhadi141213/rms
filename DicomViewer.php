<?php
    include "_Config/Connection.php";
    include "_Config/GlobalFunction.php";
    include "_Config/SettingGeneral.php";

    // ID wajib terisi
    if(empty($_GET['id'])){
        echo '<div class="alert alert-danger">ID File Tidak Boleh Kosong!</div>';
        exit;
    }

    // Buat Variabel dan Sanitasi
    $id_radiologi_dicom_conv = validateAndSanitizeInput($_GET['id']);
    
    // Query database untuk mendapatkan data file DICOM
    $query = "SELECT * FROM radiologi_dicom_conv WHERE id_radiologi_dicom_conv = ?";
    $stmt = $Conn->prepare($query);
    $stmt->bind_param("s", $id_radiologi_dicom_conv);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows === 0){
        echo '<div class="alert alert-danger">ID File Tidak Valid Atau Tidak Ada Pada Database!</div>';
        exit;
    }
    
    $dicom_data = $result->fetch_assoc();
    $file_dcm = $dicom_data['filename'];
    
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
        // Tapi tetap lanjut, kadang ada DICOM tanpa prefix
    }
    
    // Format bytes untuk display
    function formatBytes($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
    
    $file_size = formatBytes(filesize($path));
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
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Cornerstone DICOM Libraries -->
    <link rel="stylesheet" href="https://unpkg.com/cornerstone-core@2.6.0/dist/cornerstone.css">
    
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .main-container {
            min-height: 100vh;
            padding: 15px;
        }
        
        .viewer-wrapper {
            background: #000;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            height: 600px;
            position: relative;
        }
        
        #dicomViewer {
            width: 100%;
            height: 100%;
            cursor: crosshair;
        }
        
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.85);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            color: white;
        }
        
        .spinner-border {
            width: 3rem;
            height: 3rem;
        }
        
        .info-card {
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .control-panel {
            background: white;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .btn-tool {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 2px;
        }
        
        .slider-container {
            margin-bottom: 15px;
        }
        
        .slider-label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        
        .metadata-table {
            font-size: 13px;
        }
        
        .metadata-table th {
            width: 40%;
            background-color: #f8f9fa;
            font-weight: 600;
        }
        
        .tool-button.active {
            background-color: #0d6efd !important;
            color: white !important;
        }
        
        .measurement-display {
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 100;
            font-size: 12px;
        }
        
        .pixel-info {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            z-index: 100;
        }
        
        .header-bar {
            background: white;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .dropdown-menu {
            max-height: 300px;
            overflow-y: auto;
        }
        
        .progress-bar {
            transition: width 0.3s ease;
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
                        <i class="fas fa-file-medical-alt me-2"></i>
                        DICOM Viewer
                    </h4>
                    <small class="text-muted">
                        <?php echo htmlspecialchars($file_dcm); ?>
                    </small>
                </div>
                <div>
                    <button class="btn btn-outline-secondary btn-sm me-2" onclick="window.history.back()">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </button>
                    <button class="btn btn-outline-primary btn-sm" onclick="downloadOriginalFile()">
                        <i class="fas fa-download me-1"></i> Download DICOM
                    </button>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Kolom Kiri: Viewer dan Info Pasien -->
            <div class="col-lg-8">
                <!-- Info Pasien -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="info-card">
                            <h6><i class="fas fa-user me-2"></i>Informasi Pasien</h6>
                            <table class="table table-sm mb-0">
                                <tbody>
                                    <?php if(isset($dicom_data['patient_name']) && !empty($dicom_data['patient_name'])): ?>
                                    <tr>
                                        <th>Nama Pasien</th>
                                        <td><?php echo htmlspecialchars($dicom_data['patient_name']); ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <?php if(isset($dicom_data['patient_id']) && !empty($dicom_data['patient_id'])): ?>
                                    <tr>
                                        <th>ID Pasien</th>
                                        <td><?php echo htmlspecialchars($dicom_data['patient_id']); ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <th>Nama File</th>
                                        <td><?php echo htmlspecialchars($file_dcm); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-card">
                            <h6><i class="fas fa-info-circle me-2"></i>Informasi File</h6>
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
                            <div class="progress mt-3" style="width: 200px;">
                                <div id="loadProgress" class="progress-bar" role="progressbar" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>
                    <div id="dicomViewer"></div>
                    <div class="measurement-display" id="measurementDisplay" style="display: none;"></div>
                    <div class="pixel-info" id="pixelInfo" style="display: none;"></div>
                </div>

                <!-- Progress Bar untuk Loading -->
                <div class="info-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1"><i class="fas fa-chart-line me-2"></i>Informasi Gambar</h6>
                            <small class="text-muted" id="imageInfoText">Loading...</small>
                        </div>
                        <div>
                            <span id="zoomLevel" class="badge bg-info">Zoom: 100%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Kontrol dan Metadata -->
            <div class="col-lg-4">
                <!-- Panel Kontrol -->
                <div class="control-panel mb-3">
                    <h6><i class="fas fa-sliders-h me-2"></i>Kontrol Gambar</h6>
                    
                    <!-- Tool Selection -->
                    <div class="mb-3">
                        <label class="form-label small">Alat Interaksi:</label>
                        <div class="btn-group d-flex mb-2" role="group">
                            <button type="button" class="btn btn-outline-primary btn-tool" id="toolWwwc" title="Window/Level">
                                <i class="fas fa-adjust"></i>
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-tool" id="toolZoom" title="Zoom">
                                <i class="fas fa-search"></i>
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-tool" id="toolPan" title="Pan">
                                <i class="fas fa-arrows-alt"></i>
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-tool" id="toolLength" title="Ukur Jarak">
                                <i class="fas fa-ruler-horizontal"></i>
                            </button>
                        </div>
                        
                        <div class="btn-group d-flex" role="group">
                            <button type="button" class="btn btn-outline-secondary btn-tool" id="toolAngle" title="Ukur Sudut">
                                <i class="fas fa-angle-right"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-tool" id="toolInvert" title="Invert Warna">
                                <i class="fas fa-exchange-alt"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-tool" id="toolReset" title="Reset View">
                                <i class="fas fa-redo"></i>
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-tool" id="toolClear" title="Hapus Pengukuran">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Window/Level Controls -->
                    <div class="slider-container">
                        <div class="slider-label">
                            <span><i class="fas fa-sun me-1"></i> Window Width</span>
                            <span id="wwValue" class="badge bg-secondary">400</span>
                        </div>
                        <input type="range" class="form-range" id="windowWidth" min="1" max="4096" value="400" step="1">
                    </div>
                    
                    <div class="slider-container">
                        <div class="slider-label">
                            <span><i class="fas fa-adjust me-1"></i> Window Center</span>
                            <span id="wcValue" class="badge bg-secondary">40</span>
                        </div>
                        <input type="range" class="form-range" id="windowCenter" min="-1024" max="3071" value="40" step="1">
                    </div>
                    
                    <!-- Zoom Controls -->
                    <div class="slider-container">
                        <div class="slider-label">
                            <span><i class="fas fa-search me-1"></i> Zoom Level</span>
                            <span id="zoomValue" class="badge bg-secondary">100%</span>
                        </div>
                        <input type="range" class="form-range" id="zoomSlider" min="10" max="500" value="100" step="1">
                    </div>
                    
                    <!-- Preset WW/WC -->
                    <div class="mb-3">
                        <label class="form-label small">Preset Window:</label>
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
                    <h6><i class="fas fa-table me-2"></i>Metadata DICOM</h6>
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

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/hammerjs@2.0.8/hammer.min.js"></script>
    
    <!-- Cornerstone DICOM Libraries -->
    <script src="https://unpkg.com/cornerstone-core"></script>
    <script src="https://unpkg.com/cornerstone-math"></script>
    <script src="https://unpkg.com/cornerstone-wado-image-loader"></script>
    <script src="https://unpkg.com/dicom-parser"></script>
    <script src="https://unpkg.com/cornerstone-tools"></script>

    <script>
    // Konfigurasi Global
    cornerstoneWADOImageLoader.external.cornerstone = cornerstone;
    cornerstoneWADOImageLoader.external.dicomParser = dicomParser;
    
    // URL File DICOM
    const DICOM_URL = '<?php echo $dicom_url; ?>';
    
    // Variabel Global
    let element = null;
    let currentTool = 'Wwwc';
    let currentMeasurements = [];
    let currentImage = null;
    
    $(document).ready(function() {
        // Inisialisasi Cornerstone
        initializeCornerstone();
        
        // Setup Event Handlers
        setupEventHandlers();
        
        // Load DICOM Image
        loadDICOMImage();
    });
    
    function initializeCornerstone() {
        element = document.getElementById('dicomViewer');
        
        // Enable cornerstone
        cornerstone.enable(element);
        
        // Inisialisasi tools
        cornerstoneTools.init({
            showSVGCursors: true
        });
        
        // Register tools
        const tools = [
            cornerstoneTools.ZoomTool,
            cornerstoneTools.PanTool,
            cornerstoneTools.WwwcTool,
            cornerstoneTools.LengthTool,
            cornerstoneTools.AngleTool
        ];
        
        tools.forEach(tool => cornerstoneTools.addTool(tool));
        
        // Setup tool configurations
        cornerstoneTools.addToolForElement(element, cornerstoneTools.ZoomTool);
        cornerstoneTools.addToolForElement(element, cornerstoneTools.PanTool);
        cornerstoneTools.addToolForElement(element, cornerstoneTools.WwwcTool);
        cornerstoneTools.addToolForElement(element, cornerstoneTools.LengthTool);
        cornerstoneTools.addToolForElement(element, cornerstoneTools.AngleTool);
        
        // Set initial active tool
        setActiveTool('Wwwc');
        
        // Setup mouse wheel for zoom
        element.addEventListener('wheel', function(e) {
            e.preventDefault();
            const viewport = cornerstone.getViewport(element);
            const delta = e.deltaY > 0 ? -0.1 : 0.1;
            viewport.scale = Math.max(0.1, viewport.scale + delta);
            cornerstone.setViewport(element, viewport);
            updateZoomDisplay(viewport.scale);
        });
        
        // Update pixel info on mouse move
        element.addEventListener('mousemove', function(e) {
            if (!currentImage) return;
            
            const rect = element.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            try {
                const pixelData = cornerstone.getPixels(element, x, y);
                if (pixelData) {
                    const pixelValue = pixelData[0];
                    $('#pixelInfo').text(`Pixel: ${pixelValue}`).show();
                }
            } catch (err) {
                // Ignore errors
            }
        });
        
        // Hide pixel info when mouse leaves
        element.addEventListener('mouseleave', function() {
            $('#pixelInfo').hide();
        });
    }
    
    function setActiveTool(toolName) {
        // Deactivate all tools first
        cornerstoneTools.setToolPassive('Wwwc');
        cornerstoneTools.setToolPassive('Zoom');
        cornerstoneTools.setToolPassive('Pan');
        cornerstoneTools.setToolPassive('Length');
        cornerstoneTools.setToolPassive('Angle');
        
        // Update button states
        $('.btn-tool').removeClass('active');
        
        // Activate selected tool
        switch(toolName) {
            case 'Wwwc':
                cornerstoneTools.setToolActive('Wwwc', { mouseButtonMask: 1 });
                $('#toolWwwc').addClass('active');
                break;
            case 'Zoom':
                cornerstoneTools.setToolActive('Zoom', { mouseButtonMask: 1 });
                $('#toolZoom').addClass('active');
                break;
            case 'Pan':
                cornerstoneTools.setToolActive('Pan', { mouseButtonMask: 1 });
                $('#toolPan').addClass('active');
                break;
            case 'Length':
                cornerstoneTools.setToolActive('Length', { mouseButtonMask: 1 });
                $('#toolLength').addClass('active');
                break;
            case 'Angle':
                cornerstoneTools.setToolActive('Angle', { mouseButtonMask: 1 });
                $('#toolAngle').addClass('active');
                break;
        }
        
        currentTool = toolName;
    }
    
    function setupEventHandlers() {
        // Tool buttons
        $('#toolWwwc').click(() => setActiveTool('Wwwc'));
        $('#toolZoom').click(() => setActiveTool('Zoom'));
        $('#toolPan').click(() => setActiveTool('Pan'));
        $('#toolLength').click(() => setActiveTool('Length'));
        $('#toolAngle').click(() => setActiveTool('Angle'));
        
        // Control buttons
        $('#toolInvert').click(function() {
            const viewport = cornerstone.getViewport(element);
            viewport.invert = !viewport.invert;
            cornerstone.setViewport(element, viewport);
        });
        
        $('#toolReset').click(function() {
            cornerstone.reset(element);
            const viewport = cornerstone.getViewport(element);
            updateControlsFromViewport(viewport);
            updateZoomDisplay(1);
        });
        
        $('#toolClear').click(function() {
            cornerstoneTools.clearToolState(element, 'Length');
            cornerstoneTools.clearToolState(element, 'Angle');
            cornerstone.updateImage(element);
            $('#measurementDisplay').hide();
            currentMeasurements = [];
        });
        
        // Window Width/Center sliders
        $('#windowWidth').on('input', function() {
            const value = parseInt($(this).val());
            $('#wwValue').text(value);
            
            const viewport = cornerstone.getViewport(element);
            if (viewport.voi) {
                viewport.voi.windowWidth = value;
                cornerstone.setViewport(element, viewport);
            }
        });
        
        $('#windowCenter').on('input', function() {
            const value = parseInt($(this).val());
            $('#wcValue').text(value);
            
            const viewport = cornerstone.getViewport(element);
            if (viewport.voi) {
                viewport.voi.windowCenter = value;
                cornerstone.setViewport(element, viewport);
            }
        });
        
        // Zoom slider
        $('#zoomSlider').on('input', function() {
            const zoomPercent = parseInt($(this).val());
            const zoomFactor = zoomPercent / 100;
            
            const viewport = cornerstone.getViewport(element);
            viewport.scale = zoomFactor;
            cornerstone.setViewport(element, viewport);
            updateZoomDisplay(zoomFactor);
        });
        
        // WW/WC Presets
        $('#wwPreset').change(function() {
            const preset = $(this).val();
            let ww = 400, wc = 40;
            
            switch(preset) {
                case 'lung': ww = 1500; wc = -600; break;
                case 'bone': ww = 2000; wc = 500; break;
                case 'brain': ww = 80; wc = 40; break;
                case 'abdomen': ww = 400; wc = 50; break;
                case 'mediastinum': ww = 350; wc = 50; break;
            }
            
            if (preset !== 'custom') {
                $('#windowWidth').val(ww);
                $('#wwValue').text(ww);
                $('#windowCenter').val(wc);
                $('#wcValue').text(wc);
                
                const viewport = cornerstone.getViewport(element);
                if (viewport.voi) {
                    viewport.voi.windowWidth = ww;
                    viewport.voi.windowCenter = wc;
                    cornerstone.setViewport(element, viewport);
                }
            }
        });
        
        // Measurement display updates
        element.addEventListener(cornerstoneTools.EVENTS.MEASUREMENT_ADDED, function(e) {
            updateMeasurementDisplay(e.detail);
        });
        
        element.addEventListener(cornerstoneTools.EVENTS.MEASUREMENT_MODIFIED, function(e) {
            updateMeasurementDisplay(e.detail);
        });
        
        // Window resize handling
        $(window).resize(function() {
            if (element) {
                cornerstone.resize(element);
                cornerstone.fitToWindow(element);
                updateZoomDisplay(cornerstone.getViewport(element).scale);
            }
        });
    }
    
    function loadDICOMImage() {
        const imageId = `wadouri:${DICOM_URL}`;
        
        // Show loading overlay with progress
        $('#loadingOverlay').show();
        $('#loadProgress').css('width', '10%');
        
        // Configure progress callback
        cornerstoneWADOImageLoader.configure({
            beforeSend: function(xhr) {
                xhr.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        const percent = Math.round((e.loaded / e.total) * 100);
                        $('#loadProgress').css('width', percent + '%');
                    }
                });
            }
        });
        
        // Load and display image
        cornerstone.loadImage(imageId).then(function(image) {
            currentImage = image;
            
            // Display image
            cornerstone.displayImage(element, image);
            
            // Set initial viewport
            const viewport = cornerstone.getViewport(element);
            updateControlsFromViewport(viewport);
            updateZoomDisplay(viewport.scale);
            
            // Hide loading overlay
            setTimeout(() => {
                $('#loadingOverlay').fadeOut(300);
            }, 500);
            
            // Update image info
            updateImageInfo(image);
            
            // Extract metadata
            extractMetadata(imageId);
            
            // Fit to window after a short delay
            setTimeout(() => {
                cornerstone.fitToWindow(element);
                updateZoomDisplay(cornerstone.getViewport(element).scale);
            }, 100);
            
        }).catch(function(error) {
            console.error('Error loading DICOM:', error);
            $('#loadingOverlay').html(`
                <div class="text-center">
                    <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                    <h5>Gagal Memuat Gambar</h5>
                    <p class="mb-3">${error.message || 'Unknown error'}</p>
                    <button onclick="location.reload()" class="btn btn-primary">
                        <i class="fas fa-redo me-1"></i> Coba Lagi
                    </button>
                </div>
            `);
        });
    }
    
    function updateControlsFromViewport(viewport) {
        if (viewport.voi) {
            $('#windowWidth').val(viewport.voi.windowWidth || 400);
            $('#wwValue').text(viewport.voi.windowWidth || 400);
            
            $('#windowCenter').val(viewport.voi.windowCenter || 40);
            $('#wcValue').text(viewport.voi.windowCenter || 40);
        }
        
        updateZoomDisplay(viewport.scale || 1);
    }
    
    function updateZoomDisplay(scale) {
        const zoomPercent = Math.round(scale * 100);
        $('#zoomLevel').text(`Zoom: ${zoomPercent}%`);
        $('#zoomValue').text(`${zoomPercent}%`);
        $('#zoomSlider').val(zoomPercent);
    }
    
    function updateImageInfo(image) {
        const info = [];
        
        if (image.imageId) info.push(`ID: ${image.imageId.split('/').pop()}`);
        if (image.columns && image.rows) info.push(`Resolusi: ${image.columns}×${image.rows}`);
        if (image.color) info.push(`Warna: ${image.color ? 'Ya' : 'Grayscale'}`);
        if (image.bitsAllocated) info.push(`Bit Depth: ${image.bitsAllocated}`);
        
        $('#imageInfoText').text(info.join(' | '));
    }
    
    function updateMeasurementDisplay(measurementData) {
        let displayText = '';
        
        if (measurementData.toolType === 'Length') {
            const length = measurementData.length || 0;
            const lengthMM = measurementData.lengthInMM || 0;
            displayText = `Jarak: ${length.toFixed(1)} px (${lengthMM.toFixed(1)} mm)`;
        } else if (measurementData.toolType === 'Angle') {
            const angle = measurementData.angle || 0;
            displayText = `Sudut: ${angle.toFixed(1)}°`;
        }
        
        if (displayText) {
            $('#measurementDisplay').text(displayText).show();
        }
    }
    
    function extractMetadata(imageId) {
        cornerstone.loadImage(imageId).then(function(image) {
            const data = image.data || {};
            let metadataHtml = '';
            
            // Daftar tag DICOM yang umum
            const dicomTags = [
                { tag: 'x00100010', name: 'Patient Name' },
                { tag: 'x00100020', name: 'Patient ID' },
                { tag: 'x00100030', name: 'Patient Birth Date' },
                { tag: 'x00100040', name: 'Patient Sex' },
                { tag: 'x00080020', name: 'Study Date' },
                { tag: 'x00080030', name: 'Study Time' },
                { tag: 'x00080050', name: 'Accession Number' },
                { tag: 'x00080060', name: 'Modality' },
                { tag: 'x00080070', name: 'Manufacturer' },
                { tag: 'x00080080', name: 'Institution Name' },
                { tag: 'x00080090', name: 'Referring Physician' },
                { tag: 'x00081030', name: 'Study Description' },
                { tag: 'x0008103e', name: 'Series Description' },
                { tag: 'x00180050', name: 'Slice Thickness' },
                { tag: 'x00180060', name: 'KV' },
                { tag: 'x00180070', name: 'Exposure Time' },
                { tag: 'x00180081', name: 'Tube Current' },
                { tag: 'x00181100', name: 'Reconstruction Diameter' },
                { tag: 'x00200011', name: 'Series Number' },
                { tag: 'x00200013', name: 'Instance Number' },
                { tag: 'x00280002', name: 'Samples per Pixel' },
                { tag: 'x00280004', name: 'Photometric Interpretation' },
                { tag: 'x00280010', name: 'Rows' },
                { tag: 'x00280011', name: 'Columns' },
                { tag: 'x00280100', name: 'Bits Allocated' },
                { tag: 'x00280101', name: 'Bits Stored' },
                { tag: 'x00280102', name: 'High Bit' },
                { tag: 'x00280103', name: 'Pixel Representation' },
                { tag: 'x00280106', name: 'Smallest Image Pixel Value' },
                { tag: 'x00280107', name: 'Largest Image Pixel Value' },
                { tag: 'x00281050', name: 'Window Center' },
                { tag: 'x00281051', name: 'Window Width' },
                { tag: 'x00281052', name: 'Rescale Intercept' },
                { tag: 'x00281053', name: 'Rescale Slope' }
            ];
            
            let foundCount = 0;
            dicomTags.forEach(tagInfo => {
                try {
                    const value = data.string(tagInfo.tag);
                    if (value && value.trim() !== '') {
                        metadataHtml += `
                            <tr>
                                <th>${tagInfo.name}</th>
                                <td>${escapeHtml(value)}</td>
                            </tr>
                        `;
                        foundCount++;
                    }
                } catch (e) {
                    // Ignore errors for missing tags
                }
            });
            
            // Jika tidak ada metadata dari parsing, tampilkan info dasar
            if (foundCount === 0 && currentImage) {
                metadataHtml = `
                    <tr><th>Rows</th><td>${currentImage.rows || 'N/A'}</td></tr>
                    <tr><th>Columns</th><td>${currentImage.columns || 'N/A'}</td></tr>
                    <tr><th>Window Width</th><td>${currentImage.windowWidth || 'N/A'}</td></tr>
                    <tr><th>Window Center</th><td>${currentImage.windowCenter || 'N/A'}</td></tr>
                    <tr><th>Photometric</th><td>${currentImage.photometricInterpretation || 'N/A'}</td></tr>
                `;
            }
            
            $('#metadataTable').html(metadataHtml || '<tr><td colspan="2" class="text-center text-muted">Metadata tidak tersedia</td></tr>');
            
        }).catch(function(error) {
            console.error('Error extracting metadata:', error);
            $('#metadataTable').html(`
                <tr>
                    <td colspan="2" class="text-center text-muted">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Gagal memuat metadata
                    </td>
                </tr>
            `);
        });
    }
    
    function downloadOriginalFile() {
        window.open(DICOM_URL, '_blank');
    }
    
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text ? text.replace(/[&<>"']/g, m => map[m]) : '';
    }
    
    // Export untuk debugging
    window.dicomViewer = {
        getElement: () => element,
        getCurrentImage: () => currentImage,
        reloadImage: () => loadDICOMImage(),
        setWindowLevel: (ww, wc) => {
            const viewport = cornerstone.getViewport(element);
            viewport.voi = { windowWidth: ww, windowCenter: wc };
            cornerstone.setViewport(element, viewport);
        }
    };
    </script>
</body>
</html>
<?php
    // Clean up
    if (isset($stmt)) {
        $stmt->close();
    }
?>