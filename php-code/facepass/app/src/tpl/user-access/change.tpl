<h2>Изменение данных доступа
    пользователя <?= $useraccess[0]->first_name ?> <?= $useraccess[0]->patronymic ?> <?=$useraccess[0]->surname ?>
    <br> к слушанию - <?= $useraccess[0]->hearingname ?> </h2>
<form name='updateUserAccessForm'>
</form>
<div class='button'
     onclick="sendAjax('/useraccess/<?= $useraccess[0]->id ?>/', 'POST', 'updateUserAccessForm')">Изменить данные</div>
