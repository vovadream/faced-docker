<?php

namespace App\Models;

use \PDO;

class TopologyModel extends Model
{
    const TYPE_FLOOR = 'group_rooms';
    const TYPE_DEPARTAMENT = 'departament';
    const TYPE_ROOM = 'room';
    const TYPE_WORKER = 'worker';
    const TYPE_HEARING = 'hearing';


    /**
     * @var \PDO
     */
    protected $db;

    protected $table = 'topology';


    public function tree($parent = 0)
    {
        $sql = "SELECT topology.* FROM topology WHERE parent_id = :parent_id";
        $con = $this->db->prepare($sql);
        $con->bindParam('parent_id', $parent);
        $con->execute();
        $tree = $con->fetchAll(PDO::FETCH_ASSOC);
        $newTree = [];
        foreach ($tree as $key => $item) {
            $newTree[] = [
                'id' => $item['id'],
                'type' => $item['type'],
                'item' => $this->getItem($item['element_id'], $item['type']),
                'parent' => $item['parent_id'],
                'children' => $this->tree($item['id'])];
        }
        return $newTree;
    }

    public function treeChangeRoom($parent = 0)
    {
        $sql = "SELECT topology.* FROM topology WHERE parent_id = :parent_id";
        $con = $this->db->prepare($sql);
        $con->bindParam('parent_id', $parent);
        $con->execute();
        $tree = $con->fetchAll(PDO::FETCH_ASSOC);
        $newTree = [];
        foreach ($tree as $key => $item) {
            $newTree[] = [
                'id' => $item['id'],
                'type' => $item['type'],
                'item' => $this->getItem($item['element_id'], $item['type']),
                'parent' => $item['parent_id'],
                'children' => $item['type'] !== 'room' ? $this->treeChangeRoom($item['id']) : ''];
        }
        return $newTree;
    }

    public function getRowsForTable($parent = 0, $level)
    {
        $sql = "SELECT topology.* FROM topology WHERE parent_id = :parent_id";
        $con = $this->db->prepare($sql);
        $con->bindParam('parent_id', $parent);
        $con->execute();
        $rowsTree = $con->fetchAll(PDO::FETCH_ASSOC);
        $newTree = [];
        $level++;
        foreach ($rowsTree as $key => $row) {
            $parentDeps = $this->getParentDepartament($row);
            $stepIn = '-';
            $stepOut = '-';
            if ($row['type'] === 'room') {
                $room = $this->getItem($row['id'], 'room');
                $stepIn = $room['step_in'];
                $stepOut = $room['step_out'];
            }
            $item = $this->getItem($row['element_id'], $row['type']);

            $newTree[] = [
                'id' => $row['id'],
                'type' => $row['type'],
                'name' => $item['name'],
                'level' => $level,
                'parentName' => !empty($parentDeps['parent']['name']) ? $parentDeps['parent']['name'] : '-',
                'parentImg' => !empty($parentDeps['parent']['image']) ? $parentDeps['parent']['image'] : '-',
                'rootName' => !empty($parentDeps['root']['name']) ? $parentDeps['root']['name'] : '-',
                'rootImg' => !empty($parentDeps['root']['image']) ? $parentDeps['root']['image'] : '-',
                'stepIn' => $stepIn,
                'stepOut' => $stepOut,
                'children' => $this->getRowsForTable($row['id'], $level)];
        }
        return $newTree;
    }

    public function getItem($id, $type)
    {
        if ($type == self::TYPE_FLOOR) {
            $model = $this->container->FilialGroupRoomsModel;
            return $model->getOne($id);
        }
        if ($type == self::TYPE_DEPARTAMENT) {
            $model = $this->container->FilialDepartmentModel;
            return $model->getOne($id);
        }
        if ($type == self::TYPE_ROOM) {
            $model = $this->container->FilialRoomModel;
            return $model->getOne($id);
        }
        if ($type == self::TYPE_WORKER) {
            $model = $this->container->WorkersModel;
            $result = $model->getOne($id);
            $result['name'] = $result['surname'] . ' ' . $result['first_name'] . ' ' . $result['patronymic'];
            return $result;
        }
        if ($type == self::TYPE_HEARING) {
            $model = $this->container->FilialRoomsHearingModel;
            return $model->getOne($id);
        }
    }

    public function getOneByElementId($elementId, $type)
    {
        $sql = "SELECT * FROM " . $this->table . " WHERE element_id = :elementId AND type = :type";
        $con = $this->db->prepare($sql);
        $con->bindParam('elementId', $elementId);
        $con->bindParam('type', $type);
        $con->execute();
        $row = $con->fetch();
        return $row;
    }


