<div id="menu_work_schedule_department_<?= $id ?>">
    <a href="/workschedule/show/<?= $id ?>/">График работ</a></div>
<div onclick="sendAjax('/topology/departament/add/<?= $id ?>/', 'GET');event.stopPropagation();">Добавить отдел</div>
<div onclick="sendAjax('/topology/room/add/<?= $id ?>/', 'GET');event.stopPropagation();">Добавить кабинет</div>
<div id="menu_edit_department_<?= $id ?>" onclick="sendAjax('/topology/departament/update/<?= $id ?>/', 'GET'); event.stopPropagation();">Редактировать</div>
<div id="menu-delete-section-<?= $parentId ?>-<?= $id ?>" onclick="sendAjax('/topology/departament/delete/<?= $id ?>/', 'POST');">Удалить</div>