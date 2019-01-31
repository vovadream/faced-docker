<div onclick="sendAjax('/topology/group-rooms/update/<?= $id ?>/', 'GET'); event.stopPropagation();">Редактировать</div>
<div onclick="sendAjax('/topology/group-rooms/add/<?= $id ?>/', 'GET');event.stopPropagation();">Добавить подгруппу комнат</div>
<div onclick="sendAjax('/topology/departament/add/<?= $id ?>/', 'GET');event.stopPropagation();">Добавить отдел</div>
<div><a href="/workschedule/show/<?= $id ?>/">График работ</a></div>
<div onclick="sendAjax('/topology/group-rooms/delete/<?= $id ?>/', 'POST'); event.stopPropagation();">Удалить</div>