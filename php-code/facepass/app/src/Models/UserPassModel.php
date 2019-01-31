<?php

namespace App\Models;
use \PDO;

class UserPassModel extends Model
{


    /**
     * @param null $id
     * @param null $filter
     * @return array
     */
    public function findAll($id = null, $filter = null)
    {
        $result = [];
        if ($id != null) {
            $sql = "SELECT user_pass.*,
user_access.info AS access_info, user_access.hearing_id as hearingaccess,
hearing.name AS hearing_name, hearing.code AS hearing_code, hearing.date, hearing.hdate,
users.first_name, users.patronymic, users.surname,
filial_rooms.name AS room_name,
marks.name AS mark_name
            FROM user_pass
            LEFT JOIN user_access ON user_access.id = user_pass.access_id
            LEFT JOIN hearing ON hearing.id = user_access.hearing_id
            LEFT JOIN filial_rooms ON filial_rooms.id = hearing.room_id
			LEFT JOIN marks ON marks.id = user_pass.mark_id
LEFT JOIN users ON users.id=user_pass.user_id
            ";
            if ($id != null)
                $sql .= " WHERE user_pass.user_id='{$id}'";
            if ($filter != null) $sql .= $filter;
            $sql .= " ORDER BY user_pass.id DESC";
            $con = $this->db->prepare($sql);
            $con->execute();
            $result = $con->fetchAll(PDO::FETCH_OBJ);
        } else {
            $result['status'] = 'error';
            $result['message'] = 'Ошибка передачи данных';
        }
        return $result;
    }

    /*
     * Фиксация пропуска пользователя в БД
     * Return: array[]
     */

    public function addUserPassModel($user_id = null, $access_id=null)
    {
        $result = [];
        try {
            $con = $this->db;
            $sql = "INSERT into user_pass (user_id,access_id, date_in, time_in, date_out,time_out, info)
			values (:user_id,:access_id, (select CURRENT_DATE), (select localtime), null, null, null)";
            $query = $con->prepare($sql);
            $query->bindParam('user_id', $user_id);
            $query->bindParam('access_id', $access_id);
            $query->execute();
            $sql = "UPDATE user_access SET status='1' WHERE id='{$access_id}'";
            $query = $con->prepare($sql);
            $query->execute();
            $sql = "SELECT * FROM user_pass WHERE user_id=:user_id AND access_id=:access_id AND date_in=(select CURRENT_DATE)";
            $query = $con->prepare($sql);
            $query->bindParam('user_id', $user_id);
            $query->bindParam('access_id', $access_id);
            $query->execute();
            if ($query->rowCount() >= 1)
            {
                $result['status'] = 'success';
                $result['reload'] = 'true';
            }
            else {
                $result['status'] = 'error';
                $result['message'] = 'Не удается пропустить пользователя';
            }
        }
        catch (Exception $ex) {
            $result['status'] = 'error';
            $result['message'] = 'Неизвестная ошибка (' . $ex->getMessage() . ')';
        }
        return $result;
    }


    /*
     * Получение списка проходов
     */
    public function getUserPasses($id = null) {
        $result = [];
        if($id!=null) {
            $sql = "SELECT user_access.*,
hearing.name AS hearing_name, hearing.code AS hearing_code, hearing.date, hearing.hdate,
users.first_name, users.patronymic, users.surname, users.user_photo
            FROM user_access
            LEFT JOIN hearing ON hearing.id = user_access.hearing_id
LEFT JOIN users ON users.id=user_access.user_id
WHERE NOT user_access.hearing_id=0
            ";
            if($id!=null)
                $sql .= " AND user_access.user_id='{$id}'";
            $con = $this->db->prepare($sql);
            $con->execute();
            $result = $con->fetchAll(PDO::FETCH_OBJ);
        } else {
            $result['status'] = 'error';
            $result['message'] = 'Ошибка передачи данных';
        }
        return $result;
    }


    /*
     * Получение списка доступных комнат для слушания
     * Return: array[]
     * TODO: Проверить, что эта функция лежит в нужной модели
     */

    public function getHearingRoomModel($id = null)
    {
        $result = [];
        try {
            $con = $this->db;
            $sql = "SELECT hearing_rooms.*, filial_rooms.name
			FROM hearing_rooms
			LEFT JOIN filial_rooms ON filial_rooms.id=hearing_rooms.room_id
			WHERE hearing_id='{$id}' AND status='true'";
            $query = $con->prepare($sql);
            $query->execute();
            if ($query->rowCount() >= 1) {
                $result = $query->fetchAll(PDO::FETCH_OBJ);
            } else {
                $result = [];
                $result['status'] = "error";
                $result['message'] = "Нет доступных помещений";
            }
        } catch (Exception $ex) {
            $result['status'] = 'error';
            $result['reload'] = 'Неизвестная ошибка (' . $ex->getMessage() . ')';
        }
        return $result;
    }


    /*
    * Получение списка доступных комнат по специальному доступу посетителя
    * Return: array[]
    * TODO: Проверить, что эта функция лежит в нужной модели
    */
    public function getAccessRoomModel($id = null)
    {
        $result = "";
        try {
            $sql = "SELECT user_acces_rooms.*,
					filial_rooms.name
					FROM user_acces_rooms
					JOIN filial_rooms on filial_rooms.id=user_acces_rooms.room_id
					WHERE user_acces_rooms.status='true' AND user_acces_rooms.access_id='{$id}'";
            $con = $this->db;
            $query = $con->prepare($sql);
            $query->execute();
            if ($query->rowCount() >= 1) {
                $result = $query->fetchAll(PDO::FETCH_OBJ);
            } else {
                $result = [];
                $result['status'] = "error";
                $result['message'] = "Нет специальных помещений";
            }
            $con = null;
        } catch (Exception $ex) {
            $result['status'] = "error";
            $result['message'] = "Неизвестная ошибка.";
        }
        return $result;
    }


    /*
     * Обновление доступа на территорию
     * Return: array[]
     */

    public function updateUserPassModel($id = null)
    {
        $result = [];
        try {
            $sql = "SELECT * FROM user_pass WHERE access_id=:access_id ORDER BY id DESC";
            $con = $this->db;
            $query = $con->prepare($sql);
            $query->bindParam('access_id', $id);
            $query->execute();
            $user_pass = $query->fetchAll(PDO::FETCH_OBJ);
            $sql = "UPDATE user_access SET status='2' WHERE id='{$id}'";
            $query = $con->prepare($sql);
            $query->execute();
            $sql = "UPDATE user_pass SET date_out=(SELECT CURRENT_DATE), time_out=(SELECT localtime) WHERE id=:id";
            $query = $con->prepare($sql);
            $query->bindParam('id', $user_pass[0]->id);
            $query->execute();
            if ($query->rowCount() >= 1)
            {
                $result['status'] = 'success';
                $result['reload'] = 'true';
            }
            else {
                $result['status'] = 'error';
                $result['message'] = 'Не удается пропустить пользователя';
            }
        }
        catch (Exception $ex) {
            $result['status'] = 'error';
            $result['reload'] = 'Неизвестная ошибка (' . $ex->getMessage() . ')';
        }
        return $result;
    }
}