<?php

namespace App\Views;

use App\Models\InterfaceModel;
use DateTime;

class InterfaceView
{
    /**
     * @var InterfaceModel
     */
    private $model;

    public function __construct(InterfaceModel $model)
    {
        $this->model = $model;
    }

    /*
     * Вывод меню
     * TODO: Добавить иконки, стили
     */
    public function getMenuView()
    {
        $HTML = "";
        $HTML .= "<div id='mainMenu'>";
        $HTML .= "<div class='tablebl'>";
        $menuArray = $this->model->getMenuArrayModel();
        //$HTML .= "<div class='menuItem'><a href='" . base_path() . "'>Главная</a></div>";
        if (isset($menuArray['status'])) {
            $HTML .= "<div class='error'>{$menuArray['message']}</div>";
        } else {
            for ($i = 0; $i < count($menuArray); $i++) {
                if($_SERVER['REQUEST_URI']==base_path().$menuArray[$i]->url) {
                    if ($menuArray[$i]->active_icon == "")
                        $HTML .= "<div class='menuItem active'><a href='" . base_path() . $menuArray[$i]->url . "'>{$menuArray[$i]->name}</a></div>";
                    else
                        $HTML .= "<div class='menuItem active'><a href='" . base_path() . $menuArray[$i]->url . "'><img src='".GetImageURL($menuArray[$i]->active_icon, 'icon')."' title='{$menuArray[$i]->name}' alt='{$menuArray[$i]->name}' width='50' height='50'><br>{$menuArray[$i]->name}</a></div>";
                } else {
                    if ($menuArray[$i]->passive_icon == "")
                        $HTML .= "<div class='menuItem passive'><a href='" . base_path() . $menuArray[$i]->url . "'>{$menuArray[$i]->name}</a></div>";
                    else
                        $HTML .= "<div class='menuItem passive'><a href='" . base_path() . $menuArray[$i]->url . "'><img src='".GetImageURL($menuArray[$i]->passive_icon, 'icon')."' title='{$menuArray[$i]->name}' alt='{$menuArray[$i]->name}' width='50' height='50'><br>{$menuArray[$i]->name}</a></div>";}
            }
        }
        $HTML .= "</div></div>";
        return $HTML;
    }

    /*
     * Вывод настроек филиала
     * TODO: Удалить метод, перенес в ConfigController.php
     */
    public function getSettingsView()
    {
        $HTML = "<br><h2>Настройки системы и филиала</h2><br>";
        //Интерфейсы
        $divlist="settings_interface,settings_mark,settings_document_type,settings_filial_section,settings_filial_department,settings_user_type,settings_filial_room,settings_permission_to_interface,settings_permission_to_department,settings_equipment_type,settings_user,settings_worker,settings_hearing,settings_access";
        $HTML .= "<div class='settingsMenu'>";
        $HTML .= "<div class='settingsMenuTab' onclick=\"showHideDivs(this, '{$divlist}', 'settings_interface', 'settingsMenuTab active');\">Интерфейсы</div>";
        $HTML .= "<div class='settingsMenuTab' onclick=\"showHideDivs(this, '{$divlist}', 'settings_mark', 'settingsMenuTab active');\">Метки</div>";
        $HTML .= "<div class='settingsMenuTab' onclick=\"showHideDivs(this, '{$divlist}', 'settings_document_type', 'settingsMenuTab active');\">Типы документов</div>";
        $HTML .= "<div class='settingsMenuTab' onclick=\"showHideDivs(this, '{$divlist}', 'settings_filial_section', 'settingsMenuTab active');\">Отделы филиала {$_SESSION['filial_id']}</div>";
        $HTML .= "<div class='settingsMenuTab' onclick=\"showHideDivs(this, '{$divlist}', 'settings_filial_department', 'settingsMenuTab active');\">Департаменты филиала {$_SESSION['filial_id']}</div>";
        $HTML .= "<div class='settingsMenuTab' onclick=\"showHideDivs(this, '{$divlist}', 'settings_user_type', 'settingsMenuTab active');\">Типы пользователей</div>";
        $HTML .= "<div class='settingsMenuTab' onclick=\"showHideDivs(this, '{$divlist}', 'settings_filial_room', 'settingsMenuTab active');\">Кабинеты филиала</div>";
        $HTML .= "<div class='settingsMenuTab' onclick=\"showHideDivs(this, '{$divlist}', 'settings_permission_to_interface', 'settingsMenuTab active');\">Стандартные права доступа к интерфейсам</div>";
        $HTML .= "<div class='settingsMenuTab' onclick=\"showHideDivs(this, '{$divlist}', 'settings_permission_to_department', 'settingsMenuTab active');\">Стандартные права доступа отдела к помещениям</div>";
        $HTML .= "<div class='settingsMenuTab' onclick=\"showHideDivs(this, '{$divlist}', 'settings_equipment_type', 'settingsMenuTab active');\">Типы оборудования</div>";
        $HTML .= "<div class='settingsMenuTab' onclick=\"showHideDivs(this, '{$divlist}', 'settings_user', 'settingsMenuTab active');\">Пользователи</div>";
        $HTML .= "<div class='settingsMenuTab' onclick=\"showHideDivs(this, '{$divlist}', 'settings_worker', 'settingsMenuTab active');\">Сотрудники</div>";
        $HTML .= "<div class='settingsMenuTab' onclick=\"showHideDivs(this, '{$divlist}', 'settings_hearing', 'settingsMenuTab active');\">Слушания</div>";
        $HTML .= "<div class='settingsMenuTab' onclick=\"showHideDivs(this, '{$divlist}', 'settings_access', 'settingsMenuTab active');\">Доступы на территорию</div>";
        $HTML.="</div>";

        $HTML.="<div class='settingsContent'>";

        $interfaces = $this->model->getInterfacesModel();

        $HTML .="<div id='settings_interface' class='overfltable'>";

        $HTML .= "<div class='button' onclick=\"sendAjax('/interface/form/', 'GET')\">Добавить новый интерфейс</div><br><br>";

        $HTML .= "<table class='akkt' border='1' cellpadding='5'>";

        $HTML .= "<tr>";
        $HTML .= "<th>№</th>";
        $HTML .= "<th>Имя</th>";
        $HTML .= "<th>URL</th>";
        $HTML .= "<th>Активная иконка</th>";
        $HTML .= "<th>Пассивная иконка</th>";
        $HTML .= "<th>Порядок сортировки</th>";
        $HTML .= "<th></th>";
        $HTML .= "</tr>";

        if (isset($interfaces['status'])) {
            $HTML .= "<tr>";
            $HTML .= "<td class='error'>{$interfaces['message']}</td>";
            $HTML .= "</tr>";
        } else {
            for ($i = 0; $i < count($interfaces); $i++) {
                $HTML .= "<tr>";
                $HTML .= "<td>{$interfaces[$i]->id}</td>";
                $HTML .= "<td>{$interfaces[$i]->name}</td>";
                $HTML .= "<td>{$interfaces[$i]->url}</td>";
                $HTML .= "<td class='robotocr'><img src='" . GetImageURL($interfaces[$i]->active_icon, 'icon') . "' width='30'/></td>";
                $HTML .= "<td class='robotocr'><img src='" . GetImageURL($interfaces[$i]->passive_icon, 'icon') . "' width='30'/></td>";
                $HTML .= "<td>{$interfaces[$i]->num}</td>";
                $HTML .= "<td class='button blueak otst' text-align='center' onclick=\"sendAjax('/interface/form/{$interfaces[$i]->id}/', 'GET')\">Изменить</td>";
                //if($interfaces[$i]->id>10)
                //$HTML .= "<td class='button' onclick=\"sendAjax('deleteInterface={$interfaces[$i]->id}', 'DELETE')\">Удалить</td>";
                $HTML .= "</tr>";
            }
        }
        $HTML .= "</table>";
        $HTML .="</div>";


        //Метки
        $marks = $this->model->getMarksModel();

        $HTML .="<div id='settings_mark' style='display: none' class='overfltable'>";

        $HTML .= "<div class='button' onclick=\"sendAjax('/marks/form/', 'GET')\">Добавить новую метку</div><br><br>";

        $HTML .= "<table class='akkt' border='1' cellpadding='5'>";
        $HTML .= "<tr>";
        $HTML .= "<th>№</th>";
        $HTML .= "<th>Название</th>";
        $HTML .= "<th></th>";
        $HTML .= "</tr>";

        if (isset($marks['status'])) {
            $HTML .= "<tr>";
            $HTML .= "<td class='error'>{$marks['message']}</td>";
            $HTML .= "</tr>";
        } else {
            for ($i = 0; $i < count($marks); $i++) {
                $HTML .= "<tr>";
                $HTML .= "<td class='robotocr'>{$marks[$i]->id}</td>";
                $HTML .= "<td>{$marks[$i]->name}</td>";
                $HTML .= "<td class='button' onclick=\"sendAjax('/marks/form/{$marks[$i]->id}/', 'GET')\">Изменить</td>";
                //$HTML .= "<td class='button' onclick=\"sendAjax('deleteMark={$marks[$i]->id}', 'DELETE')\">Удалить</td>";
                $HTML .= "</tr>";
            }
        }
        $HTML .= "</table>";
        $HTML .="</div>";

        //типы документов

        $document_type = $this->model->getUserTypeDocumentsModel();

        $HTML .="<div id='settings_document_type' style='display: none' class='overfltable'>";

        $HTML .= "<div class='button' onclick=\"sendAjax('/documents-type/form/', 'GET')\">Добавить новый тип документа</div><br><br>";

        $HTML .= "<table class='akkt' border='1' cellpadding='5'>";

        $HTML .= "<tr>";
        $HTML .= "<th>№</th>";
        $HTML .= "<th>Название</th>";
        $HTML .= "<th></th>";
        $HTML .= "</tr>";

        if (isset($document_type['status'])) {
            $HTML .= "<tr>";
            $HTML .= "<td class='error'>{$document_type['message']}</td>";
            $HTML .= "</tr>";
        } else {
            for ($i = 0; $i < count($document_type); $i++) {
                $HTML .= "<tr>";
                $HTML .= "<td>{$document_type[$i]->id}</td>";
                $HTML .= "<td>{$document_type[$i]->name}</td>";
                $HTML .= "<td class='button' onclick=\"sendAjax('/documents-type/form/{$document_type[$i]->id}/', 'GET')\">Изменить</td>";
                //$HTML .= "<td class='button' onclick=\"sendAjax('deleteMark={$marks[$i]->id}', 'DELETE')\">Удалить</td>";
                $HTML .= "</tr>";
            }
        }
        $HTML .= "</table>";
        $HTML .="</div>";

        //Отделы филиала

        $filialDepartments = $this->model->getFilialDepartmentModel();

        $HTML .="<div id='settings_filial_section' style='display: none' class='overfltable'>";

        $HTML .= "<div class='button' onclick=\"sendAjax('/filial-departments/createform/section/', 'GET')\">Добавить новый отдел</div><br><br>";

        $HTML .= "<table class='akkt' border='1' cellpadding='5'>";
        $HTML .= "<tr>";
        $HTML .= "<th>№</th>";
        $HTML .= "<th>Название</th>";
        $HTML .= "<th>№ филиала</th>";
        $HTML .= "<th></th>";
        $HTML .= "</tr>";

        if (isset($filialDepartments['status'])) {
            $HTML .= "<tr>";
            $HTML .= "<td class='error'>{$filialDepartments['message']}</td>";
            $HTML .= "</tr>";
        } else {
            for ($i = 0; $i < count($filialDepartments); $i++) {
                $HTML .= "<tr>";
                $HTML .= "<td>{$filialDepartments[$i]->id}</td>";
                $HTML .= "<td>{$filialDepartments[$i]->name}</td>";
                $HTML .= "<td>{$filialDepartments[$i]->filial_id}</td>";
                $HTML .= "<td class='button' onclick=\"sendAjax('/filial-departments/form/{$filialDepartments[$i]->id}/section/', 'GET')\">Изменить</td>";
                //$HTML .= "<td class='button' onclick=\"sendAjax('deleteMark={$marks[$i]->id}', 'DELETE')\">Удалить</td>";
                $HTML .= "</tr>";
            }
        }
        $HTML .= "</table>";
        $HTML .="</div>";

        //Департаменты филиала

        $filialSuperDepartments = $this->model->getFilialDepartmentModel(null, 'departament');

        $HTML .="<div id='settings_filial_department' style='display: none' class='overfltable'>";

        $HTML .= "<div class='button' onclick=\"sendAjax('/filial-departments/createform/department/', 'GET')\">Добавить новый департамент</div><br><br>";

        $HTML .= "<table class='akkt' border='1' cellpadding='5'>";
        $HTML .= "<tr>";
        $HTML .= "<th>№</th>";
        $HTML .= "<th>Название</th>";
        $HTML .= "<th>Отдел филиала</th>";
        $HTML .= "<th></th>";
        $HTML .= "</tr>";

        if (isset($filialSuperDepartments['status'])) {
            $HTML .= "<tr>";
            $HTML .= "<td class='error'>{$filialSuperDepartments['message']}</td>";
            $HTML .= "</tr>";
        } else {
            for ($i = 0; $i < count($filialSuperDepartments); $i++) {
                $HTML .= "<tr>";
                $HTML .= "<td>{$filialSuperDepartments[$i]->id}</td>";
                $HTML .= "<td>{$filialSuperDepartments[$i]->name}</td>";
                $HTML .= "<td>{$filialSuperDepartments[$i]->filial_id}</td>";
                $HTML .= "<td class='button' onclick=\"sendAjax('/filial-departments/form/{$filialSuperDepartments[$i]->id}/department/', 'GET')\">Изменить</td>";
                //$HTML .= "<td class='button' onclick=\"sendAjax('deleteMark={$marks[$i]->id}', 'DELETE')\">Удалить</td>";
                $HTML .= "</tr>";
            }
        }
        $HTML .= "</table>";
        $HTML .="</div>";

        //Типы пользователей

        $userTypes = $this->model->getUserTypeModel();

        $HTML .="<div id='settings_user_type' style='display: none' class='overfltable'>";

        $HTML .= "<div class='button' onclick=\"sendAjax('/user-types/form/', 'GET')\">Добавить новый тип пользователей</div><br><br>";

        $HTML .= "<table class='akkt' border='1' cellpadding='5'>";
        $HTML .= "<tr>";
        $HTML .= "<th>№</th>";
        $HTML .= "<th>Название</th>";
        $HTML .= "<th></th>";
        $HTML .= "</tr>";

        if (isset($userTypes['status'])) {
            $HTML .= "<tr>";
            $HTML .= "<td class='error'>{$userTypes['message']}</td>";
            $HTML .= "</tr>";
        } else {
            for ($i = 0; $i < count($userTypes); $i++) {
                $HTML .= "<tr>";
                $HTML .= "<td>{$userTypes[$i]->id}</td>";
                $HTML .= "<td>{$userTypes[$i]->name}</td>";
                if ($userTypes[$i]->id > 3)
                    $HTML .= "<td class='button' onclick=\"sendAjax('/user-types/form/{$userTypes[$i]->id}/', 'GET')\">Изменить</td>";
                //$HTML .= "<td class='button' onclick=\"sendAjax('deleteMark={$marks[$i]->id}', 'DELETE')\">Удалить</td>";
                $HTML .= "</tr>";
            }
        }
        $HTML .= "</table>";
        $HTML .="</div>";

        //Кабинеты филиала

        $rooms = $this->model->getRoomModel();

        $HTML .="<div id='settings_filial_room' style='display: none' class='overfltable'>";

        $HTML .= "<div class='button' onclick=\"sendAjax('/rooms/form/', 'GET')\">Добавить новый кабинет в филиал</div><br><br>";

        $HTML .= "<table class='akkt' border='1' cellpadding='5'>";
        $HTML .= "<tr>";
        $HTML .= "<th>№</th>";
        $HTML .= "<th>Название</th>";
        $HTML .= "<th>Номер</th>";
        $HTML .= "<th>Рабочее время</th>";
        $HTML .= "<th>№ сотрудника</th>";
        $HTML .= "<th>Номер отдела</th>";
        $HTML .= "<th></th>";
        $HTML .= "</tr>";

        if (isset($rooms['status'])) {
            $HTML .= "<tr>";
            $HTML .= "<td class='error'>{$rooms['message']}</td>";
            $HTML .= "</tr>";
        } else {
            for ($i = 0; $i < count($rooms); $i++) {
                $HTML .= "<tr>";
                $HTML .= "<td>{$rooms[$i]->id}</td>";
                $HTML .= "<td>{$rooms[$i]->name}</td>";
                $HTML .= "<td>{$rooms[$i]->number}</td>";
                $HTML .= "<td></td>";
                $HTML .= "<td>{$rooms[$i]->worker_id}</td>";
                $HTML .= "<td>{$rooms[$i]->filial_id}</td>";
                $HTML .= "<td class='button' onclick=\"sendAjax('/rooms/form/{$rooms[$i]->id}/', 'GET')\">Изменить</td>";
                $HTML .= "</tr>";
            }
        }
        $HTML .= "</table>";
        $HTML .="</div>";


        //Стандартные права доступа к интерфейсам

        $HTML .="<div id='settings_permission_to_interface' style='display: none' class='overfltable'>";
        $permissions = $this->model->getPermissionsModel();
        $HTML .= "<select name='permission_id' onchange=\"sendAjax('/interfaces/permission/table/'+this.value+'/', 'GET');\">";
        $HTML .= "<option value='0'>Не выбраны права пользователя</option>";
        for ($i = 0; $i < count($permissions); $i++) {
            $HTML .= "<option value='{$permissions[$i]->id}'>{$permissions[$i]->name}</option>";
        }
        $HTML .= "</select><br><br>";

        $HTML .= "<div id='interfacePermissionTable'>";
        if (isset($permissions[0]->id))
            $HTML .= $this->showPermissionDefaultTableView($permissions[0]->id);
        $HTML .= "</div>";
        $HTML .= "</details>";
        $HTML .="</div>";


        //Стандартные права доступа отдела к помещениям

        $filialDepartments = $this->model->getFilialDepartmentModel();
        $HTML .="<div id='settings_permission_to_department' style='display: none' class='overfltable'>";
        $HTML .= "<select name='permissionType' onchange=\"sendAjax('/filial-departments/formroomstable/'+this.value+'/', 'GET');\">";
        $HTML .= "<option value='0'>Не выбран отдел</option>";
        for ($i = 0; $i < count($filialDepartments); $i++) {
            $HTML .= "<option value='{$filialDepartments[$i]->id}'>{$filialDepartments[$i]->name}</option>";
        }
        $HTML .= "</select><br><br>";

        $HTML .= "<div id='filialDepartmentsRoomPermissionTable'>";
        if (isset($permissions[0]->id))
            $HTML .= $this->showFilialDepartmentsRoomPermissionTableView($permissions[0]->id);
        $HTML .= "</div>";
        $HTML .="</div>";


        //Типы оборудования

        $equipment_types = $this->model->getEquipmentTypeModel();

        $HTML .="<div id='settings_equipment_type' style='display: none' class='overfltable'>";

        $HTML .= "<div class='button' onclick=\"sendAjax('/equipment/form/', 'GET')\">Создать новый тип оборудования</div><br><br>";

        $HTML .= "<table class='akkt' border='1' cellpadding='5'>";
        $HTML .= "<tr>";
        $HTML .= "<th>№</th>";
        $HTML .= "<th>Название</th>";
        $HTML .= "<th></th>";
        $HTML .= "</tr>";

        if (isset($equipment_types['status'])) {
            $HTML .= "<tr>";
            $HTML .= "<td class='error'>{$equipment_types['message']}</td>";
            $HTML .= "</tr>";
        } else {
            for ($i = 0; $i < count($equipment_types); $i++) {
                $HTML .= "<tr>";
                $HTML .= "<td>{$equipment_types[$i]->id}</td>";
                $HTML .= "<td>{$equipment_types[$i]->name}</td>";
                $HTML .= "<td class='button' onclick=\"sendAjax('/equipment/form/{$equipment_types[$i]->id}/', 'GET')\">Изменить</td>";
                //$HTML .= "<td class='button' onclick=\"sendAjax('deleteMark={$marks[$i]->id}', 'DELETE')\">Удалить</td>";
                $HTML .= "</tr>";
            }
        }
        $HTML .= "</tr>";
        $HTML .= "</table>";
        $HTML .="</div>";

        //Пользователи

        $HTML .="<div id='settings_user' style='display: none' class='overfltable'>";
        $HTML .= "<div id='UsersTable'>";
        $HTML .= $this->showUsersTableView();
        $HTML .= "</div>";
        $HTML .="</div>";



        //Сотрудники

        $HTML .="<div id='settings_worker' style='display: none' class='overfltable'>";
        $HTML .= "<div id='WorkersTable'>";
        $HTML .= $this->showWorkersTableView();
        $HTML .= "</div>";
        $HTML .="</div>";

        //Слушания

        $HTML .="<div id='settings_hearing' style='display: none' class='overfltable'>";
        $HTML .= "<div id='HearingsTable'>";
        $HTML .= $this->showHearingsTableView();
        $HTML .= "</div>";
        $HTML .="</div>";

        //Доступ на территорию

        $HTML .="<div id='settings_access' style='display: none' class='overfltable'>";
        $HTML .= "<div id='UserAccessTable'>";
        $HTML .= $this->showUserAccessTableView();
        $HTML .= "</div>";
        $HTML .="</div>";

        $HTML.="</div>";

        return $HTML;
    }


    /*
     * Вывод формы для создания интерфейса (в popup окне)
     * Return: JSON
     */
    public function showAddInterfaceFormView()
    {
        $json = [];
        $HTML = "";
        $HTML .= "<h2>Добавление интерфейса</h2>";
        $HTML .= "<form enctype='multipart/form-data' name='addInterfaceForm'>";
        $HTML .="<table>";
        $HTML .="<tr>";
        $HTML .= "<td>Название</td><td><input type='text' name='name'></td>";
        $HTML .="</tr>";
        $HTML .="<tr>";
        $HTML .= "<td>Ссылка</td><td><input type='text' name='url'</td>";
        //$HTML .= "<input type='text' name='icon' placeholder='Иконка'><br>";
        $HTML .="</tr>";
        $HTML .="<tr>";
        $HTML .= "<td>Неактивная иконка</td><td><input name='passive_icon' type='file'/></td>";
        $HTML .="</tr>";
        $HTML .="<tr>";
        $HTML .= "<td>Активная иконка</td><td><input name='active_icon' type='file' /></td>";
        $HTML .="</tr>";
        $HTML .="<tr>";
        $HTML .= "<td>Номер для сортировки</td><td><input type='number' name='num'></td>";
        $HTML .="</tr>";
        $HTML .="</table>";
        $HTML .= "</form>";
        $HTML .= "<div class='button' onclick=\"sendAjax('/interface/', 'POST', 'addInterfaceForm')\">Создать интерфейс</div>";
        $json['status'] = 'success';
        $json['div'] = 'popup';
        $json['html'] = htmlspecialchars($HTML);
        return json_encode($json);
    }

    /*
     * Вывод формы для создания интерфейса (в popup окне)
     * Return: JSON
     */
    public function showChangeInterfaceFormView($id = null)
    {
        $json = [];
        $interface = $this->model->getInterfacesModel($id);
        $HTML = "";
        $HTML .= "<h2>Изменение интерфейса</h2>";
        $HTML .= "<form enctype='multipart/form-data' name='addInterfaceForm'>";
        $HTML .= "<table><tr><td>Название</td><td><input type='text' name='name' value='{$interface[0]->name}'></td></tr>";
        $HTML .= "<tr><td>Ссылка</td><td><input type='text' name='url' value='{$interface[0]->url}'</td></tr>";
        //$HTML .= "<input type='text' name='icon' placeholder='Иконка'><br>";
        $HTML .= "<tr><td>Пасивная иконка</td><td><input name='passive_icon' type='file' /></td></tr>";
        $HTML .= "<tr><td>Активная иконка:</td><td><input name='active_icon' type='file' /></td></tr>";
        $HTML .= "<tr><td>Номер для сортировки</td><td><input type='number' name='num' value='{$interface[0]->num}'></td></tr></table>";
        $HTML .= "</form>";
        $HTML .= "<div class='button' onclick=\"sendAjax('/interface/{$interface[0]->id}/', 'POST', 'addInterfaceForm')\">Изменить интерфейс</div>";
        $json['status'] = 'success';
        $json['div'] = 'popup';
        $json['html'] = htmlspecialchars($HTML);
        return json_encode($json);
    }

