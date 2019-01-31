<?php
/**
 * @var $type
 * @var $marksModel
 * @var $passUsers
 * @var $cr \App\Controllers\Ui\MainController
 */

$HTML = "";

if ($type == 'in') {
    $htmlUid = "In";
} else {
    $htmlUid = "Out";
}
$title = ($type == 'in') ? "вход" : "выход";


$HTML .= "<div class='buttonsControl'>";
//Таблица входов / выходов
$HTML .= "<h2 class='inline'>" . $title . "</h2>";
$HTML .= "</div>";

$HTML .= "<div class='overfl'>";
$HTML .= "<table id='table{$htmlUid}' border='1' cellpadding='3'>";
if ($passUsers[0]->id == null) {
    $HTML .= "Нет данных";
} else {
    $HTML .= "<thead>";
    $HTML .= "<tr>";
    $HTML .= "<th>Дата</th>";
    $HTML .= "<th>Время</th>";
    $HTML .= "<th>Фото</th>";
    $HTML .= "<th>ФИО</th>";
    $HTML .= "<th>Статус</th>";
    $HTML .= "<th>Департ., отдел</th>";
    $HTML .= "<th>Напр-е</th>";
    $HTML .= "<th>Метка</th>";
    $HTML .= "</tr>";
    $HTML .= "</thead>";

    $HTML .= "<tbody>";
    for ($i = 0; $i < count($passUsers); $i++) {
        $passUsers[$i]->time_in = explode(".", $passUsers[$i]->time_in);
        $passUsers[$i]->time_in = $passUsers[$i]->time_in[0];
        $class = ($i == 0) ? "class='table{$htmlUid}TrActive'" : "";
        $HTML .= "<tr {$class}>";
        $date = new DateTime($passUsers[$i]->date_in);
        $HTML .= "<td class='robotocr'>{$date->Format('d.m.Y')}</td>";
        $HTML .= "<td class='robotocr'>{$passUsers[$i]->time_in}</td>";


        $HTML .= "<td class='robotocr'><img src='" . GetImageURL($passUsers[$i]->user_photo, 'user_photo') . "' width='30'/></td>";
        //if ($passUsers[$i]->user_photo!=null)
        //$HTML .= "<td class='robotocr'><img src='{$passUsers[$i]->user_photo}'/></td>";
        //else
        //$HTML .= "<td>{$passUsers[$i]->user_photo}<img src='".base_path()."images/icons/chelovek2.PNG' class='bigIcon' \"></td>";
        $HTML .= "<td class='ralewayreg'><a href='" . base_path() . "account/{$passUsers[$i]->user_id}/'>{$passUsers[$i]->surname} {$passUsers[$i]->first_name} {$passUsers[$i]->patronymic}</a></td>";
        $HTML .= "<td class='ralewayreg'>{$passUsers[$i]->user_type_name}</td>";
        $HTML .= "<td class='ralewayreg'>{$passUsers[$i]->user_departament_name}</td>";
        $HTML .= "<td class='ralewayreg'>{$passUsers[$i]->user_room_name}</td>";

        $HTML .= "<td class=''>";
        if ($passUsers[$i]->mark_name != null) $HTML .= "{$passUsers[$i]->mark_name}";
        else {
            $HTML .= "<select name='mark' onchange=\"sendAjax('/usermark/main/{$passUsers[$i]->id}/'+this.value+'/', 'GET');\" onclick=\"event.stopPropagation();\">";
            $HTML .= "<option value='0'>Не выбрана метка</option>";
            for ($j = 0; $j < count($marksModel); $j++) {
                $HTML .= "<option value='{$marksModel[$j]->id}'>{$marksModel[$j]->name}</option>";
            }
            $HTML .= "</select>";
        }
        $HTML .= "</td>";
        $HTML .= "</tr>";
    }
    $HTML .= "<tbody>";
}
$HTML .= "</table>";
$HTML .= "</div>";

$HTML .= "</div>";
echo $HTML;