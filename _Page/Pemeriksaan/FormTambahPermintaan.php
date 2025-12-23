<?php
    // Koneksi, Global Function, Session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    // Set Zona Waktu
    date_default_timezone_set("Asia/Jakarta");

    // Validasi Sesi Akses
    if(empty($SessionIdAccess)){
        echo '
            <div class="alert alert-danger">
                <small>Sesi Akses Sudah Berakhir. Silahkan Login Ulang!</small>
            </div>
        ';
        exit;
    }

    // Validasi id_kunjungan tidak boleh kosong
    if(empty($_POST['id_kunjungan'])){
        echo '
            <div class="alert alert-danger">
                <small>ID Kunjungan Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }

    // Buat Variabel id_kunjungan dan sanitasi
    $id_kunjungan = validateAndSanitizeInput($_POST['id_kunjungan']);

    // Buka URL SIMRS
    $status_connection_simrs = 1;
    $url_connection_simrs = GetDetailData($Conn,'connection_simrs','status_connection_simrs',$status_connection_simrs,'url_connection_simrs');

    //Dapatkan Token SIMRS
    $token = GetSimrsToken($Conn);

    // Jika Token Tidak Valid Dan Gagal Dibuat
    if ($token === false) {
        echo '
            <div class="alert alert-danger">
                <small>Gagal mendapatkan token SIMRS!</small>
            </div>
        ';
        exit;
    }

    // Mulai CURL service API SIMRS Untuk Mendapatkan Detail Kunjungan
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => ''.$url_connection_simrs.'/API/SIMRS/get_detail_kunjungan.php?id='.$id_kunjungan.'',
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

    $response = curl_exec($curl);
    curl_close($curl);

    // Ubah Response Menjadi Arry
    $data = json_decode($response, true);

    // Jika Response Tidak Valid
    if (empty($data['response']['code']) ||$data['response']['code'] != 200) {
        echo '
            <div class="alert alert-danger">
                <small>Gagal memuat data kunjungan<br> Pesan : '.$data['response']['message'].'</small>
            </div>
        ';
        exit;
    }

    // Buka Metadata
    $metadata = $data['metadata'];

    // Buat Variabel Penting
    $id_encounter = $metadata['id_encounter'];
    $tujuan       = $metadata['tujuan'];
    $id_dokter    = $metadata['id_dokter'];

    //Routing asal kiriman
    if($tujuan=="Rajal"){
        $asal_kiriman = $metadata['poliklinik'];
    }else{
        $asal_kiriman = $metadata['ruangan'];
    }

    //Tampilkan Form
    echo '
        <div class="row mb-2">
            <div class="col-12">
                <b><small>A. Informasi Pasien</small></b>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <label for="id_pasien"><small>No.RM</small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="text" readonly name="id_pasien" id="id_pasien" class="form-control" value="'.$metadata['pasien']['id_pasien'].'">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <label for="id_kunjungan"><small>ID.Reg</small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="text" readonly name="id_kunjungan" id="id_kunjungan" class="form-control" value="'.$id_kunjungan.'">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <label for="nama_pasien"><small>Nama Pasien</small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="text" name="nama_pasien" id="nama_pasien" class="form-control" value="'.$metadata['pasien']['nama'].'">
            </div>
        </div>
        <div class="row mb-2 mt-3">
            <div class="col-12">
                <b><small>B. Informasi Kunjungan</small></b>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <label for="tujuan"><small>Tujuan Kunjungan</small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="text" name="tujuan" id="tujuan" class="form-control" value="'.$metadata['tujuan'].'">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <label for="pembayaran"><small>Pembayaran</small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="text" name="pembayaran" id="pembayaran" class="form-control" value="'.$metadata['pembayaran'].'">
            </div>
        </div>
        <div class="row mb-2 mt-3">
            <div class="col-12">
                <b><small>C. Informasi Permintaan</small></b>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <label for="asal_kiriman"><small>Asal Kiriman</small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="text" name="asal_kiriman" id="asal_kiriman" class="form-control" value="'.$asal_kiriman.'">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <label for="tanggal_diminta"><small>Tanggal/Jam</small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-4">
                <input type="date" name="tanggal_diminta" id="tanggal_diminta" class="form-control" value="'.date('Y-m-d').'">
            </div>
            <div class="col-3">
                <input type="time" name="jam_diminta" id="jam_diminta" class="form-control" value="'.date('H:i').'">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <label for="priority"><small>Prioritisasi</small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <select name="priority" id="priority" class="form-control">
                    <option value="routine">Biasa</option>
                    <option value="urgent">Segera</option>
                    <option value="stat">Gawat</option>
                </select>
            </div>
        </div>
    ';

    // ===============================
    // MEMBUKA DATA REFERENSI DOKTER DARI SIMRS
    // ===============================
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
            <div class="alert alert-danger">
                <small>Gagal memuat data kunjungan<br> Pesan : '.$data['response']['message'].'</small>
            </div>
        ';
        exit;
    }

    $metadata_dokter = $data_doketer['metadata'];
    $list_dokter     = $metadata_dokter['list_dokter']?? [];

    // Jika Data Dokter Tidak Ada
    if (empty($list_dokter)) {
        echo '
            <div class="alert alert-danger">
                <small>Tidak Ada Data Dokter Yang Ditampilkan</small>
            </div>
        ';
        exit;
    }

    //Menampilkan Form
    echo '<div class="row mb-2">';
    echo '
            <div class="col-4">
                <label for="dokter_pengirim"><small>Dokter Pengirim</small></label>
            </div>
    ';
    echo '  <div class="col-1"><small>:</small></div>';
    echo '  <div class="col-7">';
    echo '      <select name="dokter_pengirim" id="dokter_pengirim" class="form-control">';
    echo '          <option value="">Pilih</option>';
    foreach ($list_dokter as $row) {
        $id_dokter_list      = $row['id_dokter'];
        $kode                = $row['kode'];
        $nama                = $row['nama'];
        $kategori            = $row['kategori'];
        $id_ihs_practitioner = $row['id_ihs_practitioner'];
        if($id_dokter== $id_dokter_list){
            echo '<option selected value="'.$id_dokter_list.'">'.$nama.'</option>';
        }else{
            echo '<option value="'.$id_dokter_list.'">'.$nama.'</option>';
        }
        
    }
    echo '      </select>';
    echo '  </div>';
    echo '</div>';

    echo '
        <div class="row mb-2">
            <div class="col-4">
                <label for="klinis"><small>Klinis Pasien</small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <select name="klinis[]" id="klinis" class="form-control" multiple></select>
            </div>
        </div>
    ';
    echo '
        <div class="row mb-2">
            <div class="col-4">
                <label for="alat_pemeriksa"><small>Modality/Alat</small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <select name="alat_pemeriksa" id="alat_pemeriksa" class="form-control">
                    <option value="">Pilih</option>
                    <option value="XR">X-Ray</option>
                    <option value="US">USG / Echo</option>
                    <option value="CT">CT Scan</option>
                    <option value="MR">MRI</option>
                    <option value="NM">Nuclear Medicine (Kedokteran nuklir)</option>
                    <option value="PT">PET Scan</option>
                    <option value="DX">Digital Radiography</option>
                    <option value="CR">Computed Radiography</option>
                </select>
            </div>
        </div>
    ';
    echo '
        <div class="row mb-2">
            <div class="col-4">
                <label for="permintaan_pemeriksaan"><small>Permintaan Pemerriksaan</small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <select name="permintaan_pemeriksaan" id="permintaan_pemeriksaan" class="form-control"></select>
            </div>
        </div>
    ';
    echo '
        <div class="row mb-2">
            <div class="col-4">
                <label for="pesan"><small>Pesan / Keterangan</small></label>
            </div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <textarea class="form-control" name="pesan" id="pesan"></textarea>
                <small class="text text-grayish">
                    <small>Pesan atau keterangan yang perlu disertakan. Misalnya : Tolong menggunakan kontras</small>
                </small>
            </div>
        </div>
    ';


?>