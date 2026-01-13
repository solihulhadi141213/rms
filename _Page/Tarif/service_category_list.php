<?php
    //koneksi dan Funcition
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";

    $query = mysqli_query($Conn, "SELECT DISTINCT service_category FROM master_service_prices ORDER BY service_category ASC");
    while ($data = mysqli_fetch_array($query)) {
        $service_category = $data['service_category'];
        echo '<option value="'.$service_category.'">';
    }
?>