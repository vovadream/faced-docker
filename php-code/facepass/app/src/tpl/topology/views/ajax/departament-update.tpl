<h2>Изменение департамента <?= $filialDepartment[0]->name ?></h2>
<form name='updateFilialDepartmentForm'>
    <table><tr><td>Название</td><td><input type='text' name='name'  value='<?= $filialDepartment[0]->name ?>'></td></tr>
        <?php if ($filialDepartment[0]->parent_id != 0) { ?>
        <tr><td>Департамент</td>
            <td><select name='parent_id'>
                    <?php if (isset($superDepartments['status'])) { ?>
                    <option value='0'>Нет данных</option>
                    <?php } else { ?>
                    <option value='0'>Не выбран департамент</option>
                    <?php for ($i = 0; $i < count($superDepartments); $i++) { ?>
                    <option value='<?= $superDepartments[$i]->id ?>'
                    <?php if($superDepartments[$i]->id == $filialDepartment[0]->parent_id) { ?>
                    selected>
                    <?php } else { ?>
                    >
                    <?php } ?>
                    <?= $superDepartments[$i]->name ?></option>
                    <?php }
                    } ?>
                    </select>
            </td>
        </tr>
        <?php } ?>
        </tr>
        <td><input type='checkbox' name='public'
            <?php if($filialDepartment[0]->public) ?>  checked>Публичный</td>
        <td></td>
        </tr>
    </table>
    </form>
<div class='button' onclick="sendAjax('/topology/departament/update/<?= $id ?>/', 'POST', 'updateFilialDepartmentForm')">Изменить департамент</div>