    /*
     * Вывод формы для создания метки
     * Return: JSON
     */
    public function showAddMarkFormView()
    {
        $json = [];
        $HTML = "";
        $HTML .= "<h2>Добавление метки</h2>";
        $HTML .= "<form name='addMarkForm'>";
        $HTML .= "<table><tr><td>Название</td><td><input type='text' name='name'></td></tr>";
        $HTML .= "<tr><td>Тихая тревога<input name='quite_alert' type='checkbox'";
        $HTML .= "</form>";
        $HTML .= "<div class='button' onclick=\"sendAjax('/marks/', 'POST', 'addMarkForm')\">Создать метку</div>";
        $json['status'] = 'success';
        $json['div'] = 'popup';
        $json['html'] = htmlspecialchars($HTML);
        return json_encode($json);
    }

    /*
     * Вывод формы для изменения метки
     * Return: JSON
     */
    public function showChangeMarkFormView($id = null)
    {
        $json = [];
        $mark = $this->model->getMarksModel($id);
        $HTML = "";
        $HTML .= "<h2>Изменение метки - {$mark[0]->name}</h2>";
        $HTML .= "<form name='updateMarkForm'>";
        $HTML .= "<table><tr><td>Название метки</td><td><input type='text' name='name' value='{$mark[0]->name}'></td></tr>";
        $HTML .= "<tr><td>Тихая тревога<input name='quite_alert' type='checkbox'";
        if ($mark[0]->quite_alert) $HTML.=" checked";
        $HTML.="></td><td></td></tr></table>";
        $HTML .= "</form>";
        $HTML .= "<div class='button' onclick=\"sendAjax('/marks/{$mark[0]->id}/', 'POST', 'updateMarkForm')\">Изменить метку</div>";
        $json['status'] = 'success';
        $json['div'] = 'popup';
        $json['html'] = htmlspecialchars($HTML);
        return json_encode($json);
    }



    /*
    *Вывод формы для создания типов документов
    *Return: JSON
    */


    public function showAddUserDocumentTypeFormView()
    {
        $json = [];
        $HTML = "";
        $HTML .= "<h2>Добавление типа документа</h2>";
        $HTML .= "<form name='addUserDocumentTypeForm'>";
        $HTML .= "<table><tr><td>Название</td><td><input type='text' name='name' </td></tr></table>";
        $HTML .= "</form>";
        $HTML .= "<div class='button' onclick=\"sendAjax('/documents-type/', 'POST', 'addUserDocumentTypeForm')\">Создать тип документа</div>";
        $json['status'] = 'success';
        $json['div'] = 'popup';
        $json['html'] = htmlspecialchars($HTML);
        return json_encode($json);
    }

    /*
     * Вывод формы для изменения типа документов
     * Return: JSON
     */
    public function showChangeUserDocumentTypeView($id = null)
    {
        $json = [];
        $document_type = $this->model->getUserTypeDocumentsModel($id);
        $HTML = "";
        $HTML .= "<h2>Изменение типа документа - {$document_type[0]->name}</h2>";
        $HTML .= "<form name='updateUserTypeDocumentForm'>";
        $HTML .= "<table><tr><td>Название типа документа</td><td><input type='text' name='name' value='{$document_type[0]->name}'></td></tr></table>";
        $HTML .= "</form>";
        $HTML .= "<div class='button' onclick=\"sendAjax('/documents-type/{$document_type[0]->id}/', 'POST', 'updateUserTypeDocumentForm')\">Изменить тип документа</div>";
        $json['status'] = 'success';
        $json['div'] = 'popup';
        $json['html'] = htmlspecialchars($HTML);
        return json_encode($json);
    }


    /*
    *Вывод формы для создания типа пользователя
    *Return: JSON
    */


    public function showAddUserTypeView()
    {
        $json = [];
        $HTML = "";
        $HTML .= "<h2>Добавление типа пользователя</h2>";
        $HTML .= "<form name='addUserTypeForm'>";
        $HTML .= "<table><tr><td>Название<td><td><input type='text' name='name'></td></tr>";
        $user_types = $this->model->getUserTypesModel();
        $HTML .= "<tr><td>Родительский тип</td><td><select name='parent_id'>";
        if (isset($user_types['status'])) {
            $HTML .= "<option value='0'>Нет данных</option>";
        } else {
            $HTML .= "<option value='0'>Нет родителя</option>";
            for ($i = 0; $i < count($user_types); $i++) {
                $HTML .= "<option value='{$user_types[$i]->id}'>{$user_types[$i]->name}</option>";
            }
        }
        $HTML .= "</select></td></tr></table>";
        //$HTML .= "<input type='text' name='pare' placeholder='Название'><br>";
        //$HTML .= "<input type='text' name='name' placeholder='Название'><br>";
        $HTML .= "</form>";
        $HTML .= "<div class='button' onclick=\"sendAjax('/user-types/', 'POST', 'addUserTypeForm')\">Создать тип пользователя</div>";
        $json['status'] = 'success';
        $json['div'] = 'popup';
        $json['html'] = htmlspecialchars($HTML);
        return json_encode($json);
    }

    /*
     * Вывод формы для изменения типа пользователя
     * Return: JSON
     */
    public function showChangeUserTypeView($id = null)
    {
        $json = [];
        $userType = $this->model->getUserTypeModel($id);
        $HTML = "";
        $HTML .= "<h2>Изменение тип пользователя - {$userType[0]->name}</h2>";
        $HTML .= "<form name='updateUserTypeForm'>";
        $HTML .= "<table><tr><td>Название отдела</td><td><input type='text' name='name' value='{$userType[0]->name}'></td></tr>";
        $user_types = $this->model->getUserTypesModel();
        $HTML .= "<tr><td>Родительский тип</td><td><select name='parent_id'>";
        if (isset($user_types['status'])) {
            $HTML .= "<option value='0' selected>Нет данных</option>";
        } else {
            $HTML .= "<option value='0'";
            if ($userType[0]->parent_id == 0) $HTML .= " selected ";
            $HTML .= ">Нет родителя</option>";
            for ($i = 0; $i < count($user_types); $i++) {
                if ($user_types[$i]->id == $userType[0]->id) continue;
                $HTML .= "<option value='{$user_types[$i]->id}'";
                if ($user_types[$i]->id == $userType[0]->parent_id) $HTML .= " selected ";
                $HTML .= ">{$user_types[$i]->name}</option>";
            }
        }
        $HTML .= "</select></td></tr></table>";
        $HTML .= "</form>";
        $HTML .= "<div class='button' onclick=\"sendAjax('/user-types/{$userType[0]->id}/', 'POST', 'updateUserTypeForm')\">Изменить тип пользователя</div>";
        $json['status'] = 'success';
        $json['div'] = 'popup';
        $json['html'] = htmlspecialchars($HTML);
        return json_encode($json);
    }

    /*
    *Вывод формы для создания кабинета филиала
    *Return: JSON
    */

    public function showAddRoomsView()
    {
        $json = [];
        $HTML = "";
        $HTML .= "<h2>Добавление кабинета филиала</h2>";
        $HTML .= "<form name='addRoomsForm'>";
        $HTML .= "<table><tr><td>Название</td><td><input type='text' name='name'></td></tr>";
        $HTML .= "<tr><td>Этаж</td><td><input type='number' name='floor'></td></tr>";
        $HTML .= "<tr><td>Номер</td><td><input type='number' name='number'></td></tr>";
        $HTML .= "<tr><td>Время работы</td><td><input type='text' name='work_time'></td></tr>";
        $workers = $this->model->getWorkersModel();
        $HTML .= "<tr><td>Ответственный работник</td><td><select name='worker_id'>";
        if (isset($workers['status'])) {
            $HTML .= "<option value='0'>Нет данных</option>";
        } else {
            $HTML .= "<option value='0'>Не выбран ответственный работник</option>";
            for ($i = 0; $i < count($workers); $i++) {
                $HTML .= "<option value='{$workers[$i]->id}'>{$workers[$i]->first_name} {$workers[$i]->patronymic} {$workers[$i]->surname}</option>";
            }
        }
        $HTML .= "</select></td></tr></table>";
        $HTML .= "</form>";
        $HTML .= "<div class='button' onclick=\"sendAjax('/rooms/', 'POST', 'addRoomsForm')\">Создать филиал</div>";
        $json['status'] = 'success';
        $json['div'] = 'popup';
        $json['html'] = htmlspecialchars($HTML);
        return json_encode($json);
    }

    /*
     * Вывод формы для изменения филиала отдела
     * Return: JSON
     */
    public function showChangeRoomView($id = null)
    {
        $json = [];
        $rooms = $this->model->getRoomModel($id);
        $HTML = "";
        $HTML .= "<h2>Изменение комнаты - {$rooms[0]->name}</h2>";
        $HTML .= "<form name='updateRoomForm'>";
        $HTML .= "<table><tr><td>Название</td><td><input type='text' name='name' value='{$rooms[0]->name}'></td></tr>";
        $HTML .= "<tr><td>Номер</td><td><input type='number' name='number' value='{$rooms[0]->number}'></td></tr>";
        $workers = $this->model->getWorkersModel();
        $HTML .= "<tr><td>Ответственный работник</td><td><select name='worker_id'>";
        if (isset($workers['status'])) {
            $HTML .= "<option value='0' selected>Нет данных</option>";
        } else {
            for ($i = 0; $i < count($workers); $i++) {
                $HTML .= "<option value='{$workers[$i]->id}'";
                if ($workers[$i]->id == $rooms[0]->worker_id) $HTML .= " selected ";
                $HTML .= ">{$workers[$i]->first_name} {$workers[$i]->patronymic} {$workers[$i]->surname}</option>";
            }
        }
        $HTML .= "</select></td></tr></table>";
        $HTML .= "</form>";
        $HTML .= "<div class='button' onclick=\"sendAjax('/rooms/{$rooms[0]->id}/', 'POST', 'updateRoomForm')\">Изменить помещение</div>";
        $json['status'] = 'success';
        $json['div'] = 'popup';
        $json['html'] = htmlspecialchars($HTML);
        return json_encode($json);
    }


    /*
    *Вывод формы для создания типа оборудования
    *Return: JSON
    */

    public function showAddEquipmentTypeView()
    {
        $json = [];
        $HTML = "";
        $HTML .= "<h2>Добавление типа оборудования</h2>";
        $HTML .= "<form name='addEquipmentTypeForm'>";
        $HTML .= "<table><tr><td>Название</td><td><input type='text' name='name'></td></tr></table>";
        $HTML .= "</form>";
        $HTML .= "<div class='button' onclick=\"sendAjax('/equipment/', 'POST', 'addEquipmentTypeForm')\">Создать тип оборудования</div>";
        $json['status'] = 'success';
        $json['div'] = 'popup';
        $json['html'] = htmlspecialchars($HTML);
        return json_encode($json);
    }

    /*
     * Вывод формы для изменения типа оборудования
     * Return: JSON
     */
    public function showChangeEquipmentTypeView($id = null)
    {
        $json = [];
        $equipment_types = $this->model->getEquipmentTypeModel($id);
        $HTML = "";
        $HTML .= "<h2>Изменение типа оборудования</h2>";
        $HTML .= "<form name='updateEquipmentTypeForm'>";
        $HTML .= "<table><tr><td>Название</td><td><input type='text' name='name' placeholder='Название' value='{$equipment_types[0]->name}'></td></tr></table>";
        $HTML .= "</form>";
        $HTML .= "<div class='button' onclick=\"sendAjax('/equipment/{$equipment_types[0]->id}/', 'POST', 'updateEquipmentTypeForm')\">Изменить</div>";
        $json['status'] = 'success';
        $json['div'] = 'popup';
        $json['html'] = htmlspecialchars($HTML);
        return json_encode($json);
    }


    /*
     * Таблица пользователей
     * Return: HTML
     */
    public function showUsersTableView()
    {
        $users = $this->model->getUsersModel();
        $HTML = "<div class='overfltable'>";
        $HTML .= "<div class='button otst' onclick=\"sendAjax('/users/form/', 'GET')\">Добавить нового пользователя</div><br><br>";
        $HTML .= "<div class='fixheight'>";
        $HTML .= "<table class='akkt' border='1' cellpadding='5'>";
        $HTML .= "<tr>";
        $HTML .= "<th>№</th>";
        $HTML .= "<th>ФИО</th>";
        $HTML .= "<th>Дата рождения</th>";
        $HTML .= "<th>Телефон</th>";
        $HTML .= "<th>Почта</th>";
        $HTML .= "<th>Фото</th>";
        $HTML .= "<th>Дата регистрации</th>";
        $HTML .= "<th>Персона</th>";
        $HTML .= "<th>Статус</th>";
        $HTML .= "<th>Филиал</th>";
        $HTML .= "</tr>";

        if (isset($users['status'])) {
            $HTML .= "<tr>";
            $HTML .= "<td class='error'>{$users['message']}</td>";
            $HTML .= "</tr>";
        } else {
            for ($i = 0; $i < count($users); $i++) {
                $HTML .= "<tr>";

                $HTML .= "<td class='robotocr'>{$users[$i]->id}</td>";
                $HTML .= "<td class='ralewayreg'><a href='".base_path()."account/{$users[$i]->id}/'>{$users[$i]->surname} {$users[$i]->first_name} {$users[$i]->patronymic}</a></td>";
                $date=new DateTime($users[$i]->birthday);
				$HTML .= "<td class='robotocr'>{$date->Format('d.m.Y')}</td>";
                $HTML .= "<td class='robotocr'>{$users[$i]->phone}</td>";
                $HTML .= "<td class='robotocr'>{$users[$i]->email}</td>";

                $HTML .= "<td class='robotocr'><img src='" . GetImageURL($users[$i]->user_photo, 'user_photo') . "' width='37'/></td>";
                //$HTML .= "<td class='robotocr'><img src='".base_path()."images/icons/chelovek2.PNG' class='bigIcon'></td>";

                $date=new DateTime($users[$i]->reg_date);
				$HTML .= "<td class='robotocr'>{$date->Format('d.m.Y')}</td>";
                //$HTML .= "<td class='robotocr'>{$users[$i]->video_identify_id}</td>";
                $HTML .= "<td class='robotocr'></td>";
                $HTML .= "<td class='robotocr'>{$users[$i]->user_type}</td>";
                $HTML .= "<td class='ralewayreg'>{$users[$i]->filial_name}</td>";

                $HTML .= "<td class='button blueak otst' onclick=\"sendAjax('/users/form/{$users[$i]->id}/', 'GET')\">Изменить</td>";
                if ($users[$i]->user_type_id == 2 || $users[$i]->main_class == 2) $HTML .= "<td class='button greenak otst' onclick=\"sendAjax('/workers/form/{$users[$i]->id}/', 'GET')\">Сделать сотрудником</td>";
                //$HTML .= "<td class='button grayak otst'><a href='".base_path()."account/{$users[$i]->id}/' style='color: white;'>Профиль</a></td>";
                $HTML .= "</tr>";
            }
        }

        $HTML .= "</table>";

        $HTML .= "</div>";
        $HTML .= "</div>";

        return $HTML;
    }


    /*
     * Вывод формы добавления сотрудника
     * Return: HTML
     */
    public function showAddWorkerView($id = null)
    {
        $permissions = $this->model->getPermissionsModel();
        $user = $this->model->getUsersModel($id);
        $user_types = "SELECT * FROM user_types WHERE (main_class='1' OR id='1') AND (filial_id='0' OR filial_id='{$_SESSION['filial_id']}')";
        $user_types = $this->model->sendQuery($user_types);
        $departaments = $this->model->getFilialDepartmentModel();

        $HTML = "";
        $HTML .= "<h2>Создание сотрудника Пользователь - {$user[0]->surname} {$user[0]->first_name} {$user[0]->patronymic}</h2>";
        $HTML .= "<form name='addWorkerForm'>";
        $HTML .= "<table><tr><td>Логин</td><td><input class='margins boryes robotocr' type='text' name='login'></td></tr>";
        $HTML .= "<tr><td>Пароль</td><td><input class='margins boryes robotocr' type='text' name='password'></td></tr>";
        $HTML .= "<tr><td>Код</td><td><input class='margins boryes robotocr' type='text' name='code'></td></tr>";
        $HTML .= "<tr><td>Уровень доступа</td><td><select class='margins boryes' name='permission_id'>";
        if (isset($permissions['status'])) {
            $HTML .= "<option value='0' disabled>Нет данных</option>";
        } else {
            $HTML .= "<option value='0' disabled>Не выбран уровень доступа</option>";
            for ($i = 0; $i < count($permissions); $i++) {
                $HTML .= "<option value='{$permissions[$i]->id}'>{$permissions[$i]->name}</option>";
            }
        }
        $HTML .= "</select></td></tr>";

        $HTML .= "<tr><td>Тип сотрудника</td><td><select class='margins boryes' name='user_type'>";
        for($i=0;$i<count($user_types); $i++)
        {
            $HTML .= "<option value='{$user_types[$i]->id}'>{$user_types[$i]->name}</option>";
        }
        $HTML .= "</select></td></tr>";
        $HTML .= "<tr><td><h1>Доступ к отделам</h1></td></tr>";

        for($i=0;$i<count($departaments); $i++) {
            $HTML .= "<tr><td><input class='margins boryes' type='checkbox' name='departament_{$departaments[$i]->id}'>{$departaments[$i]->name}</td><td></td></tr>";
        }

        $HTML .= "</table></form>";
        $HTML .= "<div class='button margins' onclick=\"sendAjax('/workers/{$id}/', 'POST', 'addWorkerForm')\">Создать сотрудника</div>";
        return $HTML;
    }


    /*
     * Вывод формы для изменения сотрудника
     * Return: JSON
     */
    public function showChangeWorkerView($id = null)
    {
        $json = [];
        $workers = $this->model->getWorkersModel($id);
        $HTML = "";
        $HTML .= "<h2>Изменение данных сотрудника - {$workers[0]->surname} {$workers[0]->first_name} {$workers[0]->patronymic}</h2>";
        $HTML .= "<form name='updateWorkerForm'>";
        $HTML .= "<table><tr><td>Логин</td><td><input type='text' name='login' value='{$workers[0]->login}'></td></tr>";
        $HTML .= "<tr><td>Пароль</td><td><input type='text' name='password' value='{$workers[0]->password}'></td></tr>";
        $HTML .= "<tr><td>Код</td><td><input type='text' name='code' value='{$workers[0]->code}'></td></tr>";
        $HTML .= "<tr><td>Уровень доступа</td><td><select name='permission_id'>";
        $permissions = $this->model->getPermissionsModel();
        if (isset($permissions['status'])) {
            $HTML .= "<option value='0'>Нет данных</option>";
        } else {
            $HTML .= "<option value='0'>Не выбран уровень доступа</option>";
            for ($i = 0; $i < count($permissions); $i++) {
                $HTML .= "<option value='{$permissions[$i]->id}'";
                if ($permissions[$i]->id == $workers[0]->permission_id) $HTML .= " selected";
                $HTML .= ">{$permissions[$i]->name}</option>";
            }
        }
        $HTML .= "</select></td></tr></table>";
        $HTML .= "</form>";
        $HTML .= "<div class='button' onclick=\"sendAjax('/workers/edit/{$workers[0]->id}/', 'POST', 'updateWorkerForm')\">Изменить данные</div>";
        $json['status'] = 'success';
        $json['div'] = 'popup';
        $json['html'] = htmlspecialchars($HTML);
        return json_encode($json);
    }

    /*
     * Таблица сотрудников
     * Return: HTML
     */
    public function showWorkersTableView()
    {
        $workers = $this->model->getWorkersModel();
        $HTML ="";

        $HTML .= "<table class='akkt' border='1' cellpadding='5'>";
        $HTML .= "<tr>";
        $HTML .= "<th>№</th>";
        $HTML .= "<th>Имя</th>";
        $HTML .= "<th>Отчество</th>";
        $HTML .= "<th>Фамилия</th>";
        $HTML .= "<th>Логин</th>";
        $HTML .= "<th>Пароль</th>";
        $HTML .= "<th>Код</th>";
        $HTML .= "<th>№ доступа</th>";
        $HTML .= "<th>№ филиала</th>";
        $HTML .= "<th>Идентификатор пользователя</th>";
        $HTML .= "<th></th>";
        $HTML .= "</tr>";

        if (isset($workers['status'])) {
            $HTML .= "<tr>";
            $HTML .= "<td class='error'>{$workers['message']}</td>";
            $HTML .= "</tr>";
        } else {
            for ($i = 0; $i < count($workers); $i++) {
                $HTML .= "<tr>";
                $HTML .= "<td>{$workers[$i]->id}</td>";
                $HTML .= "<td>{$workers[$i]->first_name}</td>";
                $HTML .= "<td>{$workers[$i]->patronymic}</td>";
                $HTML .= "<td>{$workers[$i]->surname}</td>";
                $HTML .= "<td>{$workers[$i]->login}</td>";
                $HTML .= "<td>{$workers[$i]->password}</td>";
                $HTML .= "<td>{$workers[$i]->code}</td>";
                $HTML .= "<td>{$workers[$i]->permission_id}</td>";
                $HTML .= "<td>{$workers[$i]->filial_id}</td>";
                $HTML .= "<td>{$workers[$i]->user_id}</td>";
                $HTML .= "<td class='button' onclick=\"sendAjax('/workers/form/edit/{$workers[$i]->id}/', 'GET')\">Изменить</td>";
                $HTML .= "</tr>";
            }
        }
        $HTML .= "</table>";
        return $HTML;
    }



    /*
     * Таблица слушаний
     * Return: HTML
     */
    public function showHearingsTableView()
    {
        $HTML ="";
        $hearings = $this->model->getHearingsModel();
        $HTML .= "<table class='akkt' border='1' cellpadding='5'>";
        $HTML .= "<tr>";
        $HTML .= "<th>№</th>";
        $HTML .= "<th>№ кабинета</th>";
        $HTML .= "<th>Название</th>";
        $HTML .= "<th>Код</th>";
        $HTML .= "<th>Дата слушания</th>";
        $HTML .= "<th>Код сотрудника</th>";
        $HTML .= "<th>№ филиала</th>";
        $HTML .= "<th>Дата</th>";
        $HTML .= "<th>Время</th>";
        $HTML .= "<th></th>";
        $HTML .= "</tr>";

        if (isset($hearings['status'])) {
            $HTML .= "<tr>";
            $HTML .= "<td class='error'>{$hearings['message']}</td>";
            $HTML .= "</tr>";
        } else {
            for ($i = 0; $i < count($hearings); $i++) {
                $HTML .= "<tr>";
                $HTML .= "<td>{$hearings[$i]->id}</td>";
                $HTML .= "<td>{$hearings[$i]->room_id}</td>";
                $HTML .= "<td>{$hearings[$i]->name}</td>";
                $HTML .= "<td>{$hearings[$i]->code}</td>";
				$date = new DateTime($hearings[$i]->hdate);
                $HTML .= "<td>{$date->Format('d.m.Y')}</td>";
                $HTML .= "<td>{$hearings[$i]->worker_id}</td>";
                $HTML .= "<td>{$hearings[$i]->filial_id}</td>";
				$date = new DateTime($hearings[$i]->date);
                $HTML .= "<td>{$date->Format('d.m.Y')}</td>";
                $HTML .= "<td>{$hearings[$i]->time}</td>";
                $HTML .= "<td class='button' onclick=\"sendAjax('/hearings/form/{$hearings[$i]->id}/', 'GET')\">Изменить</td>";
                $HTML .= "</tr>";
            }
        }
        /*$HTML .= "<tr>";
        $HTML .= "<td class='button' onclick=\"sendAjax('/hearings/form/', 'GET')\">Добавить новое слушание</td>";
        $HTML .= "</tr>";*/
        $HTML .= "</table>";
        return $HTML;
    }







