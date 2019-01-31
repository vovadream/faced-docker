<form enctype="multipart/form-data" action="<?php echo $web_path; ?>ocr/proc/" method="POST">
    Отправить этот файл: <input name="userfile" type="file" />
    <select name="docpage">
        <option value=1 selected>Кем выдан</option>
        <option value=2>ФИО</option>
        <option value=3>Прописка</option>
    </select>
    <input type="submit" value="Отправить" />
</form>
