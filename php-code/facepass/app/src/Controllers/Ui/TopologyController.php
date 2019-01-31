<?php

namespace App\Controllers\Ui;

use App\Controllers\Controller;
use \Slim\Container;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Slim\Http\Response as Response;

use App\Controllers\HttpClientController;
use App\Models\InterfaceModel;
use App\Models\TopologyModel;
use App\Models\WorkersModel;
use App\Models\WorkScheduleModel;
use App\Models\FilialDepartmentModel;
use App\Models\FilialRoomModel;
use App\Models\FilialGroupRoomsModel;
use App\Views\InterfaceView;

class TopologyController extends Controller
{
    /**
     * @var InterfaceModel
     */
    private $model;

    /**
     * @var InterfaceView
     */
    private $view;

    /**
     * @var EquipmentModel
     */
    private $equipment;

    /**
     * @var array settings
     */
    private $settings;

    /**
     * @var HttpClientController
     */
    private $client;

    public function __construct(Container $c)
    {
        parent::__construct($c);
        $this->settings = $c->get('settings');
        $this->model = $c->get('InterfaceModel');
        $this->view = $c->get('InterfaceView');
        $this->equipment = $c->get('EquipmentModel');
        $this->client = $c->get('HttpClientController');
    }

    /*
     * Вывод окна топологии
     * Return: JSON
     */
    public function actionIndex(Request $request, Response $response)
    {
        $data = layout('topology/views/index', []);

        return $response->getBody()->write($data);
    }

    //То что касается таблиц.

    public function actionAjaxTableTopology(Request $request, Response $response)
    {
        $elementId = $request->getAttribute('id');
        $type = $request->getAttribute('type');

        $model = $this->TopologyModel;
        $parent = $model->getOneByElementId($elementId, $type);
        $parentItem = $model->getItem($parent['element_id'], $parent['type']);
        $rows = $model->getRowsForTable($parent['id'], 0);

        $data['status'] = 'success';
        $data['div'] = 'selectedtopologygroup';
        $data['html'] = tpl('topology/views/table', ['rows' => $rows, 'groupName' => $parentItem['name']]);
        $response = $response->withJson($data);
        return $response;
    }

    public function actionAjaxChangeRoom(Request $request, Response $response)
    {
        $data['status'] = 'success';
        $data['div'] = 'popup';
        $data['html'] = tpl('topology/views/change-room', [
            'workerChangeId' => $request->getAttribute('worker-id')
        ]);
        $response = $response->withJson($data);
        return $response;
    }

    public function actionAjaxMakeChangeRoom(Request $request, Response $response)
    {

        $model = $this->TopologyModel;
        $data = $request->getParsedBody();
        $data['status'] = 'success';

        $id = $request->getAttribute('room-id');
        $data['topology_parent'] = $id;
        $item = $model->getOne($id);

        $data['worker_id'] = $request->getAttribute('worker-id');
        $data['room_id'] = $item['element_id'];
        $data = $model->addWorkerToRoom($data);
        $response = $response->withJson($data);
        return $response;
    }

    public function actionAjaxAddWorkerToRoom(Request $request, Response $response)
    {
        $elementId = $request->getAttribute('id');
        $type = $request->getAttribute('type');

        $model = $this->TopologyModel;
        $parent = $model->getOneByElementId($elementId, $type);
        $parentItem = $model->getItem($parent['element_id'], $parent['type']);
        $rows = $model->getRowsForTable($parent['id'], 0);

        $data['status'] = 'success';
        $data['div'] = 'selectedtopologygroup';
        $data['html'] = tpl('topology/views/table', ['rows' => $rows, 'groupName' => $parentItem['name']]);
        $response = $response->withJson($data);
        return $response;
    }


    /*
     * Вывод формы добавления подгруппы комнат
     * Return: JSON
     */
    public function actionGroupRoomsAdd(Request $request, Response $response)
    {
        if ($request->isGet()) {
            $data = [];
            $data['status'] = 'success';
            $data['id'] = $request->getAttribute('id');
            $data['div'] = "popup";
            $data['html'] = tpl('topology/views/ajax/group-rooms-add', $data);
            $response = $response->withJson($data);
            return $response;
        }
        if ($request->isPost()) {
            $model = $this->TopologyModel;
            $data = $request->getParsedBody();
            $data['status'] = 'success';
            $id = $request->getAttribute('id');

            $item = $model->getOne($id);
            $data['floor_id'] = $item['element_id'];
            $data['topology_parent'] = $id;
            $data = $model->checkAddFloor($data);
            $response = $response->withJson($data);
            return $response;
        }
    }

