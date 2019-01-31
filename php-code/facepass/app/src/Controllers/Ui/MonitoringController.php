<?php

namespace App\Controllers\Ui;

use \Slim\Container;
use App\Controllers\Controller;
use \Slim\Http\Request as Request;
use \Slim\Http\Response as Response;
use App\Models\EquipmentModel;

/**
 * Class MonitoringController
 * @package App\Controllers\Ui
 */
class MonitoringController extends Controller
{

    /**
     * @param Request $request
     * @param Response $response
     * @var $model EquipmentModel
     * @return string html
     */
    public function Main(Request $request, Response $response)
    {
        $active = (int) $request->getAttribute('active');
        $model = $this->EquipmentModel;
        $data = layout('monitoring/index', [
            'terminals' => $model->getTerminal(),
            'active' => $active
        ]);

        $response = $response->getBody()->write($data);
        return $response;
    }
}