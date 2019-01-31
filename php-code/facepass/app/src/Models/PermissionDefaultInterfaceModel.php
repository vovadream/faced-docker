<?php


namespace App\Models;

use \PDO;

/**
 * Класс модели списка комнат филиала (Таблица filial_rooms)
 * Class FakeModel
 * @package App\Models
 */
class PermissionDefaultInterfaceModel extends Model
{
    /**
     * @var \PDO
     */
    protected $db;


    /*
     * Получение стандартных прав доступа к интерфейсам
     * Return: array[]
     */

    public function getPermissionDefaultInterfaceModel($id = 0)
    {
        $result = [];
        try {
            $sql = "SELECT permissions_def_interfaces.*, permissions.name AS permission_name, interfaces.name AS interface_name
FROM permissions_def_interfaces
LEFT JOIN permissions ON permissions.id=permissions_def_interfaces.permission_id
LEFT JOIN interfaces ON interfaces.id=permissions_def_interfaces.interface_id
";
            if ($id != 0) {
                $sql .= " WHERE permission_id='{$id}'";
                $sql .= "ORDER BY permissions_def_interfaces.id ASC";
            }

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
     * Сохранение стандартного права доступа к интерфейсам в БД
     * Return: array[]
     */

    public function addPermissionDefaultInterfaceModel($data = null)
    {
        $result = [];
        if ($data == null) {
            $result['status'] = 'error';
            $result['message'] = 'Ошибка передачи данных';
        } else {
            try {
                $con = $this->db;
                //проверяем, не было ли уже добавлено данное право доступа к указанному интерфейсу
                $sql = "SELECT * FROM permissions_def_interfaces WHERE permission_id=:permission_id AND interface_id=:interface_id";
                $query = $con->prepare($sql);
                $query->bindParam('permission_id', $data['permission_id']);
                $query->bindParam('interface_id', $data['interface_id']);
                $query->execute();
                if($query->rowCount()>=1) {
                    $result['status'] = 'error';
                    $result['message'] = 'Данное право доступа уже существует';
                }
                //если это право не задано, то создаем его
                else {
                    $sql = "INSERT INTO permissions_def_interfaces(permission_id, interface_id,status) VALUES(:permission_id, :interface_id,:status)";
                    $query = $con->prepare($sql);
                    $query->bindParam('permission_id', $data['permission_id']);
                    $query->bindParam('interface_id', $data['interface_id']);
                    $query->bindParam('status', $data['status']);
                    $query->execute();
                    $sql = "SELECT * FROM permissions_def_interfaces WHERE permission_id=:permission_id AND interface_id=:interface_id AND status=:status";
                    $query = $con->prepare($sql);
                    $query->bindParam('permission_id', $data['permission_id']);
                    $query->bindParam('interface_id', $data['interface_id']);
                    $query->bindParam('status', $data['status']);
                    $query->execute();
                    if ($query->rowCount() >= 1) {
                        $result['status'] = 'success';
                        $result['reload'] = 'true';
                    } else {
                        $result['status'] = 'error';
                        $result['message'] = 'Доступ не сохранен';
                    }
                }
            } catch (Exception $ex) {
                $result['status'] = 'error';
                $result['message'] = 'Неизвестная ошибка (' . $ex->getMessage() . ')';
            }
        }
        return $result;
    }

    /*
     * Обновление стандартного права доступа
     * Return: array[]
     */
    public function updatePermissionDefaultInterfaceModel($data = null, $id = null)
    {
        $result = [];
        try {
            $sql = "UPDATE permissions_def_interfaces SET status=:status WHERE id=:id";
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
}
