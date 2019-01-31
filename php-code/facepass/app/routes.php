<?php

use App\Controllers\Deploy;
use App\Controllers\ApiController;
use App\Controllers\HttpClientController;
use App\Controllers\InterfaceController;
use App\Controllers\EquipmentController;
use App\Controllers\FilesController;
use App\Controllers\WorkersController;
use App\Controllers\Ui\ConfigController;
use App\Controllers\Ui\NullAccountController;
use App\Controllers\Ui\TopologyController;
use App\Controllers\Ui\AccessControllController;
use App\Controllers\Ui\UserPassController;
use App\Controllers\Ui\HearingsController;
use App\Controllers\Ui\UserAccessController;
use App\Controllers\Ui\FilialDepartamentController;
use App\Controllers\Ui\AccountController;
use App\Controllers\Ui\MonitoringController;
use App\Controllers\Ui\WorkScheduleController;
use App\Controllers\Ui\PassOfficeController;
use App\Controllers\Ui\PageController;
use App\Controllers\Ui\InterfacesPermissionController;
use App\Middleware\ApiErrorMiddleware;
use App\Middleware\AuthMiddleware;
use App\Controllers\Ui\MainController;




//собираем класс авторизации(в разработке)
$auth = new AuthMiddleware($container);
//$app->get('/',  PageController::class.':Main')->add($auth);
$app->map(['GET', 'POST'], '/login/', PageController::class . ':Login');

//Получение картинок с правами
//$app->get('/img/{type}/{name}',  FilesController::class.':GetImage')->add($auth);
$app->get('/img/{type}/{name}', FilesController::class . ':GetImage');

// <--- Обработка интерфейсов
//Главная
$app->get('/', MainController::class . ':getIndex')->add($auth);;

$app->group('/main', function() {
    $this->get('/', MainController::class . ':getIndex');
    $this->post('/in-out-content', MainController::class . ":postInOutContent");
    //блок отображения выбранной записи таблицы выходов
    $this->get('/show-big-user/{user_id}/{type}/', MainController::class . ':getShowBigUserPass');
})->add($auth);

//Фильтры для главной страницы
//Отображение формы фильтрации для вкладки Главная
$app->get('/filter/main/{type}/', MainController::class . ':getShowMainFilterControl');
//Отображение отфильтрованных данных для вкладки Главная
$app->post('/filter/filtered/{type}/', MainController::class . ':showMainFilteredDataControl');


//Настройки
$app->group('/configs', function () {
    $this->get('/', ConfigController::class . ':actionIndex');
    $this->get('/marks', ConfigController::class . ':actionMark');
    $this->get('/document-types', ConfigController::class . ':actionDocType');
    $this->get('/filial-departaments', ConfigController::class . ':actionFilialDepartment');
    $this->get('/filial-super-departaments', ConfigController::class . ':actionFilialSuperDepartament');
    $this->get('/user-types', ConfigController::class . ':actionUserTypes');
    $this->get('/filial-rooms', ConfigController::class . ':actionRooms');
    $this->get('/permission-interfaces', ConfigController::class . ':actionPermissions');
    $this->get('/permission-filial-rooms', ConfigController::class . ':actionFilialPermissions');
    $this->get('/equipment-type', ConfigController::class . ':actionEquipmentTypes');
    $this->get('/users', ConfigController::class . ':actionUsers');
    $this->get('/workers', ConfigController::class . ':actionWorkers');
    $this->get('/hearings', ConfigController::class . ':actionHearings');
    $this->get('/permission-access', ConfigController::class . ':actionUserAccess');
    $this->map(['GET', 'POST'], '/cameras', ConfigController::class . ':Cameras');
    $this->get('/cameras/restart', ConfigController::class . ':RestartCameras');
    $this->get('/cameras/del/{id}', ConfigController::class . ':DelCamera');
//$app->get('/configs/', InterfaceController::class.':createWorkerInterface');
})->add($auth);;

//Аккаунты
$app->group('/accounts', function () {
    $this->get('/', AccountController::class . ':getIndex');
})->add($auth);
//Аккаунты (Экшены)

