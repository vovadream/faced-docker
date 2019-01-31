<?php

namespace App\Models;

use \PDO;

/**
 * Class WorkersModel
 * @package App\Models
 */
class WorkScheduleModel extends Model
{
    /**
     * @var \PDO
     */
    protected $db;

    private $hearings = [];

    //Задел на будущее

    const WORKING_DAY_TYPE = 1;
    const DAY_OFF_TYPE = 2;
    const SHORT_DAY_TYPE = 3;

    const DEFAULT_FULL_DAY_START_TIME = '08:00';
    const DEFAULT_FULL_DAY_END_TIME = '16:00';
    const DEFAULT_FULL_DAY_BREAK_FOOD_START_TIME = '12:00';
    const DEFAULT_FULL_DAY_BREAK_FOOD_END_TIME = '13:00';
    const DEFAULT_FULL_DAY_LOG_IN_OPEN_FOR = '60';
    const DEFAULT_FULL_DAY_LOG_OUT_OPEN_AFTER = '90';
    const DEFAULT_FULL_DAY_BLOCK_AFTER = '15';
    const DEFAULT_FULL_DAY_FREE = true;

    const DEFAULT_SHORT_DAY_START_TIME = '09:00';
    const DEFAULT_SHORT_DAY_END_TIME = '16:00';
    const DEFAULT_SHORT_DAY_BREAK_FOOD_START_TIME = '12:00';
    const DEFAULT_SHORT_DAY_BREAK_FOOD_END_TIME = '13:00';
    const DEFAULT_SHORT_DAY_LOG_IN_OPEN_FOR = '60';
    const DEFAULT_SHORT_DAY_LOG_OUT_OPEN_AFTER = '90';
    const DEFAULT_SHORT_DAY_BLOCK_AFTER = '15';
    const DEFAULT_SHORT_DAY_FREE = true;


    /*
     * Создание рабочего графика
     * Return: array[]
     */

