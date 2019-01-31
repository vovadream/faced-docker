<?php

namespace App\Controllers;

use \Slim\Container;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Slim\Http\Response as Response;


use App\Controllers\HttpClientController;
use App\Models\InterfaceModel;
use App\Models\EquipmentModel;
use App\Views\InterfaceView;

class InterfaceController extends Controller
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
     * Главная страница
     */

    public function createWorkerInterface(Request $request, Response $response, $args = [])
    {
        $response = $this->createInterfaceControl($request);
        return $response;
    }



    /*
     * AJAX без параметра
     */
    /*
        function ajaxWithoutParam(Request $request, Response $response)
        {
            $name = $request->getAttribute('name');
            $data = $request->getParsedBody();
            $response = $this->checkAjaxControl($name, $data);
            return $response;
        }
    */

    /*
     * AJAX с параметром
     */
    /*
    function ajaxWithParam(Request $request, Response $response)
    {
        $name = $request->getAttribute('name');
        $param = $request->getAttribute('param');
        $data = $request->getParsedBody();
        $response = $this->checkAjaxControl($name, $data, $param);
        return $response;
    }
    */

    /*
     * Построение интерфейса
     */

    public function createInterfaceControl(Request $request)
    {
        $HTML = tpl('header');
        $HTML .= $this->checkAuthControl($request);
        $HTML .= tpl('footer');
        return $HTML;
    }


    /*
     * Проверка авторизации пользователя и вывод нужного интерфейса
     * TODO: создать обработчик запрашиваемого интерфейса
     */
    public function checkAuthControl(Request $request)
    {
        $url = $request->getUri()->getPath();
        $HTML = "";
        //$HTML .= "future interface";
        //$HTML .= " ({$url})";
        if ((!isset($_SESSION['id'])) OR ($_SESSION['id'] == null)) {
            //Если нет сессии - выводим форму авторизации
            $HTML .= tpl('auth');
        } else {
            //Если сессия имеется - выводим интерфейсы
            if ($url != null) {

                $HTML .= "<div id='content'>";
                switch ($url) {
                    //Главная
                    case "/" :
                        $HTML .= "<div>Главная страница</div>";
                        break;

                    //Выход
                    case "/logout/" :
                        $HTML .= "<div>Страница выхода</div>";
                        break;

                    //Настройки
                    case "/configs/" :
                        $HTML .= $this->view->getSettingsView();
                        break;

                    //Главная (мониторинг)
                    case "/main/" :
                        $HTML .= $this->view->mainView();
                        break;

                    //Аккаунты
                    case "/accounts/" :
                        $HTML .= $this->view->accountsView();
                        break;

                    //Аккаунты
                    case (preg_match('/.account.[0-9]{0,15}./', $url) ? true : false):
                        $id = $request->getAttribute('id');
                        $HTML .= $this->view->accountView($id);
                        break;


                    //Контроль доступа
                    case "/access-control/" :
                        $HTML .= $this->view->accessControlView();
                        break;

                    //Статистика
                    case "/statistic/" :
                        $HTML .= $this->view->statisticView();
                        break;

                    case "/topology/topologyadv/" :
                        $HTML .= $this->view->topologyView();
                        break;

                    case "/workschedule/" :
                        $HTML .= $this->view->workScheduleView();
                        break;

                    default :
                        $HTML .= "<div>Другие страницы</div>";
                }
                //$HTML .= "future interface";
                //$HTML .= " ({$url})";
                $HTML .= "</div>";
            }
        }
        return $HTML;
    }



// <--- ПОЛЬЗОВАТЕЛЬ


    /*
     * Авторизация пользователя
     * Return: JSON
     */

    public function authUserControl(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        $data = $this->model->authUserModel($data);
        $response = $response->withJson($data);
        return $response;
    }

    /*
     * Деавторизация пользователя
     * Return: JSON
     */
    public function logoutUserControl(Request $request, Response $response)
    {
        $data = $this->model->logoutUserModel();
        $response = $response->withJson($data);
        return $response;
    }


// ПОЛЬЗОВАТЕЛЬ --->


