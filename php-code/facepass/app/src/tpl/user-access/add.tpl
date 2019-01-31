<h2>Добавление доступа к слушанию</h2>
<form name='addUserAccessForm'>
    <table>
        <tr>
            <td>Пользователь</td>
            <td>
                <select name='user_id'>
                    <?php if (isset($users['status'])) { ?>
                    <option value='0' disabled>Нет данных</option>
                    <?php } else { ?>
                    <option value='0' disabled>Не выбран пользователь</option>
                    <?php for ($i = 0; $i < count($users); $i++) { ?>
                    <option value='<?= $users[$i]->id ?>'><?= $users[$i]->first_name ?> <?= $users[$i]->patronymic ?> <?= $users[$i]->surname ?></option>
                    <?php }
                    } ?>
                </select>
            </td>
        </tr>

        <tr><td>Слушание</td><td><select name='hearing_id'>
                    <option value='0' disabled>Не выбрано слушание</option>
                    <?php for($i=0;$i<count($hearings); $i++) { ?>
                    <option value='<?= $hearings[$i]->id ?>'><?=$hearings[$i]->name ?></option>
                    <?php } ?>
                    </select></td></tr></table>

    </form>
<div class='button' onclick=\"sendAjax('/useraccess/', 'POST', 'addUserAccessForm')\">Создать доступ</div>
        