<?php

namespace App\Models;

use \PDO;
use DateInterval;
use DateTime;

class InterfaceModel extends Model
{
    /**
     * @var \PDO
     */
    protected $db;


    /*
     * Получение информации о пользователе
     */
    public function getUserInfoModel($id = null)
    {
        $result = [];
        $sql = "SELECT * FROM users WHERE id=:id";
        $sql .= " ORDER BY id ASC";
        try {
            $con = $this->db;
            $query = $con->prepare($sql);
            $query->bindParam("id", $id);
            $query->execute();
            $db = null;
            $result = $query->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $ex) {
            $result['status'] = 'error';
            $result['message'] = $ex->getMessage();
        }
        return $result;
    }

    /*
     * Авторизация пользователя
     * Return: array[]
     */
    public function authUserModel($data)
    {
        $sql = "SELECT workers.*, users.first_name, users.patronymic, users.surname
FROM workers
LEFT JOIN users ON users.id = workers.user_id
WHERE login=:login
";
        $result = [];
        $result['status'] = 'error';
        $result['message'] = 'Неверный логин или пароль';
        $result['div'] = 'authResult';
        try {

            $con = $this->db;
            $query = $con->prepare($sql);
            $query->bindParam('login', $data['login']);
            $query->execute();
            $count = $query->rowCount();
            if ($count >= 1) {
                $user = $query->fetch();
                if ($user['password'] == md5($data['password'])) {
                    $result['status'] = 'success';
                    $result['message'] = 'Вы успешно прошли авторизацию!';
                    $result['reload'] = 'true';
                    $result['page'] = base_path().'main/';

                    $_SESSION['id'] = $user['id'];
                    $_SESSION['first_name'] = $user['first_name'];
                    $_SESSION['patronymic'] = $user['patronymic'];
                    $_SESSION['surname'] = $user['surname'];
                    $_SESSION['filial_id'] = $user['filial_id'];
                } else {
                    return $result;
                }
            } else {
                return $result;
            }
            $con = null;
        } catch (Exception $ex) {
            $result['message'] = $ex->getMessage();
            return $result;
        }
        return $result;
    }

    /*
     * Список доступных интерфейсов пользователя
     * Return: array[]
     */
    public function getMenuArrayModel()
    {
        $menuArray = [];
        try {
            $sql = "SELECT permissions_to_interfaces.*, interfaces.name, interfaces.url, interfaces.active_icon, interfaces.passive_icon
 FROM permissions_to_interfaces
 LEFT JOIN interfaces ON interfaces.id = permissions_to_interfaces.interface_id
 WHERE permissions_to_interfaces.status = true AND permissions_to_interfaces.worker_id=:user_id
 ORDER BY interfaces.num ASC
        ";
            $con = $this->db;
            $query = $con->prepare($sql);
            $query->bindParam('user_id', $_SESSION['id']);
            $query->execute();
            if ($query->rowCount() >= 1) {
                $menuArray = $query->fetchAll(PDO::FETCH_OBJ);
            } else {
                $menuArray['status'] = "error";
                $menuArray['message'] = "Нет доступных интерфейсов.";
            }
            $con = null;
        } catch (Exception $ex) {
            $menuArray['status'] = "error";
            $menuArray['message'] = $ex->getMessage() . " <br>### SQL: " . $query->queryString;
        }
        return $menuArray;
    }

    /*
     * Список существующих интерфейсов
     * Return: array[]
     */
    public function getInterfacesModel($id = null)
    {
        
        $interfaces = [];
        try {
            $sql = "SELECT * FROM interfaces ";
            if ($id != null) $sql .= " WHERE id='{$id}' ";
            $sql .= " ORDER BY interfaces.id ASC";
            $con = $this->db;
            $query = $con->prepare($sql);
            $query->execute();
            if ($query->rowCount() >= 1) {
                $interfaces = $query->fetchAll(PDO::FETCH_OBJ);
            } else {
                $interfaces['status'] = "error";
                $interfaces['message'] = "Нет доступных интерфейсов.";
            }
            $con = null;
        } catch (Exception $ex) {
            $interfaces['status'] = "error";
            $interfaces['message'] = $ex->getMessage() . " ### SQL: " . $query->queryString;
        }
        return $interfaces;
    }

    /*
     * Деавторизация пользователя
     * Return: array[]
     */
    public function logoutUserModel()
    {
        session_destroy();
        session_start();
        $result = [];
        $result['status'] = 'success';
        $result['reload'] = 'true';
        return $result;
    }

    /*
     * Создание интерфейса
     * Return: array[]
     */
    public function addInterfaceModel($data)
    {
        $result = [];
        $warnings = "";
        try {
            //Cоздаем запись в интерфейсах
            $sql = "INSERT INTO interfaces (name, url, passive_icon, active_icon, num) VALUES(:name, :url, :passive_icon, :active_icon, :num)";
            $con = $this->db;
            $query = $con->prepare($sql);
            $passive_icon = SaveImage($_FILES['passive_icon']['tmp_name'], "icon");
            $active_icon = SaveImage($_FILES['active_icon']['tmp_name'], "icon");

            if ($passive_icon == null) $warnings .= "### Не загружена иконка интерфейса.\\r\\n";
            if ($active_icon == null) $warnings .= "### Не загружена иконка интерфейса.\\r\\n";

            $query->bindParam('name', $data['name']);
            $query->bindParam('url', $data['url']);
            $query->bindParam('passive_icon', $passive_icon);
            $query->bindParam('active_icon', $active_icon);
            $query->bindParam('num', $data['num']);
            $query->execute();

            //Проверяем сохранение
            $sql = "SELECT * FROM interfaces WHERE name=:name AND url=:url AND num=:num";
            $query = $con->prepare($sql);
            $query->bindParam('name', $data['name']);
            $query->bindParam('url', $data['url']);
            $query->bindParam('num', $data['num']);
            $query->execute();
            if ($query->rowCount() >= 1) {
                /*
                 * Если интерфейс сохранён - необходимо добавить его всем системным администраторам системы
                 */
                $interface = $query->fetchAll(PDO::FETCH_OBJ);
                $sql = "SELECT id FROM workers WHERE permission_id='1'";
                $query = $con->prepare($sql);
                $query->execute();
                $workers = $query->fetchAll(PDO::FETCH_OBJ);
                $sql = "INSERT INTO permissions_to_interfaces (worker_id, interface_id, status) VALUES";
                $values = "";
                for ($i = 0; $i < count($workers); $i++) {
                    if ($values != "") $values .= ",";
                    $values .= "('{$workers[$i]->id}','{$interface[0]->id}',true)";
                }
                $sql .= $values;
                $query = $con->prepare($sql);
                $query->execute();
                $message = "Интерфейс был сохранён.\\r\\n";
                if ($warnings == "") $message .= "Замечаний нет";
                else $message .= "Замечания:\\r\\n" . $warnings;
                $result['status'] = 'success';
                $result['message'] = $message;
            } else {
                $message = "Интерфейс не был сохранён.\\r\\n";
                if ($warnings == "") $message .= "Замечаний нет";
                else $message .= "Замечания:\\r\\n" . $warnings;
                $result['status'] = 'error';
                $result['message'] = $message;
            }
            $con = null;
        } catch (Exception $ex) {
            $message = "Неизвестная ошибка (" . $ex->getMessage() . ")\\r\\n";
            if ($warnings == "") $message .= "Замечаний нет";
            else $message .= "Замечания:\\r\\n" . $warnings;

            $result['status'] = 'error';
            $result['message'] = $message;
        }
        return $result;
    }

    /*
     * Обновление интерфейса
     * Return: array[]
     */
    public function updateInterfaceModel($data, $id = null)
    {
        $result = [];
        $warnings = "";
        try {
            //Cоздаем запись в интерфейсах
            $sql = "UPDATE interfaces SET name=:name, url=:url, active_icon=:active_icon, passive_icon=:passive_icon, num=:num WHERE id=:id";
            $con = $this->db;
            $query = $con->prepare($sql);

            $active_icon = SaveImage($_FILES['active_icon']['tmp_name'],"icon");
            $passive_icon = SaveImage($_FILES['passive_icon']['tmp_name'],"icon");

            $interface = $this->getInterfacesModel($id);
            if ($active_icon == null)
                $active_icon = $interface[0]->active_icon;
            if ($passive_icon == null)
                $passive_icon = $interface[0]->passive_icon;

            $query->bindParam('name', $data['name']);
            $query->bindParam('url', $data['url']);
            $query->bindParam('active_icon', $active_icon);
            $query->bindParam('passive_icon', $passive_icon);
            $query->bindParam('num', $data['num']);
            $query->bindParam('id', $id);
            $query->execute();

            //Проверяем сохранение
            $sql = "SELECT * FROM interfaces WHERE name=:name AND url=:url AND num=:num";
            $query = $con->prepare($sql);
            $query->bindParam('name', $data['name']);
            $query->bindParam('url', $data['url']);
            $query->bindParam('num', $data['num']);
            $query->execute();
            if ($query->rowCount() >= 1) {
                $result['status'] = 'success';
                $result['reload'] = 'true';
            } else {
                $message = "Интерфейс не был сохранён.\\r\\n";
                $result['status'] = 'error';
                $result['reload'] = $message;
            }
            $con = null;
        } catch (Exception $ex) {
            $message = "Неизвестная ошибка (" . $ex->getMessage() . ")\\r\\n";
            if ($warnings == "") $message .= "Замечаний нет";
            else $message .= "Замечания:\\r\\n" . $warnings;
            $result['status'] = 'error';
            $result['reload'] = $message;
        }
        return $result;
    }

    /*
     * //TODO Deleted
     * Сохранение временных файлов из POST
     * Return: array[]
     */
    public function saveFileModel($path = null, $data = null)
    {
        $result = [];
        $result['status'] = "error";
        $file = pathinfo(basename($_FILES[$data]['name']));
        $filename = RandomString(30);
        $filename .= "." . $file['extension'];
        $uploadfile = $path . $filename;
        $webfile = upload_path() . $path . $filename;
        if (move_uploaded_file($_FILES[$data]['tmp_name'], $uploadfile)) {
            $result['status'] = "success";
            $result['webpath'] = $webfile;
            $result['syspath'] = $uploadfile;
            shell_exec("chmod 777 {$uploadfile}");
        } else {
            $result['status'] = "error";
            $result['webpath'] = null;
            $result['syspath'] = null;
        }
        return $result;
    }

    /*
     * Получение списка меток
     * Return: array[]
     * TODO: Удалить. Перенес в MarkModel
     */
    public function getMarksModel($id = null)
    {
        $result = "";
        try {
            $sql = "SELECT marks.* FROM marks";
            if ($id != null)
                $sql .= " WHERE marks.id={$id}";
            $sql .= " ORDER BY marks.id ASC";
            $con = $this->db;
            $query = $con->prepare($sql);
            $query->execute();
            if ($query->rowCount() >= 1) {
                $result = $query->fetchAll(PDO::FETCH_OBJ);
            } else {
                $result = [];
                $result['status'] = "error";
                $result['message'] = "Нет меток.";
            }
            $con = null;
        } catch (Exception $ex) {
            $result['status'] = "error";
            $result['message'] = "Неизвестная ошибка.";
        }
        return $result;
    }

