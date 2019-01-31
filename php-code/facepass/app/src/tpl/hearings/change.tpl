<h2>Изменение данных слушания - <?= $hearings[0]->name ?> </h2>
<form name='updateHearingForm'>
    <table><tr><td>Название</td><td><input type='text' name='name' value='<?= $hearings[0]->name ?>'></td></tr>
        <tr><td>Дата слушания</td><td><input type='date' name='hdate' value='<?= $hearings[0]->hdate ?>'></td></tr>
        <tr><td>Время слушания</td><td><input type='time' name='time' value='<?= $hearings[0]->time ?>'></td></tr>
        <tr><td>Код слушания</td><td><input type='text' name='code' value='<?= $hearings[0]->code ?>'></td></tr>
        <tr><td>Сотрудник</td><td><select name='worker_id'>
                    <?php if (!empty($workers)) { ?>
                    <option value='0' selected>Нет данных</option>
                    <? php } else { ?>
                    <option value='0' disabled>Не выбран сотрудник</option>
                    <?php for ($i = 0; $i < count($workers); $i++) { ?>
                    <option value='<?= $workers[$i]->id ?>'
                    <?php if ($workers[$i]->id == $hearings[0]->worker_id) { ?> selected <?php } ?> ><?= $workers[$i]->first_name?> <?= $workers[$i]->patronymic ?> <?= $workers[$i]->surname ?></option>
                    <?php }
                    } ?>
                    </select></td></tr>
        <tr><td>Помещение</td><td><select name='room_id'>
                    <?php if (isset($rooms['status'])) { ?>
                    <option value='0' disabled>Нет данных</option>
                    <?php } else { ?>
                    <option value='0' disabled>Не выбрано помещение</option>
                    <?php for ($i = 0; $i < count($rooms); $i++) { ?>
                    <option value='<?=$rooms[$i]->id ?>'
                    <?php if ($rooms[$i]->id == $hearings[0]->room_id) ?> selected
                    ><?=$rooms[$i]->name ?></option>
                    <?php }
                    } ?>
                    </select></td></tr>
        <tr><td>Доступные помещения</td><td></td></tr>
        <?php for($i=0;$i<count($rooms); $i++) { ?>
        <tr><td><input type='checkbox' name='room_<?= $rooms[$i]->id ?>'
                <?php for ($j=0;$j<count($selectedRooms);$j++) {
                if (($selectedRooms[$j]->room_id==$rooms[$i]->id)&&($selectedRooms[$j]->status))
                { ?> checked
            <?php break;
                }
                } ?>
                > <?= $rooms[$i]->name ?></td><td></td></tr>
        <?php } ?>
        </table></form>
<div class='button' onclick="sendAjax('/hearings/<?= $hearings[0]->id ?>/', 'POST', 'updateHearingForm')">Изменить данные</div>
