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

    //'id_radiologi' wajib terisi
    if(empty($_POST['id_radiologi'])){
        echo '
            <div class="row mb-3">
                <div class="col-12 text-center">
                    <div class="alert alert-danger"><small>ID Pemeriksaan Radiologi Tidak Boleh Kosong!</small></div>
                </div>
            </div>
        ';
        exit;
    }

    //'id_question' wajib terisi
    if(empty($_POST['id_question'])){
        echo '
            <div class="row mb-3">
                <div class="col-12 text-center">
                    <div class="alert alert-danger"><small>ID Pertanyaan Tidak Boleh Kosong!</small></div>
                </div>
            </div>
        ';
        exit;
    }

    //Buat variabel 'id_question' dan sanitasi
    $id_radiologi = validateAndSanitizeInput($_POST['id_radiologi']);
    $id_question  = validateAndSanitizeInput($_POST['id_question']);

    //===========================================
    //BUKA PERTANYAAN 'question'
    //===========================================
    $Qry = $Conn->prepare("SELECT * FROM question WHERE id_question = ?");
    $Qry->bind_param("i", $id_question);
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
    $id_question      = $Data['id_question'];
    $id_questionnaire = $Data['id_questionnaire'] ?? "-";
    $link_id          = $Data['link_id'];
    $question_group   = $Data['question_group'];
    $question_text    = $Data['question_text'];
    $question_type    = $Data['question_type'];

    //===========================================
    // BUKA RADIOLOGI
    //===========================================
    $Qry2 = $Conn->prepare("SELECT * FROM radiologi WHERE id_radiologi = ?");
    $Qry2->bind_param("i", $id_radiologi);
    if (!$Qry2->execute()) {
        $error=$Conn->error;
        echo '
            <div class="alert alert-danger">
                <small>Terjadi kesalahan pada saat membuka data dari database!<br>Keterangan : '.$error.'</small>
            </div>
        ';
        exit;
    }
    $Result2 = $Qry2->get_result();
    $Data2 = $Result2->fetch_assoc();
    $Qry2->close();

    //Buat Variabel
    $id_access              = $Data2['id_access'];
    $id_pasien              = $Data2['id_pasien'];
    $id_kunjungan           = $Data2['id_kunjungan'];
    $accession_number       = $Data2['accession_number'];
    $nama_pasien            = $Data2['nama_pasien'];
    $priority               = $Data2['priority'];
    $asal_kiriman           = $Data2['asal_kiriman'];
    $alat_pemeriksa         = $Data2['alat_pemeriksa'];
    $kode_dokter_pengirim   = $Data2['kode_dokter_pengirim'];
    $ihs_dokter_pengirim    = $Data2['ihs_dokter_pengirim'];
    $nama_dokter_pengirim   = $Data2['nama_dokter_pengirim'];
    $kode_dokter_penerima   = $Data2['kode_dokter_penerima'];
    $ihs_dokter_penerima    = $Data2['ihs_dokter_penerima'];
    $nama_dokter_penerima   = $Data2['nama_dokter_penerima'];
    $radiografer            = $Data2['radiografer'] ?? "-";
    $pesan                  = $Data2['pesan'] ?? "-";
    $kesan                  = $Data2['kesan'];
    $klinis                 = $Data2['klinis'];
    $permintaan_pemeriksaan = $Data2['permintaan_pemeriksaan'];
    $kv                     = $Data2['kv'];
    $ma                     = $Data2['ma'];
    $sec                    = $Data2['sec'];
    $tujuan                 = $Data2['tujuan'];
    $pembayaran             = $Data2['pembayaran'];
    $datetime_diminta       = $Data2['datetime_diminta'];
    $datetime_dikerjakan    = $Data2['datetime_dikerjakan'];
    $datetime_hasil         = $Data2['datetime_hasil'];
    $datetime_selesai       = $Data2['datetime_selesai'];
    $status_pemeriksaan     = $Data2['status_pemeriksaan'];

    //Format Tanggal
    $sekarang = date('Y-m-d H:i:s');
    $dt = new DateTime($sekarang, new DateTimeZone('Asia/Jakarta'));
    $datetime_iso = $dt->format('Y-m-d\TH:i:sP');

    // ===========================================
    // Membuka Data Kunjungan
    // ===========================================
    
    // Buka URL SIMRS
    $status_connection_simrs = 1;
    $url_connection_simrs    = GetDetailData($Conn,'connection_simrs','status_connection_simrs',$status_connection_simrs,'url_connection_simrs');

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
    $data = json_decode($response, true);

    // Jika Response Tidak Valid
    if (empty($data['response']['code']) ||$data['response']['code'] != 200) {
        echo '
            <div class="row mb-3">
                <div class="col-12">
                    <div class="alert alert-danger">
                        <small>Gagal memuat data kunjungan<br> Pesan : '.$data['response']['message'].'</small>
                    </div>
                </div>
            </div>
        ';
        exit;
    }

    // Buka Metadata
    $metadata      = $data['metadata'] ?? [];
    $diagnosa_awal = $metadata['DiagAwal'] ?? '-';

    // Pastikan array pasien ada
    $pasien = $metadata['pasien'] ?? [];

    // Helper function untuk nilai yang mungkin kosong
    function getDisplayValue($value, $default = '-') {
        return (isset($value) && trim($value) !== '') ? $value : $default;
    }

    // Buat Variabel Penting
    $id_encounter   = getDisplayValue($metadata['id_encounter'] ?? null);
    $id_ihs         = getDisplayValue($pasien['id_ihs'] ?? null);
    $nama           = getDisplayValue($pasien['nama'] ?? null);
    $gender         = getDisplayValue($pasien['gender'] ?? null);
    $tempat_lahir   = getDisplayValue($pasien['tempat_lahir'] ?? null);
    $tanggal_lahir  = getDisplayValue($pasien['tanggal_lahir'] ?? null);
    $kontak         = getDisplayValue($pasien['kontak'] ?? null);
    $kontak_darurat = getDisplayValue($pasien['kontak_darurat'] ?? null);
    $nik            = getDisplayValue($pasien['nik'] ?? null);
    $no_bpjs        = getDisplayValue($pasien['no_bpjs'] ?? null);
    $propinsi       = getDisplayValue($pasien['propinsi'] ?? null);
    $kabupaten      = getDisplayValue($pasien['kabupaten'] ?? null);
    $kecamatan      = getDisplayValue($pasien['kecamatan'] ?? null);
    $desa           = getDisplayValue($pasien['desa'] ?? null);
    $alamat         = getDisplayValue($pasien['alamat'] ?? null);
    $perkawinan     = getDisplayValue($pasien['perkawinan'] ?? null);

    //Menghitung Usia Dan Format Tanggal Lahir
    $usia                    = hitungUsia($tanggal_lahir);
    $tanggal_lahir_formatted = formatTanggalLahir($tanggal_lahir);

    //===========================================
    // BUKA JAWABAN
    //===========================================

    $Qry3 = $Conn->prepare("SELECT * FROM question_response WHERE id_radiologi = ? AND id_question = ?");
    $Qry3->bind_param("ii", $id_radiologi, $id_question);
    if (!$Qry3->execute()) {
        $error=$Conn->error;
        echo '
            <div class="alert alert-danger">
                <small>Terjadi kesalahan pada saat membuka data dari database!<br>Keterangan : '.$error.'</small>
            </div>
        ';
        exit;
    }
    $Result3 = $Qry3->get_result();
    $Data3 = $Result3->fetch_assoc();
    $Qry3->close();

    //Buat Variabel
    if(empty($Data3['id_question_response'])){
        $id_question_response = "";
        $answer = "";
    }else{
        $id_question_response = $Data3['id_question_response'];
        $answer = $Data3['answer'];
    }
