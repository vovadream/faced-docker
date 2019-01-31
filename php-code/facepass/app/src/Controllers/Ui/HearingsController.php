<?php

namespace App\Controllers\Ui;

use App\Controllers\Controller;
use App\Models\HearingsModel;
use App\Models\FilialRoomModel;
use App\Models\WorkersModel;
use Slim\Container;
use Slim\Http\Request as Request;
use Slim\Http\Response as Response;

use PDO;

class HearingsController extends Controller
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
     * Вывод формы добавления слушания
     * Return: HTML
     */
    public function showAddHearingControl(Request $request, Response $response)
    {
        $filialModel = $this->FilialRoomModel;
        $workersModel = $this->WorkersModel;

        $rooms = $filialModel->getRoomModel();
        $workers = $workersModel->getWorkersModel();


        $data['status'] = 'success';
        $data['div'] = 'popup';
        $data['html'] = tpl('hearings/add', [
            'rooms' => $rooms,
            'workers' => $workers
        ]);
        $response = $response->withJson($data);
        return $response;
    }

    /*
        * Вывод формы изменения слушания
        * Return: HTML
        */
    public function showChangeHearingControl(Request $request, Response $response)
    {
        $id = $request->getAttribute('id');
        $model = $this->HearingsModel;
        $filialModel = $this->FilialRoomModel;
        $workersModel = $this->WorkersModel;

        $rooms = $filialModel->getRoomModel();
        $workers = $workersModel->getWorkersModel();
        $hearings = $model->getHearingsModel($id);

        $response = [];
        $selectedRooms = "SELECT * FROM hearing_rooms WHERE (hearing_id='{$hearings[0]->id}')";
        $selectedRooms = $model->sendQuery($selectedRooms);
        $response['status'] = 'success';
        $response['div'] = 'popup';
        $response['html'] = htmlspecialchars(tpl('hearings/change', [
            'rooms' => $rooms,
            'workers' => $workers,
            'hearings' => $hearings,
            'selectedRooms' => $selectedRooms
        ]));
        return json_encode($response);
    }
    
    /*
    * Обновление слушания
    * Return: JSON
    */
    public function updateHearingControl(Request $request, Response $response)
    {
        $model = $this->HearingsModel;
        $id = $request->getAttribute('id');
        $data = $request->getParsedBody();
        $data = $model->updateHearingModel($data, $id);
        $response = $response->withJson($data);
        return $response;
    }





}