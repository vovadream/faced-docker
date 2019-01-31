<div id="settings_mark" style="display: block;" class="overfltable">
    <div class="button" onclick="sendAjax('/marks/form/', 'GET')">Добавить новую метку</div>
    <br><br>
    <table class="akkt" cellpadding="5" border="1">
        <tbody>
        <tr>
            <th>№</th>
            <th>Название</th>
            <th></th>
        </tr>
        <?php if (isset($marks['status'])) { ?>
        <tr>
            <td class='error'><?= $marks['message']?></td>
        </tr>

        <?php  } else { for ($i = 0; $i < count($marks); $i++) { ?>

        <tr>

            <td class='robotocr'><?= $marks[$i]->id?></td>

            <td><?= $marks[$i]->name?></td>

            <td class='button' onclick="sendAjax('/marks/form/<?= $marks[$i]->id ?>/', 'GET')">Изменить</td>

            <td class='button' onclick="sendAjax('deleteMark=<?= $marks[$i]->id ?>', 'DELETE')">Удалить</td>


        </tr>

        <?php }
        }
        ?>
        </tbody>
    </table>
</div>