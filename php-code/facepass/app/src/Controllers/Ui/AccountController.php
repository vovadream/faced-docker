<?php

namespace App\Controllers\Ui;

use App\Controllers\Controller;
use App\Models\TopologyModel;
use App\Models\UserMarksModel;
use App\Models\UserImagesModel;
use App\Models\UserPassModel;
use App\Models\UsersModel;
use App\Models\WorkerDepartmentsModel;
use App\Models\WorkerRoomsModel;
use App\Models\WorkersModel;
use Couchbase\UserSettings;
use Slim\Container;
use Slim\Http\Request as Request;
use Slim\Http\Response as Response;

use PDO;

class AccountController extends Controller
{

    /**
     * @var $users UsersModel
     * @var $settings UserSettings
     * @var $userPass UserPassModel
     * @var $workers WorkersModel
     * @var $workerRooms WorkerRoomsModel
     * @var $workerDepartments WorkerDepartmentsModel
     */
    public $users, $settings, $userPass, $marks, $workerRooms, $workers, $workerDepartments, $topology;

    /**
     * AccountController constructor.
     * @param Container $c
     */
    public function __construct(Container $c)
    {
        parent::__construct($c);
        $this->db = $c->get('db');

        $this->userPass = $c->get('UserPassModel');
        $this->settings = $c->get('settings');
        $this->users = $c->get('UsersModel');
        $this->marks = $c->get('UserMarksModel');
        $this->workers = $c->get('WorkersModel');
        $this->workerDepartments = $c->get('WorkerDepartmentsModel');
        $this->workerRooms = $c->get('WorkerRoomsModel');
        $this->topology = $c->get('TopologyModel');
    }

    /**
     * @param $req
     * @param $res
     * @param $args
     * @return string
     */
    public function getIndex(Request $req, Response $res, $args)
    {
        $model = $this->UsersModel;
        return layout('account/index', [
            'users' => $model->findAllFromTo(0, 20)
        ]);
    }

    public function actionLoad(Request $request, Response $response)
    {
        $count = $request->getParam('count');
        $begin = $request->getParam('begin');
        $str = $request->getParam('str');

        $model = $this->UsersModel;

        if(iconv_strlen($str) < 2) {
            $str = null;
        }

        $data['status'] = 'success';
        $data['html'] = tpl('account/ajax/table', [
            'users' => $model->findAllFromTo($begin, $count, $str)
        ]);
        $data['div'] = 'access-control-category';
        $response = $response->withJson($data);
        return $response;
    }


    /**
     * @param Request $req
     * @param Response $res
     * @param $args
     * @return string
     */
    public function getOne(Request $req, Response $res, $args)
    {
        $imagesModel = $this->UserImagesModel;
        $user = $this->users->GetOne($args['id']);
        $userPhotos = $imagesModel->getByUser($args['id']);
        if (empty($user)) return $res->withStatus(404);

        $marksModel = $this->UserMarksModel;
        $userMarks = $marksModel->findByUserId($user->id);

        $allMarks = $marksModel->findAll();

        $worker = null;
        $workerDepartments = null;
        $workerRooms = null;
        $workerRoom = null;

        if ($user->main_class == 1 || $user->user_type_id == 1) {
            $worker = $this->workers->findAll(null, $user->id);

            if ($worker != null && $worker[0]->id != null) {
                $workerDepartments = $this->workerDepartments->findOne($worker[0]->id);
                $workerRooms = $this->workerRooms->getCurrent($worker[0]->id);
                $workerRoom = $this->workerRooms->getRoomForWorker($worker[0]->id);
            }
        }

        $userPassModel = $this->UserPassModel;

        $userPass = $userPassModel->findAll($user->id);
        $topologyModel = $this->TopologyModel;

        $topology = $topologyModel->get();


        return layout('account/view', [
                'user'              => $user,
                'userMarks'         => $userMarks,
                'marks'             => $allMarks,
                'workerDepartments' => $workerDepartments,
                'workerRooms'       => $workerRooms,
                'worker'            => $worker,
                'userPass'          => $userPass,
                'topology'          => $topology,
                'controller'        => $this,
                'userPhotos'        => $userPhotos,
                'workerRoom'        => $workerRoom
            ]
        );
    }

    public function getPhoto(Request $request, Response $response)
    {
        $id = $request->getAttribute('id');
        $imagesModel = $this->UserImagesModel;
        $photo = $imagesModel->getOne($id);
//        var_dump($photo);
        $data['status'] = 'success';
        $data['html'] = '<img src=/images/'.$photo['path'].'>';
        $data['div'] = 'popup';
        $response = $response->withJson($data);
        return $response;
    }

}