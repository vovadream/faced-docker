<?php


namespace App\Models;

use \PDO;

/**
 * Class LoggerModel
 * @package App\Models
 */
class LoggerModel extends Model
{
    /**
     * @var \PDO
     */
    protected $db;

    /**
     * Добавить действие в лог
     * @param array $data
     * @return int
     */
    public function Add($data = [])
    {
        $date = date_create()->format('Y-m-d');
        $time = date_create()->format('H:i:s');
        $sql = "INSERT INTO logs
            (message, ldate, ltime, user_id, filial_id, equipment_id, room_id, debug_info)
			VALUES(:message, :ldate, :ltime, :user_id, :filial_id, :equipment_id, :room_id, :debug_info)";
        $sth = $this->db->prepare($sql);
        $sth->bindParam('message', $data['message']);
        $sth->bindParam('ldate', $date);
        $sth->bindParam('ltime', $time);
        $sth->bindParam('user_id', $data['user_id']);
        $sth->bindParam('filial_id', $data['filial_id']);
        $sth->bindParam('equipment_id', $data['equipment_id']);
        $sth->bindParam('room_id', $data['room_id']);
        $sth->bindParam('debug_info', $data['debug']);
        $sth->execute();
        return $this->db->lastInsertId();
    }
}