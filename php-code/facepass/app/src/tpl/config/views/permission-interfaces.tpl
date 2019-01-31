<div id='settings_permission_to_interface' class='overfltable'>
    <select name='permission_id' onchange="sendAjax('/interfaces/permission/table/'+this.value+'/', 'GET');">
    <option value='0'>Не выбраны права пользователя</option>
    <?php for ($i = 0; $i < count($permissions); $i++) { ?>
    <option value=<?= $permissions[$i]->id ?>><?= $permissions[$i]->name ?></option>
    <?php } ?>
    </select><br><br>

    <div id='interfacePermissionTable'>
        <?php if (isset($permissions[0]->id)) ?>
        <?php if ($permissions[0]->id != 0) ?>
        <div class='button' onclick="sendAjax('/interfaces/permission/form/add/',
        'GET')">Создать новое право доступа</div>
    <br><br>

    <table class='akkt' border='1' cellpadding='5'>
        <tr>
            <th>№</th>
            <th>№ доступа</th>
            <th>Название доступа</th>
            <th>№ интерфейса</th>
            <th>Название интерфейса</th>
            <th>Статус</th>
            <th></th>
        </tr>

        <?php if (isset($permissions_def_interfaces['status'])) { ?>
        <tr>
            <td class='error'><?= $permissions_def_interfaces['message'] ?></td>
        </tr>
        <?php } else { for ($i = 0; $i < count($permissions_def_interfaces); $i++) { ?>
        <tr>
            <td><?= $permissions_def_interfaces[$i]->id ?></td>
            <td><?= $permissions_def_interfaces[$i]->permission_id ?></td>
            <td><?= $permissions_def_interfaces[$i]->permission_name ?></td>
            <td><?= $permissions_def_interfaces[$i]->interface_id ?></td>
            <td><?= $permissions_def_interfaces[$i]->interface_name ?></td>
        <?php if ($permissions_def_interfaces[$i]->status == 1) { ?>
            <td>Разрешен</td>
        <?php } if ($permissions_def_interfaces[$i]->status == 0) { ?>
            <td>Запрещен</td>
            <?php } ?>
            <td class='button' onclick="sendAjax('/interfaces/permission/form/update/<?= $permissions_def_interfaces[$i]->id ?>/',
            'GET')">Изменить</td>

            <?php /** <td class='button' onclick="sendAjax('deleteMark=<?= $marks[$i]->id ?>','DELETE')">Удалить</td> **/ ?>
        </tr>
        <?php
        }
        }
        ?>
    </table>
</div>
</details>
</div