    public function getParentDepartament($item)
    {
        $parentDep = "-";
        $rootDep = "-";

        if ($item['type'] == self::TYPE_FLOOR) {
            return [
                'parent' => $parentDep,
                'root' => $rootDep
            ];
        }


        while ($parentDep == '-' || $rootDep == '-') {
            $item = $this->getParentItem($item['element_id'], $item['type']);
            if ($item['type'] == self::TYPE_FLOOR) {
                return [
                    'parent' => $parentDep,
                    'root' => $rootDep
                ];
            }
            if ($parentDep == '-' && $item['type'] === 'departament') {
                $parentDep = $this->getItem($item['element_id'], $item['type']);
            }

            if ($rootDep == '-' && $item['type'] === 'departament') {
                $isRoot = $this->getParentItem($item['element_id'], $item['type']);
                if ($isRoot['type'] == self::TYPE_FLOOR) {
                    $rootDep = $this->getItem($item['element_id'], $item['type']);
                }
            }

        }
        return [
            'parent' => $parentDep,
            'root' => $rootDep
        ];
    }


    public function getParentId($elementId, $type)
    {
        $sql = "SELECT topology.* FROM topology WHERE element_id = :element_id AND type = :type";
        $con = $this->db->prepare($sql);
        $con->bindParam('element_id', $elementId);
        $con->bindParam('type', $type);
        $con->execute();
        $row = $con->fetch();

        if (!empty($row['parent_id'])) {
            $sql = "SELECT topology.* FROM topology WHERE id = :id";
            $con = $this->db->prepare($sql);
            $con->bindParam('id', $row['parent_id']);
            $con->execute();
            $row = $con->fetch();
            return !empty($row['element_id']) ? $row['element_id'] : 0;
        }


        return 0;
    }

    public function getParentItem($elementId, $type)
    {
        $sql = "SELECT topology.* FROM topology WHERE element_id = :element_id AND type = :type";
        $con = $this->db->prepare($sql);
        $con->bindParam('element_id', $elementId);
        $con->bindParam('type', $type);
        $con->execute();
        $row = $con->fetch();

        if (!empty($row['parent_id'])) {
            $sql = "SELECT topology.* FROM topology WHERE id = :id";
            $con = $this->db->prepare($sql);
            $con->bindParam('id', $row['parent_id']);
            $con->execute();
            $row = $con->fetch();
            return !empty($row) ? $row : null;
        }


        return false;
    }

    public function addTopologyItem($parentId, $elementId, $type)
    {
        $sql = "INSERT INTO topology(parent_id, element_id, type) VALUES(:parent_id, :elementId, :type)";
        $con = $this->db->prepare($sql);;
        $con->bindParam('parent_id', $parentId);
        $con->bindParam('elementId', $elementId);
        $con->bindParam('type', $type);
        $con->execute();
        return ['status' => 'success'];
    }

    public function deleteTopologyItem($id)
    {
        $sql = "DELETE FROM topology WHERE id =:id";
        $con = $this->db->prepare($sql);
        $con->bindParam('id', $id);
        $con->execute();
        return ['status' => 'success'];
    }

    public function deleteTopologyItemByElementIdAndType($id, $type)
    {
        $sql = "DELETE FROM topology WHERE element_id =:id AND type = :type";
        $con = $this->db->prepare($sql);
        $con->bindParam('id', $id);
        $con->bindParam('type', $type);
        $con->execute();
        return ['status' => 'success'];
    }


    public function updateTopologyItem($id, $newParentId = null, $newElementId = null)
    {
        $con = $this->db;
        if (!is_null($newParentId) && !is_null($newElementId)) {
            $sql = "UPDATE topology SET parent_id=:parent_id, element_id=:element_id WHERE id=:id";
            $query = $con->prepare($sql);
            $query->bindParam('parent_id', $newParentId);
            $query->bindParam('id', $id);
            $query->bindParam('element_id', $newElementId);
            $query->execute();
        }
        if (!is_null($newParentId) && is_null($newElementId)) {
            $sql = "UPDATE topology SET parent_id=:parent_id WHERE id=:id";
            $query = $con->prepare($sql);
            $query->bindParam('parent_id', $newParentId);
            $query->bindParam('id', $id);
            $query->execute();
        }
        if (is_null($newParentId) && !is_null($newElementId)) {
            $sql = "UPDATE topology SET element_id=:element_id WHERE id=:id";
            $query = $con->prepare($sql);
            $query->bindParam('element_id', $newElementId);
            $query->bindParam('id', $id);
            $query->execute();
        }

    }

    /**
     * @param int $level
     * @param int $parent_id
     * @param null $departament_id
     * @return array|null
     */

