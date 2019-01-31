<div id="settings_interface" class="overfltable">
    <div class="button" onclick="sendAjax('/interface/form/', 'GET')">Добавить новый интерфейс</div>
    <br><br>
    <table class="akkt" cellpadding="5" border="1">
        <tbody>
        <?php if (isset($interfaces['status'])) { ?>
        <tr>
            <td class='error'><?=$interfaces['message']?></td>
        </tr>
        <?php } else { for ($i = 0; $i < count($interfaces); $i++) { ?>
        <tr>
            <td><?= $interfaces[$i]->id ?></td>
            <td><?= $interfaces[$i]->name?></td>
            <td><?= $interfaces[$i]->url?></td>
            <td class='robotocr'>
                <img src='<?php echo GetImageURL($interfaces[$i]->active_icon, 'icon'); ?>' width='30'/>
            </td>
            <td class='robotocr'><img src='<?php echo GetImageURL($interfaces[$i]->passive_icon, 'icon'); ?>' width='30'/>
            </td>
            <td><?= $interfaces[$i]->num ?></td>
            <td class='button blueak otst' text-align='center'
                onclick="sendAjax('/interface/form/<?= $interfaces[$i]->id ?>/',
            'GET')">Изменить</td>
            <td class='button' onclick="sendAjax('deleteInterface=<?= $interfaces[$i]->id ?>', 'DELETE')">Удалить</td>
        </tr>
        <?php }
            }
        ?>
        </tbody>
    </table>
</div>