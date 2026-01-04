<?php
    //koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";
    
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

    //id_radiologi wajib terisi
    if(empty($_POST['id_radiologi'])){
        echo '
            <div class="row mb-3">
                <div class="col-12 text-center">
                    <div class="alert alert-danger"><small>ID Pemeriksaan Tiidak Boleh Kosong!</small></div>
                </div>
            </div>
        ';
        exit;
    }

    //Buat variabel 'id_radiologi' dan sanitasi
    $id_radiologi = validateAndSanitizeInput($_POST['id_radiologi']);

    //Buka Detail Koneksi Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM radiologi WHERE id_radiologi = ?");
    $Qry->bind_param("i", $id_radiologi);
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

    //Buat Variabel
    $kode_dokter_pengirim   = $Data['kode_dokter_pengirim'];
    $ihs_dokter_pengirim    = $Data['ihs_dokter_pengirim'];
    $nama_dokter_pengirim   = $Data['nama_dokter_pengirim'];
    $kode_dokter_penerima   = $Data['kode_dokter_penerima'];
    $ihs_dokter_penerima    = $Data['ihs_dokter_penerima'];
    $nama_dokter_penerima   = $Data['nama_dokter_penerima'];

    // ============================
    // Form Penerimaan Permintaan
    // ============================
    
    // Buka URL SIMRS
    $status_connection_simrs = 1;
    $url_connection_simrs = GetDetailData($Conn,'connection_simrs','status_connection_simrs',$status_connection_simrs,'url_connection_simrs');

    //Dapatkan Token SIMRS
    $token = GetSimrsToken($Conn);

        $curl2 = curl_init();
    curl_setopt_array($curl2, array(
        CURLOPT_URL => ''.$url_connection_simrs.'/API/SIMRS/get_dokter.php',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'token: '.$token.'',
            'X-API-Key: ••••••'
        ),
    ));
    $response_dokter = curl_exec($curl2);
    curl_close($curl2);
    
    // Ubah Response Menjadi Arry
    $data_doketer = json_decode($response_dokter, true);

    // Jika Response Tidak Valid
    if (empty($data_doketer['response']['code']) ||$data_doketer['response']['code'] != 200) {
        echo '
            <div class="row mb-2">
                <div class="col-12">
                    <div class="alert alert-danger">
                        <small>Gagal memuat data dokter<br> Pesan : '.$data['response']['message'].'</small>
                    </div>
                </div>
            </div>
        ';
        exit;
    }

    $metadata_dokter = $data_doketer['metadata'];
    $list_dokter     = $metadata_dokter['list_dokter']?? [];
?>
<input type="hidden" name="id_radiologi" value="<?php echo $id_radiologi; ?>">
<div class="row mb-3">
    <div class="col-12">
        <label for="dokter_pengirim_edit">Dokter Pengirim</label>
        <select name="dokter_pengirim" id="dokter_pengirim_edit" class="form-control">
            <option value="">Pilih</option>
            <?php
                foreach ($list_dokter as $row) {
                    $id_dokter_list      = $row['id_dokter'];
                    $kode                = $row['kode'];
                    $nama                = $row['nama'];
                    $kategori            = $row['kategori'];
                    $id_ihs_practitioner = $row['id_ihs_practitioner'];
                    if($kode_dokter_pengirim== $kode){
                        echo '<option selected value="'.$id_dokter_list.'">'.$nama.'</option>';
                    }else{
                        echo '<option value="'.$id_dokter_list.'">'.$nama.'</option>';
                    }
                    
                }
            ?>
        </select>
    </div>
</div>

<div class="row mb-3">
    <div class="col-12">
        <label for="dokter_penerima_edit">Dokter Penerima</label>
        <select name="dokter_penerima" id="dokter_penerima_edit" class="form-control">
            <option value="">Pilih</option>
            <?php
                foreach ($list_dokter as $row2) {
                    $id_dokter_list2      = $row2['id_dokter'];
                    $kode2                = $row2['kode'];
                    $nama2                = $row2['nama'];
                    $kategori            = $row2['kategori'];
                    if($kode_dokter_penerima== $kode2){
                        echo '<option selected value="'.$id_dokter_list2.'">'.$nama2.'</option>';
                    }else{
                        echo '<option value="'.$id_dokter_list2.'">'.$nama2.'</option>';
                    }
                    
                }
            ?>
        </select>
    </div>
</div>