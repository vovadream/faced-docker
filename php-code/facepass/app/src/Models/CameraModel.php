<?php

namespace App\Models;

use \PDO;

/**
 * Class CameraModel
 * @package App\Models
 */
class CameraModel extends Model
{
    /**
     * @var \PDO
     */
    protected $db;

    protected $table = 'filial_camera';

    /**
     * Добавление камеры
     * @param $data array
     * @return int
     */
    public function Add($data)
    {
        $sql = "INSERT INTO {$this->table} 
                (equipment_id, stream_url, face_min_width, face_min_height) 
                VALUES (:equipment_id, :stream_url, :face_min_width, :face_min_height)";
        $sth = $this->db->prepare($sql);
        $sth->bindParam("equipment_id", $data['equipment_id']);
        $sth->bindParam("stream_url", $data['stream_url']);
        $sth->bindParam("face_min_width", $data['face_min_width']);
        $sth->bindParam("face_min_height", $data['face_min_height']);
        $sth->execute();
        return $this->db->lastInsertId();
    }

    /**
     * Добавление связи камеры к терминалу
     * @param $id_cam
     * @param $id_eq
     * @return int
     */
    public function AddRelationToTerminal($id_cam, $id_eq)
    {
        $sql = "INSERT INTO filial_terminal
                (equipment_id, camera_id) 
                VALUES (:equipment_id, :camera_id)";
        $sth = $this->db->prepare($sql);
        $sth->bindParam("equipment_id", $id_eq);
        $sth->bindParam("camera_id", $id_cam);
        $sth->execute();
        return $this->db->lastInsertId();
    }

    /**
     * Добавление связи камеры к проходной
     * @param $id_cam
     * @param $id_eq
     * @param $in bool
     * @return int
     */
    public function AddRelationToTurnstile($id_cam, $id_eq, $in = true)
    {
        //определяем на вход или на выход ставим камеру
        $col = 'camera_out_id';
        if ($in)
            $col = 'camera_in_id';

        //ищем есть ли уже к проходу привязанные камеры
        $sql = "SELECT * FROM filial_turnstiles WHERE equipment_id=:equipment_id";
        $sth = $this->db->prepare($sql);
        $sth->bindParam('equipment_id', $id_eq);
        $sth->execute();
        $eq = $sth->fetch();

        //если нашли то обновляем, нет так вставляем
        if ($eq) {
            $sql = "UPDATE filial_turnstiles
                SET {$col}=:camera_id
                WHERE equipment_id=:equipment_id";
        } else {
            $sql = "INSERT INTO filial_turnstiles
                (equipment_id, {$col}) 
                VALUES (:equipment_id, :camera_id)";
        }

        $sth = $this->db->prepare($sql);
        $sth->bindParam("equipment_id", $id_eq);
        $sth->bindParam("camera_id", $id_cam);
        $sth->execute();
        return $this->db->lastInsertId();
    }

    /**
     * Удаление связей с устройствами
     * @param $id_cam
     * @return bool
     */
    public function DelRelations($id_cam)
    {
        //удаляем связи к терминалам
        $sql = "DELETE FROM filial_terminal WHERE camera_id=:value";
        $con = $this->db->prepare($sql);
        $con->bindParam('value', $id_cam);
        $con->execute();

        //удаляем связи к проходным
        $sql = "DELETE FROM filial_turnstiles WHERE camera_in_id=:cam_id OR camera_out_id=:cam_id";
        $sth = $this->db->prepare($sql);
        $sth->bindParam('cam_id', $id_cam);
        $sth->execute();

        return true;
    }

}