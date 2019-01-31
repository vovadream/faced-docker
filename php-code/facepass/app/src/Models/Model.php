<?php


namespace App\Models;

use \PDO;
use Slim\Container;



/**
 * Служит для хранения списка типов (Ролей) пользователей.
 * Class UserTypeModel
 * @package App\Models
 */
class Model
{
    /**
     * @var \PDO
     */
    protected $db;

    protected static $connect;

    protected $table = null;

    protected $container;

    public static function setConnect($db) {
        if(empty(static::$connect)) {
            static::$connect = $db;
        }
    }

    public function __construct(Container $c)
    {
        $this->container = $c;
        self::setConnect($c->get('db'));
        if(empty($this->db)) {
            $this->db = static::$connect;
        }
    }

    /**
     * Получить одну запись таблицы
     * @param $value
     * @param string $field название колонки для выбора
     * @return bool|object
     */
    public function getOne($value, $field = 'id')
    {
        if (is_null($this->table))
            return false;

        $sql = "SELECT * FROM {$this->table} WHERE {$field}=:value";
        $con = $this->db->prepare($sql);
        $con->bindParam('value', $value);
        $con->execute();
        return $con->fetch();
    }

    /**
     * Выборка всех записей из таблицы
     * @param bool $active - достать только активные
     * @return array|bool
     */
    public function getAll($active = false)
    {
        if (is_null($this->table))
            return false;

        $where = '';
        if ($active)
            $where = 'WHERE active=TRUE';

        $sth = $this->db->prepare("SELECT * FROM {$this->table} {$where}");
        $sth->execute();
        return $sth->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Удалить 1 запись
     * @param $value
     * @param string $field
     * @return bool
     */
    public function delOne($value, $field = 'id')
    {
        if (is_null($this->table))
            return false;

        $sql = "DELETE FROM {$this->table} WHERE {$field}=:value";
        $con = $this->db->prepare($sql);
        $con->bindParam('value', $value);
        $con->execute();
        return true;
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


}
