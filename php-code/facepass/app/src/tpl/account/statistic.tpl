<?php
/**
 * @var $user_id
 * @var $userPass
 */


$HTML = "";
$HTML .= "<div class='filexit'><div class='button filtr' onclick=\"sendAjax('/filter/statistic/{$user_id}/null/', 'GET');event.stopPropagation();\">Фильтр</div>
		<div class='button white' title='Сброс фильтра' onclick=\"sendAjax('/filter/filtered/statistic/{$user_id}/null/', 'POST', 'filterform');event.stopPropagation();\"><img class='bigIcon' src='" . base_path() . "images/icons/close.jpg'></div></div>";
if ($userPass != null) {

    $HTML .= "<table class='full' border='1' cellpadding='5'>";
    $HTML .= "<tr>";
    $HTML .= "<th rowspan='2'>№</th>";
    $HTML .= "<th colspan='2'>Вход</th>";
    $HTML .= "<th colspan='2'>Выход</th>";
    $HTML .= "<th rowspan='2'>Источник</th>";
    $HTML .= "<th rowspan='2'>№ дела, накладной</th>";
    $HTML .= "<th rowspan='2'>Статус</th>";
    $HTML .= "<th rowspan='2'>Информация</th>";
    $HTML .= "<th rowspan='2'>Метка</th>";
    $HTML .= "<th rowspan='2'>Документ, удостов. личность</th>";
    $HTML .= "</tr>";

    $HTML .= "<tr>";
    $HTML .= "<th>Дата</th>";
    $HTML .= "<th>Время</th>";
    $HTML .= "<th>Дата</th>";
    $HTML .= "<th>Время</th>";
    $HTML .= "</tr>";

    for ($i = 0; $i < count($userPass); $i++) {
        if ($userPass[$i]->id == null) continue;

        $userPass[$i]->time_in = explode(".", $userPass[$i]->time_in);
        $userPass[$i]->time_in = $userPass[$i]->time_in[0];

        $userPass[$i]->time_out = explode(".", $userPass[$i]->time_out);
        $userPass[$i]->time_out = $userPass[$i]->time_out[0];

        $HTML .= "<tr>";
        $HTML .= "<td class='robotocr'>{$userPass[$i]->id}</td>";
        $date = new DateTime($userPass[$i]->date_in);
        $HTML .= "<td class='robotocr'>{$date->Format('d.m.Y')}</td>";
        $HTML .= "<td class='robotocr'>{$userPass[$i]->time_in}</td>";
        $date = new DateTime($userPass[$i]->date_out);
        $HTML .= "<td class='robotocr'>{$date->Format('d.m.Y')}</td>";
        $HTML .= "<td class='robotocr'>{$userPass[$i]->time_out}</td>";
        $HTML .= "<td>{$userPass[$i]->room_name}</td>";
        $HTML .= "<td class='robotocr'>{$userPass[$i]->hearing_code}</td>";
        $HTML .= "<td class='robotocr'>{$userPass[$i]->info}</td>";
        $HTML .= "<td class='robotocr'>{$userPass[$i]->access_info}</td>";
        if ($userPass[$i]->mark_name != null)
            $HTML .= "<td class='ralewaymed red'>{$userPass[$i]->mark_name}</td>";
        else
            $HTML .= "<td class='ralewaymed'>Нет метки</td>";
        $HTML .= "<td>Паспорт РФ - <b class='robotocr'>0234 123456</b></td>";
        $HTML .= "</tr>";
    }
    $HTML .= "</table>";
} else {
    $HTML .= "Нет данных";
}
echo $HTML;
