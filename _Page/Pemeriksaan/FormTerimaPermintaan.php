<?php
    // Koneksi, Global Function, Session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";
    include "../../_Config/FungsiAkses.php";

    // Validasi Sesi Akses
    if(empty($SessionIdAccess)){
        echo '
            <div class="alert alert-danger">
                <small>Sesi Akses Sudah Berakhir. Silahkan Login Ulang!</small>
            </div>
        ';
        exit;
    }

    // Validasi id_radiologi tidak boleh kosong
    if(empty($_POST['id_radiologi'])){
        echo '
            <div class="alert alert-danger">
                <small>ID Pemeriksaan Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }

    // Validasi status tidak boleh kosong
    if(empty($_POST['status'])){
        echo '
            <div class="alert alert-danger">
                <small>Kategori Permintaan Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }

    // Buat Variabelnya
    $id_radiologi = validateAndSanitizeInput($_POST['id_radiologi']);
    $status       = validateAndSanitizeInput($_POST['status']);

    //Buka Detail Radiologi Dengan Prepared Statment
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
    $id_access              = $Data['id_access'];
    $id_pasien              = $Data['id_pasien'];
    $id_kunjungan           = $Data['id_kunjungan'];
    $accession_number       = $Data['accession_number'];
    $nama_pasien            = $Data['nama_pasien'];
    $asal_kiriman           = $Data['asal_kiriman'];
    $alat_pemeriksa         = $Data['alat_pemeriksa'];
    $kode_dokter_pengirim   = $Data['kode_dokter_pengirim'];
    $ihs_dokter_pengirim    = $Data['ihs_dokter_pengirim'];
    $nama_dokter_pengirim   = $Data['nama_dokter_pengirim'];
    $kode_dokter_penerima   = $Data['kode_dokter_penerima'];
    $ihs_dokter_penerima    = $Data['ihs_dokter_penerima'];
    $nama_dokter_penerima   = $Data['nama_dokter_penerima'];
    $radiografer            = $Data['radiografer'] ?? "-";
    $kesan                  = $Data['kesan'];
    $klinis                 = $Data['klinis'];
    $permintaan_pemeriksaan = $Data['permintaan_pemeriksaan'];
    $kv                     = $Data['kv'];
    $ma                     = $Data['ma'];
    $sec                    = $Data['sec'];
    $tujuan                 = $Data['tujuan'];
    $pembayaran             = $Data['pembayaran'];
    $datetime_diminta       = $Data['datetime_diminta'];
    $datetime_dikerjakan    = $Data['datetime_dikerjakan'];
    $datetime_hasil         = $Data['datetime_hasil'];
    $datetime_selesai       = $Data['datetime_selesai'];
    $status_pemeriksaan     = $Data['status_pemeriksaan'];

    //Nama Radiografer
    if(empty($data['radiografer'])){
        $radiografer = "-";
    }

    // Nama Modalitas
    $modalitas_list = [
        'XR' => 'X-Ray',
        'CT' => 'CT-Scan',
        'US' => 'USG',
        'MR' => 'MRI',
        'NM' => 'Nuclear Medicine (Kedokteran Nuklir)',
        'PT' => 'PET Scan',
        'DX' => 'Digital Radiography',
        'CR' => 'Computed Radiography'
    ];

    // Ambil nama modalitas
    $nama_modalitas = $modalitas_list[$alat_pemeriksa] ?? '-';
    
    //Routing Status
    if($status_pemeriksaan=="Diminta"){
        $label_status = '<span class="badge bg-warning">Diminta</span>';
    }else{
        if($status_pemeriksaan=="Dikerjakan"){
            $label_status = '<span class="badge bg-info">Dikerjakan</span>';
        }else{
            if($status_pemeriksaan=="Hasil"){
                $label_status = '<span class="badge bg-primary">Hasil</span>';
            }else{
                if($status_pemeriksaan=="Selesai"){
                    $label_status = '<span class="badge bg-success">Selesai</span>';
                }else{
                    if($status_pemeriksaan=="Batal"){
                        $label_status = '<span class="badge bg-danger">Batal</span>';
                    }else{
                        $label_status = '<span class="badge bg-dark">None</span>';
                    }
                }
            }
        }
    }

    //Format Tanggal
    $datetime_diminta     = formatDateTimeStrict($Data['datetime_diminta']);
    $datetime_dikerjakan  = formatDateTimeStrict($Data['datetime_dikerjakan']);
    $datetime_hasil       = formatDateTimeStrict($Data['datetime_hasil']);
    $datetime_selesai     = formatDateTimeStrict($Data['datetime_selesai']);

    echo '
        <div class="row mb-2">
            <div class="col-5"><small>ID Radiologi</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-6"><small class="text text-grayish">'.$id_radiologi.'</small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-5"><small>No.RM</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-6"><small class="text text-grayish">'.$id_pasien.'</small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-5"><small>Nama Pasien</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-6"><small class="text text-grayish">'.$nama_pasien.'</small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-5"><small>Asal Kiriman</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-6"><small class="text text-grayish">'.$asal_kiriman.'</small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-5"><small>Modalitas/Alat</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-6"><small class="text text-grayish">'.$nama_modalitas.'</small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-5"><small>Tanggal Permintaan</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-6"><small class="text text-grayish">'.$datetime_diminta.'</small>
            </div>
        </div>
        <div class="row mb-3 border-1 border-bottom">
            <div class="col-12 mb-3"><br></div>
        </div>
    ';

    if($status=="Pembatalan"){
        // ============================
        // Form Penolakan (Pembatalan)
        // ============================
        echo '
            <input type="hidden" name="id_radiologi" value="'.$id_radiologi.'">
            <input type="hidden" name="status" value="Batal">
            <div class="row mb-3">
                <div class="col-12">
                    <label for="radiografer">Radiografer</label>
                    <input type="text" readonly name="radiografer" id="radiografer" class="form-control" value="'.$access_name.'">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-12">
                    <label for="alasan_pembatalan">Alasan Pembatalan / Penolakan</label>
                    <textarea class="form-control" name="alasan_pembatalan" id="alasan_pembatalan"></textarea>
                    <small>
                        <small>
                            Silahkan isi alasan pembatalan secara singkat yang menjelaskan alasan logis anda melakukan pembatalan tersebut.
                        </small>
                    </small>
                </div>
            </div>        
        ';
    }else{
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

        // Jika Data Dokter Tidak Ada
        if (empty($list_dokter)) {
            echo '
                <div class="row mb-2">
                    <div class="col-12">
                        <div class="alert alert-danger">
                            <small>Tidak Ada Data Dokter Yang Ditampilkan</small>
                        </div>
                    </div>
                </div>
            ';
            exit;
        }

        // Form Penerimaan
        echo '
            <input type="hidden" name="id_radiologi" value="'.$id_radiologi.'">
            <input type="hidden" name="status" value="Dikerjakan">
            <div class="row mb-3">
                <div class="col-12">
                    <label for="radiografer">Radiografer</label>
                    <input type="text" readonly name="radiografer" id="radiografer" class="form-control" value="'.$access_name.'">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-12">
                    <label for="tanggal_dikerjakan">Tanggal & Jam</label>
                    <div class="input-group">
                        <input type="date" name="tanggal_dikerjakan" id="tanggal_dikerjakan" class="form-control" value="'.date('Y-m-d').'">
                        <input type="time" name="jam_dikerjakan" id="jam_dikerjakan" class="form-control" value="'.date('H:i').'">
                    </div>
                </div>
            </div>
        ';

        //Menampilkan Form Dokter
        echo '<div class="row mb-2">';
        echo '<div class="col-12">';
        echo '      <label for="dokter_penerima"><small>Dokter Penerima</small></label>';
        echo '      <select name="dokter_penerima" id="dokter_penerima" class="form-control">';
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
    }
?>