<div id='settings_document_type' class='overfltable'>

    <div class='button' onclick="sendAjax('/documents-type/form/', 'GET')">Добавить новый тип документа</div><br><br>

<table class='akkt' border='1' cellpadding='5'>

    <tr>
        <th>№</th>
        <th>Название</th>
        <th></th>
        </tr>

    <?php if (isset($document_type['status'])) { ?>
    <tr>
        <td class='error'><?= $document_type['message'] ?></td>
        </tr>
    <?php } else { for ($i = 0; $i < count($document_type); $i++) { ?>
    <tr>
        <td><?= $document_type[$i]->id ?></td>
        <td><?= $document_type[$i]->name ?></td>
        <td class='button' onclick="sendAjax('/documents-type/form/<?= $document_type[$i]->id ?>/', 'GET')">Изменить</td>
        <td class='button' onclick="sendAjax('deleteMark=<?= $marks[$i]->id ?>', 'DELETE')">Удалить</td>
        </tr>
    <?php
    }
    }
    ?>
    </table>
</div>