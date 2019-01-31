<?php


namespace App\Models;

use \PDO;

/**
 * Класс модели услуг (Таблица hearing)
 * Class FakeModel
 * @package App\Models
 */
class HearingsModel extends Model
{
    /**
     * @var \PDO
     */
    protected $db;

    protected $table = 'hearing';

    /*
     * Получение списка слушаний
     * Return: array[]
     */

    public function getHearingsModel($id = null) {
        $result = [];
        try {
            $sql = "SELECT * FROM hearing";
            if($id != null)
                $sql .= " WHERE id='{$id}'";
            $sql .= " ORDER BY id ASC";
            $con = $this->db;
            $query = $con->prepare($sql);
            $query->execute();
            if($query->rowCount()>=1) {
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
     * Обновление слушания
     * Return: array[]
     */

    public function updateHearingModel($data = null, $id = null)
    {
        $result = [];
        try {
            $sql = "UPDATE hearing SET room_id=:room_id, name=:name, hdate=:hdate,
			worker_id=:worker_id, time=:time, code=:code WHERE id=:id";
            $con = $this->db;
            $query = $con->prepare($sql);
            $query->bindParam('room_id', $data['room_id']);
            $query->bindParam('name', $data['name']);
            $hdate=pg_escape_string($data['hdate']);
            $query->bindParam('hdate', $hdate );
            $query->bindParam('worker_id', $data['worker_id']);
            $query->bindParam('time', $data['time']);
            $query->bindParam('code', $data['code']);
            $query->bindParam('id', $id);
            $query->execute();
            $sql = "SELECT * FROM hearing_rooms WHERE hearing_id='{$id}'";
            $query = $con->prepare($sql);
            $query->execute();
            $hearing_rooms = $query->fetchAll(PDO::FETCH_OBJ);
            $rooms = $this->getRoomModel();
            for ($i = 0; $i < count($rooms); $i++)
            {
                $find=false;
                for ($j=0;$j<count($hearing_rooms);$j++)
                {
                    if ($rooms[$i]->id == $hearing_rooms[$j]->room_id)
                    {
                        if ($hearing_rooms[$j]->status==0)
                        {
                            if ((isset($data['room_'.$rooms[$i]->id]))||($rooms[$i]->id==$data['room_id']))
                            {
                                $sql = "UPDATE hearing_rooms SET status='true' WHERE hearing_id='{$id}' AND room_id='{$rooms[$i]->id}'";
                                $query = $con->prepare($sql);
                                $query->execute();
                            }
                        }
                        if ($hearing_rooms[$j]->status==1)
                        {
                            if (!isset($data['room_'.$rooms[$i]->id])&&($rooms[$i]->id!=$data['room_id']))
                            {
                                $sql = "UPDATE hearing_rooms SET status='false' WHERE hearing_id='{$id}' AND room_id='{$rooms[$i]->id}'";
                                $query = $con->prepare($sql);
                                $query->execute();
                            }
                        }
                        $find=true;
                        break;
                    }
                }
                if ($find==false)
                {
                    $sql = 'INSERT INTO hearing_rooms (hearing_id, room_id, status) VALUES (:hearing_id,:room_id, :status)';
                    $query = $con->prepare($sql);
                    if ($rooms[$i]->id==$data['room_id']) $status='true';
                    else $status = (isset($data['room_'.$rooms[$i]->id])) ? 'true' : 'false';
                    $query->bindParam('hearing_id', $id);
                    $query->bindParam('room_id', $rooms[$i]->id);
                    $query->bindParam('status', $status);
                    $query->execute();
                }
            }
            $result['status'] = 'success';
            $result['reload'] = 'true';
        } catch (Exception $ex) {
            $result['status'] = 'error';
            $result['reload'] = 'Неизвестная ошибка (' . $ex->getMessage() . ')';
        }
        return $result;
    }

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

    public function getByDate($date)
    {
        $sth = $this->db->prepare("SELECT id, name FROM {$this->table} 
                WHERE (hdate=:date OR hdate is NULL)");
        $sth->bindParam('date', $date);
        $sth->execute();
        return $sth->fetchAll(PDO::FETCH_OBJ);
    }
}