$app->group('/account', function () {
    $this->post('/load', AccountController::class . ':actionLoad');
    $this->post('/search', AccountController::class . ':actionSearch');
    //Возможно нужен отдельный роут для работы с фотками
    $this->get('/get-photo-document/{id}/', AccountController::class . ':getPhoto');


//Аккаунт
    $this->get('/{id:[0-9]+}/', AccountController::class . ':getOne');
})->add($auth);


//Контроль доступа
$app->group('/access-control', function () {
    $this->get('/', AccessControllController::class . ':accessControlView');
    $this->get('/category/{id}/{dep_id}/', AccessControllController::class . ':accessControlCategoryUsersControl');
    $this->post('/category_search/{id}/{dep_id}/', AccessControllController::class . ':accessControlCategoryUsersSearchControl');
    $this->get('/showbutton/{id}/{dep_id}/', AccessControllController::class . ':showSearchAccessCategoryButtonControl');
    $this->get('/showchangedepartment/{id}/', AccessControllController::class . ':showChangeDepartmentAccessCategoryControl');
    $this->post('/changedepartment/{id}/', AccessControllController::class . ':changeDepartmentAccessCategoryControl');
    $this->get('/showadddepartment/{id}/', AccessControllController::class . ':showAddDepartmentAccessCategoryControl');
    $this->post('/adddepartment/{id}/', AccessControllController::class . ':addDepartmentAccessCategoryControl');
})->add($auth);





//Режим наблюдения
//$app->get('/monitoring/', InterfaceController::class . ':createWorkerInterface');
$app->get('/monitoring/[{active}]', MonitoringController::class . ':Main');

//Streams
$app->group('/stream', function () {
    $this->get('/terminal/create/{id}/', InterfaceController::class . ':createTerminalStream');
    $this->get('/terminal/delete/{id}/', InterfaceController::class . ':deleteTerminalStream');
})->add(new ApiErrorMiddleware($container));


//Статистика
$app->get('/statistic/', InterfaceController::class . ':createWorkerInterface');

$app->get('/logout/', InterfaceController::class . ':createWorkerInterface');
// Обработка интерфейсов --->

// <--- AJAX-запросы для интерфейсов
$app->post('/auth/', InterfaceController::class . ':authUserControl');
$app->post('/logout/', InterfaceController::class . ':logoutUserControl');

//Интерфейсы
$app->post('/interface/', InterfaceController::class . ':addInterfaceControl');
$app->post('/interface/{id}/', InterfaceController::class . ':updateInterfaceControl');
$app->get('/interface/form/', InterfaceController::class . ':showAddInterfaceFormControl');
$app->get('/interface/form/{id}/', InterfaceController::class . ':showChangeInterfaceFormControl');

//Доступ к интерфейсам
$app->post('/interfaces/permission/update/{id}/', InterfacesPermissionController::class . ':updatePermissionDefaultInterfaceControl');
$app->post('/interfaces/permission/add/', InterfacesPermissionController::class . ':addPermissionDefaultInterfaceControl');
$app->get('/interfaces/permission/form/add/', InterfacesPermissionController::class . ':showAddPermissionDefaultInterfaceControl');
$app->get('/interfaces/permission/form/update/{id}/', InterfacesPermissionController::class . ':showChangePermissionDefaultInterfaceControl');
$app->get('/interfaces/permission/table/{id}/', InterfacesPermissionController::class . ':showTablePermissionDefaultInterfaceControl');

//Метки
$app->post('/marks/', InterfaceController::class . ':addMarkControl');
$app->post('/marks/{id}/', InterfaceController::class . ':updateMarkControl');
$app->get('/marks/form/', InterfaceController::class . ':showAddMarkFormControl');
$app->get('/marks/form/{id}/', InterfaceController::class . ':showUpdateMarkFormControl');

//Типы документов
$app->post('/documents-type/', InterfaceController::class . ':addUserDocumentTypeControl');
$app->post('/documents-type/{id}/', InterfaceController::class . ':updateUserDocumentTypeControl');
$app->get('/documents-type/form/', InterfaceController::class . ':showAddUserDocumentTypeFormControl');
$app->get('/documents-type/form/{id}/', InterfaceController::class . ':showChangeUserDocumentTypeControl');