    /*
     * Вывод формы изменения группы комнат
     * Return: JSON
     */
    public function actionGroupRoomsUpdate(Request $request, Response $response)
    {
        if ($request->isGet()) {
            $model = $this->TopologyModel;
            $group_id = $request->getAttribute('id');

            $item = $model->getOne($group_id);

            $groupRoomsModel = $this->FilialGroupRoomsModel;
            $workersModel = $this->WorkersModel;
            $filialsModel = $this->FilialDepartmentModel;

            $group_rooms = $groupRoomsModel->sendQuery("SELECT * FROM filial_group_rooms WHERE id='{$item['element_id']}'");
            $workers = $workersModel->getWorkersModel();
            $category = $groupRoomsModel->getRoomModel(null);
            $sections = $filialsModel->getFilialDepartmentModel(null, 'section');


            $html = tpl('topology/views/ajax/group-rooms-update', [
                'group_rooms' => $group_rooms,
                'workers' => $workers,
                'category' => $category,
                'sections' => $sections,
                'group_id' => $group_id
            ]);

            $data['status'] = 'success';
            $data['div'] = 'popup';
            $data['html'] = $html;
            $response = $response->withJson($data);
            return $response;
        }
        if ($request->isPost()) {
            $model = $this->TopologyModel;

            $group_id = $request->getAttribute('id');
            $item = $model->getOne($group_id);


            $data = $request->getParsedBody();
            $newParent = $model->getOneByElementId($data['parent_id'], 'group_rooms');
            $model->updateTopologyItem($item['id'], $newParent['id']);
            $data = $model->updateGroupRooms($data, $item['element_id']);
            $response = $response->withJson($data);
            return $response;
        }

    }

    public function actionGroupRoomsDelete(Request $request, Response $response)
    {
        $model = $this->TopologyModel;
        //$groupRoomsModel = new FilialGroupRoomsModel();

        $data = [];

        $group_id = $request->getAttribute('id');

        $item = $model->getOne($group_id);

        $data['floor_id'] = $item['element_id'];


        $data = $model->topologyDeleteFloor($data);
        $model->deleteTopologyItem($group_id);
        $data['status'] = 'success';
        $data['reload'] = 'true';
        $response = $response->withJson($data);

        return $response;
    }


    /*
     * Добавление департамента
     * Return: JSON
     */
    public function actionDepartamentAdd(Request $request, Response $response)
    {
        if ($request->isGet()) {
            $id = $request->getAttribute('id');
            $data = [];
            $data['id'] = $id;
            $data['html'] = tpl('topology/views/ajax/departament-add', $data);
            $data['status'] = 'success';
            $data['div'] = "popup";
            $response = $response->withJson($data);
            return $response;
        }
        if ($request->isPost()) {
            $model = $this->TopologyModel;

            $group_id = $request->getAttribute('id');
            $item = $model->getOne($group_id);
            $data = $request->getParsedBody();
            $data['topology_parent'] = $group_id;

            if ($item['type'] == 'group_rooms') {
                $data = $model->checkAddFilialDepartamentToFloor($item['element_id'], $data);
            }
            if ($item['type'] == 'departament') {
                $departamentModel = $this->FilialDepartmentModel;

                $filialDepartment = $departamentModel->getFilialDepartmentModel($item['element_id']);
                $data['parent_id'] = $filialDepartment[0]->id;
                $data = $model->checkAddFilialSubdepartament($data);
            }

            $response = $response->withJson($data);
            return $response;
        }

    }

    /*
     * Изменение департамента
     * Return: JSON
     */
    public function actionDepartamentUpdate(Request $request, Response $response)
    {
        if ($request->isGet()) {
            $id = $request->getAttribute('id');
            $model = $this->TopologyModel;
            $departamentModel = $this->FilialDepartmentModel;

            $item = $model->getOne($id);

            $json = [];
            $filialDepartment = $departamentModel->getFilialDepartmentModel($item['element_id']);
            $superDepartments = $departamentModel->getFilialDepartmentModel(null);
            $json['status'] = 'success';
            $json['div'] = 'popup';
            $json['html'] = htmlspecialchars(tpl('topology/views/ajax/departament-update', [
                'filialDepartment' => $filialDepartment,
                'superDepartments' => $superDepartments,
                'id' => $id
            ]));
            $response = json_encode($json);
            return $response;
        }
        if ($request->isPost()) {
            $model = $this->TopologyModel;

            $id = $request->getAttribute('id');
            $item = $model->getOne($id);

            $data = $request->getParsedBody();
            $newParent = $model->getOneByElementId($data['parent_id'], 'departament');
            $model->updateTopologyItem($item['id'], $newParent['id']);
            $data = $model->updateDepartament($data, $item['element_id']);
            $response = $response->withJson($data);
            return $response;
        }
    }


