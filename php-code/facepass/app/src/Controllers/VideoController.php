<?php

namespace App\Controllers;

use \Slim\Container;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use App\Models\VideoModel;
use App\Models\EquipmentModel;

class VideoController
{
    private $model;
    private $equipment;


    public function __construct(Container $c)
    {
        $this->model = $c->get('VideoModel');
        $this->equipment = $c->get('EquipmentModel');
    }

    public function getCameraList(Request $request, Response $response)
    {
        $json = $this->model->getCameraList();
        return $response->withJson($json);
    }

    public function checkFace(Request $request, Response $response) {
        $data = $request->getParsedBody();
        $camera = $this->equipment->getOneCamera($data['cam_id'], 'ff_cam_id');
        $json = $this->model->checkFace($camera, $data);
        return $response->withJson($json);
    }
}