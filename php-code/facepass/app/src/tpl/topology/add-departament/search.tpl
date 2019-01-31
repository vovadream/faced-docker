<?php if(isset($departaments)) { ?>
    <?php for($i=0;$i<count($departaments);$i++) { ?>
        <div class="search-item" onclick="setFormValue('searchDepartamentResult_topology_<?php echo $id; ?>',
            ['add-departament-form-topology-<?php echo $id; ?>-departament-id', 'add-departament-form-topology-<?php echo $id; ?>-departament-name'],
            ['<?php echo $departaments[$i]->id;?>', '<?php echo $departaments[$i]->name;?>']
            );"><?php echo $departaments[$i]->name;?></div>
    <?php } ?>
<?php } ?>