<?php

namespace App\Controllers\Ui;

use App\Controllers\Controller;
use Slim\Container;
use Slim\Http\Request as Request;
use Slim\Http\Response as Response;

use PDO;

class InterfacesPermissionController extends Controller
{

    /**
     * InterfacesPermission constructor.
     * @param Container $c
     */
    public function __construct(Container $c)
    {
        parent::__construct($c);
        $this->db = $c->get('db');
    }


    /*
    * Изменение доступа к интерфейсу
    * Return: JSON
    */
    public function updatePermissionDefaultInterfaceControl(Request $request, Response $response)
    {
        $id = $request->getAttribute('id');
        $data = $request->getParsedBody();
        $model = $this->PermissionDefaultInterfaceModel;
        $data = $model->updatePermissionDefaultInterfaceModel($data, $id);
        $response = $response->withJson($data);
        return $response;
    }

    /*
     * Добавление доступа к интерфейсам
     * Return: JSON
     */
    public function addPermissionDefaultInterfaceControl(Request $request, Response $response)
    {
        $model = $this->PermissionDefaultInterfaceModel;
        $data = $request->getParsedBody();
        $data = $model->addPermissionDefaultInterfaceModel($data);
        $response = $response->withJson($data);
        return $response;
    }

    /*
     * Вывод формы добавления доступа к интерфейсу
     * Return: HTML
     */
    public function showAddPermissionDefaultInterfaceControl(Request $request, Response $response)
    {
        $html = $this->showAddPermissionDefaultInterfaceView();
        $data = [];
        $data['status'] = 'success';
        $data['div'] = 'popup';
        $data['html'] = $html;
        $response = $response->withJson($data);
        return $response;
    }

    /*
     * Вывод формы изменения доступа к интерфейсу
     * Return: HTML
     */
    public function showChangePermissionDefaultInterfaceControl(Request $request, Response $response)
    {
        $id = $request->getAttribute('id');
        $html = $this->showChangePermissionDefaultInterfaceView($id);
        $data = [];
        $data['status'] = 'success';
        $data['div'] = 'popup';
        $data['html'] = $html;
        $response = $response->withJson($data);
        return $response;
    }

    public function showTablePermissionDefaultInterfaceControl(Request $request, Response $response)
    {
        $id = $request->getAttribute('id');
        $html = $this->showPermissionDefaultTableView($id);
        $data = [];
        $data['html'] = $html;
        $data['status'] = 'success';
        $data['div'] = 'interfacePermissionTable';
        $response = $response->withJson($data);
        return $response;
    }


    /*
     * Таблица стандартных интерфейсов
     * Return: HTML
     */
    public function showPermissionDefaultTableView($id = null)
    {
        $diModel = $this->PermissionDefaultInterfaceModel;
        $permissions_def_interfaces = $diModel->getPermissionDefaultInterfaceModel($id);
        $HTML = "";

            $HTML .= "<div class='button' onclick=\"sendAjax('/interfaces/permission/form/add/{$id}/', 'GET')\">Создать новое право доступа</div><br><br>";

            $HTML .= "<table class='akkt' border='1' cellpadding='5'>";
            $HTML .= "<tr>";
            $HTML .= "<th>№</th>";
            $HTML .= "<th>№ доступа</th>";
            $HTML .= "<th>Название доступа</th>";
            $HTML .= "<th>№ интерфейса</th>";
            $HTML .= "<th>Название интерфейса</th>";
            $HTML .= "<th>Статус</th>";
            $HTML .= "<th></th>";
            $HTML .= "</tr>";



        if (isset($permissions_def_interfaces['status'])) {
            $HTML .= "<tr>";
            $HTML .= "<td class='error'>{$permissions_def_interfaces['message']}</td>";
            $HTML .= "</tr>";
        } else {
            for ($i = 0; $i < count($permissions_def_interfaces); $i++) {
                $HTML .= "<tr>";
                $HTML .= "<td>{$permissions_def_interfaces[$i]->id}</td>";
                $HTML .= "<td>{$permissions_def_interfaces[$i]->permission_id}</td>";
                $HTML .= "<td>{$permissions_def_interfaces[$i]->permission_name}</td>";
                $HTML .= "<td>{$permissions_def_interfaces[$i]->interface_id}</td>";
                $HTML .= "<td>{$permissions_def_interfaces[$i]->interface_name}</td>";
                if ($permissions_def_interfaces[$i]->status == 1) $HTML .= "<td>Разрешен</td>";
                if ($permissions_def_interfaces[$i]->status == 0) $HTML .= "<td>Запрещен</td>";
                $HTML .= "<td class='button' onclick=\"sendAjax('/interfaces/permission/form/{$permissions_def_interfaces[$i]->id}/', 'GET')\">Изменить</td>";
                //$HTML .= "<td class='button' onclick=\"sendAjax('deleteMark={$marks[$i]->id}', 'DELETE')\">Удалить</td>";
                $HTML .= "</tr>";
            }
        }
        $HTML .= "</table>";
        return $HTML;
    }


