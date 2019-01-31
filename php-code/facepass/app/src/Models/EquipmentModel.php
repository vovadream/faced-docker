<?php

namespace App\Models;

use \PDO;
use \Slim\Container;


/**
 * Class EquipmentModel
 * @package App\Models
 */
class EquipmentModel extends Model
{
    /**
     * @var \PDO
     */
    protected $db;

    protected $table = 'filial_equipment';


    /**
     * Добавление устройства
     * @param $ip
     * @param $mac
     * @param $type_id
     * @return int
     */
    public function Add($ip, $mac, $type_id)
    {
        $sql = "INSERT INTO filial_equipment (ip_adress, mac_adress, type_id, filial_id) VALUES (:ip, :mac, :type_id, 1)";
        $sth = $this->db->prepare($sql);
        $sth->bindParam("ip", $ip);
        $sth->bindParam("mac", $mac);
        $sth->bindParam("type_id", $type_id);
        $sth->execute();
        return $this->db->lastInsertId();
    }

    /**
     * Поиск устройства по опредеённому полю
     * @param $field
     * @param string $type
     * @return object
     */
    public function FindOne($field, $type = 'mac_adress')
    {
        $sth = $this->db->prepare("SELECT * FROM filial_equipment WHERE {$type}=:field");
        $sth->bindParam("field", $field);
        $sth->execute();
        return $sth->fetchObject();
    }

    /**
     * Получение камеры по иду терминала
     * @param $id int идентификатор терминала
     * @return object
     */
    public function GetTerminalCamera($id)
    {
        $query = "SELECT filial_camera.*
        FROM filial_camera
        LEFT JOIN filial_terminal ON filial_camera.id = filial_terminal.camera_id
        WHERE filial_terminal.equipment_id=:id";
        $sth = $this->db->prepare($query);
        $sth->bindParam("id", $id);
        $sth->execute();
        return $sth->fetchObject();
    }

    /**
     * Обновить определённое поле в таблице устройств
     * @param $id
     * @param $field
     * @param string $type
     * @return object
     */
    public function Update($id, $field, $type = 'ip_adress')
    {
        $sth = $this->db->prepare("UPDATE filial_equipment SET {$type}=:field WHERE id=:id");
        $sth->bindParam("field", $field);
        $sth->bindParam("id", $id);
        $sth->execute();
        return $sth->fetchObject();
    }


    /**
     * Получение типа оборудования
     * @param $field
     * @param string $type
     * @return object
     */
    public function GetType($field, $type = 'id')
    {
        $sth = $this->db->prepare("SELECT * FROM equipment_types WHERE {$type}=:field");
        $sth->bindParam("field", $field);
        $sth->execute();
        return $sth->fetchObject();
    }

    /*
     * Выбор терминала(ов) и привязанных к ним камер
     * $id = null/filial_terminal.equipment_id
     */
    public function getTerminal($id = null, $camera_id = null)
    {
        $terminals = null;
        try {
            $sql = "SELECT filial_terminal.*
                    FROM filial_terminal
                    WHERE filial_terminal.equipment_id IN (SELECT id FROM filial_equipment WHERE type_id='1' AND active=TRUE) ";
            if ($id != null) $sql .= " AND filial_terminal.equipment_id='{$id}' ";
            if ($camera_id != null) $sql .= " AND filial_terminal.camera_id='{$camera_id}' ";
            $sql .= " ORDER BY id ASC ";

            $con = $this->db;
            $query = $con->prepare($sql);
            $query->execute();
            $terminals = $query->fetchAll(PDO::FETCH_OBJ);
            for ($i = 0; $i < count($terminals); $i++) {
                $terminals[$i]->camera = $this->getOneCamera($terminals[$i]->camera_id);
            }
        } catch (Exception $ex) {
            $result['status'] = 'error';
            $result['message'] = 'Неизвестная ошибка (' . $ex->getMessage() . ')';
        }
        return $terminals;
    }

    /**
     * Обнуление сессии терминала
     * @param $id
     */
    public function terminalCloseSession($id)
    {
        $camera = $this->GetTerminalCamera($id);
        if ($camera) {
            $sql = "UPDATE filial_camera SET ff_person_id=NULL WHERE id=:id";
            $con = $this->db->prepare($sql);
            $con->bindParam('id', $camera->id);
            $con->execute();
        }
    }

    /*
     * Выбор одной камеры
     * $param - column
     * $value 
     */
    public function getOneCamera($value = null, $param = 'id')
    {
        $result = [];
        try {
            $sql = "SELECT *
                    FROM filial_camera
                    WHERE {$param}=:value";
            $con = $this->db;
            $query = $con->prepare($sql);
            $query->bindParam("value", $value);
            $query->execute();
            $result = $query->fetchObject();
        } catch (Exception $ex) {
            $result['status'] = 'error';
            $result['message'] = $ex->getMessage();
        }
        return $result;
    }


    public function getTurnstile($id)
    {
        $result = [];
        $sql = "SELECT filial_equipment.*, filial_turnstiles.camera_in_id, filial_turnstiles.camera_out_id
                FROM filial_equipment 
                LEFT JOIN filial_turnstiles ON filial_turnstiles.equipment_id = filial_equipment.id
                WHERE filial_equipment.id=:id";
        $con = $this->db->prepare($sql);
        $con->bindParam('id', $id);
        $con->execute();
        if ($con->rowCount() > 0) {
            $result['status'] = 'success';
            $result['data'] = $con->fetchObject();
            //get camera_in
            $result['data']->camera_in = $this->getOneCamera($result['data']->camera_in_id);
            //get camera_out
            $result['data']->camera_out = $this->getOneCamera($result['data']->camera_out_id);
        } else {
            $result['status'] = 'error';
            $result['message'] = 'Equipment not found';
        }

        return $result;
    }



    /**
     * Получение всех устройств определённого типа
     * @param $id int идентификатор типа устройства
     * @return array objects
     */
    public function getAllByType($id)
    {
        $sql = "SELECT id, name
                FROM filial_equipment
                WHERE active IS TRUE AND type_id=:type";
        $query = $this->db->prepare($sql);
        $query->bindParam('type', $id);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_OBJ);
    }

}
