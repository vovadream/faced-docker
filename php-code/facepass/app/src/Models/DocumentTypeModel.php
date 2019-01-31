<?php


namespace App\Models;

use \PDO;

/**
 * Класс модели типов документов (Таблица document_type)
 * Class DocumentTypeModel
 * @package App\Models
 */
class DocumentTypeModel extends Model
{
    /**
     * @var \PDO
     */
    protected $db;


    /*
     * Получение списка типов документов
     * Return: array[]
     */
    public function getUserTypeDocumentsModel($id = null)
    {
        $result = [];
        try {
            $sql = "SELECT * FROM document_type";
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

    public function addUserDocumentTypeModel($data = null)
    {
        $result = [];
        try {
            $sql = "INSERT INTO document_type(name) VALUES(:name)";
            $con = $this->db;
            $query = $con->prepare($sql);
            $query->bindParam('name', $data['name']);
            $query->execute();
            $sql = "SELECT * FROM document_type WHERE name=:name";
            $query = $con->prepare($sql);
            $query->bindParam('name', $data['name']);
            $query->execute();
            if ($query->rowCount() >= 1) {
                $result['status'] = 'success';
                $result['reload'] = 'true';
            } else {
                $result['status'] = 'error';
                $result['message'] = 'Тип документа не сохранен!';
            }
        } catch (Exception $ex) {
            $result['status'] = 'error';
            $result['message'] = 'Неизвестная ошибка (' . $ex->getMessage() . ')';
        }
        return $result;
    }

    /*
     * Обновление типа документа
     * Return: array[]
     */
    public function updateUserDocumentTypeModel($data = null, $id = null)
    {
        $result = [];
        try {
            $sql = "UPDATE document_type SET name=:name WHERE id=:id";
            $con = $this->db;
            $query = $con->prepare($sql);
            $query->bindParam('name', $data['name']);
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
     * Удаление типа документа
     * Return: array[]
     * TODO: Написать метод
     */
    public function removeUserDocumentTypeModel($data = null, $id = null)
    {
//        $result = [];
//        try {
//            $sql = "UPDATE document_type SET name=:name WHERE id=:id";
//            $con = $this->db;
//            $query = $con->prepare($sql);
//            $query->bindParam('name', $data['name']);
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
}
