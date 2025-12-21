<?php
    include "../../_Config/Connection.php";
    if(empty($_POST['KeywordBy'])){
        echo '<input type="text" name="keyword" id="keyword" class="form-control">';
    }else{
        $keyword_by=$_POST['KeywordBy'];
        if($keyword_by=="modalitas"){
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
            echo '<input type="text" name="keyword" id="keyword" class="form-control">';
        }
    }
?>