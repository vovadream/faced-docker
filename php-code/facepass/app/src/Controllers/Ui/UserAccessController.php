<?php

namespace App\Controllers\Ui;

use App\Controllers\Controller;
use App\Models\UsersModel;
use App\Models\HearingsModel;
use App\Models\UserAccessModel;
use Slim\Container;
use Slim\Http\Request as Request;
use Slim\Http\Response as Response;

use PDO;

class UserAccessController extends Controller
{

    /*
      * Добавление доступа на территорию
      * Return: JSON
      */
    public function addUserAccessControl(Request $request, Response $response)
    {
        $model = $this->UserAccessModel;
        $data = $request->getParsedBody();
        $data = $model->addUserAccessModel($data);
        $response = $response->withJson($data);
        return $response;
    }

    /*
     * Обновление доступа на территорию
     * Return: JSON
     */
    public function updateUserAccessControl(Request $request, Response $response)
    {
        $model = $this->container->get('UserAccessModel');
        $id = $request->getAttribute('id');
        $data = $request->getParsedBody();
        $data = $model->updateUserAccessModel($data, $id);
        $response = $response->withJson($data);
        return $response;
    }

    /*
     * Вывод формы изменения доступа на территорию
     * Return: HTML
     */
    public function showChangeUserAccessControl(Request $request, Response $response)
    {
        $id = $request->getAttribute('id');
        $model = $this->UserAccessModel;
        $response = [];
        $useraccess = $model->getUserAccessModel($id);
        $response['status'] = 'success';
        $response['div'] = 'popup';
        $response['html'] = tpl('user-access/change', [
            'useraccess' => $useraccess
        ]);

        return json_encode($response);
    }

    /*
     * Вывод формы добавления доступа на территорию
     * Return: HTML
     */
    public function showAddUserAccessControl(Request $request, Response $response)
    {
        $usersModel = $this->UsersModel;
        $hearingsModel = $this->HearingsModel;

        $users = $usersModel->getUsersModel();
        $hearings = $hearingsModel->getHearingsModel();

        $data['status'] = 'success';
        $data['div'] = 'popup';
        $data['html'] = tpl('user-access/add', [
            'users' => $users,
            'hearings' => $hearings
        ]);;
        $response = $response->withJson($data);
        return $response;
    }


}