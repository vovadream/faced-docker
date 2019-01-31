<?php


namespace App\Models;

use \PDO;

/**
 * Класс модели списка меток (Таблица marks)
 * Class FakeModel
 * @package App\Models
 */
class UserAccessModel extends Model
{
    /**
     * @var \PDO
     */
    protected $db;


    /*
     * Получение списка доступов
     * Return: array[]
     */

    public function getUserAccessModel($id = null) {
        $result = [];
        try {
            $sql = "SELECT user_access.*,
					hearing.name as hearingname, users.first_name, users.patronymic, users.surname
					FROM user_access
					LEFT JOIN users on user_access.user_id=users.id
					LEFT JOIN hearing on user_access.hearing_id=hearing.id";
            if($id != null)
                $sql .= " WHERE user_access.id='{$id}'";
            $sql .= " ORDER BY user_access.id ASC";
            $con = $this->db;
            $query = $con->prepare($sql);
            $query->execute();
            if($query->rowCount()>=1) {
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


    /*
     * Сохранение доступа на территорию в БД
     * Return: array[]
     */

    public function addUserAccessModel($data = null)
    {
        $result = [];
        try {
            $con = $this->db;
            $sql = "SELECT * FROM user_access WHERE user_id=:user_id AND hearing_id=:hearing_id";
            $query = $con->prepare($sql);
            $query->bindParam('user_id', $data['user_id']);
            $query->bindParam('hearing_id', $data['hearing_id']);
            $query->execute();
            if ($query->rowCount() >= 1)
            {
                $result['status'] = 'error';
                $result['message'] = 'Данный пользователь уже имеет доступ к этому слушанию!';
            }
            else
            {
                $sql = "INSERT into user_access (user_id,hearing_id,status) values (:user_id,:hearing_id,'0')";
                $query = $con->prepare($sql);
                $query->bindParam('user_id', $data['user_id']);
                $query->bindParam('hearing_id', $data['hearing_id']);
                $query->execute();
                $sql = "SELECT * FROM user_access WHERE user_id=:user_id AND hearing_id=:hearing_id";
                $query = $con->prepare($sql);
                $query->bindParam('user_id', $data['user_id']);
                $query->bindParam('hearing_id', $data['hearing_id']);
                $query->execute();
                $access = $query->fetchAll(PDO::FETCH_OBJ);
                if ($query->rowCount() >= 1) {
                    $sql = 'SELECT * FROM hearing_rooms WHERE hearing_id=:hearing_id';
                    $query = $con->prepare($sql);
                    $query->bindParam('hearing_id', $data['hearing_id']);
                    $query->execute();
                    $hearing_rooms = $query->fetchAll(PDO::FETCH_OBJ);
                    if (isset($hearing_rooms[0]->id))
                    {
                        $sql = 'INSERT INTO user_acces_rooms (access_id, room_id, status) VALUES ';
                        $values = '';
                        for ($i = 0; $i < count($hearing_rooms); $i++) {
                            if ($values != '') $values .= ",";
                            $values .= "('{$access[0]->id}', '{$hearing_rooms[$i]->room_id}', '{$hearing_rooms[$i]->status}')";
                        }
                        $sql .= $values;
                        $query = $con->prepare($sql);
                        $query->execute();
                    }
                    $result['status'] = 'success';
                    $result['reload'] = 'true';
                } else {
                    $result['status'] = 'error';
                    $result['message'] = 'Доступ не сохранен';
                }
            }
        }
        catch (Exception $ex) {
            $result['status'] = 'error';
            $result['message'] = 'Неизвестная ошибка (' . $ex->getMessage() . ')';
        }
        return $result;
    }

    /*
     * Обновление доступа на территорию
     * Return: array[]
     */

    public function updateUserAccessModel($data = null, $id = null)
    {
        $result = [];
        try {
            /*
            $sql = "UPDATE hearing SET room_id=:room_id, name=:name, hdate=:hdate,
            worker_id=:worker_id, time=:time, code=:code WHERE id=:id";
            $con = $this->db;
            $query = $con->prepare($sql);
            $query->bindParam('room_id', $data['room_id']);
            $query->bindParam('name', $data['name']);
            $query->bindParam('hdate', pg_escape_string($data['hdate']));
            $query->bindParam('worker_id', $data['worker_id']);
            $query->bindParam('time', $data['time']);
            $query->bindParam('code', $data['code']);
            $query->bindParam('id', $id);
            $query->execute();
            $sql = "SELECT * FROM hearing_rooms WHERE hearing_id='{$id}'";
            $query = $con->prepare($sql);
            $query->execute();
            $hearing_rooms = $query->fetchAll(PDO::FETCH_OBJ);
            $rooms = $this->getRoomModel();
            for ($i = 0; $i < count($rooms); $i++)
            {
                $find=false;
                for ($j=0;$j<count($hearing_rooms);$j++)
                {
                    if ($rooms[$i]->id == $hearing_rooms[$j]->room_id)
                    {
                        if ($hearing_rooms[$j]->status==0)
                        {
                        if (($data['room_'.$rooms[$i]->id] == '1')||($rooms[$i]->id==$data['room_id']))
                            {
                            $sql = "UPDATE hearing_rooms SET status='true' WHERE hearing_id='{$id}' AND room_id='{$rooms[$i]->id}'";
                            $query = $con->prepare($sql);
                            $query->execute();
                            }
                        }
                        if ($hearing_rooms[$j]->status==1)
                        {
                            if (($data['room_'.$rooms[$i]->id] != '1')&&($rooms[$i]->id!=$data['room_id']))
                            {
                                $sql = "UPDATE hearing_rooms SET status='false' WHERE hearing_id='{$id}' AND room_id='{$rooms[$i]->id}'";
                                $query = $con->prepare($sql);
                                $query->execute();
                            }
                        }
                        $find=true;
                        break;
                    }
                }
                if ($find==false)
                {
                    $sql = 'INSERT INTO hearing_rooms (hearing_id, room_id, status) VALUES (:hearing_id,:room_id, :status)';
                    $query = $con->prepare($sql);
                    if ($rooms[$i]->id==$data['room_id']) $status='true';
                    else $status = ($data['room_'.$rooms[$i]->id] == '1') ? 'true' : 'false';
                    $query->bindParam('hearing_id', $id);
                    $query->bindParam('room_id', $rooms[$i]->id);
                    $query->bindParam('status', $status);
                    $query->execute();
                }
            }
            */
            $result['status'] = 'success';
            $result['reload'] = 'true';
        } catch (Exception $ex) {
            $result['status'] = 'error';
            $result['reload'] = 'Неизвестная ошибка (' . $ex->getMessage() . ')';
        }
        return $result;
    }
}