    /*
     * Сохранение метки в БД
     * Return: array[]
     * TODO: Удалить. Перенес в MarkModel
     */

    public function addMarkModel($data = null)
    {
        $result = [];
        try {
            $sql = "INSERT INTO marks(name, quite_alert) VALUES(:name, :quite_alert)";
            $con = $this->db;
            $query = $con->prepare($sql);
            $query->bindParam('name', $data['name']);
            $quite_alert=isset($data['quite_alert'])?'true':'false';
            $query->bindParam('quite_alert', $data['quite_alert']);
            $query->execute();
            $sql = "SELECT * FROM marks WHERE name=:name";
            $query = $con->prepare($sql);
            $query->bindParam('name', $data['name']);
            $query->execute();
            if ($query->rowCount() >= 1) {
                $result['status'] = 'success';
                $result['reload'] = 'true';
            } else {
                $result['status'] = 'error';
                $result['message'] = 'Метка не сохранена!';
            }
        } catch (Exception $ex) {
            $result['status'] = 'error';
            $result['message'] = 'Неизвестная ошибка (' . $ex->getMessage() . ')!';
        }
        return $result;
    }

    /*
     * Обновление метки
     * Return: array[]
     * TODO: Удалить. Перенес в MarkModel
     */
    public function updateMarkModel($data = null, $id = null)
    {
        $result = [];
        try {
            $sql = "UPDATE marks SET name=:name, quite_alert=:quite_alert WHERE id=:id";
            $con = $this->db;
            $query = $con->prepare($sql);
            $query->bindParam('name', $data['name']);
            $quite_alert=isset($data['quite_alert'])?'true':'false';
            $query->bindParam('quite_alert', $data['quite_alert']);
            $query->bindParam('id', $id);
            $query->execute();
            $result['status'] = 'success';
            $result['reload'] = 'true';
        } catch (Exception $ex) {
            $result['status'] = 'error';
            $result['message'] = 'Неизвестная ошибка';
        }
        return $result;
    }

    /*
     * Получение списка типов документов
     * Return: array[]
     * TODO: Удалить. Перенес в DocumentTypeModel
     */
    public function getUserTypeDocumentsModel($id = null)
    {
        $result = [];
        try {
            $sql = "SELECT * FROM document_type";
            if ($id != null)
                $sql .= " WHERE id={$id}";
            $sql .= " ORDER BY id ASC";
            $con = $this->db;
            $query = $con->prepare($sql);
            $query->execute();
            if ($query->rowCount() >= 1) {
                $result = $query->fetchAll(PDO::FETCH_OBJ);
            } else {
                $result = [];
                $result['status'] = "error";
                $result['message'] = "Нет типов документов.";
            }
            $con = null;
        } catch (Exception $ex) {
            $result['status'] = "error";
            $result['message'] = "Неизвестная ошибка.";
        }
        return $result;
    }

    /*
     * Сохранение типа документа в БД
     * Return: array[]
     * TODO: Удалить. Перенес в DocumentTypeModel
     */

    public function addUserDocumentTypeModel($data = null)
    {
        $result = [];
        try {
            $sql = "INSERT INTO document_type(name) VALUES(:name)";
            $con = $this->db;
            $query = $con->prepare($sql);
            $query->bindParam('name', $data['name']);
            $query->execute();
            $sql = "SELECT * FROM document_type WHERE name=:name";
            $query = $con->prepare($sql);
            $query->bindParam('name', $data['name']);
            $query->execute();
            if ($query->rowCount() >= 1) {
                $result['status'] = 'success';
                $result['reload'] = 'true';
            } else {
                $result['status'] = 'error';
                $result['message'] = 'Тип документа не сохранен!';
            }
        } catch (Exception $ex) {
            $result['status'] = 'error';
            $result['message'] = 'Неизвестная ошибка (' . $ex->getMessage() . ')';
        }
        return $result;
    }

    /*
     * Обновление типа документа
     * Return: array[]
     * TODO: Удалить. Перенес в DocumentTypeModel
     */
    public function updateUserDocumentTypeModel($data = null, $id = null)
    {
        $result = [];
        try {
            $sql = "UPDATE document_type SET name=:name WHERE id=:id";
            $con = $this->db;
            $query = $con->prepare($sql);
            $query->bindParam('name', $data['name']);
            $query->bindParam('id', $id);
            $query->execute();
            $result['status'] = 'success';
            $result['reload'] = 'true';
        } catch (Exception $ex) {
            $result['status'] = 'error';
            $result['message'] = 'Неизвестная ошибка';
        }
        return $result;
    }


    /*
     * Получение списка отделов филиала
     * Return: array[]
     * TODO: Удалить. Перенес в FilialDepartmentModel
     */
    public function getFilialDepartmentModel($id = null, $type='section')
    {
        $result = [];
        try {
            $sql = "SELECT * FROM filial_departament";
            if ($id != null)
                $sql .= " WHERE id={$id}";
            else $sql .= " WHERE filial_id={$_SESSION['filial_id']} ";
            if ($type=='section') $sql .= " AND filial_departament.group='true'";
            if ($type=='departament') $sql .= " AND filial_departament.group='false'";
            $sql .= " ORDER BY id ASC";

            $con = $this->db;
            $query = $con->prepare($sql);

            $query->execute();
            if ($query->rowCount() >= 1) {
                $result = $query->fetchAll(PDO::FETCH_OBJ);
            } else {
                $result['status'] = "error";
                $result['message'] = "Нет отделов в данном филиале.";
            }
            $con = null;
        } catch (Exception $ex) {
            $result['status'] = "error";
            $result['message'] = "Неизвестная ошибка.";
        }
        return $result;
    }



    /*
     * Получение списка типов пользователей
     * Return: array[]
     * TODO: Удалить т.к. дублируется в модели UserTypeModel
     */
    public function getUserTypeModel($id = null)
    {
        $result = [];
        try {
            $sql = "SELECT * FROM user_types";
            if ($id != null)
                $sql .= " WHERE id={$id}";
            //else $sql .= " WHERE filial_id={$_SESSION['filial_id']}";
            $sql .= " ORDER BY id ASC";

            $con = $this->db;
            $query = $con->prepare($sql);
            $query->execute();
            if ($query->rowCount() >= 1) {
                $result = $query->fetchAll(PDO::FETCH_OBJ);
            } else {
                $result['status'] = "error";
                $result['message'] = "Нет отделов в данном филиале.";
            }
            $con = null;
        } catch (Exception $ex) {
            $result['status'] = "error";
            $result['message'] = "Неизвестная ошибка.";
        }
        return $result;
    }

    /*
     * Получение типов пользователей
     * Return: array[]
     * TODO: Удалить т.к. дублируется в модели UserTypeModel
     */