    /*
     * Таблица доступа пользователей на территорию филиала
     * Return: HTML
     */
    public function showUserAccessTableView()
    {
        $HTML ="";
        $useraccess = $this->model->getUserAccessModel();
        $HTML .= "<div class='button' onclick=\"sendAjax('/useraccess/form/', 'GET')\">Добавить новый доступ</div><br><br>";
        $HTML .= "<table class='akkt' border='1' cellpadding='5'>";
        $HTML .= "<tr>";
        $HTML .= "<th>№</th>";
        $HTML .= "<th>Пользователь</th>";
        $HTML .= "<th>Слушание</th>";
        $HTML .= "<th>Код</th>";
        $HTML .= "<th>Статус</th>";
        $HTML .= "<th>Металлосканер</th>";
        $HTML .= "<th>Информация</th>";
        $HTML .= "<th>Действие</th>";
        $HTML .= "<th></th>";
        $HTML .= "</tr>";

        if (isset($useraccess['status'])) {
            $HTML .= "<tr>";
            $HTML .= "<td class='error'>{$useraccess['message']}</td>";
            $HTML .= "</tr>";
        } else {
            for ($i = 0; $i < count($useraccess); $i++) {
                $HTML .= "<tr>";
                $HTML .= "<td>{$useraccess[$i]->id}</td>";
                $HTML .= "<td>{$useraccess[$i]->first_name} {$useraccess[$i]->patronymic} {$useraccess[$i]->surname}</td>";
                $HTML .= "<td>{$useraccess[$i]->hearingname}</td>";
                $HTML .= "<td>{$useraccess[$i]->code}</td>";
                $HTML .= "<td>";
                if ((($useraccess[$i]->status==0)||($useraccess[$i]->status==2))&&($useraccess[$i]->hearing_id==0))
                    $HTML .= "Нет на рабочем месте";
                if (($useraccess[$i]->status==1)&&($useraccess[$i]->hearing_id==0))
                    $HTML .= "Сотрудник на работе";
                if (($useraccess[$i]->status==1)&&($useraccess[$i]->hearing_id!=0))
                    $HTML .= "Посетитель в учреждении";
                if (($useraccess[$i]->status==2)&&($useraccess[$i]->hearing_id!=0))
                    $HTML .= "Посетитель покинул учреждение";
                $HTML .= "</td>";
                //$HTML .= "<td>{$useraccess[$i]->metalscaner}</td>";
                $HTML .= "<td></td>";
                $HTML .= "<td></td>";
                $HTML .= "<td>{$useraccess[$i]->info}</td>";
                $HTML .= "<td class='button' onclick=\"sendAjax('/useraccess/form/{$useraccess[$i]->id}/', 'GET')\">Изменить</td>";
                if (($useraccess[$i]->hearing_id==0)&&(($useraccess[$i]->status==0)||($useraccess[$i]->status==2)))
                    $HTML .= "<td class='button' onclick=\"sendAjax('/userpass/{$useraccess[$i]->user_id}/{$useraccess[$i]->id}/', 'POST')\">Пропустить</td>";
                if (($useraccess[$i]->hearing_id!=0)&&($useraccess[$i]->status==0))
                    $HTML .= "<td class='button' onclick=\"sendAjax('/userpass/{$useraccess[$i]->user_id}/{$useraccess[$i]->id}/', 'POST')\">Пропустить</td>";
                if ($useraccess[$i]->status==1)
                    $HTML .= "<td class='button' onclick=\"sendAjax('/userpass_modify/{$useraccess[$i]->id}/', 'POST')\">Выпустить</td>";
                $HTML .= "</tr>";
            }
        }
        $HTML .= "</table>";
        return $HTML;
    }



    /*
     * Страница - Аккаунты
     */
    public function accountsView()
    {
        $HTML = "";
        $HTML .= $this->showUsersTableView();
        return $HTML;
    }

    /*
     * Страница - Аккаунт
     */
    public function accountView($user_id = null)
    {
        $user = $this->model->getUsersModel($user_id);
        $marks = $this->model->getUserMars($user_id);
        $userPass = $this->model->getUserPass($user_id);
        $marksModel = $this->model->getMarksModel();

        $worker = null;
        $workerDepartaments = null;

        if($user[0]->main_class == 1 || $user[0]->user_type_id  == 1) {
            $worker = $this->model->getWorkersModel(null, $user_id);
            if ($worker!=null&&$worker[0]->id!=null)
            {
                $workerDepartaments = $this->model->getWorkerDepartaments($worker[0]->id);
                $workerRooms = $this->model->getWorkerRooms($worker[0]->id);
            }
        }


        $HTML = "";
        $HTML .= "<div class='buttonsControl nomargin'>";
        $HTML .= "<div class='tabButton active' onclick=\"showHideDivs(this, 'userAccount,passTable,userMarks,worker_access', 'userAccount', 'tabButton active');\">Аккаунт</div>";
        $HTML .= "<div class='tabButton' onclick=\"showHideDivs(this,'userAccount,passTable,userMarks,worker_access', 'passTable', 'tabButton active');\">Статистика</div>";
        $HTML .= "<div class='tabButton' onclick=\"showHideDivs(this,'userAccount,passTable,userMarks,worker_access', 'userMarks', 'tabButton active');\">Метки</div>";
        if($user[0]->main_class==1 || $user[0]->user_type_id==1) {
            $HTML .= "<div class='tabButton' onclick=\"showHideDivs(this,'userAccount,passTable,userMarks,worker_access', 'worker_access', 'tabButton active');\">Категория доступа</div>";
        }
        $HTML .= "</div>";

        $HTML .= "<div class='userProfile'>";
        //Аккаунт
        $HTML .= "<div id='userAccount'>";
        $HTML .= "<div class='userData'>";
        //$HTML .= "<div class='userPhoto'><img src='".base_path()."images/icons/Vhod_photo.PNG' class='bigIcon' \"></div>";
        $HTML .= "<div class='userPhoto'><img src='".GetImageURL($user[0]->user_photo, 'user_photo')."' class='bigIcon' ></div>";
        $HTML .= "<table border='1' cellpadding='7'>";
        $HTML .= "<tr><td>Фамилия</td><td class='ralewaybold'>{$user[0]->surname}</td></tr>";
        $HTML .= "<tr><td>Имя</td><td class='ralewaybold'>{$user[0]->first_name}</td></tr>";
        $HTML .= "<tr><td>Отчество</td><td class='ralewaybold'>{$user[0]->patronymic}</td></tr>";
		$date = new DateTime($user[0]->birthday);
        $HTML .= "<tr><td>Дата рождения</td><td class='robotocr'>{$date->Format('d.m.Y')}</td></tr>";
        $HTML .= "<tr><td>Тип документа</td><td class='robotocr'>Паспорт РФ</td></tr>";
        $HTML .= "<tr><td>Серия, номер</td><td class='robotocr'>0234 123456</td></tr>";
        $HTML .= "<tr><td>Статус</td><td class='robotocr'>{$user[0]->user_type}</td></tr>";
        $HTML .= "<tr><td>Место работы</td><td class='robotocr'>{$user[0]->work_place}</td></tr>";
        $HTML .= "<tr><td>Должность</td><td class='robotocr'>{$user[0]->work_position}</td></tr>";
        $HTML .= "<tr><td>Прописка</td><td class='robotocr'>г. Москва, ул. Ленина, 45-8</td></tr>";
        //$HTML .= "<tr><td>Работа в суде</td><td>Пример</td></tr>";
        $HTML .= "<tr><td>Телефон</td><td class='robotocr'>{$user[0]->phone}</td></tr>";
        $HTML .= "<tr><td>Почта</td><td class='robotocr'>{$user[0]->email}</td></tr>";
        $HTML .= "<tr><td>Рег. номер</td><td class='robotocr'>{$user[0]->id}</td></tr>";
        if($marks!=null&&$marks[0]->id!=null) {
            $HTML .= "<tr><td>Метка</td><td class='ralewaymed red'>{$marks[0]->mark_name}</td></tr>";
        } else {
            $HTML .= "<tr><td>Метка</td><td>Нет меток</td></tr>";
        }

        $HTML .= "</table>";

        $HTML .= "<div id='userDocuments'>";
        //$HTML .= " <div class='blockweight'><img src='".base_path()."images/icons/doc1.PNG' class='bigIcon' >";
        //$HTML .= "<img src='".base_path()."images/icons/doc2.PNG' class='bigIcon'>";
        //$HTML .= "<img src='".base_path()."images/icons/doc3.PNG' class='bigIcon'></div>";
        //$HTML .= "<div class='blockheight'><img src='".base_path()."images/icons/pasport1.PNG' class='bigIcon'>";
        //$HTML .= "<img src='".base_path()."images/icons/pasport2.PNG' class='bigIcon'></div>";
        //$HTML .= "<img src='".base_path()."images/icons/snils.PNG' class='bigIcon' \">";
        $HTML .= tpl('chunks/documents', ['user_id' => $user[0]->id]);

        /*$HTML .= "<div class='blockheight'><div id='rengakk'>";
         $HTML .= "<div class='reng ak'>";
            $HTML .= "<img src='".base_path()."images/icons/rentgen.PNG' class='bigIcon'>";
            $HTML .= "</br>Рентген</div><div class='lotok ak'>";
            $HTML .= "<img src='".base_path()."images/icons/lotok.PNG' class='bigIcon'>";
            $HTML .= "</br>Лоток</br> металлодетектора</div>";
        $HTML .= "</div>";

        $HTML .= "</div>";*/
        $HTML .= "</div>";

        $HTML .= "<div class='userDataButtons'>";
        //$HTML .= "<div class='button greenak otstupkn'>Подтвердить</div>";
        //$HTML .= "<div class='button redak otstupkn'>Не подтверждено</div>";
        $HTML .= "<div class='button nocolor otstupkn'><a href='#'>Заблокировать вход/выход !</a></div>";
        $HTML .= "<div class='button blueak otstupkn' style='color: white;float:right;' onclick=\"sendAjax('/userpass/form/{$user_id}/', 'GET')\">Пропуск</div>";
        $HTML .= "</div>";
        $HTML .= "</div>";



        $HTML .= "</div>";
        //$HTML .= "</div>";

        //Статистика
        $HTML .= "<div id='passTable' style='display: none'>";
        $HTML .= $this->showAccountStatisticView($user_id, null);
        $HTML .= "</div>";

        //Метки
        $HTML .= "<div id='userMarks' style='display: none'>";
        $HTML .= "<form name='addUserMarkForm'>";
        $HTML .= "<div class='metkhead'><b class='otstupkn'>Метка</b> ";
        $HTML .= "<select class='otstupkn' name='mark'>";
        for($i=0;$i<count($marksModel); $i++)
        {
            $HTML .= "<option value='{$marksModel[$i]->id}'>{$marksModel[$i]->name}</option>";
        }
        $HTML .= "</select>";
        $HTML .= "<div class='button otstupkn'  onclick=\"sendAjax('/usermark/{$user_id}/', 'POST', 'addUserMarkForm')\">Сохранить</div>";
        $HTML .= "<div class='button redak' onclick=\"sendAjax('/usermark_modify/', 'POST', 'selectedMarkForm')\">Удалить</div>";
        $HTML .= "</div></form>";



        $HTML .= "<div id='userMarksTable'>";
        if($marks!=null&&$marks[0]->id!=null) {
            $HTML .= "<form name='selectedMarkForm'>";
            $HTML .= "<table class='full' border='1' cellpadding='5'>";
            $HTML .= "<tr>";
            $HTML .= "<th colspan='2'>№</th>";
            $HTML .= "<th>Дата</th>";
            $HTML .= "<th>Время</th>";
            $HTML .= "<th>Метка</th>";
            $HTML .= "<th>Кто поставил</th>";
            $HTML .= "<th>Дата удаления</th>";
            $HTML .= "<th>Время удаления</th>";
            $HTML .= "<th>Кто удалил</th>";
            //$HTML .= "<th>Выбрать</th>";
            $HTML .= "</tr>";

            for ($i = 0; $i < count($marks); $i++) {
                if ($marks[$i]->id == null) continue;
                $HTML .= "<tr>";
                if ($marks[$i]->date_close == null) {
                    $HTML .= "<td class='robotocr'><input type='radio' name='selectedMark' value='{$marks[$i]->id}'></td>";
                    $HTML .= "<td class='robotocr'>{$marks[$i]->id}</td>";
                } else {
                    $HTML .= "<td class='robotocr' colspan='2'>{$marks[$i]->id}</td>";
                }
                $HTML .= "<td class='robotocr'>{$marks[$i]->mdate}</td>";
                $HTML .= "<td class='robotocr'>{$marks[$i]->mtime}</td>";
                $HTML .= "<td class='ralewaymed red'>{$marks[$i]->mark_name}</td>";
                $HTML .= "<td>{$marks[$i]->worker_add_surname} {$marks[$i]->worker_add_first_name} {$marks[$i]->worker_add_last_name} </td>";
                $date = new DateTime($marks[$i]->date_close);
				$HTML .= "<td class='robotocr'>{$date->Format('d.m.Y')}</td>";
				$date = new DateTime($marks[$i]->time_close);
                $HTML .= "<td class='robotocr'>{$date->Format('d.m.Y')}</td>";
                $HTML .= "<td>{$marks[$i]->worker_close_surname} {$marks[$i]->worker_close_first_name} {$marks[$i]->worker_close_last_name}</td>";
                $HTML .= "</tr>";
            }
            $HTML .= "</table>";

            $HTML .= "</form>";

        } else {
            $HTML .= "Нет данных";
        }
        $HTML .= "</div>";
        $HTML .= "</div>";

        //Если сотрудник - выводим доп. пункты
        if($worker!=null&&($worker[0]->id!=null)&&($user[0]->main_class==1 || $user[0]->user_type_id==1)) {
            //Контроль доступа сотрудника
            $HTML .= "<div id='worker_access' style='display: none;'>";

            $HTML .= "<b>Доступ к кабинетам</b>";
            $topology = $this->model->getTopologyModel();
            $HTML .= "<table>";
            $HTML .= "<tbody>";
            $HTML .= $this->printWorkerAccessTopologyView(0, $topology,$worker[0]->id);
            $HTML .= "</table>";
            $HTML .= "</tbody>";

            $HTML .= "<div id='worker_departaments'>";
            $HTML .= "<b>Доступ к отделам</b>";
            $HTML .= $this->printWorkerDepartmentsTopologyView($worker[0]->id);
            $HTML .= "</div>";
            $HTML .= "</div>";
        }

        $HTML .= "</div>";

        $HTML .= "</div>";

        //Если сотрудник - выводим доп. пункты
        if($worker!=null&&($worker[0]->id!=null)&&($user[0]->main_class==1 || $user[0]->user_type_id==1)) {
            //Контроль доступа сотрудника
            $HTML .= "<div id='worker_access' style='display: none;'>";
            $HTML .= "<b>Категория доступа</b>";
            $topology = $this->model->getTopologyModel();
            $HTML .= $this->printWorkerAccessTopologyView(0, $topology,$worker[0]->id);
            $HTML .= "<div id='worker_departaments'>";
            $HTML .= "<b>Доступ к отделам</b>";
            $HTML .= $this->printWorkerDepartmentsTopologyView($worker[0]->id);
            $HTML .= "</div>";
            $HTML .= "</div>";
        }

        return $HTML;
    }


    /*
    Вывод топологии для формирования категории доступа сотрудника
    */

    public function printWorkerAccessTopologyView($level=0, $topology=null,$worker_id=null)
    {
        $HTML = "";
        //$HTML .= "<table>";
        //$HTML .= "<tbody>";
        for($i=0;$i<count($topology);$i++)
        {
            if(!$topology[$i]->room) {
                //$HTML .= "<div id='main_category_{$topology[$i]->id}'><tr><td>";
				$HTML .= "<tr><td>";
                for($j=0;$j<$level;$j++)
                    $HTML .= "-";
                $HTML .= "{$topology[$i]->name}";
                //select departaments
                $sql = "SELECT * FROM filial_departament WHERE id IN
                        (SELECT parent_id FROM filial_departament WHERE id IN
                        (SELECT DISTINCT department_id FROM filial_rooms WHERE parent_id={$topology[$i]->id} AND room IS TRUE))";
                $departaments = $this->model->sendQuery($sql);
                for ($departaments_i=0;$departaments_i<count($departaments);$departaments_i++)
                {
                    $HTML .= "/{$departaments[$departaments_i]->name}</td><td></td><td></td>";
                }
				//$HTML .= "</div></tr>";
				$HTML .= "</tr>";
                //select rooms
                $sql = "SELECT filial_rooms.*, filial_departament.name AS dep_name FROM filial_rooms
                        LEFT JOIN filial_departament ON filial_departament.id=filial_rooms.department_id
                        WHERE filial_rooms.parent_id={$topology[$i]->id} AND room IS TRUE";
                $rooms = $this->model->sendQuery($sql);
                for ($rooms_i = 0; $rooms_i < count($rooms); $rooms_i++) {
                    //$HTML .= "<tr><td><div id='room_{$rooms[$rooms_i]->id}'>";
					$HTML .= "<tr><td>";
                    $HTML .= "Кабинет: {$rooms[$rooms_i]->name}/{$rooms[$rooms_i]->dep_name}</td>";
                    $sql = "SELECT * FROM workers_permissions_access WHERE worker_id='{$worker_id}' AND room_id='{$rooms[$rooms_i]->id}'";
                    $worker_access = $this->model->sendQuery($sql);
                    $HTML .= "<td><input type='checkbox' name='worker_category_{$rooms[$rooms_i]->id}'";
                    if ($worker_access!=null&&$worker_access[0]->id!=null&&$worker_access[0]->status) $HTML .=" checked ";
                    $HTML .= "onchange=\"sendAjax('/workers/access/category/{$worker_id}/{$rooms[$rooms_i]->id}/'+this.checked+'/', 'GET');\">Доступ</td>";
                    $HTML .= "<td><input type='checkbox' name='worker_security_{$rooms[$rooms_i]->id}'";
                    if ($worker_access!=null&&$worker_access[0]->id!=null&&$worker_access[0]->security_mode) $HTML .=" checked ";
                    $HTML .= "onchange=\"sendAjax('/workers/access/security/{$worker_id}/{$rooms[$rooms_i]->id}/'+this.checked+'/', 'GET');\">Установить/снять с охраны";
                   //$HTML .= "</td></tr></div>";
				   $HTML .= "</td></tr>";
                }
                if($topology[$i]->sub!=null) {
                    $HTML .= $this->printWorkerAccessTopologyView(($level+1),$topology[$i]->sub,$worker_id);
                }

            }
            if($topology[$i]->room&&$level==0)
            {
                $sql = "SELECT filial_rooms.*, filial_departament.name AS dep_name FROM filial_rooms
                        LEFT JOIN filial_departament ON filial_departament.id=filial_rooms.department_id
                        WHERE filial_rooms.id={$topology[$i]->id}";
                $rooms = $this->model->sendQuery($sql);
                //$HTML .= "<div id='room_{$rooms[0]->id}'>";
                $HTML .= "<tr><td>Кабинет: {$rooms[0]->name}/{$rooms[0]->dep_name}</td>";
                $sql = "SELECT * FROM workers_permissions_access WHERE worker_id='{$worker_id}' AND room_id='{$rooms[0]->id}'";
                $worker_access =  $this->model->sendQuery($sql);
                $HTML .= "<td><input type='checkbox' name='worker_category_{$rooms[0]->id}'";
                if ($worker_access!=null&&$worker_access[0]->id!=null&&$worker_access[0]->status) $HTML .=" checked ";
                $HTML .= "onchange=\"sendAjax('/workers/access/category/{$worker_id}/{$rooms[0]->id}/'+this.checked+'/', 'GET');\">Доступ</td>";
                $HTML .= "<td><input type='checkbox' name='worker_security_{$rooms[0]->id}'";
                if ($worker_access!=null&&$worker_access[0]->id!=null&&$worker_access[0]->security_mode) $HTML .=" checked ";
                $HTML .= "onchange=\"sendAjax('/workers/access/security/{$worker_id}/{$rooms[0]->id}/'+this.checked+'/', 'GET');\">Установить/снять с охраны</td></tr>";
                //$HTML .= "</div>";
            }
        }
        //$HTML .= "</tbody>";
        //$HTML .= "</table>";
        return $HTML;
    }


    /*
    Вывод топологии для формирования доступов к отделам
    */

    public function printWorkerDepartmentsTopologyView($worker_id=null)
    {
        $HTML = "";
        $HTML .= "<table>";
        $HTML .= "<tbody>";
        $HTML .= "<tr>";
        $HTML .= "<td>ID</td>";
        $HTML .= "<td>Название</td>";
        $HTML .= "<td>Доступ</td>";
        $HTML .= "</tr>";
        $sql = "SELECT * FROM filial_departament WHERE filial_departament.group IS NOT TRUE";
        $departaments = $this->model->sendQuery($sql);
        for($departaments_i=0;$departaments_i<count($departaments);$departaments_i++)
        {
            $HTML .= "<tr>";
            $HTML .= "<td>{$departaments[$departaments_i]->id}</td>";
            $HTML .= "<td colspan='2'>Департамент: {$departaments[$departaments_i]->name}</td>";
            $HTML .= "</tr>";

            $sql = "SELECT * FROM filial_departament WHERE parent_id={$departaments[$departaments_i]->id}";
            $sections = $this->model->sendQuery($sql);
            for($section_i=0;$section_i<count($sections);$section_i++)
            {
                $sql = "SELECT * FROM workers_departamet_access WHERE worker_id='{$worker_id}' AND departament_id='{$sections[$section_i]->id}'";
                $worker_access = $this->model->sendQuery($sql);

                $HTML .= "<tr>";
                $HTML .= "<td>{$sections[$section_i]->id}</td>";
                $HTML .= "<td>Отдел: {$sections[$section_i]->name}</td>";
                $HTML .= "<td>";
                $HTML .= "<input type='checkbox' name='worker_access_{$sections[$section_i]->id}'";
                if ($worker_access!=null&&$worker_access[0]->id!=null&&$worker_access[0]->status) $HTML .=" checked ";
                $HTML .= "onchange=\"sendAjax('/workerdepartmnetaccess/{$worker_id}/{$sections[$section_i]->id}/'+this.checked+'/', 'GET');\">Доступ";
                $HTML .= "</td>";
                $HTML .= "</tr>";
            }
        }
        $HTML .= "</tbody>";
        $HTML .= "</table>";
        return $HTML;
    }


    /*
     * Вывод увеличенной таблицы входов/выходов
     * Return: HTML
     */
    public function showBigUserPassView($type=null,$filter=null)
    {
        $HTML="";
        if ($type=='in') $HTML.= $this->inMainView($filter);
        if ($type=='out') $HTML.=$this->outMainView($filter);

        return $HTML;
    }

    /*
     * Страница - Главная
     ** TODO перенес MainController
     */
    function mainView($filterIn = null, $filterOut = null)
    {
        $HTML = "";
        $HTML .= "<div id='tableInContent'>";
        $HTML.=$this->inMainView($filterIn);
        $HTML .= "</div>";
        $HTML .= "<div id='tableOutContent'>";
        $HTML.=$this->outMainView($filterOut);
        $HTML .= "</div>";
        return $HTML;
    }

