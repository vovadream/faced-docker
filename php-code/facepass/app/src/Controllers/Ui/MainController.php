<?php

namespace App\Controllers\Ui;

use App\Controllers\Controller;
use App\Models\MarkModel;
use App\Models\UserMarksModel;
use App\Models\UserPassModel;
use App\Models\UsersModel;
use App\Models\WorkerDepartmentsModel;
use App\Models\WorkerRoomsModel;
use App\Models\WorkersModel;
use Couchbase\UserSettings;
use PDO;
use Slim\Container;
use Slim\Http\Request as Request;
use Slim\Http\Response as Response;

class MainController extends Controller
{
    /**
     * @var $users UsersModel
     * @var $settings UserSettings
     * @var $userPass UserPassModel
     * @var $workers WorkersModel
     * @var $marks MarkModel
     * @var $workerRooms WorkerRoomsModel
     * @var $workerDepartments WorkerDepartmentsModel
     * @var $userMarks UserMarksModel
     */
    public $users, $settings, $userPass, $marks, $workerRooms, $workers, $workerDepartments, $topology, $userMarks, $roomModel;

    /**
     * MainController constructor.
     * @param Container $c
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function __construct(Container $c)
    {
        parent::__construct($c);
        $this->db = $c->get('db');

        $this->userPass = $c->get('UserPassModel');
        $this->settings = $c->get('settings');
        $this->users = $c->get('UsersModel');
        $this->userMarks = $c->get('UserMarksModel');
        $this->marks = $c->get('MarkModel');

        $this->roomModel = $c->get('FilialRoomModel');
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
        $passIn = $this->users->getUserPassInOut(null, "in", null, 100);
        $passOut = $this->users->getUserPassInOut(null, "out", null, 100);

        $marksModel = $this->marks->getMarksModel();

        return layout('main/index', [
            'passIn'     => $passIn,
            'passOut'    => $passOut,
            'marksModel' => $marksModel,
            'controller' => $this
        ]);
    }


    /**
     * @param $data
     * @param $type
     * @return string
     */
    public function getFiltered($data, $type)
    {
        $HTML = "";
        $filter = '';

        $type = ($type != 'in') ? 'out' : 'in';

        if ($data != null) {
            if ($data['dateafter'] != null) {
                if ($type == 'in') $filter .= " AND date_in >= '" . pg_escape_string($data['dateafter']) . "'";
                if ($type == 'out') $filter .= " AND date_out >= '" . pg_escape_string($data['dateafter']) . "'";
            }
            if ($data['datebefore'] != null) {
                if ($type == 'in') $filter .= " AND date_in <= '" . pg_escape_string($data['datebefore']) . "'";
                if ($type == 'out') $filter .= " AND date_out <= '" . pg_escape_string($data['datebefore']) . "'";
            }
            if ($data['timeafter'] != null) {
                if ($type == 'in') $filter .= " AND time_in >= '" . pg_escape_string($data['timeafter']) . "'";
                if ($type == 'out') $filter .= " AND time_out >= '" . pg_escape_string($data['timeafter']) . "'";
            }
            if ($data['timebefore'] != null) {
                if ($type == 'in') $filter .= " AND time_in <= '" . pg_escape_string($data['timebefore']) . "'";
                if ($type == 'out') $filter .= " AND time_out <= '" . pg_escape_string($data['timebefore']) . "'";
            }
            if ($data['surname'] != null) $filter .= " AND users.surname LIKE '%{$data['surname']}%'";
            if ($data['name'] != null) $filter .= " AND users.first_name LIKE '%{$data['name']}%'";
            if ($data['patronymic'] != null) $filter .= " AND users.patronymic LIKE '%{$data['patronymic']}%'";
            if (isset($data['worker_checkbox']) && $data['worker_type_id'] != 0)
                $filter .= " AND workers.department_id = '{$data['worker_type_id']}' OR workers.department_id  IN (SELECT id FROM filial_departament WHERE parent_id = '{$data['worker_type_id']}')";
            if (isset($data['visitor_checkbox']) && $data['visitor_type_id'] != 0) $filter .= " AND (user_types.id = '{$data['visitor_type_id']}' OR user_types.main_class = '{$data['visitor_type_id']}')";
            if ($data['target_room'] != 0) $filter .= " AND filial_rooms.id = '{$data['target_room']}'";
            if ($data['mark'] != 0) $filter .= " AND mark_id = '{$data['mark']}'";
        }

        $marksModel = $this->marks->getMarksModel();

        $passUsers = ($filter != "") ? $this->users->getUserPassInOut($filter, $type) : [];
        $HTML .= tpl('main/filter-result', [
            'cr'         => $this,
            'filter'     => $filter,
            'passUsers'  => $passUsers,
            'marksModel' => $marksModel,
            'type'       => $type
        ]);

        return $HTML;
    }


