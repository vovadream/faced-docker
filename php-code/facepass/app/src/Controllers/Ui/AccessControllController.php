<?php

namespace App\Controllers\Ui;

use App\Controllers\Controller;
use Slim\Http\Request as Request;
use Slim\Http\Response as Response;


class AccessControllController extends Controller
{


    public function accessControlView(Request $request, Response $response)
    {
        $model = $this->AccessControlModel;
        $query = "SELECT * FROM user_types WHERE (filial_id='{$_SESSION['filial_id']}' OR filial_id='0') AND parent_id='0'";
        $userTypes = $model->sendQuery($query);


        $HTML = "";

        // TODO:Посмотреть в UserTypesModel аналог

        $HTML .= "<div id='accessleft'>";
        for ($i = 0; $i < count($userTypes); $i++) {
            $HTML .= $this->accessControlTopologyItem($userTypes[$i], 0);
        }
        $HTML .= "</div>";

        $HTML .= "<div id='accessright'>";
        $HTML .= "<form name='accessCategorySearchForm'><div id='access-control-category-buttons_bar'>";
        $HTML .= "<div class='button white' title='Увеличить'><img class='bigIcon' src='" . base_path() . "images/icons/zoom-in.jpg'></div>";
        $HTML .= "<div class='button white' title='Уменьшить'><img class='bigIcon' src='" . base_path() . "images/icons/zoom-out.jpg'></div>";
        $HTML .= "<div class='button white' title='Развернуть на весь экран' onclick=\"toggleClass('#nullaccountin', 'active');\"><img class='bigIcon' src='" . base_path() . "images/icons/full-view.jpg'></div>";

        $HTML .= "<input id='access_category_search' name='search_string' class='poisktopology' type='text' placeholder='Введите ФИО/должность'>";

        $HTML .= "<div class='button' onclick=\"sendAjax('/access-control/category_search/0/0/', 'POST', 'accessCategorySearchForm');\">Поиск</div>";

        $HTML .= "</div></form>";
        $HTML .= "<div id='access-control-category'>";
        $HTML .= $this->accessControlCategoryUsersView();
        $HTML .= "</div>";
        $HTML .= "</div>";

        $data = layout('access-control/index',[
            'html' => $HTML
        ]);

        return $response->getBody()->write($data);
    }

    /*
     * Вывод топологии типов пользователей
     */
    public function accessControlTopologyItem($data, $level)
    {
        $model = $this->AccessControlModel;
        $var = ' ';
        $id = ' ';
        $HTML = "<div class='robotocr typeName' id='accessType_{$data->id}' onclick=\"sendAjax('/access-control/category/{$data->id}/{$id}/', 'GET');
	   sendAjax('/access-control/showbutton/{$data->id}/{$id}/','GET'); divMakeActive(this, 'accessDiv', 'accessDivActive');\">";
        if ($data->id != 1) {
            for ($i = 1; $i <= $level; $i++) $HTML .= "-";
            $HTML .= " {$data->name}</div>";
            $query = "SELECT * FROM user_types WHERE parent_id='{$data->id}'";
            $userTypes = $model->sendQuery($query);
            $level++;
            for ($i = 0; $i < count($userTypes); $i++) {
                $HTML .= $this->accessControlTopologyItem($userTypes[$i], $level);
            }
        } else {
            $HTML .= "{$data->name}</div>";
            $departments = $model->sendQuery("SELECT * FROM filial_departament WHERE ((parent_id=0 OR parent_id IS NULL) OR \"group\" IS FALSE) AND delete='false'");
            for ($j = 0; $j < count($departments); $j++) {
                $sections = $model->sendQuery("SELECT * FROM filial_departament WHERE parent_id={$departments[$j]->id} AND delete='false'");

                $HTML .= "<div class='robotocr' id='accessType_{$departments[$j]->id}' onclick=\"sendAjax('/access-control/category/1/{$departments[$j]->id}/', 'GET');
	   sendAjax('/access-control/showbutton/{$data->id}/{$departments[$j]->id}/','GET'); divMakeActive(this, 'accessDiv', 'accessDivActive'); event.stopPropagation();\">";
                $HTML .= "<div class='typeName'>";
                if (count($sections) > 0)
                    $HTML .= "<button onclick=\"divSlide(this, '#accessType_{$departments[$j]->id}', '#subDepartaments-{$departments[$j]->id}', false);\">+</button>";
                $HTML .= "{$departments[$j]->name}";
                $HTML .= "</div>";
                if (!$departments[$j]->group) {
                    $HTML .= "<div style='display: none;' id='subDepartaments-{$departments[$j]->id}'>";
                    for ($sections_j = 0; $sections_j < count($sections); $sections_j++) {
                        $HTML .= "<div class='robotocr typeName' id='accessType_{$sections[$sections_j]->id}'
			onclick=\"sendAjax('/access-control/category/1/{$sections[$sections_j]->id}/', 'GET');
	   sendAjax('/access-control/showbutton/{$data->id}/{$departments[$j]->id}/','GET'); divMakeActive(this, 'accessDiv', 'accessDivActive'); event.stopPropagation();\">{$sections[$sections_j]->name}</div>";
                    }
                    $HTML .= "</div>";
                }
                $HTML .= "</div>";
            }
        }
        return $HTML;
    }

