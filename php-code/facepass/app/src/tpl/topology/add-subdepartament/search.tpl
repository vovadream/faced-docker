<?php if(isset($departaments)) { ?>
<?php for($i=0;$i<count($departaments);$i++) { ?>
<div class="search-item" onclick="setFormValue('searchSubdepartamentResult_topology_<?php echo $floor_id; ?>_department_<?php echo $departament_id; ?>',
            ['add-subdepartament-form-topology-<?php echo $floor_id; ?>-<?php echo $departament_id; ?>-subdepartament-id', 'add-subdepartament-form-topology-<?php echo $floor_id; ?>-<?php echo $departament_id; ?>-subdepartament-name'],
            ['<?php echo $departaments[$i]->id;?>', '<?php echo $departaments[$i]->name;?>']
            );"><?php echo $departaments[$i]->name;?></div>
<?php } ?>
<?php } ?>