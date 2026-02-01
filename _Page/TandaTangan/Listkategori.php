<?php
    //koneksi dan session
    include "../../_Config/Connection.php";

    $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT DISTINCT kategori FROM master_signature"));
    if(!empty($jml_data)){
        $query = mysqli_query($Conn, "SELECT DISTINCT kategori FROM master_signature ORDER BY kategori ASC");
        while ($data = mysqli_fetch_array($query)) {
            $kategori = $data['kategori'];
            echo '<option value="'.$kategori.'">';
        }
    }
?>