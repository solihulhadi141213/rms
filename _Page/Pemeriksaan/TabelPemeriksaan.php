<?php
    //koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    //Zona Waktu
    date_default_timezone_set("Asia/Jakarta");


    $JmlHalaman = 0;
    $page       = 0;
    //Validasi Akses
    if(empty($SessionIdAccess)){
        echo '
            <tr>
                <td colspan="12" class="text-center">
                    <small class="text-danger">Sesi Akses Sudah Berakhir! Silahkan Login Ulang!</small>
                </td>
            </tr>
            <script>
                $("#page_info").html("0 / 0");
                $("#prev_button").prop("disabled", true);
                $("#next_button").prop("disabled", true);
            </script>
        ';
        exit;
    }
    //Keyword_by
    if(!empty($_POST['keyword_by'])){
        $keyword_by=$_POST['keyword_by'];
    }else{
        $keyword_by="";
    }
    //keyword
    if(!empty($_POST['keyword'])){
        $keyword=$_POST['keyword'];
    }else{
        $keyword="";
    }
    //batas
    if(!empty($_POST['batas'])){
        $batas=$_POST['batas'];
    }else{
        $batas="10";
    }
    //ShortBy
    if(!empty($_POST['ShortBy'])){
        $ShortBy=$_POST['ShortBy'];
    }else{
        $ShortBy="DESC";
    }
    //OrderBy
    if(!empty($_POST['OrderBy'])){
        $OrderBy=$_POST['OrderBy'];
    }else{
        $OrderBy="id_radiologi";
    }
    //Atur Page
    if(!empty($_POST['page'])){
        $page=$_POST['page'];
        $posisi = ( $page - 1 ) * $batas;
    }else{
        $page="1";
        $posisi = 0;
    }
    if(empty($keyword_by)){
        if(empty($keyword)){
            $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT id_radiologi FROM radiologi "));
        }else{
            $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT id_radiologi FROM radiologi WHERE id_pasien like '%$keyword%' OR id_kunjungan like '%$keyword%' OR nama_pasien like '%$keyword%' OR asal_kiriman like '%$keyword%' OR accession_number like '%$keyword%'"));
        }
    }else{
        if(empty($keyword)){
            $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT id_radiologi FROM radiologi "));
        }else{
            $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT id_radiologi FROM radiologi WHERE $keyword_by like '%$keyword%'"));
        }
    }
    //Mengatur Halaman
    $JmlHalaman = ceil($jml_data/$batas); 
    if(empty($jml_data)){
        echo '
            <tr>
                <td colspan="12" class="text-center">
                    <small class="text-danger">Tidak Ada Data Yang Ditemukan!</small>
                </td>
            </tr>
            <script>
                $("#page_info").html("0 / 0");
                $("#prev_button").prop("disabled", true);
                $("#next_button").prop("disabled", true);
            </script>
        ';
        exit;
    }
    $no = 1+$posisi;
    //KONDISI PENGATURAN MASING FILTER
    if(empty($keyword_by)){
        if(empty($keyword)){
            $query = mysqli_query($Conn, "SELECT id_radiologi, id_pasien, id_kunjungan, id_service_request, id_procedure, id_imaging_study, id_observation, id_diagnostic_report, pacs, accession_number, nama_pasien, priority, asal_kiriman, alat_pemeriksa, radiografer, tujuan, pembayaran, datetime_diminta, status_pemeriksaan  FROM radiologi ORDER BY $OrderBy $ShortBy LIMIT $posisi, $batas");
        }else{
            $query = mysqli_query($Conn, "SELECT id_radiologi, id_pasien, id_kunjungan, id_service_request, id_procedure, id_imaging_study, id_observation, id_diagnostic_report, pacs, accession_number, nama_pasien, priority, asal_kiriman, alat_pemeriksa, radiografer, tujuan, pembayaran, datetime_diminta, status_pemeriksaan FROM radiologi WHERE id_pasien like '%$keyword%' OR id_kunjungan like '%$keyword%' OR nama_pasien like '%$keyword%' OR asal_kiriman like '%$keyword%' OR accession_number like '%$keyword%' ORDER BY $OrderBy $ShortBy LIMIT $posisi, $batas");
        }
    }else{
        if(empty($keyword)){
            $query = mysqli_query($Conn, "SELECT id_radiologi, id_pasien, id_kunjungan, id_service_request, id_procedure, id_imaging_study, id_observation, id_diagnostic_report, pacs, accession_number, nama_pasien, priority, asal_kiriman, alat_pemeriksa, radiografer, tujuan, pembayaran, datetime_diminta, status_pemeriksaan FROM radiologi ORDER BY $OrderBy $ShortBy LIMIT $posisi, $batas");
        }else{ 
            $query = mysqli_query($Conn, "SELECT id_radiologi, id_pasien, id_kunjungan, id_service_request, id_procedure, id_imaging_study, id_observation, id_diagnostic_report, pacs, accession_number, nama_pasien, priority, asal_kiriman, alat_pemeriksa, radiografer, tujuan, pembayaran, datetime_diminta, status_pemeriksaan FROM radiologi WHERE $keyword_by like '%$keyword%' ORDER BY $OrderBy $ShortBy LIMIT $posisi, $batas");
        }
    }
    while ($data = mysqli_fetch_array($query)) {
        $id_radiologi         = $data['id_radiologi'];
        $id_pasien            = $data['id_pasien'];
        $id_kunjungan         = $data['id_kunjungan'];
        $id_service_request   = $data['id_service_request'];
        $id_procedure         = $data['id_procedure'];
        $id_imaging_study     = $data['id_imaging_study'];
        $id_observation       = $data['id_observation'];
        $id_diagnostic_report = $data['id_diagnostic_report'];
        $accession_number     = $data['accession_number'];
        $nama_pasien          = $data['nama_pasien'];
        $priority             = $data['priority'];
        $asal_kiriman         = $data['asal_kiriman'];
        $alat_pemeriksa       = $data['alat_pemeriksa'];
        $radiografer          = $data['radiografer'];
        $tujuan               = $data['tujuan'];
        $pembayaran           = $data['pembayaran'];
        $datetime_diminta     = $data['datetime_diminta'];
        $status_pemeriksaan   = $data['status_pemeriksaan'];
        $pacs                 = $data['pacs'] ?? null;
        $tanggal              = date('d/m/y', strtotime($datetime_diminta));
        $jam                  = date('H:i', strtotime($datetime_diminta));

        //Mendapatkan Inisial Nama Radiografer
        $radiografer = getInisialNama($data['radiografer'] ?? null);

        //Routing pembayaran
        if($pembayaran=="UMUM"){
            $pembayaran_code = "UMM";
            $labal_pembyaran = "text-dark";
        }else{
            $pembayaran_code = "ASR";
            $labal_pembyaran = "text-grayish";
        }

        // Nama Modalitas
        $nama_modalitas = [
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
        $modalitas_nama = $nama_modalitas[$alat_pemeriksa] ?? '-';

        //Routing Status
        $tombol_lanjutan = '';
        if($status_pemeriksaan=="Diminta"){
            $tombol_lanjutan = '
                <li>
                    <a class="dropdown-item text-success modal_terima_permintaan" href="javascript:void(0)" data-id="'.$id_radiologi .'" data-status="Terima">
                        <i class="bi bi-check-circle"></i> Terima
                    </a>
                </li>
                <li>
                    <a class="dropdown-item text-danger modal_terima_permintaan" href="javascript:void(0)" data-id="'.$id_radiologi .'" data-status="Pembatalan">
                        <i class="bi bi-x-circle"></i> Batalkan
                    </a>
                </li>
            ';
        }else{
            if($status_pemeriksaan=="Dikerjakan"){
                $tombol_lanjutan = '
                    <li>
                        <a class="dropdown-item modal_pengisian_expertise" href="javascript:void(0)" data-id="'.$id_radiologi .'">
                            <i class="bi bi-clipboard-check"></i> Pengisian Expertise
                        </a>
                    </li>
                ';
            }else{
                if($status_pemeriksaan=="Hasil"){
                    $tombol_lanjutan = '
                        <li>
                            <a class="dropdown-item modal_cetak_laporan" href="javascript:void(0)" data-id="'.$id_radiologi .'">
                                <i class="bi bi-printer"></i> Cetak Laporan
                            </a>
                        </li>
                    ';
                }else{
                    if($status_pemeriksaan=="Selesai"){
                        $tombol_lanjutan = '
                            <li>
                                <a class="dropdown-item modal_cetak_laporan" href="javascript:void(0)" data-id="'.$id_radiologi .'">
                                    <i class="bi bi-printer"></i> Cetak Laporan
                                </a>
                            </li>
                        ';
                    }else{
                         $tombol_lanjutan = "";
                    }
                }
            }
        }

        // Routing Label Status
        $map_status = [
            'diminta'     => 'REQ',
            'dikerjakan'  => 'PRC',
            'hasil'       => 'RES',
            'selesai'     => 'DON',
            'batal'       => 'CAN'
        ];
        $badge_status = [
            'REQ'  => 'secondary',
            'PRC' => 'warning',
            'RES'  => 'info',
            'DON' => 'success',
            'CAN' => 'danger',
            'UNK'  => 'dark'
        ];

        $key_status   = strtolower(trim($status_pemeriksaan));
        $label_status = $map_status[$key_status] ?? 'UNK';
        $badge_class  = $badge_status[$label_status] ?? 'dark';

        // Inisialisasi Jumlah Resource
        $jumlah_resource = 0;

        // Routing Service Request
        if(empty($id_service_request)){
            $sr = '
                <li>
                    <a href="javascript:void(0)" class="dropdown-item text-danger modal_service_request" data-id="'.$id_radiologi .'">
                        1. Service Request
                    </a>
                </li>
            ';
        }else{
            $sr = '
                <li>
                    <a href="javascript:void(0)" class="dropdown-item text-info modal_detail_service_request" data-id="'.$id_service_request .'">
                        1. Service Request
                    </a>
                </li>
            ';
            $jumlah_resource = $jumlah_resource + 1;
        }

        // Routing Procedure
        if(empty($id_procedure)){
            $pc = '
                <li>
                    <a href="javascript:void(0)" class="dropdown-item text-danger modal_procedure" data-id="'.$id_radiologi .'">
                        2. Procedure
                    </a>
                </li>
            ';
        }else{
            $pc = '
                <li>
                    <a href="javascript:void(0)" class="dropdown-item text-info modal_detail_procedure" data-id="'.$id_procedure .'">
                        2. Procedure
                    </a>
                </li>
            ';
            $jumlah_resource = $jumlah_resource + 1;
        }

        // Routing Imaging Study
        if(empty($id_imaging_study)){
            $is = '
                <li>
                    <a href="javascript:void(0)" class="dropdown-item text-danger modal_imaging_study" data-id="'.$id_radiologi .'">
                        3. Imaging Study
                    </a>
                </li>
            ';
        }else{
            $is = '
                <li>
                    <a href="javascript:void(0)" class="dropdown-item text-info modal_detail_imaging_study" data-id="'.$id_imaging_study .'">
                        3. Imaging Study
                    </a>
                </li>
            ';
            $jumlah_resource = $jumlah_resource + 1;
        }

        // Routing Observation
        if(empty($id_observation)){
            $ob = '
                <li>
                    <a href="javascript:void(0)" class="dropdown-item text-danger modal_observation" data-id="'.$id_radiologi .'">
                        4. Observation
                    </a>
                </li>
            ';
        }else{
            $ob = '
                <li>
                    <a href="javascript:void(0)" class="dropdown-item text-info modal_detail_observation" data-id="'.$id_observation .'">
                        4. Observation
                    </a>
                </li>
            ';
            $jumlah_resource = $jumlah_resource + 1;
        }

        // Routing Diagnostic Report
        if(empty($id_diagnostic_report)){
            $dr = '
                <li>
                    <a href="javascript:void(0)" class="dropdown-item text-danger modal_diagnostic_report" data-id="'.$id_radiologi .'">
                        5. Diagnostic Report
                    </a>
                </li>
            ';
        }else{
            $dr = '
                <li>
                    <a href="javascript:void(0)" class="dropdown-item text-info modal_detail_diagnostic_report" data-id="'.$id_diagnostic_report .'">
                        5. Diagnostic Report
                    </a>
                </li>
            ';
            $jumlah_resource = $jumlah_resource + 1;
        }

        if(empty($jumlah_resource)){
            $border_satu_sehat = "border border-secondary";
            $text_satu_sehat   = "text-secondary";
        }else{
            if($jumlah_resource==5){
                $border_satu_sehat = "bg-success";
                $text_satu_sehat   = "text-white";
            }else{
                $border_satu_sehat = "border border-success";
                $text_satu_sehat   = "text-success";
            }
        }


        // Routing label pacs
        if(empty($pacs)){
            $pacs_label = '
                <a href="javascript:void(0);" class="modal_order_pacs" data-id="'.$id_radiologi.'">
                    <span class="badge border border-danger text-danger" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Kirim Order Ke PACS">
                        <i class="bi bi-send"></i>
                    </span>
                </a>
            ';
        }else{
            $pacs_label = '
                <a href="javascript:void(0);" class="modal_detail_pacd" data-id="'.$accession_number.'">
                    <span class="badge border border-success text-success" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Lihat Detail Order">
                        <i class="bi bi-check"></i>
                    </span>
                </a>
            ';
        }

        //Routing Periority
        if($priority=="routine"){
            $priority_label='bg-success';
        }else{
            if($priority=="urgent"){
                $priority_label='bg-warning';
            }else{
                if($priority=="stat"){
                    $priority_label='bg-danger';
                }else{
                    $priority_label='bg-dark';
                }
            }
        }
        //klasifikasi prioritas
        $priority_list = [
            'routine' => 'Biasa',
            'urgent'  => 'Segera',
            'stat'    => 'Gawat'
        ];
        $priority_name = $priority_list[$priority] ?? '-';
        
        // Labeling tujuan
        if($tujuan=="Rajal"){
            $labal_tujuan = 'text-success';
        }else{
            $labal_tujuan = 'text-warning';
        }
       
        echo '
            <tr>
                <td><small>'.$no.'</small></td>
                <td>
                    <a href="javascript:void(0);" class="modal_detail" data-id="'.$id_radiologi .'" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="ACN: '.$accession_number.'">
                       <small class="underscore_doted">'.$nama_pasien.'</small>
                    </a>
                </td>
                <td><small>'.$id_pasien.'</small></td>
                <td><small>'.$tanggal.'</small></td>
                <td><small>'.$jam.'</small></td>
                <td class="text-center">
                    <small class="text '.$labal_pembyaran.'" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="'.$pembayaran.'">
                        '.$pembayaran_code.'
                    </small>
                </td>
                <td class="text-center">
                    <small class="'.$labal_tujuan.'" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="'.$asal_kiriman.'">
                        '.$tujuan.'
                    </small>
                </td>
                <td class="text-center">
                    <small data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="'.$modalitas_nama.'">
                        '.$alat_pemeriksa.'
                    </small>
                </td>
                <td>
                    <small data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="'.$data['radiografer'].'">
                        '.$radiografer.'
                    </small>
                </td>
                <td class="text-center">
                    <a href="javascript:void(0);" class="badge '.$border_satu_sehat.' '.$text_satu_sehat.'" data-bs-toggle="dropdown" aria-expanded="false">
                        '.$jumlah_resource.'/5
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="">
                        '.$sr.'
                        '.$pc.'
                        '.$is.'
                        '.$ob.'
                        '.$dr.'
                    </ul>
                </td>
                <td class="text-center">'.$pacs_label.'</td>
                <td class="text-center">
                    <span class="badge bg-'.$badge_class.'" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="'.$status_pemeriksaan.'">
                        '.$label_status.'
                    </span>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-dark btn-floating"  data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-three-dots-vertical"></i>
                        <span class="position-absolute top-0 start-100 translate-middle p-2 '.$priority_label.' border border-light rounded-circle" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="'.$priority_name.'">
                            <span class="visually-hidden">New alerts</span>
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="">
                        <li class="dropdown-header text-start">
                            <h6>Option</h6>
                        </li>
                        <li>
                            <a class="dropdown-item modal_detail" href="javascript:void(0)" data-id="'.$id_radiologi .'">
                                <i class="bi bi-info-circle"></i> Detail
                            </a>
                        </li>
                        '.$tombol_lanjutan.'
                        <li>
                            <a class="dropdown-item modal_edit" href="javascript:void(0)" data-id="'.$id_radiologi .'">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item modal_hapus" href="javascript:void(0)" data-id="'.$id_radiologi .'">
                                <i class="bi bi-x"></i> Hapus
                            </a>
                        </li>
                    </ul>
                </td>
            </tr>
        ';
        $no++;
    }
?>
<script>
    //Creat Javascript Variabel
    var page_count  = <?php echo $JmlHalaman; ?>;
    var curent_page = <?php echo $page; ?>;
    
    //Put Into Pagging Element
    $('#page_info').html('Page : '+curent_page+' / '+page_count+'');
    
    //Set Pagging Button
    if(curent_page==1){
        $('#prev_button').prop('disabled', true);
    }else{
        $('#prev_button').prop('disabled', false);
    }
    if(page_count<=curent_page){
        $('#next_button').prop('disabled', true);
    }else{
        $('#next_button').prop('disabled', false);
    }
</script>