    public function get($level = 0, $parent_id = 0, $departament_id = null)
    {
        $topology = null;
        //Выбираем все этажи parent_id
        $sql = "SELECT filial_rooms.* FROM filial_rooms WHERE filial_rooms.parent_id=:parent_id AND room IS FALSE AND delete IS FALSE ORDER BY id ASC";
        $con = $this->db->prepare($sql);
        $con->bindParam('parent_id', $parent_id);
        $con->execute();

        if ($con->rowCount() >= 1) {
            //Этажи есть
            $topology = $con->fetchAll(PDO::FETCH_OBJ);
            for ($i = 0; $i < count($topology); $i++) {
                $topology[$i] = $this->getFloorTopology($topology[$i]);
                //SubStruct
                if ($parent_id != $topology[$i]->id)
                    $topology[$i]->sub = $this->get(($level + 1), $topology[$i]->id, $departament_id);
            }
        } else if ($con->rowCount() < 1 && $level == 0) {
            $sql = "SELECT filial_rooms.* FROM filial_rooms WHERE filial_rooms.id=:parent_id AND room IS FALSE AND delete IS FALSE ORDER BY id ASC";
            $con = $this->db->prepare($sql);
            $con->bindParam('parent_id', $parent_id);
            $con->execute();
            if ($con->rowCount() > 0) {
                $topology = $con->fetchAll(PDO::FETCH_OBJ);
                $topology[0] = $this->getFloorTopology($topology[0], $departament_id);
                if (!isset($topology[0]->departaments)) $topology = null;
            } else {
                $topology = null;
            }
        }
        return $topology;
    }

    public function getFloorTopology($topology, $departament_id = null)
    {
        //Обработка этажей

        //All departaments on floor

        $sql = "SELECT filial_departament.*
            FROM filial_departament

            ";
        $where = " WHERE filial_departament.id IN (SELECT departament_id FROM filial_departament_floor WHERE floor_id = :floor_id) AND parent_id=0 AND delete IS FALSE ";
        if (isset($departament_id)) {
            $where .= " AND filial_departament.id='{$departament_id}'";
        }
        $sql .= "{$where} ORDER BY id ASC";
        $con = $this->db->prepare($sql);
        $con->bindParam('floor_id', $topology->id);
        $con->execute();
        if ($con->rowCount() > 0) {
            $topology->departaments = $con->fetchAll(PDO::FETCH_OBJ);
            for ($j = 0; $j < count($topology->departaments); $j++) {

                if (!$topology->departaments[$j]->group) {
                    //Выбор sub-departments
                    $sql = "SELECT * FROM filial_departament WHERE parent_id=:parent_id AND id IN (SELECT departament_id FROM filial_departament_floor WHERE floor_id=:floor_id) AND delete IS FALSE ORDER BY id ASC";
                    $con = $this->db->prepare($sql);
                    $con->bindParam('parent_id', $topology->departaments[$j]->id);
                    $con->bindParam('floor_id', $topology->id);
                    $con->execute();
                    if ($con->rowCount() > 0) {
                        $topology->departaments[$j]->sub = $con->fetchAll(PDO::FETCH_OBJ);
                    } else {
                        $topology->departaments[$j]->sub = null;
                    }
                }

                if ($topology->departaments[$j]->group) {
                    $topology->departaments[$j]->rooms = $this->getRoomDepartment($topology->id, $topology->departaments[$j]->id);
                } else if (isset($topology->departaments[$j]->sub)) {
                    for ($k = 0; $k < count($topology->departaments[$j]->sub); $k++) {
                        $topology->departaments[$j]->sub[$k]->rooms = $this->getRoomDepartment($topology->id, $topology->departaments[$j]->sub[$k]->id);
                    }
                }
            }
        } else {
            $topology->departaments = null;
        }
        return $topology;
    }


    public function getRoomDepartment($floor_id, $departament_id)
    {
        //Выбор кабинетов отдела
        $sql = "SELECT * FROM filial_rooms WHERE department_id = :department_id AND parent_id=:parent_id AND room='true' AND delete='false' ORDER BY id ASC";
        $con = $this->db->prepare($sql);
        $con->bindParam('department_id', $departament_id);
        $con->bindParam('parent_id', $floor_id);
        $con->execute();

        if ($con->rowCount() > 0) {
            $rooms = $con->fetchAll(PDO::FETCH_OBJ);

            //Получение информации о кабинете
            for ($k = 0; $k < count($rooms); $k++) {
                //Выбор сотрудников
                $rooms[$k]->workers = $this->getRoomWorkers($rooms[$k]->id);
            }
        } else {
            $rooms = null;
        }
        return $rooms;
    }


    public function getRoomWorkers($id)
    {
        $sql = "SELECT workers.*, users.surname, users.first_name, users.patronymic
                            FROM workers
                            LEFT JOIN users ON users.id = workers.user_id
                            WHERE room_id = :room_id";
        $con = $this->db->prepare($sql);
        $con->bindParam('room_id', $id);
        $con->execute();
        if ($con->rowCount() > 0) {
            $workers = $con->fetchAll(PDO::FETCH_OBJ);
            for ($i = 0; $i < count($workers); $i++) {
                $sql = "SELECT * FROM filial_rooms_hearing WHERE room_id = :room_id AND worker_id = :worker_id";
                $con = $this->db->prepare($sql);
                $con->bindParam('room_id', $id);
                $con->bindParam('worker_id', $workers[$i]->id);
                $con->execute();
                $workers[$i]->hearing = $con->rowCount() > 0 ? $workers[$i]->hearing = $con->fetchAll(PDO::FETCH_OBJ) : null;
            }
            return $workers;
        } else {
            return null;
        }
    }

