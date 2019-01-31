<form action="" name="add-departament-form-topology-<?php echo $id; ?>" onsubmit="sendAjax('/topology/departament/add/<?= $id ?>/', 'POST', 'add-departament-form-topology-<?php echo $id; ?>'); return false;">
    <input type="text" name="name" id="add-departament-form-topology-<?php echo $id; ?>-departament-name" placeholder="Введите название отдела">
    <div id="searchDepartamentResult_topology_<?php echo $id; ?>"></div>
</form>