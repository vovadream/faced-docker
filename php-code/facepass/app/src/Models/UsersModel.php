<?php

namespace App\Models;

use \PDO;

/**
 * Class UsersModel
 * @package App\Models
 */
class UsersModel extends Model
{
    /**
     * @var \PDO
     */
    protected $db;


    /**
     * Добавление посетителя
     * @param array $data
     * @return int
     */
    public function Add($data = [])
    {
        //TODO: delete this check. make default value in BD?
        if(!isset($data['user_type_id'])) $data['user_type_id'] = 2;

        $sql = "INSERT INTO users
            (email, phone,	first_name,	patronymic, surname, birthday, user_photo, 
            reg_date, filial_id, user_type_id, ff_person_id, work_place, work_position)
			VALUES(:email, :phone, :first_name, :patronymic, :surname, :birthday, :user_photo, 
			(SELECT CURRENT_DATE), :filial_id, :user_type_id, :ff_person_id, :work_place, :work_position)";
        $sth = $this->db->prepare($sql);
        $sth->bindParam('email', $data['email']);
        $sth->bindParam('phone', $data['phone']);
        $sth->bindParam('first_name', $data['first_name']);
        $sth->bindParam('patronymic', $data['patronymic']);
        $sth->bindParam('surname', $data['surname']);
        $sth->bindParam('birthday', $data['birthday']);
        $sth->bindParam('user_photo', $data['user_photo']);
        $sth->bindParam('filial_id', $data['filial_id']);
        $sth->bindParam('user_type_id', $data['user_type_id']);
        $sth->bindParam('ff_person_id', $data['id_person']);
        $sth->bindParam('work_place', $data['work_place']);
        $sth->bindParam('work_position', $data['work_position']);
        $sth->execute();
        return $this->db->lastInsertId();
    }

    /**
     * Обновление данных пользователя
     * @param $data array - массив данных для обновления
     * @param $id int -идентификатор пользователя
     * @return object|bool
     */
    public function Update($data, $id)
    {
        if (!is_array($data) || sizeof($data) == 0)
            return false;

        //строим запрос
        $count = sizeof($data);$i=1;
        $sql = "UPDATE users SET";
        foreach ($data as $key => $value) {
            $sql .= " {$key}=:{$key}";
            if ($count != $i)
                $sql .= ",";
            else
                $sql .= " ";
            $i++;
        }
        $sql .= "WHERE id=:id";

        $sth = $this->db->prepare($sql);
        //собираем значения параметров
        foreach ($data as $k => $v) {
            $sth->bindParam($k, $v);
            //wtf не срабатывает стандартный сборщик, приходится руками чистить
            unset($k, $v);
        }
        $sth->bindParam("id", $id);
        $sth->execute();
        return true;
    }

    /**
     * Получить одного пользователя по полю и значению
     * @param $value
     * @param string $field
     * @return object
     */
    public function GetOne($value, $field = 'id')
    {
        $sth = $this->db->prepare("SELECT * FROM users WHERE {$field}=:value");
        $sth->bindParam("value", $value);
        $sth->execute();
        return $sth->fetchObject();
    }