// <--- ИНТЕРФЕЙС

    /*
     * Вывод формы создания интерфейсов
     * Return: HTML
     */
    public function showAddInterfaceFormControl(Request $request, Response $response)
    {
        $response = $this->view->showAddInterfaceFormView();
        return $response;
    }

    public function showChangeInterfaceFormControl(Request $request, Response $response)
    {
        $id = $request->getAttribute('id');
        $response = $this->view->showChangeInterfaceFormView($id);
        return $response;
    }

    /*
     * Вывод формы изменения интерфейса
     * Return: HTML
     */
    public function addInterfaceControl(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        $data = $this->model->addInterfaceModel($data);
        $response = $response->withJson($data);
        return $response;
    }

    /*
     * Изменение интерфейса
     * Return: JSON
     */
    public function updateInterfaceControl(Request $request, Response $response)
    {
        $id = $request->getAttribute('id');
        $data = $request->getParsedBody();
        $data = $this->model->updateInterfaceModel($data, $id);
        $response = $response->withJson($data);
        return $response;
    }

// ИНТЕРФЕЙС --->

// <--- МЕТКИ

    /*
     * Вывод формы создания метки
     * Return: HTML
     */
    public function showAddMarkFormControl(Request $request, Response $response)
    {
        $response = $this->view->showAddMarkFormView();
        return $response;
    }

    /*
     * Создание метки:
     * Return: JSON
     */
    public function addMarkControl(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        $data = $this->model->addMarkModel($data);
        $response = $response->withJson($data);
        return $response;
    }

    /*
     * Вывод формы изменения метки
     * Return: HTML
     */
    public function showUpdateMarkFormControl(Request $request, Response $response)
    {
        $id = $request->getAttribute('id');
        $response = $this->view->showChangeMarkFormView($id);
        return $response;
    }

    /*
     * Изменение метки
     * Return: JSON
     */
    public function updateMarkControl(Request $request, Response $response)
    {
        $id = $request->getAttribute('id');
        $data = $request->getParsedBody();
        $data = $this->model->updateMarkModel($data, $id);
        $response = $response->withJson($data);
        return $response;
    }

// МЕТКИ --->

// <--- ТИПЫ ДОКУМЕНТОВ

    /*
     * Добавление типа документа
     * Return: JSON
     */
    public function addUserDocumentTypeControl(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        $data = $this->model->addUserDocumentTypeModel($data);
        $response = $response->withJson($data);
        return $response;
    }

    /*
     * Изменение типа документов
     */
    public function updateUserDocumentTypeControl(Request $request, Response $response)
    {
        $id = $request->getAttribute('id');
        $data = $request->getParsedBody();
        $data = $this->model->updateUserDocumentTypeModel($data, $id);
        $response = $response->withJson($data);
        return $response;
    }

    /*
     * Вывод формы изменения типа документов
     * Return: JSON
     */
    public function showChangeUserDocumentTypeControl(Request $request, Response $response)
    {
        $id = $request->getAttribute('id');
        $response = $this->view->showChangeUserDocumentTypeView($id);
        return $response;
    }

    /*
     * Вывод формы добавления типа документов
     * Return: JSON
     */
    public function showAddUserDocumentTypeFormControl(Request $request, Response $response)
    {
        $response = $this->view->showAddUserDocumentTypeFormView();
        return $response;
    }

// ТИПЫ ДОКУМЕНТОВ --->

// <--- ОТДЕЛЫ ФИЛИАЛА



















// ОТДЕЛЫ ФИЛИАЛА --->

// <--- ТИПЫ ПОЛЬЗОВАТЕЛЕЙ

    /*
     * Изменение типа пользователя
     * Return: JSON
     */
    public function updateUserTypeControl(Request $request, Response $response)
    {
        $id = $request->getAttribute('id');
        $data = $request->getParsedBody();
        $data = $this->model->updateUserTypeModel($data, $id);
        $response = $response->withJson($data);
        return $response;
    }

    /*
     * Добавление типа пользователя
     * Return: JSON
     */
    public function addUserTypeControl(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        $data = $this->model->addUserTypeModel($data);
        $response = $response->withJson($data);
        return $response;
    }

    /*
     * Вывод формы добавления типа пользователя
     * Return: HTML
     */
    public function showAddUserTypeControl(Request $request, Response $response)
    {
        $response = $this->view->showAddUserTypeView();
        return $response;
    }

    /*
     * Вывод формы изменения типа пользователя
     * Return: HTML
     */
    public function showChangeUserTypeControl(Request $request, Response $response)
    {
        $id = $request->getAttribute('id');
        $response = $this->view->showChangeUserTypeView($id);
        return $response;
    }

