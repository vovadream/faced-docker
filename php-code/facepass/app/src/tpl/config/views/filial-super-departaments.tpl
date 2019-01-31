<div id='settings_filial_department' class='overfltable'>

    <div class='button' onclick="sendAjax('/filial-departments/createform/department/', 'GET')">Добавить новый департамент</div><br><br>

<table class='akkt' border='1' cellpadding='5'>
    <tr>
        <th>№</th>
        <th>Название</th>
        <th>Отдел филиала</th>
        <th></th>
        </tr>

     <?php if (isset($filialSuperDepartments['status'])) { ?>
    <tr>
        <td class='error'><?= $filialSuperDepartments['message'] ?></td>
        </tr>
    <?php } else { for ($i = 0; $i < count($filialSuperDepartments); $i++) { ?>
    <tr>
        <td><?= $filialSuperDepartments[$i]->id ?></td>
        <td><?= $filialSuperDepartments[$i]->name ?></td>
        <td><?= $filialSuperDepartments[$i]->filial_id ?></td>
        <td class='button' onclick="sendAjax('/filial-departments/form/<?= $filialSuperDepartments[$i]->id ?>/department/', 'GET')">Изменить</td>
        <td class='button' onclick="sendAjax('deleteMark=<?= $marks[$i]->id ?>', 'DELETE')">Удалить</td>
        </tr>
    <?php
    }
    }
    ?>
    </table>
</div>