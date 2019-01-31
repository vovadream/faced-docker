<?php

/**
 * @var $controller \App\Controllers\Ui\MainController
 * @var $marksModel
 * @var $passIn
 * @var $passOut
 */

?>

<div id='content'>
    <div id='tableInContent'>
        <!-- TODO BEGIN IN -->
        <div class='buttonsControl'>
        <?php

        $HTML = "";

        $HTML .= "";
            //Таблица входов
            $HTML .= "<h2 class='inline'>Вход</h2>";
            $HTML .= "<div class='button white button__zoom-in' title='Увеличить'><img class='bigIcon' src='".base_path()."images/icons/zoom-in.jpg'></div>";
            $HTML .= "<div class='button white button__zoom-out' title='Уменьшить'><img class='bigIcon' src='".base_path()."images/icons/zoom-out.jpg'></div>";
            $HTML .= "<div class='button white button__full-screen' title='Развернуть на весь экран' onclick=\"toggleClass('#tableInContent', 'active');\"><img class='bigIcon' src='".base_path()."images/icons/full-view.jpg'></div>";
        $HTML .= "<div class='filexit'>";
            $HTML .= "<div class='button filtr' onclick=\"sendAjax('/filter/main/in/', 'GET');event.stopPropagation();\">Фильтр</div>";
        $HTML .= "</div>";
$HTML .= "</div>";

$HTML .= "<div class='button white' id='tableInRollUpButton' title='Свернуть в стандартный режим' onclick=\"toggleClass('#tableInContent', 'active');\"><img class='bigIcon' src='".base_path()."images/icons/standart-view.jpg'></div>";

