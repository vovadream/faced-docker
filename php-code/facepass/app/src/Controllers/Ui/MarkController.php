<?php

namespace App\Controllers\Ui;

use App\Controllers\Controller;
use App\Models\MarkModel;
use App\Models\UserPassModel;
use Slim\Container;
use Slim\Http\Request as Request;
use Slim\Http\Response as Response;

use PDO;

class MarkController extends Controller
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
     * Добавление пользовательской метки
     * Return: JSON
     */
    public function addUserMarkControl(Request $request, Response $response)
    {
        $user_id = $request->getAttribute('user_id');
        $data = $request->getParsedBody();
        $model = new MarkModel();
        $data = $model->addUserMarkModel($data, $user_id);
        $response = $response->withJson($data);
        return $response;
    }

    /*
     * Обновление пользовательской метки
     * Return: JSON
     */
    public function updateUserMarkControl(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        $model = new MarkModel();
        $data = $model->updateUserMarkModel($data);
        $response = $response->withJson($data);
        return $response;
    }

    /*
     * Обновление пользовательской метки, прикрепленной к слушанию
     * Return: JSON
     */
    public function updateUserMarkPassControl(Request $request, Response $response)
    {
        $user_pass = $request->getAttribute('user_pass');
        $mark_id = $request->getAttribute('mark_id');
        $model = new MarkModel();
        $data = $model->updateUserMarkPassModel($user_pass, $mark_id);
        $response = $response->withJson($data);
        return $response;
    }
}