//Отделы филиала
$app->group('/filial-departments', function () {
    $this->post('/create/{type}/', FilialDepartamentController::class . ':addFilialDepartmentControl');
    $this->post('/change/{id}/{type}/', FilialDepartamentController::class . ':updateFilialDepartmentControl');
    $this->get('/createform/{type}/', FilialDepartamentController::class . ':showAddFilialDepartmentControl');
    $this->get('/form/{id}/{type}/', FilialDepartamentController::class . ':showChangeFilialDepartmentControl');

//Доступ отдела к помещениям
    //TODO: Возможно назвать роут по другому и вынести методы в отдельный контроллер. (Они относятся не к филиалам а к его комнатам)
    $this->post('/addroom/{id}/', FilialDepartamentController::class . ':addRoomPermissionToFilialDepartmentControl');
    $this->post('/addroommodify/{id}/', FilialDepartamentController::class . ':updateRoomPermissionToFilialDepartmentControl');
    $this->get('/formaddroom/{id}/', FilialDepartamentController::class . ':showAddRoomPermissionToFilialDepartmentFormControl');
    $this->get('/formaddroommodify/{id}/', FilialDepartamentController::class . ':showChangeRoomPermissionToFilialDepartmentControl');
    $this->get('/formroomstable/{id}/', FilialDepartamentController::class . ':showTablePermissionFilialDepartmentRoomsControl');

})->add($auth);

//Типы пользователей
$app->post('/user-types/', InterfaceController::class . ':addUserTypeControl');
$app->post('/user-types/{id}/', InterfaceController::class . ':updateUserTypeControl');
$app->get('/user-types/form/', InterfaceController::class . ':showAddUserTypeControl');
$app->get('/user-types/form/{id}/', InterfaceController::class . ':showChangeUserTypeControl');

//Пользователи
$app->post('/users/', InterfaceController::class . ':addUserControl');
$app->post('/users/{id}/', InterfaceController::class . ':updateUserControl');
$app->get('/users/form/', InterfaceController::class . ':showAddUserControl');
$app->get('/users/form/{id}/', InterfaceController::class . ':showChangeUserControl');

//Помещения
$app->post('/rooms/', InterfaceController::class . ':addRoomControl');
$app->post('/rooms/{id}/', InterfaceController::class . ':updateRoomControl');
$app->get('/rooms/form/', InterfaceController::class . ':showAddRoomsControl');
$app->get('/rooms/form/{id}/', InterfaceController::class . ':showChangeRoomControl');

//Оборудование
$app->post('/equipment/', InterfaceController::class . ':addEquipmentTypeControl');
$app->post('/equipment/{id}/', InterfaceController::class . ':updateEquipmentTypeControl');
$app->get('/equipment/form/', InterfaceController::class . ':showAddEquipmentTypeControl');
$app->get('/equipment/form/{id}/', InterfaceController::class . ':showChangeEquipmentTypeControl');
//получение устройств по типу
$app->get('/equipment/type/{type}/', EquipmentController::class . ':getByType');


//Сотрудники
$app->post('/workers/{id}/', InterfaceController::class . ':addWorkerControl');
$app->post('/workers/edit/{id}/', InterfaceController::class . ':updateWorkerControl');
$app->get('/workers/form/{id}/', InterfaceController::class . ':showAddWorkerControl');
$app->get('/workers/form/edit/{id}/', InterfaceController::class . ':showChangeWorkerControl');

//Слушания
$app->group('/hearings', function () {
    $this->post('/', HearingsController::class . ':addHearingControl');
    $this->post('/{id}/', HearingsController::class . ':updateHearingControl');
    $this->get('/form/{id}/', HearingsController::class . ':showChangeHearingControl');
    $this->get('/form/', HearingsController::class . ':showAddHearingControl');
})->add($auth);

//Доступ пользователей на территорию
$app->group('/useraccess', function () {
    $this->post('/', UserAccessController::class . ':addUserAccessControl');
    $this->post('/{id}/', UserAccessController::class . ':updateUserAccessControl');
    $this->get('/form/{id}/', UserAccessController::class . ':showChangeUserAccessControl');
    $this->get('/form/', UserAccessController::class . ':showAddUserAccessControl');
})->add($auth);

//Проходы пользователей
$app->post('/userpass_modify/{id}/', UserPassController::class . ':updateUserPassControl');
//$app->get('/useraccess/form/',InterfaceController::class.':showAddUserAccessControl');

