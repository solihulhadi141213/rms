<?php
    include "../../_Config/Connection.php";
?>

<div class="row mb-3">
    <div class="col-md-12">
        <label for="question_group">
            <small>Group Pertanyaan</small>
        </label>
        <input type="text" class="form-control" name="question_group" id="question_group" list="list_question_group" required>
        <datalist id="list_question_group">
            <?php
                $query_question = mysqli_query($Conn, "SELECT DISTINCT question_group FROM question ORDER BY question_group ASC");
                while ($data_question = mysqli_fetch_array($query_question)) {
                    $question_group = $data_question['question_group'];
                    echo '<option value="'.$question_group.'">';
                }
            ?>
        </datalist>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-12">
        <label for="question_text"><small>Pertanyaan</small></label>
        <input type="text" class="form-control" name="question_text" id="question_text" required>
        <small class="text text-grayish">Kalimat pertanyaan dalam bentuk text</small>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-12">
        <label for="question_type">
            <small>Tipe Pertanyaan</small>
        </label>
        <select name="question_type" id="question_type" class="form-control" required>
            <option value="">Pilih</option>
            <option value="boolean">boolean</option>
            <option value="decimal">decimal</option>
            <option value="integer">integer</option>
            <option value="date">date</option>
            <option value="string">string</option>
            <option value="text">text</option>
        </select>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-12">
        <label for="id_questionnaire"><small>ID Satu Sehat</small></label>
        <input type="text" class="form-control" name="id_questionnaire" id="id_questionnaire">
        <small class="text text-grayish">Jika pertanyaan sudah pernah di kirim ke satu sehat</small>
    </div>
</div>