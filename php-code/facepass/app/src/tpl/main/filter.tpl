<?php

/**
 * @var $type
 * @var $cr \App\Controllers\Ui\MainController
 */

$htmlUid = ($type == 'in') ? 'In' : 'Out';
$stickyTableHeader = "function() { $('#table{$htmlUid}').stickyTableHeaders({ scrollableArea: $('.overfl')[0], 'fixedOffset': 2 }); alert(123)}";

//$workers=$this->model->sendQuery("SELECT id,name FROM user_types WHERE id='1' OR main_class='1'");
$visitors = $cr->sendQuery("SELECT id,name FROM user_types WHERE NOT (id='1' OR main_class='1')");
$marks = $cr->marks->getMarksModel();
$rooms = $cr->roomModel->getRoomModel(null, 'room');
$deparmnents = $cr->sendQuery("SELECT * FROM filial_departament WHERE ((parent_id=0 OR parent_id IS NULL) OR \"group\" IS FALSE)");
for ($i = 0; $i < count($deparmnents); $i++) {
    $deparmnents[$i]->sections = $cr->sendQuery("SELECT * FROM filial_departament WHERE parent_id='{$deparmnents[$i]->id}'");
}
$HTML = "<h2>Фильтр</h2>";
$HTML .= "<form name='filterform'>";

$HTML .= "<table><tr><td>Дата с </td><td><input class='form-control' name='dateafter' type='date'></td><td class='text-center'> по </td><td><input class='form-control' name='datebefore' type='date'></td></tr>";
$HTML .= "<tr><td>Время с </td><td><input class='form-control' name='timeafter' type='time'></td><td class='text-center'> по </td><td><input class='form-control' name='timebefore' type='time'></td></tr>";
$HTML .= "<tr><td>Фамилия</td><td><input class='form-control' name='surname' type='text'></td><td></td><td></td></tr>";
$HTML .= "<tr><td>Имя</td><td><input class='form-control' name='name' type='text'></td><td></td><td></td></tr>";
$HTML .= "<tr><td>Отчество</td><td><input class='form-control' name='patronymic' type='text'></td><td></td><td></td></tr>";
$HTML .= "<tr><td>Пол</td><td><input name='male' type='checkbox'> Мужской <input  name='female' type='checkbox'> Женский</td><td></td><td></td></tr>";
$HTML .= "<tr><td>Сотрудник <input name='worker_checkbox' type='checkbox'></td>";
$HTML .= "<td><select class='form-control' name='worker_type_id'>";//<option value='0'>Не выбрано значение</option>";
/*for ($i = 0; $i < count($workers); $i++) {
$HTML .= "<option value='{$workers[$i]->id}'>{$workers[$i]->name}</option>";
}*/
for ($i = 0; $i < count($deparmnents); $i++) {
    $HTML .= "<option value='{$deparmnents[$i]->id}'>";
    if (!$deparmnents[$i]->group) $HTML .= "Департамент";
    else $HTML .= "Отдел";
    $HTML .= ": {$deparmnents[$i]->name}";
    if ($deparmnents[$i]->delete) $HTML .= " (удален)";
    $HTML .= "</option>";
    for ($j = 0; $j < count($deparmnents[$i]->sections); $j++) {
        $HTML .= "<option value='{$deparmnents[$i]->sections[$j]->id}'>";
        if (!$deparmnents[$i]->sections[$j]->group) $HTML .= "Департамент";
        else $HTML .= "Отдел";
        $HTML .= ": {$deparmnents[$i]->sections[$j]->name}";
        if ($deparmnents[$i]->sections[$j]->delete) $HTML .= " (удален)";
        $HTML .= "</option>";
    }
}
$HTML .= "</select></td><td></td><td></td></tr>";
$HTML .= "<tr><td>Посетитель <input name='visitor_checkbox' type='checkbox'></td>";
$HTML .= "<td><select class='form-control' name='visitor_type_id'>";
for ($i = 0; $i < count($visitors); $i++) {
    $HTML .= "<option value='{$visitors[$i]->id}'>{$visitors[$i]->name}</option>";
}
$HTML .= "</select></td><td></td><td></td></tr>";
//$HTML .= "<br>Тех персонал <input name='technical_staff_checkbox' type='checkbox'>";
//$HTML .= "<br>Подрядчик <input name='contractor_checkbox' type='checkbox'>";
//$HTML .= "<br>Сотрудник органов <input name='officcer_checkbox' type='checkbox'><br>";
$HTML .= "<tr><td>Направление</td><td><select class='form-control' name='target_room'><option value='0'>Направление не выбрано</option>";
for ($i = 0; $i < count($rooms); $i++) {
    $HTML .= "<option value='{$rooms[$i]->id}'>{$rooms[$i]->name}</option>";
}
$HTML .= "</select></td><td></td><td></td></tr>";
//$HTML .= "№ дела, накладной <input name='case_number' type='text'></br>";
$HTML .= "<tr><td>Метка</td><td><select class='form-control' name='mark'><option value='0'>Метка не выбрана</option>";
for ($i = 1; $i < count($marks); $i++) {
    $HTML .= "<option value='{$marks[$i]->id}'>{$marks[$i]->name}</option>";
}
$HTML .= "</select></td><td></td><td></td></tr>";
$HTML .= "<tr><td><div class='button' onclick=\"closePopup();\">Отмена</div></td><td>
                <div class='button' onclick=\"sendAjax('/filter/filtered/{$type}/', 'POST', 'filterform', $stickyTableHeader);\">ОК</div></td><td></td><td></td></tr></table>";

$HTML .= "</form>";
$HTML .= "";
$HTML .= "";
echo $HTML;


?>