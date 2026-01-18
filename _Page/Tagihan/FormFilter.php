<?php
    include "../../_Config/Connection.php";
    if(empty($_POST['KeywordBy'])){
        echo '<input type="text" name="keyword" id="keyword" class="form-control">';
    }else{
        $keyword_by=$_POST['KeywordBy'];
        if($keyword_by=="status_pemeriksaan"){
            echo '<select type="text" name="keyword" id="keyword" class="form-control">';
            echo '  <option value="">Pilih</option>';
            $query = mysqli_query($Conn, "SELECT DISTINCT status_pemeriksaan FROM radiologi ORDER BY status_pemeriksaan ASC");
            while ($data = mysqli_fetch_array($query)) {
                $status_pemeriksaan= $data['status_pemeriksaan'];
                echo '  <option value="'.$status_pemeriksaan.'">'.$status_pemeriksaan.'</option>';
            }
            echo '</select>';
        }else{
            if($keyword_by=="alat_pemeriksa"){
                echo '
                    <select name="keyword" id="keyword" class="form-control">
                        <option value="">Pilih</option>
                        <option value="XR">X-Ray</option>
                        <option value="CT">CT-Scan</option>
                        <option value="US">USG</option>
                        <option value="MR">MRI</option>
                        <option value="NM">Nuclear Medicine (Kedokteran nuklir)</option>
                        <option value="PT">PET Scan</option>
                        <option value="DX">Digital Radiography</option>
                        <option value="CR">Computed Radiography</option>
                    </select>
                ';
            }else{
                if($keyword_by=="datetime_diminta"){
                    echo '<input type="date" name="keyword" id="keyword" class="form-control">';
                }else{
                    if($keyword_by=="tujuan"){
                        echo '<select type="text" name="keyword" id="keyword" class="form-control">';
                        echo '  <option value="">Pilih</option>';
                        $query = mysqli_query($Conn, "SELECT DISTINCT tujuan FROM radiologi ORDER BY tujuan ASC");
                        while ($data = mysqli_fetch_array($query)) {
                            $tujuan= $data['tujuan'];
                            echo '  <option value="'.$tujuan.'">'.$tujuan.'</option>';
                        }
                        echo '</select>';
                    }else{
                        if($keyword_by=="pembayaran"){
                            echo '<select type="text" name="keyword" id="keyword" class="form-control">';
                            echo '  <option value="">Pilih</option>';
                            $query = mysqli_query($Conn, "SELECT DISTINCT pembayaran FROM radiologi ORDER BY pembayaran ASC");
                            while ($data = mysqli_fetch_array($query)) {
                                $pembayaran= $data['pembayaran'];
                                echo '  <option value="'.$pembayaran.'">'.$pembayaran.'</option>';
                            }
                            echo '</select>';
                        }else{
                            echo '<input type="text" name="keyword" id="keyword" class="form-control">';
                        }
                    }
                }
            }
        }
    }
?>