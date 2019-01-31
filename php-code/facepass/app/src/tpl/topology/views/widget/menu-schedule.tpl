<?php
for($i = 0; $i < count($menu); $i++) {
    $button = (!empty($menu[$i]['children']) ? "<button onclick=\"divSlide(this, &quot;#main_department_{$menu[$i]['item']['id']}&quot;, &quot;.topology_item&quot;, false); event.stopPropagation();\">+</button>" : "");
?>
<div class="topology_item" <?= ($menu[$i]['parent'] !== 0 ? "style=\"display:none;\"" : '') ?> id="main_department_<?= $menu[$i]['item']['id'] ?>" onclick="activeTopologyItem('#main_department_<?= $menu[$i]['item']['id'] ?>', '.topology_item_name', 'active', 0); sendAjax('/workschedule/showbutton/<?=$menu[$i]['id']?>/', 'GET'); event.stopPropagation();">
<div class="topology_item_name">
        <?= $button ?>
        <?= $menu[$i]["item"]['name'] ?>
        <div class="topology_submenu">
            <div class="topology_menu_icon"></div>
            <div class="topology_menu">
                <?= $menu[$i]['htmlCode'] ?>
            </div>
        </div>
    </div>
    <?php
    if(!empty($menu[$i]['children'])) {
        echo widget('TopologyMenuWidget', ['parent' => $menu[$i]['id'], 'view' => $view]);
    }
    ?>
    </div>

<?php } ?>
