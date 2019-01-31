<?php


namespace App\Models;

use \PDO;

/**
 * Класс модели списка меток (Таблица marks)
 * Class FakeModel
 * @package App\Models
 */
class NullAccountModel extends Model
{
    /**
     * @var \PDO
     */
    protected $db;


    /*
     * Получение модели нулевого аккаунта
     * Return: array[]
    */
    public function getNullAccountModel()
    {
        $result = [];
        try {
            $sql = "SELECT user_pass.*,
			users.first_name, users.patronymic, users.surname, users.user_photo,
			user_types.name AS user_type_name
			FROM user_pass
			LEFT JOIN users ON user_pass.user_id = users.id
			LEFT JOIN user_types ON  users.user_type_id = user_types.id
			WHERE (users.user_type_id=3 OR user_types.main_class=3) AND user_pass.date_in IS NOT NULL AND user_pass.date_out IS NULL";
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

    /*
    * Вход нулевого аккаунта
    * Return: array[]
    */

    public function addNullAccountModel($data = null)
    {
        $result = [];
        try {
            $con = $this->db;
            if ($data['user_type_id']==0) $user_type_id=3;
            else $user_type_id=$data['user_type_id'];
            $sql = "INSERT INTO users (first_name,patronymic,surname,user_type_id, ff_person_id,filial_id)
			VALUES (:first_name,:patronymic,:surname,:user_type_id,((SELECT MIN(ff_person_id) FROM users)-1),:filial_id)";
            $query = $con->prepare($sql);
            $query->bindParam('first_name', $data['first_name']);
            $query->bindParam('surname', $data['surname']);
            $query->bindParam('patronymic', $data['patronymic']);
            $query->bindParam('user_type_id', $user_type_id);
            $query->bindParam('filial_id', $_SESSION['filial_id']);
            $query->execute();
            $sql = "INSERT INTO user_access (user_id,hearing_id, status)
			VALUES ((SELECT id FROM users WHERE first_name=:first_name AND patronymic=:patronymic AND surname=:surname
			AND user_type_id=:user_type_id ORDER BY id DESC LIMIT 1),'777','1')";
            $query = $con->prepare($sql);
            $query->bindParam('first_name', $data['first_name']);
            $query->bindParam('surname', $data['surname']);
            $query->bindParam('patronymic', $data['patronymic']);
            $query->bindParam('user_type_id', $user_type_id);
            $query->execute();
            $sql = "INSERT into user_pass (user_id,access_id, date_in, time_in, date_out,time_out, info)
			VALUES ((SELECT id FROM users WHERE first_name=:first_name AND patronymic=:patronymic AND surname=:surname
			AND user_type_id=:user_type_id ORDER BY id DESC LIMIT 1),
			(SELECT id FROM user_access WHERE hearing_id='777'
			AND user_id= (SELECT id FROM users WHERE first_name=:first_name AND patronymic=:patronymic AND surname=:surname
			AND user_type_id=:user_type_id ORDER BY id DESC LIMIT 1)),
			(select CURRENT_DATE), (select localtime), null, null, null)";
            $query = $con->prepare($sql);
            $query->bindParam('first_name', $data['first_name']);
            $query->bindParam('surname', $data['surname']);
            $query->bindParam('patronymic', $data['patronymic']);
            $query->bindParam('user_type_id', $user_type_id);
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

    /*
     * Выход нулевого аккаунта
     * Return: array[]
     */

    public function updateNullAccountModel($data = null, $id = null)
    {
        $result = [];
        try {
            $con = $this->db;
            $sql = "UPDATE users SET first_name=:first_name,patronymic=:patronymic,surname=:surname,user_type_id=:user_type_id WHERE id=:id";
            $query = $con->prepare($sql);
            $query->bindParam('id', $id);
            $query->bindParam('first_name', $data['first_name']);
            $query->bindParam('surname', $data['surname']);
            $query->bindParam('patronymic', $data['patronymic']);
            $query->bindParam('user_type_id', $data['user_type_id']);
            $query->execute();
            $sql = "UPDATE user_access SET status='2' WHERE user_id=:id AND hearing_id='777' AND status='1'";
            $query = $con->prepare($sql);
            $query->bindParam('id', $id);
            $query->execute();
            $sql = "SELECT id FROM user_access WHERE user_id=:id AND hearing_id='777' AND status='2' ORDER BY id DESC";
            $query = $con->prepare($sql);
            $query->bindParam('id', $id);
            $query->execute();
            if ($query->rowCount() >= 1) {
                $result = $query->fetchAll(PDO::FETCH_OBJ);
                $sql = "UPDATE user_pass SET date_out=(select CURRENT_DATE), time_out=(select localtime) WHERE user_id=:id AND access_id=:access_id";
                $query = $con->prepare($sql);
                $query->bindParam('id', $id);
                $query->bindParam('access_id', $result[0]->id);
                $query->execute();
            }
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