$app->group('/userpass', function () {
    $this->post('/{user_id}/{access_id}/', UserPassController::class . ':addUserPassControl');
//Пропуски
    $this->get('/form/{id}/', UserPassController::class . ':showUserPassControl');
})->add($auth);


//Прикрепление меток к пользователю
$app->post('/usermark/{user_id}/', MarkController::class . ':addUserMarkControl');
$app->post('/usermark_modify/', MarkController::class . ':updateUserMarkControl');
$app->get('/usermark/main/{user_pass}/{mark_id}/', MarkController::class . ':updateUserMarkPassControl');

//увеличенная таблица проходов
$app->get('/bigview/form/{type}/', InterfaceController::class . ':showBigUserPassControl');
//$app->get('/bigview/form/{type}/{filter}/',InterfaceController::class.':showBigUserPassControl');

//блок отображения выбранной записи таблицы входов
$app->get('/bigview/table-in/{user_id}/', InterfaceController::class . ':showInBigUserPassControl');

//блок отображения выбранной записи таблицы выходов
$app->get('/bigview/table-out/{user_id}/', InterfaceController::class . ':showOutBigUserPassControl');

//Доступ работников к отделам
$app->get('/workerdepartmnetaccess/{worker_id}/{department_id}/{status}/', InterfaceController::class . ':addWorkerDepartmentAccessControl');
//$app->post('/workerdepartmnetaccess_modify/{id}/{status}/',InterfaceController::class.':updateWorkerDepartmentAccessControl');

//Доступ работников к помещениям
$app->post('/workerpermissionsaccess/{worker_id}/{room_id}/', InterfaceController::class . ':addWorkerPermissionAccessControl');
$app->post('/workerpermissionsaccess_modify/{id}/{status}/', InterfaceController::class . ':updateWorkerPermissionAccessControl');

//Категория доступа работников к помещениям
$app->get('/workers/access/category/{worker_id}/{room_id}/{status}/', InterfaceController::class . ':addWorkerCategoryAccessControl');
//Установка/снятие объектов с охраны
$app->get('/workers/access/security/{worker_id}/{room_id}/{status}/', InterfaceController::class . ':addWorkerSecurityAccessControl');

