<div id="content page-notifications">
    <div class="overfltable">
        <div class="fixheight">
            <table class="akkt" cellpadding="5" border="1">
                <tbody>
                    <tr>
                        <th>№</th>
                        <th>Дата\Время</th>
                        <th>Источник</th>
                        <th>Тип</th>
                        <th>Ответ</th>
                    </tr>

                    <?php foreach($notifications as $notification): ?>
                    <tr class="notify<?php echo $notification['read']? '': ' new'; ?>"
                            onclick="sendAjax('/notifications/get/<?php echo $notification['id']; ?>/', 'GET')">
                        <td class="robotocr"><?php echo $notification['id']; ?></td>
                        <td class="robotocr"><?php echo $notification['adate']; ?> <?php echo $notification['atime']; ?></td>
                        <td class="robotocr"><?php echo $notification['eq']; ?></td>
                        <td class="robotocr"><?php echo $notification['name_type']; ?></td>
                        <td class="robotocr"><?php echo $notification['reply']? 'Да': 'Нет'; ?></td>
                    </tr>
                    <?php endforeach; ?>

                </tbody>
            </table>
    </div>
</div>
