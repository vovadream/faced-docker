<div id='topology' class='userData'>
    <div class='buttonsControl'>
        <input type='button' class='button bornone' value='Добавить группу' onclick=\"sendAjax('/topology/add/form/',
        'GET')\">
        <form name='topologySearchForm' id='topologySearchForm' onsubmit=\"sendAjax('/topology/0/search/',
        'POST', 'topologySearchForm'); return false;\">
            <input class='poisktopology' name='name' type='text' placeholder='департамент/отдел/кабинет/график'>
            <button class='button'>Поиск</button>
        </form>
    </div>

    <div id='topologynavigation' class='userData'>
        <div id='leftopology'>
            <div id='topologyHiddenForm' class='hiddenFormDiv'></div>
            <?php echo widget('TopologyMenuWidget'); ?>
        </div>
    </div>

    <div id='selectedtopologygroup' class='userData'></div>
</div>
