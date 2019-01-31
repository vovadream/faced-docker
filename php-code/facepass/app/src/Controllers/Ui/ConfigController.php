<?php


namespace App\Controllers\Ui;

use \Slim\Container;
use DateTime;
use App\Controllers\Controller;
use App\Models\UserTypeModel;
use App\Models\InterfaceModel;
use App\Models\MarkModel;
use App\Models\DocumentTypeModel;
use App\Models\FilialDepartmentModel;
use App\Models\FilialRoomModel;
use App\Models\PermissionsModel;
use App\Models\PermissionDefaultInterfaceModel;
use App\Models\FilialDepartmentRoomsModel;
use App\Models\EquipmentTypeModel;
use App\Models\UsersModel;
use App\Models\WorkersModel;
use App\Models\HearingsModel;
use App\Models\UserAccessModel;
use App\Models\CameraModel;
use App\module\ffserver\ffserver;
use \Slim\Http\Request as Request;
use \Slim\Http\Response as Response;

/**
 * Класс для работы с настройками системы
 * Class UserTypeController
 * @package App\Controllers
 */
class ConfigController extends Controller
{

    /*
     * Вывод интерфейсов филиала
     * Return: HTML
     */
    public function actionIndex(Request $request, Response $response)
    {

        $model = $this->InterfaceModel;

        $interfaces = $model->getInterfacesModel();

        $content = tpl('config/views/index', ['interfaces' => $interfaces]);
        $data = layout('config/config_tpl',
            [
                'content' => $content
            ]);

        return $response->getBody()->write($data);
    }



    /*
     * Вывод меток
     * Return: HTML
     */
    public function actionMark(Request $request, Response $response)
    {
        $model = $this->MarkModel;

        $marks = $model->getMarksModel();

        $content = tpl('config/views/marks', ['marks' => $marks]);

        $data = layout('config/config_tpl',
            [
                'content' => $content
            ]);

        return $response->getBody()->write($data);
    }

    /*
     * Вывод типов документа
     * Return: HTML
     */
    public function actionDocType(Request $request, Response $response)
    {
        $model = $this->DocumentTypeModel;

        $document_type = $model->getUserTypeDocumentsModel();

        $content = tpl('config/views/document-types', ['document_type' => $document_type]);

        $data = layout('config/config_tpl',
            [
                'content' => $content
            ]);

        return $response->getBody()->write($data);
    }

    /*
     * Вывод отделов филиала
     * Return: HTML
     */
    public function actionFilialDepartment(Request $request, Response $response)
    {
        $model = $this->FilialDepartmentModel;

        $filialDepartments = $model->getFilialDepartmentModel();

        $content = tpl('config/views/filial-departaments', ['filialDepartments' => $filialDepartments]);


        $data = layout('config/config_tpl',
            [
                'content' => $content
            ]);

        return $response->getBody()->write($data);
    }

    /*
     * Вывод департаментов филиала
     * Return: HTML
     */
    public function actionFilialSuperDepartament(Request $request, Response $response)
    {
        $model = $this->FilialDepartmentModel;

        $filialSuperDepartments = $model->getFilialDepartmentModel(null, 'departament');

        $content = tpl('config/views/filial-super-departaments', ['filialSuperDepartments' => $filialSuperDepartments]);


        $data = layout('config/config_tpl',
            [
                'content' => $content
            ]);

        return $response->getBody()->write($data);
    }

    /*
     * Вывод типов пользователей
     * Return: HTML
     */
    public function actionUserTypes(Request $request, Response $response)
    {
        $model = $this->UserTypeModel;

        $userTypes = $model->getUserTypeModel();

        $content = tpl('config/views/user-types', ['userTypes' => $userTypes]);

        $data = layout('config/config_tpl',
            [
                'content' => $content
            ]);

        return $response->getBody()->write($data);
    }

    /*
     * Вывод кабинетов филиала
     * Return: HTML
     */
    public function actionRooms(Request $request, Response $response)
    {
        $model = $this->FilialRoomModel;

        $rooms = $model->getRoomModel();

        $content = tpl('config/views/filial-rooms', ['rooms' => $rooms]);

        $data = layout('config/config_tpl',
            [
                'content' => $content
            ]);

        return $response->getBody()->write($data);
    }

    /*
     * Вывод стандартныx прав доступа к интерфейсам
     * Return: HTML
     */
    public function actionPermissions(Request $request, Response $response)
    {
        $permissionsModel = $this->PermissionsModel;

        $permissions = $permissionsModel->getPermissionsModel();

        $defaultPermissionsModel = $this->PermissionDefaultInterfaceModel;

        $permissions_def_interfaces = $defaultPermissionsModel->getPermissionDefaultInterfaceModel();

        $content = tpl('config/views/permission-interfaces', ['permissions' => $permissions, 'permissions_def_interfaces' => $permissions_def_interfaces]);

        $data = layout('config/config_tpl',
            [
                'content' => $content
            ]);

        return $response->getBody()->write($data);
    }