// ТИПЫ ПОЛЬЗОВАТЕЛЕЙ --->

// <--- ПОМЕЩЕНИЯ ФИЛИАЛА

    /*
     * Добавление помещения в филиал
     * Return: JSON
     */
    public function addRoomControl(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        $data = $this->model->addRoomModel($data);
        $response = $response->withJson($data);
        return $response;
    }

    /*
     * Обновления помещения в филиале
     * Return: JSON
     */
    public function updateRoomControl(Request $request, Response $response)
    {
        $id = $request->getAttribute('id');
        $data = $request->getParsedBody();
        $data = $this->model->updateRoomModel($data, $id);
        $response = $response->withJson($data);
        return $response;
    }

    /*
     * Вывод формы изменения помещения
     * Return: HTML
     */
    public function showChangeRoomControl(Request $request, Response $response)
    {
        $id = $request->getAttribute('id');
        $response = $this->view->showChangeRoomView($id);
        return $response;
    }

    /*
     * Вывод формы добавления помещения
     * Return: HTML
     */
    public function showAddRoomsControl(Request $request, Response $response)
    {
        $response = $this->view->showAddRoomsView();
        return $response;
    }

// ПОМЕЩЕНИЯ ФИЛИАЛА --->


// <--- ТИПЫ ОБОРУДОВАНИЯ

    /*
     * Добавление типа оборудования
     * Return: JSON
     */
    public function addEquipmentTypeControl(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        $data = $this->model->addEquipmentTypeModel($data);
        $response = $response->withJson($data);
        return $response;
    }

    /*
     * Обновление типа оборудования
     * Return: JSON
     */
    public function updateEquipmentTypeControl(Request $request, Response $response)
    {
        $id = $request->getAttribute('id');
        $data = $request->getParsedBody();
        $data = $this->model->updateEquipmentTypeModel($data, $id);
        $response = $response->withJson($data);
        return $response;
    }

    /*
     * Отобразить форму изменения типа оборудования
     * Return: HTML
     */
    public function showChangeEquipmentTypeControl(Request $request, Response $response)
    {
        $id = $request->getAttribute('id');
        $response = $this->view->showChangeEquipmentTypeView($id);
        return $response;
    }

    /*
     * Отобразить форму добавления типов оборудования
     * Return: HTML
     */
    public function showAddEquipmentTypeControl(Request $request, Response $response)
    {
        $response = $this->view->showAddEquipmentTypeView();
        return $response;
    }

// ТИПЫ ОБОРУДОВАНИЯ --->

// <--- ПОЛЬЗОВАТЕЛИ

    /*
     * Добавление пользователя в филиал
     * Return: JSON
     */
    public function addUserControl(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        $data = $this->model->addUserModel($data);
        $response = $response->withJson($data);
        return $response;
    }

    /*
     * Обновление данных пользователя
     * Return: JSON
     */
    public function updateUserControl(Request $request, Response $response)
    {
        $id = $request->getAttribute('id');
        $data = $request->getParsedBody();
        $data = $this->model->updateUserModel($data, $id);
        $response = $response->withJson($data);
        return $response;
    }

    /*
     * Вывод формы изменения пользователя
     * Return: HTML
     */
    public function showChangeUserControl(Request $request, Response $response)
    {
        $id = $request->getAttribute('id');
		$users = $this->model->getUsersModel($id);
		$data['user_types'] = $this->model->getUserTypesModel();
		$data['users']=$this->model->getUsersModel($id);
		if ($users[0]->user_type_id==1||$users[0]->main_class==1)
		{
		$data['departments']=$this->model->getFilialDepartmentModel(null, 'section');
		}
        $HTML = tpl('users/changeUserForm',$data);
		$data=[];
        $data['status'] = 'success';
        $data['div'] = 'popup';
        $data['html'] = $HTML;
        $response = $response->withJson($data);
        return $response;
    }

    /*
     * Вывод формы добавления пользователя
     * Return: HTML
     */
    public function showAddUserControl(Request $request, Response $response)
    {
        $HTML = tpl('users/addUserForm');
        $data['status'] = 'success';
        $data['div'] = 'popup';
        $data['html'] = $HTML;
        $response = $response->withJson($data);
        return $response;
    }