    public function getTopologyModel($level = 0, $parent_id = 0, $departament_id = null)
    {
        /*
         * if object[i]->room == false
         * object[i] -> [
         * id, name, number, worker_id, filial_id, parent_id, room, departament_id, step_in, step_out,
         * department -> [
         *      main -> [id, parent_id, name, image],
         *      sub -> [id, parent_id, name, image]
         * ],
         * rooms[j] -> [
         *      id, name, number, worker_id, filial_id, parent_id, room, departament_id, room, departament_id, step_in, step_out
         * ],
         * departaments[j] -> [
         *      id, name, filial_id, public, parent_id, group, image, info,
         *      main_departament -> [
         *          id, name, filial_id, public, parent_id, group, image, info
         *      ],
         *      rooms[k] -> [
         *          id, name, number, worker_id, filial_id, parent_id, room, departament_id, step_in, step_out,
         *          workers[o] -> [
         *              hearing[p] -> [],
         *          ],
         *      ]
         * ]
         * ]
         * else
         * object[i] -> [
         * id, name, number, worker_id, filial_id, parent_id, room, departament_id, step_in, step_out,
         * departaments -> [
         *      id, name, filial_id, public, parent_id, group, image, info,
         *      main_departament -> [
         *          id, name, filial_id, public, parent_id, group, image, info
         *      ],
         *      workers -> [],
         *      hearing -> [],
         * ]
         */

        $topology = null;
        //Выбираем все этажи parent_id
        $sql = "SELECT filial_rooms.* FROM filial_rooms WHERE filial_rooms.parent_id=:parent_id AND room IS FALSE AND delete IS FALSE ORDER BY id ASC";
        $con = $this->db->prepare($sql);
        $con->bindParam('parent_id', $parent_id);
        $con->execute();

        if ($con->rowCount() >= 1) {
            //Этажи есть
            $topology = $con->fetchAll(PDO::FETCH_OBJ);
            for ($i = 0; $i < count($topology); $i++) {
                $topology[$i] = $this->getFloorTopology($topology[$i]);
                //SubStruct
                if ($parent_id != $topology[$i]->id)
                    $topology[$i]->sub = $this->getTopologyModel(($level + 1), $topology[$i]->id, $departament_id);
            }
        } else if ($con->rowCount() < 1 && $level == 0) {
            $sql = "SELECT filial_rooms.* FROM filial_rooms WHERE filial_rooms.id=:parent_id AND room IS FALSE AND delete IS FALSE ORDER BY id ASC";
            $con = $this->db->prepare($sql);
            $con->bindParam('parent_id', $parent_id);
            $con->execute();
            if ($con->rowCount() > 0) {
                $topology = $con->fetchAll(PDO::FETCH_OBJ);
                $topology[0] = $this->getFloorTopology($topology[0], $departament_id);
                if (!isset($topology[0]->departaments)) $topology = null;
            } else {
                $topology = null;
            }
        }
        return $topology;
    }


    //Выбираем все  parent_id
    //TODO: Перенсти в FilialRoomModel
    public function getFloors($parentId = 0)
    {
        $topology = null;

        $sql = "SELECT filial_rooms.* FROM filial_rooms WHERE filial_rooms.parent_id=:parent_id AND room IS FALSE AND delete IS FALSE ORDER BY id ASC";
        $con = $this->db->prepare($sql);
        $con->bindParam('parent_id', $parentId);
        $con->execute();
        $topology = $con->fetchAll(PDO::FETCH_OBJ);
        for ($i = 0; $i < count($topology); $i++) {
            $topology[$i] = $this->getFloorTopology($topology[$i]);
        }
        return $topology;
    }

    //Выбираем все этажи parent_id
//    TODO: Перенсти в FilialRoomModel
    public function getDepartaments($parentId = 0)
    {
        $topology = null;

        $sql = "SELECT filial_rooms.* FROM filial_rooms WHERE filial_rooms.parent_id=:parent_id AND room IS FALSE AND delete IS FALSE ORDER BY id ASC";
        $con = $this->db->prepare($sql);
        $con->bindParam('parent_id', $parentId);
        $con->execute();
        $topology = $con->fetchAll(PDO::FETCH_OBJ);
        for ($i = 0; $i < count($topology); $i++) {
            $topology[$i] = $this->getFloorTopology($topology[$i]);
        }
        return $topology;
    }


    /*
     * Редактирование кабинета/помещения
     * Return: array[]
     */

