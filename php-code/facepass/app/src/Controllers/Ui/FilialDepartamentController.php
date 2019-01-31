<?php

namespace App\Controllers\Ui;

use App\Controllers\Controller;
use App\Models\FilialRoomModel;
use App\Models\FilialDepartmentModel;
use App\Models\FilialDepartmentRoomsModel;
use Slim\Container;
use Slim\Http\Request as Request;
use Slim\Http\Response as Response;

use PDO;

class FilialDepartamentController extends Controller
{

    /**
     * AccountController constructor.
     * @param Container $c
     */
    public function __construct(Container $c)
    {
        parent::__construct($c);
        $this->db = $c->get('db');

    }

    /*
     * Добавление отдела филиала
     * Return: JSON
     */
    public function addFilialDepartmentControl(Request $request, Response $response)
    {
        $model = $this->FilialDepartmentModel;
        $data = $request->getParsedBody();
        $type = $request->getAttribute('type');
        $data = $model->addFilialDepartmentModel($data, $type);
        $response = $response->withJson($data);
        return $response;
    }

    /*
     * Изменение отдела филала
     * Return: JSON
     */
    public function updateFilialDepartmentControl(Request $request, Response $response)
    {
        $model = $this->FilialDepartmentModel;
        $id = $request->getAttribute('id');
        $type = $request->getAttribute('type');
        $data = $request->getParsedBody();
        $data = $model->updateFilialDepartmentModel($data, $id, $type);
        $response = $response->withJson($data);
        return $response;
    }

    /*
     * Вывод формы добавления отдела филиала
     * Return: HTML
     */
    public function showAddFilialDepartmentControl(Request $request, Response $response)
    {
        $type = $request->getAttribute('type');
        $response = $this->showAddFilialDepartmentView($type);
        return $response;
    }

    /*
     * Вывод формы изменения отдела филиала
     * Return: HTML
     */
    public function showChangeFilialDepartmentControl(Request $request, Response $response)
    {
        $id = $request->getAttribute('id');
        $type = $request->getAttribute('type');
        $response = $this->showChangeFilialDepartmentView($id,$type);
        return $response;
    }

    public function addRoomPermissionToFilialDepartmentControl(Request $request, Response $response)
    {
        $filialRoomsModel = $this->FilialDepartmentRoomsModel;
        $id = $request->getAttribute('id');
        $data = $request->getParsedBody();
        $data = $filialRoomsModel->addRoomPermissionToFilialDepartmentModel($data, $id);
        $response = $response->withJson($data);
        return $response;
    }

    public function updateRoomPermissionToFilialDepartmentControl(Request $request, Response $response)
    {
        $filialRoomsModel = $this->FilialDepartmentRoomsModel;
        $id = $request->getAttribute('id');
        $data = $request->getParsedBody();
        $data = $filialRoomsModel->updateRoomPermissionToFilialDepartmentModel($data, $id);
        $response = $response->withJson($data);
        return $response;
    }

    public function showAddRoomPermissionToFilialDepartmentFormControl(Request $request, Response $response)
    {
        $id = $request->getAttribute('id');
        $html = $this->showAddRoomPermissionToFilialDepartmentFormView($id);
        $data = [];
        $data['html'] = $html;
        $data['status'] = 'success';
        $data['div'] = 'popup';
        $response = $response->withJson($data);
        return $response;
    }

    /*
     * Вывод формы изменения доступа филиала к помещению
     * Return: HTML
     */
    public function showChangeRoomPermissionToFilialDepartmentControl(Request $request, Response $response)
    {
        $id = $request->getAttribute('id');
        $html = $this->showChangeRoomPermissionToFilialDepartmentView($id);
        $data = [];
        $data['status'] = 'success';
        $data['div'] = 'popup';
        $data['html'] = $html;
        $response = $response->withJson($data);
        return $response;
    }

    /*
    * Вывод таблицы доступа отдела к помещениям
    * Return: JSON
    */
    public function showTablePermissionFilialDepartmentRoomsControl(Request $request, Response $response)
    {
        $id = $request->getAttribute('id');
        $html = $this->showFilialDepartmentsRoomPermissionTableView($id);
        $data = [];
        $data['html'] = $html;
        $data['status'] = 'success';
        $data['div'] = 'filialDepartmentsRoomPermissionTable';
        $response = $response->withJson($data);
        return $response;
    }


    /*
    *Вывод формы для создания отдела филиала
    *Return: JSON
    */


