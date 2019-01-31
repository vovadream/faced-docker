


$subHTML .= "<div class='topology_item' {$style} id='department_{$floor_id}_{$departament->id}' onclick=\"activeTopologyItem('#department_{$floor_id}_{$departament->id}', '.topology_item_name', 'active', {$shablons['all']}); {$onclickSubDepartament}\">";
$subHTML .= "<div class='topology_item_name'>";

    if(isset($departament->rooms))
    $subHTML .= "<button onclick='divSlide(this, \"#department_{$floor_id}_{$departament->id}\", \".topology_item\", false); event.stopPropagation();'>{$buttonPlusMinus}</button>";

    if($sub==0)
    $subHTML .= "Департамент: {$departament->name}";
    else if($sub==1)
    $subHTML .= "Отдел: {$departament->name}";


    $subHTML .= "<div class='topology_submenu'>";
        $subHTML .= "<div class='topology_menu_icon'></div>";
        $subHTML .= "<div class='topology_menu'>";
            $subHTML .= "<div id='menu_work_schedule_section_{$departament->id}'><a href='" . base_path() . "workschedule/show/{$departament->id}/section/{$floor_id}/'>График работ</a></div>";
            if(!isset($departament->rooms) && ($departament->parent_id == 0))
            $subHTML .= "<div onclick=\"sendAjax('/topology/{$floor_id}/{$departament->id}/get/form/add/subdepartment/', 'GET');\">Добавить отдел</div>";
        $subHTML .= "<div onclick=\"sendAjax('/topology/{$floor_id}/{$departament->id}/get/form/add/room/', 'GET');\">Добавить кабинет</div>";
    $subHTML .= "<div id='menu_edit_section_{$departament->id}' onclick=\"{$onclickSubDepartamentEdit}\">Редактировать</div>";
    $subHTML .= "<div id='menu-delete-section-{$floor_id}-{$departament->id}' onclick=\"sendAjax('/topology/{$floor_id}/{$departament->id}/delete/departament/', 'POST');\">Удалить</div>";
$subHTML .= "</div>";
$subHTML .= "</div>";
$subHTML .= "</div>";

$subHTML .= "<div class='hiddenFormDiv' id='topologyHiddenForm_departament_{$floor_id}_{$departament->id}'></div>";