    /**
     * @param null $filterIn
     * @return string
     */
    function inMainView($filterIn=null)
    {
        $passIn = $this->model->getUserPassInOut($filterIn, 'in');
        $type='in';
        $marksModel = $this->model->getMarksModel();
        $HTML = "";

        $HTML .= "<div class='buttonsControl'>";
        //Таблица входов
        $HTML .= "<h2 class='inline'>Вход</h2>";
        $HTML .= "<div class='button white button__zoom-in' title='Увеличить'><img class='bigIcon' src='".base_path()."images/icons/zoom-in.jpg'></div>";
        $HTML .= "<div class='button white button__zoom-out' title='Уменьшить'><img class='bigIcon' src='".base_path()."images/icons/zoom-out.jpg'></div>";
        $HTML .= "<div class='button white button__full-screen' title='Развернуть на весь экран' onclick=\"toggleClass('#tableInContent', 'active');\"><img class='bigIcon' src='".base_path()."images/icons/full-view.jpg'></div>";
        $HTML .= "<div class='filexit'>";
        $HTML .= "<div class='button filtr' onclick=\"sendAjax('/filter/main/in/', 'GET');event.stopPropagation();\">Фильтр</div>";
        $HTML .= "<div class='button white' title='Сброс фильтра' onclick=\"sendAjax('/filter/filtered/in/', 'POST', 'filterform');event.stopPropagation();\"><img class='bigIcon' src='".base_path()."images/icons/close.jpg'></div>";
        $HTML .= "</div>";
        $HTML .= "</div>";

        $HTML .= "<div class='button white' id='tableInRollUpButton' title='Свернуть в стандартный режим' onclick=\"toggleClass('#tableInContent', 'active');\"><img class='bigIcon' src='".base_path()."images/icons/standart-view.jpg'></div>";

        $HTML .= "<div class='overfl'>";
        $HTML .= "<table id='tableIn' border='1' cellpadding='3'>";
        if($passIn!=null&&$passIn[0]->id==null) {
            $HTML .= "Нет данных";
        } else {
            $HTML .= "<thead>";
                $HTML .= "<tr>";
                $HTML .= "<th>Дата</th>";
                $HTML .= "<th>Время</th>";
                $HTML .= "<th>Фото</th>";
                $HTML .= "<th>ФИО</th>";
                $HTML .= "<th>Статус</th>";
                $HTML .= "<th>Департ., отдел</th>";
                $HTML .= "<th>Напр-е</th>";
                $HTML .= "<th>Метка</th>";
                $HTML .= "</tr>";
            $HTML .= "</thead>";

            $HTML .= "<tbody>";
            for($i=0;$i<count($passIn);$i++) {
                $passIn[$i]->time_in = explode(".", $passIn[$i]->time_in);
                $passIn[$i]->time_in = $passIn[$i]->time_in[0];
                $class = ($i==0) ? "class='tableInTrActive'" : "";
                $HTML .= "<tr {$class} onclick=\"sendAjax('/bigview/table-in/{$passIn[$i]->user_id}/', 'GET'); divMakeActive(this, 'tableInTr', 'tableInTrActive'); \">";
                $date = new DateTime($passIn[$i]->date_in);
				$HTML .= "<td class='robotocr'>{$date->Format('d.m.Y')}</td>";
                $HTML .= "<td class='robotocr'>{$passIn[$i]->time_in}</td>";


                $HTML .= "<td class='robotocr'><img src='" . GetImageURL($passIn[$i]->user_photo, 'user_photo') . "' width='30'/></td>";
                //if ($passIn[$i]->user_photo!=null)
                //$HTML .= "<td class='robotocr'><img src='{$passIn[$i]->user_photo}'/></td>";
                //else
                //$HTML .= "<td>{$passIn[$i]->user_photo}<img src='".base_path()."images/icons/chelovek2.PNG' class='bigIcon' \"></td>";
                $HTML .= "<td class='ralewayreg'><a href='".base_path()."account/{$passIn[$i]->user_id}/'>{$passIn[$i]->surname} {$passIn[$i]->first_name} {$passIn[$i]->patronymic}</a></td>";
                $HTML .= "<td class='ralewayreg'>{$passIn[$i]->user_type_name}</td>";
                $HTML .= "<td class='ralewayreg'>{$passIn[$i]->user_departament_name}</td>";
                $HTML .= "<td class='ralewayreg'>{$passIn[$i]->user_room_name}</td>";

                $HTML .= "<td class='ralewaymed red'>";
                if ($passIn[$i]->mark_name!=null) $HTML .= "{$passIn[$i]->mark_name}";
                else
                {
                    $HTML .= "<select name='mark' onchange=\"sendAjax('/usermark/main/{$passIn[$i]->id}/'+this.value+'/', 'GET');\" onclick=\"event.stopPropagation();\">";
                    $HTML .= "<option value='0'>Не выбрана метка</option>";
                    for($j=0;$j<count($marksModel); $j++)
                    {
                        $HTML .= "<option value='{$marksModel[$j]->id}'>{$marksModel[$j]->name}</option>";
                    }
                    $HTML .= "</select>";
                }
                $HTML .= "</td>";
                $HTML .= "</tr>";
            }
            $HTML .= "<tbody>";
        }
        $HTML .= "</table>";
        $HTML .= "</div>";
        $HTML .= "<div id='tableInUserInfo'>";
        if(count($passIn)>0) {
            $HTML .= $this->showInBigUserPassView($passIn[0]->user_id);
        }
        $HTML .= "</div>";
        $HTML .= "</div>";
        $HTML .= "<script>$('#tableIn').stickyTableHeaders({ scrollableArea: $('.overfl')[0], 'fixedOffset': 2 });</script>";
        return $HTML;
    }

    /*
     * Вывод блока отображения выбранной записи таблицы входов
     * Return: HTML
     */
    public function showInBigUserPassView($user_id)
    {
        $type = "in";
        $userinfo=$this->model->getUserPassInOut(null, $type,$user_id);
        $HTML = "";
        $HTML .= "<div class='obchinfo'>";
        $HTML .= "<div class='borderpic'>";
        $HTML .= "<img src='" . GetImageURL($userinfo[0]->user_photo, 'user_photo') . "' class='bigIcon'/>";
        $HTML .= "</div>";

        $HTML .= "<div class='obkart'>";
        if (!empty($userinfo[0]->{'x-ray'})) {
            $HTML .= "<div class='reng'>";
            $HTML .= "<img src='" . base_path() . "images/icons/rentgen.PNG' class='bigIcon' \">";
            $HTML .= "</br>Рентген</div>";
        }
        if (!empty($userinfo[0]->metal_detector)) {
            $HTML .= "<div class='lotok'>";
            $HTML .= "<img src='" . base_path() . "images/icons/lotok.PNG' class='bigIcon' \">";
            $HTML .= "</br>Лоток</br> металлодетектора</div>";
        }
        $HTML .= "</div>";

        $userinfo[0]->time_in = explode(".", $userinfo[0]->time_in);
        $userinfo[0]->time_in = $userinfo[0]->time_in[0];

        $HTML .= "<div class='tableniz'><table class='tableinfo'><tbody><tr>";
        $HTML .= "<td class='ralewaymed bordertd'>Дата/время прохода</td>";
		$date = new DateTime($userinfo[0]->date_in);
        $HTML .= "<td class='robotocr bordertd'>{$date->Format('d.m.Y')}/{$userinfo[0]->time_in}</td>";
        $HTML .= "</tr>";
        $HTML .= "<tr class='ralewayreg bordertd'>";
        $HTML .= "<td class='ralewaymed bordertd'>Фамилия</td>";
        $HTML .= "<td class='ralewaybold bordertd'>{$userinfo[0]->surname}</td>";
        $HTML .= "</tr>";
        $HTML .= "<tr>";
        $HTML .= "<td class='ralewaymed bordertd'>Имя</td>";
        $HTML .= "<td class='ralewaybold bordertd'>{$userinfo[0]->first_name}</td>";
        $HTML .= "</tr>";
        $HTML .= "<tr>";
        $HTML .= "<td class='ralewaymed bordertd'>Отчество</td>";
        $HTML .= "<td class='ralewaybold bordertd'>{$userinfo[0]->patronymic}</td>";
        $HTML .= "</tr>";
        $HTML .= "<tr>";
        $HTML .= "<td class='ralewaymed bordertd'>Дата рождения</td>";
		$date = new DateTime($userinfo[0]->birthday);
        $HTML .= "<td class='robotocr bordertd'>{$date->Format('d.m.Y')}</td>";
        $HTML .= "</tr>";
        $HTML .= "<tr>";
        $HTML .= "<td class='ralewaymed bordertd'>Напр-е</td>";
        $HTML .= "<td class='ralewaymed bordertd'>{$userinfo[0]->user_room_name}</td>";
        $HTML .= "</tr>";
        /*$HTML .= "<tr>";
        $HTML .= "<td class='ralewaymed bordertd'>Категория доступа</td>";
        $HTML .= "<td class='ralewaymed bordertd'>А</td>";
        $HTML .= "</tr>";*/
        $HTML .= "<tr>";
        $HTML .= "<td class='ralewaymed bordertd'>Доступно</td>";
        $HTML .= "<td class='ralewaymed bordertd'>Доступно</td>";
        $HTML .= "</tr>";
        $HTML .= "<tr>";
        $HTML .= "<td class='ralewaymed bordertd'>Статус</td>";
        $HTML .= "<td class='robotocr bordertd'>{$userinfo[0]->user_type_name}</td>";
        $HTML .= "</tr>";
        $HTML .= "<tr>";
        $HTML .= "<td class='ralewaymed bordertd'>Доступное время для входа</td>";
        $HTML .= "<td class='robotocr bordertd'>c 8:00 по 18:00</td>";
        $HTML .= "</tr>";
        $HTML .= "<tr>";
        $HTML .= "<td class='ralewaymed bordertd'>Доступное время для выхода</td>";
        $HTML .= "<td class='robotocr bordertd'>c 8:00 по 18:00</td>";
        $HTML .= "</tr>";
        $HTML .= "</tbody></table></div>";
        $HTML .= "<div class='knopki'>";
        $HTML .= "<div class='button__container--50'><div class='button otstup margins button__personal-card--main'><a href='".base_path()."account/{$user_id}/' style='color: white;'>Личная карточка</a></div></div>";
        $HTML .= "<div class='button__container--50'><div class='button gray margins button__silent-alarm--main'>Тихая тревога</div></div>";
        $HTML .= "</div>";
        $HTML .= "</div>";

        return $HTML;
    }

    function outMainView($filterOut=null)
    {
        $passOut = $this->model->getUserPassInOut($filterOut, 'out');
        $type='out';
        $marksModel = $this->model->getMarksModel();
        $HTML ="";
        $HTML .= "<div class='buttonsControl'>";
        $HTML .= "<h2 class='inline'>Выход</h2>";
        $HTML .= "<div class='button white button__zoom-in' title='Увеличить'><img class='bigIcon' src='".base_path()."images/icons/zoom-in.jpg'></div>";
        $HTML .= "<div class='button white button__zoom-out' title='Уменьшить'><img class='bigIcon' src='".base_path()."images/icons/zoom-out.jpg'></div>";
        $HTML .= "<div class='button white button__full-screen' title='Развернуть на весь экран' onclick=\"toggleClass('#tableOutContent', 'active');\"><img class='bigIcon' src='".base_path()."images/icons/full-view.jpg'></div>";
        $HTML .= "<div class='filexit'>";
        $HTML .= "<div class='button filtr' onclick=\"sendAjax('/filter/main/out/', 'GET');event.stopPropagation();\">Фильтр</div>";
        $HTML .= "<div class='button white' title='Сброс фильтра' onclick=\"sendAjax('/filter/filtered/out/', 'POST', 'filterform');event.stopPropagation();\"><img class='bigIcon' src='".base_path()."images/icons/close.jpg'></div>";
        $HTML .= "</div>";
        $HTML .= "</div>";

        $HTML .= "<div class='button white' id='tableOutRollUpButton' title='Свернуть в стандартный режим' onclick=\"toggleClass('#tableOutContent', 'active');\"><img class='bigIcon' src='".base_path()."images/icons/standart-view.jpg'></div>";

        $HTML .= "<div class='overfl'>";
        $HTML .= "<table id='tableOut' border='1' cellpadding='3'>";
        if($passOut[0]->id==null) {
            $HTML .= "Нет данных";
        } else {
            $HTML .= "<thead>";
            $HTML .= "<tr>";
            $HTML .= "<th>Дата</th>";
            $HTML .= "<th>Время</th>";
            $HTML .= "<th>Фото</th>";
            $HTML .= "<th>ФИО</th>";
            $HTML .= "<th>Статус</th>";
            $HTML .= "<th>Департ., отдел</th>";
            $HTML .= "<th>Напр-е</th>";
            $HTML .= "<th>Метка</th>";
            $HTML .= "</tr>";
            $HTML .= "</thead>";

            $HTML .= "<tbody>";
            for ($i = 0; $i < count($passOut); $i++) {
                $passOut[$i]->time_out = explode(".", $passOut[$i]->time_out);
                $passOut[$i]->time_out = $passOut[$i]->time_out[0];
                $class = ($i==0) ? "class='tableOutTrActive'" : "";
                $HTML .= "<tr {$class} onclick=\"sendAjax('/bigview/table-out/{$passOut[$i]->user_id}/', 'GET'); divMakeActive(this, 'tableOutTr', 'tableOutTrActive');\">";
                $date = new DateTime($passOut[$i]->date_out);
				$HTML .= "<td class='robotocr'>{$date->Format('d.m.Y')}</td>";
                $HTML .= "<td class='robotocr'>{$passOut[$i]->time_out}</td>";
                $HTML .= "<td class='robotocr'><img src='" . GetImageURL($passOut[$i]->user_photo, 'user_photo') . "' width='30'/></td>";
                $HTML .= "<td class='ralewayreg'><a href='".base_path()."account/{$passOut[$i]->user_id}/'>{$passOut[$i]->surname} {$passOut[$i]->first_name} {$passOut[$i]->patronymic}</a></td>";
                $HTML .= "<td class='ralewayreg'>{$passOut[$i]->user_type_name}</td>";
                $HTML .= "<td class='ralewayreg'>{$passOut[$i]->user_departament_name}</td>";
                $HTML .= "<td class='ralewayreg'>{$passOut[$i]->user_room_name}</td>";
                $HTML .= "<td class='ralewaymed red'>";
                if ($passOut[$i]->mark_name!=null) $HTML .= "{$passOut[$i]->mark_name}";
                else
                {
                    $HTML .= "<select class='ralewaymed' name='mark' onchange=\"sendAjax('/usermark/main/{$passOut[$i]->id}/'+this.value+'/', 'GET');\"
					onclick=\"event.stopPropagation();\">";
                    $HTML .= "<option value='0'>Не выбрана метка</option>";
                    for($j=0;$j<count($marksModel); $j++)
                    {
                        $HTML .= "<option value='{$marksModel[$j]->id}'>{$marksModel[$j]->name}</option>";
                    }
                    $HTML .= "</select>";
                }
                $HTML .= "</td>";
                $HTML .= "</tr>";
            }
            $HTML .= "</tbody>";
        }
        $HTML .= "</table>";
        $HTML .= "</div>";
        $HTML .= "<div id='tableOutUserInfo'>";
        if(count($passOut)>0) {
            $HTML .= $this->showOutBigUserPassView($passOut[0]->user_id);
        }
        $HTML .= "</div>";
        $HTML .= "</div>";
        $HTML .= "<script>$('#tableOut').stickyTableHeaders({ scrollableArea: $('.overfl')[0], 'fixedOffset': 2 });</script>";
        return $HTML;
    }

    /*
     * Вывод блока отображения выбранной записи таблицы выходов
     * Return: HTML
     */
    public function showOutBigUserPassView($user_id)
    {
        $type = "in";
        $userinfo=$this->model->getUserPassInOut(null, $type,$user_id);
        $HTML='';

        $HTML .= "<div class='obchinfo'>";
        $HTML .= "<div class='borderpic'>";
        $HTML .= "<img src='" . GetImageURL($userinfo[0]->user_photo, 'user_photo') . "' class='bigIcon'/>";
        $HTML .= "</div>";

        $HTML .= "<div class='obkart'>";
        if (!empty($userinfo[0]->{'x-ray'})) {
            $HTML .= "<div class='reng'>";
            $HTML .= "<img src='" . base_path() . "images/icons/rentgen.PNG' class='bigIcon' \">";
            $HTML .= "</br>Рентген</div>";
        }
        if (!empty($userinfo[0]->metal_detector)) {
            $HTML .= "<div class='lotok'>";
            $HTML .= "<img src='" . base_path() . "images/icons/lotok.PNG' class='bigIcon' \">";
            $HTML .= "</br>Лоток</br> металлодетектора</div>";
        }
        $HTML .= "</div>";

        $userinfo[0]->time_in = explode(".", $userinfo[0]->time_in);
        $userinfo[0]->time_in = $userinfo[0]->time_in[0];

        $HTML .= "<div class='tableniz'><table class='tableinfo'><tbody><tr>";
        $HTML .= "<td class='ralewaymed bordertd'>Дата/время прохода</td>";
		$date = new DateTime($userinfo[0]->date_in);
        $HTML .= "<td class='robotocr bordertd'>{$date->Format('d.m.Y')}/{$userinfo[0]->time_in}</td>";
        $HTML .= "</tr>";
        $HTML .= "<tr class='ralewayreg bordertd'>";
        $HTML .= "<td class='ralewaymed bordertd'>Фамилия</td>";
        $HTML .= "<td class='ralewaybold bordertd'>{$userinfo[0]->surname}</td>";
        $HTML .= "</tr>";
        $HTML .= "<tr>";
        $HTML .= "<td class='ralewaymed bordertd'>Имя</td>";
        $HTML .= "<td class='ralewaybold bordertd'>{$userinfo[0]->first_name}</td>";
        $HTML .= "</tr>";
        $HTML .= "<tr>";
        $HTML .= "<td class='ralewaymed bordertd'>Отчество</td>";
        $HTML .= "<td class='ralewaybold bordertd'>{$userinfo[0]->patronymic}</td>";
        $HTML .= "</tr>";
        $HTML .= "<tr>";
        $HTML .= "<td class='ralewaymed bordertd'>Дата рождения</td>";
		$date = new DateTime($userinfo[0]->birthday);
        $HTML .= "<td class='robotocr bordertd'>{$date->Format('d.m.Y')}</td>";
        $HTML .= "</tr>";
        $HTML .= "<tr>";
        $HTML .= "<td class='ralewaymed bordertd'>Напр-е</td>";
        $HTML .= "<td class='ralewaymed bordertd'>{$userinfo[0]->user_room_name}</td>";
        $HTML .= "</tr>";
        /*$HTML .= "<tr>";
        $HTML .= "<td class='ralewaymed bordertd'>Категория доступа</td>";
        $HTML .= "<td class='ralewaymed bordertd'>А</td>";
        $HTML .= "</tr>";*/
        $HTML .= "<tr>";
        $HTML .= "<td class='ralewaymed bordertd'>Доступно</td>";
        $HTML .= "<td class='ralewaymed bordertd'>Доступно</td>";
        $HTML .= "</tr>";
        $HTML .= "<tr>";
        $HTML .= "<td class='ralewaymed bordertd'>Статус</td>";
        $HTML .= "<td class='robotocr bordertd'>{$userinfo[0]->user_type_name}</td>";
        $HTML .= "</tr>";
        $HTML .= "<tr>";
        $HTML .= "<td class='ralewaymed bordertd'>Доступное время для входа</td>";
        $HTML .= "<td class='robotocr bordertd'>c 8:00 по 18:00</td>";
        $HTML .= "</tr>";
        $HTML .= "<tr>";
        $HTML .= "<td class='ralewaymed bordertd'>Доступное время для выхода</td>";
        $HTML .= "<td class='robotocr bordertd'>c 8:00 по 18:00</td>";
        $HTML .= "</tr>";
        $HTML .= "</tbody></table></div>";
        $HTML .= "<div class='knopki'>";
        $HTML .= "<div class='button__container--50'><div class='button otstup margins button__personal-card--main'><a href='".base_path()."account/{$userinfo[0]->user_id}/' style='color: white;'>Личная карточка</a></div></div>";




        $HTML .= "</div>";
        return $HTML;
    }

    //Статистика

    public function statisticView()
    {
        $HTML = "";
        $HTML .= "<div class='buttonsControl nomargin'>";
        $HTML .= "<div class='tabButton active' onClick=\"showHideDivs(this,'NotificationsTab,ManualTurnstileTab,WaitingUsersTab', 'NotificationsTab', 'tabButton active');\">Уведомления</div>";
        $HTML .= "<div class='tabButton' onClick=\"showHideDivs(this,'NotificationsTab,ManualTurnstileTab,WaitingUsersTab', 'ManualTurnstileTab', 'tabButton active');\">Ручное управление турникетом</div>";
        $HTML .= "<div class='tabButton' onClick=\"showHideDivs(this,'NotificationsTab,ManualTurnstileTab,WaitingUsersTab', 'WaitingUsersTab', 'tabButton active');\">Ожидаемые посетители</div>";
        $HTML .= "</div>";

        $HTML .= "<div id='AllNotifications'>";
        $HTML .= "<div id='NotificationsTab'>";
        $HTML.="<h1><input type='checkbox' name='allnotificationscheck' checked> Все уведомления</h1></br></br>";
        $HTML .= "Дата<input class='robotocr margins' type='date'>";
        $HTML .= "Период с <input class='robotocr margins' type='date'>";
        $HTML .= "по<input class='robotocr margins' type='date'>";
        $HTML .= "<br>";
        $HTML .= "Время с<input class='robotocr margins' type='time'>";
        $HTML .= "по <input class='robotocr margins' type='time'>";
        $HTML .= "<br><input type='button' class='button margins bornone' value='Сформировать'>";

        $HTML .= "<div class='statists'><br><table class='akkt' border='1' cellpadding='5'>";
        $HTML .= "<tr>";
        $HTML .= "<th>№</th>";
        $HTML .= "<th>Дата/время</th>";
        $HTML .= "<th>Источник</th>";
        $HTML .= "<th>Посетитель</th>";
        $HTML .= "<th>Ответ</th>";
        $HTML .= "<th>Причина</th>";
        $HTML .= "<th>Запись</th>";
        $HTML .= "</tr>";
        $HTML .= "</table>";
        $HTML .= "</div>";
        $HTML .= "</div>";

        $HTML .= "<div id='ManualTurnstileTab' style='display: none'>";
        $HTML .= "<input type='button' class='button margins bornone' value='Пропустить'>";
        $HTML .= "<input type='button' class='button margins bornone gray' value='Выпустить'>";
        $HTML .= "</div>";

        $HTML .= "<div id='WaitingUsersTab' style='display: none'>";
        $HTML .= "Ожидаемые посетители";
        $HTML .= "</div>";
        $HTML .= "</div>";

        return $HTML;
    }



    /*
     * Вывод топологии
     * Return: HTML
     */
    public function showTopologyView()
    {
        $HTML = "";
        $HTML .= "<div class='buttonsControl'>";
        $HTML .= "<input type='button' class='button bornone' value='Добавить группу' onclick=\"sendAjax('/topology/add/form/', 'GET')\">";
        $HTML .= "<form name='topologySearchForm' id='topologySearchForm' onsubmit=\"sendAjax('/topology/0/search/', 'POST', 'topologySearchForm'); return false;\">";
        $HTML .= "<input class='poisktopology' name='name' type='text' placeholder='департамент/отдел/кабинет/график'>";
        $HTML .= "<button class='button'>Поиск</button>";
        $HTML .= "</form>";
        $HTML .= "</div>";

        $HTML .= "<div id='topologynavigation' class='userData'>";
        $HTML .= "<div id='leftopology'>";
        $top=$this->model->getTopologyModel();
        $HTML .= $this->printTopologyView(0,$top);
        $HTML .= "</div>";
        $HTML .= "</div>";

        $HTML .= "<div id='selectedtopologygroup' class='userData'></div>";
        return $HTML;
    }