$HTML .= "<div class='overfl'>";
    $HTML .= "<table id='tableIn' border='1' cellpadding='3'>";
        if($passIn!=null&&$passIn[0]->id==null) {
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

        $HTML .= "<tbody id='tableInBody'>";
        for($i=0;$i<count($passIn);$i++) {
        $passIn[$i]->time_in = explode(".", $passIn[$i]->time_in);
        $passIn[$i]->time_in = $passIn[$i]->time_in[0];
        $class = ($i==0) ? "class='tableInTrActive'" : "";
        $HTML .= "<tr {$class} onclick=\"sendAjax('/main/show-big-user/{$passIn[$i]->user_id}/in/', 'GET'); divMakeActive(this, 'tableInTr', 'tableInTrActive'); \">";
            $date = new DateTime($passIn[$i]->date_in);
            $HTML .= "<td class='robotocr'>{$date->Format('d.m.Y')}</td>";
            $HTML .= "<td class='robotocr'>{$passIn[$i]->time_in}</td>";


            $HTML .= "<td class='robotocr'><img src='" . GetImageURL($passIn[$i]->user_photo, 'user_photo') . "' width='30'/></td>";
            //if ($passIn[$i]->user_photo!=null)
            //$HTML .= "<td class='robotocr'><img src='{$passIn[$i]->user_photo}'/></td>";
            //else
            //$HTML .= "<td>{$passIn[$i]->user_photo}<img src='".base_path()."images/icons/chelovek2.PNG' class='bigIcon' \"></td>";
            $HTML .= "<td class='ralewayreg'><a href='".base_path()."account/{$passIn[$i]->user_id}/'>{$passIn[$i]->surname} {$passIn[$i]->first_name} {$passIn[$i]->patronymic}</a></td>";
            $HTML .= "<td class='ralewayreg'>{$passIn[$i]->user_type_name}</td>";
            $HTML .= "<td class='ralewayreg'>{$passIn[$i]->user_departament_name}</td>";
            $HTML .= "<td class='ralewayreg'>{$passIn[$i]->user_room_name}</td>";

            $HTML .= "<td class='ralewayreg'>";
                if ($passIn[$i]->mark_name!=null) $HTML .= "{$passIn[$i]->mark_name}";
                else
                {
                $HTML .= "<select name='mark' onchange=\"sendAjax('/usermark/main/{$passIn[$i]->id}/'+this.value+'/', 'GET');\" onclick=\"event.stopPropagation();\">";
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
        $HTML .= "</tbody>";
        }
        $HTML .= "</table>";
    $HTML .= "</div>";
$HTML .= "<div id='tableInUserInfo'>";
    if(count($passIn)>0) {
    $HTML .= $controller->showBigUserPassView($passIn[0]->user_id, 'in');
    }
    $HTML .= "</div>";
$HTML .= "</div>";
echo $HTML;

?>
    </div>
    <div id='tableOutContent'>

        <?php

        $HTML ="";
        $HTML .= "<div class='buttonsControl'>";
        $HTML .= "<h2 class='inline'>Выход</h2>";
        $HTML .= "<div class='button white button__zoom-in' title='Увеличить'><img class='bigIcon' src='".base_path()."images/icons/zoom-in.jpg'></div>";
        $HTML .= "<div class='button white button__zoom-out' title='Уменьшить'><img class='bigIcon' src='".base_path()."images/icons/zoom-out.jpg'></div>";
        $HTML .= "<div class='button white button__full-screen' title='Развернуть на весь экран' onclick=\"toggleClass('#tableOutContent', 'active');\"><img class='bigIcon' src='".base_path()."images/icons/full-view.jpg'></div>";
        $HTML .= "<div class='filexit'>";
        $HTML .= "<div class='button filtr' onclick=\"sendAjax('/filter/main/out/', 'GET');event.stopPropagation();\">Фильтр</div>";
        $HTML .= "</div>";
        $HTML .= "</div>";

        $HTML .= "<div class='button white' id='tableOutRollUpButton' title='Свернуть в стандартный режим' onclick=\"toggleClass('#tableOutContent', 'active');\"><img class='bigIcon' src='".base_path()."images/icons/standart-view.jpg'></div>";

        $HTML .= "<div class='overfl'>";
        $HTML .= "<table id='tableOut' border='1' cellpadding='3'>";


        if($passOut[0]->id==null) {
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

            $HTML .= "<tbody id='tableOutBody'>";
            for ($i = 0; $i < count($passOut); $i++) {
                $passOut[$i]->time_out = explode(".", $passOut[$i]->time_out);
                $passOut[$i]->time_out = $passOut[$i]->time_out[0];
                $class = ($i==0) ? "class='tableOutTrActive'" : "";
                $HTML .= "<tr {$class} onclick=\"sendAjax('/main/show-big-user/{$passOut[$i]->user_id}/ount/', 'GET'); divMakeActive(this, 'tableOutTr', 'tableOutTrActive');\">";
                $date = new DateTime($passOut[$i]->date_out);
                $HTML .= "<td class='robotocr'>{$date->Format('d.m.Y')}</td>";
                $HTML .= "<td class='robotocr'>{$passOut[$i]->time_out}</td>";
                $HTML .= "<td class='robotocr'><img src='" . GetImageURL($passOut[$i]->user_photo, 'user_photo') . "' width='30'/></td>";
                $HTML .= "<td class='ralewayreg'><a href='".base_path()."account/{$passOut[$i]->user_id}/'>{$passOut[$i]->surname} {$passOut[$i]->first_name} {$passOut[$i]->patronymic}</a></td>";
                $HTML .= "<td class='ralewayreg'>{$passOut[$i]->user_type_name}</td>";
                $HTML .= "<td class='ralewayreg'>{$passOut[$i]->user_departament_name}</td>";
                $HTML .= "<td class='ralewayreg'>{$passOut[$i]->user_room_name}</td>";
                $HTML .= "<td class='ralewayreg'>";
                if ($passOut[$i]->mark_name!=null) $HTML .= "{$passOut[$i]->mark_name}";
                else
                {
                    $HTML .= "<select class='ralewaymed' name='mark' onchange=\"sendAjax('/usermark/main/{$passOut[$i]->id}/'+this.value+'/', 'GET');\"
					onclick=\"event.stopPropagation();\">";
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
            $HTML .= "</tbody>";
        }
        $HTML .= "</table>";
        $HTML .= "</div>";
        $HTML .= "<div id='tableOutUserInfo'>";
        if(count($passOut)>0) {
            $HTML .= $controller->showBigUserPassView($passOut[0]->user_id, 'out');
        }
        $HTML .= "</div>";
        $HTML .= "</div>";
        #$HTML .= "<script>$($('#tableIn, #tableOut')).stickyTableHeaders({ scrollableArea: $('.overfl')[0], 'fixedOffset': 2 });</script>";
        echo $HTML;

        ?>

    </div>
</div>