    /*
     * Удаление департамента
     * Return: JSON
     */
    public function actionDepartamentDelete(Request $request, Response $response)
    {
        $model = $this->TopologyModel;
        $data = [];
        $id = $request->getAttribute('id');

        $item = $model->getOne($id);

        $data['departament_id'] = $item['element_id'];

        $data = $model->topologyCheckDeleteDepartament($data);
        $model->deleteTopologyItem($id);

        $response = $response->withJson($data);
        return $response;
    }


    /*
    * Добавление комнаты
    * Return: JSON
    */
    public function actionRoomAdd(Request $request, Response $response)
    {
        if ($request->isGet()) {
            $model = $this->TopologyModel;
            $data = [];
            $id = $request->getAttribute('id');
            $data['id'] = $id;
            $item = $model->getOne($id);
            $data['departament_id'] = $item['element_id'];
            $data['html'] = tpl('topology/views/ajax/room-add', $data);
            $data['status'] = 'success';
            $data['div'] = "popup";
            $response = $response->withJson($data);
            return $response;
        }
        if ($request->isPost()) {
            $model = $this->TopologyModel;
            $data = $request->getParsedBody();
            $id = $request->getAttribute('id');

            $item = $model->getOne($id);

            $data['topology_parent'] = $id;
            $data['departament_id'] = $item['element_id'];

            $data = $model->checkAddRoomToDepartament($data);
            $response = $response->withJson($data);
            return $response;
        }
    }


    /*
    * комнаты
    * Return: JSON
    */
    public function actionRoomUpdate(Request $request, Response $response)
    {

    }

    /*
    * Удаление комнаты
    * Return: JSON
    */
    public function actionRoomDelete(Request $request, Response $response)
    {
        $model = $this->TopologyModel;
        $data = [];
        $id = $request->getAttribute('id');
        $item = $model->getOne($id);

        $data['room_id'] = $item['element_id'];
        $data = $model->topologyDeleteRoom($data);
        $model->deleteTopologyItem($id);
        $data['status'] = 'success';
        $data['reload'] = 'true';
        $response = $response->withJson($data);
        return $response;
    }

    /*
    * Добавление сотрудника в топологии, привязка к кабинету
    * Return: JSON
    */
    public function actionWorkerAddToRoom(Request $request, Response $response)
    {
        if ($request->isGet()) {
            $model = $this->TopologyModel;
            $id = $request->getAttribute('id');
            $data['id'] = $id;
            $item = $model->getOne($id);
            $data['room_id'] = $item['element_id'];
            $room = $model->getItem($item['element_id'], 'room');
            $departaments = $model->getWorkerInDepartament($room['department_id']);
            $data['departaments'] = $departaments['departaments'];

            $html = tpl('topology/views/ajax/worker-add', $data);
            $data['status'] = 'success';
            $data['div'] = "popup";
            $data['html'] = $html;
            $response = $response->withJson($data);
            return $response;
        }

        if ($request->isPost()) {
            $model = $this->TopologyModel;
            $data = $request->getParsedBody();
            $data['status'] = 'success';

            $id = $request->getAttribute('id');
            $data['topology_parent'] = $id;
            $item = $model->getOne($id);

            $data['room_id'] = $request->getAttribute('worker_id');
            $data['room_id'] = $item['element_id'];

            $data = $model->addWorkerToRoom($data);
            $response = $response->withJson($data);
            return $response;
        }
    }

    /*
    * Удаление сотрудника из топологии, отвязка от кабинета
    * Return: JSON
    */
    public function actionWorkerDeleteFromRoom(Request $request, Response $response)
    {
        $model = $this->TopologyModel;
        $data = [];
        $id = $request->getAttribute('id');
        $data['topology_parent'] = $id;

        $item = $model->getOne($id);
        $data['worker_id'] = $item['element_id'];

        $parent = $model->getOne($item['parent_id']);
        $data['room_id'] = $parent['element_id'];


        $data = $model->unlinkTopologyWorkerModel($data);
        $response = $response->withJson($data);
        return $response;
    }

    /*
    * Добавление услуги
    * Return: JSON
    */
    public function actionServiceAdd(Request $request, Response $response)
    {
        if ($request->isGet()) {
            $data = [];
            $id = $request->getAttribute('id');
            $data['id'] = $id;

            $data['html'] = tpl('topology/views/ajax/service-add', $data);
            $data['status'] = 'success';
            $data['div'] = "popup";
            $response = $response->withJson($data);
            return $response;
        }
        if ($request->isPost()) {
            $model = $this->TopologyModel;
            $id = $request->getAttribute('id');
            $data = $request->getParsedBody();
            $data['topology_parent'] = $id;

            $item = $model->getOne($id);
            $worker_id = $item['element_id'];

            $parent = $model->getOne($item['parent_id']);
            $room_id = $parent['element_id'];


            $data = $model->addHearingTopologyModel($data, $room_id, $worker_id);
            $response = $response->withJson($data);
            return $response;
        }
    }