?>
<div class="row mb-2">
    <div class="col-4">
        <label for="id_radiologi">ID Radiologi</label>
    </div>
    <div class="col-1">:</div>
    <div class="col-7">
        <input type="text" name="id_radiologi" id="id_radiologi" class="form-control" value="<?php echo $id_radiologi; ?>">
    </div>
</div>
<div class="row mb-2">
    <div class="col-4">
        <label for="id_question">ID Question</label>
    </div>
    <div class="col-1">:</div>
    <div class="col-7">
        <input type="text" name="id_question" id="id_question" class="form-control" value="<?php echo $id_question; ?>">
    </div>
</div>
<div class="row mb-2">
    <div class="col-4">
        <label for="questionnaire">ID Questionnaire</label>
    </div>
    <div class="col-1">:</div>
    <div class="col-7">
        <input type="text" name="questionnaire" id="questionnaire" class="form-control" value="<?php echo $id_questionnaire; ?>">
    </div>
</div>
<div class="row mb-2">
    <div class="col-4">
        <label for="subject_reference">IHS Pasien</label>
    </div>
    <div class="col-1">:</div>
    <div class="col-7">
        <input type="text" name="subject_reference" id="subject_reference" class="form-control" value="<?php echo $id_ihs; ?>">
    </div>
</div>
<div class="row mb-2">
    <div class="col-4">
        <label for="subject_display">Nama Pasien</label>
    </div>
    <div class="col-1">:</div>
    <div class="col-7">
        <input type="text" name="subject_display" id="subject_display" class="form-control" value="<?php echo $nama_pasien; ?>">
    </div>
</div>
<div class="row mb-2">
    <div class="col-4">
        <label for="encounter_reference">ID Encounter</label>
    </div>
    <div class="col-1">:</div>
    <div class="col-7">
        <input type="text" name="encounter_reference" id="encounter_reference" class="form-control" value="<?php echo $id_encounter; ?>">
    </div>
</div>
<div class="row mb-2">
    <div class="col-4">
        <label for="authored">Tanggal / Jam</label>
    </div>
    <div class="col-1">:</div>
    <div class="col-7">
        <input type="text" name="authored" id="authored" class="form-control" value="<?php echo $datetime_iso; ?>">
    </div>
</div>
<div class="row mb-2">
    <div class="col-4">
        <label for="author_reference">ID Practitioner</label>
    </div>
    <div class="col-1">:</div>
    <div class="col-7">
        <input type="text" name="author_reference" id="author_reference" class="form-control" value="<?php echo $ihs_dokter_penerima; ?>">
    </div>
</div>
<div class="row mb-2">
    <div class="col-4">
        <label for="item_linkId">ID Pertanyaan</label>
    </div>
    <div class="col-1">:</div>
    <div class="col-7">
        <input type="text" name="item_linkId" id="item_linkId" class="form-control" value="<?php echo $link_id; ?>">
    </div>
</div>

<div class="row mb-2 mt-4">
    <div class="col-12 border-1 border-bottom"></div>
</div>
<div class="row mb-2">
    <div class="col-4">
        <label for="item_answer">Pertanyaan</label>
    </div>
    <div class="col-1">:</div>
    <div class="col-7">
        <small><i><?php echo $question_text; ?></i></small>
    </div>
</div>
<div class="row mb-2">
    <div class="col-4">
        <label for="item_answer">Jawaban</label>
    </div>
    <div class="col-1">:</div>
    <div class="col-7">
        <select name="item_answer" id="item_answer" class="form-control">
            <option <?php if($answer=="true"){echo "selected";} ?> value="1">Ya</option>
            <option selected <?php if($answer=="false"){echo "selected";} ?> value="0">Tidak</option>
        </select>
    </div>
</div>