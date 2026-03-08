
<div class="pagetitle">
    <h1>
        <a href="">
            <i class="bi bi-question-circle"></i> Dokumentasi</a>
        </a>
    </h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
            <li class="breadcrumb-item active">Dokumentasi</li>
        </ol>
    </nav>
</div>
<section class="section dashboard">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-12">
                            <ol>
                                <li class="mb-3">
                                    <b class="mb-3">Referensi</b>
                                    <ul>
                                        <li class="mb-2">
                                            <a target="_blank" href="https://parasilva-technology.gitbook.io/radix/">
                                                <small>Radix Documentastion Page (Github Book)</small>
                                            </a>
                                        </li>
                                        <li class="mb-2">
                                            <a target="_blank" href="https://github.com/solihulhadi141213/rms">
                                                <small>Radix Repository</small>
                                            </a>
                                        </li>
                                        <li class="mb-2">
                                            <a target="_blank" href="https://satusehat.kemkes.go.id/">
                                                <small>Satu Sehat Platform</small>
                                            </a>
                                        </li>
                                        <li class="mb-2">
                                            <a target="_blank" href="https://bootstrapmade.com/demo/NiceAdmin/">
                                                <small>Bootstrapmade Admin Template</small>
                                            </a>
                                        </li>
                                        <li class="mb-2">
                                            <a target="_blank" href="https://getbootstrap.com/">
                                                <small>Bootstrap Main Website</small>
                                            </a>
                                        </li>
                                        <li class="mb-2">
                                            <a target="_blank" href="https://icons.getbootstrap.com/">
                                                <small>Bootstrap Icon</small>
                                            </a>
                                        </li>
                                        <li class="mb-2">
                                            <a target="_blank" href="https://jquery.com/">
                                                <small>Jquery</small>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="mb-3">
                                    <b class="mb-3">Postman Collection</b>
                                    <ul>
                                        <li class="mb-2">
                                            <a target="_blank" href="https://rsuelsyifa.postman.co/workspace/Radix~e35c857e-bb89-4c24-9b4a-9b169d7d936c/collection/12795177-7fa1747e-2e09-44db-8f8d-466d2a89862d?action=share&source=copy-link&creator=12795177">
                                                <small>Radix API Collection</small>
                                            </a>
                                        </li>
                                        <li class="mb-2">
                                            <a target="_blank" href="https://rsuelsyifa.postman.co/workspace/SIMRS-V2~efd89395-6ec5-446e-88f2-801f31b88e97/collection/12795177-e1b3c122-0693-41a7-8337-674be340f66b?action=share&source=copy-link&creator=12795177">
                                                <small>SIMRS El-Syifa V2 Collection</small>
                                            </a>
                                        </li>
                                        <li class="mb-2">
                                            <a target="_blank" href="https://rsuelsyifa.postman.co/workspace/Orthanc-DICOM-Server~6efa9070-2767-4da8-a1df-284148592853/collection/12795177-8565c3c0-042d-443e-808a-8fd83b953d15?action=share&source=copy-link&creator=12795177">
                                                <small>Basic Orthanc Collection</small>
                                            </a>
                                        </li>
                                        <li class="mb-2">
                                            <a target="_blank" href="https://rsuelsyifa.postman.co/workspace/Orthanc-DICOM-Server~6efa9070-2767-4da8-a1df-284148592853/collection/12795177-dd8acee2-2239-4f69-bd6e-710abe198257?action=share&source=copy-link&creator=12795177">
                                                <small>DICOM Orthanc Collection</small>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <?php
                                    // Buka Pengaturan Koneksi SIMRS
                                    $status_connection_simrs = 1;
                                    $url_connection_simrs = GetDetailData($Conn,'connection_simrs','status_connection_simrs',$status_connection_simrs,'url_connection_simrs');

                                    // Buka Pengaturan Koneksi Satu Sehat
                                    $status_active = 1;
                                    $url_satu_sehat = GetDetailData($Conn,'connection_satu_sehat','status_connection_satu_sehat',$status_active,'url_connection_satu_sehat');

                                     // Buka Pengaturan Koneksi PACS
                                    $status_connection_pacs = 1;
                                    $url_pacs = GetDetailData($Conn,'connection_pacs','status_connection_pacs',$status_connection_pacs,'url_connection_pacs');
                                ?>
                                <li>
                                    <b class="mb-3">Base URL Integration</b>
                                    <ul>
                                        <li class="mb-2">
                                            <a target="_blank" href="<?php echo $url_connection_simrs; ?>">
                                                <small>SIMRS Integration</small>
                                            </a>
                                        </li>
                                        <li class="mb-2">
                                            <a target="_blank" href="<?php echo $url_satu_sehat; ?>">
                                                <small>Satu Sehat Platform Integration</small>
                                            </a>
                                        </li>
                                        <li class="mb-2">
                                            <a target="_blank" href="<?php echo $url_pacs; ?>">
                                                <small>PACS Integration (Senalogy)</small>
                                            </a>
                                        </li>
                                        <li class="mb-2">
                                            <a target="_blank" href="https://rselsyifa-vista.senalogy.com/">
                                                <small>Vista Camera</small>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>