    public function updateGroupRooms($data = null, $group_rooms_id = null)
    {
        $result = [];
        try {
            $con = $this->db;
            $sql = "UPDATE filial_group_rooms SET worker_id=:worker_id, parent_id=:parent_id, department_id=:department_id, name=:name WHERE id=:id";
            $query = $con->prepare($sql);
            $query->bindParam('name', $data['name']);
            $query->bindParam('worker_id', $data['worker_id']);
            $query->bindParam('parent_id', $data['parent_id']);
            $query->bindParam('department_id', $data['department_id']);
            $query->bindParam('id', $group_rooms_id);
            $query->execute();
            $result['status'] = 'success';
            $result['reload'] = 'true';
        } catch (Exception $ex) {
            $result['status'] = 'error';
            $result['message'] = 'Неизвестная ошибка (' . $ex->getMessage() . ')';
        }
        return $result;
    }

    /*
    * Редактирование кабинета/помещения
    * Return: array[]
    */

    public function updateDepartament($data, $depId)
    {
        $result = [];
        try {
            $con = $this->db;
            $sql = "UPDATE filial_departament SET parent_id=:parent_id, public=:public, filial_id= 1, name=:name WHERE id=:id";
            $query = $con->prepare($sql);

            $query->bindParam('public', $data['public']);
            $query->bindParam('parent_id', $data['parent_id']);
            $query->bindParam('name', $data['name']);
            $query->bindParam('id', $depId);
            $query->execute();
            $result['status'] = 'success';
            $result['reload'] = 'true';
        } catch (Exception $ex) {
            $result['status'] = 'error';
            $result['message'] = 'Неизвестная ошибка (' . $ex->getMessage() . ')';
        }
        return $result;
    }

    /*
     * Редактирование кабинета/помещения
     * Return: array[]
     */

    public function updateTopologyObjectModel($data = null, $room_id = null)
    {
        $result = [];
        try {
            $con = $this->db;
            $sql = "UPDATE filial_rooms SET worker_id=:worker_id, parent_id=:parent_id, department_id=:department_id, number=:number, name=:name WHERE id=:id";
            $query = $con->prepare($sql);
            $query->bindParam('name', $data['name']);
            $query->bindParam('number', $data['number']);
            $query->bindParam('worker_id', $data['worker_id']);
            $query->bindParam('parent_id', $data['parent_id']);
            $query->bindParam('department_id', $data['department_id']);
            $query->bindParam('id', $room_id);
            $query->execute();
            $result['status'] = 'success';
            $result['reload'] = 'true';
        } catch (Exception $ex) {
            $result['status'] = 'error';
            $result['message'] = 'Неизвестная ошибка (' . $ex->getMessage() . ')';
        }
        return $result;
    }


    /*
     * Удалить услугу
     * Return: array[]
     * TODO: Возможно перенести?
     */

    public function deleteHearingTopologyModel($hearing_id = null)
    {
        $result = [];
        try {
            $con = $this->db;
            $sql = "DELETE FROM filial_rooms_hearing WHERE id=:hearing_id";
            $query = $con->prepare($sql);
            $query->bindParam('hearing_id', $hearing_id);
            $query->execute();
            $result['status'] = 'success';
            $result['reload'] = 'true';
        } catch (Exception $ex) {
            $result['status'] = 'error';
            $result['message'] = 'Неизвестная ошибка (' . $ex->getMessage() . ')';
        }
        return $result;
    }

    /*
     * Добавить услугу
     * Return: array[]
     * TODO: Возможно перенести?
     */

