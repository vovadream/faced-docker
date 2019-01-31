<?php

namespace App\Models;

use \PDO;

/**
 * Class FilialModel
 * @package App\Models
 */
class FilialModel extends Model
{
    /**
     * @var \PDO
     */
    protected $db;


    /**
     * Получение всех отделов по филиалу для теминала
     * @param $filial_id int
     * @return array
     */
    public function GetDepartments($filial_id)
    {
        $sth = $this->db->prepare("SELECT id, name, image, info, parent_id 
                FROM filial_departament WHERE public=TRUE AND delete=FALSE AND filial_id=:filial_id");
        $sth->bindParam("filial_id", $filial_id);
        $sth->execute();
        return $sth->fetchAll();
    }

    /**
     * Получение топологии для терминала
     * @param $filial_id int
     * @return array
     */
    public function GetTerminalTopology($filial_id, $parent_id=0)
    {
        //TODO: Delete?
        $sth = $this->db->prepare("SELECT id, name, image, info, parent_id 
                FROM filial_departament WHERE public=TRUE AND filial_id=:filial_id AND parent_id=:parent_id AND delete=FALSE");
        $sth->bindParam("filial_id", $filial_id);
        $sth->bindParam("parent_id", $parent_id);
        $sth->execute();

        $topology = $sth->fetchAll();
        for($i=0;$i<count($topology);$i++) {
            $topology[$i]['children'] = array_merge($this->GetTerminalTopology($filial_id, $topology[$i]['id']), $this->GetHearing($topology[$i]['id']));
        }


        return $topology;
    }

    /**
     * Получение инфы о департаменте
     * @param $id
     * @return object
     */
    public function GetOneDepartment($id)
    {
        $sth = $this->db->prepare("SELECT * FROM filial_departament WHERE id=:id");
        $sth->bindParam("id", $id);
        $sth->execute();
        return $sth->fetchObject();
    }

    /**
     * Получение слушаний и услуг для терминала
     * @param $department_id int
     * @return array
     */
    public function GetHearing($department_id)
    {
        $date = date_create()->format('Y-m-d');
        $sth = $this->db->prepare("SELECT 
            id, name, pass_in, pass_out, dinner_start, dinner_end 
            FROM hearing WHERE departament_id=:departament_id AND (hdate=:date OR hdate is null)");
        $sth->bindParam("departament_id", $department_id);
        $sth->bindParam("date", $date);
        $sth->execute();
        return $sth->fetchAll();
    }

    /**
     * Получение комнаты
     * @param $id
     * @return object
     */
    public function GetRoom($id)
    {
        $sth = $this->db->prepare("SELECT * FROM filial_rooms WHERE id=:id");
        $sth->bindParam("id", $id);
        $sth->execute();
        return $sth->fetchObject();
    }
}