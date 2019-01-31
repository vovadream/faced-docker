<div id='settings_filial_room' class='overfltable'>

    <div class='button' onclick="sendAjax('/rooms/form/', 'GET')">Добавить новый кабинет в филиал</div>
    <br><br>

    <table class='akkt' border='1' cellpadding='5'>
        <tr>
            <th>№</th>
            <th>Название</th>
            <th>Номер</th>
            <th>Рабочее время</th>
            <th>№ сотрудника</th>
            <th>Номер отдела</th>
            <th></th>
        </tr>

        <?php if (isset($rooms['status'])) { ?>
        <tr>
            <td class='error'><?= $rooms['message'] ?></td>
        </tr>
        <?php  } else { for ($i = 0; $i < count($rooms); $i++) { ?>
        <tr>
            <td><?= $rooms[$i]->id ?></td>
            <td><?= $rooms[$i]->name ?></td>
            <td><?= $rooms[$i]->number ?></td>
            <td></td>
            <td><?= $rooms[$i]->worker_id ?></td>
            <td><?= $rooms[$i]->filial_id ?></td>
            <td class='button' onclick="sendAjax('/rooms/form/<?= $rooms[$i]->id ?>/', 'GET')">Изменить</td>
        </tr>
        <?php
        }
        }
        ?>
    </table>
</div>