// ПОЛЬЗОВАТЕЛИ--->

// <--- СОТРУДНИКИ

    /*
     * Добавление сотрудника в филиал
     * Return: JSON
     */
    public function addWorkerControl(Request $request, Response $response)
    {
        $id = $request->getAttribute('id');
        $data = $request->getParsedBody();
        $data = $this->model->addWorkerModel($data, $id);
        $response = $response->withJson($data);
        return $response;
    }

    /*
     * Обновление данных сотруднка
     * Return: JSON
     */
    public function updateWorkerControl(Request $request, Response $response)
    {
        $id = $request->getAttribute('id');
        $data = $request->getParsedBody();
        $data = $this->model->updateWorkerModel($data, $id);
        $response = $response->withJson($data);
        return $response;
    }

    /*
     * Вывод формы изменения сотрудника
     * Return: HTML
     */
    public function showChangeWorkerControl(Request $request, Response $response)
    {
        $id = $request->getAttribute('id');
        $response = $this->view->showChangeWorkerView($id);
        return $response;
    }

    /*
     * Вывод формы добавления сотрудника
     * Return: HTML
     */
    public function showAddWorkerControl(Request $request, Response $response)
    {
        $id = $request->getAttribute('id');
        $html = $this->view->showAddWorkerView($id);
        $data['status'] = 'success';
        $data['div'] = 'popup';
        $data['html'] = $html;
        $response = $response->withJson($data);
        return $response;
    }

// СОТРУДНИКИ --->


// <--- СЛУШАНИЯ

    /*
     * Добавление слушания
     * Return: JSON
     */
    public function addHearingControl(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        $data = $this->model->addHearingModel($data);
        $response = $response->withJson($data);
        return $response;
    }






// СЛУШАНИЯ --->

// <--- ДОСТУП ПОЛЬЗОВАТЕЛЕЙ НА ТЕРРИТОРИЮ







// ДОСТУП ПОЛЬЗОВАТЕЛЕЙ НА ТЕРРИТОРИЮ --->

// <--- ПРОХОДЫ НА ТЕРРИТОРИЮ



//  ПРОХОДЫ НА ТЕРРИТОРИЮ--->

    /*
         * Вывод увеличенной таблицы проходов
         * Return: HTML
         */
    public function showBigUserPassControl(Request $request, Response $response)
    {
        $type = $request->getAttribute('type');
        $filter = $request->getAttribute('filter');
        $html = $this->view->showBigUserPassView($type,$filter);
        $data['status'] = 'success';
        $data['div'] = 'popup';
        $data['html'] = $html;
        $response = $response->withJson($data);
        return $response;
    }

    /*
     * Вывод подробной информации о записе в таблице входа
     * Return: HTML
     */
    public function showInBigUserPassControl(Request $request, Response $response)
    {
        $user_id = $request->getAttribute('user_id');
        $html = $this->view->showInBigUserPassView($user_id);
        $data['status'] = 'success';
        $data['div'] = 'tableInUserInfo';
        $data['html'] = $html;
        $response = $response->withJson($data);
        return $response;
    }

    /*
     * Вывод подробной информации о записе в таблице выхода
     * Return: HTML
     */
    public function showOutBigUserPassControl(Request $request, Response $response)
    {
        $user_id = $request->getAttribute('user_id');
        $html = $this->view->showOutBigUserPassView($user_id);
        $data['status'] = 'success';
        $data['div'] = 'tableOutUserInfo';
        $data['html'] = $html;
        $response = $response->withJson($data);
        return $response;
    }



// <--- ДОСТУП К ОТДЕЛАМ

    /*
     * Разрешение доступа к отделам
     * Return: JSON
     */
    public function addWorkerDepartmentAccessControl(Request $request, Response $response)
    {
        $worker_id = $request->getAttribute('worker_id');
        $department_id = $request->getAttribute('department_id');
        $status = $request->getAttribute('status');
        $data = $this->model->addWorkerDepartmentAccessModel($worker_id, $department_id,$status);
        $response = $response->withJson($data);
        return $response;
    }

    /*
     * Запрет доступа к отделам
     * Return: JSON

    public function updateWorkerDepartmentAccessControl(Request $request, Response $response)
    {
        $id = $request->getAttribute('id');
		$status = $request->getAttribute('status');
        $data = $this->model->updateWorkerDepartmentAccessModel($id,$status);
        $response = $response->withJson($data);
        return $response;
    }
	*/

