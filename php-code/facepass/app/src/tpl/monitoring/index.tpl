<div class='buttonsControl nomargin'>
    <div class='tabButton active' onClick="showHideDivs(this,'MonitoringTab,SearchOnTerritoryTab,DynamicSearchTab', 'MonitoringTab', 'tabButton active');">Наблюдение</div>
    <!--
    <div class='tabButton' onClick="showHideDivs(this,'MonitoringTab,SearchOnTerritoryTab,DynamicSearchTab', 'SearchOnTerritoryTab', 'tabButton active');\">Найти на территории</div>
    <div class='tabButton' onClick="showHideDivs(this,'MonitoringTab,SearchOnTerritoryTab,DynamicSearchTab', 'DynamicSearchTab', 'tabButton active');\">Динамический поиск</div>
    -->
</div>

<div class='content' id='MonitoringTab'>

    <div>
    <?php if(count($terminals)==0) { ?>
        Нет терминалов
    <?php } else { ?>
        <?php
        for ($i = 0; $i < count($terminals); $i++) {
            $class = ($i == $active) ? "active" : "white";
        ?>
            <a href="<?php echo base_path();?>monitoring/<?= $i ?>" class="button <?= $class ?>">
                terminal_id_<?=$terminals[$i]->equipment_id?>
            </a>
        <?php } ?>
    </div>


    <div class='monitors'>
    <h2>Мониторы</h2></br>
    <div class='terminal-screen' id='terminal_monitor_<?=$terminals[$active]->id?>'>
        <?php if ($terminals[$active]->webrtc_room == null) { ?>
            Стрим монитора неактивен
            <div class='button full' onclick="sendAjax('/stream/terminal/create/<?=$terminals[$active]->equipment_id?>/', 'GET')">Активировать стрим</div>
        <?php } else { ?>
            <iframe class='terminal-screen-frame' id='terminalFrame<?= $terminals[$active]->equipment_id ?>'
                    onload="setCursorArea('#terminalFrame<?= $terminals[$active]->equipment_id ?>', '#terminalCursorFrame<?= $terminals[$active]->equipment_id ?>');" src='<?= webrtc_url(); ?>/r/<?= $terminals[$active]->webrtc_room ?>' allow="geolocation; microphone; camera">123</iframe>
            <div id='terminalCursorFrame<?= $terminals[$active]->equipment_id ?>'
                 onclick="sendCursorCoordinates('#terminalFrame<?= $terminals[$active]->equipment_id ?>', event, <?= $terminals[$active]->equipment_id ?>);" style='z-index: 100; width: 100%; height: 100%; position: absolute; top: 0; right: 0;'></div>
        <button id='terminal-maximize' class='terminal-size maximize' onclick='terminalSize("1", "terminal_monitor_<?= $terminals[$active]->id ?>"); setCursorArea("#terminalFrame<?= $terminals[$active]->equipment_id ?>", "#terminalCursorFrame<?= $terminals[$active]->equipment_id ?>");'>Развернуть стрим</button>
        <button id='terminal-minimize' class='terminal-size minimize' onclick='terminalSize("0", "terminal_monitor_<?= $terminals[$active]->id ?>); setCursorArea("#terminalFrame<?= $terminals[$active]->equipment_id ?>", "#terminalCursorFrame<?= $terminals[$active]->equipment_id ?>");' style='display: none;'>Свернуть стрим</button>

            <div class='button full' onclick="sendAjax('/stream/terminal/delete/<?= $terminals[$active]->equipment_id ?>/', 'GET')">Деактивировать стрим</div>
        <?php } ?>
        <!--<div class='blockweight'>
            <img src='<?= base_path() ?>images/icons/doc1.PNG' class='bigIcon'>
            <img src='<?= base_path() ?>images/icons/doc2.PNG' class='bigIcon'>
            <img src='<?= base_path() ?>images/icons/doc3.PNG' class='bigIcon'>
        </div>-->
    </div>
    </div>


    <div class='cameras'>
        <h2>Камера терминала</h2></br>
        <div id='terminal_camera_<?= $terminals[$active]->camera_id?>'>
            <img src="http://<?= $_SERVER['SERVER_ADDR'] ?>:8888/s<?= $terminals[$active]->camera_id ?>.mjpeg" style="max-width: 100%;" alt="">
        </div>
    </div>
    <?php } ?>

</div>

<div class='content' id='SearchOnTerritoryTab' style='display: none'>
some info
</div>

<div class='content' id='DynamicSearchTab' style='display: none'>
some info
</div>