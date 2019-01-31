<h2>Изменение группы комнат <?= $group_rooms[0]->name ?>
<form name='updateRoomsForm'>
    <table><tr>
            <td>Название</td>
            <td><input class='margins' name='name' value='<?= $group_rooms[0]->name?>'></td>
        </tr>
        <tr>
            <td>Ответственный сотрудник</td>
            <td><select class='margins' name='worker_id'>
                    <?php if (!empty($workers['status'])) { ?>
                    <option value='0'>Нет данных</option>
                    <?php } else { ?>
                    <option value='0'>Не выбран ответственный сотрудник</option>
                    <?php for ($i = 0; $i < count($workers); $i++) { ?>
                    <option value='<?= $workers[$i]->id ?>'
                    <?php if ($workers[$i]->id==$group_rooms[0]->worker_id) { ?>
                    selected>
                    <?php } else { ?>
                    >
                    <?php } ?>
                        <?= $workers[$i]->first_name ?> <?= $workers[$i]->patronymic ?> <?= $workers[$i]->surname ?></option>
                    <?php }
                    } ?>
                    </select></td></tr>
        <tr><td>Помещение-родитель</td>
            <td><select class='margins' name='parent_id'>
                    <?php if (empty($category)) { ?>
                    <option value='0'>Нет данных</option>
                    <?php } else { ?>
                    <option value='0'>Не выбрана категория</option>
                    <?php for ($i = 0; $i < count($category); $i++) { ?>
                    <option value='<?= $category[$i]->id?>'
                    <?php if ($category[$i]->id==$group_rooms[0]->parent_id) { ?>
                    selected>
                    <?php } else { ?>
                    >
                    <?php } ?>
                        <?= $category[$i]->name ?></option>
                    <?php }
                    } ?>
                    </select></td></tr>
    </table>
    </form>
<div class='button' onclick="sendAjax('/topology/group-rooms/update/<?= $group_id ?>/', 'POST', 'updateRoomsForm');">Редактировать</div>
