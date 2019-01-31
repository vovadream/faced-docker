<?php


namespace App\Models;

use \PDO;

/**
 * Класс модели списка меток (Таблица marks)
 * Class FakeModel
 * @package App\Models
 */
class MarkModel extends Model
{
    /**
     * @var \PDO
     */
    protected $db;


    /*
     * Получение списка меток
     * Return: array[]
     */
    public function getMarksModel($id = null)
    {
        $result = "";
        try {
            $sql = "SELECT marks.* FROM marks";
            if ($id != null)
                $sql .= " WHERE marks.id={$id}";
            $sql .= " ORDER BY marks.id ASC";
            $con = $this->db;
            $query = $con->prepare($sql);
            $query->execute();
            if ($query->rowCount() >= 1) {
                $result = $query->fetchAll(PDO::FETCH_OBJ);
            } else {
                $result = [];
                $result['status'] = "error";
                $result['message'] = "Нет меток.";
            }
            $con = null;
        } catch (Exception $ex) {
            $result['status'] = "error";
            $result['message'] = "Неизвестная ошибка.";
        }
        return $result;
    }

    /*
     * Сохранение метки в БД
     * Return: array[]
     */

    public function addMarkModel($data = null)
    {
        $result = [];
        try {
            $sql = "INSERT INTO marks(name, quite_alert) VALUES(:name, :quite_alert)";
            $con = $this->db;
            $query = $con->prepare($sql);
            $query->bindParam('name', $data['name']);
            $quite_alert=isset($data['quite_alert'])?'true':'false';
            $query->bindParam('quite_alert', $data['quite_alert']);
            $query->execute();
            $sql = "SELECT * FROM marks WHERE name=:name";
            $query = $con->prepare($sql);
            $query->bindParam('name', $data['name']);
            $query->execute();
            if ($query->rowCount() >= 1) {
                $result['status'] = 'success';
                $result['reload'] = 'true';
            } else {
                $result['status'] = 'error';
                $result['message'] = 'Метка не сохранена!';
            }
        } catch (Exception $ex) {
            $result['status'] = 'error';
            $result['message'] = 'Неизвестная ошибка (' . $ex->getMessage() . ')!';
        }
        return $result;
    }

    /*
     * Обновление метки
     * Return: array[]
     */
    public function updateMarkModel($data = null, $id = null)
    {
        $result = [];
        try {
            $sql = "UPDATE marks SET name=:name, quite_alert=:quite_alert WHERE id=:id";
            $con = $this->db;
            $query = $con->prepare($sql);
            $query->bindParam('name', $data['name']);
            $quite_alert=isset($data['quite_alert'])?'true':'false';
            $query->bindParam('quite_alert', $data['quite_alert']);
            $query->bindParam('id', $id);
            $query->execute();
            $result['status'] = 'success';
            $result['reload'] = 'true';
        } catch (Exception $ex) {
            $result['status'] = 'error';
            $result['message'] = 'Неизвестная ошибка';
        }
        return $result;
    }

    /*
     * Удаление метки
     * Return: array[]
     * TODO: Написать метод
     */
    public function removeMarkModel($data = null, $id = null)
    {
//        $result = [];
//        try {
//            $sql = "UPDATE marks SET name=:name, quite_alert=:quite_alert WHERE id=:id";
//            $con = $this->db;
//            $query = $con->prepare($sql);
//            $query->bindParam('name', $data['name']);
//            $quite_alert=isset($data['quite_alert'])?'true':'false';
//            $query->bindParam('quite_alert', $data['quite_alert']);
//            $query->bindParam('id', $id);
//            $query->execute();
//            $result['status'] = 'success';
//            $result['reload'] = 'true';
//        } catch (Exception $ex) {
//            $result['status'] = 'error';
//            $result['message'] = 'Неизвестная ошибка';
//        }
//        return $result;
    }


    /*
     * Прикрепление метки к пользователю
     * Return: array[]
     */

    public function addUserMarkModel($data = null, $user_id = null)
    {
        $result = [];
        try {
            $con = $this->db;
            $sql = "INSERT INTO user_marks (mark_id,user_id,mdate, mtime, worker_id, status)
			VALUES (:mark_id,:user_id, (select CURRENT_DATE), (select localtime),
			(SELECT id FROM workers WHERE user_id=:worker), 'true')";
            $query = $con->prepare($sql);
            $query->bindParam('mark_id', $data['mark']);
            $query->bindParam('user_id', $user_id);
            $query->bindParam('worker', $_SESSION['id']);
            $query->execute();
            $userpass = $this->getUserPass($user_id);
            if ($userpass!=null)
            {
                if ($userpass[0]->mark_id==null)
                {
                    $sql = "UPDATE user_pass SET mark_id=:mark_id WHERE id=:id";
                    $query = $con->prepare($sql);
                    $query->bindParam('mark_id', $data['mark']);
                    $query->bindParam('id', $userpass[0]->id);
                    $query->execute();
                }
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


    /*
     * Закрытие пользовательской метки
     * Return: array[]
     */

    public function updateUserMarkModel($data = null)
    {
        $result = [];
        try {
            $con = $this->db;
            $sql = "UPDATE user_marks SET status='false', worker_id_close=(SELECT id FROM workers WHERE user_id=:worker), date_close=(SELECT CURRENT_DATE), time_close=(SELECT localtime) WHERE id=:id";
            $query = $con->prepare($sql);
            $query->bindParam('id', $data['selectedMark']);
            $query->bindParam('worker', $_SESSION['id']);
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
     * Прикрепление метки к слушанию
     * Return: array[]
     */

    public function updateUserMarkPassModel($user_pass=null, $mark_id=null)
    {
        $result = [];
        try {
            $con = $this->db;
            $sql = "INSERT INTO user_marks (mark_id,user_id,mdate, mtime, worker_id, status)
			VALUES (:mark_id,(SELECT user_id FROM user_pass WHERE id=:pass_id), (select CURRENT_DATE), (select localtime),
			(SELECT id FROM workers WHERE user_id=:worker), 'true')";
            $query = $con->prepare($sql);
            $query->bindParam('mark_id', $mark_id);
            $query->bindParam('pass_id', $user_pass);
            $query->bindParam('worker', $_SESSION['id']);
            $query->execute();
            $sql = "UPDATE user_pass SET mark_id=:mark_id WHERE id=:id";
            $query = $con->prepare($sql);
            $query->bindParam('mark_id', $mark_id);
            $query->bindParam('id', $user_pass);
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