//  ДОСТУП К ОТДЕЛАМ--->

// <--- ДОСТУП К ПОМЕЩЕНИЯМ

    /*
     * Разрешение доступа к помещениям
     * Return: JSON
     */
    public function addWorkerPermissionAccessControl(Request $request, Response $response)
    {
        $worker_id = $request->getAttribute('worker_id');
        $room_id = $request->getAttribute('room_id');
        $data = $this->model->addWorkerPermissionAccessModel($worker_id, $room_id );
        $response = $response->withJson($data);
        return $response;
    }

    /*
     * Запрет доступа к помещениям
     * Return: JSON
     */
    public function updateWorkerPermissionAccessControl(Request $request, Response $response)
    {
        $id = $request->getAttribute('id');
        $status = $request->getAttribute('status');
        $data = $this->model->updateWorkerPermissionAccessModel($id,$status);
        $response = $response->withJson($data);
        return $response;
    }

//  ДОСТУП К ПОМЕЩЕНИЯМ--->



    // <--- ТОПОЛОГИЯ
    // TODO:Оставил функции которые сейчас закоментированы, разобраться, что нужно




	/*
	 * Отображение формы со всеми свободными сотрудниками
	 * Return: JSON
	 */
    public function showAllWorkersTopologyControl(Request $request, Response $response)
    {
        $room_id = $request->getAttribute('room_id');
		$department_id = $request->getAttribute('department_id');
		$html = $this->view->showAddWorkerTopologyAllWorkersView($room_id, $department_id);
        $data['status'] = 'success';
        $data['div'] = 'addworkertopology_allworkers';
        $data['html'] = $html;
        $response = $response->withJson($data);
        return $response;
    }

	/*
	 * Отображение формы со всеми занятыми сотрудниками
	 * Return: JSON
	 */
    public function showBusyWorkersTopologyControl(Request $request, Response $response)
    {
        $room_id = $request->getAttribute('room_id');
		$department_id = $request->getAttribute('department_id');
		$html = $this->view->showAddWorkerTopologySelectedWorkersView($room_id, $department_id);
        $data['status'] = 'success';
        $data['div'] = 'addworkertopology_selectedworkers';
        $data['html'] = $html;
        $response = $response->withJson($data);
        return $response;
    }

	/*
    * Сделать сотрудника публичным/закрытым
    * Return: JSON
    */

    public function makeWorkerPublicControl(Request $request, Response $response)
    {
        $worker_id = $request->getAttribute('worker_id');
        $status = $request->getAttribute('status');
        $data = $this->model->makeWorkerPublicModel($worker_id,$status);
        $response = $response->withJson($data);
        return $response;
    }


	/*
	 * Отображение формы со всеми услугами при добавлении услуги
	 * Return: JSON
	 */
    public function showExistingHearingsTopologyControl(Request $request, Response $response)
    {
        $room_id = $request->getAttribute('room_id');
		$html = $this->view->showAddHearingTopologySelectedWorkersView($room_id);
        $data['status'] = 'success';
        $data['div'] = 'addhearingtopology_selectedworkers';
        $data['html'] = $html;
        $response = $response->withJson($data);
        return $response;
    }

	/*
	 * Отображение кнопки для добавления услуги
	 * Return: JSON
	 */
    public function showHearingCreateButtonTopologyControl(Request $request, Response $response)
    {
        $room_id = $request->getAttribute('room_id');
		$worker_id = $request->getAttribute('worker_id');
		$html = $this->view->showHearingCreateButtonTopologyView($room_id, $worker_id);
        $data['status'] = 'success';
        $data['div'] = 'topologyaddhearingcreatebutton';
        $data['html'] = $html;
        $response = $response->withJson($data);
        return $response;
    }