    /*
     * Вывод вложенной топологии
     * Return: HTML
     */
    public function showInsideTopologyView($parent_id = 0, $type, $category = null)
    {
        $HTML = "";

        $HTML .= "<h2> Группа:";
        if($category=='null') {
            $sql = "SELECT * FROM filial_rooms WHERE id={$parent_id}";
            $group = $this->model->sendQuery($sql);
            $HTML .= $group[0]->name;
        } else {
            $topology = $this->model->getTopologyModel(0, $category, $parent_id);
            if(isset($topology[0]->departament->main_departament))
                $HTML .= $topology[0]->departament->main_departament->name;
            else $HTML .= $topology[0]->name;
        }
        $HTML .= "</h2>";


        $HTML .= "<table class='akkt' border='1' cellpadding='5'>";
        $HTML .= "<tr>";
        $HTML .= "<th rowspan='2'></th>";
        $HTML .= "<th rowspan='2'>Название</th>";
        //$HTML .= "<th rowspan='2'>Описание</th>";
        $HTML .= "<th rowspan='2'>Путь</th>";
        $HTML .= "<th colspan='2'>Отдел</th>";
        $HTML .= "<th colspan='2'>Департамент</th>";
        $HTML .= "<th rowspan='2'>Шагомер вход</th>";
        $HTML .= "<th rowspan='2'>Шагомер выход</th>";
        $HTML .= "</tr>";
        $HTML .= "<tr>";

        $HTML .= "<th>Изображение</th>";
        $HTML .= "<th>Название</th>";
        $HTML .= "<th>Изображение</th>";
        $HTML .= "<th>Название</th>";
        $HTML .= "</tr>";
        if ($type=='department')
        {
            $HTML .= $this->printInsideCategoryTopologyView(0, $category, $parent_id);
            //$HTML .= $this->printInsideDepartmentTopologyView($id,$category);
        } else {
            $HTML .= $this->printInsideCategoryTopologyView(0, $parent_id);
        }
        $HTML .= "</table>";

        return $HTML;
    }

    /*
     * Шаблон вывода Категории/Подкатегории
     */
    public function printInsideCategoryTopologyView($level=0, $parent_id = null, $departament_id = null)
    {
        $HTML = "";
        $topology = null;
        if($departament_id == null) {
            $topology = $this->model->getTopologyModel($level, $parent_id);
        } else if ($parent_id != null && $departament_id != null) {
            $topology = $this->model->getTopologyModel($level, $parent_id, $departament_id);
        }

        for($i=0;$i<count($topology);$i++)
        {
            $previous_departament = null;
            $sublevel = 0;
            if(!$topology[$i]->room) {
                $sublevel++;
                $style = "style='padding-left: ".(20*($level+$sublevel))."px;'";
                $HTML.="<tr>";
                $HTML .= "<td {$style}>";
                $HTML .= "<div {$style}>Подгруппа:<br><b>{$topology[$i]->name}</b></div>";

                $HTML .= "<div class='topology_submenu'>";
                $HTML .= "<div class='topology_menu_icon'></div>";
                $HTML .= "<div class='topology_menu'>";
                $HTML .= "<div id=\"menu_delete\" onclick=\"sendAjax('/topology/{$topology[$i]->id}/delete/floor/', 'POST');\">Удалить категорию</div>";
                $HTML .= "</div>";
                $HTML .= "</div>";

                $HTML .= "</td>";

                $HTML .= "<td class='changeCell' ondblclick=\"alert('Изменение названия категории здания');\">{$topology[$i]->name}</td>"; //Название
                $HTML .= "<td>-</td>"; //Путь
                $HTML .= "<td>-</td>"; //Изображение - Отдел
                $HTML .= "<td>-</td>"; //Название - Отдел
                $HTML .= "<td>-</td>"; //Изображение - Департамент
                $HTML .= "<td>-</td>"; //Название - Департамент
                $HTML .= "<td>-</td>"; //Шагомер - вход
                $HTML .= "<td>-</td>"; //Шагомер - выход
                $HTML.="</tr>";

                if(isset($topology[$i]->departaments)) {
                    for ($j = 0; $j < count($topology[$i]->departaments); $j++) {
                        $sublevel++;
                        $style = "style='padding-left: ".(20*($level+$sublevel))."px;'";
                        $HTML .= "<tr>";
                        $HTML .= "<td>";
                        $HTML .= "<div {$style}>Департамент:<br><b>{$topology[$i]->departaments[$j]->name}</b></div>";

                        $HTML .= "<div class='topology_submenu'>";
                        $HTML .= "<div class='topology_menu_icon'></div>";
                        $HTML .= "<div class='topology_menu'>";
                        $HTML .= "<div id=\"menu_delete\" onclick=\"sendAjax('/topology/{$topology[$i]->id}/{$topology[$i]->departaments[$j]->id}/delete/departament/', 'POST');\">Удалить отдел</div>";
                        $HTML .= "</div>";
                        $HTML .= "</div>";

                        $HTML .= "</td>";
                        $HTML .= "<td class='changeCell' ondblclick=\"alert('Изменение название департамента');\">{$topology[$i]->departaments[$j]->name}</td>"; //Название
                        $HTML .= "<td>-</td>"; //Путь
                        $HTML .= "<td>-</td>"; //Изображение - Отдел
                        $HTML .= "<td>-</td>"; //Название - Отдел
                        $HTML .= "<td class='changeCell' ondblclick=\"alert('Изменение изображения департамента');\"><img src='{$topology[$i]->departaments[$j]->image}'></td>"; //Изображение - Департамент
                        $HTML .= "<td class='changeCell' ondblclick=\"alert('Изменение названия департамента');\">{$topology[$i]->departaments[$j]->name}</td>"; //Название - Департамент
                        $HTML .= "<td>-</td>"; //Шагомер - вход
                        $HTML .= "<td>-</td>"; //Шагомер - выход
                        $HTML .= "</tr>";
                        if(!$topology[$i]->departaments[$j]->group && isset($topology[$i]->departaments[$j]->sub)) {
                            for($k = 0; $k < count($topology[$i]->departaments[$j]->sub); $k++) {
                                $sublevel++;
                                $style = "style='padding-left: ".(20*($level+$sublevel))."px;'";
                                $HTML .= "<tr>";
                                $HTML .= "<td>";
                                $HTML .= "<div {$style}>Отдел:<br><b>{$topology[$i]->departaments[$j]->sub[$k]->name}</b></div>";

                                $HTML .= "<div class='topology_submenu'>";
                                $HTML .= "<div class='topology_menu_icon'></div>";
                                $HTML .= "<div class='topology_menu'>";
                                $HTML .= "<div id=\"menu_delete\" onclick=\"sendAjax('/topology/{$topology[$i]->id}/{$topology[$i]->departaments[$j]->sub[$k]->id}/delete/departament/', 'POST');\">Удалить отдел</div>";
                                $HTML .= "</div>";
                                $HTML .= "</div>";

                                $HTML .= "</td>";
                                $HTML .= "<td class='changeCell' ondblclick=\"alert('Изменение название отдела');\">{$topology[$i]->departaments[$j]->sub[$k]->name}</td>"; //Название
                                $HTML .= "<td>-</td>"; //Путь
                                $HTML .= "<td class='changeCell' ondblclick=\"alert('Изменение изображения отдела');\"><img src='{$topology[$i]->departaments[$j]->sub[$k]->image}'></td>"; //Изображение - Отдел
                                $HTML .= "<td class='changeCell' ondblclick=\"alert('Изменение название отдела');\">{$topology[$i]->departaments[$j]->sub[$k]->name}</td>"; //Название - Отдел
                                $HTML .= "<td>{$topology[$i]->departaments[$j]->image}</td>"; //Изображение - Департамент
                                $HTML .= "<td>{$topology[$i]->departaments[$j]->name}</td>"; //Название - Департамент
                                $HTML .= "<td>-</td>"; //Шагомер - вход
                                $HTML .= "<td>-</td>"; //Шагомер - выход
                                $HTML .= "</tr>";
                                if(isset($topology[$i]->departaments[$j]->sub[$k]->rooms)) {
                                    for($r = 0; $r<count($topology[$i]->departaments[$j]->sub[$k]->rooms); $r++) {
                                        $HTML .= $this->printTopologyTableRoom($level, $sublevel, $topology[$i]->id, $topology[$i]->departaments[$j]->sub[$k], $topology[$i]->departaments[$j]->sub[$k]->rooms[$r]);
                                    }
                                }
                                $sublevel--;
                            }
                        } else {
                            if(isset($topology[$i]->departaments[$j]->rooms)) {
                                $sublevel++;
                                for($r = 0; $r<count($topology[$i]->departaments[$j]->rooms); $r++) {
                                    $HTML .= $this->printTopologyTableRoom($level, $sublevel, $topology[$i]->id, $topology[$i]->departaments[$j], $topology[$i]->departaments[$j]->rooms[$r]);
                                }
                                $sublevel--;
                            }
                        }
                        $sublevel--;
                    }
                }

                if(isset($topology[$i]->sub)) {
                    $HTML .= $this->printInsideCategoryTopologyView(($level+1),$topology[$i]->id);
                }
            }
            $sublevel = 0;
        }
        return $HTML;
    }

    public function printTopologyTableRoom($level, $sublevel, $floor_id, $departament, $cabinet)
    {
        $HTML = "";
        //(Строка begin) Вывод отдела & image
        $sublevel++;
        $style = "style='padding-left: " . (20 * ($level + $sublevel)) . "px'";
        $HTML .= "<tr>";
        $HTML .= "<td class='robotocr changeCell' ondblclick=\"alert('Изменить номер кабинета');\">";

        $HTML .= "<div class='topology_submenu'>";
        $HTML .= "<div class='topology_menu_icon'></div>";
        $HTML .= "<div class='topology_menu'>";
        $HTML .= "<div id=\"menu_delete_room_68\" onclick=\"sendAjax('/topology/delete/room/{$floor_id}/{$departament->id}/{$cabinet->id}/', 'POST');\">Удалить кабинет</div>";
        $HTML .= "</div>";
        $HTML .= "</div>";

        $HTML .= "<div {$style}>Кабинет:<br><b>{$cabinet->number}</b></div>";
        $HTML .= "</td>";
        $HTML .= "<td class='robotocr changeCell' ondblclick=\"alert('Изменить название кабинета');\">";
        if($cabinet->workers!=null)
            $HTML .= "<button onclick=\"toggleClass('.topologyTableRoomWorkers{$cabinet->id}.tableHiddenRow', 'show'); switchButtonVal(this);\">+</button>";
        $HTML .= "<b>{$cabinet->name}</b>";
        $HTML .= "</td>";
        $HTML .= "<td>-</td>"; //Путь кабинета
        $HTML .= "<td><img src='{$departament->image}'></td>"; //Изображение отдела
        $HTML .= "<td>{$departament->name}</td>"; //Название отдела

        if (isset($departament->main_departament)) {
            $HTML .= "<td><img src='{$departament->main_departament->image}'></td>"; //Изображение департамента
            $HTML .= "<td>{$departament->main_departament->name}</td>"; //Название департамента
        } else {
            $HTML .= "<td>-</td>"; //Изображение департамента
            $HTML .= "<td>-</td>"; //Название департамента
        }
        $HTML .= "<td class='changeCell' ondblclick=\"alert('Изменить значение шагомера - вход');\">{$cabinet->step_in}</td>"; //Шагомер вход
        $HTML .= "<td class='changeCell' ondblclick=\"alert('Изменить значение шагомера - выход');\">{$cabinet->step_out}</td>"; //Шагомер выход
        $HTML .= "</tr>";


        if($cabinet->workers!=null) {
            $sublevel++;
            for($worker_i = 0; $worker_i < count($cabinet->workers); $worker_i++) {
                $style = "style='padding-left: " . (20 * ($level + $sublevel)) . "px'";
                $HTML .= "<tr class='topologyTableRoomWorkers{$cabinet->id} tableHiddenRow'>";
                $HTML .= "<td>";

                $HTML .= "<div class='topology_submenu'>";
                $HTML .= "<div class='topology_menu_icon'></div>";
                $HTML .= "<div class='topology_menu'>";
                $HTML .= "<div id=\"menu_delete_room_68\" onclick=\"sendAjax('/topology/unlink/worker/{$cabinet->id}/{$cabinet->workers[$worker_i]->id}/', 'POST');\">Отвязать сотрудника</div>";
                $HTML .= "</div>";
                $HTML .= "</div>";

                $HTML .= "<div {$style}>Сотрудник:<br><b>{$cabinet->workers[$worker_i]->surname} {$cabinet->workers[$worker_i]->first_name} {$cabinet->workers[$worker_i]->patronymic}</b></div>";
                $HTML .= "</td>";
                $HTML .= "<td>";
                if($cabinet->workers!=null)
                    $HTML .= "<button onclick=\"toggleClass('.topologyTableRoomWorkerHearing_{$cabinet->workers[$worker_i]->id}.tableHiddenRow', 'show'); switchButtonVal(this);\">+</button>";
                $HTML .= "</td>";
                $HTML .= "<td>-</td>"; // Путь
                $HTML .= "<td>-</td>"; // Отдел - Изображение
                $HTML .= "<td>-</td>"; // Отдел - Название
                $HTML .= "<td>-</td>"; // Департамент - Изображение
                $HTML .= "<td>-</td>"; // Департамент - Название
                $HTML .= "<td>-</td>"; // Шагомер вход
                $HTML .= "<td>-</td>"; // Шагомер выход
                $HTML .= "</tr>";
                if(count($cabinet->workers[$worker_i]->hearing) > 0) {
                    $sublevel++;
                    $style = "style='padding-left: " . (20 * ($level + $sublevel)) . "px'";
                    for ($hearing_i = 0; $hearing_i < count($cabinet->workers[$worker_i]->hearing); $hearing_i++) {
                        $HTML .= "<tr class='topologyTableRoomWorkerHearing_{$cabinet->workers[$worker_i]->id} tableHiddenRow'>";
                        $HTML .= "<td>";
                        $HTML .= "<div {$style}>Услуга: <b>{$cabinet->workers[$worker_i]->hearing[$hearing_i]->name}</b></div>";

                        $HTML .= "<div class='topology_submenu'>";
                        $HTML .= "<div class='topology_menu_icon'></div>";
                        $HTML .= "<div class='topology_menu'>";
                        //$HTML .= "<div id=\"menu_delete_room_68\" onclick=\"sendAjax('/topology/delete/hearing/{$cabinet->workers[$worker_i]->hearing[$hearing_i]->id}/', 'POST');\">Удалить услугу (нужно сделать)</div>";
						$HTML .= "<div id=\"menu_edit_week_template_68\" onclick=\"sendAjax('/workschedule/weektemlateedit/show/{$cabinet->workers[$worker_i]->hearing[$hearing_i]->id}/', 'GET');\">Настроить шаблон</div>";
						$HTML .= "<div id=\"menu_delete_hearing_68\" onclick=\"sendAjax('/topology/add/hearing/deletehearing/{$cabinet->workers[$worker_i]->hearing[$hearing_i]->id}/', 'GET');\">Удалить услугу</div>";
                        $HTML .= "</div>";
                        $HTML .= "</div>";

                        $HTML .= "</td>";
                        $HTML .= "<td class='changeCell' ondblclick=\"alert('Изменить название услуги');\">{$cabinet->workers[$worker_i]->hearing[$hearing_i]->name}</td>";
                        $HTML .= "<td>-</td>"; // Путь
                        $HTML .= "<td>-</td>"; // Отдел - Изображение
                        $HTML .= "<td>-</td>"; // Отдел - Название
                        $HTML .= "<td>-</td>"; // Департамент - Изображение
                        $HTML .= "<td>-</td>"; // Департамент - Название
                        $HTML .= "<td>-</td>"; // Шагомер вход
                        $HTML .= "<td>-</td>"; // Шагомер выход
                        $HTML .= "</tr>";
                    }
                    $sublevel--;
                }
            }
            $sublevel--;
        }
        return $HTML;
    }

    public function printTopologyView($level=0, $topology=null, $type=0, $search=false, $param=null, $value=null, $mainFindSearch=false)
    {
        $shablons = [];
        switch($type) {
            case 0 :
                $shablons['onclickTopologyUrl'] = "sendAjax('/topology/show/{topology_id}/category/null/', 'GET'); event.stopPropagation();";
                $shablons['onclickTopologyEditUrl'] = "sendAjax('/topology/edit/form/{topology_id}/category/', 'GET'); event.stopPropagation();";
                $shablons['onclickDepartamentUrl'] = "sendAjax('/topology/show/{departament_id}/department/{topologry_id}/', 'GET'); event.stopPropagation();";
                $shablons['onclickDepartamentEditUrl'] = "sendAjax('/filial-departments/form/{departament_id}/department/', 'GET'); event.stopPropagation();";
                $shablons['onclickSubDepartamentUrl'] = "sendAjax('/topology/show/{departament_id}/department/{topology_id}/', 'GET'); event.stopPropagation();";
                $shablons['onclickSubDepartamentEditUrl'] = "sendAjax('/filial-departments/form/{departament_id}/section/', 'GET'); event.stopPropagation();";
                $shablons['onclickCabinetUrl'] = '';
                $shablons['onclickCabinetUrlEdit'] = '';
                $shablons['onclickWorkerUrl'] = '';
                $shablons['onclickHearingUrl'] = '';
                $shablons['all'] = 0;

                break;

            case 1 :
                $shablons['onclickTopologyUrl'] = "sendAjax('/workschedule/showbutton/{topology_id}/category/null/', 'GET'); event.stopPropagation();";
                $shablons['onclickTopologyEditUrl'] = "sendAjax('/topology/edit/form/{topology_id}/category/', 'GET'); event.stopPropagation();";
                $shablons['onclickDepartamentUrl'] = "sendAjax('/workschedule/showbutton/{departament_id}/department/{topologry_id}/', 'GET'); event.stopPropagation();";
                $shablons['onclickDepartamentEditUrl'] = "sendAjax('/filial-departments/form/{departament_id}/department/', 'GET'); event.stopPropagation();";
                $shablons['onclickSubDepartamentUrl'] = "sendAjax('/workschedule/showbutton/{departament_id}/section/{topology_id}/', 'GET'); event.stopPropagation();";
                $shablons['onclickSubDepartamentEditUrl'] = "sendAjax('/filial-departments/form/{departament_id}/section/', 'GET'); event.stopPropagation();";
                $shablons['onclickCabinetUrl'] = "sendAjax('/workschedule/showbutton/{room_id}/room/{departament_id}/', 'GET'); event.stopPropagation();";
                $shablons['onclickCabinetUrlEdit'] = "sendAjax('/topology/edit/form/{room_id}/room/', 'GET'); event.stopPropagation();";
                $shablons['onclickWorkerUrl'] = "sendAjax('/workschedule/showbutton/{worker_id}/worker/{departament_id}/', 'GET'); event.stopPropagation();";
                $shablons['onclickHearingUrl'] = "sendAjax('/workschedule/showbutton/{hearing_id}/hearing/{departament_id}/', 'GET'); event.stopPropagation();";
                $shablons['all'] = 1;
                break;

            default:
                $shablons['onclickTopologyUrl'] = '';
                $shablons['onclickTopologyEditUrl'] = '';
                $shablons['onclickDepartamentUrl'] = '';
                $shablons['onclickDepartamentEditUrl'] = '';
                $shablons['onclickSubDepartamentUrl'] = '';
                $shablons['onclickSubDepartamentEditUrl'] = '';
                $shablons['onclickCabinetUrl'] = '';
                $shablons['onclickCabinetUrlEdit'] = '';
                $shablons['onclickWorkerUrl'] = '';
                $shablons['onclickWorkerUrlEdit'] = '';
                $shablons['onclickHearingUrl'] = '';
                $shablons['all'] = 0;
                break;
        }

        $HTML = "";
        if($level==0) $HTML .= "<div id='topologyHiddenForm' class='hiddenFormDiv''></div>";

        if($param==null || $value==null) $search = false;
        for($i=0;$i<count($topology);$i++)
        {
            $subHTML = "";
            if(!$search) $findSearch = true;
            else $findSearch = $mainFindSearch;
            $onclickTopology = str_replace('{topology_id}', $topology[$i]->id, $shablons['onclickTopologyUrl']);
            $onclickTopologyEdit = str_replace('{topology_id}', $topology[$i]->id, $shablons['onclickTopologyEditUrl']);

            $style = ($level == 0 || $search) ? "" : "style='display: none;'";
            $buttonPlusMinus = (($level == 0 && !$search) || !$search) ? "+" : "-";
            if($search && (strpos($topology[$i]->name, $value)!==false || $findSearch)) {
                $findSearch = true;
            }

            $subHTML .= "<div class='topology_item' {$style} id='main_department_{$topology[$i]->id}' onclick=\"activeTopologyItem('#main_department_{$topology[$i]->id}', '.topology_item_name', 'active', {$shablons['all']}); {$onclickTopology}\">";
            $subHTML .= "<div class='topology_item_name'>";
            if(isset($topology[$i]->sub) || isset($topology[$i]->departaments))
                $subHTML .= "<button onclick='divSlide(this, \"#main_department_{$topology[$i]->id}\", \".topology_item\", false); event.stopPropagation();'>{$buttonPlusMinus}</button>";
            $subHTML .= "{$topology[$i]->name}";
            $subHTML .= "<div class='topology_submenu'>";
            $subHTML .= "<div class='topology_menu_icon'></div>";
            $subHTML .= "<div class='topology_menu'>";
            $subHTML .= "<div onclick=\"{$onclickTopologyEdit}\">Редактировать</div>";
            $subHTML .= "<div onclick=\"sendAjax('/topology/{$topology[$i]->id}/get/form/add/subtopology/', 'GET');\">Добавить подкатегорию</div>";
            $subHTML .= "<div onclick=\"sendAjax('/topology/{$topology[$i]->id}/get/form/add/departament/', 'GET');\">Добавить департамент</div>";
            $subHTML .= "<div><a href='" . base_path() . "workschedule/show/{$topology[$i]->id}/category/null/'>График работ</a></div>";
            $subHTML .= "<div onclick=\"sendAjax('/topology/{$topology[$i]->id}/delete/floor/', 'POST');\">Удалить</div>";
            $subHTML .= '</div>';
            $subHTML .= '</div>';
            $subHTML .= '</div>';

            $subHTML .= "<div id='topologyHiddenForm_{$topology[$i]->id}' class='hiddenFormDiv'></div>";

            if($topology[$i]->departaments!=null) {
                $sublevel = 0;
                for ($j = 0; $j < count($topology[$i]->departaments); $j++) {
                    //Проверяем тип сущности

                    if(!$topology[$i]->departaments[$j]->group) {
                        //Департамент
                        //Выодим Департамент/Отдел
                        $onclickDepartament = str_replace('{departament_id}', $topology[$i]->departaments[$j]->id, $shablons['onclickDepartamentUrl']);
                        $onclickDepartament = str_replace('{topologry_id}', $topology[$i]->id, $onclickDepartament);
                        $onclickDepartamentEdit = str_replace('{departament_id}', $topology[$i]->departaments[$j]->id, $shablons['onclickDepartamentEditUrl']);

                        $sublevel++;
                        $style = ($search) ? '' : "style='display: none;'";
                        $buttonPlusMinus = ($search) ? "-" : "+";

                        if($search && (strpos($topology[$i]->departaments[$j]->name, $value)!==false || $findSearch)) {
                            $findSearch = true;
                        }

                        $subHTML .= "<div class='topology_item' {$style} id='department_{$topology[$i]->id}_{$topology[$i]->departaments[$j]->id}' onclick=\"activeTopologyItem('#department_{$topology[$i]->id}_{$topology[$i]->departaments[$j]->id}', '.topology_item_name', 'active', {$shablons['all']}); {$onclickDepartament}\">";
                        $subHTML .= "<div class='topology_item_name'>";

                        if(isset($topology[$i]->departaments[$j]->sub))
                            $subHTML .= "<button onclick='divSlide(this, \"#department_{$topology[$i]->id}_{$topology[$i]->departaments[$j]->id}\", \".topology_item\", false); event.stopPropagation();'>{$buttonPlusMinus }</button>";

                        $subHTML .= "Департамент: {$topology[$i]->departaments[$j]->name}";
                        $subHTML .= "<div class='topology_submenu'>";
                        $subHTML .= "<div class='topology_menu_icon'></div>";
                        $subHTML .= "<div class='topology_menu'>";
                        $subHTML .= "<div id='menu_work_schedule_department_{$topology[$i]->departaments[$j]->id}'>
                    <a href='" . base_path() . "workschedule/show/{$topology[$i]->departaments[$j]->id}/department/{$topology[$i]->id}/'>График работ</a></div>";
                        $subHTML .= "<div onclick=\"sendAjax('/topology/{$topology[$i]->id}/{$topology[$i]->departaments[$j]->id}/get/form/add/subdepartment/', 'GET');\">Добавить отдел</div>";
                        $subHTML .= "<div id='menu_edit_department_{$topology[$i]->departaments[$j]->id}' onclick=\"{$onclickDepartamentEdit}\">Редактировать</div>";
                        $subHTML .= "<div id='menu-delete-section-{$topology[$i]->id}-{$topology[$i]->departaments[$j]->id}' onclick=\"sendAjax('/topology/{$topology[$i]->id}/{$topology[$i]->departaments[$j]->id}/delete/departament/', 'POST');\">Удалить</div>";
                        $subHTML .= "</div>";
                        $subHTML .= "</div>";
                        $subHTML .= "</div>";

                        $subHTML .= "<div class='hiddenFormDiv' id='topologyHiddenForm_departament_{$topology[$i]->id}_{$topology[$i]->departaments[$j]->id}'></div>";
                        if(isset($topology[$i]->departaments[$j]->sub)) {
                            for($k=0;$k<count($topology[$i]->departaments[$j]->sub); $k++) {
                                //Вывод отделов
                                $subDepartamentHTML = $this->printfTopologySubdepartment($level, $sublevel, $shablons, $topology[$i]->id, $topology[$i]->departaments[$j]->sub[$k], 1, $search, $param, $value, $findSearch);
                                if($search && $subDepartamentHTML!="") $findSearch = true;
                                $subHTML .= $subDepartamentHTML;
                            }
                        }
                        $subHTML .= "</div>";
                        $sublevel--;

                    } else {
                        //Вывод отдела
                        $subDepartamentHTML = $this->printfTopologySubdepartment($level, $sublevel, $shablons, $topology[$i]->id, $topology[$i]->departaments[$j], $search, $param, $value, $findSearch);
                        if($search && $subDepartamentHTML!="") $findSearch = true;
                        $subHTML .= $subDepartamentHTML;
                    }
                }
            }
            if(isset($topology[$i]->sub)) {
                $subDepartamentHTML = $this->printTopologyView(($level+1),$topology[$i]->sub, $type, $search, $param, $value, $findSearch);
                if($search && $subDepartamentHTML!="") $findSearch = true;
                $subHTML .= $subDepartamentHTML;
            }
            $subHTML .= "</div>";
            if($findSearch) $HTML .= $subHTML;
        }
        return $HTML;
    }