    /*
    * услуги
    * Return: JSON
    */
    public function actionServiceUpdate(Request $request, Response $response)
    {
        if($request->isGet()) {
            $id = $request->getAttribute('id');
            $topologyModel = $this->TopologyModel;
            $item = $topologyModel->getOne($id);
            $model = $this->WorkScheduleModel;
            $hearing = $model->sendQuery("SELECT * FROM filial_rooms_hearing WHERE id='{$item['element_id']}'");
            $day_types =array('1'=>"Рабочий",'2'=>"Сокращенный",'3'=>"Выходной");

            $html = tpl('work-schedule/edit-tmp', [
                'hearing' => $hearing,
                'day_types' => $day_types,
                'id' => $item['element_id']
            ]);

            $data['status'] = 'success';
            $data['div'] = 'popup';
            $data['html'] = $html;
            $response = $response->withJson($data);
            return $response;
        }
        if ($request->isPost()) {
            $model = $this->WorkScheduleModel;
            $data = $request->getParsedBody();

            $hearing_id = $request->getAttribute('id');
            $data = $model->updateHearingWeekTemplateModel($data,$hearing_id);
            $response = $response->withJson($data);
            return $response;
        }
    }

    /*
    * Удаление услуги
    * Return: JSON
    */
    public function actionServiceDelete(Request $request, Response $response)
    {
        $model = $this->TopologyModel;
        $hearing_id = $request->getAttribute('hearing_id');
        $data = $model->deleteHearingTopologyModel($hearing_id);
        $response = $response->withJson($data);
        return $response;
    }

    /*
    * Привязка сотрудника из панельки аккаунта
    * Return: JSON
    */
    public function actionAccountLinkWorkerToRoom(Request $request, Response $response)
    {
        $model = $this->TopologyModel;
        $hearing_id = $request->getAttribute('hearing_id');
        $data = $model->deleteHearingTopologyModel($hearing_id);
        $response = $response->withJson($data);
        return $response;
    }

    /*
         * Создание группы/помещения
         * Return: JSON
         */
    public function addTopologyObjectControl(Request $request, Response $response)
    {
        $type = $request->getAttribute('type');
        $data = $request->getParsedBody();
        $data = $this->model->addTopologyObjectModel($data, $type);
        $response = $response->withJson($data);
        return $response;
    }

    /*
     * Вывод формы добавления группы/помещения
     * Return: HTML
     */
    public function showAddTopologyObjectView($type = null)
    {

        $category = $this->model->getRoomModel(null, 'category');
        $workers = $this->model->getWorkersModel();
        $sections = $this->model->getFilialDepartmentModel(null, 'section');

        $HTML = "";
        $HTML .= "<h2>Добавление группы/помещения</h2>";
        $HTML .= "<form name='addRoomsForm'>";
        $HTML .= "<table><tr><td>Название</td><td><input class='margins' type='text' name='name'></td></tr>";
        //Если это добавление кабинета
        if ($type == 'room') $HTML .= "<tr><td>Номер</td><td><input class='margins' type='number' name='number'></td></tr>";
        $HTML .= "<tr><td>Ответственный сотрудник</td><td><select class='margins' name='worker_id'>";
        if (isset($workers['status'])) {
            $HTML .= "<option value='0'>Нет данных</option>";
        } else {
            $HTML .= "<option value='0'>Не выбран ответственный сотрудник</option>";
            for ($i = 0; $i < count($workers); $i++) {
                $HTML .= "<option value='{$workers[$i]->id}'>{$workers[$i]->first_name} {$workers[$i]->patronymic} {$workers[$i]->surname}</option>";
            }
        }
        $HTML .= "</select></td></tr>";

        $HTML .= "<tr><td>Помещение-родитель</td><td><select class='margins' name='parent_id'>";
        if (isset($category['status'])) {
            $HTML .= "<option value='0'>Нет данных</option>";
        } else {
            $HTML .= "<option value='0'>Не выбрана категория</option>";
            for ($i = 0; $i < count($category); $i++) {
                $HTML .= "<option value='{$category[$i]->id}'>{$category[$i]->name}</option>";
            }
        }
        $HTML .= "</select></td></tr>";
        $HTML .= "<tr><td>Отдел</td><td><select class='margins' name='department_id'>";
        if (isset($sections['status'])) {
            $HTML .= "<option value='0'>Нет данных</option>";
        } else {
            $HTML .= "<option value='0'>Нет выбран отдел</option>";
            for ($i = 0; $i < count($sections); $i++) {
                $HTML .= "<option value='{$sections[$i]->id}'>{$sections[$i]->name}</option>";
            }
        }
        $HTML .= "</select></td></tr></table>";
        $HTML .= "</form>";
        if ($type == 'room') $HTML .= "<div class='button margins' onclick=\"sendAjax('/topology/add/{$type}/', 'POST', 'addRoomsForm')\">Создать помещение</div>";
        if ($type == 'category') $HTML .= "<div class='button' onclick=\"sendAjax('/topology/add/{$type}/', 'POST', 'addRoomsForm')\">Создать группу</div>";
        return $HTML;
    }


