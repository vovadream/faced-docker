<form action="" name="add-topology-form-topology-<?php echo $id; ?>" onsubmit="sendAjax('/topology/group-rooms/add/<?= $id ?>/', 'POST', 'add-topology-form-topology-<?php echo $id; ?>'); return false;">
    <input type="text" name="name" id="add-subtopology-form-topology-<?php echo $id; ?>-subtopology-name" placeholder="Введите название Подкатегории этажа">
    <button class="cancelAction" onclick="clearDiv('topologyHiddenForm_<?php echo $id; ?>')">Отмена</button>
</form>