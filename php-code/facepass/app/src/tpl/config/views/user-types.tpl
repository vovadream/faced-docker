<div id='settings_user_type' class='overfltable'>

    <div class='button' onclick="sendAjax('/user-types/form/', 'GET')">Добавить новый тип пользователей</div><br><br>

<table class='akkt' border='1' cellpadding='5'>
    <tr>
        <th>№</th>
        <th>Название</th>
        <th></th>
        </tr>

    <?php if (isset($userTypes['status'])) { ?>
    <tr>
        <td class='error'><?= $userTypes['message'] ?></td>
        </tr>
    <?php } else { for ($i = 0; $i < count($userTypes); $i++) { ?>
    <tr>
        <td><?= $userTypes[$i]->id ?></td>
        <td><?= $userTypes[$i]->name ?></td>
        <?php if ($userTypes[$i]->id > 3) ?>
        <td class='button' onclick="sendAjax('/user-types/form/<?= $userTypes[$i]->id ?>/', 'GET')">Изменить</td>
        <td class='button' onclick="sendAjax('deleteMark=<?= $marks[$i]->id ?>', 'DELETE')">Удалить</td>
        </tr>
    <?php
    }
    }
    ?>
    </table>
</div>