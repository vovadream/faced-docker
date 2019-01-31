<?php


namespace App\Models;

use \PDO;

/**
 * Класс модели списка отделов филиала (Таблица filial_departament)
 * Class FilialDepartmentModel
 * @package App\Models
 */
class FilialDepartmentModel extends Model
{
    /**
     * @var \PDO
     */
    protected $db;

    protected $table = 'filial_departament';


    /*
     * Получение списка отделов филиала
     * Return: array[]
     */
    public function getFilialDepartmentModel($id = null, $type='section')
    {
        $result = [];
        try {
            $sql = "SELECT * FROM filial_departament";
            if ($id != null) {
                $sql .= " WHERE id={$id}";
            }
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
        * Получение департамента/отдела по его имени и группе комнат
        */
    public function getFilialDepartmentByName($name) {
        $sql = "SELECT * FROM filial_departament WHERE name = {$name} AND delete IS FALSE";
        $con = $this->db;
        $query = $con->prepare($sql);

        $query->execute();
        if ($query->rowCount() >= 1) {
            $result = $query->fetchAll(PDO::FETCH_OBJ);
        } else {
            $result = null;
        }
        $con = null;
        return $result;
    }

    /*
     * Получение департаментов/отделов
     */
    public function getDepartamentsByName($name, $parent = 0)
    {
        $sql = "SELECT * FROM filial_departament WHERE parent_id='{$parent}' AND delete IS FALSE ";
        $where = "";
        if($name!=null) $where .= " AND name LIKE '%{$name}%' ";
        $sql .= $where." ORDER BY id ASC";
        $con = $this->db;
        $query = $con->prepare($sql);
        $query->execute();
        if ($query->rowCount() >= 1) {
            $result = $query->fetchAll(PDO::FETCH_OBJ);
        } else $result = null;
        return $result;
    }

    /*
     * Сохранение отдела в БД
     * Return: array[]
     * TODO: type сейчас не юзается, Проверить нужен ли он.
     */

    public function addFilialDepartmentModel($data = null)
    {
        $result = [];
        try {
            $con = $this->db;

            $public = isset($data['public']) ? 'true' : 'false';
            $group = isset($data['group']) ? $data['group'] : 'true';
            $parent_id = isset($data['parent_id']) ? $data['parent_id'] : 0;
            $sql = "INSERT INTO filial_departament(name,filial_id,public,parent_id,\"group\", date_create) VALUES(:name, :filial_id, :public, :parent_id, :group, (SELECT CURRENT_DATE))";
            $query = $con->prepare($sql);

            $query->bindParam('name', $data['name']);
            $query->bindParam('filial_id', $_SESSION['filial_id']);
            $query->bindParam('public', $public);
            $query->bindParam('parent_id', $parent_id);
            $query->bindParam('group', $group);
            $query->execute();

            $sql = "SELECT * FROM filial_departament WHERE name=:name AND filial_id=:filial_id AND delete IS FALSE";
            $query = $con->prepare($sql);
            $query->bindParam('name', $data['name']);
            $query->bindParam('filial_id', $_SESSION['filial_id']);
            $query->execute();
            if ($query->rowCount() >= 1) {
                if($parent_id!=0) {
                    $sql = 'UPDATE filial_departament SET "group"=FALSE WHERE id=:parent_id';
                    $con = $this->db->prepare($sql);
                    $con->bindParam('parent_id', $parent_id);
                    $con->execute();
                }

                $departament = $query->fetchObject();
                $result['status'] = 'success';
                $result['id'] = $departament->id;
                $result['reload'] = 'true';
            } else {
                $result['status'] = 'error';
                $result['message'] = 'Отдел не сохранен!';
            }
        } catch (Exception $ex) {
            $result['status'] = 'error';
            $result['message'] = 'Неизвестная ошибка (' . $ex->getMessage() . ')!';
        }
        return $result;
    }

    /*
     * Обновление отдела филиала
     * Return: array[]
     */
    public function updateFilialDepartmentModel($data = null, $id = null, $type=null)
    {
        $result = [];
        try {
            $sql = "UPDATE filial_departament SET name=:name,public=:public, parent_id=:parent_id WHERE id=:id";
            $con = $this->db;
            $query = $con->prepare($sql);
            $query->bindParam('name', $data['name']);
            $ispublic=isset($data['public'])?'true':'false';
            $query->bindParam('public', $ispublic);
            $parent_id=isset($data['parent_id'])?$data['parent_id']:'0';
            $query->bindParam('parent_id', $parent_id);
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
