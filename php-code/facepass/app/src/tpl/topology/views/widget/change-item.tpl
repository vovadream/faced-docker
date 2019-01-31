<?php
for($i = 0; $i < count($menu); $i++) {
    if($menu[$i]["type"] == 'room') {

        $onclick = "onclick=\"sendAjax('/topology/make-change-room/{$menu[$i]["id"]}/{$workerChangeId}/', 'GET');event.stopPropagation();\"";
        $menu[$i]["item"]['name'] = "Выбрать кабинет: ".$menu[$i]["item"]['name'];
    } else {
        $onclick = '';

    }
    $button = (!empty($menu[$i]['children']) ? "<button onclick=\"divSlide(this, '#main_department_{$menu[$i]['item']['id']}', '.topology_item', false); event.stopPropagation();\">+</button>" : "");
?>
<div class="topology_item" <?= ($menu[$i]['parent'] !== 0 ? "style=\"display:none;\"" : '') ?> id="main_department_<?= $menu[$i]['item']['id'] ?>">
<div class="topology_item_name" <?= $onclick ?> >
        <?= $button ?>
        <?= $menu[$i]["item"]['name'] ?>
    </div>
    <?php
    if(!empty($menu[$i]['children'])) {
        echo widget('TopologyChangeRoomWidget', ['parent' => $menu[$i]['id'], 'workerChangeId' => $workerChangeId]);
    }
    ?>
    </div>
<?php } ?>
