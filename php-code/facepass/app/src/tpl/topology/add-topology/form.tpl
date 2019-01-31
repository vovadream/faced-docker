<form action="" name="add-topology-form" onsubmit="sendAjax('/topology/add/topology/', 'POST', 'add-topology-form'); return false;">
    <input type="text" name="name" id="add-topology-form-topology-name" placeholder="Введите название Категории/Этажа">
    <div class="cancelAction" onclick="clearDiv('topologyHiddenForm')">Отмена</div>
</form>