    public function getUserTypesModel($id = null)
    {
        $result = [];
        try {
            $sql = "SELECT * FROM user_types WHERE (filial_id=:filial_id OR filial_id='0')";
            if ($id != null)
                $sql .= " AND id='{$id}'";
            $sql .= " ORDER BY id ASC";

            $con = $this->db;
            $query = $con->prepare($sql);
            $query->bindParam('filial_id', $_SESSION['filial_id']);
            $query->execute();
            if ($query->rowCount() >= 1) {
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
     * Получение основного класса пользователя
     * Return: integer
     * TODO: Удалить т.к. дублируется в модели UserTypeModel
     */
    public function getMainClassModel($id = 0)
    {
        $mainClass = -1;
        if ($id != 0) {
            //Search main_class
            $currentTypeID = $id;
            while ($mainClass == -1) {
                $sql = "SELECT id, parent_id, main_class FROM user_types WHERE id=:id";
                $con = $this->db;
                $query = $con->prepare($sql);
                $query->bindParam('id', $currentTypeID);
                $query->execute();
                $class = $query->fetchAll(PDO::FETCH_OBJ);
                if ($class[0]->main_class == 0 && $class[0]->parent_id == 0) {
                    $mainClass = $class[0]->id;
                } else {
                    $currentTypeID = $class[0]->parent_id;
                }
                $con = null;
            }
        } else {
            $mainClass = 3;
        }
        return $mainClass;
    }

    /*
     * Сохранение типа пользователя в БД
     * Return: array[]
     * TODO: Удалить т.к. дублируется в модели UserTypeModel
     */

    public function addUserTypeModel($data = null)
    {
        $result = [];
        try {
            $mainClass = $this->getMainClassModel($data['parent_id']);
            $con = $this->db;
            $sql = "INSERT INTO user_types(name, filial_id, parent_id, main_class) VALUES(:name, :filial_id, :parent_id, :main_class)";
            $query = $con->prepare($sql);
            $query->bindParam('name', $data['name']);
            $query->bindParam('filial_id', $_SESSION['filial_id']);
            $query->bindParam('parent_id', $data['parent_id']);
            $query->bindParam('main_class', $mainClass);

            $query->execute();
            //$sql = "SELECT * FROM filial_departament WHERE name=:name AND filial_id={$_SESSION['filial_id']}";
            $sql = "SELECT * FROM user_types WHERE name=:name AND filial_id=:filial_id";
            $query = $con->prepare($sql);
            $query->bindParam('name', $data['name']);
            $query->bindParam('filial_id', $_SESSION['filial_id']);
            $query->execute();
            if ($query->rowCount() >= 1) {
                $result['status'] = 'success';
                $result['reload'] = 'true';
            } else {
                $result['status'] = 'error';
                $result['message'] = 'Отдел не сохранен';
            }
        } catch (Exception $ex) {
            $result['status'] = 'error';
            $result['message'] = 'Неизвестная ошибка (' . $ex->getMessage() . ')';
        }
        return $result;
    }


    /*
     * Обновление типа пользователя
     * Return: array[]
     * TODO: Удалить т.к. дублируется в модели UserTypeModel
     */
    public function updateUserTypeModel($data = null, $id = null)
    {
        $result = [];
        try {
            $mainClass = $this->getMainClassModel();
            $sql = "UPDATE user_types SET name=:name, parent_id=:parent_id, main_class=:main_class WHERE id=:id";
            $con = $this->db;
            $query = $con->prepare($sql);
            $query->bindParam('name', $data['name']);
            $query->bindParam('parent_id', $data['parent_id']);
            $query->bindParam('main_class', $mainClass);
            $query->bindParam('id', $id);
            $query->execute();
            $result['status'] = 'success';
            $result['reload'] = 'true';
        } catch (Exception $ex) {
            $result['status'] = 'error';
            $result['reload'] = 'Неизвестная ошибка (' . $ex->getMessage() . ')';
        }
        return $result;
    }


    /*
     * Сохранение комнаты филиала в БД
     * Return: array[]
     * TODO: Удалить т.к. дублируется в модели FilialRoomModel
     */

    public function addRoomModel($data = null)
    {
        $result = [];
        $errors = "";
        if(!isset($data['name'])) $errors .= "Вы не указали название кабинета!\r\n";
        if(!isset($data['floor_id'])) $errors .= "Вы не указали этаж кабинета!\r\n";
        if(!isset($data['departament_id'])) $errors .= "Вы не указали отдел кабинета!\r\n";
        if($errors!="") {return ['status' => 'error', 'message' => $errors];}

        try {
            $con = $this->db;
            $sql = "INSERT INTO filial_rooms(room, name, parent_id, number, department_id, filial_id, date_create) VALUES(true, :name,:floor_id, -1, :departament_id, :filial_id, (SELECT CURRENT_DATE))";
            $query = $con->prepare($sql);
            $query->bindParam('name', $data['name']);
            $query->bindParam('floor_id', $data['floor_id']);
            $query->bindParam('departament_id', $data['departament_id']);
            $query->bindParam('filial_id', $_SESSION['filial_id']);
            $query->execute();
            $sql = "SELECT * FROM filial_rooms WHERE name=:name AND filial_id=:filial_id";
            $query = $con->prepare($sql);
            $query->bindParam('name', $data['name']);
            $query->bindParam('filial_id', $_SESSION['filial_id']);
            $query->execute();
            if ($query->rowCount() >= 1) {
                $result['status'] = 'success';
                $result['reload'] = 'true';
            } else {
                $result['status'] = 'error';
                $result['message'] = 'Помещение не сохранено!';
            }
        } catch (Exception $ex) {
            $result['status'] = 'error';
            $result['message'] = 'Неизвестная ошибка (' . $ex->getMessage() . ')';
        }
        return $result;
    }

    /*
     * Обновление кабинетов филиала
     * Return: array[]
     * TODO: Удалить т.к. дублируется в модели FilialRoomModel
     */
    public function updateRoomModel($data = null, $id = null)
    {
        $result = [];
        try {
            $sql = "UPDATE filial_rooms SET name=:name,floor=:floor,number=:number,work_time=:work_time,worker_id=:worker_id WHERE id=:id";
            $con = $this->db;
            $query = $con->prepare($sql);
            $query->bindParam('name', $data['name']);
            $query->bindParam('floor', $data['floor']);
            $query->bindParam('number', $data['number']);
            $query->bindParam('work_time', $data['work_time']);
            $query->bindParam('worker_id', $data['worker_id']);
            $query->bindParam('id', $id);
            $query->execute();
            $result['status'] = 'success';
            $result['reload'] = 'true';
        } catch (Exception $ex) {
            $result['status'] = 'error';
            $result['message'] = 'Неизвестная ошибка (' . $ex->getMessage() . ')';
        }
        return $result;
    }

    /*
     * Получение кабинетов филиала
     * Return: array[]
     * TODO: Удалить т.к. дублируется в модели FilialRoomModel
     */

    public function getRoomModel($id = null, $type='room')
    {
        $result = [];
        try {
            $sql = "SELECT * FROM filial_rooms WHERE (filial_id=:filial_id)";
            if ($id != null)
                $sql .= " AND id='{$id}' ";
            if ($type=='room')
                $sql .= " AND room='true'";
            if ($type=='category')
                $sql .= " AND room='false'";
            $sql .= " ORDER BY id ASC ";

            $con = $this->db;
            $query = $con->prepare($sql);
            $query->bindParam('filial_id', $_SESSION['filial_id']);
            $query->execute();
            if ($query->rowCount() >= 1) {
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
     * Получение работников
     * Return: array[]
     * TODO: Удалить, перенес в WorkersModel
     */

    public function getWorkersModel($id = null, $user_id = null)
    {
        $result = [];
        try {
            $sql = "SELECT workers.*, users.first_name, users.patronymic, users.surname
				FROM workers
				LEFT JOIN users ON users.id = workers.user_id
				WHERE workers.filial_id = :filial_id";
            if ($id != null)
                $sql .= " AND workers.id='{$id}'";
            else if($user_id!=null)
                $sql .= " AND workers.user_id='{$user_id}'";
            $sql .= " ORDER BY workers.id ASC";
            $con = $this->db;
            $query = $con->prepare($sql);
            $query->bindParam('filial_id', $_SESSION['filial_id']);
            $query->execute();
            if ($query->rowCount() >= 1) {
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
     * Сохранение стандартного права доступа к интерфейсам в БД
     * Return: array[]
     * TODO: Удалить, перенес в PermissionDefaultInterfaceModel
     */

    public function addPermissionDefaultInterfaceModel($data = null, $id = null)
    {
        $result = [];
        if ($data == null || $id == null) {
            $result['status'] = 'error';
            $result['message'] = 'Ошибка передачи данных';
        } else {
            try {
                $con = $this->db;
                //проверяем, не было ли уже добавлено данное право доступа к указанному интерфейсу
                $sql = "SELECT * FROM permissions_def_interfaces WHERE permission_id=:permission_id AND interface_id=:interface_id";
                $query = $con->prepare($sql);
                $query->bindParam('permission_id', $data['permission_id']);
                $query->bindParam('interface_id', $id);
                $query->execute();
                if($query->rowCount()>=1) {
                    $result['status'] = 'error';
                    $result['message'] = 'Данное право доступа уже существует';
                }
                //если это право не задано, то создаем его
                else {
                    $sql = "INSERT INTO permissions_def_interfaces(permission_id, interface_id,status) VALUES(:permission_id, :interface_id,:status)";
                    $query = $con->prepare($sql);
                    $query->bindParam('permission_id', $id);
                    $query->bindParam('interface_id', $data['interface_id']);
                    $query->bindParam('status', $data['status']);
                    $query->execute();
                    $sql = "SELECT * FROM permissions_def_interfaces WHERE permission_id=:permission_id AND interface_id=:interface_id AND status=:status";
                    $query = $con->prepare($sql);
                    $query->bindParam('permission_id', $id);
                    $query->bindParam('interface_id', $data['interface_id']);
                    $query->bindParam('status', $data['status']);
                    $query->execute();
                    if ($query->rowCount() >= 1) {
                        $result['status'] = 'success';
                        $result['reload'] = 'true';
                    } else {
                        $result['status'] = 'error';
                        $result['message'] = 'Доступ не сохранен';
                    }
                }
            } catch (Exception $ex) {
                $result['status'] = 'error';
                $result['message'] = 'Неизвестная ошибка (' . $ex->getMessage() . ')';
            }
        }
        return $result;
    }

    /*
     * Обновление стандартного права доступа
     * Return: array[]
     * TODO: Удалить, перенес в PermissionDefaultInterfaceModel
     */
    public function updatePermissionDefaultInterfaceModel($data = null, $id = null)
    {
        $result = [];
        try {
            $sql = "UPDATE permissions_def_interfaces SET status=:status WHERE id=:id";
            $con = $this->db;
            $query = $con->prepare($sql);
            $query->bindParam('status', $data['status']);
            $query->bindParam('id', $id);
            $query->execute();
            $result['status'] = 'success';
            $result['reload'] = 'true';
        } catch (Exception $ex) {
            $result['status'] = 'error';
            $result['reload'] = 'Неизвестная ошибка (' . $ex->getMessage() . ')';
        }
        return $result;
    }

    /*
     * Получение стандартных прав доступа к интерфейсам
     * Return: array[]
     * TODO: Удалить, перенес в PermissionDefaultInterfaceModel
     */

    public function getPermissionDefaultInterfaceModel($id = null)
    {
        $result = [];
        try {
            $sql = "SELECT permissions_def_interfaces.*, permissions.name AS permission_name, interfaces.name AS interface_name
FROM permissions_def_interfaces
LEFT JOIN permissions ON permissions.id=permissions_def_interfaces.permission_id
LEFT JOIN interfaces ON interfaces.id=permissions_def_interfaces.interface_id
";
            if ($id != null)
                $sql .= " WHERE permission_id='{$id}'";
            $sql .= "ORDER BY permissions_def_interfaces.id ASC";
            $con = $this->db;
            $query = $con->prepare($sql);
            $query->execute();
            if ($query->rowCount() >= 1) {
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
     * Получение доступов отдела к помещениям
     * Return: array[]
     * TODO: Удалить, перенес в FilialDepartmentRoomsPermissionsModel
     */

    public function getFilialDepartmentRoomsPermissionsModel($id = null, $departament_id = null)
    {
        $result = [];
        try {
            $sql = "SELECT filial_departament_rooms.*, filial_departament.name AS departament_name, filial_rooms.name AS room_name, filial_rooms.number AS room_number
FROM filial_departament_rooms
LEFT JOIN filial_departament ON filial_departament.id=filial_departament_rooms.departament_id
LEFT JOIN filial_rooms ON filial_rooms.id=filial_departament_rooms.room_id
";
            if ($id!=null) $sql.=" WHERE filial_departament_rooms.departament_id= {$id}";
            /*$filter = '';
            if ($id != null)
                $filter .= " filial_departament_rooms.id='{$id}' ";

            if($departament_id!=null) {
                if($filter!='') $filter .= ' AND ';
                $filter .= " filial_departament_rooms.departament_id='{$departament_id}' ";
            }

            if($filter!='') {
                $sql .= ' WHERE ' . $filter;
            }*/

            $sql .= " ORDER BY filial_departament_rooms.id ASC";
            $con = $this->db;
            $query = $con->prepare($sql);
            $query->execute();
            if ($query->rowCount() >= 1) {
                $result = $query->fetchAll(PDO::FETCH_OBJ);
            } else {
                $result['status'] = "error";
                $result['message'] = "Нет данных.";
            }
        } catch (Exception $ex) {
            $result['status'] = "error";
            $result['message'] = "Неизвестная ошибка (" . $ex->getMessage() . ")";
        }
        return $result;
    }

    /*
     * Создание прав доступа отдела к помещению
     * Return: array[]
     * TODO: Удалить, перенес в FilialDepartmentRoomsPermissionsModel
     */

    public function addRoomPermissionToFilialDepartmentModel($data = null, $id = null)
    {
        $result = [];
        if ($id == null || $data == null) {
            $result['status'] = 'error';
            $result['message'] = 'Ошибка передачи данных.';
        } else {
            try {
                $sql = "INSERT INTO filial_departament_rooms(departament_id, room_id, status) VALUES(:departament_id, :room_id, :status)";
                $con = $this->db;
                $query = $con->prepare($sql);
                $query->bindParam('departament_id', $id);
                $query->bindParam('room_id', $data['room_id']);
                $query->bindParam('status', $data['status']);
                $query->execute();

                $sql = "SELECT * FROM filial_departament_rooms WHERE departament_id=:departament_id AND room_id=:room_id AND status=:status";
                $query = $con->prepare($sql);
                $query->bindParam('departament_id', $id);
                $query->bindParam('room_id', $data['room_id']);
                $query->bindParam('status', $data['status']);
                $query->execute();
                if ($query->rowCount() >= 1) {
                    $result['status'] = 'success';
                    $result['reload'] = 'true';
                } else {
                    $result['status'] = 'error';
                    $result['div'] = 'alert';
                    $result['message'] = 'Запись не сохранена';
                }
            } catch (Exception $ex) {
                $result['status'] = 'error';
                $result['message'] = 'Неизвестная ошибка (' . $ex->getMessage() . ')';
            }
        }
        return $result;
    }


    /*
     * Обновление права доступа отдела к помещению
     * Return: array[]
     * TODO: Удалить, перенес в FilialDepartmentRoomsPermissionsModel
     */
    public function updateRoomPermissionToFilialDepartmentModel($data = null, $id = null)
    {
        $result = [];
        try {
            $sql = "UPDATE filial_departament_rooms SET status=:status WHERE id=:id";
            $con = $this->db;
            $query = $con->prepare($sql);
            $query->bindParam('status', $data['status']);
            $query->bindParam('id', $id);
            $query->execute();
            $result['status'] = 'success';
            $result['reload'] = 'true';
        } catch (Exception $ex) {
            $result['status'] = 'error';
            $result['reload'] = 'Неизвестная ошибка (' . $ex->getMessage() . ')';
        }
        return $result;
    }

    /*
     * Получение прав доступа
     * Return: array[]
     * TODO: Удалить т.к. перенес в PermissionsModel
     */

    public function getPermissionsModel($id = null)
    {
        $result = [];
        try {
            $sql = "SELECT permissions.* FROM permissions";
            if ($id != null)
                $sql .= " WHERE permissions.id='{$id}'";
            $sql .= " ORDER BY permissions.id ASC";
            $con = $this->db;
            $query = $con->prepare($sql);
            $query->execute();
            if ($query->rowCount() >= 1) {
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
     * Получение списка типов оборудования
     * Return: array[]
     */
    public function getEquipmentTypeModel($id = null)
    {
        $result = "";
        try {
            $sql = "SELECT * FROM equipment_types";
            if ($id != null)
                $sql .= " WHERE id={$id}";
            $sql .= " ORDER BY id ASC";
            $con = $this->db;
            $query = $con->prepare($sql);
            $query->execute();
            if ($query->rowCount() >= 1) {
                $result = $query->fetchAll(PDO::FETCH_OBJ);
            } else {
                $result = [];
                $result['status'] = "error";
                $result['message'] = "Нет типов документов.";
            }
            $con = null;
        } catch (Exception $ex) {
            $result['status'] = "error";
            $result['message'] = "Неизвестная ошибка.";
        }
        return $result;
    }

    /*
     * Сохранение типа документа в БД
     * Return: array[]
     */

    public function addEquipmentTypeModel($data = null)
    {
        $result = [];
        try {
            $sql = "INSERT INTO equipment_types(name) VALUES(:name)";
            $con = $this->db;
            $query = $con->prepare($sql);
            $query->bindParam('name', $data['name']);
            $query->execute();
            $sql = "SELECT * FROM equipment_types WHERE name=:name";
            $query = $con->prepare($sql);
            $query->bindParam('name', $data['name']);
            $query->execute();
            if ($query->rowCount() >= 1) {
                $result['status'] = 'success';
                $result['reload'] = 'true';
            } else {
                $result['status'] = 'error';
                $result['reload'] = 'Тип документа не сохранен';
            }
        } catch (Exception $ex) {
            $result['status'] = 'error';
            $result['reload'] = 'Неизвестная ошибка (' . $ex->getMessage() . ')';
        }
        return $result;
    }

    /*
     * Обновление типа оборудования
     * Return: array[]
     */
    public function updateEquipmentTypeModel($data = null, $id = null)
    {
        $result = [];
        try {
            $sql = "UPDATE equipment_types SET name=:name WHERE id=:id";
            $con = $this->db;
            $query = $con->prepare($sql);
            $query->bindParam('name', $data['name']);
            $query->bindParam('id', $id);
            $query->execute();
            $result['status'] = 'success';
            $result['reload'] = 'true';
        } catch (Exception $ex) {
            $result['status'] = 'error';
            $result['message'] = 'Неизвестная ошибка (' . $ex->getMessage() . ')';
        }
        return $result;
    }

    /*
     * Сохранение пользователя в БД
     * Return: array[]
     */

    public function addUserModel($data = null)
    {
        $result = [];
        try {
            $con = $this->db;
            $sql = "INSERT INTO users(email, phone,	first_name,	patronymic, surname,	birthday, reg_date,	filial_id, user_type_id, ff_person_id)
			VALUES(:email, :phone, :first_name, :last_name, :surname, :birthday, (SELECT CURRENT_DATE),	:filial_id, '2', ((SELECT MIN(ff_person_id) FROM users)-1))";
            $query = $con->prepare($sql);
            $query->bindParam('email', $data['email']);
            $query->bindParam('phone', $data['phone']);
            $query->bindParam('first_name', $data['first_name']);
            $query->bindParam('last_name', $data['last_name']);
            $query->bindParam('surname', $data['surname']);
            $birthday=pg_escape_string($data['birthday']);
            $query->bindParam('birthday', $birthday);

            $query->bindParam('filial_id', $_SESSION['filial_id']);
            $query->execute();
            $sql = "SELECT * FROM users WHERE email=:email AND phone=:phone AND	first_name=:first_name AND	patronymic=:last_name AND
			surname=:surname AND birthday=:birthday AND reg_date=(SELECT CURRENT_DATE) AND	filial_id=:filial_id AND user_type_id='2'";
            $query = $con->prepare($sql);
            $query->bindParam('email', $data['email']);
            $query->bindParam('phone', $data['phone']);
            $query->bindParam('first_name', $data['first_name']);
            $query->bindParam('last_name', $data['last_name']);
            $query->bindParam('surname', $data['surname']);
            $birthday=pg_escape_string($data['birthday']);
            $query->bindParam('birthday', $birthday);
            $query->bindParam('filial_id', $_SESSION['filial_id']);
            $query->execute();
            if ($query->rowCount() >= 1) {
                $result['status'] = 'success';
                $result['reload'] = 'true';
            } else {
                $result['status'] = 'error';
                $result['message'] = 'Пользователь не сохранен';
                //}
            }
        } catch (Exception $ex) {
            $result['status'] = 'error';
            $result['message'] = 'Неизвестная ошибка (' . $ex->getMessage() . ')';
        }
        return $result;
    }

    /*
     * Обновление пользователя
     * Return: array[]
     */
    public function updateUserModel($data = null, $id = null)
    {
        $result = [];
        try {
            $sql = "UPDATE users SET email=:email, phone=:phone, first_name=:first_name, patronymic=:last_name,
			surname=:surname, birthday=:birthday, user_type_id=:user_type_id WHERE id=:id";
            $con = $this->db;
            $query = $con->prepare($sql);
            $query->bindParam('email', $data['email']);
            $query->bindParam('phone', $data['phone']);
            $query->bindParam('first_name', $data['first_name']);
            $query->bindParam('last_name', $data['last_name']);
            $query->bindParam('surname', $data['surname']);
            $query->bindParam('birthday', $data['birthday']);
            $query->bindParam('user_type_id', $data['user_type_id']);
            $query->bindParam('id', $id);
            $query->execute();
            if (isset($data['department_id']))
            {
                $sql = "UPDATE workers SET department_id=:department_id WHERE user_id=:id";
                $con = $this->db;
                $query = $con->prepare($sql);
                $query->bindParam('department_id', $data['department_id']);
                $query->bindParam('id', $id);
                $query->execute();
            }
            $result['status'] = 'success';
            $result['reload'] = 'true';
        } catch (Exception $ex) {
            $result['status'] = 'error';
            $result['reload'] = 'Неизвестная ошибка (' . $ex->getMessage() . ')';
        }
        return $result;
    }

    /*
      * Получение списка пользователей
      * Return: array[]
      */

    public function getUsersModel($id = null)
    {
        $result = [];
        try {
            $sql = "SELECT users.*, user_types.name AS user_type, filial.name AS filial_name, user_types.main_class, workers.department_id AS dep_id
		FROM users
		LEFT JOIN filial ON filial.id=users.filial_id
		LEFT JOIN user_types ON user_types.id=users.user_type_id
		LEFT JOIN workers ON users.id=workers.user_id";
            if ($id != null)
                $sql .= " WHERE users.id='{$id}'";
            $sql .= " ORDER BY users.id ASC";
            $con = $this->db;
            $query = $con->prepare($sql);
            $query->execute();
            if ($query->rowCount() >= 1) {
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
     * Получение списка меток пользователя
     */
    public function getUserMars($id = null)
    {
        $result = [];
        if($id!=null) {
            $sql = "SELECT user_marks.*, marks.name as mark_name,
worker_add.first_name AS worker_add_first_name, worker_add.patronymic AS worker_add_last_name, worker_add.surname AS worker_add_surname,
worker_close.first_name AS worker_close_first_name, worker_close.patronymic AS worker_close_last_name, worker_close.surname AS worker_close_surname
FROM user_marks
LEFT JOIN marks ON marks.id = user_marks.mark_id
LEFT JOIN users AS worker_add ON worker_add.id = user_marks.worker_id
LEFT JOIN users AS worker_close ON worker_close.id = user_marks.worker_id_close
WHERE user_marks.user_id=:id
ORDER BY user_marks.id DESC
 ";
            $con = $this->db->prepare($sql);
            $con->bindParam('id', $id);
            $con->execute();
            $result = $con->fetchAll(PDO::FETCH_OBJ);
        } else {
            $result['status'] = 'error';
            $result['message'] = 'Ошибка передачи данных';
        }
        return $result;
    }

    /*
     * Получение списка проходов
     */
    public function getUserPass($id = null, $filter=null) {
        $result = [];
        if($id!=null) {
            $sql = "SELECT user_pass.*,
user_access.info AS access_info, user_access.hearing_id as hearingaccess,
hearing.name AS hearing_name, hearing.code AS hearing_code, hearing.date, hearing.hdate,
users.first_name, users.patronymic, users.surname,
filial_rooms.name AS room_name,
marks.name AS mark_name
            FROM user_pass
            LEFT JOIN user_access ON user_access.id = user_pass.access_id
            LEFT JOIN hearing ON hearing.id = user_access.hearing_id
            LEFT JOIN filial_rooms ON filial_rooms.id = hearing.room_id
			LEFT JOIN marks ON marks.id = user_pass.mark_id
LEFT JOIN users ON users.id=user_pass.user_id
            ";
            if($id!=null)
                $sql .= " WHERE user_pass.user_id='{$id}'";
            if ($filter!=null) $sql .= $filter;
            $sql .= " ORDER BY user_pass.id DESC";
            $con = $this->db->prepare($sql);
            $con->execute();
            $result = $con->fetchAll(PDO::FETCH_OBJ);
        } else {
            $result['status'] = 'error';
            $result['message'] = 'Ошибка передачи данных';
        }
        return $result;
    }




    /*
     * Получение списка департаментов работника
     */

    public function getWorkerDepartaments($id = null, $status = null) {
        $result = [];
        if($id!=null) {
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
                $result['message'] = 'Неизвестная ошибка ('.$ex->getMessage().')';
            }
        } else {
            $result['stauts'] = 'error';
            $result['message'] = 'Ошибка передачи данных';
        }
        return $result;
    }

    /*
     * Получение списка помещений сотрудника
     */
    public function getWorkerRooms($id = null, $status = null) {
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


    /*
     * Сохранение сотрудника в БД
     * Return: array[]
     */

    public function addWorkerModel($data = null, $id = null)
    {
        $result = [];
        try {
            $con = $this->db;
            $sql = "INSERT INTO workers(user_id, permission_id,	login, password, code,	filial_id)
			VALUES(:user_id, :permission_id, :login, MD5(:password), :code, :filial_id)";
            $query = $con->prepare($sql);
            $query->bindParam('user_id', $id);
            $query->bindParam('permission_id', $data['permission_id']);
            $query->bindParam('login', $data['login']);
            $query->bindParam('password', $data['password']);
            $query->bindParam('code', $data['code']);
            $query->bindParam('filial_id', $_SESSION['filial_id']);
            $query->execute();
            $sql = "UPDATE users SET user_type_id=:user_type_id WHERE id=:id";

            $query = $con->prepare($sql);
            $query->bindParam('user_type_id', $data['user_type']);
            $query->bindParam('id', $id);
            $query->execute();
            $sql = "SELECT * FROM workers WHERE user_id=:user_id AND permission_id=:permission_id AND	login=:login AND
			password=MD5(:password) AND code=:code AND filial_id=:filial_id";
            $query = $con->prepare($sql);
            $query->bindParam('user_id', $id);
            $query->bindParam('permission_id', $data['permission_id']);
            $query->bindParam('login', $data['login']);
            $query->bindParam('password', $data['password']);
            $query->bindParam('code', $data['code']);
            $query->bindParam('filial_id', $_SESSION['filial_id']);
            $query->execute();
            if ($query->rowCount() >= 1) {
                $worker = $query->fetchAll(PDO::FETCH_OBJ);
                $departament = $this->getFilialDepartmentModel();
                $interfaces = $this->getPermissionDefaultInterfaceModel($data['permission_id']);
                $sql = '';
                $values = '';
                for ($i = 0; $i < count($departament); $i++) {
                    if (isset($data['departament_'.$departament[$i]->id])) {
                        if ($values != '') $values .= ",";
                        if ($values == '') $values .= 'INSERT INTO workers_departamet_access(worker_id, departament_id) VALUES ';
                        $values .= "('{$worker[0]->id}', '{$departament[$i]->id}')";
                    }
                }
                $sql .= $values;
                if ($sql != '')
                {
                    $query = $con->prepare($sql);
                    $query->execute();
                }

                if ($interfaces!=null&&isset($interfaces[0]->id))
                {
                    $sql = 'INSERT INTO permissions_to_interfaces(worker_id, interface_id, status) VALUES ';
                    $values = '';
                    for ($i = 0; $i < count($interfaces); $i++) {
                        if ($values != '') $values .= ",";
                        $status = ($interfaces[$i]->status) ? 'true' : 'false';
                        $values .= "('{$worker[0]->id}', '{$interfaces[$i]->interface_id}', '{$status}')";
                    }
                    $sql .= $values;
                    $query = $con->prepare($sql);
                    $query->execute();
                }


                $rooms_to_access = $this->sendQuery("SELECT DISTINCT(room_id) FROM filial_departament_rooms
WHERE
departament_id IN (SELECT departament_id FROM workers_departamet_access WHERE worker_id='{$worker[0]->id}')
AND status='true'");
                if ($rooms_to_access!=null&&isset($rooms_to_access[0]->id))
                {
                    $sql = 'INSERT INTO workers_permissions_access(worker_id, room_id, acces_from_time, acces_to_time) VALUES ';
                    $values = '';
                    for ($i = 0; $i < count($rooms_to_access); $i++) {
                        if ($values != '') $values .= ',';
                        $values .= "('{$worker[0]->id}','{$rooms_to_access[$i]->room_id}','00:00','23:59')";
                    }
                    $sql .= $values;
                    $query = $con->prepare($sql);
                    $query->execute();
                }


                $result['status'] = 'success';
                $result['reload'] = 'true';
            } else {
                $result['status'] = 'error';
                $result['message'] = 'Содрудник не сохранен';
            }
        }
        catch (Exception $ex) {
            $result['status'] = 'error';
            $result['message'] = 'Неизвестная ошибка (' . $ex->getMessage() . ')';
        }
        return $result;
    }

    /*
     * Обновление сотрудника
     * Return: array[]
     */

    public function updateWorkerModel($data = null, $id = null)
    {
        $result = [];
        try {
            $sql = "UPDATE workers SET permission_id=:permission_id, login=:login,
			password=:password, code=:code WHERE id=:id";
            $con = $this->db;
            $query = $con->prepare($sql);
            $query->bindParam('permission_id', $data['permission_id']);
            $query->bindParam('login', $data['login']);
            $query->bindParam('password', $data['password']);
            $query->bindParam('code', $data['code']);
            $query->bindParam('id', $id);
            $query->execute();
            $result['status'] = 'success';
            $result['reload'] = 'true';
        } catch (Exception $ex) {
            $result['status'] = 'error';
            $result['reload'] = 'Неизвестная ошибка (' . $ex->getMessage() . ')';
        }
        return $result;
    }

    /*
     * Получение списка сотрудников
     * Return: array[]
     */
    /*
    public function getWorkersModel($id = null) {
        $result = [];
        try {
            $sql = "SELECT workers.*, users.surname, users.first_name, users.last_name
            FROM workers
            LEFT JOIN users ON workers.user_id=users.id";
            if($id != null)
                $sql .= " WHERE workers.id='{$id}'";
            $sql .= " ORDER BY workers.id ASC";
            $con = getConnectionModel();
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


*/

    /*
         * Сохранение слушания в БД
         * Return: array[]
         */

    public function addHearingModel($data = null)
    {
        $result = [];
        try {
            $con = $this->db;
            $sql = "INSERT INTO hearing(room_id,name, code,	hdate, worker_id, date,	filial_id, time)
			VALUES(:room_id,:name, :code,	:hdate, :worker_id, (SELECT CURRENT_DATE),	:filial_id, :time)";
            $query = $con->prepare($sql);
            $query->bindParam('room_id', $data['room_id']);
            $query->bindParam('name', $data['name']);
            $query->bindParam('hdate', pg_escape_string($data['hdate']));
            $query->bindParam('worker_id', $data['worker_id']);
            $query->bindParam('time', $data['time']);
            $query->bindParam('code', $data['code']);
            $query->bindParam('filial_id', $_SESSION['filial_id']);
            $query->execute();
            $sql = "SELECT * FROM hearing WHERE room_id=:room_id AND code=:code AND name=:name AND	hdate=:hdate AND
			worker_id=:worker_id AND date=(SELECT CURRENT_DATE) AND time=:time AND filial_id=:filial_id";
            $query = $con->prepare($sql);
            $query->bindParam('room_id', $data['room_id']);
            $query->bindParam('name', $data['name']);
            $query->bindParam('hdate', pg_escape_string($data['hdate']));
            $query->bindParam('worker_id', $data['worker_id']);
            $query->bindParam('time', pg_escape_string($data['time']));
            $query->bindParam('code', $data['code']);
            $query->bindParam('filial_id', $_SESSION['filial_id']);
            $query->execute();
            if ($query->rowCount() >= 1) {
                $hearing = $query->fetchAll(PDO::FETCH_OBJ);
                $rooms = $this->getRoomModel();
                $sql = 'INSERT INTO hearing_rooms (hearing_id, room_id, status) VALUES (:hearing_id,:room_id, :status)';
                $query = $con->prepare($sql);
                $status ='true';
                $query->bindParam('hearing_id', $hearing[0]->id);
                $query->bindParam('room_id', $hearing[0]->room_id);
                $query->bindParam('status', $status);
                $query->execute();
                $sql = 'INSERT INTO hearing_rooms (hearing_id, room_id, status) VALUES ';
                $values = '';
                for ($i = 0; $i < count($rooms); $i++) {
                    if ($rooms[$i]->id!=$hearing[0]->room_id) {
                        if ($values != '') $values .= ",";
                        $status = ($data['room_'.$rooms[$i]->id] == '1') ? 'true' : 'false';
                        $values .= "('{$hearing[0]->id}', '{$rooms[$i]->id}', '{$status}')";
                    }
                }
                $sql .= $values;
                $query = $con->prepare($sql);
                $query->execute();
                $result['status'] = 'success';
                $result['reload'] = 'true';
            } else {
                $result['status'] = 'error';
                $result['message'] = 'Слушание не сохранено';
            }
        }
        catch (Exception $ex) {
            $result['status'] = 'error';
            $result['message'] = 'Неизвестная ошибка (' . $ex->getMessage() . ')';
        }
        return $result;
    }



    /*
     * Получение списка слушаний
     * Return: array[]
     * TODO: Удалить, перенес в HearingsModel
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
     * Сохранение доступа на территорию в БД
     * Return: array[]
     * TODO: Перенес в UserAccessModel, удалить
     */

    public function addUserAccessModel($data = null)
    {
        $result = [];
        try {
            $con = $this->db;
            $sql = "SELECT * FROM user_access WHERE user_id=:user_id AND hearing_id=:hearing_id";
            $query = $con->prepare($sql);
            $query->bindParam('user_id', $data['user_id']);
            $query->bindParam('hearing_id', $data['hearing_id']);
            $query->execute();
            if ($query->rowCount() >= 1)
            {
                $result['status'] = 'error';
                $result['message'] = 'Данный пользователь уже имеет доступ к этому слушанию!';
            }
            else
            {
                $sql = "INSERT into user_access (user_id,hearing_id,status) values (:user_id,:hearing_id,'0')";
                $query = $con->prepare($sql);
                $query->bindParam('user_id', $data['user_id']);
                $query->bindParam('hearing_id', $data['hearing_id']);
                $query->execute();
                $sql = "SELECT * FROM user_access WHERE user_id=:user_id AND hearing_id=:hearing_id";
                $query = $con->prepare($sql);
                $query->bindParam('user_id', $data['user_id']);
                $query->bindParam('hearing_id', $data['hearing_id']);
                $query->execute();
                $access = $query->fetchAll(PDO::FETCH_OBJ);
                if ($query->rowCount() >= 1) {
                    $sql = 'SELECT * FROM hearing_rooms WHERE hearing_id=:hearing_id';
                    $query = $con->prepare($sql);
                    $query->bindParam('hearing_id', $data['hearing_id']);
                    $query->execute();
                    $hearing_rooms = $query->fetchAll(PDO::FETCH_OBJ);
                    if (isset($hearing_rooms[0]->id))
                    {
                        $sql = 'INSERT INTO user_acces_rooms (access_id, room_id, status) VALUES ';
                        $values = '';
                        for ($i = 0; $i < count($hearing_rooms); $i++) {
                            if ($values != '') $values .= ",";
                            $values .= "('{$access[0]->id}', '{$hearing_rooms[$i]->room_id}', '{$hearing_rooms[$i]->status}')";
                        }
                        $sql .= $values;
                        $query = $con->prepare($sql);
                        $query->execute();
                    }
                    $result['status'] = 'success';
                    $result['reload'] = 'true';
                } else {
                    $result['status'] = 'error';
                    $result['message'] = 'Доступ не сохранен';
                }
            }
        }
        catch (Exception $ex) {
            $result['status'] = 'error';
            $result['message'] = 'Неизвестная ошибка (' . $ex->getMessage() . ')';
        }
        return $result;
    }

    /*
     * Обновление доступа на территорию
     * Return: array[]
     * TODO: Перенес в UserAccessModel, удалить
     */

    public function updateUserAccessModel($data = null, $id = null)
    {
        $result = [];
        try {
            /*
            $sql = "UPDATE hearing SET room_id=:room_id, name=:name, hdate=:hdate,
            worker_id=:worker_id, time=:time, code=:code WHERE id=:id";
            $con = $this->db;
            $query = $con->prepare($sql);
            $query->bindParam('room_id', $data['room_id']);
            $query->bindParam('name', $data['name']);
            $query->bindParam('hdate', pg_escape_string($data['hdate']));
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
                        if (($data['room_'.$rooms[$i]->id] == '1')||($rooms[$i]->id==$data['room_id']))
                            {
                            $sql = "UPDATE hearing_rooms SET status='true' WHERE hearing_id='{$id}' AND room_id='{$rooms[$i]->id}'";
                            $query = $con->prepare($sql);
                            $query->execute();
                            }
                        }
                        if ($hearing_rooms[$j]->status==1)
                        {
                            if (($data['room_'.$rooms[$i]->id] != '1')&&($rooms[$i]->id!=$data['room_id']))
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
                    else $status = ($data['room_'.$rooms[$i]->id] == '1') ? 'true' : 'false';
                    $query->bindParam('hearing_id', $id);
                    $query->bindParam('room_id', $rooms[$i]->id);
                    $query->bindParam('status', $status);
                    $query->execute();
                }
            }
            */
            $result['status'] = 'success';
            $result['reload'] = 'true';
        } catch (Exception $ex) {
            $result['status'] = 'error';
            $result['reload'] = 'Неизвестная ошибка (' . $ex->getMessage() . ')';
        }
        return $result;
    }





    /*
     * Получение списка доступов
     * Return: array[]
     * TODO: Перенес в UserAccessModel, удалить
     *
     */

    public function getUserAccessModel($id = null) {
        $result = [];
        try {
            $sql = "SELECT user_access.*,
					hearing.name as hearingname, users.first_name, users.patronymic, users.surname
					FROM user_access
					LEFT JOIN users on user_access.user_id=users.id
					LEFT JOIN hearing on user_access.hearing_id=hearing.id";
            if($id != null)
                $sql .= " WHERE user_access.id='{$id}'";
            $sql .= " ORDER BY user_access.id ASC";
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
    * Получение списка прошедших через турникет
    * Таблица прохода у нас одна, выборка вошедших/вышедших происходит сортировкой датой и временем прохода
    * type - In/Out
     * TODO Перенес UsersModel
    */
    public function getUserPassInOut($filter = null, $type = "in", $user_id=null)
    {
        $result = [];
        try {
            $sql = "SELECT user_pass.*,
users.first_name, users.patronymic, users.surname, users.user_photo, users.birthday,
user_types.name AS user_type_name,
filial_rooms.name AS user_room_name,
marks.name AS mark_name,
filial_departament.name AS user_departament_name,
filial_rooms.id AS user_room_id
FROM user_pass
LEFT JOIN users ON user_pass.user_id = users.id
LEFT JOIN workers ON users.id = workers.user_id
LEFT JOIN filial_departament ON workers.department_id=filial_departament.id
LEFT JOIN user_types ON  users.user_type_id = user_types.id
LEFT JOIN user_access ON user_pass.access_id = user_access.id
LEFT JOIN hearing ON user_access.hearing_id = hearing.id
LEFT JOIN filial_rooms ON hearing.room_id = filial_rooms.id
LEFT JOIN marks ON marks.id=user_pass.mark_id
";

            if($type=='in')
            {
                $sql .= " WHERE date_in IS NOT NULL";
                if ($user_id!=null) $sql .= " AND user_pass.user_id='{$user_id}'";
                if ($filter!=null) $sql .= $filter;
                $sql .= " ORDER BY date_in DESC, time_in DESC ";
            }
            else if($type=='out')
            {
                $sql .= " WHERE user_pass.date_out IS NOT NULL ";
                if ($user_id!=null) $sql .= " AND user_pass.user_id='{$user_id}'";
                if ($filter!=null) $sql .= $filter;
                $sql .= " ORDER BY date_out DESC, time_out DESC ";
            }

            /*if($filter == null)
                $sql .= "LIMIT 100";
            else $sql .= "LIMIT ".$filter['limit'];*/
            $con = $this->db->prepare($sql);
            $con->execute();
            $result = $con->fetchAll(PDO::FETCH_OBJ);
        }
        catch (Exception $ex) {
            $result['status'] = 'error';
            $result['message'] = $ex->getMessage();
        }
        return $result;
    }










    /*
     * Создание доступа работника к отделу
     * Return: array[]
     */

    public function addWorkerDepartmentAccessModel($worker_id = null, $department_id = null, $status = null)
    {
        $result = [];
        try {
            $con = $this->db;
            $sql = "SELECT * FROM workers_departamet_access WHERE worker_id=:worker_id AND departament_id=:departament_id";
            $query = $con->prepare($sql);
            $query->bindParam('worker_id', $worker_id);
            $query->bindParam('departament_id', $department_id);
            $query->execute();
            if($query->rowCount()>=1) {
                $worker_access = $query->fetchAll(PDO::FETCH_OBJ);
                $sql = "UPDATE workers_departamet_access SET status=:status WHERE id=:id";
                $query = $con->prepare($sql);
                $query->bindParam('status', $status);
                $query->bindParam('id', $worker_access[0]->id);
                $query->execute();
            }
            else
            {
                $sql = "INSERT INTO workers_departamet_access (worker_id,departament_id, status)
				VALUES (:worker_id,:departament_id,:status)";
                $query = $con->prepare($sql);
                $query->bindParam('worker_id', $worker_id);
                $query->bindParam('departament_id', $department_id);
                $query->bindParam('status', $status);
                $query->execute();
            }
            $result['status'] = 'success';
        }
        catch (Exception $ex) {
            $result['status'] = 'error';
            $result['message'] = 'Неизвестная ошибка (' . $ex->getMessage() . ')';
        }
        return $result;
    }

    /*
     * Запрет доступа работника к отделу
     * Return: array[]


    public function updateWorkerDepartmentAccessModel($id = null, $status = null)
    {
        $result = [];
		try {
			$con = $this->db;
			$sql = "UPDATE workers_departamet_access SET status=:status WHERE id='{$id}'";
            $query = $con->prepare($sql);
			$query->bindParam('status', $status);
            $query->execute();
			$result['status'] = 'success';
            $result['reload'] = 'true';
        }
		catch (Exception $ex) {
            $result['status'] = 'error';
            $result['reload'] = 'Неизвестная ошибка (' . $ex->getMessage() . ')';
        }
        return $result;
    }
	*/

    /*
     * Создание доступа работника к помещению
     * Return: array[]
     */

    public function addWorkerPermissionAccessModel($worker_id = null, $room_id = null)
    {
        $result = [];
        try {
            $con = $this->db;
            $sql = "INSERT INTO workers_permissions_access (worker_id,room_id, access_from_time, access_to_time, status)
			VALUES (:worker_id,:room_id, '00:00', '23:59', :status)";
            $status='true';
            $query = $con->prepare($sql);
            $query->bindParam('worker_id', $worker_id);
            $query->bindParam('room_id', $room_id);
            $query->bindParam('status', $status);
            $query->execute();
            $sql = "SELECT * FROM workers_permissions_access WHERE worker_id=:worker_id AND room_id=:room_id";
            $query = $con->prepare($sql);
            $query->bindParam('worker_id', $worker_id);
            $query->bindParam('room_id', $room_id);
            $query->execute();
            if($query->rowCount()>=1) {
                $result['status'] = 'success';
                $result['reload'] = 'true';
            }
            else
            {
                $result['status'] = 'error';
                $result['message'] = 'Не удается создать доступ';
            }
        }
        catch (Exception $ex) {
            $result['status'] = 'error';
            $result['message'] = 'Неизвестная ошибка (' . $ex->getMessage() . ')';
        }
        return $result;
    }

    /*
     * Запрет доступа работника к помещению
     * Return: array[]
     */

    public function updateWorkerPermissionAccessModel($id = null, $status = null)
    {
        $result = [];
        try {
            $con = $this->db;
            $sql = "UPDATE workers_permissions_access SET status=:status WHERE id='{$id}'";
            $query = $con->prepare($sql);
            $query->bindParam('status', $status);
            $query->execute();
            $result['status'] = 'success';
            $result['reload'] = 'true';
        }
        catch (Exception $ex) {
            $result['status'] = 'error';
            $result['reload'] = 'Неизвестная ошибка (' . $ex->getMessage() . ')';
        }
        return $result;
    }






    /*
     * TODO: Перенес в TopologyModel.php - удалить
     */
    public function getTopologyModel($level = 0, $parent_id = 0, $departament_id = null)
    {
        /*
         * if object[i]->room == false
         * object[i] -> [
         * id, name, number, worker_id, filial_id, parent_id, room, departament_id, step_in, step_out,
         * department -> [
         *      main -> [id, parent_id, name, image],
         *      sub -> [id, parent_id, name, image]
         * ],
         * rooms[j] -> [
         *      id, name, number, worker_id, filial_id, parent_id, room, departament_id, room, departament_id, step_in, step_out
         * ],
         * departaments[j] -> [
         *      id, name, filial_id, public, parent_id, group, image, info,
         *      main_departament -> [
         *          id, name, filial_id, public, parent_id, group, image, info
         *      ],
         *      rooms[k] -> [
         *          id, name, number, worker_id, filial_id, parent_id, room, departament_id, step_in, step_out,
         *          workers[o] -> [
         *              hearing[p] -> [],
         *          ],
         *      ]
         * ]
         * ]
         * else
         * object[i] -> [
         * id, name, number, worker_id, filial_id, parent_id, room, departament_id, step_in, step_out,
         * departaments -> [
         *      id, name, filial_id, public, parent_id, group, image, info,
         *      main_departament -> [
         *          id, name, filial_id, public, parent_id, group, image, info
         *      ],
         *      workers -> [],
         *      hearing -> [],
         * ]
         */

        $topology = null;
        //Выбираем все этажи parent_id
        $sql = "SELECT filial_rooms.* FROM filial_rooms WHERE filial_rooms.parent_id=:parent_id AND room IS FALSE AND delete IS FALSE ORDER BY id ASC";
        $con = $this->db->prepare($sql);
        $con->bindParam('parent_id', $parent_id);
        $con->execute();

        if ($con->rowCount() >= 1) {
            //Этажи есть
            $topology = $con->fetchAll(PDO::FETCH_OBJ);
            for ($i = 0; $i < count($topology); $i++) {
                $topology[$i] = $this->getFloorTopology($topology[$i]);
                //SubStruct
                if($parent_id!=$topology[$i]->id)
                    $topology[$i]->sub = $this->getTopologyModel(($level + 1), $topology[$i]->id, $departament_id);
            }
        } else if($con->rowCount() < 1 && $level==0) {
            $sql = "SELECT filial_rooms.* FROM filial_rooms WHERE filial_rooms.id=:parent_id AND room IS FALSE AND delete IS FALSE ORDER BY id ASC";
            $con = $this->db->prepare($sql);
            $con->bindParam('parent_id', $parent_id);
            $con->execute();
            if($con->rowCount()>0) {
                $topology = $con->fetchAll(PDO::FETCH_OBJ);
                $topology[0] = $this->getFloorTopology($topology[0], $departament_id);
                if (!isset($topology[0]->departaments)) $topology = null;
            } else {
                $topology = null;
            }
        }
        return $topology;
    }

    public function getFloorTopology($topology, $departament_id=null)
    {
        //Обработка этажей

        //All departaments on floor

        $sql = "SELECT filial_departament.*
            FROM filial_departament

            ";
        $where = " WHERE filial_departament.id IN (SELECT departament_id FROM filial_departament_floor WHERE floor_id = :floor_id) AND parent_id=0 AND delete IS FALSE ";
        if(isset($departament_id)) {
            $where .= " AND filial_departament.id='{$departament_id}'";
        }
        $sql .= "{$where} ORDER BY id ASC";
        $con = $this->db->prepare($sql);
        $con->bindParam('floor_id', $topology->id);
        $con->execute();
        if ($con->rowCount() > 0) {
            $topology->departaments = $con->fetchAll(PDO::FETCH_OBJ);
            for ($j = 0; $j < count($topology->departaments); $j++) {

                if(!$topology->departaments[$j]->group) {
                    //Выбор sub-departments
                    $sql = "SELECT * FROM filial_departament WHERE parent_id=:parent_id AND id IN (SELECT departament_id FROM filial_departament_floor WHERE floor_id=:floor_id) AND delete IS FALSE ORDER BY id ASC";
                    $con = $this->db->prepare($sql);
                    $con->bindParam('parent_id', $topology->departaments[$j]->id);
                    $con->bindParam('floor_id', $topology->id);
                    $con->execute();
                    if ($con->rowCount() > 0) {
                        $topology->departaments[$j]->sub = $con->fetchAll(PDO::FETCH_OBJ);
                    } else {
                        $topology->departaments[$j]->sub = null;
                    }
                }

                if($topology->departaments[$j]->group) {
                    $topology->departaments[$j]->rooms = $this->getRoomDepartament($topology->id, $topology->departaments[$j]->id);
                } else if(isset($topology->departaments[$j]->sub)) {
                    for($k=0;$k<count($topology->departaments[$j]->sub); $k++) {
                        $topology->departaments[$j]->sub[$k]->rooms = $this->getRoomDepartament($topology->id, $topology->departaments[$j]->sub[$k]->id);
                    }
                }
            }
        } else {
            $topology->departaments = null;
        }
        return $topology;
    }

    public function getRoomDepartament($floor_id, $departament_id)
    {
        //Выбор кабинетов отдела
        $sql = "SELECT * FROM filial_rooms WHERE department_id = :department_id AND parent_id=:parent_id AND room='true' AND delete='false' ORDER BY id ASC";
        $con = $this->db->prepare($sql);
        $con->bindParam('department_id', $departament_id);
        $con->bindParam('parent_id', $floor_id);
        $con->execute();

        if ($con->rowCount() > 0) {
            $rooms = $con->fetchAll(PDO::FETCH_OBJ);

            //Получение информации о кабинете
            for ($k = 0; $k < count($rooms); $k++) {
                //Выбор сотрудников
                $rooms[$k]->workers = $this->getRoomWorkers($rooms[$k]->id);
            }
        } else {
            $rooms = null;
        }
        return $rooms;
    }


    public function getRoomWorkers($id)
    {
        $sql = "SELECT workers.*, users.surname, users.first_name, users.patronymic
                            FROM workers
                            LEFT JOIN users ON users.id = workers.user_id
                            WHERE room_id = :room_id";
        $con = $this->db->prepare($sql);
        $con->bindParam('room_id', $id);
        $con->execute();
        if ($con->rowCount() > 0) {
            $workers = $con->fetchAll(PDO::FETCH_OBJ);
            for($i=0; $i<count($workers); $i++) {
                $sql = "SELECT * FROM filial_rooms_hearing WHERE room_id = :room_id AND worker_id = :worker_id";
                $con = $this->db->prepare($sql);
                $con->bindParam('room_id', $id);
                $con->bindParam('worker_id', $workers[$i]->id);
                $con->execute();
                $workers[$i]->hearing = $con->rowCount() > 0 ? $workers[$i]->hearing = $con->fetchAll(PDO::FETCH_OBJ) : null;
            }
            return $workers;
        } else {
            return null;
        }
    }

    public function getRoomHearing($id)
    {
        $sql = "SELECT * FROM filial_rooms_hearing WHERE room_id = :room_id";
        $con = $this->db->prepare($sql);
        $con->bindParam('room_id', $id);
        $con->execute();
        if ($con->rowCount() > 0) {
            return $con->fetchAll(PDO::FETCH_OBJ);
        } else {
            return null;
        }
    }

    /*
 * Создание группы/помещения
 * Return: array[]
 */

    public function addTopologyObjectModel($data=null, $type=null)
    {
        $result = [];
        try {
            $con = $this->db;
            if ($type=='room')
            {
                $sql = "INSERT INTO filial_rooms (name,	number,	worker_id,	filial_id, parent_id, room, department_id)
			VALUES(:name, :number, :worker_id, :filial_id, :parent_id, 'true', :department_id)";
                $query = $con->prepare($sql);
                $query->bindParam('name', $data['name']);
                $query->bindParam('number', $data['number']);
                $query->bindParam('worker_id', $data['worker_id']);
                $query->bindParam('parent_id', $data['parent_id']);
                $query->bindParam('department_id', $data['department_id']);
                $query->bindParam('filial_id', $_SESSION['filial_id']);
                $query->execute();
            }
            if ($type=='category')
            {
                $sql = "INSERT INTO filial_rooms (name,	worker_id,	filial_id, parent_id, room, department_id)
			VALUES(:name, :worker_id, :filial_id, :parent_id, 'false', :department_id)";
                $query = $con->prepare($sql);
                $query->bindParam('name', $data['name']);
                $query->bindParam('worker_id', $data['worker_id']);
                $query->bindParam('parent_id', $data['parent_id']);
                $query->bindParam('department_id', $data['department_id']);
                $query->bindParam('filial_id', $_SESSION['filial_id']);
                $query->execute();
            }
            $result['status'] = 'success';
            $result['reload'] = 'true';
        }
        catch (Exception $ex) {
            $result['status'] = 'error';
            $result['message'] = 'Неизвестная ошибка (' . $ex->getMessage() . ')';
        }
        return $result;
    }


    /*
     * Получение модели нулевого аккаунта
     * Return: array[]
     * TODO: Перенес в NullAccountModel - удалить
    */
    public function getNullAccountModel()
    {
        $result = [];
        try {
            $sql = "SELECT user_pass.*,
			users.first_name, users.patronymic, users.surname, users.user_photo,
			user_types.name AS user_type_name
			FROM user_pass
			LEFT JOIN users ON user_pass.user_id = users.id
			LEFT JOIN user_types ON  users.user_type_id = user_types.id
			WHERE (users.user_type_id=3 OR user_types.main_class=3) AND user_pass.date_in IS NOT NULL AND user_pass.date_out IS NULL";
            $con = $this->db;
            $query = $con->prepare($sql);
            $query->execute();
            if ($query->rowCount() >= 1) {
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
     * Вход нулевого аккаунта
     * Return: array[]
     * TODO: Перенес в NullAccountModel - удалить
    */

    public function addNullAccountModel($data = null)
    {
        $result = [];
        try {
            $con = $this->db;
            if ($data['user_type_id']==0) $user_type_id=3;
            else $user_type_id=$data['user_type_id'];
            $sql = "INSERT INTO users (first_name,patronymic,surname,user_type_id, ff_person_id,filial_id)
			VALUES (:first_name,:patronymic,:surname,:user_type_id,((SELECT MIN(ff_person_id) FROM users)-1),:filial_id)";
            $query = $con->prepare($sql);
            $query->bindParam('first_name', $data['first_name']);
            $query->bindParam('surname', $data['surname']);
            $query->bindParam('patronymic', $data['patronymic']);
            $query->bindParam('user_type_id', $user_type_id);
            $query->bindParam('filial_id', $_SESSION['filial_id']);
            $query->execute();
            $sql = "INSERT INTO user_access (user_id,hearing_id, status)
			VALUES ((SELECT id FROM users WHERE first_name=:first_name AND patronymic=:patronymic AND surname=:surname
			AND user_type_id=:user_type_id ORDER BY id DESC LIMIT 1),'777','1')";
            $query = $con->prepare($sql);
            $query->bindParam('first_name', $data['first_name']);
            $query->bindParam('surname', $data['surname']);
            $query->bindParam('patronymic', $data['patronymic']);
            $query->bindParam('user_type_id', $user_type_id);
            $query->execute();
            $sql = "INSERT into user_pass (user_id,access_id, date_in, time_in, date_out,time_out, info)
			VALUES ((SELECT id FROM users WHERE first_name=:first_name AND patronymic=:patronymic AND surname=:surname
			AND user_type_id=:user_type_id ORDER BY id DESC LIMIT 1),
			(SELECT id FROM user_access WHERE hearing_id='777'
			AND user_id= (SELECT id FROM users WHERE first_name=:first_name AND patronymic=:patronymic AND surname=:surname
			AND user_type_id=:user_type_id ORDER BY id DESC LIMIT 1)),
			(select CURRENT_DATE), (select localtime), null, null, null)";
            $query = $con->prepare($sql);
            $query->bindParam('first_name', $data['first_name']);
            $query->bindParam('surname', $data['surname']);
            $query->bindParam('patronymic', $data['patronymic']);
            $query->bindParam('user_type_id', $user_type_id);
            $query->execute();
            $result['status'] = 'success';
            $result['reload'] = 'true';
        }
        catch (Exception $ex) {
            $result['status'] = 'error';
            $result['message'] = 'Неизвестная ошибка (' . $ex->getMessage() . ')';
        }
        return $result;
    }

    /*
     * Выход нулевого аккаунта
     * Return: array[]
     */

    public function updateNullAccountModel($data = null, $id = null)
    {
        $result = [];
        try {
            $con = $this->db;
            $sql = "UPDATE users SET first_name=:first_name,patronymic=:patronymic,surname=:surname,user_type_id=:user_type_id WHERE id=:id";
            $query = $con->prepare($sql);
            $query->bindParam('id', $id);
            $query->bindParam('first_name', $data['first_name']);
            $query->bindParam('surname', $data['surname']);
            $query->bindParam('patronymic', $data['patronymic']);
            $query->bindParam('user_type_id', $data['user_type_id']);
            $query->execute();
            $sql = "UPDATE user_access SET status='2' WHERE user_id=:id AND hearing_id='777' AND status='1'";
            $query = $con->prepare($sql);
            $query->bindParam('id', $id);
            $query->execute();
            $sql = "SELECT id FROM user_access WHERE user_id=:id AND hearing_id='777' AND status='2' ORDER BY id DESC";
            $query = $con->prepare($sql);
            $query->bindParam('id', $id);
            $query->execute();
            if ($query->rowCount() >= 1) {
                $result = $query->fetchAll(PDO::FETCH_OBJ);
                $sql = "UPDATE user_pass SET date_out=(select CURRENT_DATE), time_out=(select localtime) WHERE user_id=:id AND access_id=:access_id";
                $query = $con->prepare($sql);
                $query->bindParam('id', $id);
                $query->bindParam('access_id', $result[0]->id);
                $query->execute();
            }
            $result['status'] = 'success';
            $result['reload'] = 'true';
        }
        catch (Exception $ex) {
            $result['status'] = 'error';
            $result['message'] = 'Неизвестная ошибка (' . $ex->getMessage() . ')';
        }
        return $result;
    }








    /*
     * Создание категории доступа работника
     * Return: array[]
     */

    public function addWorkerCategoryAccessModel($worker_id = null, $room_id = null,$status=null)
    {
        $result = [];
        try {
            $con = $this->db;
            $sql = "SELECT * FROM workers_permissions_access WHERE worker_id=:worker_id AND room_id=:room_id";
            $query = $con->prepare($sql);
            $query->bindParam('worker_id', $worker_id);
            $query->bindParam('room_id', $room_id);
            $query->execute();
            if($query->rowCount()>=1) {
                $worker_access = $query->fetchAll(PDO::FETCH_OBJ);
                $sql = "UPDATE workers_permissions_access SET status=:status WHERE id=:id";
                $query = $con->prepare($sql);
                $query->bindParam('status', $status);
                $query->bindParam('id', $worker_access[0]->id);
                $query->execute();
            }
            else
            {
                $sql = "INSERT INTO workers_permissions_access (worker_id,room_id, acces_from_time, acces_to_time, status,security_mode)
				VALUES (:worker_id,:room_id, '00:00', '23:59', :status,'false')";
                $query = $con->prepare($sql);
                $query->bindParam('worker_id', $worker_id);
                $query->bindParam('room_id', $room_id);
                $query->bindParam('status', $status);
                $query->execute();
            }
            $result['status'] = 'success';
        }
        catch (Exception $ex) {
            $result['status'] = 'error';
            $result['message'] = 'Неизвестная ошибка (' . $ex->getMessage() . ')';
        }
        return $result;
    }

    /*
     * Установка/снятие объектов с охраны
     * Return: array[]
     */

    public function addWorkerSecurityAccessModel($worker_id = null, $room_id = null,$status=null)
    {
        $result = [];
        try {
            $con = $this->db;
            $sql = "SELECT * FROM workers_permissions_access WHERE worker_id=:worker_id AND room_id=:room_id";
            $query = $con->prepare($sql);
            $query->bindParam('worker_id', $worker_id);
            $query->bindParam('room_id', $room_id);
            $query->execute();
            if($query->rowCount()>=1) {
                $worker_access = $query->fetchAll(PDO::FETCH_OBJ);
                $sql = "UPDATE workers_permissions_access SET security_mode=:security_mode WHERE id=:id";
                $query = $con->prepare($sql);
                $query->bindParam('security_mode', $status);
                $query->bindParam('id', $worker_access[0]->id);
                $query->execute();
            }
            else
            {
                $sql = "INSERT INTO workers_permissions_access (worker_id,room_id, acces_from_time, acces_to_time, status,security_mode)
				VALUES (:worker_id,:room_id, '00:00', '23:59', 'false',:security_mode)";
                $query = $con->prepare($sql);
                $query->bindParam('worker_id', $worker_id);
                $query->bindParam('room_id', $room_id);
                $query->bindParam('security_mode', $status);
                $query->execute();
            }
            $result['status'] = 'success';
        }
        catch (Exception $ex) {
            $result['status'] = 'error';
            $result['message'] = 'Неизвестная ошибка (' . $ex->getMessage() . ')';
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

    /*
     * Присвоение комнаты для терминала
     */
    public function createTerminalStream($id, $room)
    {
        $sql = "UPDATE filial_terminal SET webrtc_room=:room WHERE equipment_id=:id";
        $con = $this->db->prepare($sql);
        $con->bindParam("id", $id);
        $con->bindParam("room", $room);
        $con->execute();
        $data = [];
        $data['status'] = 'success';
        $data['reload'] = 'true';
        return $data;
    }

    /*
     * Добавление сотрудника в кабинет
     * Return: array[]
     */

    public function addTopologyWorkerModel($room_id=null,$worker_id=null)
    {
        $result = [];
        try {
            $con = $this->db;
            $sql = "UPDATE workers SET room_id=:room_id WHERE id=:id";
            $query = $con->prepare($sql);
            $query->bindParam('id', $worker_id);
            $query->bindParam('room_id', $room_id);
            $query->execute();
            $result['status'] = 'success';
        }
        catch (Exception $ex) {
            $result['status'] = 'error';
            $result['message'] = 'Неизвестная ошибка (' . $ex->getMessage() . ')';
        }
        return $result;
    }



    /*
     * Сделать сотрудника публичным/закрытым
     * Return: array[]
     */

    public function makeWorkerPublicModel($worker_id=null,$status=null)
    {
        $result = [];
        try {
            $con = $this->db;
            $sql = "UPDATE workers SET public=:status WHERE id=:worker_id";
            $query = $con->prepare($sql);
            $query->bindParam('worker_id', $worker_id);
            $query->bindParam('status', $status);
            $query->execute();
            $result['status'] = 'success';
        }
        catch (Exception $ex) {
            $result['status'] = 'error';
            $result['message'] = 'Неизвестная ошибка (' . $ex->getMessage() . ')';
        }
        return $result;
    }







	public function addWorkerDepartmentCategoryAccessModel($data = null, $worker_id = null)
    {
        $result = [];
        try {
            $sql = "UPDATE workers SET department_id=:department_id WHERE id='{$worker_id}'";
            $con = $this->db;
            $query = $con->prepare($sql);
            $query->bindParam('department_id', $data['department_id']);
            $query->execute();
			$sql = "UPDATE users SET user_type_id='1' WHERE id=(SELECT user_id FROM workers WHERE id='{$worker_id}')";
            $con = $this->db;
            $query = $con->prepare($sql);
            $query->execute();
            $result['status'] = 'success';
            $result['reload'] = 'true';
        } catch (Exception $ex) {
            $result['status'] = 'error';
            $result['reload'] = 'Неизвестная ошибка (' . $ex->getMessage() . ')';
        }
        return $result;
    }

	public function changeWorkerDepartmentCategoryAccessModel($data = null, $worker_id = null)
    {
        $result = [];
        try {
			$con = $this->db;
			$worker=$this->getWorkersModel($worker_id);
			if ($worker[0]->room_id!=null)
			{
				$sql = "SELECT * FROM filial_departament_rooms
				WHERE departament_id = '{$data['department_id']}' AND room_id = '{$worker[0]->room_id}' AND status IS TRUE";
				$query = $con->prepare($sql);
				$query->execute();
				if ($query->rowCount()==0) {
					$sql = "INSERT INTO filial_departament_rooms (departament_id, room_id, status)
					VALUES ('{$data['department_id']}','{$worker[0]->room_id}','true')";
					$query = $con->prepare($sql);
					$query->execute();
				}
				$sql = "SELECT * FROM filial_departament_floor WHERE departament_id =
				(SELECT parent_id FROM filial_departament WHERE id='{$data['department_id']}')
				AND floor_id = (SELECT parent_id FROM filial_rooms WHERE id='{$worker[0]->room_id}')";
				$query = $con->prepare($sql);
				$query->execute();
				if ($query->rowCount()==0) {
					$sql = "INSERT INTO filial_departament_floor (departament_id, floor_id)
					VALUES ((SELECT parent_id FROM filial_departament WHERE id='{$data['department_id']}'),
					(SELECT parent_id FROM filial_rooms WHERE id='{$worker[0]->room_id}'))";
					$query = $con->prepare($sql);
					$query->execute();
				}
				$sql = "SELECT * FROM filial_departament_floor WHERE departament_id =
				'{$data['department_id']}' AND floor_id = (SELECT parent_id FROM filial_rooms WHERE id='{$worker[0]->room_id}')";
				$query = $con->prepare($sql);
				$query->execute();
				if ($query->rowCount()==0) {
					$sql = "INSERT INTO filial_departament_floor (departament_id, floor_id)
					VALUES ('{$data['department_id']}',
					(SELECT parent_id FROM filial_rooms WHERE id='{$worker[0]->room_id}'))";
					$query = $con->prepare($sql);
					$query->execute();
				}
				$sql = "SELECT * FROM filial_rooms_hearing WHERE departament_id = '{$worker[0]->department_id}'
				AND room_id ='{$worker[0]->room_id}' AND worker_id='{$worker[0]->id}'";
				$query = $con->prepare($sql);
				$query->execute();
				if ($query->rowCount()>=1) {
					$subresult=$query->fetchAll(PDO::FETCH_OBJ);
					for ($i=0;$i<count($subresult);$i++)
					{
						$sql = "UPDATE filial_rooms_hearing SET departament_id='{$data['department_id']}' WHERE id = '{$subresult[$i]->id}'";
						$query = $con->prepare($sql);
						$query->execute();
					}
				}
			}
			$sql = "UPDATE workers SET department_id=:department_id WHERE id='{$worker_id}'";
            $query = $con->prepare($sql);
            $query->bindParam('department_id', $data['department_id']);
            $query->execute();
            $result['status'] = 'success';
            $result['reload'] = 'true';
        } catch (Exception $ex) {
            $result['status'] = 'error';
            $result['reload'] = 'Неизвестная ошибка (' . $ex->getMessage() . ')';
        }
        return $result;
    }
}
