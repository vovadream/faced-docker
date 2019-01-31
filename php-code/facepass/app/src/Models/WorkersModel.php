<?php

namespace App\Models;

use \PDO;

/**
 * Class WorkersModel
 * @package App\Models
 */
class WorkersModel extends Model
{
    /**
     * @var \PDO
     */
    protected $db;

    protected $table = 'workers';

    /**
     * Добавление работника
     * @var $permission_id
     * 1 - Сис. админ
     * 2 - Админ
     * 3 - Охранник
     * 4 - Оператор
     * 5 - Сотрудник
     * @param array $data
     * @return int
     */
    public function Add($data = [])
    {
        $sql = "INSERT INTO workers
            (user_id, permission_id, login, password, code, filial_id)
			VALUES(:user_id, :permission_id, :login, :password, :code, :filial_id)";
        $sth = $this->db->prepare($sql);
        $sth->bindParam('user_id', $data['user_id']);
        $sth->bindParam('permission_id', $data['permission_id']);
        $sth->bindParam('login', $data['login']);
        $sth->bindParam('password', $data['password']);
        $sth->bindParam('code', $data['code']);
        $sth->bindParam('filial_id', $data['filial_id']);
        $sth->execute();
        return $this->db->lastInsertId();
    }

    /**
     * Получение рабочего с инфой о нём
     * @param $value
     * @param string $field
     * @return object
     */
    public function GetOne($value, $field = 'id')
    {
        $sth = $this->db->prepare("SELECT 
          workers.*, users.first_name, users.patronymic, users.surname, users.ff_person_id
          FROM workers
          LEFT JOIN users ON users.id = workers.user_id
          WHERE workers.{$field}=:value");
        $sth->bindParam("value", $value);
        $sth->execute();
        return $sth->fetch();
    }

    /**
     * Добавление уведомления
     * @param $worker_id
     * @param $action_text
     * @param $type int
     * 1 - стандартные|2 - критичные|
     * 3 - запрос на помощь|4 - запрос на стравнение ввода|
     * 5 - запрос на сравнение лиц|6 - тревога
     * @return string
     */
    public function AddNotification($worker_id, $action_text, $type, $equipment_id = null)
    {
        $date = date_create()->format('Y-m-d');
        $time = date_create()->format('H:i:s');
        $sql = "INSERT INTO workers_notifications 
                (worker_id, action_text, adate, atime, type, equipment_id) 
                VALUES (:worker_id, :action_text, :date, :time, :type, :equipment_id)";
        $sth = $this->db->prepare($sql);
        $sth->bindParam("worker_id", $worker_id);
        $sth->bindParam("action_text", $action_text);
        $sth->bindParam("type", $type);
        $sth->bindParam("equipment_id", $equipment_id);
        $sth->bindParam("date", $date);
        $sth->bindParam("time", $time);
        $sth->execute();
        return $this->db->lastInsertId();
    }

    /**
     * Получение всех уведомлений работника
     * @param $worker_id
     * @return array
     */
    public function GetNotifications($worker_id)
    {
        $sth = $this->db->prepare("SELECT workers_notifications.*, 
                filial_equipment.name as eq, notification_types.name as name_type
                FROM workers_notifications 
                LEFT JOIN filial_equipment ON workers_notifications.equipment_id = filial_equipment.id
                LEFT JOIN notification_types ON workers_notifications.type = notification_types.id
                WHERE worker_id=:worker_id OR worker_id is null ORDER BY id DESC");
        $sth->bindParam("worker_id", $worker_id);
        $sth->execute();
        return $sth->fetchAll();
    }

    /**
     * Получение конкретного уведомления
     * @param $id
     * @return object
     */
    public function GetOneNotify($id)
    {
        $sth = $this->db->prepare("SELECT * FROM workers_notifications WHERE id=:id");
        $sth->bindParam("id", $id);
        $sth->execute();
        return $sth->fetchObject();
    }

    /**
     * Пометка о прочтении уведомления
     * @param $id
     * @return bool
     */
    public function ReadNotify($id)
    {
        $sql = "UPDATE workers_notifications SET read=TRUE WHERE id=:id";
        $sth = $this->db->prepare($sql);
        $sth->bindParam('id', $id);
        $sth->execute();
        return true;
    }

    /**
     * Пометка о ответе на уведомление
     * @param $id
     * @return bool
     */
    public function ReplyNotify($id)
    {
        $sql = "UPDATE workers_notifications SET reply=TRUE WHERE id=:id";
        $sth = $this->db->prepare($sql);
        $sth->bindParam('id', $id);
        $sth->execute();
        return true;
    }

    /**
     * Вернёт количество новых непрочитанных уведомлений
     * @param $worker_id
     * @return object
     */
    public function NewsNotify($worker_id)
    {
        $sth = $this->db->prepare("SELECT COUNT(*) FROM workers_notifications 
                WHERE (worker_id=:worker_id OR worker_id is null) AND read=false");
        $sth->bindParam("worker_id", $worker_id);
        $sth->execute();
        return $sth->fetchObject();
    }

    /**
     * Проверка кода пропуска рабочего
     * @param $code string код для проверки
     * @return object
     */
    public function VerifyCode($code)
    {
        $sth = $this->db->prepare("SELECT workers.*, users.surname, users.first_name, users.patronymic, users.ff_person_id 
            FROM workers 
            LEFT JOIN users ON users.id = workers.user_id 
            WHERE workers.code=:code");
        $sth->bindParam("code", $code);
        $sth->execute();
        return $sth->fetchObject();
    }

    /**
     * Получение всех кодов сотрудников филиала
     * @param $filial_id
     * @return array objects
     */
    public function AllCodes($filial_id)
    {
        $sth = $this->db->prepare("SELECT id, code
                FROM workers 
                WHERE filial_id=:filial_id");
        $sth->bindParam("filial_id", $filial_id);
        $sth->execute();
        return $sth->fetchAll();
    }


    /**
     * @param null $id
     * @param null $user_id
     * @return array
     */
    public function findAll($id = null, $user_id = null)
    {
        $result = [];
        try {
            $sql = "SELECT workers.*, users.first_name, users.patronymic, users.surname
				FROM workers
				LEFT JOIN users ON users.id = workers.user_id
				WHERE workers.filial_id = :filial_id";
            if ($id != null)
                $sql .= " AND workers.id='{$id}'";
            else if ($user_id != null)
                $sql .= " AND workers.user_id='{$user_id}'";
            $sql .= " ORDER BY workers.id ASC";
            $con = $this->db;
            $query = $con->prepare($sql);
            $query->bindParam('filial_id', $_SESSION['filial_id']);
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

    /*
     * Получение работников
     * Return: array[]
     */

    public function getWorkersModel($id = null, $user_id = null)
    {
        $result = [];
        try {
            $sql = "SELECT workers.*, users.first_name, users.patronymic, users.surname
				FROM workers
				LEFT JOIN users ON users.id = workers.user_id
				WHERE workers.filial_id = :filial_id";
            if ($id != null)
                $sql .= " AND workers.id='{$id}'";
            else if($user_id!=null)
                $sql .= " AND workers.user_id='{$user_id}'";
            $sql .= " ORDER BY workers.id ASC";
            $con = $this->db;
            $query = $con->prepare($sql);
            $query->bindParam('filial_id', $_SESSION['filial_id']);
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