    /*
     * Вывод отдела в топологии
     */
    public function printfTopologySubdepartment($level, $sublevel, $shablons, $floor_id, $departament, $sub = 0, $search=false, $param=null, $value=null, $mainFindSearch=true)
    {
        $HTML = "";
        $subHTML = "";

        $onclickSubDepartament = str_replace('{departament_id}', $departament->id, $shablons['onclickSubDepartamentUrl']);
        $onclickSubDepartament = str_replace('{topology_id}', $floor_id, $onclickSubDepartament);
        $onclickSubDepartamentEdit = str_replace('{departament_id}', $departament->id, $shablons['onclickSubDepartamentEditUrl']);

        $sublevel++;
        $style = ($search) ? '' : "style='display: none;'";
        $buttonPlusMinus = ($search) ? "-" : "+";

        $someFind = !$search;
        $findDepartamentSearch = !$search;

        if($search && (strpos($departament->name, $value)!==false || $mainFindSearch)) {
            $findDepartamentSearch = $someFind = true;
        }

        $subHTML .= "<div class='topology_item' {$style} id='department_{$floor_id}_{$departament->id}' onclick=\"activeTopologyItem('#department_{$floor_id}_{$departament->id}', '.topology_item_name', 'active', {$shablons['all']}); {$onclickSubDepartament}\">";
        $subHTML .= "<div class='topology_item_name'>";

        if(isset($departament->rooms))
            $subHTML .= "<button onclick='divSlide(this, \"#department_{$floor_id}_{$departament->id}\", \".topology_item\", false); event.stopPropagation();'>{$buttonPlusMinus}</button>";

        if($sub==0)
            $subHTML .= "Департамент: {$departament->name}";
        else if($sub==1)
            $subHTML .= "Отдел: {$departament->name}";


        $subHTML .= "<div class='topology_submenu'>";
        $subHTML .= "<div class='topology_menu_icon'></div>";
        $subHTML .= "<div class='topology_menu'>";
        $subHTML .= "<div id='menu_work_schedule_section_{$departament->id}'><a href='" . base_path() . "workschedule/show/{$departament->id}/section/{$floor_id}/'>График работ</a></div>";
        if(!isset($departament->rooms) && ($departament->parent_id == 0))
            $subHTML .= "<div onclick=\"sendAjax('/topology/{$floor_id}/{$departament->id}/get/form/add/subdepartment/', 'GET');\">Добавить отдел</div>";
        $subHTML .= "<div onclick=\"sendAjax('/topology/{$floor_id}/{$departament->id}/get/form/add/room/', 'GET');\">Добавить кабинет</div>";
        $subHTML .= "<div id='menu_edit_section_{$departament->id}' onclick=\"{$onclickSubDepartamentEdit}\">Редактировать</div>";
        $subHTML .= "<div id='menu-delete-section-{$floor_id}-{$departament->id}' onclick=\"sendAjax('/topology/{$floor_id}/{$departament->id}/delete/departament/', 'POST');\">Удалить</div>";
        $subHTML .= "</div>";
        $subHTML .= "</div>";
        $subHTML .= "</div>";

        $subHTML .= "<div class='hiddenFormDiv' id='topologyHiddenForm_departament_{$floor_id}_{$departament->id}'></div>";

        //Вывод кабинетов отдела
        for ($rooms_i = 0; $rooms_i < count($departament->rooms); $rooms_i++) {
            $onclickCabinet = str_replace('{room_id}', $departament->rooms[$rooms_i]->id, $shablons['onclickCabinetUrl']);
            $onclickCabinet = str_replace('{topology_id}', $floor_id, $onclickCabinet);
            $onclickCabinetEdit = str_replace('{room_id}', $departament->rooms[$rooms_i]->id, $shablons['onclickCabinetUrlEdit']);

            $sublevel++;

            $findRoomSearch = !$search;
            if($search && (strpos($departament->rooms[$rooms_i]->name, $value)!==false || $findDepartamentSearch)) {
                $findRoomSearch = $someFind = true;
            }

            $subRoomHTML = "<div class='topology_item' {$style} id='room_{$departament->rooms[$rooms_i]->id}' onclick=\"activeTopologyItem('#room_{$departament->rooms[$rooms_i]->id}', '.topology_item_name', 'active', {$shablons['all']}); {$onclickCabinet}\">";
            $subRoomHTML .= "<div class='topology_item_name'>";
            if($departament->rooms[$rooms_i]->workers!=null)
                $subRoomHTML .= "<button onclick='divSlide(this, \"#room_{$departament->rooms[$rooms_i]->id}\", \".topology_item\", false); event.stopPropagation();'>{$buttonPlusMinus}</button>";
            $subRoomHTML .= "Кабинет: {$departament->rooms[$rooms_i]->name}";
            $subRoomHTML .= "<div class='topology_submenu'>";
            $subRoomHTML .= "<div class='topology_menu_icon'></div>";
            $subRoomHTML .= "<div class='topology_menu'>";
            $subRoomHTML .= "<div id='menu_add_worker_room_{$departament->rooms[$rooms_i]->id}' onclick=\"sendAjax('/topology/{$floor_id}/{$departament->id}/{$departament->rooms[$rooms_i]->id}/add/worker/form/', 'GET');
                            event.stopPropagation();\">Добавить сотрудника</div>";
            $subRoomHTML .= "<div id='menu_delete_room_{$departament->rooms[$rooms_i]->id}' onclick=\"sendAjax('/topology/delete/room/{$floor_id}/{$departament->id}/{$departament->rooms[$rooms_i]->id}/', 'POST');
                            event.stopPropagation();\">Удалить кабинет</div>";

            $subRoomHTML .= "<div id='menu_edit_room_{$departament->rooms[$rooms_i]->id}' onclick=\"{$onclickCabinetEdit}\">Редактировать</div>";
            $subRoomHTML .= "<div id='menu_work_schedule_room_{$departament->rooms[$rooms_i]->id}'>
                            <a href='" . base_path() . "workschedule/show/{$departament->rooms[$rooms_i]->id}/room/{$floor_id}/'>График работ</a></div>";
            $subRoomHTML .= "</div>";
            $subRoomHTML .= "</div>";
            $subRoomHTML .= "</div>";

            $subRoomHTML .= "<div class='hiddenFormDiv' id='topologyHiddenForm_departament_{$floor_id}_{$departament->id}_{$departament->rooms[$rooms_i]->id}'></div>";

            //Вывод сотрудников кабинета
            if($departament->rooms[$rooms_i]->workers!=null) {
                $sublevel++;

                $subRoomHTML .= "<div class='topology_item' {$style} id='workers_{$departament->rooms[$rooms_i]->id}' onclick=\"activeTopologyItem('#workers_{$departament->rooms[$rooms_i]->id}', '.topology_item_name', 'active', {$shablons['all']}); {$onclickCabinet}\" >";
                $subRoomHTML .= "<div class='topology_item_name'>";
                $subRoomHTML .= "<button onclick='divSlide(this, \"#workers_{$departament->rooms[$rooms_i]->id}\", \".topology_item\", false); event.stopPropagation();'>{$buttonPlusMinus}</button>";
                $subRoomHTML .= "Сотрудники";
                $subRoomHTML .= "</div>";

                $sublevel++;

                $subRoomHTML .= "<div class='topology_item' {$style}>";
                for ($workers_i = 0; $workers_i < count($departament->rooms[$rooms_i]->workers); $workers_i++) {
                    $fio = "{$departament->rooms[$rooms_i]->workers[$workers_i]->surname} {$departament->rooms[$rooms_i]->workers[$workers_i]->first_name} {$departament->rooms[$rooms_i]->workers[$workers_i]->patronymic}";

                    $findWorkerSearch = !$search;
                    if($search && (strpos($fio, $value)!==false || $findRoomSearch)) {
                        $findWorkerSearch = $someFind = $findRoomSearch = true;
                    }

                    $onclickWorker = str_replace('{worker_id}', $departament->rooms[$rooms_i]->workers[$workers_i]->id, $shablons['onclickWorkerUrl']);
                    $onclickWorker = str_replace('{topology_id}', $floor_id, $onclickWorker);
                    $subWorkerHTML = "<div id='worker_{$departament->rooms[$rooms_i]->workers[$workers_i]->id}' onclick=\"activeTopologyItem('#worker_{$departament->rooms[$rooms_i]->workers[$workers_i]->id}', '.topology_item_name', 'active', {$shablons['all']}); {$onclickWorker}\">";
                    $subWorkerHTML .= "<div class='topology_item_name'>";
                    if($departament->rooms[$rooms_i]->workers[$workers_i]->hearing!=null)
                        $subWorkerHTML .= "<button onclick='divSlide(this, \"#worker_{$departament->rooms[$rooms_i]->workers[$workers_i]->id}\", \".topology_item\", false); event.stopPropagation();'>{$buttonPlusMinus}</button>";
                    $subWorkerHTML .= "{$fio}";
                    $subWorkerHTML .= "<div class='topology_submenu'>";
                    $subWorkerHTML .= "<div class='topology_menu_icon'></div>";
                    $subWorkerHTML .= "<div class='topology_menu'>";
                    $subWorkerHTML .= "<div id='menu_work_schedule_worker_{$departament->rooms[$rooms_i]->workers[$workers_i]->id}'>
                                                        <a href='" . base_path() . "workschedule/show/{$departament->rooms[$rooms_i]->workers[$workers_i]->id}/worker/{$floor_id}/'>График работ</a></div>";
                    $subWorkerHTML .= "<div id='menu_add_hearing_{$departament->rooms[$rooms_i]->id}-{$departament->rooms[$rooms_i]->workers[$workers_i]->id}' onclick=\"sendAjax('/topology/add/hearing/{$departament->rooms[$rooms_i]->id}/{$departament->rooms[$rooms_i]->workers[$workers_i]->id}/form/', 'GET');
                            event.stopPropagation();\">Добавить услугу</div>";
                    $subWorkerHTML .= "<div id='menu_delete_hearing_{$departament->rooms[$rooms_i]->id}-{$departament->rooms[$rooms_i]->workers[$workers_i]->id}' onclick=\"sendAjax('/topology/unlink/worker/{$departament->rooms[$rooms_i]->id}/{$departament->rooms[$rooms_i]->workers[$workers_i]->id}/', 'POST');
                            event.stopPropagation();\">Отвязать сотрудника</div>";
                    $subWorkerHTML .= "</div>";
                    $subWorkerHTML .= "</div>";
                    $subWorkerHTML .= "</div>";

                    $subWorkerHTML .= "<div class='hiddenFormDiv' id='topologyHiddenForm_worker-{$departament->rooms[$rooms_i]->id}-{$departament->rooms[$rooms_i]->workers[$workers_i]->id}'></div>";

                    $sublevel++;

                    $subWorkerHTML .= "<div class='topology_item' {$style}>";
                    for ($hearing_i = 0; $hearing_i < count($departament->rooms[$rooms_i]->workers[$workers_i]->hearing); $hearing_i++) {
                        $onclickHearing = str_replace('{hearing_id}', $departament->rooms[$rooms_i]->workers[$workers_i]->hearing[$hearing_i]->id, $shablons['onclickHearingUrl']);
                        $onclickHearing = str_replace('topology_id',$floor_id, $onclickHearing);

                        $findHearingSearch = !$search;
                        if($search && (strpos($departament->rooms[$rooms_i]->workers[$workers_i]->hearing[$hearing_i]->name, $value)!==false || $findWorkerSearch)) {
                            $findHearingSearch = $someFind = $findRoomSearch = $findWorkerSearch = true;
                        }

                        $subHearingHTML = "<div id='worker_hearing_{$departament->rooms[$rooms_i]->workers[$workers_i]->hearing[$hearing_i]->id}' onclick=\"activeTopologyItem('#worker_hearing_{$departament->rooms[$rooms_i]->workers[$workers_i]->hearing[$hearing_i]->id}', '.topology_item_name', 'active', {$shablons['all']}); {$onclickHearing}\">";
                        $subHearingHTML .= "<div class='topology_item_name'>";
                        $subHearingHTML .= "{$departament->rooms[$rooms_i]->workers[$workers_i]->hearing[$hearing_i]->name}";
                        $subHearingHTML .= "<div class='topology_submenu'>";
                        $subHearingHTML .= "<div class='topology_menu_icon'></div>";
                        $subHearingHTML .= "<div class='topology_menu'>";
                        $subHearingHTML .= "<div id='menu_work_schedule_hearing_{$departament->rooms[$rooms_i]->workers[$workers_i]->hearing[$hearing_i]->id}'>
                                                            <a href='" . base_path() . "workschedule/show/{$departament->rooms[$rooms_i]->workers[$workers_i]->hearing[$hearing_i]->id}/hearing/null/'>График работ</a></div>";
						$subHearingHTML .= "<div id='menu_work_schedule_hearing_week_template_{$departament->rooms[$rooms_i]->workers[$workers_i]->hearing[$hearing_i]->id}'
						onclick=\"sendAjax('/workschedule/weektemlateedit/show/{$departament->rooms[$rooms_i]->workers[$workers_i]->hearing[$hearing_i]->id}/', 'GET')\">Редактировать шаблон</div>";
                        $subHearingHTML .= "<div id='menu_work_schedule_hearing_week_template_{$departament->rooms[$rooms_i]->workers[$workers_i]->hearing[$hearing_i]->id}'
						onclick=\"sendAjax('/topology/add/hearing/deletehearing/{$departament->rooms[$rooms_i]->workers[$workers_i]->hearing[$hearing_i]->id}/', 'GET')\">Удалить услугу</div>";
						$subHearingHTML .= "</div>";
                        $subHearingHTML .= "</div>";
                        $subHearingHTML .= "</div>";
                        $subHearingHTML .= "</div>";
                        if($findHearingSearch) $subWorkerHTML .= $subHearingHTML;
                    }
                    $subWorkerHTML .= "</div>";

                    $subWorkerHTML .= "</div>";
                    if($findWorkerSearch) $subRoomHTML .= $subWorkerHTML;
                }
                $subRoomHTML .= "</div>";
                $subRoomHTML .= "</div>";
                $sublevel--;
            }
            $sublevel--;
            $subRoomHTML .= "</div>";
            if($findRoomSearch) $subHTML .= $subRoomHTML;
        }
        $subHTML .= "</div>";
        if($someFind) $HTML .= $subHTML;
        return $HTML;
    }



    /*
     * Вывод уведомлений
     * Return: HTML
     */
    public function showNotificationsView()
    {
        $HTML = "";
        $HTML .= "<div id='notifications' class='userData'>";
        $HTML .= "<h2>Здесь будет <strike>Вася</strike> уведомления</h2>";
        $HTML .= "</div>";
        return $HTML;
    }

    /*
     * Вывод сообщений
     * Return: HTML
     */
    public function showMessagesView()
    {
        $HTML = "";
        $HTML .= "<div id='messages' class='userData'>";
        $HTML .= "<h2>Сообщения (3 пропущенных)</h2>";
        $HTML .= "</div>";
        return $HTML;
    }


    /*
     * Вывод нулевого аккаунта
     * Return: HTML
     * TODO: Перенес во вьюхи нужно удалить
    */
    public function showNullAccountView()
    {
        $HTML = "";
        $HTML .= "<div id='nullaccount' class='userData'>";


        $HTML .= "<div id='nullaccountin' class='userData'>";
        $HTML .= "<div class='buttonsControl'>";
        $HTML .= "<h2 class='inline'>Вход</h2>";
        $HTML .= "<div class='button white' title='Увеличить'><img class='bigIcon' src='".base_path()."images/icons/zoom-in.jpg'></div>";
        $HTML .= "<div class='button white' title='Уменьшить'><img class='bigIcon' src='".base_path()."images/icons/zoom-out.jpg'></div>";
        $HTML .= "<div class='button white' title='Развернуть на весь экран' onclick=\"toggleClass('#nullaccountin', 'active');\"><img class='bigIcon' src='".base_path()."images/icons/full-view.jpg'></div></div>";

        $HTML .= $this->showAddNullAccountView();
        $HTML .= "</div>";


        $HTML .= "<div id='nullaccountout' class='userData'>";
        $HTML .= "<div class='buttonsControl' style='inline-block'>";
        $HTML .= "<h2 class='inline'>Вход</h2>";
        $HTML .= "<div class='button white' title='Увеличить'><img class='bigIcon' src='".base_path()."images/icons/zoom-in.jpg'></div>";
        $HTML .= "<div class='button white' title='Уменьшить'><img class='bigIcon' src='".base_path()."images/icons/zoom-out.jpg'></div>";
        $HTML .= "<div class='button white' title='Развернуть на весь экран' onclick=\"toggleClass('#nullaccountin', 'active');\"><img class='bigIcon' src='".base_path()."images/icons/full-view.jpg'></div></div>";

        $nullaccount = $this->model->getNullAccountModel();
        $HTML .= "<select name='nullaccountid' onchange=\"sendAjax('/nullaccount/updateform/'+this.value+'/', 'GET')\">";
        if(isset($nullaccount['status'])) {
            $HTML .= "<option value='0'>Нет данных</option>";
        } else {
            $HTML .= "<option value='0'>Не выбран нулевой аккаунт</option>";
            for($i=0;$i<count($nullaccount);$i++)
            {
                if (($nullaccount[$i]->first_name==null)&&($nullaccount[$i]->patronymic==null)&&($nullaccount[$i]->surname==null))
                    $HTML .= "<option value='{$nullaccount[$i]->user_id}'>Не были указаны данные при входе</option>";
                else
                {
                    $HTML .= "<option value='{$nullaccount[$i]->user_id}'>";
                        if ($nullaccount[$i]->surname==null) $HTML .=" [Нет фамилии] ";
                        else  $HTML .=" {$nullaccount[$i]->surname} ";

                        if ($nullaccount[$i]->first_name==null) $HTML .=" [Нет имени] ";
                        else  $HTML .=" {$nullaccount[$i]->first_name} ";

                        if ($nullaccount[$i]->patronymic==null) $HTML .=" [Нет отчества] ";
                        else  $HTML .=" {$nullaccount[$i]->patronymic} ";
                    $HTML .="</option>";
                }
            }
        }
        $HTML .= "</select>";
        $HTML .= "<div id='nullaccountoutform' class='userData'>";
        $HTML .= "</div>";
        $HTML .= "</div>";
        $HTML .= "</div>";
        return $HTML;
    }

    /*
     * Вывод формы пропуска нулевого аккаунта
     * Return: HTML
     * TODO: Перенес во вьюхи нужно удалить
     */
    public function showAddNullAccountView()
    {
        $HTML = "";
        $HTML .= "<div id='nullAccountUserPhoto' class='userPhoto'>";

        $HTML .= "<img src='".base_path()."images/icons/Vhod_photo.PNG' class='bigIcon' \">";

        $HTML .= "</div>";
        $HTML .= "<form name='addNullAccountForm'>";
        $HTML .= "<table><tr><td>Фамилия</td><td><input type='text' class='margins boryes' name='surname'></td></tr>";
        $HTML .= "<tr><td>Имя</td><td><input class='margins boryes' type='text' name='first_name'></td></tr>";
        $HTML .= "<tr><td>Отчество</td><td><input class='margins boryes' type='text' name='patronymic'></td></tr>";
        $user_types = $this->model->getUserTypesModel();
        $HTML .= "<tr><td>Статус</td><td><select name='user_type_id'>";
        if(isset($user_types['status'])) {
            $HTML .= "<option value='0'>Нет данных</option>";
        } else {
            $HTML .= "<option value='0' disabled>Не выбран статус</option>";
            for($i=0;$i<count($user_types);$i++)
            {
                if (($user_types[$i]->id==3)||($user_types[$i]->main_class==3))
                {
                    $HTML .= "<option value='{$user_types[$i]->id}'>{$user_types[$i]->name}</option>";
                }
            }
        }
        $HTML .= "</select></td></tr></table>";
        $HTML .= "</form>";
        $HTML .= "<div class='button greenak margins' >Сфотографировать</div><br>";
        $HTML .= "<div class='button greenak margins ' >Добавить копированием</div><br>";
        $HTML .= "<div class='button greenak margins ' >Громкая связь</div><br>";
        $HTML .= "<div class='button margins ' onclick=\"sendAjax('/nullaccount/add/', 'POST', 'addNullAccountForm')\">Пропустить</div>";
        return $HTML;
    }

    /*
     * Вывод формы выпуска нулевого аккаунта
     * Return: HTML
     */
    public function showChangeNullAccountView($id=null)
    {
        $HTML = "";
        if ($id!=0)
        {
            $HTML .= "<div id='nullAccountUserPhoto' class='userPhoto margins'>";
            //$HTML .= "{$userinfo[0]->user_photo}<img src='".base_path()."images/icons/Vhod_photo.PNG' class='bigIcon' \">";
            $HTML .= "<img src='".base_path()."images/icons/Vhod_photo.PNG' class='bigIcon' \">";
            $HTML .= "</div>";
            $HTML .= "<form name='changeNullAccountForm'>";
            $user=$this->model->getUsersModel($id);
            $HTML .= "<table><tr><td>Фамилия</td><td><input class='margins boryes' type='text' name='surname' value='{$user[0]->surname}'></td></tr>";
            $HTML .= "<tr><td>Имя</td><td><input class='margins boryes' type='text' name='first_name' value='{$user[0]->first_name}'></td></tr>";
            $HTML .= "<tr><td>Отчество</td><td><input class='margins boryes' type='text' name='patronymic' value='{$user[0]->patronymic}'></td></tr>";
            $user_types = $this->model->getUserTypesModel();
            $HTML .= "<tr><td>Статус</td><td><select name='user_type_id'>";
            if(isset($user_types['status'])) {
                $HTML .= "<option value='0'>Нет данных</option>";
            } else {
                $HTML .= "<option value='0' disabled>Не выбран статус</option>";
                for($i=0;$i<count($user_types);$i++)
                {
                    if (($user_types[$i]->id==3)||($user_types[$i]->main_class==3))
                    {
                        $HTML .= "<option value='{$user_types[$i]->id}'";
                        if ($user[0]->user_type_id==$user_types[$i]->id) $HTML.=" selected";
                        $HTML .=">{$user_types[$i]->name}</option>";
                    }
                }
            }
            $HTML .= "</select></td></tr></table>";
            $HTML .= "</form>";
            $HTML .= "<div class='button greenak margins'>Сфотографировать</div><br>";
            $HTML .= "<div class='button greenak margins' >Добавить копированием</div><br>";
            $HTML .= "<div class='button greenak margins' >Громкая связь</div><br>";
            $HTML .= "<div class='button margins' onclick=\"sendAjax('/nullaccount/update/{$id}/', 'POST', 'changeNullAccountForm')\">Выпустить</div>";
        }
        return $HTML;
    }

