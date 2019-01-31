<?php


namespace App\Models;

use \PDO;

/**
 * Класс модели списка комнат филиала (Таблица filial_departament_rooms)
 * Class FakeModel
 * @package App\Models
 */
class FilialDepartmentRoomsModel extends Model
{
    /**
     * @var \PDO
     */
    protected $db;


    /*
     * Получение доступов отдела к помещениям
     * Return: array[]
     */

    public function getFilialDepartmentRoomsPermissionsModel($id = null, $departament_id = null)
    {
        $result = [];
        try {
            $sql = "SELECT filial_departament_rooms.*, filial_departament.name AS departament_name, filial_rooms.name AS room_name, filial_rooms.number AS room_number
FROM filial_departament_rooms
LEFT JOIN filial_departament ON filial_departament.id=filial_departament_rooms.departament_id
LEFT JOIN filial_rooms ON filial_rooms.id=filial_departament_rooms.room_id
";
            if ($id!=null) $sql.=" WHERE filial_departament_rooms.departament_id= {$id}";
            /*$filter = '';
            if ($id != null)
                $filter .= " filial_departament_rooms.id='{$id}' ";

            if($departament_id!=null) {
                if($filter!='') $filter .= ' AND ';
                $filter .= " filial_departament_rooms.departament_id='{$departament_id}' ";
            }

            if($filter!='') {
                $sql .= ' WHERE ' . $filter;
            }*/

            $sql .= " ORDER BY filial_departament_rooms.id ASC";
            $con = $this->db;
            $query = $con->prepare($sql);
            $query->execute();
            if ($query->rowCount() >= 1) {
                $result = $query->fetchAll(PDO::FETCH_OBJ);
            } else {
                $result['status'] = "error";
                $result['message'] = "Нет данных.";
            }
        } catch (Exception $ex) {
            $result['status'] = "error";
            $result['message'] = "Неизвестная ошибка (" . $ex->getMessage() . ")";
        }
        return $result;
    }

    /*
     * Создание прав доступа отдела к помещению
     * Return: array[]
     */

    public function addRoomPermissionToFilialDepartmentModel($data = null, $id = null)
    {
        $result = [];
        if ($id == null || $data == null) {
            $result['status'] = 'error';
            $result['message'] = 'Ошибка передачи данных.';
        } else {
            try {
                $sql = "INSERT INTO filial_departament_rooms(departament_id, room_id, status) VALUES(:departament_id, :room_id, :status)";
                $con = $this->db;
                $query = $con->prepare($sql);
                $query->bindParam('departament_id', $id);
                $query->bindParam('room_id', $data['room_id']);
                $query->bindParam('status', $data['status']);
                $query->execute();

                $sql = "SELECT * FROM filial_departament_rooms WHERE departament_id=:departament_id AND room_id=:room_id AND status=:status";
                $query = $con->prepare($sql);
                $query->bindParam('departament_id', $id);
                $query->bindParam('room_id', $data['room_id']);
                $query->bindParam('status', $data['status']);
                $query->execute();
                if ($query->rowCount() >= 1) {
                    $result['status'] = 'success';
                    $result['reload'] = 'true';
                } else {
                    $result['status'] = 'error';
                    $result['div'] = 'alert';
                    $result['message'] = 'Запись не сохранена';
                }
            } catch (Exception $ex) {
                $result['status'] = 'error';
                $result['message'] = 'Неизвестная ошибка (' . $ex->getMessage() . ')';
            }
        }
        return $result;
    }


    /*
     * Обновление права доступа отдела к помещению
     * Return: array[]
     */
    public function updateRoomPermissionToFilialDepartmentModel($data = null, $id = null)
    {
        $result = [];
        try {
            $sql = "UPDATE filial_departament_rooms SET status=:status WHERE id=:id";
            $con = $this->db;
            $query = $con->prepare($sql);
            $query->bindParam('status', $data['status']);
            $query->bindParam('id', $id);
            $query->execute();
            $result['status'] = 'success';
            $result['reload'] = 'true';
        } catch (Exception $ex) {
            $result['status'] = 'error';
            $result['reload'] = 'Неизвестная ошибка (' . $ex->getMessage() . ')';
        }
        return $result;
    }

    /*
    * Получение департамента/отдела по его имени и группе комнат
    */
    public function getFilialDepartmentByName($name, $groupId) {
        $sql = "SELECT * FROM filial_departament_floor AS fdf
                JOIN filial_departament ON filial_departament.name = {$name} AND filial_departament.delete IS FALSE WHERE floor_id = {$groupId}";
        $con = $this->db;
        $query = $con->prepare($sql);

        $query->execute();
        if ($query->rowCount() >= 1) {
            $result = $query->fetchAll(PDO::FETCH_OBJ);
        } else {
            $result = null;
        }
        $con = null;
        return $result;
    }
}