    public
    function printTopologyView($level = 0, $topology = null, $type = 0, $search = false, $param = null, $value = null, $mainFindSearch = false)
    {
        $shablons = [];
        switch ($type) {
            case 0 :
                $shablons['onclickTopologyUrl'] = "sendAjax('/topology/show/{topology_id}/category/null/', 'GET'); event.stopPropagation();";
                $shablons['onclickTopologyEditUrl'] = "sendAjax('/topology/edit/form/{topology_id}/category/', 'GET'); event.stopPropagation();";
                $shablons['onclickDepartamentUrl'] = "sendAjax('/topology/show/{departament_id}/department/{topologry_id}/', 'GET'); event.stopPropagation();";
                $shablons['onclickDepartamentEditUrl'] = "sendAjax('/filial-departments/form/{departament_id}/department/', 'GET'); event.stopPropagation();";
                $shablons['onclickSubDepartamentUrl'] = "sendAjax('/topology/show/{departament_id}/department/{topology_id}/', 'GET'); event.stopPropagation();";
                $shablons['onclickSubDepartamentEditUrl'] = "sendAjax('/filial-departments/form/{departament_id}/section/', 'GET'); event.stopPropagation();";
                $shablons['onclickCabinetUrl'] = '';
                $shablons['onclickCabinetUrlEdit'] = '';
                $shablons['onclickWorkerUrl'] = '';
                $shablons['onclickHearingUrl'] = '';
                $shablons['all'] = 0;

                break;

            case 1 :
                $shablons['onclickTopologyUrl'] = "sendAjax('/workschedule/showbutton/{topology_id}/category/null/', 'GET'); event.stopPropagation();";
                $shablons['onclickTopologyEditUrl'] = "sendAjax('/topology/edit/form/{topology_id}/category/', 'GET'); event.stopPropagation();";
                $shablons['onclickDepartamentUrl'] = "sendAjax('/workschedule/showbutton/{departament_id}/department/{topologry_id}/', 'GET'); event.stopPropagation();";
                $shablons['onclickDepartamentEditUrl'] = "sendAjax('/filial-departments/form/{departament_id}/department/', 'GET'); event.stopPropagation();";
                $shablons['onclickSubDepartamentUrl'] = "sendAjax('/workschedule/showbutton/{departament_id}/section/{topology_id}/', 'GET'); event.stopPropagation();";
                $shablons['onclickSubDepartamentEditUrl'] = "sendAjax('/filial-departments/form/{departament_id}/section/', 'GET'); event.stopPropagation();";
                $shablons['onclickCabinetUrl'] = "sendAjax('/workschedule/showbutton/{room_id}/room/{departament_id}/', 'GET'); event.stopPropagation();";
                $shablons['onclickCabinetUrlEdit'] = "sendAjax('/topology/edit/form/{room_id}/room/', 'GET'); event.stopPropagation();";
                $shablons['onclickWorkerUrl'] = "sendAjax('/workschedule/showbutton/{worker_id}/worker/{departament_id}/', 'GET'); event.stopPropagation();";
                $shablons['onclickHearingUrl'] = "sendAjax('/workschedule/showbutton/{hearing_id}/hearing/{departament_id}/', 'GET'); event.stopPropagation();";
                $shablons['all'] = 1;
                break;

            default:
                $shablons['onclickTopologyUrl'] = '';
                $shablons['onclickTopologyEditUrl'] = '';
                $shablons['onclickDepartamentUrl'] = '';
                $shablons['onclickDepartamentEditUrl'] = '';
                $shablons['onclickSubDepartamentUrl'] = '';
                $shablons['onclickSubDepartamentEditUrl'] = '';
                $shablons['onclickCabinetUrl'] = '';
                $shablons['onclickCabinetUrlEdit'] = '';
                $shablons['onclickWorkerUrl'] = '';
                $shablons['onclickWorkerUrlEdit'] = '';
                $shablons['onclickHearingUrl'] = '';
                $shablons['all'] = 0;
                break;
        }

        $HTML = "";
        if ($level == 0) $HTML .= "<div id='topologyHiddenForm' class='hiddenFormDiv''></div>";

        if ($param == null || $value == null) $search = false;
        for ($i = 0; $i < count($topology); $i++) {
            $subHTML = "";
            if (!$search) $findSearch = true;
            else $findSearch = $mainFindSearch;
            $onclickTopology = str_replace('{topology_id}', $topology[$i]->id, $shablons['onclickTopologyUrl']);
            $onclickTopologyEdit = str_replace('{topology_id}', $topology[$i]->id, $shablons['onclickTopologyEditUrl']);

            $style = ($level == 0 || $search) ? "" : "style='display: none;'";
            $buttonPlusMinus = (($level == 0 && !$search) || !$search) ? "+" : "-";
            if ($search && (strpos($topology[$i]->name, $value) !== false || $findSearch)) {
                $findSearch = true;
            }

            $subHTML .= "<div class='topology_item' {$style} id='main_department_{$topology[$i]->id}' onclick=\"activeTopologyItem('#main_department_{$topology[$i]->id}', '.topology_item_name', 'active', {$shablons['all']}); {$onclickTopology}\">";
            $subHTML .= "<div class='topology_item_name'>";
            if (isset($topology[$i]->sub) || isset($topology[$i]->departaments))
                $subHTML .= "<button onclick='divSlide(this, \"#main_department_{$topology[$i]->id}\", \".topology_item\", false); event.stopPropagation();'>{$buttonPlusMinus}</button>";
            $subHTML .= "{$topology[$i]->name}";
            $subHTML .= "<div class='topology_submenu'>";
            $subHTML .= "<div class='topology_menu_icon'></div>";
            $subHTML .= "<div class='topology_menu'>";
            $subHTML .= "<div onclick=\"{$onclickTopologyEdit}\">Редактировать</div>";
            $subHTML .= "<div onclick=\"sendAjax('/topology/{$topology[$i]->id}/get/form/add/subtopology/', 'GET');\">Добавить подкатегорию</div>";
            $subHTML .= "<div onclick=\"sendAjax('/topology/{$topology[$i]->id}/get/form/add/departament/', 'GET');\">Добавить департамент</div>";
            $subHTML .= "<div><a href='" . base_path() . "workschedule/show/{$topology[$i]->id}/category/null/'>График работ</a></div>";
            $subHTML .= "<div onclick=\"sendAjax('/topology/{$topology[$i]->id}/delete/floor/', 'POST');\">Удалить</div>";
            $subHTML .= '</div>';
            $subHTML .= '</div>';
            $subHTML .= '</div>';
            $subHTML .= "<div id='topologyHiddenForm_{$topology[$i]->id}' class='hiddenFormDiv'></div>";


            if ($topology[$i]->departaments != null) {
                $sublevel = 0;
                for ($j = 0; $j < count($topology[$i]->departaments); $j++) {
                    //Проверяем тип сущности

                    if (!$topology[$i]->departaments[$j]->group) {
                        //Департамент
                        //Выодим Департамент/Отдел
                        $onclickDepartament = str_replace('{departament_id}', $topology[$i]->departaments[$j]->id, $shablons['onclickDepartamentUrl']);
                        $onclickDepartament = str_replace('{topologry_id}', $topology[$i]->id, $onclickDepartament);
                        $onclickDepartamentEdit = str_replace('{departament_id}', $topology[$i]->departaments[$j]->id, $shablons['onclickDepartamentEditUrl']);

                        $sublevel++;
                        $style = ($search) ? '' : "style='display: none;'";
                        $buttonPlusMinus = ($search) ? "-" : "+";

                        if ($search && (strpos($topology[$i]->departaments[$j]->name, $value) !== false || $findSearch)) {
                            $findSearch = true;
                        }

                        $subHTML .= "<div class='topology_item' {$style} id='department_{$topology[$i]->id}_{$topology[$i]->departaments[$j]->id}' onclick=\"activeTopologyItem('#department_{$topology[$i]->id}_{$topology[$i]->departaments[$j]->id}', '.topology_item_name', 'active', {$shablons['all']}); {$onclickDepartament}\">";
                        $subHTML .= "<div class='topology_item_name'>";

                        if (isset($topology[$i]->departaments[$j]->sub))
                            $subHTML .= "<button onclick='divSlide(this, \"#department_{$topology[$i]->id}_{$topology[$i]->departaments[$j]->id}\", \".topology_item\", false); event.stopPropagation();'>{$buttonPlusMinus }</button>";

                        $subHTML .= "Департамент: {$topology[$i]->departaments[$j]->name}";
                        $subHTML .= "<div class='topology_submenu'>";
                        $subHTML .= "<div class='topology_menu_icon'></div>";
                        $subHTML .= "<div class='topology_menu'>";
                        $subHTML .= "<div id='menu_work_schedule_department_{$topology[$i]->departaments[$j]->id}'>
                    <a href='" . base_path() . "workschedule/show/{$topology[$i]->departaments[$j]->id}/department/{$topology[$i]->id}/'>График работ</a></div>";
                        $subHTML .= "<div onclick=\"sendAjax('/topology/{$topology[$i]->id}/{$topology[$i]->departaments[$j]->id}/get/form/add/subdepartment/', 'GET');\">Добавить отдел</div>";
                        $subHTML .= "<div id='menu_edit_department_{$topology[$i]->departaments[$j]->id}' onclick=\"{$onclickDepartamentEdit}\">Редактировать</div>";
                        $subHTML .= "<div id='menu-delete-section-{$topology[$i]->id}-{$topology[$i]->departaments[$j]->id}' onclick=\"sendAjax('/topology/{$topology[$i]->id}/{$topology[$i]->departaments[$j]->id}/delete/departament/', 'POST');\">Удалить</div>";
                        $subHTML .= "</div>";
                        $subHTML .= "</div>";
                        $subHTML .= "</div>";

                        $subHTML .= "<div class='hiddenFormDiv' id='topologyHiddenForm_departament_{$topology[$i]->id}_{$topology[$i]->departaments[$j]->id}'></div>";
                        if (isset($topology[$i]->departaments[$j]->sub)) {
                            for ($k = 0; $k < count($topology[$i]->departaments[$j]->sub); $k++) {
                                //Вывод отделов
                                $subDepartamentHTML = $this->printfTopologySubdepartment($level, $sublevel, $shablons, $topology[$i]->id, $topology[$i]->departaments[$j]->sub[$k], 1, $search, $param, $value, $findSearch);
                                if ($search && $subDepartamentHTML != "") $findSearch = true;
                                $subHTML .= $subDepartamentHTML;
                            }
                        }
                        $subHTML .= "</div>";
                        $sublevel--;

                    } else {
                        //Вывод отдела
                        $subDepartamentHTML = $this->printfTopologySubdepartment($level, $sublevel, $shablons, $topology[$i]->id, $topology[$i]->departaments[$j], $search, $param, $value, $findSearch);
                        if ($search && $subDepartamentHTML != "") $findSearch = true;
                        $subHTML .= $subDepartamentHTML;
                    }
                }
            }
            if (isset($topology[$i]->sub)) {
                $subDepartamentHTML = $this->printTopologyView(($level + 1), $topology[$i]->sub, $type, $search, $param, $value, $findSearch);
                if ($search && $subDepartamentHTML != "") $findSearch = true;
                $subHTML .= $subDepartamentHTML;
            }
            $subHTML .= "</div>";
            if ($findSearch) $HTML .= $subHTML;
        }
        return $HTML;
    }


//    TODO: Все что ниже прокомментировать и перенести вьюхи куда положено
//    TODO: Модели прошерстить.
    public
    function topologyGetFormAddTopology(Request $request, Response $response)
    {
        $data = [];
        $data['status'] = 'success';
        $data['html'] = tpl('topology/add-topology/form', $data);
        $data['div'] = "topologyHiddenForm";
        $response = $response->withJson($data);
        return $response;
    }

