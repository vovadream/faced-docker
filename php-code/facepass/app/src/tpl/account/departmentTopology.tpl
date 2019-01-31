<?php

$HTML = "";
$HTML .= "<table>";
$HTML .= "<tbody>";
$HTML .= "<tr>";
$HTML .= "<td>ID</td>";
$HTML .= "<td>Название</td>";
$HTML .= "<td>Доступ</td>";
$HTML .= "</tr>";
$sql = "SELECT * FROM filial_departament WHERE filial_departament.group IS NOT TRUE";
$departaments = $controller->FilialDepartmentModel->sendQuery($sql);
for ($departaments_i = 0; $departaments_i < count($departaments); $departaments_i++) {
    $HTML .= "<tr>";
    $HTML .= "<td>{$departaments[$departaments_i]->id}</td>";
    $HTML .= "<td colspan='2'>Департамент: {$departaments[$departaments_i]->name}</td>";
    $HTML .= "</tr>";

    $sql = "SELECT * FROM filial_departament WHERE parent_id={$departaments[$departaments_i]->id}";
    $sections = $controller->FilialDepartmentModel->sendQuery($sql);
    for ($section_i = 0; $section_i < count($sections); $section_i++) {
        $sql = "SELECT * FROM workers_departamet_access WHERE worker_id='{$worker_id}' AND departament_id='{$sections[$section_i]->id}'";
        $worker_access = $controller->FilialDepartmentModel->sendQuery($sql);

        $HTML .= "<tr>";
        $HTML .= "<td>{$sections[$section_i]->id}</td>";
        $HTML .= "<td>Отдел: {$sections[$section_i]->name}</td>";
        $HTML .= "<td>";
        $HTML .= "<input type='checkbox' name='worker_access_{$sections[$section_i]->id}'";
        if ($worker_access != null && $worker_access[0]->id != null && $worker_access[0]->status) $HTML .= " checked ";
        $HTML .= "onchange=\"sendAjax('/workerdepartmnetaccess/{$worker_id}/{$sections[$section_i]->id}/'+this.checked+'/', 'GET');\">Доступ";
        $HTML .= "</td>";
        $HTML .= "</tr>";
    }
}
$HTML .= "</tbody>";
$HTML .= "</table>";
echo $HTML;