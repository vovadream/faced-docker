<div id='settings_equipment_type' class='overfltable'>

    <div class='button' onclick="sendAjax('/equipment/form/', 'GET')">Создать новый тип оборудования</div>
    <br><br>

    <table class='akkt' border='1' cellpadding='5'>
        <tr>
            <th>№</th>
            <th>Название</th>
            <th></th>
        </tr>

        <?php if (isset($equipment_types['status'])) { ?>
        <tr>
            <td class='error'><?= $equipment_types['message']?></td>
        </tr>
        <?php } else { for ($i = 0; $i < count($equipment_types); $i++) { ?>
        <tr>
            <td><?= $equipment_types[$i]->id ?></td>
            <td><?= $equipment_types[$i]->name ?></td>
            <td class='button' onclick="sendAjax('/equipment/form/<?= $equipment_types[$i]->id ?>/', 'GET')">Изменить</td>
            <td class='button' onclick="sendAjax('deleteMark=<?= $marks[$i]->id ?>', 'DELETE')">Удалить</td>
        </tr>
        <?php
        }
        }
        ?>
        </tr>
    </table>
</div>