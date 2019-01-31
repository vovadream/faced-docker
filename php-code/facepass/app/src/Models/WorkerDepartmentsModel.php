<?php

namespace App\Models;

use \PDO;

class WorkerDepartmentsModel extends Model
{


    /**
     * @var \PDO
     */
    protected $db;



    public function findOne($id = null, $status = null)
    {
        $result = [];
        if ($id != null) {
            try {
                $sql = "SELECT filial_departament.*,
workers_departamet_access.status AS access_status, workers_departamet_access.id AS access_id
            FROM filial_departament
            LEFT JOIN workers_departamet_access ON workers_departamet_access.departament_id = filial_departament.id AND workers_departamet_access.worker_id = :id
             WHERE filial_departament.filial_id = NULL OR filial_departament.filial_id = :filial_id
";
                if ($status != null) {
                    $sql .= " AND access_status='true'";
                }
                $con = $this->db->prepare($sql);
                $con->bindParam('id', $id);
                $con->bindParam('filial_id', $_SESSION['filial_id']);
                $con->execute();
                $result = $con->fetchAll(PDO::FETCH_OBJ);
            } catch (Exception $ex) {
                $result['stauts'] = 'error';
                $result['message'] = 'Неизвестная ошибка (' . $ex->getMessage() . ')';
            }
        } else {
            $result['stauts'] = 'error';
            $result['message'] = 'Ошибка передачи данных';
        }
        return $result;
    }
}