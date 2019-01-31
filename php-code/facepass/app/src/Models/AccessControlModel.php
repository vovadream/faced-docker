<?php

namespace App\Models;

use \PDO;

class AccessControlModel extends Model
{

    public function sendQuery($string)
    {
        $con = $this->db;
        $query = $con->prepare($string);
        $query->execute();
        if($query->rowCount()>0)
            return $query->fetchAll(PDO::FETCH_OBJ);
        else
            return null;
    }


    /*
    * Вывод списка пользователей по ID типа пользователя + доступы к помещениям
    * Сотрудники - постоянный доступ
    * Посетители - последний доступ
    */
    public function accessControlCategoryUsersModel($id = null, $dep_id=null, $data=null)
    {
        $result = [];
        try {
            $sql = "SELECT users.*, user_types.main_class, workers.id AS worker_id, workers.department_id as dep_id, filial_departament.name AS dep_name, filial_rooms.name AS room_name
        FROM users
        LEFT JOIN user_types ON user_types.id=users.user_type_id
        LEFT JOIN workers ON users.id=workers.user_id
		LEFT JOIN filial_departament ON workers.department_id=filial_departament.id
		LEFT JOIN filial_rooms ON workers.room_id=filial_rooms.id
        WHERE users.filial_id=:filial_id ";
            if($id!=null&&$id!=0) $sql .= " AND (user_type_id='{$id}' OR user_types.main_class='{$id}')";
            if ($dep_id!=null&&$dep_id!=0)
            {
                $departament=$this->sendQuery("SELECT * FROM filial_departament WHERE id={$dep_id}");
                if ($departament[0]->group)  $sql .= " AND workers.department_id='{$dep_id}'";
                else $sql .= " AND workers.department_id IN (SELECT id FROM filial_departament WHERE parent_id={$dep_id})";
            }
            if(isset($data['search_string'])&&$data['search_string']!=null)//&&$searchString!=' ')
            {
                $resultString=explode(' ',$data['search_string']);
                $searchvalues='';
                if (count($resultString)>0)
                {
                    if($id==null) $searchvalues.=' WHERE (';
                    else $searchvalues.=' AND (';
                    for ($searchCount=0;$searchCount<count($resultString);$searchCount++)
                    {
                        if ($searchvalues!=' AND ('&&$searchvalues!=' WHERE (') $searchvalues.=" OR ";
                        $searchvalues.=" lower(users.first_name) LIKE lower('%{$resultString[$searchCount]}%') OR lower(users.patronymic) LIKE lower('%{$resultString[$searchCount]}%')
						OR lower(users.surname) LIKE lower('%{$resultString[$searchCount]}%')";
                    }
                    $searchvalues.=') ';
                    $sql .= $searchvalues;
                }
            }
            $sql .= " ORDER BY users.id ASC";
            $con = $this->db->prepare($sql);
            $con->bindParam('filial_id', $_SESSION['filial_id']);
            $con->execute();
            $result = $con->fetchAll(PDO::FETCH_OBJ);

            for($i=0;$i<count($result);$i++) {
                $sql = null;
                if(($result[$i]->main_class==1 || $result[$i]->user_type_id==1) && ($result[$i]->worker_id!=null)) {
                    //Сотрудник
                    $sql = "SELECT workers_permissions_access.*, filial_rooms.name AS room_name
FROM workers_permissions_access
LEFT JOIN filial_rooms ON filial_rooms.id = workers_permissions_access.room_id
 WHERE workers_permissions_access.worker_id='{$result[$i]->worker_id}' AND workers_permissions_access.status IS TRUE
";
                }
                if($result[$i]->main_class==2 || $result[$i]->main_class==3 || $result[$i]->user_type_id==2 || $result[$i]->user_type_id==3) {
                    //Посетитель/нулевой
                    /*$sql = "SELECT user_acces_rooms.*, filial_rooms.name AS room_name
                            FROM user_acces_rooms
                            LEFT JOIN filial_rooms ON filial_rooms.id = user_acces_rooms.room_id
                            WHERE access_id=(
                        SELECT id FROM user_access WHERE user_id = '{$result[$i]->id}' AND hearing_id IN (
                          SELECT id FROM hearing WHERE hdate = (SELECT CURRENT_DATE)
                        )
                        ORDER BY id DESC LIMIT 1
                    )
                    ";*/
                    $sql = "SELECT user_acces_rooms.*, filial_rooms.name AS room_name
                            FROM user_acces_rooms
                            LEFT JOIN filial_rooms ON filial_rooms.id = user_acces_rooms.room_id
                            WHERE access_id=(SELECT id FROM user_access WHERE user_id = '{$result[$i]->id}'
                        ORDER BY id DESC LIMIT 1)";
                }
                $result[$i]->access = null;
                if($sql!=null) {
                    $con = $this->db->prepare($sql);
                    $con->execute();
                    if ($con->rowCount() >= 1)
                        $result[$i]->access = $con->fetchAll(PDO::FETCH_OBJ);
                }
            }
        }
        catch (Exception $ex) {
            $result['status'] = 'error';
            $result['message'] = $ex->getMessage();
        }
        return $result;
    }

    /*
     * Получение списка меток пользователя
     */
    public function getUserMars($id = null)
    {
        $result = [];
        if($id!=null) {
            $sql = "SELECT user_marks.*, marks.name as mark_name,
worker_add.first_name AS worker_add_first_name, worker_add.patronymic AS worker_add_last_name, worker_add.surname AS worker_add_surname,
worker_close.first_name AS worker_close_first_name, worker_close.patronymic AS worker_close_last_name, worker_close.surname AS worker_close_surname
FROM user_marks
LEFT JOIN marks ON marks.id = user_marks.mark_id
LEFT JOIN users AS worker_add ON worker_add.id = user_marks.worker_id
LEFT JOIN users AS worker_close ON worker_close.id = user_marks.worker_id_close
WHERE user_marks.user_id=:id
ORDER BY user_marks.id DESC
 ";
            $con = $this->db->prepare($sql);
            $con->bindParam('id', $id);
            $con->execute();
            $result = $con->fetchAll(PDO::FETCH_OBJ);
        } else {
            $result['status'] = 'error';
            $result['message'] = 'Ошибка передачи данных';
        }
        return $result;
    }

}