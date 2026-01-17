<?php
    include "../../_Config/Connection.php";
    if(empty($_POST['id_master_pemeriksaan'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>Silahkan Pilih Referensi Pemeriksaan Terlebih Dulu!</small>
            </div>
        ';
        exit;
    }
    $id_master_pemeriksaan = $_POST['id_master_pemeriksaan'];

    // Buka Data master klinis
    $Qry = $Conn->prepare("SELECT * FROM master_pemeriksaan WHERE id_master_pemeriksaan = ?");
    $Qry->bind_param("i", $id_master_pemeriksaan);
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
    if(empty($Data['id_master_pemeriksaan'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>Referensi Pemeriksaan Tidak Ditemukan!</small>
            </div>
        ';
        exit;
    }

    $id_master_pemeriksaan   = $Data['id_master_pemeriksaan'];
    $nama_pemeriksaan        = $Data['nama_pemeriksaan'];
    $modalitas               = $Data['modalitas'];
    $pemeriksaan_code        = $Data['pemeriksaan_code'];
    $pemeriksaan_description = $Data['pemeriksaan_description'];
    $pemeriksaan_sys         = $Data['pemeriksaan_sys'];
    $bodysite_code           = $Data['bodysite_code'];
    $bodysite_description    = $Data['bodysite_description'];
    $bodysite_sys            = $Data['bodysite_sys'];
    $report_code             = $Data['report_code'];
    $report_description      = $Data['report_description'];
    $report_sys              = $Data['report_sys'];

    // Tampilkan
    echo '
        <div class="table table-responsive">
            <table class="table table-sm table-bordered">
                <tbody>
                    <tr>
                        <td><small class="text text-dark">Nama Pemeriksaan</small></td>
                        <td class="text text-grayish">
                            <small class="text text-grayish">'.$nama_pemeriksaan.'</small>
                        </td>
                    </tr>
                    <tr>
                        <td><small class="text text-dark"><i>Modality</i></small></td>
                        <td class="text text-grayish">
                            <small class="text text-grayish">'.$modalitas.'</small>
                        </td>
                    </tr>
                    <tr>
                        <td><small class="text text-dark"><i>Loinc Code</i></small></td>
                        <td class="text text-grayish">
                            <small class="text text-grayish">'.$pemeriksaan_code.'</small>
                        </td>
                    </tr>
                    <tr>
                        <td><small class="text text-dark"><i>Loinc Display</i></small></td>
                        <td>
                            <small class="text text-grayish">'.$pemeriksaan_description.'</small>
                        </td>
                    </tr>
                    <tr>
                        <td><small class="text text-dark"><i>Loinc System</i></small></td>
                        <td class="text text-grayish">
                            <small class="text text-grayish">'.$pemeriksaan_sys.'</small>
                        </td>
                    </tr>
                    <tr>
                        <td><small class="text text-dark"><i>Body Site Code</i></small></td>
                        <td class="text text-grayish">
                            <small class="text text-grayish">'.$bodysite_code.'</small>
                        </td>
                    </tr>
                    <tr>
                        <td><small class="text text-dark"><i>Body Site Description</i></small></td>
                        <td class="text text-grayish">
                            <small class="text text-grayish">'.$bodysite_description.'</small>
                        </td>
                    </tr>
                    <tr>
                        <td><small class="text text-dark"><i>Body Site System</i></small></td>
                        <td class="text text-grayish">
                            <small class="text text-grayish">'.$bodysite_sys.'</small>
                        </td>
                    </tr>
                    <tr>
                        <td><small class="text text-dark"><i>Report Code</i></small></td>
                        <td class="text text-grayish">
                            <small class="text text-grayish">'.$report_code.'</small>
                        </td>
                    </tr>
                    <tr>
                        <td><small class="text text-dark"><i>Report Description</i></small></td>
                        <td class="text text-grayish">
                            <small class="text text-grayish">'.$report_description.'</small>
                        </td>
                    </tr>
                    <tr>
                        <td><small class="text text-dark"><i>Report System</i></small></td>
                        <td class="text text-grayish">
                            <small class="text text-grayish">'.$report_sys.'</small>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    ';
?>