<?php

namespace App\Controllers\Ui;

use App\Controllers\Controller;
use App\Models\UserTypeModel;
use App\Models\UsersModel;
use App\Models\NullAccountModel;
use \Slim\Http\Request as Request;
use \Slim\Http\Response as Response;

/**
 * Класс для работы с нулевым аккаунтом
 * Class NullAccountController
 * @package App\Controllers\Ui
 */
class NullAccountController extends Controller
{

    /*
     * Вывод окна нулевого аккаунта
     * Return: JSON
     */
    public function actionIndex(Request $request, Response $response)
    {
        $model = $this->NullAccountModel;
        $nullaccount = $model->getNullAccountModel();

        $modelUserType = $this->UserTypeModel;
        $user_types = $modelUserType->getUserTypesModel();

        $data = layout('null-account/views/index',
            [
                'nullaccount' => $nullaccount,
                'user_types' => $user_types
            ]);

        return $response->getBody()->write($data);
    }

    /*
     * Вывод формы обновления (Выхода) нулевого аккаунта
     * Return: JSON
     */

    public function actionAjaxUpdate(Request $request, Response $response)
    {
        $id = $request->getAttribute('id');
        $modelUser = $this->UsersModel;
        $user = $modelUser->getUsersModel($id);

        $modelUserType = $this->UserTypeModel;
        $user_types = $modelUserType->getUserTypesModel();

        $html = tpl('null-account/ajax/update-form',[
            'user' => $user,
            'user_types' => $user_types
        ]);
        $data['status'] = 'success';
        $data['div'] = 'nullaccountoutform';
        $data['html'] = $html;
        $response = $response->withJson($data);
        return $response;
    }

    /*
     * Вход нулевого аккаунта
     * Return: JSON
     */
    public function addNullAccountControl(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        $model = $this->NullAccountModel;
        $data = $model->addNullAccountModel($data);
        $response = $response->withJson($data);
        return $response;
    }

    /*
    * Выход нулевого аккаунта
    * Return: JSON
    */

    public function updateNullAccountControl(Request $request, Response $response)
    {
        $id = $request->getAttribute('id');
        $data = $request->getParsedBody();
        $model = $this->NullAccountModel;
        $data = $model->updateNullAccountModel($data, $id);
        $response = $response->withJson($data);
        return $response;
    }

}