    /*
     * Вывод таблицы пользователей с доступом
     */
    public function accessControlCategoryUsersView($id = null, $dep_id = null, $data = null)
    {
        $model = $this->AccessControlModel;
        $usersAccess = $model->accessControlCategoryUsersModel($id, $dep_id, $data);
        $HTML = "";
        $HTML .= "<div class='overfltable'><table class='akkt' border='1' cellpadding='5'>";
        $HTML .= "<tr>";
        $HTML .= "<th>№</th>";
        $HTML .= "<th>Фото</th>";
        $HTML .= "<th>ФИО</th>";
        $HTML .= "<th>Место работы</th>";
        $HTML .= "<th>Должность</th>";
        $HTML .= "<th>Метка</th>";
        $HTML .= "<th></th>";
        $HTML .= "</tr>";

        for ($i = 0; $i < count($usersAccess); $i++) {
            $HTML .= "<td class='robotocr'>{$usersAccess[$i]->id}</td>";

            $HTML .= "<td class='robotocr'><img src='" . GetImageURL($usersAccess[$i]->user_photo, 'user_photo') . "' width='30'/></td>";
            //if ($usersAccess[$i]->user_photo!=null)
            //    $HTML .= "<td class='robotocr'><img src='{$usersAccess[$i]->user_photo}'/></td>";
            //	else
            //	$HTML .= "<td class='robotocr'>{$usersAccess[$i]->user_photo}<img src='".base_path()."images/icons/chelovek2.PNG' class='bigIcon' \"></td>";
            $HTML .= "<td class='ralewayreg'><a href='" . base_path() . "account/{$usersAccess[$i]->id}/'>{$usersAccess[$i]->surname} {$usersAccess[$i]->first_name} {$usersAccess[$i]->patronymic}</a></td>";
            //$HTML .= "<td class='ralewayreg'	>{$usersAccess[$i]->info}</td>";
            if ($usersAccess[$i]->main_class == 1 || $usersAccess[$i]->user_type_id == 1)
                $HTML .= "<td class='ralewayreg'>{$usersAccess[$i]->dep_name}<br>Кабинет {$usersAccess[$i]->room_name}</td>";
            else if ($usersAccess[$i]->main_class == 4 || $usersAccess[$i]->user_type_id == 4) $HTML .= "<td><div class='button'
			onclick=\"sendAjax('/access-control/showadddepartment/{$usersAccess[$i]->worker_id}/', 'GET')\">Добавить</div></td>";
            else $HTML .= "<td class='ralewayreg'>{$usersAccess[$i]->work_place}</td>";
            /*$access = "";
            for($j=0;$j<count($usersAccess[$i]->access);$j++)
            {
                if($access!='') $access.= ', ';
                $access .= $usersAccess[$i]->access[$j]->room_name;
            }
			if($access=='') $access.= 'Нет данных';*/
            $HTML .= "<td class='ralewayreg'>{$usersAccess[$i]->work_position}</td>";
            $marks = $model->getUserMars($usersAccess[$i]->id);
            if ($marks != null && $marks[0]->mark_name != null)
                $HTML .= "<td class='ralewaymed red'>{$marks[0]->mark_name}</td>";
            else
                $HTML .= "<td class='ralewaymed'>Нет метки</td>";
            if ($usersAccess[$i]->main_class == 1 || $usersAccess[$i]->user_type_id == 1) $HTML .= "<td><div class='button'
			onclick=\"sendAjax('/access-control/showchangedepartment/{$usersAccess[$i]->worker_id}/', 'GET')\">Изменить</div></td>";
            $HTML .= "</tr>";
        }
        $HTML .= "</table></div>";
        if ($data['search_string'] != '') {
            $resultString = explode(' ', $data['search_string']);
            for ($searchCount = 0; $searchCount < count($resultString); $searchCount++) {
                $HTML = str_replace($resultString[$searchCount], "<div class='find'>{$resultString[$searchCount]}</div>", $HTML);
                //$HTML=mb_eregi_replace($resultString[$searchCount],"<div class='find'>{$resultString[$searchCount]}</div>",$HTML);
            }
        }
        /*
        {
            /*$pos=0;

//while($pos<=(strlen($HTML)-strlen($resultString[$searchCount])))

//{
            $HTML.="{$pos}<br>";
            $pos=mb_stripos($HTML, $resultString[$searchCount],$pos);
            //if ($pos===false) break;
            //else
            //{
                $HTML.="{$pos}<br>";
            $find=substr($HTML,$pos,strlen($resultString[$searchCount]));
            $pos=$pos+strlen($resultString[$searchCount]);
            $HTML.="{$pos} {$find}<br>";
            $HTML=mb_eregi_replace($find,"<div class='find'>{$find}</div>",$HTML);
            //$HTML=substr_replace($HTML,"<div class='find'>{$find}</div>",$pos,strlen($resultString[$searchCount]));
            //}
            //}
            //*/
        //$HTML=mb_eregi_replace($resultString[$searchCount],"<div class='find'>{$resultString[$searchCount]}</div>",$HTML);
        //}

        //*/
        return $HTML;
    }