    /*
     * Вывод людей в здании
     * Return: HTML
     */
    public function showPeopleInBuildingView()
    {

        $people=$this->model->sendQuery("SELECT
(SELECT COUNT (*) AS allpeople FROM user_pass
LEFT JOIN users ON users.id = user_pass.user_id
 WHERE date_in=(SELECT CURRENT_DATE) AND users.filial_id={$_SESSION['filial_id']}),
(SELECT COUNT (*) AS allpeoplein FROM user_pass
LEFT JOIN users ON users.id = user_pass.user_id
 WHERE date_in=(SELECT CURRENT_DATE) AND date_out IS NULL AND users.filial_id={$_SESSION['filial_id']}),
(SELECT COUNT (*) AS allpeopleout FROM user_pass
LEFT JOIN users ON users.id = user_pass.user_id
 WHERE date_out=(SELECT CURRENT_DATE) AND date_in=(SELECT CURRENT_DATE) AND users.filial_id={$_SESSION['filial_id']});");

        $HTML = "";
        $HTML .= "<div id='peopleinbulding' class='userData'>";
        $HTML .= "<table><tr><td>Вошли в здание</td><td>{$people[0]->allpeople}</td></tr>";
        $HTML .= "<tr><td>Вышли из здания</td><td>{$people[0]->allpeopleout}</td></tr>";
        $HTML .= "<tr><td>Количество людей в здании</td><td>{$people[0]->allpeoplein}</td></tr></table>";
        $HTML .= "</div>";
        return $HTML;
    }

    public function topologyView()
    {
        $HTML = "";
        $HTML .= "<div id='topology' class='userData'>";
        $HTML .= $this->showTopologyView();
        $HTML .= "</div>";
        return $HTML;
    }





    /*
     * Топология на графике работ
     */
    public function printWorkScheduleTopologyView($level=0, $topology=null)
    {
        $HTML = "";
        for($i=0;$i<count($topology);$i++)
        {
            if(!$topology[$i]->room) {
                //select rooms
                $sql = "SELECT * FROM filial_rooms WHERE parent_id={$topology[$i]->id}";
                $rooms = $this->model->sendQuery($sql);

                $main_style = ($level == 0) ? "" : "style='display: none;'";
                $HTML .= "<div class='topology_item' {$main_style} id='main_department_{$topology[$i]->id}' onclick=\"sendAjax('/workschedule/showbutton/{$topology[$i]->id}/category/null/', 'GET'); event.stopPropagation();\">";
                if($topology[$i]->sub!=null) {
                    $HTML .= "<button onclick='divSlide(this, \"#main_department_{$topology[$i]->id}\", \".topology_item\", false); event.stopPropagation();'>+</button>";
                }
                $HTML .= "{$topology[$i]->name}";
                $HTML .= "<div class='topology_submenu'>";
                $HTML .= "<div class='topology_menu_icon'></div>";
                $HTML .= "<div class='topology_menu'>";
                $HTML .= "<div id='menu_edit_main_department_{$topology[$i]->id}' onclick=\"sendAjax('/topology/edit/form/{$topology[$i]->id}/category/', 'GET');
				event.stopPropagation();\">Редактировать</div>";
                $HTML .= "<div id='menu_work_schedule_main_department_{$topology[$i]->id}'>
				<a href='" . base_path() . "workschedule/show/{$topology[$i]->id}/category/null/'>График работ</a></div>";
                $HTML .= '</div>';
                $HTML .= '</div>';

                if(count($rooms)>=1) {
                    $sql = "SELECT *
					FROM filial_departament
					WHERE id IN (SELECT DISTINCT department_id FROM filial_rooms WHERE parent_id={$topology[$i]->id})";
                    $departaments = $this->model->sendQuery($sql);
                    for ($j = 0; $j < count($departaments); $j++) {
                        $sub = 0;
                        if ($departaments[$j]->parent_id != 0 || $departaments[$j]->parent_id != null) {
                            $sql = "SELECT * FROM filial_departament WHERE id='{$departaments[$j]->parent_id}'";
                            $main_departament = $this->model->sendQuery($sql);
                            $HTML .= "<div class='topology_item' style='display: none;'
							id='department_{$main_departament[0]->id}' onclick=\"sendAjax('/workschedule/showbutton/{$main_departament[0]->id}/department/{$topology[$i]->id}/', 'GET');
							event.stopPropagation();\">";$HTML .= "<button onclick='divSlide(this, \"#department_{$topology[$i]->id}_{$main_departament[0]->id}\", \".topology_item\", false); event.stopPropagation();'>+</button>";
                            $HTML .= "Департамент: {$main_departament[0]->name}";
                            $sub = 1;
                            $HTML .= "<div class='topology_submenu'>";
                            $HTML .= "<div class='topology_menu_icon'></div>";
                            $HTML .= "<div class='topology_menu'>";
                            $HTML .= "<div id='menu_work_schedule_department_{$main_departament[0]->id}'>
				<a href='" . base_path() . "workschedule/show/{$main_departament[0]->id}/department/{$topology[$i]->id}/'>График работ</a></div>";
                            $HTML .= "<div id='menu_edit_department_{$main_departament[0]->id}' onclick=\"sendAjax('/filial-departments/form/{$main_departament[0]->id}/department/', 'GET');
                                    event.stopPropagation();\">Редактировать</div>";
                            $HTML .= "</div>";
                            $HTML .= "</div>";
                        }
                        $HTML .= "<div class='topology_item' style='display: none;'
						id='section_{$departaments[$j]->id}' onclick=\"sendAjax('/workschedule/showbutton/{$departaments[$j]->id}/section/{$topology[$i]->id}/', 'GET');
						event.stopPropagation();\">";
                        $HTML .= "Отдел: {$departaments[$j]->name}";
                        $HTML .= "<div class='topology_submenu'>";
                        $HTML .= "<div class='topology_menu_icon'></div>";
                        $HTML .= "<div class='topology_menu'>";
                        $HTML .= "<div id='menu_work_schedule_section_{$departaments[$j]->id}'>
				<a href='" . base_path() . "workschedule/show/{$departaments[$j]->id}/section/{$topology[$i]->id}/'>График работ</a></div>";
                        $HTML .= "<div id='menu_edit_section_{$departaments[$j]->id}' onclick=\"sendAjax('/filial-departments/form/{$departaments[$j]->id}/section/', 'GET');
                                    event.stopPropagation();\">Редактировать</div>";
                        $HTML .= "</div>";
                        $HTML .= "</div>";

                        //Вывод кабинетов отдела
                        $sql = "SELECT * FROM filial_rooms WHERE department_id = '{$departaments[$j]->id}' AND parent_id='{$topology[$i]->id}' AND room='true'";

                        $rooms = $this->model->sendQuery($sql);
                        for ($rooms_i = 0; $rooms_i < count($rooms); $rooms_i++) {
                            $HTML .= "<div class='topology_item' style='display: none;'
							id='room_{$rooms[$rooms_i]->id}' onclick=\"sendAjax('/workschedule/showbutton/{$rooms[$rooms_i]->id}/room/{$topology[$i]->id}/', 'GET');
							event.stopPropagation();\">";
                            $HTML .= "Кабинет: {$rooms[$rooms_i]->name}";
                            $HTML .= "<div class='topology_submenu'>";
                            $HTML .= "<div class='topology_menu_icon'></div>";
                            $HTML .= "<div class='topology_menu'>";
                            $HTML .= "<div id='menu_add_worker_room_{$rooms[$rooms_i]->id}' onclick=\"sendAjax('/topology/add/worker/{$rooms[$rooms_i]->id}/', 'GET');
				event.stopPropagation();\">Добавить сотрудника</div>";
                            $HTML .= "<div id='menu_add_room_{$rooms[$rooms_i]->id}' onclick=\"sendAjax('/topology/add/hearing/{$rooms[$rooms_i]->id}/', 'GET');
				event.stopPropagation();\">Добавить услугу</div>";
                            $HTML .= "<div id='menu_edit_room_{$rooms[$rooms_i]->id}' onclick=\"sendAjax('/topology/edit/form/{$rooms[$rooms_i]->id}/room/', 'GET');
							event.stopPropagation();\">Редактировать</div>";
                            $HTML .= "<div id='menu_work_schedule_room_{$rooms[$rooms_i]->id}'>
				<a href='" . base_path() . "workschedule/show/{$rooms[$rooms_i]->id}/room/{$topology[$i]->id}/'>График работ</a></div>";
                            $HTML .= "</div>";
                            $HTML .= "</div>";

                            //Вывод сотрудников кабинета
                            $sql = "SELECT workers.*, users.surname, users.first_name, users.patronymic
							FROM workers
							LEFT JOIN users ON users.id = workers.user_id
							WHERE room_id = '{$rooms[$rooms_i]->id}'";
                            $workers = $this->model->sendQuery($sql);
                            for ($workers_i = 0; $workers_i < count($workers); $workers_i++) {
                                $HTML .= "<div class='topology_item' style='display: none;'
								id='worker_{$workers[$workers_i]->id}' onclick=\"sendAjax('/workschedule/showbutton/{$workers[$workers_i]->id}/worker/{$topology[$i]->id}/', 'GET');
								event.stopPropagation();\">";
                                $HTML .= "Сотрудник: {$workers[$workers_i]->surname} {$workers[$workers_i]->first_name} {$workers[$workers_i]->patronymic}";
                                $HTML .= "<div class='topology_submenu'>";
                                $HTML .= "<div class='topology_menu_icon'></div>";
                                $HTML .= "<div class='topology_menu'>";
                                $HTML .= "<div id='menu_work_schedule_worker_{$workers[$workers_i]->id}'>
				<a href='" . base_path() . "workschedule/show/{$workers[$workers_i]->id}/worker/{$topology[$i]->id}/'>График работ</a></div>";
                                $HTML .= "</div>";
                                $HTML .= "</div>";

                                //Вывод услуг кабинета
                                $sql = "SELECT *
								FROM filial_rooms_hearing
								WHERE room_id = '{$rooms[$rooms_i]->id}' AND departament_id='{$departaments[$j]->id}' AND worker_id='{$workers[$workers_i]->id}'";
                                $hearing = $this->model->sendQuery($sql);
                                for ($hearing_i = 0; $hearing_i < count($hearing); $hearing_i++) {
                                    $HTML .= "<div class='topology_item' style='display: none;'
									id='hearing_{$hearing[$hearing_i]->id}' onclick=\"sendAjax('/workschedule/showbutton/{$hearing[$hearing_i]->id}/hearing/{$topology[$i]->id}/', 'GET');
									event.stopPropagation();\">";
                                    $HTML .= "Услуга: {$hearing[$hearing_i]->name}";
                                    $HTML .= "<div class='topology_submenu'>";
                                    $HTML .= "<div class='topology_menu_icon'></div>";
                                    $HTML .= "<div class='topology_menu'>";
                                    $HTML .= "<div id='menu_work_schedule_hearing_{$hearing[$hearing_i]->id}'>
									<a href='" . base_path() . "workschedule/show/{$hearing[$hearing_i]->id}/hearing/{$topology[$i]->id}/'>График работ</a></div>";
                                    $HTML .= "</div>";
                                    $HTML .= "</div>";
                                    $HTML .= "</div>";
                                }
                                $HTML .= "</div>";
                            }
                            $HTML .= "</div>";
                        }
                        $HTML .= "</div></div>";
                    }
                }
                if($topology[$i]->sub!=null) {
                    $HTML .= $this->printWorkScheduleTopologyView(($level+1),$topology[$i]->sub);
                }
                $HTML .= "</div>";
            }
        }
        return $HTML;
    }







    /*
     * Вывод формы добавления работника в топологии
     * Return: HTML
     */
    public function showAddTopologyWorkerView($room_id=null)
    {
        $HTML = "";
        $HTML .= "<div id='addworkertopology' class='userData'>";
        $room=$this->model->sendQuery("SELECT filial_rooms.*, filial_departament.name AS dep_name, filial_departament.id AS dep_id FROM filial_rooms
		LEFT JOIN filial_departament ON filial_rooms.department_id = filial_departament.id
		WHERE filial_rooms.id = '{$room_id}'");
        $HTML .= "<h2>Добавить сотрудника в кабинет № {$room[0]->number}/{$room[0]->name}</h2>";
        $HTML .= "<h2>{$room[0]->dep_name}</h2>";
        $HTML .= "<table><tr><td><h1>Свободные сотрудники</h1></td><td><h1>Занятые сотрудники</h1></td></tr>";
        $HTML .= "<tr valign='top'><td><div id='addworkertopology_allworkers' class='userData'>";
        $HTML .= $this->showAddWorkerTopologyAllWorkersView($room[0]->id, $room[0]->department_id);
        $HTML .= "</div></td>";
        $HTML .= "<td><div id='addworkertopology_selectedworkers' class='userData'>";
        $HTML .= $this->showAddWorkerTopologySelectedWorkersView($room[0]->id, $room[0]->department_id);
        $HTML .= "</div></td></tr></table>";
        $HTML .= "</div>";
        return $HTML;
    }

    /*
     * Вывод формы со всеми работниками, доступными для добавления в топологии
     * Return: HTML
     */
    public function showAddWorkerTopologyAllWorkersView($room_id=null, $department_id=null)
    {
        $HTML = "";
        $all_workers=$this->model->sendQuery("SELECT workers.*, users.first_name, users.patronymic, users.surname FROM workers
		LEFT JOIN users ON workers.user_id=users.id
		LEFT JOIN user_types ON user_types.id=users.user_type_id
		WHERE workers.room_id IS NULL AND workers.department_id = '{$department_id}' AND (user_types.id=1 OR user_types.main_class=1)");
        for ($all_workers_i=0;$all_workers_i<count($all_workers);$all_workers_i++)
        {
            $first_name=substr($all_workers[$all_workers_i]->first_name,0,1);
            $patronymic=substr($all_workers[$all_workers_i]->patronymic,0,1);
            $HTML .= "<div id='addworkertopology_allworkers_{$all_workers[$all_workers_i]->id}'
			onclick=\"sendAjax('/topology/add_to_topology/worker/{$room_id}/{$all_workers[$all_workers_i]->id}/', 'GET');
			sendAjax('/topology/add/show_busy_worker/{$room_id}/{$department_id}/', 'GET');
			sendAjax('/topology/add/show_all_worker/{$room_id}/{$department_id}/', 'GET');
			sendAjax('/topology/', 'GET');
			event.stopPropagation();\">
			{$all_workers[$all_workers_i]->surname} {$first_name}. {$patronymic}. </div>";
        }
        return $HTML;
    }

    /*
     * Вывод формы списка работников,уже прикрепленных к кабинету в топологии
     * Return: HTML
     */
    public function showAddWorkerTopologySelectedWorkersView($room_id=null, $department_id=null)
    {
        $HTML = "";
        $all_workers=$this->model->sendQuery("SELECT workers.*, users.first_name, users.patronymic, users.surname FROM workers
		LEFT JOIN users ON workers.user_id=users.id
		LEFT JOIN user_types ON user_types.id=users.user_type_id
		WHERE workers.room_id = '{$room_id}' AND (user_types.id=1 OR user_types.main_class=1)");
        for ($all_workers_i=0;$all_workers_i<count($all_workers);$all_workers_i++)
        {
            $first_name=substr($all_workers[$all_workers_i]->first_name,0,1);
            $patronymic=substr($all_workers[$all_workers_i]->patronymic,0,1);
            $HTML .= "<div id='addworkertopology_allworkers_{$all_workers[$all_workers_i]->id}'
			onclick=\"sendAjax('/topology/drop_from_topology/worker/{$all_workers[$all_workers_i]->id}/', 'GET');
			sendAjax('/topology/add/show_busy_worker/{$room_id}/{$department_id}/', 'GET');
			sendAjax('/topology/add/show_all_worker/{$room_id}/{$department_id}/', 'GET');
			sendAjax('/topology/', 'GET');
			event.stopPropagation();\">
			{$all_workers[$all_workers_i]->surname} {$first_name}. {$patronymic}. </div>";
        }
        return $HTML;
    }


    /*
     * Вывод формы добавления услуги в топологии
     * Return: HTML
     */
    public function showAddTopologyHearingView($room_id=null)
    {
        $HTML = "";
        $room=$this->model->sendQuery("SELECT filial_rooms.*, filial_departament.name AS dep_name, filial_departament.id AS dep_id FROM filial_rooms
		LEFT JOIN filial_departament ON filial_rooms.department_id = filial_departament.id
		WHERE filial_rooms.id = '{$room_id}'");
        $HTML .= "<div id='addhearingtopology' class='userData'>";
        $HTML .= "<h2>Добавить услугу в кабинет № {$room[0]->number}/{$room[0]->name}</h2>";
        $HTML .= "<table><form name='topologyaddhearingcreatebuttonform'>";
        $HTML .= "<tr><td align='right'>Название услуги</td><td align='left'><input name='hearing_name' type='text' value='Услуга'></td><td></td>";
        $HTML .= "</form>";
        $HTML .= "<tr><td valign='top'><div id='addhearingtopology_allworkers' class='userData'>";
        $HTML .= $this->showAddHearingTopologyAllWorkersView($room[0]->id);
        $HTML .= "</div></td>";
        $HTML .= "<td><div id='addhearingtopology_selectedworkers' class='userData'>";
        $HTML .= $this->showAddHearingTopologySelectedWorkersView($room[0]->id);
        $HTML .= "</div></td></tr></table>";
        $HTML .="<div id='topologyaddhearing_scheduletemplate'>";
        $HTML .="Шаблон графика";
        $HTML .="<input type='checkbox' name='template_type' value='week'>Недельный";
        $HTML .="<input type='checkbox' name='template_type' value='manual'>Произвольный";
        $HTML .="<div id='topologyaddhearing_week'><br>День недели<br>";
        $HTML .= "<table style='display: block;'>";
        $HTML .= "<tbody>";
        $HTML .= "<tr>";
        $HTML .= "<td>ПН</td>";
        $HTML .= "<td><select name='daytype_monday'><option value='1'>Рабочий день</option>
					<option value='2'>Сокращенный день</option><option value='3'>Выходной день</option></td>";
        $HTML .= "</tr>";

        $HTML .= "<tr>";
        $HTML .= "<td>ВТ</td>";
        $HTML .= "<td><select name='daytype_tuesday'><option value='1'>Рабочий день</option>
					<option value='2'>Сокращенный день</option><option value='3'>Выходной день</option></td>";
        $HTML .= "</tr>";
        $HTML .= "<tr>";
        $HTML .= "<td>СР</td>";
        $HTML .= "<td><select name='daytype_wednesday'><option value='1'>Рабочий день</option>
					<option value='2'>Сокращенный день</option><option value='3'>Выходной день</option></td>";
        $HTML .= "</tr>";

        $HTML .= "<tr>";
        $HTML .= "<td>ЧТ</td>";
        $HTML .= "<td><select name='daytype_thursday'><option value='1'>Рабочий день</option>
					<option value='2'>Сокращенный день</option><option value='3'>Выходной день</option></td>";
        $HTML .= "</tr>";

        $HTML .= "<tr>";
        $HTML .= "<td>ПТ</td>";
        $HTML .= "<td><select name='daytype_friday'><option value='1'>Рабочий день</option>
					<option value='2'>Сокращенный день</option><option value='3'>Выходной день</option></td>";
        $HTML .= "</tr>";

        $HTML .= "<tr>";
        $HTML .= "<td>СБ</td>";
        $HTML .= "<td><select name='daytype_saturday'><option value='1'>Рабочий день</option>
					<option value='2'>Сокращенный день</option><option value='3'>Выходной день</option></td>";
        $HTML .= "</tr>";

        $HTML .= "<tr>";
        $HTML .= "<td>ВС</td>";
        $HTML .= "<td><select name='daytype_sunday'><option value='1'>Рабочий день</option>
					<option value='2'>Сокращенный день</option><option value='3'>Выходной день</option></td>";
        $HTML .= "</tr>";
        $HTML .= "</tbody>";
        $HTML .= "</table>";
        $HTML .="</div>";
        $HTML .= "<div class='polov'>";
        $HTML .= "<br><b>Рабочие дни</b>";
        $HTML .= "<table style='display: block;'>";
        $HTML .= "<tbody>";
        $HTML .= "<tr>";
        $HTML .= "<td>Начало приема</td>";
        $HTML .= "<td><input name='pass_in_fullday' type='time' value='08:00'></td>";
        $HTML .= "</tr>";

        $HTML .= "<tr>";
        $HTML .= "<td>Окончание приема</td>";
        $HTML .= "<td><input name='pass_out_fullday' type='time' value='16:00'></td>";
        $HTML .= "</tr>";

        $HTML .= "<tr>";
        $HTML .= "<td>Обед</td>";
        $HTML .= "<td>С <input name='dinner_start_fullday' type='time' value='12:00'> До <input name='dinner_end_fullday' type='time' value='13:00'></td>";
        $HTML .= "</tr>";

        $HTML .= "<tr>";
        $HTML .= "<td>Вход сотрудника за</td>";
        $HTML .= "<td><input name='pass_before_fullday' type='number' value='60'> мин.</td>";
        $HTML .= "</tr>";

        $HTML .= "<tr>";
        $HTML .= "<td>Выход сотрудника после</td>";
        $HTML .= "<td><input name='pass_after_fullday' type='number' value='90'> мин.</td>";
        $HTML .= "</tr>";

        $HTML .= "<tr>";
        $HTML .= "<td>Блокировать печать пропусков за</td>";
        $HTML .= "<td><input name='stop_print_fullday' type='number' value='15'> мин.</td>";
        $HTML .= "</tr>";

        $HTML .= "<tr>";
        $HTML .= "<td>Свободный вход/выход сотрудников</td>";
        $HTML .= "<td><input name='freepass_fullday' type='checkbox' checked></td>";
        $HTML .= "</tr>";
        $HTML .= "</tbody>";
        $HTML .= "</table>";
        $HTML .= "</div>";
        $HTML.="</div>";
        $HTML .= "<div id='topologyaddhearingcreatebutton'>";
        $HTML .= "<div class='button'>Создать</div>";
        $HTML .= "</div>";
        $HTML .= "</div>";
        return $HTML;
    }

    /*
     * Вывод формы со всеми работниками кабинета в топологии
     * Return: HTML
     */
    public function showAddHearingTopologyAllWorkersView($room_id=null)
    {
        $HTML = "";
        $HTML .= "<h2>Сотрудники кабинета</h2>";
        $all_workers=$this->model->sendQuery("SELECT workers.*, users.first_name, users.patronymic, users.surname FROM workers
		LEFT JOIN users ON workers.user_id=users.id
		LEFT JOIN user_types ON user_types.id=users.user_type_id
		WHERE workers.room_id ='{$room_id}' AND (user_types.id=1 OR user_types.main_class=1)");
        for ($all_workers_i=0;$all_workers_i<count($all_workers);$all_workers_i++)
        {
            $first_name=substr($all_workers[$all_workers_i]->first_name,0,1);
            $patronymic=substr($all_workers[$all_workers_i]->patronymic,0,1);
            $HTML .= "<div id='addhearingtopology_allworkers_{$all_workers[$all_workers_i]->id}'
			onclick=\"sendAjax('/topology/add/hearing/createbutton/{$room_id}/{$all_workers[$all_workers_i]->id}/', 'GET');
			sendAjax('/topology/', 'GET');
			event.stopPropagation();\">
			{$all_workers[$all_workers_i]->surname} {$first_name}. {$patronymic}. </div>";
        }
        return $HTML;
    }


    /*
     * Вывод формы списка уже прикрепленных к кабинету услуг в топологии
     * Return: HTML
     */
    public function showAddHearingTopologySelectedWorkersView($room_id=null)
    {
        $HTML = "";
        $HTML .= "<h2>Занятые сотрудники</h2>";
        $all_hearings=$this->model->sendQuery("SELECT filial_rooms_hearing.*, workers.public as worker_public, users.first_name, users.patronymic, users.surname FROM filial_rooms_hearing
		LEFT JOIN workers ON filial_rooms_hearing.worker_id=workers.id
		LEFT JOIN users ON workers.user_id=users.id
		WHERE filial_rooms_hearing.room_id = '{$room_id}' AND workers.room_id IS NOT NULL");
        for ($all_hearings_i=0;$all_hearings_i<count($all_hearings);$all_hearings_i++)
        {
            $first_name=substr($all_hearings[$all_hearings_i]->first_name,0,1);
            $patronymic=substr($all_hearings[$all_hearings_i]->patronymic,0,1);
            $HTML .= "<div id='addhearingtopology_selectedhearings_{$all_hearings[$all_hearings_i]->id}'>
			{$all_hearings[$all_hearings_i]->surname} {$first_name}. {$patronymic}./{$all_hearings[$all_hearings_i]->name}</div>";
            $HTML .= "<div id='addhearingtopology_selectedhearings_menu_public_worker_{$all_hearings[$all_hearings_i]->id}'>
			<input type='checkbox' name='addhearingtopology_selectedhearings_menu_public_worker_checkbox_{$all_hearings[$all_hearings_i]->id}'
			onchange=\"sendAjax('/topology/add/hearing/publicworker/{$all_hearings[$all_hearings_i]->worker_id}/'+this.checked+'/', 'GET');
			sendAjax('/topology/add/show_existing_hearings/{$room_id}/', 'GET');\"";
            if ($all_hearings[$all_hearings_i]->worker_public) $HTML .= " checked";
            $HTML .= ">Публичность для сотрудника</div>";
            $HTML .= "<div id='addhearingtopology_selectedhearings_menu_public_hearing_{$all_hearings[$all_hearings_i]->id}'>
			<input type='checkbox' name='addhearingtopology_selectedhearings_menu_public_hearing_checkbox_{$all_hearings[$all_hearings_i]->id}'
			onchange=\"sendAjax('/topology/add/hearing/publichearing/{$all_hearings[$all_hearings_i]->id}/'+this.checked+'/', 'GET');\"";
            if ($all_hearings[$all_hearings_i]->public) $HTML .= " checked";
            $HTML .= ">Публичность для услуги</div>";
            $HTML .= "<div id='addhearingtopology_selectedhearings_menu_deletehearing_{$all_hearings[$all_hearings_i]->id}'
			onclick=\"sendAjax('/topology/add/hearing/deletehearing/{$all_hearings[$all_hearings_i]->id}/', 'GET');
			sendAjax('/topology/add/show_existing_hearings/{$room_id}/', 'GET');
			sendAjax('/topology/', 'GET');
			event.stopPropagation();\">
			Удалить</div>";
        }
        return $HTML;
    }

    /*
     * Формирование кнопки для создания слушания
     * Return: HTML
     */
    public function showHearingCreateButtonTopologyView($room_id=null, $worker_id=null)
    {
        $HTML = "";
        $HTML .= "<div class='button'
			onclick=\"sendAjax('/topology/add/hearing/create/{$room_id}/{$worker_id}/', 'POST','topologyaddhearingcreatebuttonform');
			sendAjax('/topology/add/show_existing_hearings/{$room_id}/', 'GET');
			sendAjax('/topology/', 'GET');\">Создать</div>";
        return $HTML;
    }

    /*
     * Вывод формы для редактирования помещение/кабинета
     * Return: HTML
     */
    public function showChangeTopologyObjectView($room_id=null, $type=null)
    {
        $HTML = "";
        $room=$this->model->sendQuery("SELECT * FROM filial_rooms WHERE id='{$room_id}'");
        if ($type=='room') $HTML .= "<h2>Изменение кабинета № {$room[0]->number} - {$room[0]->name}</h2>";
        if ($type=='category') $HTML .= "<h2>Изменение помещения {$room[0]->name}</h2>";
        $HTML .= "<form name='updateRoomsForm'>";
        $HTML .= "<table><tr><td>Название</td><td><input class='margins' type='number' name='name' value='{$room[0]->name}'></td></tr>";
        //Если это изменение кабинета
        if ($type=='room') $HTML .= "<tr><td>Номер</td><td><input class='margins' type='number' name='number' value='{$room[0]->number}'></td></tr>";
        $workers = $this->model->getWorkersModel();
        $HTML .= "<tr><td>Ответственный сотрудник</td><td><select class='margins' name='worker_id'>";
        if (isset($workers['status'])) {
            $HTML .= "<option value='0'>Нет данных</option>";
        } else {
            $HTML .= "<option value='0'>Не выбран ответственный сотрудник</option>";
            for ($i = 0; $i < count($workers); $i++) {
                $HTML .= "<option value='{$workers[$i]->id}'";
                if ($workers[$i]->id==$room[0]->worker_id) $HTML .=" selected";
                $HTML.=">{$workers[$i]->first_name} {$workers[$i]->patronymic} {$workers[$i]->surname}</option>";
            }
        }
        $HTML .= "</select></td></tr>";
        $category = $this->model->getRoomModel(null, 'category');
        $HTML .= "<tr><td>Помещение-родитель</td><td><select class='margins' name='parent_id'>";
        if (isset($category['status'])) {
            $HTML .= "<option value='0'>Нет данных</option>";
        } else {
            $HTML .= "<option value='0'>Не выбрана категория</option>";
            for ($i = 0; $i < count($category); $i++) {
                $HTML .= "<option value='{$category[$i]->id}'";
                if ($category[$i]->id==$room[0]->parent_id) $HTML .=" selected";
                $HTML.=">{$category[$i]->name}</option>";
            }
        }
        $HTML .= "</select></td></tr>";
        $sections=$this->model->getFilialDepartmentModel(null,'section');
        $HTML .= "<tr><td>Отдел</td><td><select class='margins' name='department_id'>";
        if (isset($sections['status'])) {
            $HTML .= "<option value='0'>Нет данных</option>";
        } else {
            $HTML .= "<option value='0'>Не выбран отдел</option>";
            for ($i = 0; $i < count($sections); $i++) {
                $HTML .= "<option value='{$sections[$i]->id}'";
                if ($sections[$i]->id==$room[0]->department_id) $HTML .=" selected";
                $HTML.=">{$sections[$i]->name}</option>";
            }
        }
        $HTML .= "</select></td></tr></table>";
        $HTML .= "</form>";
        $HTML .= "<div class='button' onclick=\"sendAjax('/topology/edit/{$room_id}/', 'POST', 'updateRoomsForm');\">Редактировать</div>";
        return $HTML;
    }

    /*
     * Отображение формы фильтрации для вкладки Главная
     */
    function showMainFilterView($type=null)
    {
        $HTML = "<h1>type = {$type} </h1>";
        //$workers=$this->model->sendQuery("SELECT id,name FROM user_types WHERE id='1' OR main_class='1'");
        $visitors=$this->model->sendQuery("SELECT id,name FROM user_types WHERE NOT (id='1' OR main_class='1')");
        $marks=$this->model->getMarksModel();
        $rooms=$this->model->getRoomModel(null, 'room');
		$deparmnents=$this->model->sendQuery("SELECT * FROM filial_departament WHERE ((parent_id=0 OR parent_id IS NULL) OR \"group\" IS FALSE)");
		for ($i=0;$i<count($deparmnents);$i++)
		{
			$deparmnents[$i]->sections=$this->model->sendQuery("SELECT * FROM filial_departament WHERE parent_id='{$deparmnents[$i]->id}'");
		}
        $HTML .= "<h2>Фильтр</h2>";
        $HTML .= "<form name='filterform'>";

        $HTML .= "<table><tr><td>Дата с </td><td><input class='form-control' name='dateafter' type='date'></td><td> по </td><td><input name='datebefore' type='date'></td></tr>";
        $HTML .= "<tr><td>Время с </td><td><input class='form-control' name='timeafter' type='time'></td><td> по </td><td><input class='form-control' name='timebefore' type='time'></td></tr>";
        $HTML .= "<tr><td>Фамилия</td><td><input class='form-control' name='surname' type='text'></td><td></td><td></td></tr>";
        $HTML .= "<tr><td>Имя</td><td><input class='form-control' name='name' type='text'></td><td></td><td></td></tr>";
        $HTML .= "<tr><td>Отчество</td><td><input class='form-control' name='patronymic' type='text'></td><td></td><td></td></tr>";
        $HTML .= "<tr><td>Пол</td><td><input class='form-control' name='male' type='checkbox'> Мужской</td><td> <input class='form-control' name='female' type='checkbox'> Женский</td><td></td></tr>";
        $HTML .= "<tr><td>Сотрудник <input class='form-control' name='worker_checkbox' type='checkbox'></td>";
        $HTML .= "<td><select class='form-control' name='worker_type_id'>";//<option value='0'>Не выбрано значение</option>";
        /*for ($i = 0; $i < count($workers); $i++) {
            $HTML .= "<option value='{$workers[$i]->id}'>{$workers[$i]->name}</option>";
        }*/
		for ($i=0;$i<count($deparmnents);$i++)
		{
			$HTML .= "<option value='{$deparmnents[$i]->id}'>";
			if (!$deparmnents[$i]->group) $HTML .= "Департамент";
			else $HTML .= "Отдел";
			$HTML .= ": {$deparmnents[$i]->name}";
			if ($deparmnents[$i]->delete) $HTML .= " (удален)";
			$HTML .= "</option>";
			for ($j=0;$j<count($deparmnents[$i]->sections);$j++)
			{
				$HTML .= "<option value='{$deparmnents[$i]->sections[$j]->id}'>";
			if (!$deparmnents[$i]->sections[$j]->group) $HTML .= "Департамент";
			else $HTML .= "Отдел";
			$HTML .= ": {$deparmnents[$i]->sections[$j]->name}";
			if ($deparmnents[$i]->sections[$j]->delete) $HTML .= " (удален)";
			$HTML .= "</option>";
			}
		}
        $HTML .= "</select></td><td></td><td></td></tr>";
        $HTML .= "<tr><td>Посетитель <input name='visitor_checkbox' type='checkbox'></td>";
        $HTML .= "<td><select name='visitor_type_id'>";
        for ($i = 0; $i < count($visitors); $i++) {
            $HTML .= "<option value='{$visitors[$i]->id}'>{$visitors[$i]->name}</option>";
        }
        $HTML .= "</select></td><td></td><td></td></tr>";
        //$HTML .= "<br>Тех персонал <input name='technical_staff_checkbox' type='checkbox'>";
        //$HTML .= "<br>Подрядчик <input name='contractor_checkbox' type='checkbox'>";
        //$HTML .= "<br>Сотрудник органов <input name='officcer_checkbox' type='checkbox'><br>";
        $HTML .= "<tr><td>Направление</td><td><select name='target_room'><option value='0'>Направление не выбрано</option>";
        for ($i = 0; $i < count($rooms); $i++) {
            $HTML .= "<option value='{$rooms[$i]->id}'>{$rooms[$i]->name}</option>";
        }
        $HTML .= "</select></td><td></td><td></td></tr>";
        //$HTML .= "№ дела, накладной <input name='case_number' type='text'></br>";
        $HTML .= "<tr><td>Метка</td><td><select name='mark'><option value='0'>Метка не выбрана</option>";
        for ($i = 1; $i < count($marks); $i++) {
            $HTML .= "<option value='{$marks[$i]->id}'>{$marks[$i]->name}</option>";
        }
        $HTML .= "</select></td><td></td><td></td></tr>";
        $HTML .= "<tr><td><div class='button' onclick=\"closePopup();\">Отмена</div></td><td>
		<div class='button' onclick=\"sendAjax('/filter/filtered/{$type}/', 'POST', 'filterform');closePopup();\">ОК</div></td><td></td><td></td></tr></table>";

        $HTML .= "</form>";
        $HTML .= "";
        $HTML .= "";
        return $HTML;
    }

    /*
     * Отображение отфильтрованных данных для вкладки Главная
     */
    function showMainFilteredDataView($data=null, $type=null)
    {
        $HTML = "";
        $filter='';
        if ($data!=null)
        {
            if ($data['dateafter']!=null)
            {
                if ($type=='in') $filter.=" AND date_in >= '".pg_escape_string($data['dateafter'])."'";
                if ($type=='out') $filter.=" AND date_out >= '".pg_escape_string($data['dateafter'])."'";
            }
            if ($data['datebefore']!=null)
            {
                if ($type=='in') $filter.=" AND date_in <= '".pg_escape_string($data['datebefore'])."'";
                if ($type=='out') $filter.=" AND date_out <= '".pg_escape_string($data['datebefore'])."'";
            }
            if ($data['timeafter']!=null)
            {
                if ($type=='in') $filter.=" AND time_in >= '".pg_escape_string($data['timeafter'])."'";
                if ($type=='out') $filter.=" AND time_out >= '".pg_escape_string($data['timeafter'])."'";
            }
            if ($data['timebefore']!=null)
            {
                if ($type=='in') $filter.=" AND time_in <= '".pg_escape_string($data['timebefore'])."'";
                if ($type=='out') $filter.=" AND time_out <= '".pg_escape_string($data['timebefore'])."'";
            }
            if ($data['surname']!=null) $filter.=" AND users.surname LIKE '%{$data['surname']}%'";
            if ($data['name']!=null) $filter.=" AND users.first_name LIKE '%{$data['name']}%'";
            if ($data['patronymic']!=null) $filter.=" AND users.patronymic LIKE '%{$data['patronymic']}%'";
            if (isset($data['worker_checkbox'])&&$data['worker_type_id']!=0)
				$filter.=" AND workers.department_id = '{$data['worker_type_id']}' OR workers.department_id  IN (SELECT id FROM filial_departament WHERE parent_id = '{$data['worker_type_id']}')";
            if (isset($data['visitor_checkbox'])&&$data['visitor_type_id']!=0) $filter.=" AND (user_types.id = '{$data['visitor_type_id']}' OR user_types.main_class = '{$data['visitor_type_id']}')";
            if ($data['target_room']!=0) $filter.=" AND filial_rooms.id = '{$data['target_room']}'";
            if ($data['mark']!=0) $filter.=" AND mark_id = '{$data['mark']}'";
        }
        if ($type=='in') $HTML .=$this->inMainView($filter);
        if ($type=='out') $HTML .=$this->outMainView($filter);
        return $HTML;
    }

    /*
     * Отображение формы фильтрации для вкладки Аккаунты - Статистика
     */
    function showAccountStatisticFilterView($user_id=null, $type=null)
    {
        $HTML = "";
        $HTML .= "<h2>Фильтр</h2>";
        $HTML .= "<form name='filterform'>";
        $HTML .= "<h2>Вход</h2>";
        $HTML .= "<table><tr><td>Дата с </td><td><input name='dateafterin' type='date'></td><td> по </td><td><input name='datebeforein' type='date'></td></tr>";
        $HTML .= "<tr><td>Время с </td><td><input name='timeafterin' type='time'></td><td> по </td><td><input name='timebeforein' type='time'></td></tr></table>";
        $HTML .= "<h2>Выход</h2>";
        $HTML .= "<table><tr><td>Дата с </td><td><input name='dateafterout' type='date'></td><td> по </td><td><input name='datebeforeout' type='date'></td></tr>";
        $HTML .= "<tr><td>Время с </td><td><input name='timeafterout' type='time'></td><td> по </td><td><input name='timebeforeout' type='time'></td></tr>";
        $HTML .= "<tr><td><div class='button' onclick=\"closePopup();\">Отмена</div></td>";
        $HTML .= "<td><div class='button' onclick=\"sendAjax('/filter/filtered/statistic/{$user_id}/{$type}/', 'POST', 'filterform');closePopup();\">ОК</div></td><td></td><td></td></table>";
        $HTML .= "</form>";
        return $HTML;
    }

    /*
     * Отображение отфильтрованных данных для вкладки Главная
     */
    function showAccountStatisticFilteredDataView($data=null, $user_id=null, $type=null)
    {
        $HTML = "";
        $filter='';
        if ($data!=null)
        {
            if ($data['dateafterin']!=null)
            {
                $filter.=" AND date_in >= '".pg_escape_string($data['dateafterin'])."'";
            }
            if ($data['datebeforein']!=null)
            {
                $filter.=" AND date_in <= '".pg_escape_string($data['datebeforein'])."'";
            }
            if ($data['timeafterin']!=null)
            {
                $filter.=" AND time_in >= '".pg_escape_string($data['timeafterin'])."'";
            }
            if ($data['timebeforein']!=null)
            {
                $filter.=" AND time_in <= '".pg_escape_string($data['timebeforein'])."'";
            }
            if ($data['dateafterout']!=null)
            {
                $filter.=" AND date_out >= '".pg_escape_string($data['dateafterout'])."'";
            }
            if ($data['datebeforeout']!=null)
            {
                $filter.=" AND date_out <= '".pg_escape_string($data['datebeforeout'])."'";
            }
            if ($data['timeafterout']!=null)
            {
                $filter.=" AND time_out >= '".pg_escape_string($data['timeafterout'])."'";
            }
            if ($data['timebeforeout']!=null)
            {
                $filter.=" AND time_out <= '".pg_escape_string($data['timebeforeout'])."'";
            }
        }
        $HTML .=$this->showAccountStatisticView($user_id, $filter);
        return $HTML;
    }

    function showAccountStatisticView($user_id=null, $filter=null)
    {
        $HTML ="";
        $userPass = $this->model->getUserPass($user_id,$filter);
        $HTML .= "<div class='filexit'><div class='button filtr' onclick=\"sendAjax('/filter/statistic/{$user_id}/null/', 'GET');event.stopPropagation();\">Фильтр</div>
		<div class='button white' title='Сброс фильтра' onclick=\"sendAjax('/filter/filtered/statistic/{$user_id}/null/', 'POST', 'filterform');event.stopPropagation();\"><img class='bigIcon' src='".base_path()."images/icons/close.jpg'></div></div>";
        if($userPass!=null) {

            $HTML .= "<table class='full' border='1' cellpadding='5'>";
            $HTML .= "<tr>";
            $HTML .= "<th rowspan='2'>№</th>";
            $HTML .= "<th colspan='2'>Вход</th>";
            $HTML .= "<th colspan='2'>Выход</th>";
            $HTML .= "<th rowspan='2'>Источник</th>";
            $HTML .= "<th rowspan='2'>№ дела, накладной</th>";
            $HTML .= "<th rowspan='2'>Статус</th>";
            $HTML .= "<th rowspan='2'>Информация</th>";
            $HTML .= "<th rowspan='2'>Метка</th>";
            $HTML .= "<th rowspan='2'>Документ, удостов. личность</th>";
            $HTML .= "</tr>";

            $HTML .= "<tr>";
            $HTML .= "<th>Дата</th>";
            $HTML .= "<th>Время</th>";
            $HTML .= "<th>Дата</th>";
            $HTML .= "<th>Время</th>";
            $HTML .= "</tr>";

            for ($i = 0; $i < count($userPass); $i++) {
                if ($userPass[$i]->id == null) continue;

                $userPass[$i]->time_in = explode(".", $userPass[$i]->time_in);
                $userPass[$i]->time_in = $userPass[$i]->time_in[0];

                $userPass[$i]->time_out = explode(".", $userPass[$i]->time_out);
                $userPass[$i]->time_out = $userPass[$i]->time_out[0];

                $HTML .= "<tr>";
                $HTML .= "<td class='robotocr'>{$userPass[$i]->id}</td>";
				$date=new DateTime($userPass[$i]->date_in);
                $HTML .= "<td class='robotocr'>{$date->Format('d.m.Y')}</td>";
                $HTML .= "<td class='robotocr'>{$userPass[$i]->time_in}</td>";
				$date=new DateTime($userPass[$i]->date_out);
                $HTML .= "<td class='robotocr'>{$date->Format('d.m.Y')}</td>";
                $HTML .= "<td class='robotocr'>{$userPass[$i]->time_out}</td>";
                $HTML .= "<td>{$userPass[$i]->room_name}</td>";
                $HTML .= "<td class='robotocr'>{$userPass[$i]->hearing_code}</td>";
                $HTML .= "<td class='robotocr'>{$userPass[$i]->info}</td>";
                $HTML .= "<td class='robotocr'>{$userPass[$i]->access_info}</td>";
                if ($userPass[$i]->mark_name!=null)
                    $HTML .= "<td class='ralewaymed red'>{$userPass[$i]->mark_name}</td>";
                else
                    $HTML .= "<td class='ralewaymed'>Нет метки</td>";
                $HTML .= "<td>Паспорт РФ - <b class='robotocr'>0234 123456</b></td>";
                $HTML .= "</tr>";
            }
            $HTML .= "</table>";
        } else {
            $HTML .= "Нет данных";
        }
        return $HTML;
    }

	function showChangeDepartmentAccessCategoryView($worker_id=null)
    {
        $HTML ="<form name='changeDepartmentAccessControlForm'><table>";
		$HTML .="<tr><td>Отдел<td></tr><td>";
		$HTML .="<select name='department_id'>";
		$worker=$this->model->getWorkersModel($worker_id);
		$departmnents=$this->model->sendQuery("SELECT * FROM filial_departament WHERE filial_departament.group IS FALSE ORDER BY id ASC");
		for ($i=0;$i<count($departmnents);$i++)
		{
			$HTML .="<option value='{$departmnents[$i]->id}' disabled>{$departmnents[$i]->name}</option>";
			$subdep=$this->model->sendQuery("SELECT * FROM filial_departament WHERE parent_id = '{$departmnents[$i]->id}' ORDER BY id ASC");
			for ($j=0;$j<count($subdep);$j++)
			{
				$HTML .="<option value='{$subdep[$j]->id}'";
				if ($subdep[$j]->id==$worker[0]->department_id) $HTML .=" selected";
				$HTML .="> - {$subdep[$j]->name}</option>";
			}
		}
		$HTML .="</select>";
		$HTML .="</table></form>";
		$HTML .="<div class='button' onclick=\"sendAjax('/access-control/changedepartment/{$worker_id}/', 'POST', 'changeDepartmentAccessControlForm');\">Изменить</div>";
        return $HTML;
    }

	function showAddDepartmentAccessCategoryView($worker_id=null)
    {
        $HTML ="<form name='addDepartmentAccessControlForm'><table>";
		$HTML .="<tr><td>Отдел<td></tr><td>";
		$HTML .="<select name='department_id'>";
		/*
		$departmnents=$this->model->getFilialDepartmentModel(null,'section');
		for ($i=0;$i<count($departmnents);$i++)
			$HTML .="<option value='{$departmnents[$i]->id}'>{$departmnents[$i]->name}</option>";*/
		$departmnents=$this->model->sendQuery("SELECT * FROM filial_departament WHERE filial_departament.group IS FALSE ORDER BY id ASC");
		for ($i=0;$i<count($departmnents);$i++)
		{
			$HTML .="<option value='{$departmnents[$i]->id}' disabled>{$departmnents[$i]->name}</option>";
			$subdep=$this->model->sendQuery("SELECT * FROM filial_departament WHERE parent_id = '{$departmnents[$i]->id}' ORDER BY id ASC");
			for ($j=0;$j<count($subdep);$j++)
			{
				$HTML .="<option value='{$subdep[$j]->id}'> - {$subdep[$j]->name}</option>";
			}
		}
		$HTML .="</select>";
		$HTML .="</table></form>";
		$HTML .="<div class='button' onclick=\"sendAjax('/access-control/adddepartment/{$worker_id}/', 'POST', 'addDepartmentAccessControlForm');\">Назначить</div>";
        return $HTML;
    }
}
