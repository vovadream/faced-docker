<form action="" name="add-subdepartament-form-topology-<?php echo $floor_id; ?>-<?php echo $departament_id; ?>" onsubmit="sendAjax('/topology/<?php echo $floor_id; ?>/<?php echo $departament_id; ?>/add/subdepartament/', 'POST', 'add-subdepartament-form-topology-<?php echo $floor_id; ?>-<?php echo $departament_id; ?>'); return false;">
    <input type="hidden" name="id" id="add-subdepartament-form-topology-<?php echo $floor_id; ?>-<?php echo $departament_id; ?>-subdepartament-id">
    <input type="text" name="name" id="add-subdepartament-form-topology-<?php echo $floor_id; ?>-<?php echo $departament_id; ?>-subdepartament-name" onkeyup="
    sendAjax('/topology/<?php echo $floor_id; ?>/<?php echo $departament_id; ?>/search/subdepartament/'+this.value+'/', 'GET')
" placeholder="Введите название Отдела">
    <div class="cancelAction" onclick="clearDiv('topologyHiddenForm_departament_<?php echo $floor_id; ?>_<?php echo $departament_id; ?>')">Отмена</div>
    <div id="searchSubdepartamentResult_topology_<?php echo $floor_id; ?>_department_<?php echo $departament_id; ?>"></div>
</form>