    public function accessControlCategoryUsersControl(Request $request, Response $response)
    {
        $data = [];
        $id = $request->getAttribute('id');
        $dep_id = $request->getAttribute('dep_id');
        $html = $this->accessControlCategoryUsersView($id, $dep_id);
        $data['status'] = 'success';
        $data['html'] = $html;
        $data['div'] = 'access-control-category';
        $response = $response->withJson($data);
        return $response;
    }

    public function accessControlCategoryUsersSearchControl(Request $request, Response $response)
    {
        $data = [];
        $data = $request->getParsedBody();
        $id = $request->getAttribute('id');
        $dep_id = $request->getAttribute('dep_id');
        $html = $this->accessControlCategoryUsersView($id, $dep_id, $data);
        $data['status'] = 'success';
        $data['html'] = $html;
        $data['div'] = 'access-control-category';
        $response = $response->withJson($data);
        return $response;
    }


    public function showSearchAccessCategoryButtonControl(Request $request, Response $response)
    {
        $data = [];
        $id = $request->getAttribute('id');
        $dep_id = $request->getAttribute('dep_id');
        $html = $this->showSearchAccessCategoryButtonView($id, $dep_id);
        $data['status'] = 'success';
        $data['html'] = $html;
        $data['div'] = 'access-control-category-buttons_bar';
        $response = $response->withJson($data);
        return $response;
    }

    public function showChangeDepartmentAccessCategoryControl(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        $id = $request->getAttribute('id');
        $data['status'] = 'success';
        $data['div'] = 'popup';
        $data['html'] = $this->showChangeDepartmentAccessCategoryView($id);
        $response = $response->withJson($data);
        return $response;
    }

    public function changeDepartmentAccessCategoryControl(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        $id = $request->getAttribute('id');
        $data = $this->model->changeWorkerDepartmentCategoryAccessModel($data, $id);
        $response = $response->withJson($data);
        return $response;
    }

    public function showAddDepartmentAccessCategoryControl(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        $id = $request->getAttribute('id');
        $data['status'] = 'success';
        $data['div'] = 'popup';
        $data['html'] = $this->showAddDepartmentAccessCategoryView($id);
        $response = $response->withJson($data);
        return $response;
    }

    public function addDepartmentAccessCategoryControl(Request $request, Response $response)
    {
        $id = $request->getAttribute('id');
        $data = $this->model->addWorkerDepartmentCategoryAccessModel($data, $id);
        $response = $response->withJson($data);
        return $response;
    }

    public function showSearchAccessCategoryButtonView($id=null,$dep_id=null)
    {
        $HTML = "";
        $HTML .= "<input id='access_category_search' name='search_string' class='poisktopology' type='text' placeholder='Введите ФИО/должность'>";
        $HTML .= "<div class='button' onclick=\"sendAjax('/access-control/category_search/{$id}/{$dep_id}/', 'POST','accessCategorySearchForm');\">Поиск</div>";
        return $HTML;
    }
}