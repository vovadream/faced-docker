<?php

//контроллеры
use App\Controllers\Controller;
use App\Controllers\ApiController;
use App\Controllers\HttpClientController;
use App\Controllers\InterfaceController;
//вьюхи
use App\Views\InterfaceView;

//Middleware зависимости
$app->add(new RKA\Middleware\IpAddress()); //для определения ипа

$container = $app->getContainer();

//db connection

$container['db'] = function ($c) {
    $settings = $c->get('settings')['db'];
    $connect = new PDO($settings['driver'].":host=" . $settings['host'] . ";dbname=" . $settings['dbname'],
        $settings['user'], $settings['pass']);
    $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $connect->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $connect;
};

//Guzzle HTTP client
$container['httpClient'] = function() {
    $guzzle = new \GuzzleHttp\Client();
    return $guzzle;
};

//logger
$container['logger'] = function() {
    $logger = new \Monolog\Logger('FacePass');
    $file_handler = new \Monolog\Handler\StreamHandler( __DIR__ . "/../logs/app.log");
    $logger->pushHandler($file_handler);
    return $logger;
};

//инициализация контроллеров

$container['Controller'] = function ($c) {
    return new Controller($c);
};

$container['HttpClientController'] = function ($c) {
    return new HttpClientController($c);
};

$container['InterfaceController'] = function ($c) {
    return new InterfaceController($c);
};

//инициализация моделей

$models = [
    'Model',
    'AccessControlModel',
    'CameraModel',
    'DocumentsModel',
    'DocumentTypeModel',
    'DocumentTypeModel',
    'EquipmentModel',
    'EquipmentTypeModel',
    'FilialDepartmentModel',
    'FilialDepartmentRoomsModel',
    'FilialGroupRoomsModel',
    'FilialModel',
    'FilialRoomModel',
    'FilialRoomsHearingModel',
    'HearingsModel',
    'InterfaceModel',
    'LoggerModel',
    'MarkModel',
    'NullAccountModel',
    'PermissionDefaultInterfaceModel',
    'PermissionsModel',
    'TopologyModel',
    'UserAccessModel',
    'UserImagesModel',
    'UserMarksModel',
    'UserPassModel',
    'UserRbacModel',
    'UsersModel',
    'UserTypeModel',
    'WorkerDepartmentsModel',
    'WorkerRoomsModel',
    'WorkersModel',
    'WorkScheduleModel',
];

foreach ($models as $model) {
    $container[$model] = function (\Slim\Container $c) use ($model) {
        $class = '\\App\\Models\\'. $model;
        return new $class($c);
    };
}


//инициализация вьюх del
$container['InterfaceView'] = function ($c) {
    return new InterfaceView($c->get('InterfaceModel'));
};