//  ТОПОЛОГИЯ --->


    /*
 * Вывод окна сообщений
 * Return: JSON
 */
    public function showMessagesControl(Request $request, Response $response)
    {
        $html = $this->view->showMessagesView();
        $data['status'] = 'success';
        $data['div'] = 'popup';
        $data['html'] = $html;
        $response = $response->withJson($data);
        return $response;
    }


    /*
 * Вывод окна Люди (Батя) в здании
 * Return: JSON
 */
    public function showPeopleInBuildingControl(Request $request, Response $response)
    {
        $html = $this->view->showPeopleInBuildingView();
        $data['status'] = 'success';
        $data['div'] = 'popup';
        $data['html'] = $html;
        $response = $response->withJson($data);
        return $response;
    }

    /*
    * Добавление категории доступа
    * Return: JSON
    */

    public function addWorkerCategoryAccessControl(Request $request, Response $response)
    {
        $worker_id = $request->getAttribute('worker_id');
        $room_id = $request->getAttribute('room_id');
        $status = $request->getAttribute('status');
        $data = $this->model->addWorkerCategoryAccessModel($worker_id,$room_id,$status);
        $response = $response->withJson($data);
        return $response;
    }

    /*
     * Установка/снятие объектов с охраны
     * Return: JSON
     */

    public function addWorkerSecurityAccessControl(Request $request, Response $response)
    {
        $worker_id = $request->getAttribute('worker_id');
        $room_id = $request->getAttribute('room_id');
        $status = $request->getAttribute('status');
        $data = $this->model->addWorkerSecurityAccessModel($worker_id,$room_id,$status);
        $response = $response->withJson($data);
        return $response;
    }

    /*
     * Создание комнаты для терминала
     */
    public function createTerminalStream(Request $request, Response $response)
    {
        $id = $request->getAttribute('id');
        $room = RandomString(8);
        $data = $this->model->createTerminalStream($id,$room);
        if($data['status']=='success') {
            //TODO: Александр отправить ссылку в терминал (нужен тест)
            //{$this->settings['webrtc_url']}r/{$room}?type=screen
            $data['roomUrl'] = "{$this->settings['webrtc_url']}r/{$room}?type=screen";
            $eq = $this->equipment->FindOne($id, 'id');
            $this->client->SendTerminal($eq, 'assistantconnected', $data);
        }
        $response = $response->withJson($data);
        return $response;
    }

    /*
     * Сброс комнаты для терминала
     */
    public function deleteTerminalStream(Request $request, Response $response)
    {
        $id = $request->getAttribute('id');
        $room = null;
        $data = $this->model->createTerminalStream($id, $room);
        $response = $response->withJson($data);
        return $response;
    }

	/*
	 * TODO метод перенесен в MainController
	*Отображение формы фильтрации для вкладки Главная
	*/
	public function showMainFilterControl(Request $request, Response $response)
    {
        $type = $request->getAttribute('type');
        $html = $this->view->showMainFilterView($type);
        $data['status'] = 'success';
        $data['div'] = 'popup';
        $data['html'] = $html;
        $response = $response->withJson($data);
        return $response;
    }

	/*
	*Отображение отфильтрованных данных для вкладки Главная
	*/
	public function showMainFilteredDataControl(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
		$type = $request->getAttribute('type');
        $html = $this->view->showMainFilteredDataView($data, $type);
        $data['status'] = 'success';
		if ($type=='in') $data['div'] = 'tableInContent';
		if ($type=='out') $data['div'] = 'tableOutContent';
        $data['html'] = $html;
        $response = $response->withJson($data);
        return $response;
    }

	/*
	*Отображение формы фильтрации для вкладки Аккаунты - Фильтрация
	*/
	public function showAccountStatisticFilterControl(Request $request, Response $response)
    {
        $user_id = $request->getAttribute('user_id');
		$type = $request->getAttribute('type');
        $html = $this->view->showAccountStatisticFilterView($user_id, $type);
        $data['status'] = 'success';
        $data['div'] = 'popup';
        $data['html'] = $html;
        $response = $response->withJson($data);
        return $response;
    }

	/*
	*Отображение отфильтрованных данных для вкладки Аккаунты - Фильтрация
	*/
	public function showAccountStatisticFilteredDataControl(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
		$user_id = $request->getAttribute('user_id');
		$type = $request->getAttribute('type');
        $html = $this->view->showAccountStatisticFilteredDataView($data, $user_id, $type);
        $data['status'] = 'success';
		$data['div'] = 'passTable';
        $data['html'] = $html;
        $response = $response->withJson($data);
        return $response;
    }


}