//Вкладка Топология
$app->group('/topology', function () {
    $this->get('/topologyadv/', TopologyController::class . ':actionIndex');
    //Топология
//окно с топологией
    $this->get('/', TopologyController::class . ':showTopologyControl');

//отображение вложенной топологии
    $this->get('/show/{type}/{id}/', TopologyController::class . ':actionAjaxTableTopology');


    //Работаем с группами комнат в топологии
    $this->get('/group-rooms/add/{id}/', TopologyController::class.':actionGroupRoomsAdd');
    $this->post('/group-rooms/add/{id}/', TopologyController::class.':actionGroupRoomsAdd');

    $this->get('/group-rooms/update/{id}/', TopologyController::class . ':actionGroupRoomsUpdate');
    $this->post('/group-rooms/update/{id}/', TopologyController::class . ':actionGroupRoomsUpdate');

    $this->post('/group-rooms/delete/{id}/', TopologyController::class . ':actionGroupRoomsDelete');

    //Работаем с департаментами (Или отделами теперь нет разницы) в топологии
    $this->get('/departament/add/{id}/', TopologyController::class . ':actionDepartamentAdd');
    $this->post('/departament/add/{id}/', TopologyController::class . ':actionDepartamentAdd');

    $this->get('/departament/update/{id}/', TopologyController::class . ':actionDepartamentUpdate');
    $this->post('/departament/update/{id}/', TopologyController::class . ':actionDepartamentUpdate');

    $this->post('/departament/delete/{id}/', TopologyController::class . ':actionDepartamentDelete');

    //Работаем с комнатами в топологии
    $this->get('/room/add/{id}/', TopologyController::class . ':actionRoomAdd');
    $this->post('/room/add/{id}/', TopologyController::class . ':actionRoomAdd');
    $this->get('/room/update/{id}/', TopologyController::class . ':actionRoomUpdate');
    $this->post('/room/update/{id}/', TopologyController::class . ':actionRoomUpdate');
    $this->get('/room/delete/{id}/', TopologyController::class . ':actionRoomDelete');

    //Работаем с сотрудниками в топологии
    $this->get('/worker/add/{id}/', TopologyController::class . ':actionWorkerAddToRoom');
    $this->post('/worker/add/{id}/', TopologyController::class . ':actionWorkerAddToRoom');
    $this->get('/worker/delete/{id}/', TopologyController::class . ':actionWorkerDeleteFromRoom');

    //Работаем с услугами
    $this->get('/service/add/{id}/', TopologyController::class . ':actionServiceAdd');
    $this->post('/service/add/{id}/', TopologyController::class . ':actionServiceAdd');
    $this->get('/service/update/{id}/', TopologyController::class . ':actionServiceUpdate');
    $this->post('/service/update/{id}/', TopologyController::class . ':actionServiceUpdate');
    $this->get('/service/delete/{id}/', TopologyController::class . ':actionServiceDelete');

    $this->get('/change-room/{worker-id}/', TopologyController::class.':actionAjaxChangeRoom');
    $this->get('/make-change-room/{room-id}/{worker-id}/', TopologyController::class.':actionAjaxMakeChangeRoom');


//$app->get('/add/form/{type}/',TopologyController::class.':showAddTopologyObjectControl');
//отображение формы изменения группы/помещения
    $this->get('/edit/form/{room_id}/{type}/', TopologyController::class . ':showChangeTopologyObjectControl');
//добавление группы/помещения
//$app->post('/add/{type}/',InterfaceController::class.':addTopologyObjectControl');
//изменение группы/помещения
    $this->post('/edit/{room_id}/', TopologyController::class . ':updateTopologyObjectControl');


//Работа с категориями топологии
    $this->get('/add/form/', TopologyController::class . ':topologyGetFormAddTopology');
    $this->post('/add/topology/', TopologyController::class . ':topologyAddTopology');
    $this->post('/{floor_id}/delete/floor/', TopologyController::class . ':topologyDeleteFloor');

//Работа с подкатегориями топологии
    $this->get('/{floor_id}/get/form/add/subtopology/', TopologyController::class . ':topologyGetFormAddSubtopology');
    $this->post('/{floor_id}/add/subtopology/', TopologyController::class . ':topologyAddSubtopology');
//Работа с департаментами в топологии
    $this->get('/{id}/get/form/add/departament/', TopologyController::class . ':topologyGetFormAddDepartament');
    $this->get('/{id}/search/departament/{name}/', TopologyController::class . ':topologySearchDepartamentByName');
    $this->post('/{id}/add/departament/', TopologyController::class . ':topologyAddDepartament');
    $this->post('/{floor_id}/{departament_id}/delete/departament/', TopologyController::class . ':topologyDeleteDepartament');

//Работа с отделами в топологии
    $this->get('/{floor_id}/{departament_id}/get/form/add/subdepartment/', TopologyController::class . ':topologyGetFormAddSubdepartament');
    $this->get('/{floor_id}/{departament_id}/search/subdepartament/{name}/', TopologyController::class . ':topologySearchSubdepartamentByName');
    $this->post('/{floor_id}/{departament_id}/add/subdepartament/', TopologyController::class . ':topologyAddSubdepartament');
//Работа с кабинетами в топологии
    $this->get('/{floor_id}/{departament_id}/get/form/add/room/', TopologyController::class . ':topologyGetFormAddRoom');
    $this->post('/{floor_id}/{departament_id}/add/room/', TopologyController::class . ':topologyAddRoom');
    $this->post('/delete/room/{floor_id}/{departament_id}/{room_id}/', TopologyController::class . ':topologyDeleteRoom');

//Работа с сотрудниками
//Отображение формы Добавить сотрудника
    $this->get('/{floor_id}/{departament_id}/{room_id}/add/worker/form/', TopologyController::class . ':showAddTopologyWorkerControl');
//Прикрепление сорудника к кабинету
    $this->post('/{floor_id}/{departament_id}/{room_id}/add/worker/', TopologyController::class . ':topologyAddWorkerControl');
//Отвязка сотрудника от кабинета
    $this->post('/unlink/worker/{room_id}/{worker_id}/', TopologyController::class . ':unlinkTopologyWorkerControl');

//Отображение формы со всеми свободными сотрудниками при добавлении сотрудника
//$app->get('/add/show_all_worker/{room_id}/{department_id}/',InterfaceController::class.':showAllWorkersTopologyControl');
//Отображение формы со всеми занятыми сотрудниками
//$app->get('/add/show_busy_worker/{room_id}/{department_id}/',InterfaceController::class.':showBusyWorkersTopologyControl');
//Добавление сотрудника в кабинет
//$app->get('/add_to_topology/worker/{room_id}/{worker_id}/',InterfaceController::class.':addTopologyWorkerControl');

//Отображение формы Добавить услугу
    $this->get('/add/hearing/{room_id}/{worker_id}/form/', TopologyController::class . ':showAddTopologyHearingControl');
//Создание услуги
    $this->post('/add/hearing/{room_id}/{worker_id}/', TopologyController::class . ':addHearingTopologyControl');


//Отображение формы со всеми услугами при добавлении услуги
//$app->get('/add/show_existing_hearings/{room_id}/',InterfaceController::class.':showExistingHearingsTopologyControl');
//Отображение кнопки для добавлении услуги
//$app->get('/add/hearing/createbutton/{room_id}/{worker_id}/',InterfaceController::class.':showHearingCreateButtonTopologyControl');
//Сделать сотрудника публичным/закрытым
//$app->get('/add/hearing/publicworker/{worker_id}/{status}/',InterfaceController::class.':makeWorkerPublicControl');
//Сделать услугу публичной/закрытой
    $this->get('/add/hearing/publichearing/{hearing_id}/{status}/', TopologyController::class . ':makeHearingPublicControl');
//Удалить услугу
    $this->get('/add/hearing/deletehearing/{hearing_id}/', TopologyController::class . ':deleteHearingTopologyControl');
//Поиск по топологии
    $this->post('/{topologytype}/search/', TopologyController::class . ':topologySearch');
});