    public function topologyAddTopology(Request $request, Response $response)
    {
        $model = $this->TopologyModel;
        $data = $request->getParsedBody();
        $data['status'] = 'success';
        $data['floor_id'] = $request->getAttribute('floor_id');
        $data = $model->checkAddFloor($data);
        $response = $response->withJson($data);
        return $response;
    }

    public function topologyAddWorkerControl(Request $request, Response $response)
    {
        $model = $this->TopologyModel;
        $data = $request->getParsedBody();
        $data['status'] = 'success';
        $data['room_id'] = $request->getAttribute('room_id');
        $data = $model->addWorkerToRoom($data);
        $response = $response->withJson($data);
        return $response;
    }

//    TODO: Модели прошерстить.


    public
    function topologySearchDepartamentByName(Request $request, Response $response)
    {
        $id = $request->getAttribute('id');
        $name = $request->getAttribute('name');
        $data = [];
        $data['id'] = $id;
        $data['name'] = $name;
        $departamentModel = $this->FilialDepartmentModel;
        $data['departaments'] = $departamentModel->getDepartamentsByName($name);
        $data['html'] = tpl('topology/add-departament/search', $data);
        $data['status'] = 'success';
        $data['div'] = "searchDepartamentResult_topology_{$id}";
        $response = $response->withJson($data);
        return $response;
    }