    /*
     * Вывод стандартные прав доступа отдела к помещениям
     * Return: HTML
     */
    public function actionFilialPermissions(Request $request, Response $response)
    {

        $model = $this->FilialDepartmentModel;

        $filialDepartments = $model->getFilialDepartmentModel();

        $permissionsModel = $this->PermissionsModel;

        $permissions = $permissionsModel->getPermissionsModel();

        $filialDepartmentsRoomsPermissionModel = $this->FilialDepartmentRoomsModel;

        $permissions_def_rooms = $filialDepartmentsRoomsPermissionModel->getFilialDepartmentRoomsPermissionsModel($permissions[0]->id);

        $content = tpl('config/views/permission-filial-rooms', ['filialDepartments' => $filialDepartments, 'permissions_def_rooms' => $permissions_def_rooms]);

        $data = layout('config/config_tpl',
            [
                'content' => $content
            ]);

        return $response->getBody()->write($data);
    }

    /*
     * Вывод типов оборудования
     * Return: HTML
     */
    public function actionEquipmentTypes(Request $request, Response $response)
    {
        $model = $this->EquipmentTypeModel;

        $equipment_types = $model->getEquipmentTypeModel();

        $content = tpl('config/views/equipment-types', ['equipment_types' => $equipment_types]);

        $data = layout('config/config_tpl',
            [
                'content' => $content
            ]);

        return $response->getBody()->write($data);
    }

    /*
     * Вывод пользователей
     * Return: HTML
     */
    public function actionUsers(Request $request, Response $response)
    {
        $model = $this->UsersModel;

        $users = $model->getUsersModel();

        $content = tpl('config/views/users', ['users' => $users]);

        $data = layout('config/config_tpl',
            [
                'content' => $content
            ]);

        return $response->getBody()->write($data);
    }

    /*
     * Вывод сотрудников (Работников)
     * Return: HTML
     */
    public function actionWorkers(Request $request, Response $response)
    {
        $model = $this->WorkersModel;

        $workers = $model->getWorkersModel();

        $content = tpl('config/views/workers', ['workers' => $workers]);


        $data = layout('config/config_tpl',
            [
                'content' => $content
            ]);

        return $response->getBody()->write($data);
    }

    /*
     * Вывод слушаний
     * Return: HTML
     */
    public function actionHearings(Request $request, Response $response)
    {
        $model = $this->HearingsModel;

        $hearings = $model->getHearingsModel();

        $content = tpl('config/views/hearings', ['hearings' => $hearings]);

        $data = layout('config/config_tpl',
            [
                'content' => $content
            ]);

        return $response->getBody()->write($data);
    }

    /*
     * Доступ на территорию
     * Return: HTML
     */
    public function actionUserAccess(Request $request, Response $response)
    {
        $model = $this->UserAccessModel;

        $userAccess = $model->getUserAccessModel();

        $content = tpl('config/views/permission-access', ['useraccess' => $userAccess]);

        $data = layout('config/config_tpl',
            [
                'content' => $content
            ]);

        return $response->getBody()->write($data);
    }

    /**
     * Для работы с камерами
     * @param Request $request
     * @param Response $response
     * @return int HTML
     */
    public function Cameras(Request $request, Response $response)
    {
        $model = $this->CameraModel;

        //добавление
        if ($request->isPost()) {
            $input = $request->getParsedBody();
            $id_cam = (int) $model->Add($input);
            if ( (int)$input['what'] == 1 )
                $model->AddRelationToTerminal($id_cam, (int) $input['equipment_id']);
            else
                $model->AddRelationToTurnstile($id_cam, (int) $input['equipment_id'], (bool) $input['in']);

            $conf = new ffserver($this->container);
            $conf->GenerateConfig($model->GetAll());
        }

        $content = tpl('config/views/cameras',
            ['cameras' => $model->GetAll()]
        );

        $data = layout('config/config_tpl',
            [
                'content' => $content
            ]);

        $response = $response->getBody()->write($data);
        return $response;
    }

    /**
     * рестарт сервиса камер
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function RestartCameras(Request $request, Response $response)
    {
        $model = $this->CameraModel;

        $conf = new ffserver($this->container);
        $conf->GenerateConfig($model->GetAll());

        $response = $response->withJson(['status' => 'success']);
        return $response;
    }

    /**
     * Удаление камеры
     * @param Request $request
     * @param Response $response
     * @param $argv
     * @return Response
     */
    public function delCamera(Request $request, Response $response, $argv)
    {
        $id = (int)$argv['id'];

        $model = $this->CameraModel;
        $model->DelRelations($id);
        $model->delOne($id);

        $response = $response->withJson(['status' => 'success']);
        return $response;
    }


}