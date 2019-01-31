<?php


namespace App\Models;

use \PDO;

/**
 * Class DocumentsModel
 * @package App\Models
 */
class DocumentsModel extends Model
{
    /**
     * @var \PDO
     */
    protected $db;


    /**
     * Добавление Паспорта РФ
     * @param array $data
     * @return int
     */
    public function AddPassportRF($data = [])
    {
        if(empty($data['passport_date']))
            $data['passport_date'] = null;
        if(empty($data['date_birth']))
            $data['date_birth'] = null;
        $sql = "INSERT INTO document_passport_rf
            (surname, first_name, patronymic, series_number, date_birth, gender, birthplace, 
            passport_date, passport_code, passport_place, registration_place, page_one, page_two, page_three)
			VALUES(:surname, :first_name, :patronymic, :series_number, :date_birth, :gender, :birthplace, 
            :passport_date, :passport_code, :passport_place, :registration_place, :page_one, :page_two, :page_three)";
        $sth = $this->db->prepare($sql);
        $sth->bindParam('surname', $data['surname']);
        $sth->bindParam('first_name', $data['first_name']);
        $sth->bindParam('patronymic', $data['patronymic']);
        $sth->bindParam('series_number', $data['series_number']);
        $sth->bindParam('date_birth', $data['date_birth']);
        $sth->bindParam('gender', $data['gender']);
        $sth->bindParam('birthplace', $data['birthplace']);
        $sth->bindParam('passport_date', $data['passport_date']);
        $sth->bindParam('passport_code', $data['passport_code']);
        $sth->bindParam('passport_place', $data['passport_place']);
        $sth->bindParam('registration_place', $data['registration_place']);
        $sth->bindParam('page_one', $data['scans'][0]);
        $sth->bindParam('page_two', $data['scans'][1]);
        $sth->bindParam('page_three', $data['scans'][2]);
        $sth->execute();
        //добавляем связь
        $id = $this->db->lastInsertId();
        $sql = "INSERT INTO user_documents
            (user_id, type_id, document_id)
			VALUES(:user_id, 1, :document_id)";
        $sth = $this->db->prepare($sql);
        $sth->bindParam('user_id', $data['user_id']);
        $sth->bindParam('document_id', $id);
        $sth->execute();
        return $this->db->lastInsertId();
    }

    /**
     * Получение паспорта РФ пользователя
     * @param $user_id
     * @return object
     */
    public function GetPassportRF($user_id)
    {
        $sth = $this->db->prepare("SELECT document_passport_rf.* 
            FROM document_passport_rf 
            LEFT JOIN user_documents ON document_passport_rf.id = document_id
            WHERE user_documents.user_id=:user_id");
        $sth->bindParam("user_id", $user_id);
        $sth->execute();
        return $sth->fetchObject();
    }
}