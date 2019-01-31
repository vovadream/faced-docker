<div id='nullAccountUserPhoto' class='userPhoto margins'>
    <?php  $s = "$userinfo[0]->user_photo<img src='base_path() images/icons/Vhod_photo.PNG' class='bigIcon' \">";  ?>
    <img src='<?= base_path() ?>images/icons/Vhod_photo.PNG' class='bigIcon' \">
</div>
<form name='changeNullAccountForm'>
    <table>
        <tr>
            <td>Фамилия</td>
            <td><input class='margins boryes' type='text' name='surname' value='<?= $user[0]->surname ?>'></td>
        </tr>
        <tr>
            <td>Имя</td>
            <td><input class='margins boryes' type='text' name='first_name' value='<?= $user[0]->first_name ?>'></td>
        </tr>
        <tr>
            <td>Отчество</td>
            <td><input class='margins boryes' type='text' name='patronymic' value='<?= $user[0]->patronymic ?>'></td>
        </tr>
        <tr>
            <td>Статус</td>
            <td><select name='user_type_id'>
                    <?php if(isset($user_types['status'])) { ?>
                    <option value='0'>Нет данных</option>
                    <?php } else { ?>
                    <option value='0' disabled>Не выбран статус</option>
                    <?php for($i=0;$i<count($user_types);$i++) {
                    if (($user_types[$i]->id==3)||($user_types[$i]->main_class==3)){ ?>
                    <option value='<?= $user_types[$i]->id ?>'<?php if ($user[0]->user_type_id==$user_types[$i]->id) { ?> selected } ?>><?= $user_types[$i]->name ?></option>
                    <?php
                    }
                    }
                    }
                    }
                    ?>
                </select></td>
        </tr>
    </table>
</form>
<div class='button greenak margins'>Сфотографировать</div><br>
<div class='button greenak margins'>Добавить копированием</div><br>
<div class='button greenak margins'>Громкая связь</div><br>
<div class='button margins' onclick="sendAjax('/nullaccount/update/<?= $id ?>/', 'POST', 'changeNullAccountForm')">
    Выпустить
</div>