<?php
    include "../../_Config/Connection.php";
    if(empty($_POST['KeywordBy'])){
        echo '<input type="text" name="keyword" id="keyword" class="form-control">';
    }else{
        $keyword_by=$_POST['KeywordBy'];
        if($keyword_by=="service_category"){
            echo '<select type="text" name="keyword" id="keyword" class="form-control">';
            echo '  <option value="">Pilih</option>';
            $query = mysqli_query($Conn, "SELECT DISTINCT service_category FROM master_service_prices ORDER BY service_category ASC");
            while ($data = mysqli_fetch_array($query)) {
                $service_category= $data['service_category'];
                echo '  <option value="'.$service_category.'">'.$service_category.'</option>';
            }
            echo '</select>';
        }else{
            if($keyword_by=="modality"){
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
                if($keyword_by=="insurance_type"){
                    echo '<select type="text" name="keyword" id="keyword" class="form-control">';
                    echo '  <option value="">Pilih</option>';
                    $query = mysqli_query($Conn, "SELECT DISTINCT insurance_type FROM master_service_prices ORDER BY insurance_type ASC");
                    while ($data = mysqli_fetch_array($query)) {
                        $insurance_type= $data['insurance_type'];
                        echo '  <option value="'.$insurance_type.'">'.$insurance_type.'</option>';
                    }
                    echo '</select>';
                }else{
                   echo '<input type="text" name="keyword" id="keyword" class="form-control">';
                }
            }
        }
    }
?>