<?php
    // koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    // Zona Waktu
    date_default_timezone_set("Asia/Jakarta");

    $konten_preview = "";

    // Session
    if (empty($SessionIdAccess)) {
        echo '<div class="alert alert-danger text-center">Sesi Akses Sudah Berakhir! Silahkan Login Ulang.</div>';
        exit;
    }

    // Validasi id_radiologi
    if (empty($_POST['id_radiologi'])) {
        echo '<div class="alert alert-danger text-center">ID Pemeriksaan Tidak Boleh Kosong!</div>';
        exit;
    }

    $id_radiologi = validateAndSanitizeInput($_POST['id_radiologi']);

    // Query
    $Qry = $Conn->prepare("SELECT id_radiologi, permintaan_pemeriksaan FROM radiologi WHERE id_radiologi = ?");
    $Qry->bind_param("i", $id_radiologi);
    $Qry->execute();
    $Result = $Qry->get_result();
    $Data = $Result->fetch_assoc();
    $Qry->close();

    if (empty($Data['id_radiologi'])) {
        echo '<div class="alert alert-danger text-center">ID Radiologi Tidak Valid!</div>';
        exit;
    }

    echo '<input type="hidden" name="id_radiologi" value="'.$Data['id_radiologi'].'">';

    // Decode JSON
    $permintaan_pemeriksaan_array = json_decode($Data['permintaan_pemeriksaan'], true);

    // Validasi JSON
    if (!is_array($permintaan_pemeriksaan_array)) {
        echo '<div class="alert alert-danger">Format data permintaan pemeriksaan tidak valid.</div>';
        exit;
    }

    foreach ($permintaan_pemeriksaan_array as $list) {

        $id_master_pemeriksaan   = $list['id_master_pemeriksaan'] ?? '';
        $nama_pemeriksaan        = $list['nama_pemeriksaan'] ?? '-';
        $modalitas               = $list['modalitas'] ?? '-';
        $pemeriksaan_code        = $list['pemeriksaan_code'] ?? '-';
        $pemeriksaan_description = $list['pemeriksaan_description'] ?? '-';
        $pemeriksaan_sys         = $list['pemeriksaan_sys'] ?? '-';

        // ⬇️ PENTING: gunakan .= agar tidak tertimpa
        $konten_preview .= '
            <div class="table-responsive mb-3">
                <table class="table table-sm table-bordered">
                    <tbody>
                        <tr>
                            <td><small class="text-dark">Nama Pemeriksaan</small></td>
                            <td><small class="text-secondary">'.$nama_pemeriksaan.'</small></td>
                        </tr>
                        <tr>
                            <td><small class="text-dark"><i>Modality</i></small></td>
                            <td><small class="text-secondary">'.$modalitas.'</small></td>
                        </tr>
                        <tr>
                            <td><small class="text-dark"><i>LOINC Code</i></small></td>
                            <td><small class="text-secondary">'.$pemeriksaan_code.'</small></td>
                        </tr>
                        <tr>
                            <td><small class="text-dark"><i>LOINC Display</i></small></td>
                            <td><small class="text-secondary">'.$pemeriksaan_description.'</small></td>
                        </tr>
                        <tr>
                            <td><small class="text-dark"><i>LOINC System</i></small></td>
                            <td><small class="text-secondary">'.$pemeriksaan_sys.'</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        ';
    }
    echo '
        <div class="row mb-3">
            <div class="col-12">
                <label><small>Permintaan Pemeriksaan</small></label>
                <select name="id_master_pemeriksaan" id="id_master_pemeriksaan_ubah" class="form-control">
                    <option value="'.$id_master_pemeriksaan.'">'.$pemeriksaan_code.' - '.$nama_pemeriksaan.'</option>
                </select>
            </div>
        </div>
    ';
?>

<script>
    var konten = <?php echo json_encode($konten_preview); ?>;
    $('#preview_master_pemeriksaan').html(konten);
</script>
