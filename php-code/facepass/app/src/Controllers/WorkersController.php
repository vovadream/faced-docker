<?php

namespace App\Controllers;

use \Slim\Container;
use \Slim\Http\Request as Request;
use \Slim\Http\Response as Response;
use App\Models\WorkersModel;
use App\Models\EquipmentModel;
use App\Controllers\HttpClientController;

/**
 * Класс для вывода всего что связанно с рабочими
 * Class WorkersController
 * @package App\Controllers
 */
class WorkersController
{
    /**
     * @var WorkersModel
     */
    private $worker;

    /**
     * @var EquipmentModel
     */
    private $equipment;

    /**
     * @var HttpClientController
     */
    private $client;

    /**
     * WorkersController constructor.
     * @param Container $c
     */
    public function __construct(Container $c)
    {
        $this->worker = $c->get('WorkersModel');
        $this->equipment = $c->get('EquipmentModel');
        $this->client = $c->get('HttpClientController');
    }

    /**
     * Страница уведомлений
     * @param Request $request
     * @param Response $response
     * @return int
     */
    public function Notifications(Request $request, Response $response)
    {
        //TODO: привести $_SESSION -> to this type.
        $worker = $request->getAttribute('current_user');
        $info['notifications'] = $this->worker->GetNotifications($worker['id']);
        $data = layout('workers/notifications', $info);
        return $response->getBody()->write($data);
    }

    /**
     * Получить 1 уведомление
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response ajax
     */
    public function GetNotify(Request $request, Response $response, $args)
    {
        $id = (int)$args['id'];

        $one = $this->worker->GetOneNotify($id);
        $this->worker->ReadNotify($id);

        //add buttons
        $button = '';
        if (!$one->reply && ($one->type == 4 || $one->type == 5)) {
            $a['id'] = $id;
            $button = tpl('chunks/compare-buttons', $a);
        }

        $data['html'] = $one->action_text.$button;
        $data['status'] = 'success';
        $data['div'] = 'popup';
        $response = $response->withJson($data);
        return $response;
    }

    /**
     * Ответ на уведомление
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function ReplyNotify(Request $request, Response $response, $args)
    {
        $input = $request->getParsedBody();
        $id = (int) $args['id'];
        $reply = (bool) $input['reply'];

        $one = $this->worker->GetOneNotify($id);
        $this->worker->ReplyNotify($id);
        $eq = $this->equipment->FindOne($one->equipment_id, 'id');

        if ($reply)
            $payload['result'] = true;
        else
            $payload['result'] = false;

        if ($one->type == 4) { //compare
            $this->client->SendTerminal($eq, 'compareresult', $payload);
        } elseif ($one->type == 5) { //comparefaces
            $this->client->SendTerminal($eq, 'comparefaceresult', $payload);
        }

        $data['html'] = 'Успешно!';
        $data['status'] = 'success';
        $data['div'] = 'popup';
        $response = $response->withJson($data);
        return $response;
    }

    /**
     * Вёрнёт количество непрочитанных уведомлений
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function NewNotify(Request $request, Response $response)
    {
        $worker = $request->getAttribute('current_user');

        $count = $this->worker->NewsNotify($worker['id']);
        $data['count'] = $count->count;

        $response = $response->withJson($data);
        return $response;
    }
}