//Вывод кабинетов отдела
for ($rooms_i = 0; $rooms_i < count($departament->rooms); $rooms_i++) {
$onclickCabinet = str_replace('{room_id}', $departament->rooms[$rooms_i]->id, $shablons['onclickCabinetUrl']);
$onclickCabinet = str_replace('{topology_id}', $floor_id, $onclickCabinet);
$onclickCabinetEdit = str_replace('{room_id}', $departament->rooms[$rooms_i]->id, $shablons['onclickCabinetUrlEdit']);

$sublevel++;

$findRoomSearch = !$search;
if($search && (strpos($departament->rooms[$rooms_i]->name, $value)!==false || $findDepartamentSearch)) {
$findRoomSearch = $someFind = true;
}

$subRoomHTML = "<div class='topology_item' {$style} id='room_{$departament->rooms[$rooms_i]->id}' onclick=\"activeTopologyItem('#room_{$departament->rooms[$rooms_i]->id}', '.topology_item_name', 'active', {$shablons['all']}); {$onclickCabinet}\">";
$subRoomHTML .= "<div class='topology_item_name'>";
    if($departament->rooms[$rooms_i]->workers!=null)
    $subRoomHTML .= "<button onclick='divSlide(this, \"#room_{$departament->rooms[$rooms_i]->id}\", \".topology_item\", false); event.stopPropagation();'>{$buttonPlusMinus}</button>";
    $subRoomHTML .= "Кабинет: {$departament->rooms[$rooms_i]->name}";
    $subRoomHTML .= "<div class='topology_submenu'>";
        $subRoomHTML .= "<div class='topology_menu_icon'></div>";
        $subRoomHTML .= "<div class='topology_menu'>";
            $subRoomHTML .= "<div id='menu_add_worker_room_{$departament->rooms[$rooms_i]->id}' onclick=\"sendAjax('/topology/{$floor_id}/{$departament->id}/{$departament->rooms[$rooms_i]->id}/add/worker/form/', 'GET');
            event.stopPropagation();\">Добавить сотрудника</div>";
        $subRoomHTML .= "<div id='menu_delete_room_{$departament->rooms[$rooms_i]->id}' onclick=\"sendAjax('/topology/delete/room/{$floor_id}/{$departament->id}/{$departament->rooms[$rooms_i]->id}/', 'POST');
        event.stopPropagation();\">Удалить кабинет</div>";

    $subRoomHTML .= "<div id='menu_edit_room_{$departament->rooms[$rooms_i]->id}' onclick=\"{$onclickCabinetEdit}\">Редактировать</div>";
    $subRoomHTML .= "<div id='menu_work_schedule_room_{$departament->rooms[$rooms_i]->id}'>
        <a href='" . base_path() . "workschedule/show/{$departament->rooms[$rooms_i]->id}/room/{$floor_id}/'>График работ</a></div>";
    $subRoomHTML .= "</div>";
$subRoomHTML .= "</div>";
$subRoomHTML .= "</div>";

$subRoomHTML .= "<div class='hiddenFormDiv' id='topologyHiddenForm_departament_{$floor_id}_{$departament->id}_{$departament->rooms[$rooms_i]->id}'></div>";

//Вывод сотрудников кабинета
if($departament->rooms[$rooms_i]->workers!=null) {
$sublevel++;

$subRoomHTML .= "<div class='topology_item' {$style} id='workers_{$departament->rooms[$rooms_i]->id}' onclick=\"activeTopologyItem('#workers_{$departament->rooms[$rooms_i]->id}', '.topology_item_name', 'active', {$shablons['all']}); {$onclickCabinet}\" >";
$subRoomHTML .= "<div class='topology_item_name'>";
    $subRoomHTML .= "<button onclick='divSlide(this, \"#workers_{$departament->rooms[$rooms_i]->id}\", \".topology_item\", false); event.stopPropagation();'>{$buttonPlusMinus}</button>";
    $subRoomHTML .= "Сотрудники";
    $subRoomHTML .= "</div>";

$sublevel++;

$subRoomHTML .= "<div class='topology_item' {$style}>";
    for ($workers_i = 0; $workers_i < count($departament->rooms[$rooms_i]->workers); $workers_i++) {
    $fio = "{$departament->rooms[$rooms_i]->workers[$workers_i]->surname} {$departament->rooms[$rooms_i]->workers[$workers_i]->first_name} {$departament->rooms[$rooms_i]->workers[$workers_i]->patronymic}";

    $findWorkerSearch = !$search;
    if($search && (strpos($fio, $value)!==false || $findRoomSearch)) {
    $findWorkerSearch = $someFind = $findRoomSearch = true;
    }

    $onclickWorker = str_replace('{worker_id}', $departament->rooms[$rooms_i]->workers[$workers_i]->id, $shablons['onclickWorkerUrl']);
    $onclickWorker = str_replace('{topology_id}', $floor_id, $onclickWorker);
    $subWorkerHTML = "<div id='worker_{$departament->rooms[$rooms_i]->workers[$workers_i]->id}' onclick=\"activeTopologyItem('#worker_{$departament->rooms[$rooms_i]->workers[$workers_i]->id}', '.topology_item_name', 'active', {$shablons['all']}); {$onclickWorker}\">";
    $subWorkerHTML .= "<div class='topology_item_name'>";
        if($departament->rooms[$rooms_i]->workers[$workers_i]->hearing!=null)
        $subWorkerHTML .= "<button onclick='divSlide(this, \"#worker_{$departament->rooms[$rooms_i]->workers[$workers_i]->id}\", \".topology_item\", false); event.stopPropagation();'>{$buttonPlusMinus}</button>";
        $subWorkerHTML .= "{$fio}";
        $subWorkerHTML .= "<div class='topology_submenu'>";
            $subWorkerHTML .= "<div class='topology_menu_icon'></div>";
            $subWorkerHTML .= "<div class='topology_menu'>";
                $subWorkerHTML .= "<div id='menu_work_schedule_worker_{$departament->rooms[$rooms_i]->workers[$workers_i]->id}'>
                    <a href='" . base_path() . "workschedule/show/{$departament->rooms[$rooms_i]->workers[$workers_i]->id}/worker/{$floor_id}/'>График работ</a></div>";
                $subWorkerHTML .= "<div id='menu_add_hearing_{$departament->rooms[$rooms_i]->id}-{$departament->rooms[$rooms_i]->workers[$workers_i]->id}' onclick=\"sendAjax('/topology/add/hearing/{$departament->rooms[$rooms_i]->id}/{$departament->rooms[$rooms_i]->workers[$workers_i]->id}/form/', 'GET');
                event.stopPropagation();\">Добавить услугу</div>";
            $subWorkerHTML .= "<div id='menu_delete_hearing_{$departament->rooms[$rooms_i]->id}-{$departament->rooms[$rooms_i]->workers[$workers_i]->id}' onclick=\"sendAjax('/topology/unlink/worker/{$departament->rooms[$rooms_i]->id}/{$departament->rooms[$rooms_i]->workers[$workers_i]->id}/', 'POST');
            event.stopPropagation();\">Отвязать сотрудника</div>";
        $subWorkerHTML .= "</div>";
    $subWorkerHTML .= "</div>";
$subWorkerHTML .= "</div>";

$subWorkerHTML .= "<div class='hiddenFormDiv' id='topologyHiddenForm_worker-{$departament->rooms[$rooms_i]->id}-{$departament->rooms[$rooms_i]->workers[$workers_i]->id}'></div>";

$sublevel++;

$subWorkerHTML .= "<div class='topology_item' {$style}>";
    for ($hearing_i = 0; $hearing_i < count($departament->rooms[$rooms_i]->workers[$workers_i]->hearing); $hearing_i++) {
    $onclickHearing = str_replace('{hearing_id}', $departament->rooms[$rooms_i]->workers[$workers_i]->hearing[$hearing_i]->id, $shablons['onclickHearingUrl']);
    $onclickHearing = str_replace('topology_id',$floor_id, $onclickHearing);

    $findHearingSearch = !$search;
    if($search && (strpos($departament->rooms[$rooms_i]->workers[$workers_i]->hearing[$hearing_i]->name, $value)!==false || $findWorkerSearch)) {
    $findHearingSearch = $someFind = $findRoomSearch = $findWorkerSearch = true;
    }

    $subHearingHTML = "<div id='worker_hearing_{$departament->rooms[$rooms_i]->workers[$workers_i]->hearing[$hearing_i]->id}' onclick=\"activeTopologyItem('#worker_hearing_{$departament->rooms[$rooms_i]->workers[$workers_i]->hearing[$hearing_i]->id}', '.topology_item_name', 'active', {$shablons['all']}); {$onclickHearing}\">";
    $subHearingHTML .= "<div class='topology_item_name'>";
        $subHearingHTML .= "{$departament->rooms[$rooms_i]->workers[$workers_i]->hearing[$hearing_i]->name}";
        $subHearingHTML .= "<div class='topology_submenu'>";
            $subHearingHTML .= "<div class='topology_menu_icon'></div>";
            $subHearingHTML .= "<div class='topology_menu'>";
                $subHearingHTML .= "<div id='menu_work_schedule_hearing_{$departament->rooms[$rooms_i]->workers[$workers_i]->hearing[$hearing_i]->id}'>
                    <a href='" . base_path() . "workschedule/show/{$departament->rooms[$rooms_i]->workers[$workers_i]->hearing[$hearing_i]->id}/hearing/null/'>График работ</a></div>";
                $subHearingHTML .= "<div id='menu_work_schedule_hearing_week_template_{$departament->rooms[$rooms_i]->workers[$workers_i]->hearing[$hearing_i]->id}'
                                         onclick=\"sendAjax('/workschedule/weektemlateedit/show/{$departament->rooms[$rooms_i]->workers[$workers_i]->hearing[$hearing_i]->id}/', 'GET')\">Редактировать шаблон</div>";
            $subHearingHTML .= "<div id='menu_work_schedule_hearing_week_template_{$departament->rooms[$rooms_i]->workers[$workers_i]->hearing[$hearing_i]->id}'
                                     onclick=\"sendAjax('/topology/add/hearing/deletehearing/{$departament->rooms[$rooms_i]->workers[$workers_i]->hearing[$hearing_i]->id}/', 'GET')\">Удалить услугу</div>";
        $subHearingHTML .= "</div>";
    $subHearingHTML .= "</div>";
$subHearingHTML .= "</div>";
$subHearingHTML .= "</div>";
if($findHearingSearch) $subWorkerHTML .= $subHearingHTML;
}
$subWorkerHTML .= "</div>";

$subWorkerHTML .= "</div>";
if($findWorkerSearch) $subRoomHTML .= $subWorkerHTML;
}
$subRoomHTML .= "</div>";
$subRoomHTML .= "</div>";
$sublevel--;
}
$sublevel--;
$subRoomHTML .= "</div>";
if($findRoomSearch) $subHTML .= $subRoomHTML;
}
$subHTML .= "</div>";
if($someFind) $HTML .= $subHTML;
