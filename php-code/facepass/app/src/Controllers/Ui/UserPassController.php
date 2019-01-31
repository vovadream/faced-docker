<?php

namespace App\Controllers\Ui;

use App\Controllers\Controller;
use App\Models\UserPassModel;
use Slim\Container;
use Slim\Http\Request as Request;
use Slim\Http\Response as Response;

use PDO;

class UserPassController extends Controller
{

    /*
     * РАзершение прохода на территорию
     * Return: JSON
     */
    public function addUserPassControl(Request $request, Response $response)
    {
        $model = $this->UserPassModel;
        $user_id = $request->getAttribute('user_id');
        $access_id = $request->getAttribute('access_id');
        $data = $model->addUserPassModel($user_id, $access_id);
        $response = $response->withJson($data);
        return $response;
    }

    /*
     * Разрешение выхода из территории
     * Return: JSON
     */
    public function updateUserPassControl(Request $request, Response $response)
    {
        $model = $this->UserPassModel;
        $id = $request->getAttribute('id');
        $data = $model->updateUserPassModel($id);
        $response = $response->withJson($data);
        return $response;
    }

    /*
     * Вывод формы пропуск
     * Return: JSON
     */
    public function showUserPassControl(Request $request, Response $response)
    {
        $id = $request->getAttribute('id');
        $html = $this->showUserPassFormView($id);
        $data['status'] = 'success';
        $data['div'] = 'popup';
        $data['html'] = $html;
        $response = $response->withJson($data);
        return $response;
    }

    /*
     * Вывод формы пропуск
     * Return: HTML
     */
    public function showUserPassFormView($id = null)
    {
        $model = $this->UserPassModel;
        $userpassinfo = $model->getUserPasses($id);
        $HTML = "";
        $HTML .= "<div id='userPass' class='userData'>";
        $HTML .= "<table><tr><td class='img-td' rowspan='3'><img src='" . GetImageURL($userpassinfo[0]->user_photo, 'user_photo') . "' width='100'/></td>";
        $HTML .= "<td><h1>{$userpassinfo[0]->surname} {$userpassinfo[0]->first_name} {$userpassinfo[0]->patronymic}</h1></td></tr>";
        $HTML .= "<tr><td><h2>Паспорт РФ:</h2></td></tr>";
        $HTML .= "<tr><td><h2>Водительское удостоверение:</h2></td></tr></table><br>";
//        $HTML .= "<table><tr><td><div class='button'>Напечатать повторно пропуск</div></td>";
//        $HTML .= "<td><div class='button'>ОК</div></td></tr></table>";
        $number=0;
        if (count($userpassinfo)==0) $HTML .= "<h2>Нет данных о пропусках</h2>";
        else
        {
            $HTML .= "<table class='akkt' border='1' cellpadding='5'>";
            $HTML .= "<tr>";
            $HTML .= "<th>№</th>";
            $HTML .= "<th>№ пропуска</th>";
            $HTML .= "<th>Дата выдачи</th>";
            $HTML .= "<th>Категория доступа</th>";
            $HTML .= "<th>Доступ разрешен</th>";
            $HTML .= "<th>Время для входа</th>";
            $HTML .= "<th>Время для выхода</th>";
            $HTML .= "</tr>";
            for($i=0;$i<count($userpassinfo);$i++) {
                $number++;
                $values='';
                $HTML .= "<tr>";
                $HTML .= "<td>{$number}</td>";
                $HTML .= "<td>{$userpassinfo[$i]->id}</td>";
                $date= new \DateTime($userpassinfo[$i]->date);
                $HTML .= "<td>{$date->Format('d.m.Y')}</td>";
                $HTML .= "<td>";
                $hearing_rooms= $model->getHearingRoomModel($userpassinfo[$i]->hearing_id);
                if (!isset($hearing_rooms)) $HTML .= "<b>У слушания нет доступных помещений</b><br>";
                else
                {
                    $HTML.="<b>Доступные помещения для слушания</b><br>";
                    $values='';
                    for($j=0;$j<count($hearing_rooms);$j++)
                    {
                        if ($values!='') $values.=", ";
                        if (isset($hearing_rooms[$j]->name)) $values.="{$hearing_rooms[$j]->name}";
                    }
                    $HTML.=$values;
                }
                $rooms=$model->getAccessRoomModel($userpassinfo[$i]->id);
                if (!isset($rooms)) $HTML .= "<br><b>Посетитель не имеет специальных прав доступа</b><br>";
                else
                {
                    $HTML.="<br><b>Специальные права доступа</b><br>";
                    $values='';
                    for($j=0;$j<count($rooms);$j++)
                    {
                        if ($values!='') $values.=", ";
                        if (isset($rooms[$j]->name)) $values.="{$rooms[$j]->name}";
                    }
                    $HTML.=$values;
                }
                $HTML .= "</td>";
                $HTML .= "<td>{$userpassinfo[$i]->hdate}</td>";
                $HTML .= "<td>Время для входа</td>";
                $HTML .= "<td>Время для выхода</td>";
                $HTML .= "</tr>";
            }
        }
        $HTML .= "</table>";
        $HTML .= "</div>";
        return $HTML;
    }
}