    public
    function topologyGetFormAddSubdepartament(Request $request, Response $response)
    {
        $floor_id = $request->getAttribute('floor_id');
        $departament_id = $request->getAttribute('departament_id');
        $data = [];
        $data['floor_id'] = $floor_id;
        $data['departament_id'] = $departament_id;
        $data['html'] = tpl('topology/add-subdepartament/form', $data);
        $data['status'] = 'success';
        $data['div'] = "popup";
        $response = $response->withJson($data);
        return $response;
    }

    public
    function topologySearchSubdepartamentByName(Request $request, Response $response)
    {
        $floor_id = $request->getAttribute('floor_id');
        $departament_id = $request->getAttribute('departament_id');
        $name = $request->getAttribute('name');
        $data = [];
        $data['floor_id'] = $floor_id;
        $data['departament_id'] = $departament_id;
        $data['name'] = $name;
        $departamentModel = $this->FilialDepartmentModel;
        $data['departaments'] = $departamentModel->getDepartamentsByName($name, $departament_id);
        $data['html'] = tpl('topology/add-subdepartament/search', $data);
        $data['status'] = 'success';
        $data['div'] = "searchSubdepartamentResult_topology_{$floor_id}_department_{$departament_id}";
        $response = $response->withJson($data);
        return $response;
    }

