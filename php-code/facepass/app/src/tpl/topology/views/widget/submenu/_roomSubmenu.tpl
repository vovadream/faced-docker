    <div onclick="sendAjax('/topology/worker/add/<?= $id ?>/', 'GET'); event.stopPropagation();">Добавить сотрудника</div>
<div onclick="sendAjax('/topology/room/delete/<?= $id ?>/', 'GET');">Удалить кабинет</div>
<div onclick="sendAjax('/topology/room/update/<?= $id ?>/', 'GET');">Редактировать</div>
<div><a href="/workschedule/show/<?= $id ?>/">График работ</a></div>