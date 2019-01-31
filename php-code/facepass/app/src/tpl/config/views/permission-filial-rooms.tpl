<div id='settings_permission_to_department' class='overfltable'>
    <select name='permissionType' onchange="sendAjax('/filial-departments/formroomstable/'+this.value+'/', 'GET');">
    <option value='0'>Не выбран отдел</option>
    <?php for ($i = 0; $i < count($filialDepartments); $i++) { ?>
    <option value='<?= $filialDepartments[$i]->id ?>'><?= $filialDepartments[$i]->name ?></option>
    <?php } ?>
    </select><br><br>

    <div id='filialDepartmentsRoomPermissionTable'>
        <?php if (isset($permissions[0]->id)) ?>
        <?php if ($permissions[0]->id != 0) ?>
        <div class='button' onclick="sendAjax('/filial-departments/formaddroom/<?= $permissions[0]->id ?>/', 'GET')">Создать новое право доступа</div><br><br>

    <table class='akkt' border='1' cellpadding='5'>
        <tr>
            <th>№</th>
            <th>№ отдела</th>
            <th>Название отдела</th>
            <th>Идентификатор комнаты</th>
            <th>Название комнаты</th>
            <td>room_floor</td>
            <th>№ комнаты</th>
            <th>Статус</th>
            <th></th>
            </tr>

        <?php if (isset($permissions_def_rooms['status'])) { ?>
        <tr>
            <td class='error'><?= $permissions_def_rooms['message'] ?></td>
            </tr>
        <?php } else { for ($i = 0; $i < count($permissions_def_rooms); $i++) { ?>
        <tr>
            <td><?= $permissions_def_rooms[$i]->id ?></td>
            <td><?= $permissions_def_rooms[$i]->departament_id ?></td>
            <td><?= $permissions_def_rooms[$i]->departament_name ?></td>
            <td><?= $permissions_def_rooms[$i]->room_id ?></td>
            <td><?= $permissions_def_rooms[$i]->room_name ?></td>
            <td><?= $permissions_def_rooms[$i]->room_floor ?></td>
            <td><?= $permissions_def_rooms[$i]->room_number ?></td>
            <?php if ($permissions_def_rooms[$i]->status == 1) ?> <td>Разрешен</td>
            <?php if ($permissions_def_rooms[$i]->status == 0) ?><td>Запрещен</td>
            <td class='button' onclick="sendAjax('/filial-departments/formaddroommodify/<?= $permissions_def_rooms[$i]->id ?>/', 'GET')">Изменить</td>
            <td class='button' onclick="sendAjax('deleteMark=<?= $marks[$i]->id ?>', 'DELETE')">Удалить</td>
            </tr>
        <?php
        }
        }
        ?>
        </table>
        </div>
    </div>