<div id='settings_access' class='overfltable'>
    <div id='UserAccessTable'>
        <div class='button' onclick="sendAjax('/useraccess/form/', 'GET')">Добавить новый доступ</div><br><br>
    <table class='akkt' border='1' cellpadding='5'>
        <tr>
            <th>№</th>
            <th>Пользователь</th>
            <th>Слушание</th>
            <th>Код</th>
            <th>Статус</th>
            <th>Металлосканер</th>
            <th>Информация</th>
            <th>Действие</th>
            <th></th>
            </tr>

        <?php if (isset($useraccess['status'])) { ?>
        <tr>
            <td class='error'>{$useraccess['message']}</td>
            </tr>
        <?php } else { for ($i = 0; $i < count($useraccess); $i++) { ?>
        <tr>
            <td><?= $useraccess[$i]->id ?></td>
            <td><?= $useraccess[$i]->first_name.' '.$useraccess[$i]->patronymic.' '.$useraccess[$i]->surname ?></td>
            <td><?= $useraccess[$i]->hearingname ?></td>
            <td><?= $useraccess[$i]->code ?></td>
            <td>
                <?php if((($useraccess[$i]->status == 0) || ($useraccess[$i]->status == 2)) && ($useraccess[$i]->hearing_id == 0)) {
                echo "Нет на рабочем месте";
                }
                if(($useraccess[$i]->status == 1) && ($useraccess[$i]->hearing_id == 0)) { ?>
                Сотрудник на работе
                <?php }
                 if(($useraccess[$i]->status == 1) && ($useraccess[$i]->hearing_id != 0)) { ?>
                Посетитель в учреждении
                <?php }
                if(($useraccess[$i]->status == 2) && ($useraccess[$i]->hearing_id != 0)) { ?>
                Посетитель покинул учреждение
                <?php } ?>
            </td>
            <td><?= $useraccess[$i]->metalscaner ?></td>
            <td></td>
            <td></td>
            <td><?= $useraccess[$i]->info ?></td>
            <td class='button' onclick="sendAjax('/useraccess/form/<?= $useraccess[$i]->id ?>/', 'GET')">Изменить</td>
            <?php if (($useraccess[$i]->hearing_id == 0) && (($useraccess[$i]->status == 0) || ($useraccess[$i]->status == 2))) { ?>
            <td class='button' onclick="sendAjax('/userpass/<?= $useraccess[$i]->user_id ?>/<?= $useraccess[$i]->id ?>/', 'POST')">Пропустить</td>
            <?php } if (($useraccess[$i]->hearing_id != 0) && ($useraccess[$i]->status == 0)) { ?>
            <td class='button' onclick="sendAjax('/userpass/<?= $useraccess[$i]->user_id ?>/<?=$useraccess[$i]->id ?>/', 'POST')">Пропустить</td>
            <?php } if ($useraccess[$i]->status == 1) { ?>
            <td class='button' onclick="sendAjax('/userpass_modify/<?= $useraccess[$i]->id ?>/', 'POST')">Выпустить</td>
            <?php } ?>
        </tr>
        <?php
        }
        }
        ?>
        </table>
    </div>
</div>

</div>