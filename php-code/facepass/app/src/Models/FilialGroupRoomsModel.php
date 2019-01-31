<?php


namespace App\Models;

use \PDO;

/**
 * Класс модели списка комнат филиала (Таблица filial_rooms)
 * Class FakeModel
 * @package App\Models
 */
class FilialGroupRoomsModel extends Model
{
    /**
     * @var \PDO
     */
    protected $db;

    protected $table = 'filial_group_rooms';

    /*
     * Сохранение комнаты филиала в БД
     * Return: array[]
     */

    public function addRoomModel($data = null)
    {
        $result = [];
        $errors = "";
        if(!isset($data['name'])) $errors .= "Вы не указали название кабинета!\r\n";
        if(!isset($data['floor_id'])) $errors .= "Вы не указали этаж кабинета!\r\n";
        if(!isset($data['departament_id'])) $errors .= "Вы не указали отдел кабинета!\r\n";
        if($errors!="") {return ['status' => 'error', 'message' => $errors];}

        try {
            $con = $this->db;
            $sql = "INSERT INTO filial_rooms(room, name, parent_id, number, department_id, filial_id, date_create) VALUES(true, :name,:floor_id, -1, :departament_id, :filial_id, (SELECT CURRENT_DATE))";
            $query = $con->prepare($sql);
            $query->bindParam('name', $data['name']);
            $query->bindParam('floor_id', $data['floor_id']);
            $query->bindParam('departament_id', $data['departament_id']);
            $query->bindParam('filial_id', $_SESSION['filial_id']);
            $query->execute();
            $sql = "SELECT * FROM filial_rooms WHERE name=:name AND filial_id=:filial_id";
            $query = $con->prepare($sql);
            $query->bindParam('name', $data['name']);
            $query->bindParam('filial_id', $_SESSION['filial_id']);
            $query->execute();
            if ($query->rowCount() >= 1) {
                $result['status'] = 'success';
                $result['reload'] = 'true';
            } else {
                $result['status'] = 'error';
                $result['message'] = 'Помещение не сохранено!';
            }
        } catch (Exception $ex) {
            $result['status'] = 'error';
            $result['message'] = 'Неизвестная ошибка (' . $ex->getMessage() . ')';
        }
        return $result;
    }

    /*
     * Обновление кабинетов филиала
     * Return: array[]
     */
    public function updateRoomModel($data = null, $id = null)
    {
        $result = [];
        try {
            $sql = "UPDATE filial_rooms SET name=:name,floor=:floor,number=:number,work_time=:work_time,worker_id=:worker_id WHERE id=:id";
            $con = $this->db;
            $query = $con->prepare($sql);
            $query->bindParam('name', $data['name']);
            $query->bindParam('floor', $data['floor']);
            $query->bindParam('number', $data['number']);
            $query->bindParam('work_time', $data['work_time']);
            $query->bindParam('worker_id', $data['worker_id']);
            $query->bindParam('id', $id);
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
     * Получение кабинетов филиала
     * Return: array[]
     */

    public function getRoomModel($id = null)
    {

        $result = [];
        try {
            $sql = "SELECT * FROM filial_group_rooms ";
            if ($id != null)
                $sql .= " WHERE id='{$id}' ";
            $sql .= " ORDER BY id ASC ";

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
            $result['message'] = "Неизвестная ошибка.";
        }
        return $result;
    }
}