    public function createWorkScheduleModel($data, $id)
    {

        $result = [];
        $con = $this->db;
        $hearing_array = [];
        try {
            if ($data['workscheduletypeselect']=='1')
            {
                $beginDate = new \DateTime($data['start']);
                $endDate = new \DateTime($data['end']);

                if ($beginDate <= $endDate)
                {
                    $hearing_array = $this->getWorkScheduleHearingArray($id);

                    $interval = new \DateInterval("P1D");
                    while ($beginDate<=$endDate)
                    {

                        for ($hearing_count=0;$hearing_count<count($hearing_array);$hearing_count++)
                        {
                            $today_type=0;
                            $pass_in=null;
                            $pass_out=null;
                            $pass_before=null;
                            $pass_after=null;
                            $stop_print=null;
                            $freepass=null;
                            $dinner_start=null;
                            $dinner_end=null;
                            if ($data['template_type']=='manual')
                            {
                                switch ($beginDate->format('N'))
                                {
                                    case 1: $today_type=$data['daytype_monday']; break;
                                    case 2: $today_type=$data['daytype_tuesday']; break;
                                    case 3: $today_type=$data['daytype_wednesday']; break;
                                    case 4: $today_type=$data['daytype_thursday']; break;
                                    case 5: $today_type=$data['daytype_friday']; break;
                                    case 6: $today_type=$data['daytype_saturday']; break;
                                    case 7: $today_type=$data['daytype_sunday']; break;
                                }
                                switch ($today_type)
                                {
                                    case '1':
                                        $pass_in=$data['pass_in_fullday'];
                                        $pass_out=$data['pass_out_fullday'];
                                        $pass_before=$data['pass_before_fullday'];
                                        $pass_after=$data['pass_after_fullday'];
                                        $stop_print=$data['stop_print_fullday'];

                                        $freepass=$data['freepass_fullday']?'true':'false';

                                        $dinner_start=$data['dinner_start_fullday'];
                                        $dinner_end=$data['dinner_end_fullday'];
                                        break;

                                    case '2':
                                        $pass_in=$data['pass_in_limitedday'];
                                        $pass_out=$data['pass_out_limitedday'];
                                        $pass_before=$data['pass_before_limitedday'];
                                        $pass_after=$data['pass_after_limitedday'];
                                        $stop_print=$data['stop_print_limitedday'];

                                        $freepass=$data['freepass_fullday_limitedday']?'true':'false';

                                        $dinner_start=$data['dinner_start_limitedday'];
                                        $dinner_end=$data['dinner_end_limitedday'];
                                        break;

                                    case '3': break;
                                }
                            }
                            if ($data['template_type']=='automatic')
                            {
                                switch ($beginDate->format('N'))
                                {
                                    case 1: $today_type=$hearing_array[$hearing_count]->monday_day_type; break;
                                    case 2: $today_type=$hearing_array[$hearing_count]->tuesday_day_type; break;
                                    case 3: $today_type=$hearing_array[$hearing_count]->wednesday_day_type; break;
                                    case 4: $today_type=$hearing_array[$hearing_count]->thursday_day_type; break;
                                    case 5: $today_type=$hearing_array[$hearing_count]->friday_day_type; break;
                                    case 6: $today_type=$hearing_array[$hearing_count]->saturday_day_type; break;
                                    case 7: $today_type=$hearing_array[$hearing_count]->sunday_day_type; break;
                                }

                                switch ($today_type)
                                {
                                    case '1':
                                        $pass_in=$hearing_array[$hearing_count]->pass_in_work_day;
                                        $pass_out=$hearing_array[$hearing_count]->pass_out_work_day;
                                        $pass_before=$hearing_array[$hearing_count]->pass_before_work_day;
                                        $pass_after=$hearing_array[$hearing_count]->pass_after_work_day;
                                        $stop_print=$hearing_array[$hearing_count]->stop_print_work_day;

                                        $freepass=$hearing_array[$hearing_count]->free_pass_work_day ? 'true' : 'false';

                                        $dinner_start=$hearing_array[$hearing_count]->dinner_start_work_day;
                                        $dinner_end=$hearing_array[$hearing_count]->dinner_end_work_day;
                                        break;

                                    case '2':
                                        $pass_in=$hearing_array[$hearing_count]->pass_in_short_day;
                                        $pass_out=$hearing_array[$hearing_count]->pass_out_short_day;
                                        $pass_before=$hearing_array[$hearing_count]->pass_before_short_day;
                                        $pass_after=$hearing_array[$hearing_count]->pass_after_short_day;
                                        $stop_print=$hearing_array[$hearing_count]->stop_print_short_day;

                                        $freepass=$hearing_array[$hearing_count]->free_pass_short_day ? 'true' : 'false';

                                        $dinner_start=$hearing_array[$hearing_count]->dinner_start_short_day;
                                        $dinner_end=$hearing_array[$hearing_count]->dinner_end_short_day;
                                        break;

                                    case '3': break;
                                }
                            }
                            $sql = "SELECT * FROM hearing WHERE hdate=:hdate AND worker_id=:worker_id AND filial_id=:filial_id AND departament_id=:departament_id
							AND room_id=:room_id AND filial_rooms_hearing_id=:filial_rooms_hearing_id";
                            $query = $con->prepare($sql);
                            $query->bindParam('room_id',$hearing_array[$hearing_count]->room_id);
                            $beginDate_format=$beginDate->format('Y-m-d');
                            $query->bindParam('hdate', $beginDate_format);
                            $query->bindParam('worker_id', $hearing_array[$hearing_count]->worker_id);
                            $query->bindParam('filial_id', $_SESSION['filial_id']);
                            $query->bindParam('departament_id', $hearing_array[$hearing_count]->departament_id);
                            $query->bindParam('filial_rooms_hearing_id', $hearing_array[$hearing_count]->id);
                            $query->execute();
                            if ($query->rowCount() >= 1)
                            {
                                $existing_hearing = $query->fetchAll(PDO::FETCH_OBJ);
                                $sql = "UPDATE hearing SET pass_in=:pass_in, pass_out=:pass_out, pass_before=:pass_before, pass_after=:pass_after,
								stop_print=:stop_print, freepass=:freepass, dinner_start=:dinner_start, dinner_end=:dinner_end
								WHERE id={$existing_hearing[0]->id}";
                                $query = $con->prepare($sql);
                                $query->bindParam('pass_in', $pass_in);
                                $query->bindParam('pass_out', $pass_out);
                                $query->bindParam('pass_before', $pass_before);
                                $query->bindParam('pass_after', $pass_after);

                                $query->bindParam('stop_print', $stop_print);;
                                $query->bindParam('freepass', $freepass);

                                $query->bindParam('dinner_start', $dinner_start);
                                $query->bindParam('dinner_end',$dinner_end);
                                $query->execute();
                            }
                            else
                            {
                                $sql = "INSERT INTO hearing (room_id,name,code,hdate,worker_id,filial_id,departament_id,pass_in, pass_out,
							pass_before, pass_after, stop_print, freepass, day_type,filial_rooms_hearing_id, dinner_start, dinner_end)
							VALUES (:room_id,:name,(SELECT MAX(id) FROM hearing),:hdate,:worker_id,:filial_id,:departament_id,:pass_in, :pass_out,
							:pass_before, :pass_after, :stop_print, :freepass, '2',:filial_rooms_hearing_id, :dinner_start, :dinner_end)";
                                $query = $con->prepare($sql);
                                $query->bindParam('name', $hearing_array[$hearing_count]->name);
                                $query->bindParam('room_id',$hearing_array[$hearing_count]->room_id);
                                $beginDate_format=$beginDate->format('Y-m-d');
                                $query->bindParam('hdate', $beginDate_format);
                                $query->bindParam('worker_id', $hearing_array[$hearing_count]->worker_id);
                                $query->bindParam('filial_id', $_SESSION['filial_id']);
                                $query->bindParam('departament_id', $hearing_array[$hearing_count]->departament_id);
                                $query->bindParam('pass_in', $pass_in);
                                $query->bindParam('pass_out', $pass_out);
                                $query->bindParam('pass_before', $pass_before);
                                $query->bindParam('pass_after', $pass_after);
                                $query->bindParam('stop_print', $stop_print);
                                $query->bindParam('freepass', $freepass);
                                $query->bindParam('filial_rooms_hearing_id', $hearing_array[$hearing_count]->id);
                                $query->bindParam('dinner_start', $dinner_start);
                                $query->bindParam('dinner_end',$dinner_end);
                                $query->execute();
                            }
                        }
                        $beginDate->add($interval);
                    }
                    $result['status'] = 'success';
                    $result['reload'] = 'true';
                }
            }
            if ($data['workscheduletypeselect']=='2')
            {
                $beginDate = \DateTime::createFromFormat('d-m-Y',  date('d-m-Y'));
                $currentYear = date('Y');
                $endDate = \DateTime::createFromFormat('d-m-Y', "31-12-{$currentYear}");

                $hearing_array=$this->getWorkScheduleHearingArray($id);

                $interval = new \DateInterval("P1D");
                while ($beginDate<=$endDate)
                {
                    for ($hearing_count=0;$hearing_count<count($hearing_array);$hearing_count++)
                    {
                        $today_type=0;
                        $pass_in=null;
                        $pass_out=null;
                        $pass_before=null;
                        $pass_after=null;
                        $stop_print=null;
                        $freepass=null;
                        $dinner_start=null;
                        $dinner_end=null;
                        if ($data['template_type']=='manual')
                        {
                            switch ($data[$beginDate->format('dmY')])
                            {
                                case '1':
                                {
                                    $pass_in=$data['pass_in_fullday'];
                                    $pass_out=$data['pass_out_fullday'];
                                    $pass_before=$data['pass_before_fullday'];
                                    $pass_after=$data['pass_after_fullday'];
                                    $stop_print=$data['stop_print_fullday'];

                                    $freepass=$data['freepass_fullday']?'true':'false';

                                    $dinner_start=$data['dinner_start_fullday'];
                                    $dinner_end=$data['dinner_end_fullday'];
                                    break;
                                }
                                case '2':
                                {
                                    $pass_in=$data['pass_in_limitedday'];
                                    $pass_out=$data['pass_out_limitedday'];
                                    $pass_before=$data['pass_before_limitedday'];
                                    $pass_after=$data['pass_after_limitedday'];
                                    $stop_print=$data['stop_print_limitedday'];

                                    $freepass=$data['freepass_fullday_limitedday']?'true':'false';

                                    $dinner_start=$data['dinner_start_limitedday'];
                                    $dinner_end=$data['dinner_end_limitedday'];
                                    break;
                                }
                                case '3': break;
                            }
                        }
                        if ($data['template_type']=='automatic')
                        {
                            switch ($data[$beginDate->format('dmY')])
                            {
                                case '1':
                                {
                                    $pass_in=$hearing_array[$hearing_count]->pass_in_work_day;
                                    $pass_out=$hearing_array[$hearing_count]->pass_out_work_day;
                                    $pass_before=$hearing_array[$hearing_count]->pass_before_work_day;
                                    $pass_after=$hearing_array[$hearing_count]->pass_after_work_day;
                                    $stop_print=$hearing_array[$hearing_count]->stop_print_work_day;

                                    $freepass=$hearing_array[$hearing_count]->free_pass_work_day?'true':'false';

                                    $dinner_start=$hearing_array[$hearing_count]->dinner_start_work_day;
                                    $dinner_end=$hearing_array[$hearing_count]->dinner_end_work_day;
                                    break;
                                }
                                case '2':
                                {
                                    $pass_in=$hearing_array[$hearing_count]->pass_in_short_day;
                                    $pass_out=$hearing_array[$hearing_count]->pass_out_short_day;
                                    $pass_before=$hearing_array[$hearing_count]->pass_before_short_day;
                                    $pass_after=$hearing_array[$hearing_count]->pass_after_short_day;
                                    $stop_print=$hearing_array[$hearing_count]->stop_print_short_day;

                                    $freepass=$hearing_array[$hearing_count]->free_pass_short_day?'true':'false';

                                    $dinner_start=$hearing_array[$hearing_count]->dinner_start_short_day;
                                    $dinner_end=$hearing_array[$hearing_count]->dinner_end_short_day;
                                    break;
                                }
                                case '3': break;
                            }
                        }
                        if ($data[$beginDate->format('dmY')]!='0')
                        {
                            $sql = "SELECT * FROM hearing WHERE hdate=:hdate AND worker_id=:worker_id AND filial_id=:filial_id AND departament_id=:departament_id
							AND room_id=:room_id AND filial_rooms_hearing_id=:filial_rooms_hearing_id";
                            $query = $con->prepare($sql);
                            $query->bindParam('room_id',$hearing_array[$hearing_count]->room_id);
                            $beginDate_format=$beginDate->format('Y-m-d');
                            $query->bindParam('hdate', $beginDate_format);
                            $query->bindParam('worker_id', $hearing_array[$hearing_count]->worker_id);
                            $query->bindParam('filial_id', $_SESSION['filial_id']);
                            $query->bindParam('departament_id', $hearing_array[$hearing_count]->departament_id);
                            $query->bindParam('filial_rooms_hearing_id', $hearing_array[$hearing_count]->id);
                            $query->execute();
                            if ($query->rowCount() >= 1)
                            {
                                $existing_hearing = $query->fetchAll(PDO::FETCH_OBJ);
                                $sql = "UPDATE hearing SET pass_in=:pass_in, pass_out=:pass_out, pass_before=:pass_before, pass_after=:pass_after,
								stop_print=:stop_print, freepass=:freepass, dinner_start=:dinner_start, dinner_end=:dinner_end
								WHERE id={$existing_hearing[0]->id}";
                                $query = $con->prepare($sql);
                                $query->bindParam('pass_in', $pass_in);
                                $query->bindParam('pass_out', $pass_out);
                                $query->bindParam('pass_before', $pass_before);
                                $query->bindParam('pass_after', $pass_after);

                                $query->bindParam('stop_print', $stop_print);;
                                $query->bindParam('freepass', $freepass);

                                $query->bindParam('dinner_start', $dinner_start);
                                $query->bindParam('dinner_end',$dinner_end);
                                $query->execute();
                            }
                            else
                            {
                                $sql = "INSERT INTO hearing (room_id,name,code,hdate,worker_id,filial_id,departament_id,pass_in, pass_out,
							pass_before, pass_after, stop_print, freepass, day_type,filial_rooms_hearing_id, dinner_start, dinner_end)
							VALUES (:room_id,:name,(SELECT MAX(id) FROM hearing),:hdate,:worker_id,:filial_id,:departament_id,:pass_in, :pass_out,
							:pass_before, :pass_after, :stop_print, :freepass, '2',:filial_rooms_hearing_id, :dinner_start, :dinner_end)";
                                $query = $con->prepare($sql);
                                $query->bindParam('name', $hearing_array[$hearing_count]->name);
                                $query->bindParam('room_id',$hearing_array[$hearing_count]->room_id);
                                $beginDate_format=$beginDate->format('Y-m-d');
                                $query->bindParam('hdate', $beginDate_format);
                                $query->bindParam('worker_id', $hearing_array[$hearing_count]->worker_id);
                                $query->bindParam('filial_id', $_SESSION['filial_id']);
                                $query->bindParam('departament_id', $hearing_array[$hearing_count]->departament_id);
                                $query->bindParam('pass_in', $pass_in);
                                $query->bindParam('pass_out', $pass_out);
                                $query->bindParam('pass_before', $pass_before);
                                $query->bindParam('pass_after', $pass_after);
                                $query->bindParam('stop_print', $stop_print);
                                $query->bindParam('freepass', $freepass);
                                $query->bindParam('filial_rooms_hearing_id', $hearing_array[$hearing_count]->id);
                                $query->bindParam('dinner_start', $dinner_start);
                                $query->bindParam('dinner_end',$dinner_end);
                                $query->execute();
                            }
                        }
                    }
                    $beginDate->add($interval);
                }
                $result['status'] = 'success';
                $result['reload'] = 'true';
            }
        }
        catch (Exception $ex) {
            $result['status'] = 'error';
            $result['message'] = 'Неизвестная ошибка ('. $ex->getMessage().')';
        }
        return $result;
    }

    public function getHearingInItem($items)
    {
            foreach ($items as $item) {
                if ($item['type'] === 'hearing') {
                    $hearingModel = $this->container->FilialRoomsHearingModel;
                    $this->hearings[] = $hearingModel->getOne($item['item']['id']);
                } else {
                    if (!empty($item['children'])) {
                        $this->getHearingInItem($item['children']);
                    }
                }
            }
    }


    /*
     * Получение массива слушаний для заполнения графика
     * Return: array[]
     */

    public function getWorkScheduleHearingArray($id)
    {
        $topologyModel = $this->container->TopologyModel;
        $result = [];
        $item = $topologyModel->getOne($id);
        $parent = $item['parent_id'] > 0 ? $topologyModel->getOne($item['parent_id']) : null;

        try {
            $con = $this->db;
            $sql=null;
            switch ($item['type'])
            {
                case 'hearing': $sql = "SELECT * FROM filial_rooms_hearing WHERE id={$item['element_id']}"; break;
                case 'worker': $sql = "SELECT * FROM filial_rooms_hearing WHERE worker_id={$item['element_id']}"; break;
                case 'room': $sql = "SELECT * FROM filial_rooms_hearing WHERE room_id={$item['element_id']}"; break;
                case 'departament': $sql = "SELECT * FROM filial_rooms_hearing WHERE departament_id IN
				(SELECT id from filial_departament WHERE parent_id={$item['element_id']}) AND room_id in (SELECT id FROM filial_rooms WHERE parent_id={$parent['element_id']} AND room is true)"; break;
                case 'group_rooms':
                {
                    $topology = $topologyModel->tree(0);
                    $this->getHearingInItem($topology);
                    $result = $this->hearings;
                }

                    break;
            }

            if ($item['type']!='group_rooms')
            {
                $query = $con->prepare($sql);
                $query->execute();
                $result = $query->fetchAll(PDO::FETCH_OBJ);
            }
        }
        catch (Exception $ex) {
            $result['status'] = "error";
            $result['message'] = "Неизвестная ошибка.";
        }
        return $result;
    }


    /*
    * Получение массива слушаний для категории
    * Return: array[]
    */


    public function getWorkScheduleHearingInCategoryModel($level=0, $topology=null, $array=null)
    {
        $result = [];
        $HTML = "";
        for($i=0;$i<count($topology);$i++)
        {
            if(!$topology[$i]->room) {
                if (isset($topology[$i]->departaments))
                {
                    for ($j = 0; $j < count($topology[$i]->departaments); $j++) {
                        if($topology[$i]->departaments[$j]->group) {
                            $topology[$i]->departaments[$j]->rooms = $this->getRoomDepartament($topology[$i]->id, $topology[$i]->departaments[$j]->id);
                            for ($rooms_i=0;$rooms_i<count($topology[$i]->departaments[$j]->rooms);$rooms_i++)
                            {
                                $sql = "SELECT * FROM filial_rooms_hearing
								WHERE room_id = '{$topology[$i]->departaments[$j]->rooms[$rooms_i]->id}'";
                                $hearing = $this->sendQuery($sql);
                                for ($array_push_i=0;$array_push_i<count($hearing);$array_push_i++)
                                    array_push($result, $hearing[$array_push_i]);
                            }
                        } else if(isset($topology[$i]->departaments[$j]->sub)) {
                            for($k=0;$k<count($topology[$i]->departaments[$j]->sub); $k++) {
                                $topology[$i]->departaments[$j]->sub[$k]->rooms = $this->getRoomDepartament($topology[$i]->id, $topology[$i]->departaments[$j]->sub[$k]->id);
                                for ($rooms_i=0;$rooms_i<count($topology[$i]->departaments[$j]->sub[$k]->rooms);$rooms_i++)
                                {
                                    $sql = "SELECT * FROM filial_rooms_hearing
									WHERE room_id = '{$topology[$i]->departaments[$j]->sub[$k]->rooms[$rooms_i]->id}'";
                                    $hearing = $this->sendQuery($sql);
                                    for ($array_push_i=0;$array_push_i<count($hearing);$array_push_i++)
                                        array_push($result, $hearing[$array_push_i]);
                                }
                            }
                        }
                    }
                }

                /*
				//select rooms
                $sql = "SELECT * FROM filial_rooms WHERE parent_id={$topology[$i]->id}";
                $rooms = $this->sendQuery($sql);
                if(count($rooms)>=1) {
                    $sql = "SELECT *
					FROM filial_departament
					WHERE id IN (SELECT DISTINCT department_id FROM filial_rooms WHERE parent_id={$topology[$i]->id})";
                    $departaments = $this->sendQuery($sql);
                    for ($j = 0; $j < count($departaments); $j++) {

                        //Выборка кабинетов отдела
                        $sql = "SELECT * FROM filial_rooms WHERE department_id = '{$departaments[$j]->id}' AND parent_id='{$topology[$i]->id}' AND room='true'";
                        $rooms = $this->sendQuery($sql);
                        for ($rooms_i = 0; $rooms_i < count($rooms); $rooms_i++) {
                            //Выборка сотрудников кабинета
                            $sql = "SELECT workers.*, users.surname, users.first_name, users.patronymic
							FROM workers
							LEFT JOIN users ON users.id = workers.user_id
							WHERE room_id = '{$rooms[$rooms_i]->id}'";
                            $workers = $this->sendQuery($sql);
                            for ($workers_i = 0; $workers_i < count($workers); $workers_i++) {
                                //Выборка услуг кабинета
                                $sql = "SELECT *
								FROM filial_rooms_hearing
								WHERE room_id = '{$rooms[$rooms_i]->id}' AND departament_id='{$departaments[$j]->id}' AND worker_id='{$workers[$workers_i]->id}'";
                                $hearing = $this->sendQuery($sql);
                                for ($array_push_i=0;$array_push_i<count($hearing);$array_push_i++)
                                    array_push($result, $hearing[$array_push_i]);
                            }
                        }
                    }
                }

				*/

                if(isset($topology[$i]->sub)&&$topology[$i]->sub!=null) {
                    $HTML .= $this->getWorkScheduleHearingInCategoryModel(($level+1),$topology[$i]->sub,$result);
                }
            }
            if($topology[$i]->room)
            {
                //Выборка сотрудников кабинета
                $sql = "SELECT workers.*, users.surname, users.first_name, users.patronymic
				FROM workers
				LEFT JOIN users ON users.id = workers.user_id
				WHERE room_id = '{$topology[$i]->id}'";
                $workers = $this->sendQuery($sql);
                for ($workers_i = 0; $workers_i < count($workers); $workers_i++) {
                    //Выборка услуг кабинета
                    $sql = "SELECT *
					FROM filial_rooms_hearing
					WHERE room_id = '{$topology[$i]->id}' AND worker_id='{$workers[$workers_i]->id}'";
                    $hearing = $this->sendQuery($sql);
                    for ($array_push_i=0;$array_push_i<count($hearing);$array_push_i++)
                        array_push($result, $hearing[$array_push_i]);
                }
            }
        }
        return $result;
    }

    /*
     * Изменение шаблона для услуги
     * Return: array[]
     */

    public function updateHearingWeekTemplateModel($data=null,$hearing_id=null)
    {
        $result = [];
        try {
            $con = $this->db;
            $sql = "UPDATE filial_rooms_hearing SET pass_in_work_day=:pass_in_work_day, pass_out_work_day=:pass_out_work_day, dinner_start_work_day=:dinner_start_work_day,
			dinner_end_work_day=:dinner_end_work_day, pass_before_work_day=:pass_before_work_day, pass_after_work_day=:pass_after_work_day,
			stop_print_work_day=:stop_print_work_day, free_pass_work_day=:free_pass_work_day, pass_in_short_day=:pass_in_short_day, pass_out_short_day=:pass_out_short_day,
			dinner_start_short_day=:dinner_start_short_day, dinner_end_short_day=:dinner_end_short_day, pass_before_short_day=:pass_before_short_day,
			pass_after_short_day=:pass_after_short_day, stop_print_short_day=:stop_print_short_day, free_pass_short_day=:free_pass_short_day,
			monday_day_type=:monday_day_type, tuesday_day_type=:tuesday_day_type, wednesday_day_type=:wednesday_day_type, thursday_day_type=:thursday_day_type,
			friday_day_type=:friday_day_type, saturday_day_type=:saturday_day_type, sunday_day_type=:sunday_day_type, system_settings='false'
			WHERE id=:hearing_id";
            $query = $con->prepare($sql);
            $query->bindParam('pass_in_work_day', $data['pass_in_fullday']);
            $query->bindParam('pass_out_work_day', $data['pass_out_fullday']);
            $query->bindParam('dinner_start_work_day', $data['dinner_start_fullday']);
            $query->bindParam('dinner_end_work_day', $data['dinner_end_fullday']);
            $query->bindParam('pass_before_work_day', $data['pass_before_fullday']);
            $query->bindParam('pass_after_work_day', $data['pass_after_fullday']);
            $query->bindParam('stop_print_work_day', $data['stop_print_fullday']);
            $query->bindParam('free_pass_work_day', $data['freepass_fullday']);
            $query->bindParam('pass_in_short_day', $data['pass_in_limitedday']);
            $query->bindParam('pass_out_short_day', $data['pass_out_limitedday']);
            $query->bindParam('dinner_start_short_day', $data['dinner_start_limitedday']);
            $query->bindParam('dinner_end_short_day', $data['dinner_end_limitedday']);
            $query->bindParam('pass_before_short_day', $data['pass_before_limitedday']);
            $query->bindParam('pass_after_short_day', $data['pass_after_limitedday']);
            $query->bindParam('stop_print_short_day', $data['stop_print_limitedday']);
            $query->bindParam('free_pass_short_day', $data['freepass_fullday_limitedday']);
            $query->bindParam('monday_day_type', $data['daytype_monday']);
            $query->bindParam('tuesday_day_type', $data['daytype_tuesday']);
            $query->bindParam('wednesday_day_type', $data['daytype_wednesday']);
            $query->bindParam('thursday_day_type', $data['daytype_thursday']);
            $query->bindParam('friday_day_type', $data['daytype_friday']);
            $query->bindParam('saturday_day_type', $data['daytype_saturday']);
            $query->bindParam('sunday_day_type', $data['daytype_sunday']);
            $query->bindParam('hearing_id', $hearing_id);
            $query->execute();
            $result['status'] = 'success';
            $result['reload'] = 'true';
        }
        catch (Exception $ex) {
            $result['status'] = 'error';
            $result['message'] = 'Неизвестная ошибка (' . $ex->getMessage() . ')';
        }
        return $result;
    }
}