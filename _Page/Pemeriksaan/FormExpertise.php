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
                    <div class="alert alert-danger"><small>ID Pemeriksaan Tiidak Boleh Kosong!</small></div>
                </div>
            </div>
        ';
        exit;
    }

    //'title' wajib terisi
    if(empty($_POST['title'])){
        echo '
            <div class="row mb-3">
                <div class="col-12 text-center">
                    <div class="alert alert-danger"><small>Title Expertise Tiidak Boleh Kosong!</small></div>
                </div>
            </div>
        ';
        exit;
    }

    //Buat variabel 'id_radiologi' dan 'title'
    $id_radiologi = validateAndSanitizeInput($_POST['id_radiologi']);
    $title        = validateAndSanitizeInput($_POST['title']);

    //Buka Detail Koneksi Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM radiologi_local_exp WHERE id_radiologi = ? ");
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

    // Jika Kosong
    if(empty($Data['id_radiologi_local_exp'])){
        $konten ="";
    }else{
        $konten = $Data[$title];
    }

    // Routing Berdasarkan title
    if($title=="temuan"){
        $title_label ='Temuan <i>(Findings)</i>';
        $title_description = "Deskripsi objektif dari apa yang terlihat pada hasil pemeriksaan radiologi.";
    }else{
        if($title=="kesan"){
            $title_label ='Kesan <i>(Impression / Conclusion)</i>';
            $title_description = "Interpretasi klinis dari temuan - kesimpulan diagnostik.";
        }else{
            if($title=="saran"){
                $title_label ='saran <i>(Recommendation)</i>';
                $title_description = "Anjuran tindak lanjut berdasarkan hasil pemeriksaan.";
            }else{
                if($title=="catatan"){
                    $title_label ='Catatan <i>(Notes / Remarks)</i>';
                    $title_description = "Informasi tambahan di luar interpretasi utama.";
                }else{
                    $title_label ="";
                    $title_description = "";
                }
            }
        }
    }
?>
<input type="hidden" name="id_radiologi" value="<?php echo $id_radiologi; ?>">
<input type="hidden" name="title" value="<?php echo $title; ?>">


<div class="row mb-3">
    <div class="col-12">
        <label for="isi_expertise"><?php echo $title_label; ?></label>
        <input type="hidden" name="isi_expertise" id="isi_expertise">
        <div id="editor_expertise" style="height: 250px;">
            <?php echo $konten; ?>
        </div>
        <small class="text text-grayish"><?php echo $title_description; ?></small>
    </div>
</div>