<?php

namespace App\Models;

use \PDO;

class UserMarksModel extends Model
{




    /**
     * Получение списка меток пользователя
     * @return array
     */
    public function findByUserId($id)
    {
        $sql = "SELECT user_marks.*, marks.name as mark_name,
        worker_add.first_name AS worker_add_first_name, worker_add.patronymic AS worker_add_last_name, worker_add.surname AS worker_add_surname,
        worker_close.first_name AS worker_close_first_name, worker_close.patronymic AS worker_close_last_name, worker_close.surname AS worker_close_surname
        FROM user_marks
        LEFT JOIN marks ON marks.id = user_marks.mark_id
        LEFT JOIN users AS worker_add ON worker_add.id = user_marks.worker_id
        LEFT JOIN users AS worker_close ON worker_close.id = user_marks.worker_id_close
        WHERE user_marks.user_id=:id
        ORDER BY user_marks.id DESC";

        $con = $this->db->prepare($sql);
        $con->bindParam('id', $id);
        $con->execute();
        return $con->fetchAll(PDO::FETCH_OBJ);
    }

    /*
    * Получение списка меток
    * Return: array[]
    */
    public function findAll($id = null)
    {
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
        } catch (\Exception $ex) {
            $result = [];
            $result['status'] = "error";
            $result['message'] = "Неизвестная ошибка.";
        }
        return $result;
    }
}