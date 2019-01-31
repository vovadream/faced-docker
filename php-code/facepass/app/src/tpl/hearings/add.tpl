<h2>Создание сотрудника слушания</h2>
<form name='addHearingForm'>
    <table><tr><td>Название</td><td><input type='text' name='name'></td></tr>
        <tr><td>Дата слушания</td><td><input type='date' name='hdate'></td></tr>
        <tr><td>Время слушания</td><td><input type='time' name='time'></td></tr>
        <tr><td>Код слушания</td><td><input type='text' name='code'></td></tr>
        <tr><td>Помещение</td><td><select name='room_id'>
                    <?php if (isset($rooms['status'])) { ?>
                    <option value='0' disabled>Нет данных</option>
                    <?php } else { ?>
                    <option value='0' disabled>Не выбрано помещение</option>
                    <?php for ($i = 0; $i < count($rooms); $i++) { ?>
                    <option value='<?=$rooms[$i]->id ?>'><?= $rooms[$i]->name ?></option>
                    <?php }
                    } ?>
                    </select></td></tr>

        <tr><td>Сотрудник</td><td><select name='worker_id'>
                    <option value='0' disabled>Не выбран сотрудник</option>
                    <?php for($i=0;$i<count($workers); $i++) { ?>
                    <option value='{$workers[$i]->id}'><?= $workers[$i]->surname ?> <?= $workers[$i]->first_name ?> <?= $workers[$i]->patronymic ?></option>
                     <?php } ?>
                    </select></td></tr>
        <tr><td>Доступные помещения</td><td></td></tr>

        <?php for($i=0;$i<count($rooms); $i++) { ?>
        <tr><td><input type='checkbox' value='1' name='room_<?=$rooms[$i]->id ?>'><?=$rooms[$i]->name ?></td></tr>
        <?php } ?>

        </table></form>
<div class='button' onclick=\"sendAjax('/hearings/', 'POST', 'addHearingForm')\">Создать слушание</div>
