// Dicom Viewer JavaScript
(function($) {
    'use strict';
    
    // Konfigurasi Global
    cornerstoneWADOImageLoader.external.cornerstone = cornerstone;
    cornerstoneWADOImageLoader.external.dicomParser = dicomParser;
    
    // Variabel Global
    let element = null;
    let currentTool = 'Wwwc';
    let currentImage = null;
    let viewportRotation = 0;
    let viewportHflip = false;
    let viewportVflip = false;
    
    // Fungsi utama
    function initializeViewer() {
        // Get DICOM URL from hidden input
        const dicomUrl = $('#dicom_url').val();
        if (!dicomUrl) {
            console.error('DICOM URL tidak ditemukan');
            showError('DICOM URL tidak ditemukan');
            return;
        }
        
        // Inisialisasi Cornerstone
        initializeCornerstone();
        
        // Setup Event Handlers menggunakan jQuery
        setupEventHandlers();
        
        // Load DICOM Image
        loadDICOMImage(dicomUrl);
    }
    
    function initializeCornerstone() {
        element = document.getElementById('dicomViewer');
        
        if (!element) {
            console.error('Element #dicomViewer tidak ditemukan');
            return;
        }
        
        // Enable cornerstone
        cornerstone.enable(element);
        
        // Inisialisasi tools
        cornerstoneTools.init({
            showSVGCursors: true,
            globalToolSyncEnabled: true
        });
        
        // Register tools
        const tools = [
            cornerstoneTools.ZoomTool,
            cornerstoneTools.PanTool,
            cornerstoneTools.WwwcTool,
            cornerstoneTools.LengthTool,
            cornerstoneTools.AngleTool,
            cornerstoneTools.RotateTool
        ];
        
        tools.forEach(tool => cornerstoneTools.addTool(tool));
        
        // Setup tool configurations
        cornerstoneTools.addToolForElement(element, cornerstoneTools.ZoomTool);
        cornerstoneTools.addToolForElement(element, cornerstoneTools.PanTool);
        cornerstoneTools.addToolForElement(element, cornerstoneTools.WwwcTool);
        cornerstoneTools.addToolForElement(element, cornerstoneTools.LengthTool);
        cornerstoneTools.addToolForElement(element, cornerstoneTools.AngleTool);
        cornerstoneTools.addToolForElement(element, cornerstoneTools.RotateTool);
        
        // Set initial active tool
        setActiveTool('Wwwc');
        
        // Setup mouse wheel for zoom
        element.addEventListener('wheel', function(e) {
            e.preventDefault();
            const viewport = cornerstone.getViewport(element);
            if (!viewport) return;
            
            const delta = e.deltaY > 0 ? -0.1 : 0.1;
            viewport.scale = Math.max(0.1, viewport.scale + delta);
            cornerstone.setViewport(element, viewport);
            updateZoomDisplay(viewport.scale);
            updateImageInfo();
        });
        
        // Update pixel info on mouse move
        element.addEventListener('mousemove', function(e) {
            if (!currentImage) return;
            
            const rect = element.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            try {
                const pixelData = cornerstone.getPixels(element, x, y);
                if (pixelData && pixelData.length > 0) {
                    const pixelValue = pixelData[0];
                    $('#pixelInfo').html(`<i class="bi bi-dot"></i> Pixel: ${pixelValue}`).show();
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
        if (!element) return;
        
        // Deactivate all tools first
        cornerstoneTools.setToolPassive('Wwwc');
        cornerstoneTools.setToolPassive('Zoom');
        cornerstoneTools.setToolPassive('Pan');
        cornerstoneTools.setToolPassive('Length');
        cornerstoneTools.setToolPassive('Angle');
        cornerstoneTools.setToolPassive('Rotate');
        
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
            case 'Rotate':
                cornerstoneTools.setToolActive('Rotate', { mouseButtonMask: 1 });
                $('#toolRotate').addClass('active');
                break;
        }
        
        currentTool = toolName;
    }
    
    function setupEventHandlers() {
        // Tool buttons
        $('#toolWwwc').click(function() { 
            setActiveTool('Wwwc');
            showTooltip('Window/Level Tool aktif');
        });
        
        $('#toolZoom').click(function() { 
            setActiveTool('Zoom');
            showTooltip('Zoom Tool aktif');
        });
        
        $('#toolPan').click(function() { 
            setActiveTool('Pan');
            showTooltip('Pan Tool aktif');
        });
        
        $('#toolLength').click(function() { 
            setActiveTool('Length');
            showTooltip('Length Tool aktif');
        });
        
        $('#toolAngle').click(function() { 
            setActiveTool('Angle');
            showTooltip('Angle Tool aktif');
        });
        
        $('#toolRotate').click(function() {
            setActiveTool('Rotate');
            showTooltip('Rotate Tool aktif');
        });
        
        // Control buttons
        $('#toolFlipH').click(function() {
            if (!element || !currentImage) return;
            
            const viewport = cornerstone.getViewport(element);
            if (!viewport) return;
            
            viewport.hflip = !viewport.hflip;
            cornerstone.setViewport(element, viewport);
            viewportHflip = viewport.hflip;
            updateImageInfo();
            showTooltip('Flip Horizontal: ' + (viewport.hflip ? 'ON' : 'OFF'));
        });
        
        $('#toolFlipV').click(function() {
            if (!element || !currentImage) return;
            
            const viewport = cornerstone.getViewport(element);
            if (!viewport) return;
            
            viewport.vflip = !viewport.vflip;
            cornerstone.setViewport(element, viewport);
            viewportVflip = viewport.vflip;
            updateImageInfo();
            showTooltip('Flip Vertical: ' + (viewport.vflip ? 'ON' : 'OFF'));
        });
        
        $('#toolInvert').click(function() {
            if (!element || !currentImage) return;
            
            const viewport = cornerstone.getViewport(element);
            if (!viewport) return;
            
            viewport.invert = !viewport.invert;
            cornerstone.setViewport(element, viewport);
            showTooltip('Invert Colors: ' + (viewport.invert ? 'ON' : 'OFF'));
        });
        
        $('#toolReset').click(function() {
            if (!element || !currentImage) return;
            
            cornerstone.reset(element);
            viewportRotation = 0;
            viewportHflip = false;
            viewportVflip = false;
            $('#rotateSlider').val(0);
            $('#rotateValue').text('0°');
            const viewport = cornerstone.getViewport(element);
            if (viewport) {
                updateControlsFromViewport(viewport);
                updateZoomDisplay(viewport.scale);
                updateImageInfo();
            }
            showTooltip('View telah direset');
        });
        
        $('#toolClear').click(function() {
            if (!element) return;
            
            cornerstoneTools.clearToolState(element, 'Length');
            cornerstoneTools.clearToolState(element, 'Angle');
            cornerstone.updateImage(element);
            $('#measurementDisplay').hide();
            showTooltip('Semua pengukuran telah dihapus');
        });
        
        $('#toolCapture').click(function() {
            captureImage();
        });
        
        // Window Width/Center sliders
        $('#windowWidth').on('input', function() {
            if (!element || !currentImage) return;
            
            const value = parseInt($(this).val());
            $('#wwValue').text(value);
            
            const viewport = cornerstone.getViewport(element);
            if (viewport && viewport.voi) {
                viewport.voi.windowWidth = value;
                cornerstone.setViewport(element, viewport);
            }
        });
        
        $('#windowCenter').on('input', function() {
            if (!element || !currentImage) return;
            
            const value = parseInt($(this).val());
            $('#wcValue').text(value);
            
            const viewport = cornerstone.getViewport(element);
            if (viewport && viewport.voi) {
                viewport.voi.windowCenter = value;
                cornerstone.setViewport(element, viewport);
            }
        });
        
        // Zoom slider
        $('#zoomSlider').on('input', function() {
            if (!element || !currentImage) return;
            
            const zoomPercent = parseInt($(this).val());
            const zoomFactor = zoomPercent / 100;
            
            const viewport = cornerstone.getViewport(element);
            if (viewport) {
                viewport.scale = zoomFactor;
                cornerstone.setViewport(element, viewport);
                updateZoomDisplay(zoomFactor);
                updateImageInfo();
            }
        });
        
        // Rotate slider
        $('#rotateSlider').on('input', function() {
            if (!element || !currentImage) return;
            
            const rotateDeg = parseInt($(this).val());
            $('#rotateValue').text(rotateDeg + '°');
            
            const viewport = cornerstone.getViewport(element);
            if (viewport) {
                viewport.rotation = rotateDeg;
                cornerstone.setViewport(element, viewport);
                viewportRotation = rotateDeg;
                updateImageInfo();
            }
        });
        
        // WW/WC Presets
        $('#wwPreset').change(function() {
            if (!element || !currentImage) return;
            
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
                if (viewport && viewport.voi) {
                    viewport.voi.windowWidth = ww;
                    viewport.voi.windowCenter = wc;
                    cornerstone.setViewport(element, viewport);
                }
                
                showTooltip('Preset ' + preset + ' diterapkan');
            }
        });
        
        // Measurement display updates
        if (element) {
            element.addEventListener(cornerstoneTools.EVENTS.MEASUREMENT_ADDED, function(e) {
                updateMeasurementDisplay(e.detail);
            });
            
            element.addEventListener(cornerstoneTools.EVENTS.MEASUREMENT_MODIFIED, function(e) {
                updateMeasurementDisplay(e.detail);
            });
        }
        
        // Window resize handling
        $(window).resize(function() {
            if (element && currentImage) {
                cornerstone.resize(element);
                cornerstone.fitToWindow(element);
                const viewport = cornerstone.getViewport(element);
                if (viewport) {
                    updateZoomDisplay(viewport.scale);
                    updateImageInfo();
                }
            }
        });
    }
    
    function loadDICOMImage(dicomUrl) {
        if (!dicomUrl) {
            showError('URL DICOM tidak valid');
            return;
        }
        
        const imageId = `wadouri:${dicomUrl}`;
        console.log('Loading DICOM image:', imageId);
        
        // Show loading overlay
        $('#loadingOverlay').show();
        $('#loadProgress').css('width', '10%');
        
        // Configure WADO Image Loader
        cornerstoneWADOImageLoader.webWorkerManager.initialize({
            maxWebWorkers: 1,
            startWebWorkersOnDemand: true,
            webWorkerTaskPaths: [],
            taskConfiguration: {
                'decodeTask': {
                    initializeCodecsOnStartup: false,
                    usePDFJS: false
                }
            }
        });
        
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
            console.log('DICOM image loaded successfully:', image);
            currentImage = image;
            
            // Display image
            cornerstone.displayImage(element, image);
            
            // Set initial viewport
            const viewport = cornerstone.getDefaultViewportForImage(element, image);
            cornerstone.setViewport(element, viewport);
            
            updateControlsFromViewport(viewport);
            updateZoomDisplay(viewport.scale);
            updateImageInfo(image);
            
            // Hide loading overlay
            setTimeout(() => {
                $('#loadingOverlay').fadeOut(300);
            }, 500);
            
            // Extract metadata
            extractMetadata(imageId);
            
            // Fit to window after a short delay
            setTimeout(() => {
                cornerstone.fitToWindow(element);
                const updatedViewport = cornerstone.getViewport(element);
                updateZoomDisplay(updatedViewport.scale);
                updateImageInfo(image);
            }, 100);
            
        }).catch(function(error) {
            console.error('Error loading DICOM:', error);
            showError('Gagal memuat gambar DICOM: ' + (error.message || 'Unknown error'));
        });
    }
    
    function updateControlsFromViewport(viewport) {
        if (viewport && viewport.voi) {
            $('#windowWidth').val(viewport.voi.windowWidth || 400);
            $('#wwValue').text(viewport.voi.windowWidth || 400);
            
            $('#windowCenter').val(viewport.voi.windowCenter || 40);
            $('#wcValue').text(viewport.voi.windowCenter || 40);
        }
        
        updateZoomDisplay(viewport ? viewport.scale : 1);
    }
    
    function updateZoomDisplay(scale) {
        const zoomPercent = Math.round(scale * 100);
        $('#zoomValue').text(zoomPercent + '%');
        $('#zoomSlider').val(zoomPercent);
    }
    
    function updateImageInfo(image = currentImage) {
        if (!image) return;
        
        const viewport = cornerstone.getViewport(element);
        if (!viewport) return;
        
        const zoomPercent = Math.round(viewport.scale * 100);
        
        const info = [];
        if (image.columns && image.rows) {
            info.push(`${image.columns}×${image.rows}`);
        }
        info.push(image.color ? 'Color' : 'Grayscale');
        info.push(`Zoom: ${zoomPercent}%`);
        if (viewportRotation !== 0) info.push(`Rot: ${viewportRotation}°`);
        if (viewportHflip) info.push('H-Flip');
        if (viewportVflip) info.push('V-Flip');
        
        $('#imageInfo').html('<i class="bi bi-info-circle me-1"></i>' + info.join(' | ')).show();
    }
    
    function updateMeasurementDisplay(measurementData) {
        let displayText = '';
        
        if (measurementData.toolType === 'Length') {
            const length = measurementData.length || 0;
            const lengthMM = measurementData.lengthInMM || 0;
            displayText = `<i class="bi bi-rulers me-1"></i> ${length.toFixed(1)}px (${lengthMM.toFixed(1)}mm)`;
        } else if (measurementData.toolType === 'Angle') {
            const angle = measurementData.angle || 0;
            displayText = `<i class="bi bi-angle-90 me-1"></i> ${angle.toFixed(1)}°`;
        }
        
        if (displayText) {
            $('#measurementDisplay').html(displayText).show();
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
                { tag: 'x00280103', name: 'Pixel Representation' }
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
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Gagal memuat metadata
                    </td>
                </tr>
            `);
        });
    }
    
    function downloadOriginalFile() {
        const dicomUrl = $('#dicom_url').val();
        if (dicomUrl) {
            window.open(dicomUrl, '_blank');
        } else {
            showTooltip('URL DICOM tidak ditemukan');
        }
    }
    
    function captureImage() {
        if (!element || !currentImage) {
            showTooltip('Tidak ada gambar untuk ditangkap');
            return;
        }
        
        Swal.fire({
            title: 'Capture & Print',
            text: 'Pilih aksi yang diinginkan:',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Capture',
            cancelButtonText: 'Print',
            showDenyButton: true,
            denyButtonText: 'Capture & Print',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Capture only
                html2canvas(element).then(canvas => {
                    const link = document.createElement('a');
                    link.download = 'dicom-capture-' + new Date().getTime() + '.png';
                    link.href = canvas.toDataURL();
                    link.click();
                    showTooltip('Gambar telah disimpan');
                }).catch(err => {
                    console.error('Capture error:', err);
                    showTooltip('Gagal menangkap gambar');
                });
            } else if (result.isDenied) {
                // Capture and print
                html2canvas(element).then(canvas => {
                    const printWindow = window.open('', '_blank');
                    printWindow.document.write(`
                        <html>
                            <head>
                                <title>Print DICOM Image</title>
                                <style>
                                    body { margin: 20px; text-align: center; }
                                    img { max-width: 100%; height: auto; }
                                    .info { text-align: left; margin-bottom: 20px; }
                                </style>
                            </head>
                            <body>
                                <div class="info">
                                    <h3>DICOM Image</h3>
                                    <p>File: ${$('small.text-light').text()}</p>
                                    <p>Tanggal: ${new Date().toLocaleString()}</p>
                                </div>
                                <img src="${canvas.toDataURL()}" />
                            </body>
                        </html>
                    `);
                    printWindow.document.close();
                    printWindow.focus();
                    setTimeout(() => {
                        printWindow.print();
                        printWindow.close();
                    }, 500);
                }).catch(err => {
                    console.error('Capture error:', err);
                    showTooltip('Gagal menangkap gambar');
                });
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                // Print only current view
                const printWindow = window.open('', '_blank');
                printWindow.document.write(`
                    <html>
                        <head>
                            <title>Print DICOM Viewer</title>
                            <style>
                                body { margin: 20px; }
                                .viewer { border: 1px solid #ccc; }
                            </style>
                        </head>
                        <body>
                            <h3>DICOM Viewer</h3>
                            <div class="viewer">
                                ${element.innerHTML}
                            </div>
                        </body>
                    </html>
                `);
                printWindow.document.close();
                printWindow.focus();
                setTimeout(() => {
                    printWindow.print();
                    printWindow.close();
                }, 500);
            }
        });
    }
    
    function showTooltip(message) {
        // Create toast notification
        const toast = $(`
            <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050">
                <div class="toast show" role="alert">
                    <div class="toast-body">
                        ${message}
                    </div>
                </div>
            </div>
        `);
        
        $('body').append(toast);
        
        // Remove after 2 seconds
        setTimeout(() => {
            toast.remove();
        }, 2000);
    }
    
    function showError(message) {
        $('#loadingOverlay').html(`
            <div class="text-center">
                <i class="bi bi-exclamation-triangle fa-3x text-danger mb-3"></i>
                <h5>Error</h5>
                <p class="mb-3">${message}</p>
                <button onclick="location.reload()" class="btn btn-primary">
                    <i class="bi bi-arrow-repeat me-1"></i> Coba Lagi
                </button>
            </div>
        `);
    }
    
    function escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }
    
    // Export untuk debugging
    window.dicomViewer = {
        getElement: () => element,
        getCurrentImage: () => currentImage,
        reloadImage: () => {
            const dicomUrl = $('#dicom_url').val();
            if (dicomUrl) {
                loadDICOMImage(dicomUrl);
            }
        },
        setWindowLevel: (ww, wc) => {
            if (!element || !currentImage) return;
            
            const viewport = cornerstone.getViewport(element);
            if (viewport) {
                viewport.voi = { windowWidth: ww, windowCenter: wc };
                cornerstone.setViewport(element, viewport);
            }
        }
    };
    
    // Initialize when document is ready
    $(document).ready(function() {
        console.log('DICOM Viewer Initializing...');
        setTimeout(() => {
            initializeViewer();
        }, 100);
    });
    
})(jQuery);