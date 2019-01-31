    <div><a href="/workschedule/show/<?= $id ?>/">График работ</a></div>
<div onclick="sendAjax('/topology/service/add/<?= $id ?>/', 'GET');">Добавить услугу</div>
<div onclick="sendAjax('/topology/worker/delete/<?= $id ?>/', 'GET');">Отвязать сотрудника</div>