<div class="compare-dialog">
    <h4>Верна ли информация?</h4>
    <div class="button greenak otstupkn" onclick="sendAjax('/notifications/reply/<?php echo $id;?>/', 'POST', 'reply-true')">Подтверждаю</div>
    <form name="reply-true"><input type="hidden" name="reply" value="1"></form>
    <div class="button redak otstupkn" onclick="sendAjax('/notifications/reply/<?php echo $id;?>/', 'POST', 'reply-false')">Не подтверждено</div>
    <form name="reply-false"><input type="hidden" name="reply" value="0"></form>
</div>