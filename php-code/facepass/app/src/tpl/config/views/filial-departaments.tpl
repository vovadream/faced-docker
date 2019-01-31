<div id='settings_filial_section' class='overfltable'>

    <div class='button' onclick="sendAjax('/filial-departments/createform/section/', 'GET')">Добавить новый отдел</div><br><br>

<table class='akkt' border='1' cellpadding='5'>
    <tr>
        <th>№</th>
        <th>Название</th>
        <th>№ филиала</th>
        <th></th>
        </tr>

    <?php if (isset($filialDepartments['status'])) { ?>
    <tr>
        <td class='error'><?= $filialDepartments['message'] ?></td>
        </tr>
    <?php } else { for ($i = 0; $i < count($filialDepartments); $i++) { ?>
    <tr>
        <td><?= $filialDepartments[$i]->id ?></td>
        <td><?= $filialDepartments[$i]->name ?></td>
        <td><?= $filialDepartments[$i]->filial_id ?></td>
        <td class='button' onclick="sendAjax('/filial-departments/form/<?= $filialDepartments[$i]->id ?>/section/', 'GET')">Изменить</td>
        <td class='button' onclick="sendAjax('deleteMark=<?= $marks[$i]->id ?>', 'DELETE')">Удалить</td>
        </tr>
    <?php
    }
    }
    ?>
    </table>
</div>