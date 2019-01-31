<?php

namespace App\Models;
use \PDO;

class UserImagesModel extends Model
{

    /**
     * @var \PDO
     */
    protected $db;

    protected $table = 'user_images';


    /**
     * @param null $id
     * @param null $status
     * @return array
     */
    public function getByUser($userId, $type = 'photo')
    {
        $result = [];

            $sql = "SELECT * FROM user_images WHERE user_id = :userId AND type = :type";
            $con = $this->db->prepare($sql);
            $con->bindParam('userId', $userId);
            $con->bindParam('type', $type);
            $con->execute();
            $result = $con->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }
}