<form action="" name="add-room-form-topology-<?php echo $departament_id; ?>" onsubmit="sendAjax('/topology/room/add/<?= $id ?>/', 'POST', 'add-room-form-topology-<?php echo $departament_id; ?>'); return false;">
    <input type="text" name="name"
           id="add-room-form-topology-<?php echo $departament_id; ?>-room-name"
           placeholder="Введите название Кабинета">
</form>