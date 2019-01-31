<?php

namespace App\Models;
use \PDO;

class WorkerRoomsModel extends Model
{

    /**
     * @var \PDO
     */
    protected $db;

    protected $table = 'filial_rooms';



    /**
     * @param null $id
     * @param null $status
     * @return array
     */
    public function getCurrent($id = null, $status = null)
    {
        $result = [];
        if($id!=null) {
            $sql = "SELECT filial_rooms.*, workers_permissions_access.status AS access_status, workers_permissions_access.id AS access_id
            FROM filial_rooms
            LEFT JOIN workers_permissions_access ON workers_permissions_access.room_id = filial_rooms.id AND workers_permissions_access.worker_id = :id
             WHERE filial_id = :filial_id
";
            if($status!=null) {
                $sql .= " AND access_status='true'";
            }
            $con = $this->db->prepare($sql);
            $con->bindParam('id', $id);
            $con->bindParam('filial_id', $_SESSION['filial_id']);
            $con->execute();
            $result = $con->fetchAll(PDO::FETCH_OBJ);
        } else {
            $result['stauts'] = 'error';
            $result['message'] = 'Ошибка передачи данных';
        }
        return $result;
    }

    public function getRoomForWorker($id)
    {
        $model = $this->container->WorkersModel;
        $item = $model->getOne($id);
        return $this->getOne($item['room_id']);
    }
}