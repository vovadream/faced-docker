<?php



/*
Вывод топологии для формирования категории доступа сотрудника
*/

$HTML = "";
//$HTML .= "<table>";
//$HTML .= "<tbody>";
for ($i = 0; $i < count($topology); $i++) {
    if (!$topology[$i]->room) {
        //$HTML .= "<div id='main_category_{$topology[$i]->id}'><tr><td>";
        $HTML .= "<tr><td>";
        for ($j = 0; $j < $level; $j++)
            $HTML .= "-";
        $HTML .= "{$topology[$i]->name}";
        //select departaments
        $sql = "SELECT * FROM filial_departament WHERE id IN
                        (SELECT parent_id FROM filial_departament WHERE id IN
                        (SELECT DISTINCT department_id FROM filial_rooms WHERE parent_id={$topology[$i]->id} AND room IS TRUE))";
        $departaments = $controller->sendQuery($sql);
        for ($departaments_i = 0; $departaments_i < count($departaments); $departaments_i++) {
            $HTML .= "/{$departaments[$departaments_i]->name}</td><td></td><td></td>";
        }
        //$HTML .= "</div></tr>";
        $HTML .= "</tr>";
        //select rooms
        $sql = "SELECT filial_rooms.*, filial_departament.name AS dep_name FROM filial_rooms
                        LEFT JOIN filial_departament ON filial_departament.id=filial_rooms.department_id
                        WHERE filial_rooms.parent_id={$topology[$i]->id} AND room IS TRUE";
        $rooms = $controller->sendQuery($sql);
        for ($rooms_i = 0; $rooms_i < count($rooms); $rooms_i++) {
            //$HTML .= "<tr><td><div id='room_{$rooms[$rooms_i]->id}'>";
            $HTML .= "<tr><td>";
            $HTML .= "Кабинет: {$rooms[$rooms_i]->name}/{$rooms[$rooms_i]->dep_name}</td>";
            $sql = "SELECT * FROM workers_permissions_access WHERE worker_id='{$worker_id}' AND room_id='{$rooms[$rooms_i]->id}'";
            $worker_access = $controller->sendQuery($sql);
            $HTML .= "<td><input type='checkbox' name='worker_category_{$rooms[$rooms_i]->id}'";
            if ($worker_access != null && $worker_access[0]->id != null && $worker_access[0]->status) $HTML .= " checked ";
            $HTML .= "onchange=\"sendAjax('/workers/access/category/{$worker_id}/{$rooms[$rooms_i]->id}/'+this.checked+'/', 'GET');\">Доступ</td>";
            $HTML .= "<td><input type='checkbox' name='worker_security_{$rooms[$rooms_i]->id}'";
            if ($worker_access != null && $worker_access[0]->id != null && $worker_access[0]->security_mode) $HTML .= " checked ";
            $HTML .= "onchange=\"sendAjax('/workers/access/security/{$worker_id}/{$rooms[$rooms_i]->id}/'+this.checked+'/', 'GET');\">Установить/снять с охраны";
            //$HTML .= "</td></tr></div>";
            $HTML .= "</td></tr>";
        }
        if ($topology[$i]->sub != null) {

            $HTML .= tpl('account/workerAccessTopology', [
                'level'      => $level + 1,
                'topology'   => $topology[$i]->sub,
                'worker_id'  => $worker_id,
                'controller' => $controller
            ]);
        }

    }
    if ($topology[$i]->room && $level == 0) {
        $sql = "SELECT filial_rooms.*, filial_departament.name AS dep_name FROM filial_rooms
                        LEFT JOIN filial_departament ON filial_departament.id=filial_rooms.department_id
                        WHERE filial_rooms.id={$topology[$i]->id}";
        $rooms = $controller->sendQuery($sql);
        //$HTML .= "<div id='room_{$rooms[0]->id}'>";
        $HTML .= "<tr><td>Кабинет: {$rooms[0]->name}/{$rooms[0]->dep_name}</td>";
        $sql = "SELECT * FROM workers_permissions_access WHERE worker_id='{$worker_id}' AND room_id='{$rooms[0]->id}'";
        $worker_access = $controller->sendQuery($sql);
        $HTML .= "<td><input type='checkbox' name='worker_category_{$rooms[0]->id}'";
        if ($worker_access != null && $worker_access[0]->id != null && $worker_access[0]->status) $HTML .= " checked ";
        $HTML .= "onchange=\"sendAjax('/workers/access/category/{$worker_id}/{$rooms[0]->id}/'+this.checked+'/', 'GET');\">Доступ</td>";
        $HTML .= "<td><input type='checkbox' name='worker_security_{$rooms[0]->id}'";
        if ($worker_access != null && $worker_access[0]->id != null && $worker_access[0]->security_mode) $HTML .= " checked ";
        $HTML .= "onchange=\"sendAjax('/workers/access/security/{$worker_id}/{$rooms[0]->id}/'+this.checked+'/', 'GET');\">Установить/снять с охраны</td></tr>";
        //$HTML .= "</div>";
    }
}
//$HTML .= "</tbody>";
//$HTML .= "</table>";
echo $HTML;
