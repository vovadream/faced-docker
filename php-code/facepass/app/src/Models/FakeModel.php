<?php


namespace App\Models;

use \PDO;

/**
 * Служит для доставания "не настоящих" данных(для тестов)
 * Class FakeModel
 * @package App\Models
 */
class FakeModel
{
    /**
     * @var \PDO
     */
    private $db;

    /**
     * LoggerModel constructor.
     * @param PDO $db
     */
    public function  __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Получение данных для скана по person_id
     * @param $id_person
     * @return object
     */
    public function GetOneScan($id_person)
    {
        $sth = $this->db->prepare("SELECT * FROM fake_scans WHERE id_person=:id");
        $sth->bindParam("id", $id_person);
        $sth->execute();
        return $sth->fetchObject();
    }
}
