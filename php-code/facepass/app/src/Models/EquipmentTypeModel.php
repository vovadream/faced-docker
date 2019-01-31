<?php


namespace App\Models;

use \PDO;

/**
 * Класс модели типов документов (Таблица document_type)
 * Class DocumentTypeModel
 * @package App\Models
 */
class EquipmentTypeModel extends Model
{
    /**
     * @var \PDO
     */
    protected $db;


    /*
     * Получение списка типов оборудования
     * Return: array[]
     */
    public function getEquipmentTypeModel($id = null)
    {
        $result = "";
        try {
            $sql = "SELECT * FROM equipment_types";
            if ($id != null)
                $sql .= " WHERE id={$id}";
            $sql .= " ORDER BY id ASC";
            $con = $this->db;
            $query = $con->prepare($sql);
            $query->execute();
            if ($query->rowCount() >= 1) {
                $result = $query->fetchAll(PDO::FETCH_OBJ);
            } else {
                $result = [];
                $result['status'] = "error";
                $result['message'] = "Нет типов документов.";
            }
            $con = null;
        } catch (Exception $ex) {
            $result['status'] = "error";
            $result['message'] = "Неизвестная ошибка.";
        }
        return $result;
    }

    /*
     * Сохранение типа документа в БД
     * Return: array[]
     */

    public function addEquipmentTypeModel($data = null)
    {
        $result = [];
        try {
            $sql = "INSERT INTO equipment_types(name) VALUES(:name)";
            $con = $this->db;
            $query = $con->prepare($sql);
            $query->bindParam('name', $data['name']);
            $query->execute();
            $sql = "SELECT * FROM equipment_types WHERE name=:name";
            $query = $con->prepare($sql);
            $query->bindParam('name', $data['name']);
            $query->execute();
            if ($query->rowCount() >= 1) {
                $result['status'] = 'success';
                $result['reload'] = 'true';
            } else {
                $result['status'] = 'error';
                $result['reload'] = 'Тип документа не сохранен';
            }
        } catch (Exception $ex) {
            $result['status'] = 'error';
            $result['reload'] = 'Неизвестная ошибка (' . $ex->getMessage() . ')';
        }
        return $result;
    }

    /*
     * Обновление типа оборудования
     * Return: array[]
     */
    public function updateEquipmentTypeModel($data = null, $id = null)
    {
        $result = [];
        try {
            $sql = "UPDATE equipment_types SET name=:name WHERE id=:id";
            $con = $this->db;
            $query = $con->prepare($sql);
            $query->bindParam('name', $data['name']);
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
}