    public function showAddFilialDepartmentView($type=null)
    {
        $json = [];
        $HTML = "";
        $superDepartments=null;
        $model = $this->FilialDepartmentModel;
        $HTML .= "<h2>Добавление ";

        if ($type=='section')
        {
            $HTML .= "отдела ";
            $superDepartments= $model->getFilialDepartmentModel(null,'department');
        }

        if ($type=='department') $HTML .= "департамента ";
        $HTML .= "филиала</h2>";
        $HTML .= "<form name='addFilialDepartmentForm'>";
        $HTML .= "<table><tr><td>Название</td><td><input type='text' name='name'></td></tr>";

        if ($type=='section' && $superDepartments!=null)
        {
            $HTML .= "<tr><td>Департамент</td><td><select name='parent_id'>";
            if (isset($superDepartments['status'])) {
                $HTML .= "<option value='0'>Нет данных</option>";
            } else {
                $HTML .= "<option value='0'>Не выбран департамент</option>";
                for ($i = 0; $i < count($superDepartments); $i++) {
                    $HTML .= "<option value='{$superDepartments[$i]->id}'>{$superDepartments[$i]->name}</option>";
                }
            }
            $HTML .= "</select></td></tr>";
        }

        $HTML .= "<tr><td><input type='checkbox' name='public'>Публичный</td><td></td></tr></table>";
        $HTML .= "</form>";
        $HTML .= "<div class='button' onclick=\"sendAjax('/filial-departments/create/{$type}/', 'POST', 'addFilialDepartmentForm')\">Создать";
        if ($type=='section') $HTML .= " отдел ";
        if ($type=='department') $HTML .= " департамент ";
        $HTML .= "</div>";
        $json['status'] = 'success';
        $json['div'] = 'popup';
        $json['html'] = htmlspecialchars($HTML);
        return json_encode($json);
    }

    /*
     * Вывод формы для изменения отдела филиала
     * Return: JSON
     */
    public function showChangeFilialDepartmentView($id = null, $type=null)
    {
        $model = $this->FilialDepartmentModel;
        $json = [];
        $filialDepartment = $model->getFilialDepartmentModel($id, $type);
        $HTML = "";
        $HTML .= "<h2>Изменение ";
        $superDepartments=null;
        if ($type=='section')
        {
            $HTML .= "отдела ";
            $superDepartments=$model->getFilialDepartmentModel(null,'department');
        }

        if ($type=='department') $HTML .= "департамента ";
        $HTML .= " {$filialDepartment[0]->name}</h2>";
        $HTML .= "<form name='updateFilialDepartmentForm'>";
        $HTML .= "<table><tr><td>Название</td><td><input type='text' name='name'  value='{$filialDepartment[0]->name}'></td></tr>";
        if ($type=='section'&&$superDepartments!=null)
        {
            $HTML .= "<tr><td>Департамент</td><td><select name='parent_id'>";
            if (isset($superDepartments['status'])) {
                $HTML .= "<option value='0'>Нет данных</option>";
            } else {
                $HTML .= "<option value='0'>Не выбран департамент</option>";
                for ($i = 0; $i < count($superDepartments); $i++) {
                    $HTML .= "<option value='{$superDepartments[$i]->id}'";
                    if($superDepartments[$i]->id==$filialDepartment[0]->parent_id) $HTML .= " selected";
                    $HTML .= ">{$superDepartments[$i]->name}</option>";
                }
            }
            $HTML .= "</select></td></tr>";
        }
        $HTML .= "</tr><td><input type='checkbox' name='public'";
        if($filialDepartment[0]->public) $HTML .= " checked";
        $HTML .= ">Публичный</td><td></td></tr></table>";
        $HTML .= "</form>";
        $HTML .= "<div class='button' onclick=\"sendAjax('/filial-departments/change/{$filialDepartment[0]->id}/{$type}/', 'POST', 'updateFilialDepartmentForm')\">Изменить";
        if ($type=='section') $HTML .= " отдел ";
        if ($type=='department') $HTML .= " департамент ";
        $HTML .= "</div>";
        $json['status'] = 'success';
        $json['div'] = 'popup';
        $json['html'] = htmlspecialchars($HTML);
        return json_encode($json);
    }


    /*
     * Вывод формы добавления доступа к помещению для отдела
     * Return: HTML
     */
    public function showAddRoomPermissionToFilialDepartmentFormView($id = null)
    {
        $model = $this->FilialDepartmentModel;
        $departament = $model->getFilialDepartmentModel($id, 'section');
        $query = "SELECT * FROM filial_rooms WHERE id NOT IN(SELECT DISTINCT (room_id) FROM filial_departament_rooms WHERE departament_id='{$id}')";
        $rooms = $model->sendQuery($query);

        $HTML = "";
        if ($id != 0 && $id != null) {
            $HTML .= "<h2>Добавление стандартного доступа к помещению - отдел {$departament[0]->name}</h2>";
            $HTML .= "<form name='addRoomPermissionToFilialDepartament'>";
            $HTML .= "<input type='hidden' name='departament_id' value='{$id}'>";
            $HTML .= "<table><tr><td>Помещение</td><td><select name='room_id'>";
            if (isset($rooms['status'])) {
                $HTML .= "<option value='0' disabled>Нет данных</option>";
            } else {
                $HTML .= "<option value='0' disabled>Не выбрано помещение</option>";
                for ($i = 0; $i < count($rooms); $i++) {
                    $HTML .= "<option value='{$rooms[$i]->id}'>{$rooms[$i]->name}</option>";
                }
            }
            $HTML .= "</select></td></tr>";
            $HTML .= "<tr><td>Статус</td><td><select name='status'>";
            $HTML .= "<option value='true'>Разрешить</option>";
            $HTML .= "<option value='false'>Запретить</option>";
            $HTML .= "</select></td></tr>";
            $HTML .= "</form>";
            $HTML .= "<div class='button' onclick=\"sendAjax('/filial-departments/addroom/{$id}/', 'POST', 'addRoomPermissionToFilialDepartament')\">Создать стандартный доступ к помещению для отдела</div>";
        } else {
            $HTML .= "Ошибка передачи параметров.";
        }
        return $HTML;
    }