    /*
     * Вывод формы для изменения стандартных прав доступа к интерфейсам
     * Return: JSON
     */
    public function showChangePermissionDefaultInterfaceView($id = null)
    {
        $model = $this->PermissionDefaultInterfaceModel;
        $permissions_def_interfaces = $model->sendQuery("SELECT permissions_def_interfaces.* FROM permissions_def_interfaces WHERE id={$id}");

        $permissionsModel = $this->PermissionsModel;
        $permissions = $permissionsModel->getPermissionsModel();

        $interfacesModel = $this->InterfaceModel;
        $interface = $interfacesModel->getInterfacesModel($permissions_def_interfaces[0]->interface_id);

        $json = [];
        $HTML = "";
        $HTML .= "<h2>Изменение стандартного доступа к интерфейсу {$interface[0]->name} - {$permissions[0]->name}</h2>";
        $HTML .= "<form name='updatePermissionDefaultInterfaceForm'>";
        $HTML .= "<table><tr><td>Статус</td><td><select name='status'>";
        $HTML .= "<option value='true' ";
        if ($permissions_def_interfaces[0]->status) $HTML .= " selected ";
        $HTML .= " >Разрешить</option>";
        $HTML .= "<option value='false' ";
        if (!$permissions_def_interfaces[0]->status) $HTML .= " selected ";
        $HTML .= " >Запретить</option>";
        $HTML .= "</select><td></tr></table>";
        $HTML .= "</form>";
        $HTML .= "<div class='button' onclick=\"sendAjax('/interfaces/permission/update/{$id}/', 'POST', 'updatePermissionDefaultInterfaceForm')\">Изменить</div>";
        return $HTML;
    }

    /*
    *Вывод формы для создания стандартных прав доступа к интерфейсам
    *Return: JSON
    */

    public function showAddPermissionDefaultInterfaceView()
    {
        $permissionsModel = $this->PermissionsModel;
        $permissions = $permissionsModel->getPermissionsModel();
        $interfacesModel = $this->InterfaceModel;
        $interfaces = $interfacesModel->getInterfacesModel();


        $HTML = "";
        $HTML .= "<h2>Добавление стандартного доступа к интерфейсу</h2>";
        $HTML .= "<form name='addPermissionDefaultInterfaceForm'>";
        $HTML .= '<span>Роль в системе</span><select name="permission_id">';
        foreach ($permissions as $key => $permission) {
            $HTML .= '<option value=' . $permissions[$key]->id . ">" . $permissions[$key]->name . "</option>";
        }
        $HTML .= '</select>';
        $HTML .= "<table>";
        $HTML .= "<tr>";
        $HTML .= "<td>Интерфейс</td><td><select name='interface_id'>";
        if (isset($interfaces['status'])) {
            $HTML .= "<option value='0' disabled>Нет данных</option>";
        } else {
            $HTML .= "<option value='0' disabled>Не выбран интерфейс</option>";
            for ($i = 0; $i < count($interfaces); $i++) {
                $HTML .= "<option value='{$interfaces[$i]->id}'>{$interfaces[$i]->name}</option>";
            }
        }
        $HTML .= "</select></td></tr>";
        $HTML .= "<tr><td>Статус</td><td><select name='status'>";
        $HTML .= "<option value='true'>Разрешить</option>";
        $HTML .= "<option value='false'>Запретить</option>";
        $HTML .= "</select></td></tr>";
        $HTML .= "</table>";
        $HTML .= "</form>";
        $HTML .= "<div class='button' onclick=\"sendAjax('/interfaces/permission/add/', 'POST', 'addPermissionDefaultInterfaceForm')\">Создать стандартные права доступа</div>";

        return $HTML;
    }

}