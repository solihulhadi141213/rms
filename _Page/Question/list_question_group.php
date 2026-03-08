<?php
    //koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    $query_question = mysqli_query($Conn, "SELECT DISTINCT question_group FROM question ORDER BY question_group ASC");
    while ($data_question = mysqli_fetch_array($query_question)) {
        $question_group = $data_question['question_group'];
        echo '<option value="'.$question_group.'">';
    }
?>