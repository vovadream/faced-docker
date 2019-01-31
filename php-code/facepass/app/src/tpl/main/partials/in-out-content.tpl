<?php

/**
 * @var $passInOut
 * @var $marksModel
 * @var $type
 */
$HTML = '';
for($i=0;$i<count($passInOut);$i++) {
    $passInOut[$i]->time_in = explode(".", $passInOut[$i]->time_in);
    $passInOut[$i]->time_in = $passInOut[$i]->time_in[0];
    $class = ($i==0) ? "class='tableInTrActive'" : "";

    $HTML .= "<tr {$class} onclick=\"sendAjax('/main/show-big-user/{$passInOut[$i]->user_id}/{$type}/', 'GET'); divMakeActive(this, 'tableInTr', 'tableInTrActive'); \">";
    $date = new DateTime($passInOut[$i]->date_in);
    $HTML .= "<td class='robotocr'>{$date->Format('d.m.Y')}</td>";
    $HTML .= "<td class='robotocr'>{$passInOut[$i]->time_in}</td>";


    $HTML .= "<td class='robotocr'><img src='" . GetImageURL($passInOut[$i]->user_photo, 'user_photo') . "' width='30'/></td>";
    //if ($passInOut[$i]->user_photo!=null)
    //$HTML .= "<td class='robotocr'><img src='{$passInOut[$i]->user_photo}'/></td>";
    //else
    //$HTML .= "<td>{$passInOut[$i]->user_photo}<img src='".base_path()."images/icons/chelovek2.PNG' class='bigIcon' \"></td>";
    $HTML .= "<td class='ralewayreg'><a href='".base_path()."account/{$passInOut[$i]->user_id}/'>{$passInOut[$i]->surname} {$passInOut[$i]->first_name} {$passInOut[$i]->patronymic}</a></td>";
    $HTML .= "<td class='ralewayreg'>{$passInOut[$i]->user_type_name}</td>";
    $HTML .= "<td class='ralewayreg'>{$passInOut[$i]->user_departament_name}</td>";
    $HTML .= "<td class='ralewayreg'>{$passInOut[$i]->user_room_name}</td>";

    $HTML .= "<td class='ralewayreg'>";
    if ($passInOut[$i]->mark_name!=null) $HTML .= "{$passInOut[$i]->mark_name}";
    else
    {
        $HTML .= "<select name='mark' onchange=\"sendAjax('/usermark/main/{$passInOut[$i]->id}/'+this.value+'/', 'GET');\" onclick=\"event.stopPropagation();\">";
        $HTML .= "<option value='0'>Не выбрана метка</option>";
        for($j=0;$j<count($marksModel); $j++)
        {
            $HTML .= "<option value='{$marksModel[$j]->id}'>{$marksModel[$j]->name}</option>";
        }
        $HTML .= "</select>";
    }
    $HTML .= "</td>";
    $HTML .= "</tr>";
}

echo $HTML;