    public
    function topologySearch(Request $request, Response $response)
    {
        $model = $this->TopologyModel;
        $data = $request->getParsedBody();
        $topology = $model->getTopologyModel();
        $topologytype = $request->getAttribute('topologytype');

        $data['status'] = 'success';
        $data['div'] = 'leftopology';
        $data['html'] = $this->view->printTopologyView(0, $topology, $topologytype, $search = true, 'name', $data['name']);

        $response = $response->withJson($data);
        return $response;
    }

    public
    function topologyAddSubdepartament(Request $request, Response $response)
    {
        $model = $this->TopologyModel;
        $floor_id = $request->getAttribute('floor_id');
        $data = $request->getParsedBody();
        $data['parent_id'] = $request->getAttribute('departament_id');
        $data['group'] = true;
        $data = $model->checkAddFilialDepartamentToFloor($floor_id, $data);
        $response = $response->withJson($data);
        return $response;
    }


    public
    function topologyAddRoom(Request $request, Response $response)
    {

    }


    /*
     * Создание услуги
     * Return: JSON
     */
    public function addHearingTopologyControl(Request $request, Response $response)
    {
        $model = $this->TopologyModel;
        $room_id = $request->getAttribute('room_id');
        $worker_id = $request->getAttribute('worker_id');
        $data = $request->getParsedBody();
        $data = $model->addHearingTopologyModel($data, $room_id, $worker_id);
        $response = $response->withJson($data);
        return $response;
    }

    /*
    * Сделать услугу публичной/закрытой
    * Return: JSON
    */

    public
    function makeHearingPublicControl(Request $request, Response $response)
    {
        $model = $this->TopologyModel;
        $hearing_id = $request->getAttribute('hearing_id');
        $status = $request->getAttribute('status');
        $data = $model->makeHearingPublicModel($hearing_id, $status);
        $response = $response->withJson($data);
        return $response;
    }


    /*
    * Вывод блока вложенной топологии
    * Return: JSON
    */
    public
    function showInsideTopologyControl(Request $request, Response $response)
    {
        $id = $request->getAttribute('id');
        $type = $request->getAttribute('type');
        $category = $request->getAttribute('category');
        $html = $this->view->showInsideTopologyView($id, $type, $category);
        $data['status'] = 'success';
        $data['div'] = 'popup';
        $data['html'] = $html;
        $response = $response->withJson($data);
        return $response;
    }


    /*
     * Вывод формы изменения добавления группы/помещения
     * Return: JSON
     */
    public
    function showChangeTopologyObjectControl(Request $request, Response $response)
    {

        $room_id = $request->getAttribute('room_id');
        $type = $request->getAttribute('type');
        $html = $this->showChangeTopologyObjectView($room_id, $type);
        $data['status'] = 'success';
        $data['div'] = 'popup';
        $data['html'] = $html;
        $response = $response->withJson($data);
        return $response;
    }

    /*
     * Вывод окна топологии
     * Return: JSON
     */
    public
    function showTopologyControl(Request $request, Response $response)
    {
        $html = $this->view->showTopologyView();
        $data['status'] = 'success';
        $data['div'] = 'topology';
        $data['html'] = $html;
        $response = $response->withJson($data);
        return $response;
    }


}
