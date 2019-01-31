<div><a href="/workschedule/show/<?= $id ?>/">График работ</a></div>
<div onclick="sendAjax('/topology/service/update/<?= $id ?>/', 'GET'); event.stopPropagation();">Редактировать шаблон</div>
<div onclick="sendAjax('/topology/service/delete/<?= $id ?>/', 'GET');">Удалить услугу</div>