    /**
     * Проверка кода пропуска посетителя
     * @param $code string код для проверки
     * @return object
     */
    public function VerifyCode($code)
    {
        $sth = $this->db->prepare("SELECT user_access.*, hearing.hdate, users.surname, users.first_name, users.patronymic, users.ff_person_id 
          FROM user_access 
          LEFT JOIN hearing ON user_access.hearing_id = hearing.id
          LEFT JOIN users ON users.id = user_access.user_id
          WHERE user_access.code=:code");
        $sth->bindParam("code", $code);
        $sth->execute();
        return $sth->fetchObject();
    }

    /**
     * Все коды пользователей
     * @param $filial_id
     * @return array objects
     */
    public function AllCodes($filial_id)
    {
        $sth = $this->db->prepare("SELECT user_access.id, user_access.code, hearing.hdate
                FROM user_access 
                LEFT JOIN hearing ON user_access.hearing_id = hearing.id
                WHERE hearing.filial_id=:filial_id");
        $sth->bindParam("filial_id", $filial_id);
        $sth->execute();
        return $sth->fetchAll();
    }

    /**
     * Возвращает слушанья пользователя
     * @param $user_id
     * @return array
     */
    public function GetInviteeByUser($user_id)
    {
        $sth = $this->db->prepare("SELECT user_invitees.id, hearing.name as target,
                  user_invitees.t_date AS date, user_invitees.t_time AS time,
                  filial_rooms.name AS room_number, hearing.departament_id
                FROM user_invitees
                  LEFT JOIN hearing ON user_invitees.hearing_id = hearing.id
                  LEFT JOIN filial_rooms ON hearing.room_id = filial_rooms.id
                WHERE user_id=:value AND active=TRUE");
        $sth->bindParam("value", $user_id);
        $sth->execute();
        return $sth->fetchAll();
    }

    /**
     * Получение конкретного приглашения
     * @param $id
     * @return object
     */
    public function GetInvitee($id)
    {
        $sth = $this->db->prepare("SELECT * FROM user_invitees WHERE id=:id AND active=TRUE");
        $sth->bindParam("id", $id);
        $sth->execute();
        return $sth->fetchObject();
    }

    /**
     * Обновление приглашения
     * @param $id
     * @param $value
     * @param string $field
     * @return bool
     */
    public function UpdateInvitee($id, $value, $field = 'active')
    {
        $sql = "UPDATE user_invitees SET {$field}=:value WHERE id=:id";
        $sth = $this->db->prepare($sql);
        $sth->bindParam('id', $id);
        $sth->bindParam('value', $value);
        $sth->execute();
        return true;
    }

    /**
     * Добавление приглашений
     * @param $user_id
     * @param $hearing_id
     * @return int
     */
    public function AddInvitee($user_id, $hearing_id)
    {
        $date = date_create()->format('Y-m-d');
        $time = date_create()->format('H:i:s');

        $sth = $this->db->prepare("INSERT INTO user_invitees
            (user_id, hearing_id, t_date, t_time, active) VALUES 
            (:user_id, :hearing_id, :t_date, :t_time, TRUE)
        ");
        $sth->bindParam('user_id', $user_id);
        $sth->bindParam('hearing_id', $hearing_id);
        $sth->bindParam('t_date', $date);
        $sth->bindParam('t_time', $time);
        $sth->execute();
        return $this->db->lastInsertId();
    }

    public function GetAllInviteesGuests()
    {
        $sth = $this->db->prepare("SELECT invitees_guests.*, hearing.name, hearing.hdate
                FROM invitees_guests
                  LEFT JOIN hearing ON invitees_guests.hearing_id = hearing.id");
        $sth->execute();
        return $sth->fetchAll(PDO::FETCH_OBJ);
    }

    public function SearchGuests($data)
    {
        $sth = $this->db->prepare("SELECT *
                FROM invitees_guests
                WHERE first_name=:first_name AND surname=:surname AND patronymic=:patronymic");
        $sth->bindParam('first_name', $data['first_name']);
        $sth->bindParam('patronymic', $data['patronymic']);
        $sth->bindParam('surname', $data['surname']);
        $sth->execute();
        return $sth->fetchAll(PDO::FETCH_OBJ);
    }

    public function AddInviteGuest($data)
    {
        $date = date_create()->format('Y-m-d');
        $sth = $this->db->prepare("INSERT INTO invitees_guests
            (first_name, patronymic, surname, phone, hearing_id, created) VALUES 
            (:first_name, :patronymic, :surname, :phone, :hearing_id, :created)
        ");
        $sth->bindParam('first_name', $data['first_name']);
        $sth->bindParam('patronymic', $data['patronymic']);
        $sth->bindParam('surname', $data['surname']);
        $sth->bindParam('phone', $data['phone']);
        $sth->bindParam('hearing_id', $data['hearing_id']);
        $sth->bindParam('created', $date);
        $sth->execute();
        return $this->db->lastInsertId();
    }

    public function InviteGuestAttach($user_id, $id)
    {
        $sql = "UPDATE invitees_guests SET user_id=:user_id, status=TRUE WHERE id=:id";
        $sth = $this->db->prepare($sql);
        $sth->bindParam('id', $id);
        $sth->bindParam('user_id', $user_id);
        $sth->execute();
        return true;
    }

    /**
     * Получить слушанье\услугу
     * @param $id
     * @return object
     */
    public function GetHearing($id)
    {
        $sth = $this->db->prepare("SELECT * FROM hearing WHERE id=:id");
        $sth->bindParam("id", $id);
        $sth->execute();
        return $sth->fetchObject();
    }

    /**
     * Добавление права на проход
     * @param $user_id
     * @param $hearing_id
     * @param $code
     * @return int
     */
    public function AddAccess($user_id, $hearing_id, $code)
    {
        $time = time();
        $sql = "INSERT INTO user_access
            (user_id, hearing_id, code, creating_time)
			VALUES(:user_id, :hearing_id, :code, :creating_time)";
        $sth = $this->db->prepare($sql);
        $sth->bindParam('user_id', $user_id);
        $sth->bindParam('hearing_id', $hearing_id);
        $sth->bindParam('code', $code);
        $sth->bindParam('creating_time', $time);
        $sth->execute();
        return $this->db->lastInsertId();
    }

    /**
     * Получения прав на проход по иду
     * @param $id
     * @return mixed
     */
    public function GetAccess($id)
    {
        $sth = $this->db->prepare("SELECT * FROM user_access WHERE id=:id");
        $sth->bindParam("id", $id);
        $sth->execute();
        return $sth->fetchObject();
    }

    /**
     * Добавление прохода
     * @param $user_id int
     * @param $access_id int
     * @return int
     */
    public function AddPass($user_id, $access_id)
    {
        $sql = "INSERT INTO user_pass
            (user_id, access_id)
			VALUES(:user_id, :access_id)";
        $sth = $this->db->prepare($sql);
        $sth->bindParam('user_id', $user_id);
        $sth->bindParam('access_id', $access_id);
        $sth->execute();
        return $this->db->lastInsertId();
    }

    /**
     * Получение одного прохода
     * @param $value
     * @param string $field
     * @return object
     */
    public function GetPass($value, $field = 'id')
    {
        $sth = $this->db->prepare("SELECT * FROM user_pass WHERE {$field}=:value");
        $sth->bindParam("value", $value);
        $sth->execute();
        return $sth->fetchObject();
    }

    /**
     * Обновление информации о проходе
     * @param $id
     * @param $value
     * @param string $field
     * @return bool
     */
    public function UpdatePass($id, $value, $field = 'info')
    {
        $sql = "UPDATE user_pass SET {$field}=:value WHERE id=:id";
        $sth = $this->db->prepare($sql);
        $sth->bindParam('id', $id);
        $sth->bindParam('value', $value);
        $sth->execute();
        return true;
    }

    /**
     * Добавление времени прохода
     * @param $id
     * @param $direction
     * @param $date
     * @return int
     */
    public function AddDirectionPass($id, $direction = 'in', $date = 'current')
    {
        if ($date == 'current') {
            $date = date_create()->format('Y-m-d');
            $time = date_create()->format('H:i:s');
        } else {
            $arr = explode(' ', $date);
            $date = $arr[0];
            $time = $arr[1];
        }

        $sql = "UPDATE user_pass 
                SET date_{$direction}=:date, time_{$direction}=:time
                WHERE id=:id";
        $sth = $this->db->prepare($sql);
        $sth->bindParam('id', $id);
        $sth->bindParam('date', $date);
        $sth->bindParam('time', $time);
        $sth->execute();
        return true;
    }

    /**
     * Получение прохода в здание
     * @param $user_id
     * @param $access_id
     * @return object
     */
    public function GetPassInFilial($user_id, $access_id)
    {
        if(is_null($access_id))
            $a = ' IS NULL';
        else
            $a = '=:access_id';
        $sth = $this->db->prepare("SELECT * FROM user_pass 
                WHERE user_id=:user_id AND access_id{$a} AND date_out IS NULL");
        $sth->bindParam("user_id", $user_id);
        if(!is_null($access_id))
            $sth->bindParam("access_id", $access_id);
        $sth->execute();
        return $sth->fetchObject();
    }


    /**
     * @return array
     */
    public function findAll()
    {
        $result = [];
        try {
            $sql = "SELECT users.*, user_types.name AS user_type, filial.name AS filial_name, user_types.main_class, workers.department_id AS dep_id
		FROM users
		LEFT JOIN filial ON filial.id=users.filial_id
		LEFT JOIN user_types ON user_types.id=users.user_type_id
		LEFT JOIN workers ON users.id=workers.user_id ";
            $sql .= " ORDER BY users.id DESC";
            $con = $this->db;
            $query = $con->prepare($sql);
            $query->execute();
            if ($query->rowCount() >= 1) {
                $result = $query->fetchAll(PDO::FETCH_OBJ);
            } else {
                $result['status'] = "error";
                $result['message'] = "Нет данных.";
            }
        } catch (\Exception $ex) {
            $result['status'] = "error";
            $result['message'] = "Неизвестная ошибка.";
        }
        return $result;
    }

    /**
     * @return array
     */
    public function findAllFromTo($began, $count, $search = null)
    {

        $result = [];
            $sql = "SELECT users.*, user_types.name AS user_type, filial.name AS filial_name, user_types.main_class, workers.department_id AS dep_id
		FROM users
		LEFT JOIN filial ON filial.id=users.filial_id
		LEFT JOIN user_types ON user_types.id=users.user_type_id
		LEFT JOIN workers ON users.id=workers.user_id ";
            $con = $this->db;

            if(!is_null($search)) {
                $sql .= "WHERE users.email LIKE :search OR users.phone LIKE :search OR users.first_name LIKE :search OR users.patronymic LIKE :search OR users.surname LIKE :search OR user_types.name LIKE :search OR filial.name LIKE :search";
            }
            $sql .= " ORDER BY users.id DESC LIMIT :count OFFSET :began";
            $query = $con->prepare($sql);



            $query->bindParam("began", $began);
            $query->bindParam("count", $count);
            if(!is_null($search)) {
                $search = '%'.$search.'%';
                $query->bindValue(':search', $search);
            }

            $query->execute();
            if ($query->rowCount() >= 1) {
                $result = $query->fetchAll(PDO::FETCH_OBJ);
            }

        return $result;
    }



    public function addUserModel($data = null)
    {
        $result = [];
        try {
            $con = $this->db;
            $sql = "INSERT INTO users(email, phone,	first_name,	patronymic, surname,	birthday, reg_date,	filial_id, user_type_id, ff_person_id)
			VALUES(:email, :phone, :first_name, :last_name, :surname, :birthday, (SELECT CURRENT_DATE),	:filial_id, '2', ((SELECT MIN(ff_person_id) FROM users)-1))";
            $query = $con->prepare($sql);
            $query->bindParam('email', $data['email']);
            $query->bindParam('phone', $data['phone']);
            $query->bindParam('first_name', $data['first_name']);
            $query->bindParam('last_name', $data['last_name']);
            $query->bindParam('surname', $data['surname']);
            $birthday=pg_escape_string($data['birthday']);
            $query->bindParam('birthday', $birthday);

            $query->bindParam('filial_id', $_SESSION['filial_id']);
            $query->execute();
            $sql = "SELECT * FROM users WHERE email=:email AND phone=:phone AND	first_name=:first_name AND	patronymic=:last_name AND
			surname=:surname AND birthday=:birthday AND reg_date=(SELECT CURRENT_DATE) AND	filial_id=:filial_id AND user_type_id='2'";
            $query = $con->prepare($sql);
            $query->bindParam('email', $data['email']);
            $query->bindParam('phone', $data['phone']);
            $query->bindParam('first_name', $data['first_name']);
            $query->bindParam('last_name', $data['last_name']);
            $query->bindParam('surname', $data['surname']);
            $birthday=pg_escape_string($data['birthday']);
            $query->bindParam('birthday', $birthday);
            $query->bindParam('filial_id', $_SESSION['filial_id']);
            $query->execute();
            if ($query->rowCount() >= 1) {
                $result['status'] = 'success';
                $result['reload'] = 'true';
            } else {
                $result['status'] = 'error';
                $result['message'] = 'Пользователь не сохранен';
                //}
            }
        } catch (Exception $ex) {
            $result['status'] = 'error';
            $result['message'] = 'Неизвестная ошибка (' . $ex->getMessage() . ')';
        }
        return $result;
    }

    /*
     * Обновление пользователя
     * Return: array[]
     */
    public function updateUserModel($data = null, $id = null)
    {
        $result = [];
        try {
            $sql = "UPDATE users SET email=:email, phone=:phone, first_name=:first_name, patronymic=:last_name,
			surname=:surname, birthday=:birthday, user_type_id=:user_type_id WHERE id=:id";
            $con = $this->db;
            $query = $con->prepare($sql);
            $query->bindParam('email', $data['email']);
            $query->bindParam('phone', $data['phone']);
            $query->bindParam('first_name', $data['first_name']);
            $query->bindParam('last_name', $data['last_name']);
            $query->bindParam('surname', $data['surname']);
            $query->bindParam('birthday', $data['birthday']);
            $query->bindParam('user_type_id', $data['user_type_id']);
            $query->bindParam('id', $id);
            $query->execute();
            if (isset($data['department_id']))
            {
                $sql = "UPDATE workers SET department_id=:department_id WHERE user_id=:id";
                $con = $this->db;
                $query = $con->prepare($sql);
                $query->bindParam('department_id', $data['department_id']);
                $query->bindParam('id', $id);
                $query->execute();
            }
            $result['status'] = 'success';
            $result['reload'] = 'true';
        } catch (Exception $ex) {
            $result['status'] = 'error';
            $result['reload'] = 'Неизвестная ошибка (' . $ex->getMessage() . ')';
        }
        return $result;
    }

    /*
      * Получение списка пользователей
      * Return: array[]
      */

    public function getUsersModel($id = null)
    {
        $result = [];
        try {
            $sql = "SELECT users.*, user_types.name AS user_type, filial.name AS filial_name, user_types.main_class, workers.department_id AS dep_id
		FROM users
		LEFT JOIN filial ON filial.id=users.filial_id
		LEFT JOIN user_types ON user_types.id=users.user_type_id
		LEFT JOIN workers ON users.id=workers.user_id";
            if ($id != null)
                $sql .= " WHERE users.id='{$id}'";
            $sql .= " ORDER BY users.id ASC";
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

    /**
     * @param null $filter
     * @param string $type
     * @param null $user_id
     * @param $limit false
     * @return array
     * Получение списка прошедших через турникет
     * Таблица прохода у нас одна, выборка вошедших/вышедших происходит сортировкой датой и временем прохода
     * type - In/Out
     */
    public function getUserPassInOut($filter = null, $type = "in", $user_id=null, $limit = false)
    {
        $result = [];
        try {
            $sql = "SELECT user_pass.*,
users.first_name, users.patronymic, users.surname, users.user_photo, users.birthday,
user_types.name AS user_type_name,
filial_rooms.name AS user_room_name,
marks.name AS mark_name,
filial_departament.name AS user_departament_name,
filial_rooms.id AS user_room_id
FROM user_pass
LEFT JOIN users ON user_pass.user_id = users.id
LEFT JOIN workers ON users.id = workers.user_id
LEFT JOIN filial_departament ON workers.department_id=filial_departament.id
LEFT JOIN user_types ON  users.user_type_id = user_types.id
LEFT JOIN user_access ON user_pass.access_id = user_access.id
LEFT JOIN hearing ON user_access.hearing_id = hearing.id
LEFT JOIN filial_rooms ON hearing.room_id = filial_rooms.id
LEFT JOIN marks ON marks.id=user_pass.mark_id
";

            if($type=='in')
            {
                $sql .= " WHERE date_in IS NOT NULL";
                if ($user_id!=null) $sql .= " AND user_pass.user_id='{$user_id}'";
                if ($filter!=null) $sql .= $filter;
                $sql .= " ORDER BY date_in DESC, time_in DESC ";
            }
            else if($type=='out')
            {
                $sql .= " WHERE user_pass.date_out IS NOT NULL ";
                if ($user_id!=null) $sql .= " AND user_pass.user_id='{$user_id}'";
                if ($filter!=null) $sql .= $filter;
                $sql .= " ORDER BY date_out DESC, time_out DESC ";
            }

            if($limit) {
                $sql .= " LIMIT " . $limit;
            }

            /*if($filter == null)
                $sql .= "LIMIT 100";
            else $sql .= "LIMIT ".$filter['limit'];*/
            $con = $this->db->prepare($sql);
            $con->execute();
            $result = $con->fetchAll(PDO::FETCH_OBJ);
        }
        catch (Exception $ex) {
            $result['status'] = 'error';
            $result['message'] = $ex->getMessage();
        }
        return $result;
    }
}