    public function addHearingTopologyModel($data = null, $room_id = null, $worker_id = null)
    {
        $result = [];
        try {
            $con = $this->db;
            $sql = "INSERT into filial_rooms_hearing (departament_id,room_id, worker_id, name, public, pass_in_work_day, pass_out_work_day, dinner_start_work_day,
			dinner_end_work_day, pass_before_work_day, pass_after_work_day, stop_print_work_day, free_pass_work_day, pass_in_short_day, pass_out_short_day, dinner_start_short_day,
			dinner_end_short_day, pass_before_short_day, pass_after_short_day, stop_print_short_day, free_pass_short_day,
			monday_day_type, tuesday_day_type, wednesday_day_type, thursday_day_type, friday_day_type, saturday_day_type, sunday_day_type, system_settings)
				VALUES ((SELECT department_id FROM workers WHERE workers.id=:worker_id),:room_id, :worker_id, :name, 'true',
				'08:00', '16:00', '12:00', '13:00', '60', '90', '15', 'true', '09:00', '16:00', '12:00', '13:00', '60', '90', '15', 'true',
			'1', '1', '1', '1', '2', '3', '3', 'true')";
            $query = $con->prepare($sql);
            $query->bindParam('room_id', $room_id);
            $query->bindParam('worker_id', $worker_id);
            $query->bindParam('name', $data['name']);

            $query->execute();
            $id = $this->db->lastInsertId();


            $this->addTopologyItem($data['topology_parent'], $id, 'hearing');
            $result['status'] = 'success';
            $result['reload'] = 'true';
        } catch (Exception $ex) {
            $result['status'] = 'error';
            $result['message'] = 'Неизвестная ошибка (' . $ex->getMessage() . ')';
        }
        return $result;
    }

    /*
     * Удаление сотрудника из кабинета
     * Return: array[]
     * TODO: Возможно перенести?
     */

    public function unlinkTopologyWorkerModel($data)
    {
        $result = [];
        try {
            $con = $this->db;
            $sql = "UPDATE workers SET room_id=NULL WHERE id=:id";
            $query = $con->prepare($sql);
            $query->bindParam('id', $data['worker_id']);
            $query->execute();

            $sql = "DELETE FROM filial_rooms_hearing WHERE worker_id=:worker_id AND room_id=:room_id";
            $con = $this->db->prepare($sql);
            $con->bindParam('room_id', $data['room_id']);
            $con->bindParam('worker_id', $data['worker_id']);
            $con->execute();

            $this->deleteTopologyItem($data['topology_parent']);

            $result['status'] = 'success';
            $result['reload'] = 'true';
        } catch (Exception $ex) {
            $result['status'] = 'error';
            $result['message'] = 'Неизвестная ошибка (' . $ex->getMessage() . ')';
        }
        return $result;
    }

    // TODO: Возможно перенести?
    public function topologyDeleteRoom($data)
    {
        $result = [];
        $sql = "UPDATE filial_rooms SET delete='true', date_delete=(SELECT CURRENT_DATE) WHERE id=:room_id";
        $con = $this->db->prepare($sql);
        $con->bindParam('room_id', $data['room_id']);
        $con->execute();


        $sql = "SELECT * FROM workers WHERE room_id=:room_id";
        $con = $this->db->prepare($sql);
        $con->bindParam('room_id', $data['room_id']);
        $con->execute();
        if ($con->rowCount() > 0) {
            $workers = $con->fetchAll(PDO::FETCH_OBJ);
            for ($i = 0; $i < count($workers); $i++) {
                $this->unlinkTopologyWorkerModel(['worker_id' => $workers[$i]->id, 'room_id' => $data['room_id']]);
            }
        }
        return $result;
    }

    // TODO: Возможно перенести?
    public function topologyDeleteDepartament($data)
    {
        $result = [];
        $sql = "DELETE FROM filial_departament_floor WHERE departament_id=:departament_id AND floor_id=:floor_id";
        $con = $this->db->prepare($sql);
        $con->bindParam('departament_id', $data['departament_id']);
        $con->bindParam('floor_id', $data['floor_id']);
        $con->execute();


        //Выбор кабинетов
        $sql = "SELECT id AS room_id FROM filial_rooms WHERE department_id=:departament_id AND parent_id=:floor_id";
        $con = $this->db->prepare($sql);
        $con->bindParam('departament_id', $data['departament_id']);
        $con->bindParam('floor_id', $data['floor_id']);
        $con->execute();
        if ($con->rowCount() > 0) {
            //Удаление кабинетов, сотрудников, услуг
            $rooms = $con->fetchAll(PDO::FETCH_OBJ);
            for ($i = 0; $i < count($rooms); $i++)
                $this->topologyDeleteRoom(['room_id' => $rooms[$i]->room_id]);
        }

        //Проверка того, есть ли еще связи
        $sql = "SELECT * FROM filial_departament_floor WHERE departament_id=:departament_id";
        $con = $this->db->prepare($sql);
        $con->bindParam('departament_id', $data['departament_id']);
        $con->execute();
        if ($con->rowCount() == 0) {
            //Если связей нет - отключаем отдел/депаратмент
            $sql = "UPDATE filial_departament SET delete='true', date_delete = (SELECT CURRENT_DATE) WHERE id=:departament_id";
            $con = $this->db->prepare($sql);
            $con->bindParam('departament_id', $data['departament_id']);
            $con->execute();
        }
        return $result;
    }

    // TODO: Возможно перенести?
    public function topologyCheckDeleteDepartament($data)
    {
        $result = [];
        $result['status'] = 'success';
        $result['reload'] = 'true';
        $result['departament'] = $this->topologyDeleteDepartament($data);
        return $result;
    }

    // TODO: Возможно перенести?
    public function topologyDeleteFloor($data)
    {

        //Установка метки удаления этажа
        $sql = "UPDATE filial_group_rooms SET delete='true', date_delete = CURRENT_DATE WHERE id=:floor_id";
        $con = $this->db->prepare($sql);
        $con->bindParam('floor_id', $data['floor_id']);
        $con->execute();

        //Удаление департаментов этажа
        $sql = "SELECT id FROM filial_departament_floor WHERE floor_id=:floor_id";
        $con = $this->db->prepare($sql);
        $con->bindParam('floor_id', $data['floor_id']);
        $con->execute();
        if ($con->rowCount() > 0) {
            $departaments = $con->fetchAll(PDO::FETCH_OBJ);
            for ($i = 0; $i < count($departaments); $i++) {
                $this->topologyCheckDeleteDepartament(['floor_id' => $data['floor_id'], 'departament_id' => $departaments[$i]->id]);
            }
        }

        //Удаление подкатегорий этажа
        $sql = "SELECT id FROM filial_rooms WHERE parent_id=:floor_id";
        $con = $this->db->prepare($sql);
        $con->bindParam('floor_id', $data['floor_id']);
        $con->execute();
        if ($con->rowCount() > 0) {
            $floor = $con->fetchAll(PDO::FETCH_OBJ);
            for ($i = 0; $i < count($floor); $i++) {
                $this->topologyCheckDeleteDepartament(['floor_id' => $floor[$i]->id]);
            }
        }

        $this->deleteTopologyItem();

    }

    // TODO: Возможно перенести?
    public function addWorkerToRoom($data)
    {
        $sql = "UPDATE workers SET room_id=:room_id WHERE id=:id";
        $con = $this->db->prepare($sql);
        $con->bindParam('room_id', $data['room_id']);
        $con->bindParam('id', $data['worker_id']);
        $con->execute();

        $this->deleteTopologyItemByElementIdAndType($data['worker_id'], 'worker');
        $this->addTopologyItem($data['topology_parent'], $data['worker_id'], 'worker');
        $result = [];
        $result['status'] = 'success';
        $result['reload'] = 'true';
      
        return $result;
    }

    // TODO: Возможно перенести?
    public function checkAddFilialDepartamentToFloor($floor_id, $data)
    {
        $result = [];
        $result['name'] = $data['name'];
        $departamentRoomsModel = $this->container->FilialDepartmentRoomsModel;

        if (isset($data['name']) && $data['name'] != '') {
            if (isset($data['id']) && $data['id'] != '') {
                //Проверка соответствия указанного названия с "оригинальным"
                $departament = $departamentRoomsModel->getFilialDepartmentByName($data['name'], $floor_id);
                if (isset($departament)) {
                    $result['status'] = 'error';
                    $result['message'] = 'Данный департамент уже размещен на этом этаже.';
                } else {
                    $this->addFilialDepartamentToFloor($floor_id, $departament['id'], $data['topology_parent']);
                    $result['status'] = 'success';
                    $result['reload'] = 'true';
                }
            } else {
                //Проверка того, что в системе нет дублирующихся департаментов
                $sql = "SELECT * FROM filial_departament WHERE name=:name AND delete IS FALSE";
                $con = $this->db->prepare($sql);
                $con->bindParam('name', $data['name']);
                $con->execute();
                if ($con->rowCount() > 0) {
                    $result['status'] = 'error';
                    $result['message'] = 'Департамент/отдел с таким именем уже существует';
                } else {
                    //Создаём департамент
                    $departamentModel = $this->container->FilialDepartmentModel;
                    $makeDepartament = $departamentModel->addFilialDepartmentModel($data);
                    if ($makeDepartament['status'] == 'success') {
                        //Сохраняем данные
                        $this->addFilialDepartamentToFloor($floor_id, $makeDepartament['id'], $data['topology_parent']);
                        if (isset($data['parent_id'])) $result['parent_id'] = $data['parent_id'];
                        $result['status'] = 'success';
                        $result['reload'] = 'true';
                    } else {
                        $result['status'] = 'error';
                        $result['message'] = 'Ошибка создания департамента.';
                    }
                }
            }
        } else {
            $result['status'] = 'error';
            $result['message'] = 'Вы не ввели имя департамента';
        }
        return $result;
    }

    // TODO: Возможно перенести?
    public function checkAddFilialSubdepartament($data)
    {
        $result = [];
        $result['name'] = $data['name'];


        if (isset($data['name']) && $data['name'] != '') {
            //Проверка того, что в системе нет дублирующихся департаментов
            $sql = "SELECT * FROM filial_departament WHERE name=:name AND delete IS FALSE";
            $con = $this->db->prepare($sql);
            $con->bindParam('name', $data['name']);
            $con->execute();
            if ($con->rowCount() > 0) {
                $result['status'] = 'error';
                $result['message'] = 'Департамент/отдел с таким именем уже существует';
            } else {
                //Создаём департамент
                $departamentModel = $this->container->FilialDepartmentModel;
                $makeDepartament = $departamentModel->addFilialDepartmentModel($data);
                if ($makeDepartament['status'] == 'success') {
                    $this->addTopologyItem($data['topology_parent'], $makeDepartament['id'], 'departament');
                    $result['status'] = 'success';
                    $result['reload'] = 'true';
                } else {
                    $result['status'] = 'error';
                    $result['message'] = 'Ошибка создания департамента.';
                }
            }
        } else {
            $result['status'] = 'error';
            $result['message'] = 'Вы не ввели имя департамента';
        }
        return $result;
    }


    public function addFilialDepartamentToFloor($floor_id, $departament_id, $topologyParent)
    {
        $sql = 'INSERT INTO filial_departament_floor(floor_id, departament_id) VALUES(:floor_id, :departament_id)';
        $con = $this->db->prepare($sql);
        $con->bindParam('floor_id', $floor_id);
        $con->bindParam('departament_id', $departament_id);
        $con->execute();
        $con = null;
        $this->addTopologyItem($topologyParent, $departament_id, 'departament');
    }


    // TODO: Возможно перенести?
    public function checkAddFloor($data)
    {
        $result = [];
        $sql = "SELECT * FROM filial_group_rooms WHERE name=:name AND delete=false";
        $con = $this->db->prepare($sql);
        $con->bindParam('name', $data['name']);
        $con->execute();
        if ($con->rowCount() > 0) {
            $result['status'] = 'error';
            $result['message'] = 'Группа комнат с таким названием уже существует.';
        } else {
            $addGroupRooms = $this->addGroupRooms($data);
            $this->addTopologyItem($data['topology_parent'], $addGroupRooms['id'], 'group_rooms');
            $result['status'] = $addGroupRooms['status'];
            $result['message'] = (isset($addGroupRooms['message'])) ? $addGroupRooms['message'] : 'Floor added';
            $result['reload'] = 'true';
        }
        return $result;
    }

    // TODO: Возможно перенести?
    public function checkAddRoomToDepartament($data)
    {
        $roomModel = $this->container->FilialRoomModel;
        $result = [];
        $sql = "SELECT * FROM filial_rooms WHERE name=:name AND delete IS FALSE";
        $con = $this->db->prepare($sql);
        $con->bindParam('name', $data['name']);
        $con->execute();
        if ($con->rowCount() > 0) {
            $result['status'] = 'error';
            $result['message'] = 'Кабинет с таким названием уже существует.';
        } else {
            $addRoom = $roomModel->addRoomModel($data);
            $this->addTopologyItem($data['topology_parent'], $addRoom['id'], 'room');
            $result['status'] = $addRoom['status'];
            $result['message'] = (isset($addRoom['message'])) ? $addRoom['message'] : 'Room added';
            $result['reload'] = 'true';
        }
        return $result;
    }

    public function checkAddRoomToSubdepartament($data)
    {
        $result = [];
        $sql = "SELECT * FROM filial_rooms WHERE name=:name AND delete IS FALSE";
        $con = $this->db->prepare($sql);
        $con->bindParam('name', $data['name']);
        $con->execute();
        if ($con->rowCount() > 0) {
            $result['status'] = 'error';
            $result['message'] = 'Кабинет с таким названием уже существует.';
        } else {
            $roomModel = $this->container->FilialRoomModel;
            $addRoom = $roomModel->addRoomModel($data);
            $result['status'] = $addRoom['status'];
            $result['message'] = (isset($addRoom['message'])) ? $addRoom['message'] : 'Room added';
            $result['reload'] = 'true';
        }
        return $result;
    }

    // TODO: Возможно перенести?
    public function getWorkerInDepartament($departament_id)
    {
        $result = [];
        //Возможно несколько подуровней, здесь один подуровень TODO
        $sql = "SELECT * FROM filial_departament WHERE id IN ({$departament_id}, (SELECT id FROM filial_departament WHERE parent_id={$departament_id}))";
        $con = $this->db->prepare($sql);
        $con->execute();
        if ($con->rowCount() > 0) {
            $result['status'] = 'success';
            $result['departaments'] = $con->fetchAll(PDO::FETCH_OBJ);
            for ($i = 0; $i < count($result['departaments']); $i++) {
                $sql = "SELECT workers.*, users.first_name, users.surname, users.patronymic
FROM workers
 LEFT JOIN users ON users.id = workers.user_id
WHERE workers.department_id = {$result['departaments'][$i]->id} AND room_id IS NULL";
                $con = $this->db->prepare($sql);
                $con->execute();
                $result['departaments'][$i]->workers = ($con->rowCount() > 0) ? $con->fetchAll(PDO::FETCH_OBJ) : null;
            }
        } else {
            $result['status'] = 'success';
            $result['departaments'] = null;
        }
        return $result;
    }

    // TODO: Возможно перенести?
    public function addGroupRooms($data)
    {
        $errors = "";
        if (!isset($data['name'])) $errors .= "Вы не заполнили название Категории/Подкатегории.";
        if ($errors != "") {
            return ['status' => 'error', 'message' => $errors];
        }

        $sql = "INSERT INTO filial_group_rooms(name, filial_id, parent_id, date_create) VALUES(:name, :filial_id, :parent_id, (SELECT CURRENT_DATE))";
        $parent_id = (isset($data['floor_id'])) ? $data['floor_id'] : 0;
        $con = $this->db->prepare($sql);
        $con->bindParam('name', $data['name']);
        $con->bindParam('filial_id', $_SESSION['filial_id']);
        $con->bindParam('parent_id', $parent_id);
        $con->execute();
        $id = $this->db->lastInsertId();
        return ['id' => $id, 'status' => 'success'];
    }
}