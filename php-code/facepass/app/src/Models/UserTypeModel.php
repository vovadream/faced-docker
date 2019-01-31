<?php


namespace App\Models;

use \PDO;

/**
 * Служит для хранения списка типов (Ролей) пользователей.
 * Class UserTypeModel
 * @package App\Models
 */
class UserTypeModel extends Model
{
    /**
     * @var \PDO
     */
    protected $db;


    /*
     * Получение списка типов пользователей
     * Return: array[]
     * TODO: Проверить не дублируется ли функция
     */
    public function getUserTypeModel($id = null)
    {
        $result = [];
        try {
            $sql = "SELECT * FROM user_types";
            if ($id != null)
                $sql .= " WHERE id={$id}";
            //else $sql .= " WHERE filial_id={$_SESSION['filial_id']}";
            $sql .= " ORDER BY id ASC";

            $con = $this->db;
            $query = $con->prepare($sql);
            $query->execute();
            if ($query->rowCount() >= 1) {
                $result = $query->fetchAll(PDO::FETCH_OBJ);
            } else {
                $result['status'] = "error";
                $result['message'] = "Нет отделов в данном филиале.";
            }
            $con = null;
        } catch (Exception $ex) {
            $result['status'] = "error";
            $result['message'] = "Неизвестная ошибка.";
        }
        return $result;
    }

    /*
     * Получение типов пользователей
     * Return: array[]
     */

    public function getUserTypesModel($id = null)
    {
        $result = [];
        try {
            $sql = "SELECT * FROM user_types WHERE (filial_id=:filial_id OR filial_id='0')";
            if ($id != null)
                $sql .= " AND id='{$id}'";
            $sql .= " ORDER BY id ASC";

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
     * Сохранение типа пользователя в БД
     * Return: array[]
     */

    public function addUserTypeModel($data = null)
    {
        $result = [];
        try {
            $mainClass = $this->getMainClassModel($data['parent_id']);
            $con = $this->db;
            $sql = "INSERT INTO user_types(name, filial_id, parent_id, main_class) VALUES(:name, :filial_id, :parent_id, :main_class)";
            $query = $con->prepare($sql);
            $query->bindParam('name', $data['name']);
            $query->bindParam('filial_id', $_SESSION['filial_id']);
            $query->bindParam('parent_id', $data['parent_id']);
            $query->bindParam('main_class', $mainClass);

            $query->execute();
            //$sql = "SELECT * FROM filial_departament WHERE name=:name AND filial_id={$_SESSION['filial_id']}";
            $sql = "SELECT * FROM user_types WHERE name=:name AND filial_id=:filial_id";
            $query = $con->prepare($sql);
            $query->bindParam('name', $data['name']);
            $query->bindParam('filial_id', $_SESSION['filial_id']);
            $query->execute();
            if ($query->rowCount() >= 1) {
                $result['status'] = 'success';
                $result['reload'] = 'true';
            } else {
                $result['status'] = 'error';
                $result['message'] = 'Отдел не сохранен';
            }
        } catch (Exception $ex) {
            $result['status'] = 'error';
            $result['message'] = 'Неизвестная ошибка (' . $ex->getMessage() . ')';
        }
        return $result;
    }

    /*
     * Обновление типа пользователя
     * Return: array[]
     */
    public function updateUserTypeModel($data = null, $id = null)
    {
        $result = [];
        try {
            $mainClass = $this->getMainClassModel();
            $sql = "UPDATE user_types SET name=:name, parent_id=:parent_id, main_class=:main_class WHERE id=:id";
            $con = $this->db;
            $query = $con->prepare($sql);
            $query->bindParam('name', $data['name']);
            $query->bindParam('parent_id', $data['parent_id']);
            $query->bindParam('main_class', $mainClass);
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
     * Удаление типа пользователя
     * Return: array[]
     * TODO:Написать метод
     */
    public function removeUserTypeModel($data = null, $id = null)
    {

    }

    /*
     * Получение основного класса пользователя
     * Return: integer
     */
    public function getMainClassModel($id = 0)
    {
        $mainClass = -1;
        if ($id != 0) {
            //Search main_class
            $currentTypeID = $id;
            while ($mainClass == -1) {
                $sql = "SELECT id, parent_id, main_class FROM user_types WHERE id=:id";
                $con = $this->db;
                $query = $con->prepare($sql);
                $query->bindParam('id', $currentTypeID);
                $query->execute();
                $class = $query->fetchAll(PDO::FETCH_OBJ);
                if ($class[0]->main_class == 0 && $class[0]->parent_id == 0) {
                    $mainClass = $class[0]->id;
                } else {
                    $currentTypeID = $class[0]->parent_id;
                }
                $con = null;
            }
        } else {
            $mainClass = 3;
        }
        return $mainClass;
    }

}