//Уведомления
$app->group('/notifications', function () {
    $this->get('/', WorkersController::class . ':Notifications');
    $this->get('/get/{id}/', WorkersController::class . ':GetNotify');
    $this->get('/new/', WorkersController::class . ':NewNotify');
    $this->post('/reply/{id}/', WorkersController::class . ':ReplyNotify');
})->add($auth);

//Сообщения
$app->get('/messages/', InterfaceController::class . ':showMessagesControl');

//Нулевой аккаунт
$app->group('/nullaccount', function () {
    $this->get('/', NullAccountController::class . ':actionIndex');
    $this->post('/add/', NullAccountController::class . ':addNullAccountControl');
    $this->get('/updateform/{id}/', NullAccountController::class . ':actionAjaxUpdate');
    $this->post('/update/{id}/', NullAccountController::class . ':updateNullAccountControl');
});

//Людей в здании
$app->get('/peopleinbulding/', InterfaceController::class . ':showPeopleInBuildingControl');

//Рабочий график
$app->group('/workschedule', function () {
    //Вкладка График работ
    $this->get('/', WorkScheduleController::class . ':actionIndex');
//Вывод кнопки создания графика работ
    $this->get('/showbutton/{id}/', WorkScheduleController::class . ':showCreateWorkScheduleControl');
//Вывод рабочего графика для конкретного элемента топологии
    $this->get('/show/{id}/', WorkScheduleController::class . ':actionIndex');
//Вывод формы выбора дат
    $this->get('/showworkschedulestartenddate/{id}/', WorkScheduleController::class . ':showWorkScheduleStartEndDateControl');
//Вывод календаря в заданном диапазоне дат
    $this->get('/calendar/{start}/{end}/', WorkScheduleController::class . ':showWorkScheduleCalendarControl');
//Заполнение графика работ
    $this->post('/create/{id}/', WorkScheduleController::class . ':createWorkScheduleControl');
//Отображение формы изменения шаблона графика
    $this->get('/weektemlateedit/show/{hearing_id}/', WorkScheduleController::class . ':showUpdateHearingWeekTemplateControl');
//Изменение шаблона графика
    $this->post('/weektemlateedit/{hearing_id}/', WorkScheduleController::class . ':updateHearingWeekTemplateControl');
});

