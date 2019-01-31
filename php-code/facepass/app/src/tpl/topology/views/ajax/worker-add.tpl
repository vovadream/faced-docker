<form action="" name="add-worker-form-topology-<?php echo $floor_id; ?>-<?php echo $departament_id; ?>-<?php echo $room_id; ?>" onsubmit="sendAjax('/topology/worker/add/<?= $id ?>/', 'POST', 'add-worker-form-topology-<?php echo $floor_id; ?>-<?php echo $departament_id; ?>-<?php echo $room_id; ?>'); return false;">
    <select id="add-worker-topology" name="worker_id" style="width: 100%;">
        <?php for($i=0;$i<count($departaments);$i++) { ?>
        <option disabled value="<?php echo $departaments[$i]->id; ?>"><?php echo $departaments[$i]->name; ?></option>
        <?php for($j=0;$j<count($departaments[$i]->workers);$j++) { ?>
        <option value="<?php echo $departaments[$i]->workers[$j]->id; ?>"><?php echo $departaments[$i]->workers[$j]->surname; ?> <?php echo $departaments[$i]->workers[$j]->first_name; ?> <?php echo $departaments[$i]->workers[$j]->patronymic; ?></option>
        <?php } ?>
        <?php } ?>
    </select>
    <div class="cancelAction" onclick="clearDiv('topologyHiddenForm')">Отмена</div>
    <button>Добавить сотрудника</button>
</form>
