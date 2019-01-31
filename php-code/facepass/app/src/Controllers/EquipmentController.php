<?php

namespace App\Controllers;

use \Slim\Container;
use \Slim\Http\Request as Request;
use \Slim\Http\Response as Response;

use App\Models\EquipmentModel;
use App\Models\LoggerModel;
use App\Controllers\HttpClientController;

use DateTime;

/**
 * Класс для вывода всего что связано с устройствами
 * Class EquipmentController
 * @package App\Controllers
 */
class EquipmentController
{
    /**
     * @var EquipmentModel
     */
    private $equipment;

    /**
     * @var LoggerModel
     */
    private $logger;

    /**
     * @var HttpClientController
     */
    private $client;

    /**
     * @var EquipmentModel
     */
    private $model;


    /**
     * EquipmentController constructor.
     * @param Container $c
     */
    public function __construct(Container $c)
    {
        $this->equipment = $c->get('EquipmentModel');
        $this->logger = $c->get('LoggerModel');
        $this->client = $c->get('HttpClientController');
        $this->model = $c->get('EquipmentModel');
    }

    /**
     * Дистанционное открытие турникета
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function OpenTurnstile(Request $request, Response $response)
    {
        $input = $request->getParsedBody();
        $eq_id = (int) $input['eq_id'];
        $in = (bool) $input['in'];
        $error = false;

        $eq = $this->equipment->FindOne($eq_id, 'id');
        if (!$eq)
            $error = 'Устройство не найдено';

        if ($error) {
            $data['result'] = 'fail';
            $data['errors'] = $error;
        } else {
            if ($in) {
                //вход
                $this->client->SendTurnstile($eq, 1, 0);
            } else {
                //выход
                $this->client->SendTurnstile($eq, 1, 0, 2);
            }

            $this->logger->Add([
                'message' => 'Вручную открыт турникет из интерфейса.',
                'equipment_id' => $eq->id,
            ]);

            $data['result'] = 'success';
        }

        $response = $response->withJson($data);
        return $response;
    }

    /**
     * Получение устройств по типу
     * @param Request $request
     * @param Response $response
     * @param $argv
     * @return Response
     */
    public function getByType(Request $request, Response $response, $argv)
    {
        $type = (int) $argv['type'];

        $result['data'] = $this->equipment->getAllByType($type);

        $response = $response->withJson($result);
        return $response;
    }

}