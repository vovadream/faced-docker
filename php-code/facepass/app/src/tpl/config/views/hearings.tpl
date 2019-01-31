<div id='settings_hearing' class='overfltable'>
    <div id='HearingsTable'>
        <table class='akkt' border='1' cellpadding='5'>
            <tr>
                <th>№</th>
                <th>№ кабинета</th>
                <th>Название</th>
                <th>Код</th>
                <th>Дата слушания</th>
                <th>Код сотрудника</th>
                <th>№ филиала</th>
                <th>Дата</th>
                <th>Время</th>
                <th></th>
                </tr>

            <?php if (isset($hearings['status'])) { ?>
            <tr>
                <td class='error'><?= $hearings['message'] ?></td>
                </tr>
            <?php } else { for ($i = 0; $i < count($hearings); $i++) { ?>
            <tr>
                <td><?= $hearings[$i]->id ?></td>
                <td><?= $hearings[$i]->room_id ?></td>
                <td><?= $hearings[$i]->name ?></td>
                <td><?= $hearings[$i]->code ?></td>
                <?php $date = new DateTime($hearings[$i]->hdate); ?>
                <td><?= $date->Format('d.m.Y') ?></td>
                <td><?= $hearings[$i]->worker_id ?></td>
                <td><?= $hearings[$i]->filial_id ?></td>
                <?php $date = new DateTime($hearings[$i]->date); ?>
                <td><?= $date->Format('d.m.Y') ?></td>
                <td><?= $hearings[$i]->time ?></td>
                <td class='button' onclick="sendAjax('/hearings/form/<?= $hearings[$i]->id ?>/', 'GET')">Изменить</td>
                </tr>
            <?php
            }
            }
            ?>
            <tr>
                <td class='button' onclick="sendAjax('/hearings/form/', 'GET')">Добавить новое слушание</td>
                </tr>
            </table>
        </div>
</div>