    /**
     * @param Request $request
     * @param Response $response
     * @return Response|static
     */
    public function getShowMainFilterControl(Request $request, Response $response)
    {
        $type = $request->getAttribute('type');

        $html = tpl('main/filter', [
            'type' => $type,
            'cr'   => $this
        ]);

        $data['status'] = 'success';
        $data['div'] = 'popup';
        $data['html'] = $html;
        $response = $response->withJson($data);
        return $response;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response|static
     */

    public function getShowBigUserPass(Request $request, Response $response)
    {
        $type = $request->getAttribute('type');
        $user_id = $request->getAttribute('user_id');

        $data['status'] = 'success';
        $data['div'] = ($type == 'in') ? 'tableInUserInfo' : 'tableOutUserInfo';
        $data['html'] = $this->showBigUserPassView($user_id, $type);;
        $response = $response->withJson($data);
        return $response;
    }


    /**
     * Отображение отфильтрованных данных для вкладки Главная
     * @param Request $request
     * @param Response $response
     * @return Response|static
     */
    public function showMainFilteredDataControl(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        $type = $request->getAttribute('type');
        $html = $this->getFiltered($data, $type);
        $data['status'] = 'success';
        if ($type == 'in') $data['div'] = 'tableInContent';
        if ($type == 'out') $data['div'] = 'tableOutContent';
        $data['status'] = 'success';
        $data['div'] = 'popup';
        $data['html'] = $html;
        $response = $response->withJson($data);
        return $response;
    }


    /**
     * Вывод блока отображения выбранной записи таблицы входов
     * @param $user_id
     * @param $type
     * @return false|string
     */

    public function showBigUserPassView($user_id, $type)
    {
        $userInfo = $this->users->getUserPassInOut(null, $type, $user_id);

        return tpl('main/big-user', [
            'user_id'  => $user_id,
            'type'     => $type,
            'userInfo' => $userInfo
        ]);
    }

    /**
     * @param $string
     * @return null
     */
    public function sendQuery($string)
    {
        $con = $this->db;
        $query = $con->prepare($string);
        $query->execute();
        if ($query->rowCount() > 0)
            return $query->fetchAll(PDO::FETCH_OBJ);
        else
            return null;
    }


    /**
     * @param Request $request
     * @param Response $response
     * @return Response|static
     */
    public function postInOutContent(Request $request, Response $response)
    {

        $passIn = $this->users->getUserPassInOut(null, "in", null, 100);
        $passOut = $this->users->getUserPassInOut(null, "out", null, 100);

        $marksModel = $this->marks->getMarksModel();

        $inContent = tpl('main/partials/in-out-content', [
            'passInOut'  => $passIn,
            'marksModel' => $marksModel,
            'type'       => 'in'
        ]);

        $outContent = tpl('main/partials/in-out-content', [
            'passInOut'  => $passOut,
            'marksModel' => $marksModel,
            'type'       => 'out'
        ]);

        $data['status'] = 'success';

        $data['content'] = [
            'in'  => $inContent,
            'out' => $outContent
        ];
        $response = $response->withJson($data);
        return $response;
    }
}