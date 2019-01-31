<?php


namespace App\Models;

use \PDO;

/**
 * Класс модели списка комнат филиала (Таблица filial_rooms)
 * Class FakeModel
 * @package App\Models
 */
class PermissionsModel extends Model
{
    /**
     * @var \PDO
     */
    protected $db;


    /*
     * Получение прав доступа
     * Return: array[]
     */

    public function getPermissionsModel($id = null)
    {
        $result = [];
        try {
            $sql = "SELECT permissions.* FROM permissions";
            if ($id != null)
                $sql .= " WHERE permissions.id='{$id}'";
            $sql .= " ORDER BY permissions.id ASC";
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