    /*
     * Вывод формы для изменения доступа к помещению для отдела
     * Return: JSON
     */
    public function showChangeRoomPermissionToFilialDepartmentView($id = null)
    {
        $HTML = "";
        $model = $this->FilialDepartmentModel;
        $filialRoomsModel = $this->FilialDepartmentRoomsModel;
        $roomsModel = $this->FilialRoomModel;
        $filial_departament_rooms = $filialRoomsModel->getFilialDepartmentRoomsPermissionsModel($id);
        $department = $model->getFilialDepartmentModel($filial_departament_rooms[0]->departament_id);
        $room = $roomsModel->getRoomModel($filial_departament_rooms[0]->room_id);


        $HTML .= "<h2>Изменение доступа отдела {$department[0]->name} к помещению {$room[0]->name}</h2>";
        $HTML .= "<form name='updateFilialDepartmentRoomsPermissionsForm'>";
        $HTML .= "<table><tr><td>Статус</td><td><select name='status'>";
        $HTML .= "<option value='true' ";
        if ($filial_departament_rooms[0]->status == true) $HTML .= " selected ";
        $HTML .= " >Разрешить</option>";
        $HTML .= "<option value='false' ";
        if ($filial_departament_rooms[0]->status == false) $HTML .= " selected ";
        $HTML .= " >Запретить</option>";
        $HTML .= "</select></td></tr></table>";
        $HTML .= "</form>";
        $HTML .= "<div class='button' onclick=\"sendAjax('/filial-departments/addroommodify/{$id}/', 'POST', 'updateFilialDepartmentRoomsPermissionsForm')\">Изменить</div>";
        return $HTML;
    }

    /*
     * Таблица стандартных доступов отдела к помещениям
     * Return: HTML
     */
    public function showFilialDepartmentsRoomPermissionTableView($id = null)
    {
        $model = $this->FilialDepartmentRoomsModel;
        $permissions_def_rooms = $model->getFilialDepartmentRoomsPermissionsModel($id);
        $HTML ="";
        if ($id != 0)
            $HTML .= "<div class='button' onclick=\"sendAjax('/filial-departments/formaddroom/{$id}/', 'GET')\">Создать новое право доступа</div><br><br>";

        $HTML .= "<table class='akkt' border='1' cellpadding='5'>";
        $HTML .= "<tr>";
        $HTML .= "<th>№</th>";
        $HTML .= "<th>№ отдела</th>";
        $HTML .= "<th>Название отдела</th>";
        $HTML .= "<th>Идентификатор комнаты</th>";
        $HTML .= "<th>Название комнаты</th>";
        //$HTML .= "<td>room_floor</td>";
        $HTML .= "<th>№ комнаты</th>";
        $HTML .= "<th>Статус</th>";
        $HTML .= "<th></th>";
        $HTML .= "</tr>";

        if (isset($permissions_def_rooms['status'])) {
            $HTML .= "<tr>";
            $HTML .= "<td class='error'>{$permissions_def_rooms['message']}</td>";
            $HTML .= "</tr>";
        } else {
            for ($i = 0; $i < count($permissions_def_rooms); $i++) {
                $HTML .= "<tr>";
                $HTML .= "<td>{$permissions_def_rooms[$i]->id}</td>";
                $HTML .= "<td>{$permissions_def_rooms[$i]->departament_id}</td>";
                $HTML .= "<td>{$permissions_def_rooms[$i]->departament_name}</td>";
                $HTML .= "<td>{$permissions_def_rooms[$i]->room_id}</td>";
                $HTML .= "<td>{$permissions_def_rooms[$i]->room_name}</td>";
                //$HTML .= "<td>{$permissions_def_rooms[$i]->room_floor}</td>";
                $HTML .= "<td>{$permissions_def_rooms[$i]->room_number}</td>";
                if ($permissions_def_rooms[$i]->status == 1) $HTML .= "<td>Разрешен</td>";
                if ($permissions_def_rooms[$i]->status == 0) $HTML .= "<td>Запрещен</td>";
                $HTML .= "<td class='button' onclick=\"sendAjax('/filial-departments/formaddroommodify/{$permissions_def_rooms[$i]->id}/', 'GET')\">Изменить</td>";
                //$HTML .= "<td class='button' onclick=\"sendAjax('deleteMark={$marks[$i]->id}', 'DELETE')\">Удалить</td>";
                $HTML .= "</tr>";
            }
        }
        $HTML .= "</table>";
        return $HTML;
    }
}