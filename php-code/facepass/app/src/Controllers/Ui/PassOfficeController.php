<?php

namespace App\Controllers\Ui;

use App\Controllers\Controller;
use App\Models\HearingsModel;
use \Slim\Http\Request as Request;
use \Slim\Http\Response as Response;

/**
 * Class PassOfficeController
 * @package App\Controllers\Ui
 */
class PassOfficeController extends Controller
{

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function Main(Request $request, Response $response)
    {
        $model = $this->container->UsersModel;

        //добавление
        if ($request->isPost()) {
            $input = $request->getParsedBody();
            $model->AddInviteGuest($input);
        }

        $data = layout('pass-office/index', [
            'invitees' => $model->GetAllInviteesGuests()
        ]);

        $response = $response->getBody()->write($data);
        return $response;
    }

    public function GetHearingByDate(Request $request, Response $response)
    {
        $input = $request->getParsedBody();
        $date = $input['date'];
        $model = $this->HearingsModel;
        $response = $response->withJson([
            'status' => 'success',
            'data' => $model->getByDate($date)
        ]);
        return $response;
    }
}