//Фильтр
//Отображение формы фильтрации для вкладки Аккаунты - Статистика
$app->get('/filter/statistic/{user_id}/{type}/', InterfaceController::class . ':showAccountStatisticFilterControl');
//Отображение отфильтрованных данных для вкладки Аккаунты - Статистика
$app->post('/filter/filtered/statistic/{user_id}/{type}/', InterfaceController::class . ':showAccountStatisticFilteredDataControl');


//приглашения
$app->group('/invitees', function () {
    $this->map(['GET', 'POST'], '/', PassOfficeController::class . ':Main');
    $this->post('/hearings', PassOfficeController::class . ':GetHearingByDate');
})->add($auth);

//API v1
$app->group('/api/v1', function () {
    //открытие турникета
    $this->post('/open/turnstile', ApiController::class . ':OpenTurnstile');
    //получение конфигурации устройства
    $this->post('/configuration', ApiController::class . ':AddDeviceConfig');
    //время Сервера
    $this->get('/configuration/time', ApiController::class . ':GetTime');
    //логирование
    $this->post('/log', ApiController::class . ':Log');
    //Получение иерархической структуры филиала
    $this->get('/departments', ApiController::class . ':Departments');
    //Запрос на помощь от посетителя
    $this->post('/request/help', ApiController::class . ':RequestHelp');
    //Запрос для охранника на сравнение ручного ввода и скана паспорта
    $this->post('/request/compare', ApiController::class . ':RequestCompare');
    //Запрос на сравнение лица с паспортом
    $this->post('/request/compareface', ApiController::class . ':CompareFaces');
    //Запрос на получение стрима камеры
    $this->get('/stream', ApiController::class . ':GetStreamCam');
    //Ручное фото
    $this->post('/request/makephoto', ApiController::class . ':MakePhoto');
    //верификация по фото
    $this->post('/verify', ApiController::class . ':Verify');
    //Добавление посетителя
    $this->post('/user', ApiController::class . ':AddUser');
    //Обновление данных посетителя
    $this->put('/user/{id}', ApiController::class . ':UpdateUser');
    //Информация о посетителе
    $this->get('/user/{id}', ApiController::class . ':GetUser');
    //Добавление документа пользователя
    $this->post('/user/{id}/document', ApiController::class . ':AddDocument');
    //Получение приглашений пользователя
    $this->get('/user/{id}/invitees', ApiController::class . ':GetInvitees');
    //Получение пропуска для печати
    $this->post('/user/{id}/pass', ApiController::class . ':GetPass');
    //Добавление рабочего
    $this->post('/worker', ApiController::class . ':AddWorker');
    //закрытие сессии пользователем
    $this->post('/close', ApiController::class . ':CloseSession');
    //Добавление изображения с металлодетектора\ренгена
    $this->post('/pass/{id}/detector', ApiController::class . ':AddDetectorImg');
    //Получение всех пропусков
    $this->get('/marks/{who}', ApiController::class . ':GetAllMarks');
    //Сохранение проходов для автономной работы
    $this->post('/save/pass', ApiController::class . ':SavePass');

    //Установка курсора в указанную позицию
    $this->post('/setcursor/{id}/{x}/{y}/', ApiController::class . ':sendCursorCoordinates');

})->add(new ApiErrorMiddleware($container));


//Test Http Client Requests
$app->group('/http', function () {
    $this->get('/in/{id}/{persona_id}', HttpClientController::class . ':GoToTheTerminal');
    $this->get('/out/{id}', HttpClientController::class . ':OutTerminal');
    $this->get('/autophoto/{id}', HttpClientController::class . ':AutoPhoto');
    $this->get('/compare/{id}/{bool}', HttpClientController::class . ':ResponseCompare');
    $this->get('/comparefaces/{id}/{bool}', HttpClientController::class . ':ResponseCompareFaces');
    $this->get('/halt/{id}/{block}', HttpClientController::class . ':HaltTerminal');
    $this->get('/connect/{id}', HttpClientController::class . ':ConnectTerminal');
    $this->get('/pointer/{id}', HttpClientController::class . ':SetPointer');
    $this->get('/open/{id}', HttpClientController::class . ':OpenTurnstile');
});

//Вебхук для запуска деплоя
$app->post('/deploy/{token}', Deploy::class . ':runWeb');

?>
